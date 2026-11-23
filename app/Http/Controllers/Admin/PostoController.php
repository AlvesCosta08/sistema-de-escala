<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Posto;
use Illuminate\Http\Request;

class PostoController extends Controller
{
    public function index()
    {
        $postos = Posto::orderBy('nome')->paginate(10);
        return view('admin.postos.index', compact('postos'));
    }

    public function create()
    {
        return view('admin.postos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255', 'unique:postos'],
            'descricao' => ['nullable', 'string', 'max:255'],
        ]);

        Posto::create($validated);

        return redirect()->route('admin.postos.index')
            ->with('success', 'Posto criado com sucesso!');
    }

    public function edit(Posto $posto)
    {
        return view('admin.postos.edit', compact('posto'));
    }

    public function update(Request $request, Posto $posto)
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255', 'unique:postos,nome,' . $posto->id],
            'descricao' => ['nullable', 'string', 'max:255'],
        ]);

        $posto->update($validated);

        return redirect()->route('admin.postos.index')
            ->with('success', 'Posto atualizado com sucesso!');
    }
}