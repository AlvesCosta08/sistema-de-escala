@extends('layouts.app')
@section('title', 'Editar Culto')
@section('header', 'Editar Culto')
@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
    <form id="form-editar-turno" x-data="{ loading: false }" x-on:submit="loading = true" action="{{ route('admin.turnos.update', $turno) }}" method="POST">
        @csrf
        @method('PUT')
        @if ($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl">
                <ul class="list-disc list-inside text-sm font-semibold">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
            </div>
        @endif
        <div class="mb-5">
            <label class="block text-sm font-bold text-gray-700 mb-2">Nome do Culto</label>
            <input type="text" name="nome" value="{{ old('nome', $turno->nome) }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="grid grid-cols-2 gap-4 mb-5">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Hora de Início</label>
                <input type="time" name="hora_inicio" value="{{ old('hora_inicio', \Carbon\Carbon::parse($turno->hora_inicio)->format('H:i')) }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Hora de Fim</label>
                <input type="time" name="hora_fim" value="{{ old('hora_fim', \Carbon\Carbon::parse($turno->hora_fim)->format('H:i')) }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4 mb-8">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Tolerância (Opcional)</label>
                <input type="number" name="tolerancia_atraso_min" value="{{ old('tolerancia_atraso_min', $turno->tolerancia_atraso_min) }}" min="0" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Intervalo (Opcional)</label>
                <input type="number" name="intervalo_min" value="{{ old('intervalo_min', $turno->intervalo_min) }}" min="0" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.turnos.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-lg hover:bg-gray-200">Cancelar</a>
            <button type="submit" id="btn-atualizar-turno" x-bind:disabled="loading" class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 shadow-md disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 transition-all">
                <svg x-show="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span x-text="loading ? 'Salvando...' : 'Atualizar Turno'"></span>
            </button>
        </div>
    </form>
</div>
@endsection