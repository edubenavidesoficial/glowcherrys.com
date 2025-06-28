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
        Schema::create('gift_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('quantity'); // cantidad de diamantes
            $table->decimal('price_usd', 8, 2); // precio en dólares
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_packages');
    }
};
