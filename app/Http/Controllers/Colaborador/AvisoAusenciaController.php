<?php

namespace App\Http\Controllers\Colaborador;

use App\Http\Controllers\Controller;
use App\Models\Alocacao;
use App\Models\AvisoAusencia;
use App\Models\User;
use App\Notifications\AvisoAusenciaCriado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class AvisoAusenciaController extends Controller
{
    public function create(Alocacao $alocacao)
    {
        abort_if($alocacao->user_id !== auth()->id(), 403, 'Não autorizado.');
        $alocacao->load(['posto', 'turno']);

        return view('ausencia.create', compact('alocacao'));
    }

    public function store(Request $request, Alocacao $alocacao)
    {
        // 1. Garante que o usuário é dono da alocação
        abort_if($alocacao->user_id !== auth()->id(), 403, 'Não autorizado.');

        // 2. ✅ NOVO: Garante que APENAS alocações confirmadas podem ter ausência solicitada
        abort_if($alocacao->status !== 'confirmado', 403, 'Apenas alocações confirmadas podem ter ausência solicitada.');

        $validated = $request->validate([
            'motivo' => 'required|in:atestado_medico,emergencia_pessoal,imprevisto,folga_negociada,outro',
            'observacao' => 'nullable|string|max:500',
            'anexo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $urgente = now()->diffInHours($alocacao->data, false) < 24;

        $aviso = DB::transaction(function () use ($request, $alocacao, $validated, $urgente) {
            $anexoPath = null;
            if ($request->hasFile('anexo')) {
                $anexoPath = $request->file('anexo')->store('ausencias/anexos', 'public');
            }

            $aviso = AvisoAusencia::create([
                'alocacao_id' => $alocacao->id,
                'user_id' => auth()->id(),
                'motivo' => $validated['motivo'],
                'observacao' => $validated['observacao'] ?? null,
                'anexo_path' => $anexoPath,
                'urgente' => $urgente,
                'status' => 'pendente',
            ]);

            $alocacao->update(['status' => 'pendente']);

            return $aviso;
        });

        $aviso->load(['user', 'alocacao.posto', 'alocacao.turno']);
        Notification::send(
            User::role('admin')->get(),
            new AvisoAusenciaCriado($aviso),
        );

        return redirect()
            ->route('minha-escala')
            ->with('success', 'Aviso de ausência enviado com sucesso! A administração será notificada.');
    }
}