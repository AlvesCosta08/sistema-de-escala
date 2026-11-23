@extends('layouts.app')

@section('title', 'Minha Escala')
@section('header', 'Minha Escala de Trabalho')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Cabeçalho e Filtro de Mês -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <p class="text-slate-600 text-sm md:text-base">Confira abaixo os seus plantões agendados. Se não puder comparecer, avise com antecedência.</p>
        </div>

        <!-- Seletor de Mês -->
        <form method="GET" action="{{ route('minha-escala') }}" class="flex items-center gap-2">
            <input 
                type="month" 
                name="mes" 
                value="{{ $mes }}" 
                class="border border-slate-300 rounded-xl px-4 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                onchange="this.form.submit()"
            >
            <noscript>
                <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-xl text-sm font-bold">Filtrar</button>
            </noscript>
        </form>
    </div>

    <!-- Lista de Plantões -->
    <div class="grid gap-4">
        @forelse($alocacoes as $alocacao)
            <div class="bg-white rounded-2xl shadow-sm border-l-4 {{ $alocacao->status === 'pendente' ? 'border-amber-400' : 'border-indigo-500' }} p-4 md:p-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 card-hover">
                <div class="flex items-center gap-4 md:gap-6 w-full md:w-auto">
                    
                    <!-- Bloco da Data -->
                    <div class="text-center bg-slate-50 rounded-xl p-3 min-w-[70px] border border-slate-100 shadow-inner">
                        <span class="block text-xs font-bold text-slate-500 uppercase tracking-wide">
                            {{ \Carbon\Carbon::parse($alocacao->data)->translatedFormat('M') }}
                        </span>
                        <span class="block text-2xl font-black text-slate-800">
                            {{ \Carbon\Carbon::parse($alocacao->data)->format('d') }}
                        </span>
                    </div>
                    
                    <!-- Informações -->
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">
                            {{ $alocacao->posto->nome ?? 'Posto não informado' }}
                        </h3>
                        <p class="text-sm text-slate-500 flex items-center gap-1 mt-1 font-medium">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ $alocacao->turno->nome ?? 'Turno não informado' }} 
                            @if($alocacao->turno)
                                ({{ \Carbon\Carbon::parse($alocacao->turno->hora_inicio)->format('H:i') }} às {{ \Carbon\Carbon::parse($alocacao->turno->hora_fim)->format('H:i') }})
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="w-full md:w-auto flex justify-end mt-2 md:mt-0">
                    @if($alocacao->status === 'confirmado')
                        <a href="{{ route('ausencia.create', $alocacao) }}" class="w-full md:w-auto text-center border-2 border-rose-500 text-rose-600 hover:bg-rose-50 font-bold py-2 px-5 rounded-xl text-sm transition-colors btn-interactive">
                            Não poderei comparecer
                        </a>
                    @else
                        <span class="inline-flex items-center justify-center gap-2 bg-amber-50 text-amber-700 border border-amber-200 font-bold px-4 py-2 rounded-xl text-sm w-full md:w-auto text-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Ausência Solicitada
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <!-- Mensagem quando não há plantões -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-12 text-center">
                <svg class="w-20 h-20 mx-auto text-slate-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <p class="text-slate-500 text-lg font-medium">Você não possui plantões agendados.</p>
                <p class="text-slate-400 text-sm mt-1">Não há escalas para o mês selecionado.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection