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
        Schema::create('stock_opname_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['Open', 'Closed'])->default('Open');
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        Schema::create('stock_opname_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('so_session_id')->constrained('stock_opname_sessions')->onDelete('cascade');
            $table->foreignId('asset_id')->constrained();                    
            $table->enum('status', ['Missing', 'Found'])->default('Missing');
            // PEMBANDING 1: LOKASI
            $table->foreignId('system_location_id')->constrained('locations');
            $table->foreignId('actual_location_id')->nullable()->constrained('locations');
            // PEMBANDING 2: USER
            $table->string('system_user')->nullable();
            $table->string('actual_user')->nullable();
            // PEMBANDING 3: KONDISI
            $table->string('system_condition');
            $table->string('actual_condition')->nullable();
            // DATA TAMBAHAN SCAN
            $table->text('note')->nullable();
            $table->string('attachment_path')->nullable();
            $table->dateTime('scanned_at')->nullable();
            $table->foreignId('scanned_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index(['so_session_id', 'status']);
            $table->index('asset_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opname_details');
        Schema::dropIfExists('stock_opname_sessions');
    }
};
