<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use App\Services\Inventory\SaleStockService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use Throwable;

class StockSaleConcurrencyTest extends TestCase
{
    use DatabaseMigrations;

    protected function beforeRefreshingDatabase(): void
    {
        $connection = config('database.default');
        if ($connection !== 'mysql' || config('database.connections.mysql.database') !== 'testing'
            || ! in_array(config('database.connections.mysql.host'), ['127.0.0.1', 'localhost'])
            || ! function_exists('pcntl_fork')) {
            $this->markTestSkipped('Concurrency requires disposable MySQL database testing on loopback and pcntl (available in CI).');
        }
    }

    public function test_simultaneous_sales_cannot_both_take_the_last_unit(): void
    {
        [$owner, $first] = $this->inventory(1);
        $cart = [['product_variant_id' => $first->id, 'quantity' => 1]];

        $results = $this->recordConcurrently($owner, [$cart, $cart]);
        sort($results);

        $this->assertSame([201, 422], $results);
        $this->assertSame(0, $first->fresh()->stock_quantity);
        $this->assertDatabaseCount('sales', 1);
        $this->assertDatabaseCount('sale_items', 1);
        $this->assertDatabaseCount('stock_movements', 1);
        $this->assertDatabaseHas('stock_movements', ['product_variant_id' => $first->id, 'quantity_delta' => -1]);
    }

    public function test_opposite_cart_order_uses_consistent_locks_and_preserves_both_sales(): void
    {
        [$owner, $first, $second] = $this->inventory(2);
        $a = ['product_variant_id' => $first->id, 'quantity' => 1];
        $b = ['product_variant_id' => $second->id, 'quantity' => 1];

        $this->assertSame([201, 201], $this->recordConcurrently($owner, [[$a, $b], [$b, $a]]));
        $this->assertSame(0, $first->fresh()->stock_quantity);
        $this->assertSame(0, $second->fresh()->stock_quantity);
        $this->assertDatabaseCount('sales', 2);
        $this->assertDatabaseCount('sale_items', 4);
        $this->assertDatabaseCount('stock_movements', 4);
        $this->assertSame('40000.00', Sale::firstOrFail()->total_amount);
    }

    private function recordConcurrently(User $owner, array $carts): array
    {
        DB::disconnect();
        $workers = [];
        foreach ($carts as $cart) {
            $sockets = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);
            $this->assertNotFalse($sockets);
            $pid = pcntl_fork();
            $this->assertNotSame(-1, $pid);
            if ($pid === 0) {
                fclose($sockets[0]);
                foreach ($workers as [$socket]) {
                    fclose($socket);
                }
                DB::purge();
                fread($sockets[1], 1);
                try {
                    app(SaleStockService::class)->record(User::findOrFail($owner->id), $cart);
                    $result = 201;
                } catch (ValidationException) {
                    $result = 422;
                } catch (Throwable $exception) {
                    $result = get_class($exception);
                }
                fwrite($sockets[1], json_encode($result));
                fclose($sockets[1]);
                exit(0);
            }
            fclose($sockets[1]);
            stream_set_timeout($sockets[0], 20);
            $workers[] = [$sockets[0], $pid];
        }
        foreach ($workers as [$socket]) {
            fwrite($socket, 'g');
        }
        $results = [];
        foreach ($workers as [$socket, $pid]) {
            $result = stream_get_contents($socket);
            fclose($socket);
            pcntl_waitpid($pid, $status);
            $this->assertSame(0, pcntl_wexitstatus($status));
            $results[] = json_decode($result, true, flags: JSON_THROW_ON_ERROR);
        }

        return $results;
    }

    private function inventory(int $stock): array
    {
        $owner = User::factory()->umkm()->create();
        $category = BusinessCategory::create(['name' => 'Kuliner']);
        $business = Business::create(['user_id' => $owner->id, 'business_category_id' => $category->id, 'name' => 'Usaha']);
        $variants = [];
        foreach (['Kopi', 'Teh'] as $name) {
            $product = Product::create(['business_id' => $business->id, 'name' => $name, 'unit' => 'bungkus']);
            $variants[] = $product->variants()->create(['variant_key' => 'default', 'selling_price' => '20000.00', 'stock_quantity' => $stock]);
        }

        return [$owner, ...$variants];
    }
}
