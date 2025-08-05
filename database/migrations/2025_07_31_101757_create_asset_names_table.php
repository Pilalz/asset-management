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
        Schema::create('asset_names', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_class_id')->constrained('asset_sub_classes')->onUpdate('cascade')->onDelete('no action');
            $table->string('name');
            $table->string('code');
            $table->integer('commercial');
            $table->integer('fiscal');
            $table->integer('cost');
            $table->integer('lva');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_names');
    }
};
