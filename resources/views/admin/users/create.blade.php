@extends('layouts.app')
@section('title', 'Novo Colaborador')
@section('header', 'Novo Colaborador')
@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 p-4 md:p-8">
    <form 
        id="form-criar-usuario" 
        x-data="{ loading: false }" 
        x-on:submit="loading = true" 
        action="{{ route('admin.usuarios.store') }}" 
        method="POST"
    >
        @csrf
        
        @if ($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl">
                <ul class="list-disc list-inside text-sm font-semibold">
                    @foreach ($errors->all() as $error) 
                        <li>{{ $error }}</li> 
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-5">
            <label class="block text-sm font-bold text-gray-700 mb-2">Nome Completo</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="mb-5">
            <label class="block text-sm font-bold text-gray-700 mb-2">E-mail</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="mb-5">
            <label class="block text-sm font-bold text-gray-700 mb-2">Cargo / Função</label>
            <select name="cargo" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                <option value="">Selecione...</option>
                <option value="porteiro" {{ old('cargo') == 'porteiro' ? 'selected' : '' }}>Porteiro</option>
                <option value="recepcionista" {{ old('cargo') == 'recepcionista' ? 'selected' : '' }}>Recepcionista</option>
                <option value="admin" {{ old('cargo') == 'admin' ? 'selected' : '' }}>Administrador</option>
            </select>
        </div>

        <div class="mb-5">
            <label class="block text-sm font-bold text-gray-700 mb-2">Senha de Acesso</label>
            <input type="password" name="password" required minlength="6" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="mb-8 flex items-center">
            <input type="checkbox" name="active" id="active" value="1" {{ old('active', true) ? 'checked' : '' }} class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
            <label for="active" class="ml-2 text-sm font-bold text-gray-700">Usuário Ativo</label>
        </div>

        <div class="flex flex-col md:flex-row justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.usuarios.index') }}" class="w-full md:w-auto text-center px-6 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-lg hover:bg-gray-200">Cancelar</a>
            
            <!-- Botão Padronizado com Alpine.js -->
            <button 
                type="submit" 
                id="btn-salvar-novo-usuario"
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
@endsection