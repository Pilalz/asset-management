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
            $table->foreignId('department_id')->constrained('departments')->onUpdate('cascade')->onDelete('no action');
            $table->longText('reason');
            $table->integer('nbv');
            $table->integer('esp');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->timestamps();
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
