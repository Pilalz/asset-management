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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_number')->nullable()->unique();
            $table->foreignId('asset_name_id')->constrained('asset_names');
            $table->string('asset_type');
            $table->string('status');
            $table->string('description');
            $table->longText('detail')->nullable();
            $table->string('pareto')->nullable();
            $table->string('unit_no')->nullable();
            $table->string('sn_chassis')->nullable();
            $table->string('sn_engine')->nullable();
            $table->date('production_year')->nullable();
            $table->string('po_no')->nullable();
            $table->foreignId('location_id')->constrained('locations');
            $table->foreignId('department_id')->constrained('departments');
            $table->bigInteger('quantity');
            $table->date('capitalized_date');
            $table->date('start_depre_date')->nullable();
            $table->decimal('acquisition_value', 18, 0);
            $table->decimal('current_cost', 18, 0);
            $table->integer('commercial_useful_life_month');
            $table->decimal('commercial_accum_depre', 18, 0);
            $table->decimal('commercial_nbv', 18, 0);
            $table->integer('fiscal_useful_life_month');
            $table->decimal('fiscal_accum_depre', 18, 0);
            $table->decimal('fiscal_nbv', 18, 0);
            $table->foreignId('company_id')->constrained('companies');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
