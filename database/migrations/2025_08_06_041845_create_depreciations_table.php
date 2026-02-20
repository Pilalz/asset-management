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
        Schema::create('depreciations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets');
            $table->string('type');
            $table->date('depre_date');
            $table->decimal('monthly_depre', 18, 0);
            $table->decimal('accumulated_depre', 18, 0);
            $table->decimal('book_value', 18, 0);
            $table->foreignId('company_id')->constrained('companies');
            $table->timestamps();

            $table->unique(
                ['asset_id', 'type', 'depre_date'],
                'depreciations_asset_type_date_unique'
            );

            $table->index(
                [
                    'asset_id',
                    'book_value',
                    'monthly_depre',
                    'accumulated_depre',
                    'type',
                    'depre_date',
                ],
                'idx_depre_search'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depreciations');
    }
};
