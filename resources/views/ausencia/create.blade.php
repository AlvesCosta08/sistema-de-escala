@extends('layouts.app')

@section('title', 'Aviso de ausência')

@section('content')
    <header class="mb-4">
        <a href="{{ route('minha-escala') }}" class="text-sm text-slate-500 hover:text-slate-800 transition">← Voltar</a>
        <h1 class="mt-1 text-xl font-bold">Aviso de ausência</h1>
        <p class="text-xs text-slate-500">
            {{ $alocacao->posto->nome }} ·
            {{ $alocacao->data->isoFormat('ddd, DD/MM') }} ·
            {{ substr($alocacao->turno->hora_inicio, 0, 5) }}–{{ substr($alocacao->turno->hora_fim, 0, 5) }}
        </p>
    </header>

    {{-- ✅ ADICIONADO: ID e diretivas Alpine.js para Loading State --}}
    <form 
        id="form-solicitar-ausencia" 
        x-data="{ loading: false }" 
        x-on:submit="loading = true" 
        method="POST" 
        action="{{ route('ausencia.store', $alocacao) }}"
        enctype="multipart/form-data"
        class="space-y-4 rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200"
    >
        @csrf

        <div>
            <label class="mb-1 block text-sm font-medium">Motivo *</label>
            @foreach (['atestado_medico' => 'Atestado médico', 'emergencia_pessoal' => 'Emergência pessoal', 'imprevisto' => 'Imprevisto', 'folga_negociada' => 'Folga negociada', 'outro' => 'Outro'] as $valor => $label)
                <label class="flex items-center gap-2 py-1 cursor-pointer">
                    <input type="radio" name="motivo" value="{{ $valor }}" @checked(old('motivo') === $valor) required class="text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm">{{ $label }}</span>
                </label>
            @endforeach
            @error('motivo')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="observacao" class="mb-1 block text-sm font-medium">Observação</label>
            <textarea id="observacao" name="observacao" rows="3" class="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">{{ old('observacao') }}</textarea>
        </div>

        <div>
            <label for="anexo" class="mb-1 block text-sm font-medium">Anexo (atestado)</label>
            <input id="anexo" type="file" name="anexo" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
        </div>

        {{-- ✅ ADICIONADO: Botão com Loading State e Spinner --}}
        <button 
            type="submit" 
            id="btn-enviar-ausencia"
            x-bind:disabled="loading"
            class="w-full rounded-lg bg-red-600 py-2 text-sm font-semibold text-white hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 transition-all"
        >
            <svg x-show="loading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span x-text="loading ? 'Enviando...' : 'Confirmar aviso'"></span>
        </button>
    </form>
@endsection