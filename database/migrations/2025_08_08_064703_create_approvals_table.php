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
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('register_asset_id')->constrained('register_assets')->onUpdate('cascade')->onDelete('cascade');
            $table->string('approval_action');
            $table->string('role');
            $table->foreignId('user_id')->constrained('users')->onUpdate('no action')->onDelete('no action')->nullable();
            $table->string('status');
            $table->date('approval_date')->nullable();
            $table->integer('approval_order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
