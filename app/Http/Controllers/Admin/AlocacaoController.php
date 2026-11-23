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

        $colaboradores = User::where('active', true)->whereIn('cargo', ['porteiro', 'recepcionista'])->orderBy('name')->get();
        $postos = Posto::orderBy('nome')->get();
        $turnos = Turno::orderBy('nome')->get();

        return view('admin.escalas.alocacoes', compact('escala', 'alocacoes', 'colaboradores', 'postos', 'turnos'));
    }

    public function store(Request $request, Escala $escala)
    {
        $validated = $request->validate([
            'user_id' => [
                'required', 'exists:users,id',
                function ($attribute, $value, $fail) use ($request, $escala) {
                    $exists = Alocacao::where('escala_id', $escala->id)
                        ->where('user_id', $value)
                        ->whereDate('data', $request->data) // <-- CORREÇÃO AQUI
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
}