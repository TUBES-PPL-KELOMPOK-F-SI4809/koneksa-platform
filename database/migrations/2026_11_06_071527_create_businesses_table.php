<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->restrictOnDelete();
            $table->foreignId('business_category_id')->constrained('business_categories')->restrictOnDelete();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->text('address')->nullable();
            $table->string('district', 100)->nullable();
            $table->string('subdistrict', 100)->nullable();
            $table->string('contact_phone', 30)->nullable();
            $table->string('logo_path', 255)->nullable();
            $table->text('business_needs')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['business_category_id', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
