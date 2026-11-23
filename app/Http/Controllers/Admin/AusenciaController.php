<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AlocarSubstitutoRequest;
use App\Http\Requests\RejeitarAvisoRequest;
use App\Models\AvisoAusencia;
use App\Models\User;
use App\Services\SubstitutoService;

class AusenciaController extends Controller
{
    public function index()
    {
        $avisos = AvisoAusencia::with([
                'user:id,name,foto_thumb_path',
                'alocacao.posto:id,nome',
                'alocacao.turno:id,nome,hora_inicio,hora_fim',
            ])
            ->where('status', 'pendente')
            ->orderByDesc('urgente')
            ->orderBy('created_at')
            ->get();

        return view('admin.ausencias.index', compact('avisos'));
    }

    public function show(AvisoAusencia $aviso)
    {
        $aviso->load([
            'user:id,name,foto_thumb_path',
            'alocacao.posto',
            'alocacao.turno',
        ]);

        $substitutos = User::elegiveisParaSubstituir($aviso->alocacao);

        return view('admin.ausencias.show', compact('aviso', 'substitutos'));
    }

    public function alocar(
        AlocarSubstitutoRequest $request,
        AvisoAusencia $aviso,
        SubstitutoService $service,
    ) {
        $substituto = User::findOrFail($request->substituto_user_id);
        $service->alocar($aviso, $substituto, $request->user());

        return redirect()
            ->route('admin.ausencias.index')
            ->with('status', "Substituto {$substituto->name} alocado com sucesso.");
    }

    public function rejeitar(
        RejeitarAvisoRequest $request,
        AvisoAusencia $aviso,
        SubstitutoService $service,
    ) {
        $service->rejeitar($aviso, $request->justificativa, $request->user());

        return redirect()
            ->route('admin.ausencias.index')
            ->with('status', 'Aviso rejeitado.');
    }

    public function destroy(AvisoAusencia $aviso)
    {
        $aviso->delete();

        return redirect()
            ->route('admin.ausencias.index')
            ->with('success', 'Aviso de ausência excluído com sucesso.');
    }
}