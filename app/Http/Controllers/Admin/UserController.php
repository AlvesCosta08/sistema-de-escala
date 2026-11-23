<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'cargo' => ['required', 'in:admin,porteiro,recepcionista'],
            'password' => ['required', 'string', 'min:6'],
            'active' => ['boolean'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'cargo' => $validated['cargo'],
            'password' => Hash::make($validated['password']),
            'active' => $request->has('active'),
        ]);

        $user->assignRole($validated['cargo']);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Colaborador criado com sucesso!');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'cargo' => ['required', 'in:admin,porteiro,recepcionista'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'cargo' => $validated['cargo'],
            'active' => $request->has('active'),
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->syncRoles([$validated['cargo']]);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Colaborador atualizado com sucesso!');
    }

    public function toggle(User $user)
    {
        // Não permite desativar o próprio admin logado
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Você não pode desativar seu próprio perfil.');
        }

        $user->update(['active' => !$user->active]);
        
        $status = $user->active ? 'ativado' : 'desativado';
        return back()->with('success', "Colaborador {$status} com sucesso.");
    }
}