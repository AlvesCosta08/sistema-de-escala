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
        Schema::create('aviso_ausencias', function (Blueprint $table) {
            $table->id();

            // Coluna criada sem FK — a constraint será adicionada depois,
            // quando a tabela 'alocacoes' já existir.
            $table->unsignedBigInteger('alocacao_id')->nullable();

            $table->foreignId('user_id')->constrained('users');
            $table->enum('motivo', [
                'atestado_medico', 'emergencia_pessoal', 'imprevisto', 'folga_negociada', 'outro',
            ]);
            $table->text('observacao')->nullable();
            $table->string('anexo_path')->nullable();
            $table->boolean('urgente')->default(false);
            $table->enum('status', [
                'pendente', 'substituto_alocado', 'cancelado', 'rejeitado', 'posto_descoberto',
            ])->default('pendente');
            $table->foreignId('substituto_user_id')->nullable()->constrained('users');
            $table->foreignId('resolvido_por')->nullable()->constrained('users');
            $table->timestamp('resolvido_em')->nullable();
            $table->boolean('abonada')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aviso_ausencias');
    }
};
