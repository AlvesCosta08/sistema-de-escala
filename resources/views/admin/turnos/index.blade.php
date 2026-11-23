@extends('layouts.app')

@section('title', 'Cultos')
@section('header', 'Cultos')

@section('content')
<div class="space-y-4" x-data="{ search: '' }" x-init="
    $watch('search', value => {
        const query = value.toLowerCase().trim();
        const cards = document.querySelectorAll('[data-turno-card]');
        let visible = 0;
        
        cards.forEach(card => {
            const name = card.dataset.name.toLowerCase();
            const horario = card.dataset.horario.toLowerCase();
            
            if (!query || name.includes(query) || horario.includes(query)) {
                card.style.display = '';
                visible++;
            } else {
                card.style.display = 'none';
            }
        });
        
        const countEl = document.getElementById('turno-count');
        if (countEl) {
            countEl.textContent = visible === 1 ? '1 turno encontrado' : visible + ' turnos encontrados';
        }
        
        const emptyState = document.getElementById('empty-state');
        if (emptyState) {
            emptyState.style.display = (query && visible === 0) ? 'block' : 'none';
        }
        
        const pagination = document.getElementById('pagination-container');
        if (pagination) {
            pagination.style.display = query ? 'none' : 'block';
        }
    })
">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 bg-gray-50 p-4 rounded-2xl">
        <p class="text-gray-600 text-sm font-medium">Horários e regras de intervalo.</p>
        
        <div class="flex flex-col md:flex-row gap-3">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input 
                    type="text" 
                    id="search"
                    name="search"
                    x-model="search"
                    placeholder="Buscar por nome ou horário..."
                    class="w-full md:w-64 pl-10 pr-10 py-2 bg-white border border-gray-200 rounded-full text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none transition"
                >
                <button 
                    x-show="search.length > 0"
                    x-transition.opacity
                    @click="search = ''"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                    type="button"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <a href="{{ route('admin.turnos.create') }}" class="bg-gray-900 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded-full text-sm transition text-center">
                + Cadastrar Novo Culto
            </a>
        </div>
    </div>

    <div class="flex items-center justify-between px-2">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">
            <span id="turno-count">{{ $turnos->total() }} {{ $turnos->total() === 1 ? 'turno encontrado' : 'turnos encontrados' }}</span>
        </p>
        <p x-show="search.length > 0" x-transition.opacity class="text-xs text-cyan-600 font-medium">
            Filtrando por: "<span x-text="search"></span>"
        </p>
    </div>

    <div class="space-y-3">
        @forelse($turnos as $turno)
        <div 
            data-turno-card 
            data-name="{{ addslashes($turno->nome) }}" 
            data-horario="{{ addslashes($turno->hora_inicio . ' ' . $turno->hora_fim) }}"
            class="p-4 bg-white rounded-2xl border border-gray-100 hover:bg-gray-50 transition cursor-pointer group flex flex-col md:flex-row md:items-center justify-between gap-4"
        >
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 group-hover:text-cyan-600 transition-colors">{{ $turno->nome }}</h3>
                    <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-gray-500 font-medium">
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ \Carbon\Carbon::parse($turno->hora_inicio)->format('H:i') }} às {{ \Carbon\Carbon::parse($turno->hora_fim)->format('H:i') }}
                        </span>
                        <span class="hidden md:inline-block w-1 h-1 rounded-full bg-gray-300"></span>
                        <span>Tolerância: {{ $turno->tolerancia_atraso_min }} min</span>
                        <span class="hidden md:inline-block w-1 h-1 rounded-full bg-gray-300"></span>
                        <span>Intervalo: {{ $turno->intervalo_min }} min</span>
                    </div>
                </div>
            </div>
            
            <div class="flex border-t md:border-t-0 pt-3 md:pt-0 border-gray-100">
                <a href="{{ route('admin.turnos.edit', $turno) }}" class="flex-1 md:flex-none text-center text-cyan-600 hover:bg-cyan-50 font-bold py-2 px-6 rounded-full text-sm transition">
                    Editar Turno
                </a>
            </div>
        </div>
        @empty
        <div id="empty-state-default" class="text-center p-10 bg-white rounded-2xl border border-gray-100 text-gray-500">
            Nenhum turno cadastrado.
        </div>
        @endforelse

        {{-- Estado vazio da busca --}}
        <div id="empty-state" style="display: none;" class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <p class="text-gray-500 font-medium mb-1">Nenhum turno encontrado</p>
            <p class="text-sm text-gray-400">Tente buscar por outro termo ou 
                <button @click="search = ''" class="text-cyan-600 hover:underline font-medium">limpe a busca</button>.
            </p>
        </div>
    </div>

    <div id="pagination-container" class="mt-6">
        {{ $turnos->links() }}
    </div>
</div>
@endsection