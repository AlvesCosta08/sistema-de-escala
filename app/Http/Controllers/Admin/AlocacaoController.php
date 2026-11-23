<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alocacao;
use App\Models\Escala;
use App\Models\Posto;
use App\Models\Turno;
use App\Models\User;
use Illuminate\Http\Request;

class AlocacaoController extends Controller
{
    public function index(Escala $escala)
    {
        $alocacoes = Alocacao::with(['user', 'posto', 'turno'])
            ->where('escala_id', $escala->id)
            ->orderBy('data')
            ->orderBy('turno_id')
            ->paginate(20);

        // Eventos formatados para o FullCalendar
        $eventos = Alocacao::with(['user:id,name', 'posto:id,nome', 'turno:id,nome,hora_inicio,hora_fim'])
            ->where('escala_id', $escala->id)
            ->get()
            ->map(function ($alocacao) {
                return [
                    'id' => $alocacao->id,
                    'title' => $alocacao->user->name . ' - ' . $alocacao->posto->nome,
                    'start' => $alocacao->data . 'T' . $alocacao->turno->hora_inicio,
                    'end' => $alocacao->data . 'T' . $alocacao->turno->hora_fim,
                    'backgroundColor' => $alocacao->status === 'confirmado' ? '#4f46e5' : '#f59e0b',
                    'borderColor' => $alocacao->status === 'confirmado' ? '#4338ca' : '#d97706',
                    'extendedProps' => [
                        'user_id' => $alocacao->user_id,
                        'posto_id' => $alocacao->posto_id,
                        'turno_id' => $alocacao->turno_id,
                        'status' => $alocacao->status,
                    ],
                ];
            });

        $colaboradores = User::where('active', true)->whereIn('cargo', ['porteiro', 'recepcionista'])->orderBy('name')->get();
        $postos = Posto::orderBy('nome')->get();
        $turnos = Turno::orderBy('nome')->get();

        return view('admin.escalas.alocacoes', compact('escala', 'alocacoes', 'colaboradores', 'postos', 'turnos', 'eventos'));
    }

    public function store(Request $request, Escala $escala)
    {
        $validated = $request->validate([
            'user_id' => [
                'required', 'exists:users,id',
                function ($attribute, $value, $fail) use ($request, $escala) {
                    $exists = Alocacao::where('escala_id', $escala->id)
                        ->where('user_id', $value)
                        ->whereDate('data', $request->data)
                        ->where('turno_id', $request->turno_id)
                        ->exists();
                    if ($exists) {
                        $fail('Este colaborador já está alocado neste turno nesta data.');
                    }
                },
            ],
            'posto_id' => ['required', 'exists:postos,id'],
            'turno_id' => ['required', 'exists:turnos,id'],
            'data' => ['required', 'date'],
        ]);

        Alocacao::create([
            'escala_id' => $escala->id,
            'user_id' => $validated['user_id'],
            'posto_id' => $validated['posto_id'],
            'turno_id' => $validated['turno_id'],
            'data' => $validated['data'],
            'status' => 'confirmado',
        ]);

        return redirect()->route('admin.escalas.alocacoes.index', $escala)
            ->with('success', 'Colaborador alocado com sucesso!');
    }

    public function destroy(Escala $escala, Alocacao $alocacao)
    {
        $alocacao->delete();
        return redirect()->route('admin.escalas.alocacoes.index', $escala)
            ->with('success', 'Alocação removida com sucesso!');
    }

    /**
     * Move uma alocação para outra data via drag-and-drop do calendário.
     */
    public function mover(Request $request, Escala $escala, Alocacao $alocacao)
    {
        $request->validate([
            'data' => 'required|date',
        ]);

        // Validação de conflito
        $conflito = Alocacao::where('escala_id', $escala->id)
            ->where('user_id', $alocacao->user_id)
            ->where('turno_id', $alocacao->turno_id)
            ->whereDate('data', $request->data)
            ->where('id', '!=', $alocacao->id)
            ->exists();

        if ($conflito) {
            return response()->json([
                'success' => false,
                'message' => 'Este colaborador já está alocado neste turno nesta data.',
            ], 422);
        }

        $alocacao->update(['data' => $request->data]);

        return response()->json(['success' => true, 'message' => 'Plantão movido com sucesso!']);
    }
}