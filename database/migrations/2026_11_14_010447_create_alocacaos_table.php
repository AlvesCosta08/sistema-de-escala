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
        Schema::create('alocacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('escala_id')->constrained('escalas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('posto_id')->constrained('postos');
            $table->foreignId('turno_id')->constrained('turnos');
            $table->date('data');
            $table->enum('status', [
                'confirmado', 'pendente', 'ausente', 'substituida', 'descoberta'
            ])->default('confirmado');
            $table->foreignId('aviso_id')->nullable()->constrained('aviso_ausencias');
            $table->foreignId('substitui_alocacao_id')->nullable()->constrained('alocacoes');
            $table->timestamps();

            $table->unique(['user_id', 'data', 'turno_id']); // evita duplicidade no mesmo turno
            $table->index(['data', 'posto_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alocacaos');
    }
};
