@extends('layouts.app')
@section('title', 'Editar Colaborador')
@section('header', 'Editar Colaborador')
@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl shadow-sm">
            <ul class="list-disc list-inside text-sm font-semibold">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
        </div>
    @endif

    <!-- Formulário de Foto (Mantido sem loading state por ser ação secundária) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 md:p-8 flex flex-col md:flex-row items-center gap-6 md:gap-8">
        <div class="shrink-0">
            @if($user->foto_thumb_path)
                <img src="{{ $user->fotoThumbUrl() }}" alt="Foto de {{ $user->name }}" class="w-24 h-24 md:w-32 md:h-32 rounded-full object-cover border-4 border-white shadow-lg">
            @else
                <div class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-gray-100 flex items-center justify-center border-4 border-white shadow-lg text-gray-400">
                    <svg class="w-12 h-12 md:w-16 md:h-16" fill="currentColor" viewBox="0 0 24 24"><path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <span class="hidden">{{ substr($user->name, 0, 1) }}</span>
                </div>
            @endif
        </div>
        <div class="flex-1 w-full text-center md:text-left">
            <h3 class="text-lg font-bold text-gray-800 mb-2">Foto de Perfil</h3>
            <p class="text-sm text-gray-500 mb-4">JPG ou PNG. Máx: 2MB.</p>
            <form action="{{ route('admin.usuarios.foto.update', $user) }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-3">
                @csrf
                <input type="file" name="foto" required accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-gray-200 rounded-lg bg-gray-50">
                <button type="submit" class="w-full sm:w-auto bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-6 rounded-lg shadow-md whitespace-nowrap">Atualizar</button>
            </form>
        </div>
    </div>

    <!-- Formulário de Dados Cadastrais (COM LOADING STATE ALPINE.JS) -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 md:p-8">
        <form 
            id="form-editar-usuario" 
            x-data="{ loading: false }" 
            x-on:submit="loading = true" 
            action="{{ route('admin.usuarios.update', $user) }}" 
            method="POST"
        >
            @csrf @method('PUT')
            <h3 class="text-lg font-bold text-gray-800 mb-6 border-b border-gray-100 pb-2">Dados Cadastrais</h3>
            
            <div class="mb-5">
                <label class="block text-sm font-bold text-gray-700 mb-2">Nome Completo</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-5">
                <label class="block text-sm font-bold text-gray-700 mb-2">E-mail</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-5">
                <label class="block text-sm font-bold text-gray-700 mb-2">Cargo</label>
                <select name="cargo" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                    <option value="porteiro" {{ old('cargo', $user->cargo) == 'porteiro' ? 'selected' : '' }}>Porteiro</option>
                    <option value="recepcionista" {{ old('cargo', $user->cargo) == 'recepcionista' ? 'selected' : '' }}>Recepcionista</option>
                    <option value="admin" {{ old('cargo', $user->cargo) == 'admin' ? 'selected' : '' }}>Administrador</option>
                </select>
            </div>
            <div class="mb-5">
                <label class="block text-sm font-bold text-gray-700 mb-2">Nova Senha (Opcional)</label>
                <input type="password" name="password" minlength="6" placeholder="Deixe em branco para manter" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-8 flex items-center">
                <input type="checkbox" name="active" id="active" value="1" {{ old('active', $user->active) ? 'checked' : '' }} {{ auth()->id() === $user->id ? 'disabled' : '' }} class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="active" class="ml-2 text-sm font-bold text-gray-700">Usuário Ativo</label>
            </div>
            
            <div class="flex flex-col md:flex-row justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.usuarios.index') }}" class="w-full md:w-auto text-center px-6 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-lg hover:bg-gray-200">Cancelar</a>
                
                <!-- Botão com Alpine.js -->
                <button 
                    type="submit" 
                    id="btn-salvar-usuario"
                    x-bind:disabled="loading"
                    class="w-full md:w-auto px-6 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 shadow-md disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 transition-all"
                >
                    <svg x-show="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="loading ? 'Salvando...' : 'Salvar'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection