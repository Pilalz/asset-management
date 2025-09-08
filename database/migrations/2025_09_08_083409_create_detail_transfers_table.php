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
        Schema::create('detail_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfer_asset_id')->constrained('transfer_assets')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('asset_id')->constrained('assets');
            $table->foreignId('origin_loc_id')->constrained('locations');
            $table->foreignId('destination_loc_id')->constrained('locations');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_transfers');
    }
};
