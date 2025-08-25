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
        Schema::create('detail_disposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disposal_asset_id')->constrained('disposal_assets')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('asset_id')->constrained('assets');
            $table->decimal('kurs', 18, 0);
            $table->decimal('njab', 18, 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_disposals');
    }
};
