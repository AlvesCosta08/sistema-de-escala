@extends('layouts.app')

@section('title', 'Gestão de Escalas')
@section('header', 'Gestão de Escalas')

@section('content')
<div class="space-y-4">
    
    <!-- Formulário estilo "Postar" COM LOADING STATE -->
    <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 mb-6">
        <h3 class="text-sm font-bold text-gray-700 mb-3">Criar nova escala mensal</h3>
        <form id="form-criar-escala" x-data="{ loading: false }" x-on:submit="loading = true" action="{{ route('admin.escalas.store') }}" method="POST" class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
            @csrf
            <input type="month" name="mes_referencia" required class="w-full sm:w-auto bg-white border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            <button type="submit" id="btn-criar-escala" x-bind:disabled="loading" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-full text-sm shadow-md transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                <svg x-show="loading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="loading ? 'Criando...' : 'Criar Escala'"></span>
            </button>
        </form>
        @error('mes_referencia') <div class="text-rose-500 text-xs mt-2 font-semibold">{{ $message }}</div> @enderror
    </div>

    <!-- Feed de Escalas -->
    @forelse($escalas as $escala)
    <div class="p-4 bg-white rounded-2xl border border-gray-100 hover:bg-gray-50 transition cursor-pointer group flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-900 group-hover:text-amber-600 transition-colors whitespace-nowrap">
                    @php
                        $meses = [
                            '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
                            '04' => 'Abril', '05' => 'Maio', '06' => 'Junho',
                            '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro',
                            '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
                        ];
                        $ano = substr($escala->mes_referencia, 0, 4);
                        $mes = substr($escala->mes_referencia, 5, 2);
                    @endphp
                    {{ $meses[$mes] ?? $mes }}/{{ $ano }}
                </h3>
                <div class="flex items-center gap-2 mt-1">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold ring-1 ring-inset {{ $escala->status === 'publicada' ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-amber-50 text-amber-700 ring-amber-200' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $escala->status === 'publicada' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                        {{ ucfirst($escala->status) }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="flex flex-wrap items-center justify-end gap-2 border-t md:border-t-0 pt-3 md:pt-0 border-gray-100">
            <a href="{{ route('admin.escalas.alocacoes.index', $escala) }}" class="text-indigo-600 bg-indigo-50 hover:bg-indigo-100 font-bold py-2 px-5 rounded-full text-sm transition">
                Alocações
            </a>
            @if($escala->status === 'rascunho')
            <form action="{{ route('admin.escalas.publicar', $escala) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="text-white bg-gray-900 hover:bg-gray-800 font-bold py-2 px-5 rounded-full text-sm transition">Publicar</button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div class="text-center p-10 bg-white rounded-2xl border border-gray-100 text-gray-500">
        Nenhuma escala criada.
    </div>
    @endforelse

    <div class="mt-6">{{ $escalas->links() }}</div>
</div>
@endsection