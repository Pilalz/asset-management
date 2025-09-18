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
        Schema::create('insurances', function (Blueprint $table) {
            $table->id();
            $table->string('polish_no');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('instance_name')->nullable();
            $table->integer('annual_premium')->nullable();
            $table->integer('schedule')->nullable();
            $table->date('next_payment')->nullable();
            $table->string('status')->nullable();
            $table->foreignId('company_id')->constrained('companies');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurances');
    }
};
