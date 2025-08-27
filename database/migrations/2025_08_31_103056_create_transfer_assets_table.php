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
        Schema::create('transfer_assets', function (Blueprint $table) {
            $table->id();
            $table->date('submit_date');
            $table->string('form_no')->unique();
            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('asset_id')->constrained('assets');
            $table->foreignId('origin_loc_id')->constrained('locations');
            $table->foreignId('destination_loc_id')->constrained('locations');
            $table->longText('reason');
            $table->string('sequence');
            $table->string('status');
            $table->foreignId('company_id')->constrained('companies');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_assets');
    }
};
