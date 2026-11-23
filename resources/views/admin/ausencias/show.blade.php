@extends('layouts.app')

@section('title', 'Detalhe da ausência')

@section('content')
    <header class="mb-4">
        <a href="{{ route('admin.ausencias.index') }}" class="text-sm text-slate-500">← Voltar</a>
        <h1 class="mt-1 text-xl font-bold">Detalhe da ausência</h1>
    </header>

    <section class="mb-4 rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
        <div class="flex items-center gap-3">
            <div class="flex h-12 w-12 flex-none items-center justify-center rounded-full bg-slate-200 text-sm font-semibold text-slate-600">
                {{ mb_substr($aviso->user->name, 0, 1) }}
            </div>
            <div class="min-w-0">
                <p class="truncate text-sm font-semibold">{{ $aviso->user->name }}</p>
                <p class="text-xs text-slate-500">
                    {{ $aviso->alocacao->posto->nome }} ·
                    {{ $aviso->alocacao->data->isoFormat('ddd, DD/MM') }} ·
                    {{ substr($aviso->alocacao->turno->hora_inicio, 0, 5) }}–{{ substr($aviso->alocacao->turno->hora_fim, 0, 5) }}
                </p>
            </div>
        </div>

        <dl class="mt-3 grid grid-cols-1 gap-2 border-t border-slate-100 pt-3 text-sm">
            <div class="flex justify-between">
                <dt class="text-slate-500">Motivo</dt>
                <dd class="font-medium">{{ $aviso->motivoRotulo() }}</dd>
            </div>
            @if ($aviso->urgente)
                <div class="flex justify-between">
                    <dt class="text-slate-500">Urgência</dt>
                    <dd><span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">Urgente</span></dd>
                </div>
            @endif
            @if ($aviso->observacao)
                <div class="flex flex-col gap-1">
                    <dt class="text-slate-500">Observação</dt>
                    <dd class="text-slate-800 whitespace-pre-line">{{ $aviso->observacao }}</dd>
                </div>
            @endif
        </dl>
    </section>

    <h2 class="mb-2 text-sm font-bold text-slate-700">
        Substitutos elegíveis ({{ $substitutos->count() }})
    </h2>

    @forelse ($substitutos as $substituto)
        <article class="mb-2 rounded-lg bg-white p-3 shadow-sm ring-1 ring-slate-200">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 flex-none items-center justify-center rounded-full bg-slate-200 text-sm font-semibold text-slate-600">
                    {{ mb_substr($substituto->name, 0, 1) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold">{{ $substituto->name }}</p>
                    <p class="text-xs text-slate-500">{{ ucfirst($substituto->cargo) }}</p>
                </div>
                <form method="POST" action="{{ route('admin.ausencias.alocar', $aviso) }}">
                    @csrf
                    <input type="hidden" name="substituto_user_id" value="{{ $substituto->id }}">
                    <button type="submit"
                            class="rounded-lg bg-emerald-600 px-3 py-1 text-xs font-medium text-white hover:bg-emerald-700">
                        Alocar
                    </button>
                </form>
            </div>
        </article>
    @empty
        <p class="rounded-lg bg-white p-6 text-center text-sm text-slate-500 shadow-sm">
            Nenhum colaborador elegível para substituir neste turno.
        </p>
    @endforelse

    <details class="mt-6 rounded-lg bg-white p-3 shadow-sm ring-1 ring-slate-200">
        <summary class="cursor-pointer text-sm font-medium text-red-700">Rejeitar aviso</summary>
        <form method="POST" action="{{ route('admin.ausencias.rejeitar', $aviso) }}" class="mt-3 space-y-2">
            @csrf
            <textarea name="justificativa" rows="2" placeholder="Justificativa (opcional)"
                      class="w-full rounded border border-slate-300 px-3 py-2 text-sm"></textarea>
            <button type="submit"
                    class="w-full rounded-lg bg-red-600 py-2 text-sm font-semibold text-white hover:bg-red-700">
                Confirmar rejeição
            </button>
        </form>
    </details>
@endsection