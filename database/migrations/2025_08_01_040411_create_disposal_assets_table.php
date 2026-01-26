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
        Schema::create('disposal_assets', function (Blueprint $table) {
            $table->id();
            $table->date('submit_date');
            $table->string('form_no')->unique();
            $table->foreignId('department_id')->constrained('departments');
            $table->longText('reason');
            $table->integer('nbv');
            $table->integer('esp');
            $table->string('sequence');
            $table->string('status');
            $table->foreignId('company_id')->constrained('companies');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disposal_assets');
    }
};
