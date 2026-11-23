<?php

namespace App\Services;

use App\Models\Alocacao;
use App\Models\AvisoAusencia;
use App\Models\User;
use App\Notifications\SubstitutoAlocado;
use Illuminate\Support\Facades\DB;

class SubstitutoService
{
    /**
     * Efetiva a substituição:
     *  1. Marca a alocação original como `substituida`
     *  2. Cria uma nova alocação para o substituto (mesmo posto, turno, data)
     *  3. Atualiza o aviso para `substituto_alocado` com os metadados
     *  4. Notifica substituto e solicitante (após o commit)
     */
    public function alocar(AvisoAusencia $aviso, User $substituto, User $admin): Alocacao
    {
        $nova = DB::transaction(function () use ($aviso, $substituto, $admin) {
            $original = $aviso->alocacao;

            $original->update(['status' => 'substituida']);

            $nova = Alocacao::create([
                'escala_id' => $original->escala_id,
                'user_id' => $substituto->id,
                'posto_id' => $original->posto_id,
                'turno_id' => $original->turno_id,
                'data' => $original->data->toDateString(),
                'status' => 'confirmado',
                'substitui_alocacao_id' => $original->id,
            ]);

            $aviso->update([
                'status' => 'substituto_alocado',
                'substituto_user_id' => $substituto->id,
                'resolvido_por' => $admin->id,
                'resolvido_em' => now(),
            ]);

            return $nova;
        });

        // Notifica substituto e solicitante APÓS o commit
        $aviso->refresh()->load(['user', 'substituto', 'alocacao.posto', 'alocacao.turno']);
        $substituto->notify(new SubstitutoAlocado($aviso));
        $aviso->user->notify(new SubstitutoAlocado($aviso));

        return $nova;
    }

    /**
     * Rejeita o aviso: a alocação original volta para `confirmado`
     * e a justificativa é anexada à observação do aviso.
     */
    public function rejeitar(AvisoAusencia $aviso, ?string $justificativa, User $admin): void
    {
        DB::transaction(function () use ($aviso, $justificativa, $admin) {
            $aviso->alocacao->update(['status' => 'confirmado']);

            $observacao = $aviso->observacao ?? '';
            if ($justificativa) {
                $observacao = trim($observacao."\n\n[Rejeitado] ".$justificativa);
            }

            $aviso->update([
                'status' => 'rejeitado',
                'observacao' => $observacao ?: null,
                'resolvido_por' => $admin->id,
                'resolvido_em' => now(),
            ]);
        });
    }
}