@extends('layouts.app')

@section('title', 'Postos')
@section('header', 'Postos de Trabalho')

@section('content')
<div class="space-y-4" x-data="{ search: '' }" x-init="
    $watch('search', value => {
        const query = value.toLowerCase().trim();
        const cards = document.querySelectorAll('[data-posto-card]');
        let visible = 0;
        
        cards.forEach(card => {
            const name = card.dataset.name.toLowerCase();
            const descricao = card.dataset.descricao.toLowerCase();
            
            if (!query || name.includes(query) || descricao.includes(query)) {
                card.style.display = '';
                visible++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Atualiza contador
        const countEl = document.getElementById('posto-count');
        if (countEl) {
            countEl.textContent = visible === 1 ? '1 posto encontrado' : visible + ' postos encontrados';
        }
        
        // Atualiza estado vazio
        const emptyState = document.getElementById('empty-state');
        if (emptyState) {
            emptyState.style.display = (query && visible === 0) ? 'block' : 'none';
        }
        
        // Atualiza paginação
        const pagination = document.getElementById('pagination-container');
        if (pagination) {
            pagination.style.display = query ? 'none' : 'block';
        }
    })
">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 bg-gray-50 p-4 rounded-2xl">
        <p class="text-gray-600 text-sm font-medium">Locais onde a equipe atua.</p>
        
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
                    placeholder="Buscar por nome ou descrição..."
                    class="w-full md:w-64 pl-10 pr-10 py-2 bg-white border border-gray-200 rounded-full text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
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

            <a href="{{ route('admin.postos.create') }}" class="bg-gray-900 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded-full text-sm transition text-center">
                + Novo Posto
            </a>
        </div>
    </div>

    <div class="flex items-center justify-between px-2">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">
            <span id="posto-count">{{ $postos->total() }} postos encontrados</span>
        </p>
        <p x-show="search.length > 0" x-transition.opacity class="text-xs text-indigo-600 font-medium">
            Filtrando por: "<span x-text="search"></span>"
        </p>
    </div>

    <div class="space-y-3">
        @forelse($postos as $posto)
        <div 
            data-posto-card 
            data-name="{{ addslashes($posto->nome) }}" 
            data-descricao="{{ addslashes($posto->descricao ?? '') }}"
            class="p-4 bg-white rounded-2xl border border-gray-100 hover:bg-gray-50 transition cursor-pointer group flex flex-col md:flex-row md:items-center justify-between gap-4"
        >
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $posto->nome }}</h3>
                    <p class="text-sm text-gray-500">{{ $posto->descricao ?? 'Sem descrição cadastrada.' }}</p>
                </div>
            </div>
            
            <div class="flex border-t md:border-t-0 pt-3 md:pt-0 border-gray-100">
                <a href="{{ route('admin.postos.edit', $posto) }}" class="flex-1 md:flex-none text-center text-indigo-600 hover:bg-indigo-50 font-bold py-2 px-6 rounded-full text-sm transition">
                    Editar Posto
                </a>
            </div>
        </div>
        @empty
        <div id="empty-state-default" class="text-center p-10 bg-white rounded-2xl border border-gray-100 text-gray-500">
            Nenhum posto cadastrado.
        </div>
        @endforelse

        {{-- Estado vazio da busca --}}
        <div id="empty-state" style="display: none;" class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <p class="text-gray-500 font-medium mb-1">Nenhum posto encontrado</p>
            <p class="text-sm text-gray-400">Tente buscar por outro termo ou 
                <button @click="search = ''" class="text-indigo-600 hover:underline font-medium">limpe a busca</button>.
            </p>
        </div>
    </div>

    <div id="pagination-container" class="mt-6">
        {{ $postos->links() }}
    </div>
</div>
@endsection