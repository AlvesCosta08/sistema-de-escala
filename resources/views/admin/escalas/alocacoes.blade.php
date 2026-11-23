@extends('layouts.app')
@section('title', 'Alocações')
@section('header', 'Gerenciar Plantões')

@section('content')
{{-- FullCalendar CSS --}}
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">

<div class="space-y-4">
    <!-- Cabeçalho de Navegação -->
    <div class="flex items-center gap-3 mb-4">
        <a href="{{ route('admin.escalas.index') }}" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center hover:bg-gray-200 transition text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h2 class="font-bold text-gray-900 text-lg">Plantões</h2>
            <p class="text-xs text-gray-500 font-medium">Escala de <span class="text-indigo-600">{{ $escala->mes_referencia }}</span></p>
        </div>
    </div>

    <!-- Formulário de Alocação -->
    <form id="form-alocar-plantao" x-data="{ loading: false }" x-on:submit="loading = true" action="{{ route('admin.escalas.alocacoes.store', $escala) }}" method="POST" class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Colaborador</label>
                <select name="user_id" required class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="">Selecione...</option>
                    @foreach($colaboradores as $colab) <option value="{{ $colab->id }}">{{ $colab->name }}</option> @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Posto / Turno</label>
                <div class="flex gap-2">
                    <select name="posto_id" required class="w-1/2 bg-white border border-gray-300 rounded-lg px-2 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="">Posto...</option>
                        @foreach($postos as $posto) <option value="{{ $posto->id }}">{{ $posto->nome }}</option> @endforeach
                    </select>
                    <select name="turno_id" required class="w-1/2 bg-white border border-gray-300 rounded-lg px-2 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="">Turno...</option>
                        @foreach($turnos as $turno) <option value="{{ $turno->id }}">{{ $turno->nome }}</option> @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <input type="date" name="data" required class="w-full md:w-auto bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
            <button type="submit" id="btn-alocar" x-bind:disabled="loading" class="flex-1 md:flex-none bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-full text-sm shadow-md transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                <svg x-show="loading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="loading ? 'Alocando...' : 'Alocar'"></span>
            </button>
        </div>
    </form>

    <!-- Calendário Drag-and-Drop -->
    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-gray-700">Calendário de Plantões</h3>
            <span class="text-xs text-gray-500">Arraste os cards para alterar a data</span>
        </div>
        <div id="calendario-alocacoes"></div>
    </div>

    <!-- Lista detalhada (fallback) -->
    <details class="bg-white rounded-2xl border border-gray-100 shadow-sm">
        <summary class="cursor-pointer p-4 font-bold text-gray-700 text-sm">Ver lista detalhada de plantões</summary>
        <div class="divide-y divide-gray-100 px-4 pb-4">
            @forelse($alocacoes as $alocacao)
            <div class="py-4 flex flex-col md:flex-row md:items-center justify-between gap-3 hover:bg-gray-50 transition px-2 rounded-xl">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-14 bg-white border border-gray-200 rounded-xl flex flex-col items-center justify-center shadow-sm shrink-0">
                        <span class="text-[10px] font-bold text-gray-500 uppercase">{{ \Carbon\Carbon::parse($alocacao->data)->translatedFormat('M') }}</span>
                        <span class="text-lg font-black text-indigo-600 leading-none">{{ \Carbon\Carbon::parse($alocacao->data)->format('d') }}</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">{{ $alocacao->user->name }}</h4>
                        <p class="text-sm text-gray-500 font-medium">{{ $alocacao->posto->nome }} • {{ $alocacao->turno->nome }}</p>
                        <span class="text-xs text-gray-400">Status: {{ ucfirst($alocacao->status) }}</span>
                    </div>
                </div>
                <div>
                    <x-confirm-action 
                        :action="route('admin.escalas.alocacoes.destroy', [$escala, $alocacao])" 
                        method="DELETE"
                        title="Remover Plantão"
                        message="Tem certeza que deseja remover esta alocação? O colaborador será liberado deste turno e esta ação não pode ser desfeita."
                        buttonText="Remover"
                        buttonClass="bg-rose-600 hover:bg-rose-700 text-white"
                        id="remover-{{ $alocacao->id }}"
                    >
                        <button type="button" class="text-rose-500 hover:text-white hover:bg-rose-500 border border-rose-200 font-bold py-1.5 px-4 rounded-full text-xs transition">
                            Remover
                        </button>
                    </x-confirm-action>
                </div>
            </div>
            @empty
            <div class="text-center py-10 text-gray-500">
                Nenhum plantão alocado para esta escala ainda.
            </div>
            @endforelse
        </div>
        <div class="p-4 border-t border-gray-100">{{ $alocacoes->links() }}</div>
    </details>
</div>

{{-- FullCalendar JS --}}
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/locales/pt-br.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendario-alocacoes');
        if (!calendarEl) return;

        const eventos = @json($eventos);
        const escalaId = {{ $escala->id }};
        const csrfToken = '{{ csrf_token() }}';

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'pt-br',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listWeek'
            },
            buttonText: {
                today: 'Hoje',
                month: 'Mês',
                week: 'Semana',
                list: 'Lista'
            },
            editable: true,
            droppable: false,
            events: eventos,
            eventDisplay: 'block',
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            },
            eventDrop: function(info) {
                const novaData = info.event.startStr.split('T')[0];
                const alocacaoId = info.event.id;

                fetch(`/admin/escalas/${escalaId}/alocacoes/${alocacaoId}/mover`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ data: novaData })
                })
                .then(async (response) => {
                    const data = await response.json();
                    if (!response.ok) {
                        info.revert();
                        alert(data.message || 'Erro ao mover o plantão.');
                    } else {
                        window.location.reload();
                    }
                })
                .catch(() => {
                    info.revert();
                    alert('Erro de conexão. Tente novamente.');
                });
            }
        });

        calendar.render();
    });
</script>
@endsection