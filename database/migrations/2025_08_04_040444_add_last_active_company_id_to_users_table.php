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
            $table->foreignId('last_active_company_id')->nullable()->constrained('companies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Ini akan mengecek apakah constraint ada sebelum mencoba menghapusnya
            if (Schema::hasColumn('users', 'last_active_company_id')) { // Pastikan kolomnya ada
                $table->dropForeign(['last_active_company_id']);
                $table->dropColumn('last_active_company_id');
            }
        });
    }
    
};
