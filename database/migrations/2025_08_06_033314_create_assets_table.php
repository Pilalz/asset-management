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
            $table->string('asset_number')->unique();
            $table->foreignId('asset_name_id')->constrained('asset_names')->onUpdate('cascade')->onDelete('cascade');
            //obj
            $table->string('status');
            $table->string('description');
            $table->longText('detail')->nullable();
            //pareto
            $table->string('unit_no')->nullable();
            $table->string('sn_chassis')->nullable();
            $table->string('sn_engine')->nullable();
            //po no
            $table->foreignId('location_id')->constrained('locations')->onUpdate('cascade')->onDelete('no action');
            $table->foreignId('department_id')->constrained('departments')->onUpdate('cascade')->onDelete('no action');
            $table->bigInteger('quantity');
            $table->date('capitalized_date');
            $table->date('start_depre_date');
            $table->decimal('acquisition_value', 18, 0);
            $table->decimal('current_cost', 18, 0);
            $table->integer('useful_life_month');
            $table->decimal('accum_depre', 18, 0);
            $table->decimal('net_book_value', 18, 0);
            $table->foreignId('company_id')->constrained('companies')->onUpdate('no action')->onDelete('no action');
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
