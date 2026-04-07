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
        if (Schema::hasTable('assets')) {
            Schema::table('assets', function (Blueprint $table) {
                if (Schema::hasColumn('assets', 'fiscal_useful_life_month')) {
                    $table->dropColumn(['fiscal_useful_life_month', 'fiscal_accum_depre', 'fiscal_nbv']);
                }
            });
        }

        if (Schema::hasTable('asset_names')) {
            Schema::table('asset_names', function (Blueprint $table) {
                if (Schema::hasColumn('asset_names', 'fiscal')) {
                    $table->dropColumn('fiscal');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('assets')) {
            Schema::table('assets', function (Blueprint $table) {
                if (!Schema::hasColumn('assets', 'fiscal_useful_life_month')) {
                    $table->integer('fiscal_useful_life_month')->default(0);
                    $table->decimal('fiscal_accum_depre', 18, 0)->default(0);
                    $table->decimal('fiscal_nbv', 18, 0)->default(0);
                }
            });
        }

        if (Schema::hasTable('asset_names')) {
            Schema::table('asset_names', function (Blueprint $table) {
                if (!Schema::hasColumn('asset_names', 'fiscal')) {
                    $table->integer('fiscal')->default(0);
                }
            });
        }
    }
};
