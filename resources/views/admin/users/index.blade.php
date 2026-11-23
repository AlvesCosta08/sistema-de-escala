@extends('layouts.app')

@section('title', 'Colaboradores')
@section('header', 'Equipe')

@section('content')
<div class="space-y-4" x-data="{ search: '' }" x-init="
    $watch('search', value => {
        const query = value.toLowerCase().trim();
        const cards = document.querySelectorAll('[data-user-card]');
        let visible = 0;
        
        cards.forEach(card => {
            const name = card.dataset.name.toLowerCase();
            const email = card.dataset.email.toLowerCase();
            
            if (!query || name.includes(query) || email.includes(query)) {
                card.style.display = '';
                visible++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Atualiza contador
        const countEl = document.getElementById('user-count');
        if (countEl) {
            countEl.textContent = visible === 1 ? '1 colaborador encontrado' : visible + ' colaboradores encontrados';
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
    {{-- Cabeçalho com busca --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 bg-gray-50 p-4 rounded-2xl">
        <p class="text-gray-600 text-sm font-medium">Gerencie os acessos da equipe.</p>
        
        <div class="flex flex-col md:flex-row gap-3">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                {{-- ✅ ADICIONADO: id e name para o Playwright encontrar o campo --}}
                <input 
                    type="text" 
                    id="search"
                    name="search"
                    x-model="search"
                    placeholder="Buscar por nome ou email..."
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

            <a href="{{ route('admin.usuarios.create') }}" class="bg-gray-900 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded-full text-sm transition text-center">
                + Novo
            </a>
        </div>
    </div>

    {{-- Contador de resultados --}}
    <div class="flex items-center justify-between px-2">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">
            <span id="user-count">{{ $users->total() }} colaboradores encontrados</span>
        </p>
        <p x-show="search.length > 0" x-transition.opacity class="text-xs text-indigo-600 font-medium">
            Filtrando por: "<span x-text="search"></span>"
        </p>
    </div>

    {{-- Lista de usuários --}}
    <div class="space-y-3">
        @foreach($users as $user)
        <div 
            data-user-card 
            data-name="{{ addslashes($user->name) }}" 
            data-email="{{ addslashes($user->email) }}"
            class="flex flex-col md:flex-row md:items-center justify-between p-4 bg-white rounded-2xl border border-gray-100 hover:bg-gray-50 transition cursor-pointer group"
        >
            <div class="flex items-center gap-4">
                @if($user->foto_thumb_path)
                    <img class="w-12 h-12 rounded-full object-cover" src="{{ $user->fotoThumbUrl() }}" alt="Foto de {{ $user->name }}">
                @else
                    <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold border border-indigo-200">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h3 class="font-bold text-gray-900 text-base group-hover:text-indigo-600 transition-colors">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $user->active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $user->active ? 'Ativo' : 'Inativo' }}
                        </span>
                        <span class="text-xs text-gray-400">•</span>
                        <span class="text-xs text-gray-500 capitalize">{{ $user->cargo }}</span>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 md:mt-0 flex gap-3 border-t md:border-t-0 pt-3 md:pt-0 border-gray-100">
                <a href="{{ route('admin.usuarios.edit', $user) }}" class="flex-1 md:flex-none text-center text-indigo-600 hover:bg-indigo-50 font-bold py-2 px-4 rounded-full text-sm transition">
                    Editar
                </a>
                
                @if(auth()->id() !== $user->id)
                    <x-confirm-action 
                        :action="route('admin.usuarios.toggle', $user)" 
                        method="PATCH"
                        title="Alterar Status do Usuário"
                        :message="$user->active ? 'Tem certeza que deseja DESATIVAR este colaborador? Ele perderá o acesso ao sistema.' : 'Tem certeza que deseja ATIVAR este colaborador? Ele recuperará o acesso ao sistema.'"
                        :buttonText="$user->active ? 'Desativar' : 'Ativar'"
                        :buttonClass="$user->active ? 'bg-rose-600 hover:bg-rose-700 text-white' : 'bg-emerald-600 hover:bg-emerald-700 text-white'"
                        id="toggle-status-{{ $user->id }}"
                    >
                        <button type="button" class="w-full text-rose-500 hover:bg-rose-50 font-bold py-2 px-4 rounded-full text-sm transition">
                            {{ $user->active ? 'Desativar' : 'Ativar' }}
                        </button>
                    </x-confirm-action>
                @endif
            </div>
        </div>
        @endforeach

        {{-- Estado vazio --}}
        <div id="empty-state" style="display: none;" class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <p class="text-gray-500 font-medium mb-1">Nenhum colaborador encontrado</p>
            <p class="text-sm text-gray-400">Tente buscar por outro termo ou 
                <button @click="search = ''" class="text-indigo-600 hover:underline font-medium">limpe a busca</button>.
            </p>
        </div>
    </div>

    {{-- Paginação --}}
    <div id="pagination-container" class="mt-6">
        {{ $users->links() }}
    </div>
</div>
@endsection