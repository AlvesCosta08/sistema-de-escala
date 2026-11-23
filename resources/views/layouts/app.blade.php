<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Obede') - Obede</title>
    
    {{-- 🌐 FAVICON CORRIGIDO --}}
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-900 font-sans antialiased">

    <div class="mx-auto max-w-[1260px] flex min-h-screen relative">

        <!-- COLUNA 1: SIDEBAR ESQUERDA (Desk) -->
        <header class="hidden md:flex flex-col w-[275px] xl:w-[275px] border-r border-gray-100 h-screen sticky top-0 px-4 py-6 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="mb-6 px-2 w-max hover:opacity-80 transition block">
                {{-- 🖼️ LOGO AUMENTADO (DESKTOP) --}}
                <img src="{{ asset('images/logo.png') }}" alt="Obede" class="h-20 max-w-[180px] w-auto object-contain">
            </a>

            <nav class="space-y-2 flex-1">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-4 px-4 py-3 rounded-full hover:bg-gray-100 transition w-max {{ request()->routeIs('admin.ausencias.*') ? 'font-bold' : 'text-gray-600' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="text-lg">Início</span>
                </a>
                <a href="{{ route('admin.escalas.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-full hover:bg-gray-100 transition w-max {{ request()->routeIs('admin.escalas.index') ? 'font-bold' : 'text-gray-600' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span class="text-lg">Calendário</span>
                </a>
                <a href="{{ route('admin.escalas.gerenciar') }}" class="flex items-center gap-4 px-4 py-3 rounded-full hover:bg-gray-100 transition w-max {{ request()->routeIs('admin.escalas.gerenciar') || request()->routeIs('admin.escalas.alocacoes.*') ? 'font-bold' : 'text-gray-600' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    <span class="text-lg">Criar Escalas</span>
                </a>
                <a href="{{ route('admin.usuarios.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-full hover:bg-gray-100 transition w-max {{ request()->routeIs('admin.usuarios.*') ? 'font-bold' : 'text-gray-600' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span class="text-lg">Equipe</span>
                </a>
                <a href="{{ route('admin.postos.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-full hover:bg-gray-100 transition w-max {{ request()->routeIs('admin.postos.*') ? 'font-bold' : 'text-gray-600' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <span class="text-lg">Postos</span>
                </a>
                <a href="{{ route('admin.turnos.index') }}" class="flex items-center gap-4 px-4 py-3 rounded-full hover:bg-gray-100 transition w-max {{ request()->routeIs('admin.turnos.*') ? 'font-bold' : 'text-gray-600' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-lg">Cultos</span>
                </a>
            </nav>

            <div class="mt-8 mb-4 relative" x-data="{ open: false }">
                <button type="button" data-testid="user-menu-desktop" aria-label="Abrir menu do usuário" class="flex items-center gap-3 p-3 rounded-full hover:bg-gray-100 transition w-full text-left" @click="open = !open">
                    <x-avatar :user="auth()->user()" size="w-10 h-10" />
                    <div class="hidden xl:block flex-1 min-w-0">
                        <p class="text-sm font-bold truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ '@' . strtolower(str_replace(' ', '', auth()->user()->name)) }}</p>
                    </div>
                </button>

                <div x-show="open" x-transition @click.outside="open = false" class="absolute bottom-full mb-2 left-0 w-full bg-white border border-gray-100 rounded-xl shadow-lg py-2 z-50" style="display: none;">
                    <a href="{{ route('perfil.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Meu Perfil</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-rose-600 hover:bg-rose-50">Sair</button>
                    </form>
                </div>
            </div>
        </header>

        <!-- COLUNA 2: CONTEÚDO CENTRAL -->
        <main class="flex-1 w-full min-w-0 border-r border-gray-100 pb-20 md:pb-0">
            <header class="md:hidden bg-white/90 backdrop-blur-md sticky top-0 z-30 border-b border-gray-100 px-4 h-14 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    {{-- 🖼️ LOGO AUMENTADO (MOBILE) --}}
                    <img src="{{ asset('images/logo.png') }}" alt="Obede" class="h-10 max-w-[120px] w-auto object-contain scale-90 origin-left">
                </div>
                <h2 class="font-bold text-sm text-gray-900 absolute left-1/2 transform -translate-x-1/2">@yield('header', 'Obede')</h2>

                <div class="relative" x-data="{ open: false }">
                    <button type="button" data-testid="avatar-menu" aria-label="Abrir menu do usuário" class="rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500" @click="open = !open">
                        <x-avatar :user="auth()->user()" size="w-8 h-8" />
                    </button>

                    <div x-show="open" x-transition @click.outside="open = false" class="absolute right-0 mt-2 w-48 bg-white border border-gray-100 rounded-xl shadow-lg py-2 z-50" style="display: none;">
                        <a href="{{ route('perfil.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Meu Perfil</a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-rose-600 hover:bg-rose-50">Sair</button>
                        </form>
                    </div>
                </div>
            </header>

            <header class="hidden md:block sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-gray-100 px-6 py-4">
                <h1 class="text-xl font-bold text-gray-900">@yield('header', 'Obede')</h1>
            </header>

            <div class="p-4 md:p-6 animate-fade-in relative z-0">
                @yield('content')
            </div>
        </main>

        <!-- COLUNA 3: WIDGETS (Desk) -->
        <aside class="hidden lg:block w-[350px] h-screen sticky top-0 px-6 py-6 overflow-y-auto">
            <div class="bg-gray-50 rounded-2xl p-4 mb-4">
                <h3 class="font-bold text-lg mb-3">Ações Rápidas</h3>
                <a href="{{ route('admin.escalas.gerenciar') }}" class="block w-full text-center bg-gray-900 text-white font-bold py-3 rounded-full hover:bg-gray-800 transition mb-2">Montar Nova Escala</a>
                <a href="{{ route('admin.usuarios.create') }}" class="block w-full text-center bg-indigo-600 text-white font-bold py-3 rounded-full hover:bg-indigo-700 transition">+ Novo Colaborador</a>
            </div>

            <div class="bg-gray-50 rounded-2xl p-4">
                <h3 class="font-bold text-lg mb-3">Dica do Sistema</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Você pode usar o <strong class="text-gray-900">Calendário Mensal</strong> para ter uma visão geral de todos os plantões e verificar se há postos descobertos antes de publicar.
                </p>
            </div>
        </aside>
    </div>

    <!-- MOBILE BOTTOM NAVIGATION -->
    <nav class="md:hidden fixed bottom-0 w-full bg-white border-t border-gray-100 z-50 flex justify-around items-center h-16 pb-safe">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center w-full h-full text-gray-500 hover:text-indigo-600 {{ request()->routeIs('admin.ausencias.*') ? 'text-indigo-600' : '' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span class="text-[10px] font-medium">Início</span>
        </a>
        <a href="{{ route('admin.escalas.index') }}" class="flex flex-col items-center justify-center w-full h-full text-gray-500 hover:text-indigo-600 {{ request()->routeIs('admin.escalas.index') ? 'text-indigo-600' : '' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <span class="text-[10px] font-medium">Calendário</span>
        </a>
        <a href="{{ route('admin.usuarios.index') }}" class="flex flex-col items-center justify-center w-full h-full text-gray-500 hover:text-indigo-600 {{ request()->routeIs('admin.usuarios.*') ? 'text-indigo-600' : '' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            <span class="text-[10px] font-medium">Equipe</span>
        </a>
        <a href="{{ route('perfil.edit') }}" class="flex flex-col items-center justify-center w-full h-full text-gray-500 hover:text-indigo-600 {{ request()->routeIs('perfil.edit') ? 'text-indigo-600' : '' }}">
            <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            <span class="text-[10px] font-medium">Perfil</span>
        </a>
    </nav>

    {{-- ======================================================================= --}}
    {{-- 🍞 COMPONENTE GLOBAL DE TOASTS (Inline e Blindado) --}}
    {{-- ======================================================================= --}}
    @if (session('success') || session('error') || session('warning') || session('info') || $errors->any())
    @php
        $type = session('success') ? 'success' : (session('error') ? 'error' : (session('warning') ? 'warning' : (session('info') ? 'info' : 'error')));
        $message = session('success') ?? session('error') ?? session('warning') ?? session('info') ?? 'Por favor, corrija os erros destacados no formulário.';
        $title = $type === 'success' ? 'Sucesso' : ($type === 'error' ? 'Erro' : 'Atenção');
    @endphp

    <div 
        x-data="{ 
            show: true, 
            init() { 
                setTimeout(() => { this.show = false }, 4000); 
            } 
        }"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-x-8"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-8"
        data-toast
        class="fixed top-4 right-4 z-[60] max-w-sm w-full pointer-events-auto rounded-xl shadow-lg border p-4 flex items-start gap-3"
        :class="{
            'bg-emerald-50 border-emerald-200 text-emerald-900': '{{ $type }}' === 'success',
            'bg-rose-50 border-rose-200 text-rose-900': '{{ $type }}' === 'error',
            'bg-amber-50 border-amber-200 text-amber-900': '{{ $type }}' === 'warning',
            'bg-blue-50 border-blue-200 text-blue-900': '{{ $type }}' === 'info'
        }"
    >
        {{-- Ícone --}}
        <div class="shrink-0 mt-0.5">
            @if($type === 'success')
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @elseif($type === 'error')
                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @elseif($type === 'warning')
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            @else
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            @endif
        </div>
        
        {{-- Mensagem --}}
        <div class="flex-1 min-w-0">
            <p class="text-sm font-bold">{{ $title }}</p>
            <p class="text-sm mt-0.5 opacity-90">{{ $message }}</p>
        </div>
        
        {{-- Botão fechar --}}
        <button @click="show = false" class="shrink-0 text-current opacity-60 hover:opacity-100 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    @endif

    <style>
        a { -webkit-tap-highlight-color: transparent; }
        .pb-safe { padding-bottom: env(safe-area-inset-bottom); }
    </style>
</body>
</html>