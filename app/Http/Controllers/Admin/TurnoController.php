<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Turno;
use Illuminate\Http\Request;

class TurnoController extends Controller
{
    public function index()
    {
        $turnos = Turno::orderBy('nome')->paginate(10);
        return view('admin.turnos.index', compact('turnos'));
    }

    public function create()
    {
        return view('admin.turnos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255', 'unique:turnos'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fim' => ['required', 'date_format:H:i'],
            'tolerancia_atraso_min' => ['required', 'integer', 'min:0'],
            'intervalo_min' => ['required', 'integer', 'min:0'],
        ]);

        Turno::create($validated);

        return redirect()->route('admin.turnos.index')
            ->with('success', 'Turno criado com sucesso!');
    }

    public function edit(Turno $turno)
    {
        return view('admin.turnos.edit', compact('turno'));
    }

    public function update(Request $request, Turno $turno)
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255', 'unique:turnos,nome,' . $turno->id],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fim' => ['required', 'date_format:H:i'],
            'tolerancia_atraso_min' => ['required', 'integer', 'min:0'],
            'intervalo_min' => ['required', 'integer', 'min:0'],
        ]);

        $turno->update($validated);

        return redirect()->route('admin.turnos.index')
            ->with('success', 'Turno atualizado com sucesso!');
    }
}