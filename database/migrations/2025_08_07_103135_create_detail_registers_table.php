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
        Schema::create('detail_registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('register_asset_id')->constrained('register_assets')->onUpdate('cascade')->onDelete('cascade');
            $table->string('po_no');
            $table->string('invoice_no');
            $table->date('commission_date')->nullable();
            $table->string('specification');
            $table->foreignId('asset_name_id')->constrained('asset_names');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_registers');
    }
};
