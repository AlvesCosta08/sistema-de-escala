<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alocacao;
use App\Models\Escala; // <-- Adicionado
use Carbon\Carbon;
use Illuminate\Http\Request;

class EscalaController extends Controller
{
    public function index(Request $request)
    {
        // Mês de referência: ?mes=YYYY-MM ou o mês atual
        $mes = $request->get('mes', now()->format('Y-m'));

        // Garante formato válido (senão cai para o mês atual)
        if (! preg_match('/^\d{4}-\d{2}$/', $mes)) {
            $mes = now()->format('Y-m');
        }

        $referencia = Carbon::createFromFormat('Y-m', $mes)->startOfMonth();

        $alocacoes = Alocacao::with([
                'user:id,name,foto_thumb_path',
                'posto:id,nome',
                'turno:id,nome,hora_inicio,hora_fim',
            ])
            ->whereYear('data', $referencia->year)
            ->whereMonth('data', $referencia->month)
            ->orderBy('data')
            ->get()
            ->groupBy(fn (Alocacao $a) => $a->data->format('Y-m-d'));

        return view('admin.escalas.index', [
            'alocacoesPorDia' => $alocacoes,
            'mes' => $mes,
            'referencia' => $referencia,
        ]);
    }

    // --- MÉTODOS DE GERENCIAMENTO DE ESCALAS (TDD) ---

    public function gerenciar()
    {
        $escalas = Escala::orderBy('mes_referencia', 'desc')->paginate(10);
        return view('admin.escalas.gerenciar', compact('escalas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mes_referencia' => ['required', 'date_format:Y-m', 'unique:escalas,mes_referencia'],
        ]);

        Escala::create([
            'mes_referencia' => $validated['mes_referencia'],
            'status' => 'rascunho',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.escalas.gerenciar')
            ->with('success', 'Escala criada com sucesso!');
    }

    public function publicar(Escala $escala)
    {
        $escala->update(['status' => 'publicada']);

        return redirect()->route('admin.escalas.gerenciar')
            ->with('success', 'Escala publicada com sucesso!');
    }
}