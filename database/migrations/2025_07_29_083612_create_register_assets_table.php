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
        Schema::create('register_assets', function (Blueprint $table) {
            $table->id();
            $table->string('form_no')->unique();
            $table->foreignId('department_id')->constrained('departments')->onUpdate('cascade')->onDelete('no action');
            $table->foreignId('location_id')->constrained('locations')->onUpdate('cascade')->onDelete('no action');
            $table->boolean('insured');
            $table->string('sequence');
            $table->string('status');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('register_assets');
    }
};
