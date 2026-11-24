<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class PublicBusinessProfileController extends Controller
{
    /**
     * Tampilkan detail profil publik UMKM (FR-11 / KSA-68).
     */
    public function show(Request $request, Business $business): JsonResponse|Response
    {
        Gate::authorize('view', $business);

        $business->loadMissing(['category', 'user']);

        // FR-11.1: Katalog produk aktif beserta variannya
        $products = $business->products()
            ->whereNull('archived_at')
            ->with(['variants' => fn ($query) => $query->orderBy('variant_key')])
            ->latest()
            ->get()
            ->map(function ($product) {
                $minPrice = $product->variants->min('selling_price');
                $maxPrice = $product->variants->max('selling_price');
                $totalStock = (int) $product->variants->sum('stock_quantity');

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'image_path' => $product->image_path,
                    'unit' => $product->unit,
                    'min_price' => $minPrice !== null ? (float) $minPrice : null,
                    'max_price' => $maxPrice !== null ? (float) $maxPrice : null,
                    'total_stock' => $totalStock,
                    'variants' => $product->variants->map(function ($variant) {
                        $variantName = trim(($variant->size ?? '').' '.($variant->color ?? ''));
                        if (empty($variantName)) {
                            $variantName = $variant->variant_key === 'default' ? 'Standar' : $variant->variant_key;
                        }

                        return [
                            'id' => $variant->id,
                            'variant_key' => $variant->variant_key,
                            'variant_name' => $variantName,
                            'sku' => $variant->sku,
                            'size' => $variant->size,
                            'color' => $variant->color,
                            'selling_price' => (float) $variant->selling_price,
                            'stock_quantity' => (int) $variant->stock_quantity,
                            'low_stock_threshold' => (int) $variant->low_stock_threshold,
                            'is_low_stock' => $variant->isLowStock(),
                            'stock_status' => $variant->stock_status,
                        ];
                    }),
                ];
            });

        // FR-11.2: Status verifikasi UMKM
        $approvedVerification = $business->verificationRequests()
            ->where('status', 'approved')
            ->latest('reviewed_at')
            ->first();

        $isVerified = $approvedVerification !== null;
        $latestVerification = $approvedVerification ?? $business->verificationRequests()->latest()->first();
        $verificationStatus = $latestVerification?->status ?? 'unverified';

        $verificationLabel = match ($verificationStatus) {
            'approved' => 'Terverifikasi',
            'pending' => 'Menunggu Verifikasi',
            'rejected' => 'Perlu Perbaikan',
            default => 'Belum Terverifikasi',
        };

        // FR-11.1: Info performa yang dipublikasikan (agregat aman non-privat)
        $performanceInfo = [
            'active_products_count' => $products->count(),
            'total_variants_count' => $products->sum(fn ($p) => count($p['variants'])),
            'completed_sales_count' => $business->sales()->where('status', 'recorded')->count(),
            'joined_since' => $business->created_at?->translatedFormat('F Y') ?? $business->created_at?->format('F Y'),
        ];

        // FR-11.1: Kebutuhan kolaborasi / pengembangan usaha
        $needsList = array_values(array_filter(
            array_map('trim', preg_split('/[\r\n,]+/', (string) $business->business_needs))
        ));

        // FR-11.2: Akses dan status tombol aksi pengajuan proposal kerja sama
        $user = $request->user();
        $canSubmitProposal = $user !== null && $user->isMitra() && $user->isEnabled();
        $isOwner = $user !== null && $user->id === $business->user_id;

        $proposalAction = [
            'can_submit_proposal' => $canSubmitProposal,
            'is_owner' => $isOwner,
            'requires_auth' => $user === null,
            'user_role' => $user?->role,
        ];

        $payload = [
            'id' => $business->id,
            'name' => $business->name,
            'description' => $business->description,
            'address' => $business->address,
            'district' => $business->district,
            'subdistrict' => $business->subdistrict,
            'contact_phone' => $business->contact_phone,
            'logo_path' => $business->logo_path,
            'business_category_id' => $business->business_category_id,
            'category_name' => $business->category?->name,
            'business_needs' => $business->business_needs,
            'needs_list' => $needsList,
            'published_at' => $business->published_at?->toIso8601String(),
            'verification' => [
                'is_verified' => $isVerified,
                'status' => $verificationStatus,
                'label' => $verificationLabel,
                'reviewed_at' => $approvedVerification?->reviewed_at?->toIso8601String(),
            ],
            'performance' => $performanceInfo,
            'products' => $products,
            'proposal_action' => $proposalAction,
        ];

        if ($request->wantsJson()) {
            return response()->json(['business' => $payload]);
        }

        return Inertia::render('Public/BusinessDetail', [
            'business' => $payload,
        ]);
    }
}
