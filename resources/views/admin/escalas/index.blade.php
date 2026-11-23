@extends('layouts.app')

@section('title', 'Escala mensal')

@section('content')
    <header class="mb-4 flex items-center justify-between">
        <h1 class="text-xl font-bold">Escala mensal</h1>
        <a href="{{ route('admin.ausencias.index') }}" class="text-sm text-slate-500 hover:text-slate-800">
            Ver ausências
        </a>
    </header>

    {{-- Navegação entre meses --}}
    <nav class="mb-4 flex items-center justify-between rounded-xl bg-white p-2 shadow-sm ring-1 ring-slate-200">
        @php
            $anterior = $referencia->copy()->subMonth()->format('Y-m');
            $proximo = $referencia->copy()->addMonth()->format('Y-m');
        @endphp

        <a href="{{ route('admin.escalas.index', ['mes' => $anterior]) }}" class="rounded-lg px-3 py-1 text-sm text-slate-600 hover:bg-slate-100">
            ◀ {{ $referencia->copy()->subMonth()->isoFormat('MMM') }}
        </a>

        <span class="text-sm font-semibold">
            {{ $referencia->isoFormat('MMMM [de] YYYY') }}
        </span>

        <a href="{{ route('admin.escalas.index', ['mes' => $proximo]) }}" class="rounded-lg px-3 py-1 text-sm text-slate-600 hover:bg-slate-100">
            {{ $referencia->copy()->addMonth()->isoFormat('MMM') }} ▶
        </a>
    </nav>

    {{-- Dias com alocações --}}
    @forelse ($alocacoesPorDia as $dia => $itens)
        <section class="mb-4">
            <h2 class="mb-2 text-sm font-bold text-slate-700">
                {{ \Carbon\Carbon::parse($dia)->isoFormat('ddd, DD/MM') }}
            </h2>

            @foreach ($itens as $alocacao)
                <article class="mb-2 rounded-lg bg-white p-3 shadow-sm ring-1 ring-slate-200">
                    <div class="flex items-center gap-3">
                        <x-avatar :user="$alocacao->user" />

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold">{{ $alocacao->user->name }}</p>
                            <p class="text-xs text-slate-500">
                                {{ $alocacao->posto->nome }} ·
                                {{ substr($alocacao->turno->hora_inicio, 0, 5) }}–{{ substr($alocacao->turno->hora_fim, 0, 5) }}
                            </p>
                        </div>

                        @if ($alocacao->status !== 'confirmado')
                            <span @class([
                                'rounded-full px-2 py-0.5 text-xs font-medium',
                                'bg-amber-100 text-amber-800' => $alocacao->status === 'pendente',
                                'bg-red-100 text-red-800' => in_array($alocacao->status, ['ausente', 'substituida', 'descoberta']),
                            ])>
                                {{ ucfirst($alocacao->status) }}
                            </span>
                        @endif
                    </div>
                </article>
            @endforeach
        </section>
    @empty
        <p class="rounded-lg bg-white p-6 text-center text-sm text-slate-500 shadow-sm">
            Nenhuma alocação em {{ $referencia->isoFormat('MMMM [de] YYYY') }}.
        </p>
    @endforelse
@endsection