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
        Schema::create('partner_cooperation_types', function (Blueprint $table) {
            $table->foreignId('partner_profile_id')->constrained('partner_profiles')->cascadeOnDelete();
            $table->foreignId('cooperation_type_id')->constrained('cooperation_types')->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['partner_profile_id', 'cooperation_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_cooperation_types');
    }
};
