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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 30)->nullable()->after('password');
            $table->string('avatar_path', 255)->nullable()->after('phone');
            $table->string('account_status', 20)->default('enabled')->after('role');
            $table->boolean('must_change_password')->default(false)->after('email_verified_at');
            $table->string('current_session_id', 255)->nullable()->after('must_change_password');
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn([
                'phone',
                'avatar_path',
                'account_status',
                'must_change_password',
                'current_session_id',
            ]);
        });
    }
};
