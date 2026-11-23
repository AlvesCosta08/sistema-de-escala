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
        Schema::create('escalas', function (Blueprint $table) {
            $table->id();
            $table->string('mes_referencia', 7); // ex.: 2026-11
            $table->enum('status', ['rascunho', 'publicada'])->default('rascunho');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escalas');
    }
};
