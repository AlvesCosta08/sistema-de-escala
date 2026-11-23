@extends('layouts.app')
@section('title', 'Alocações')
@section('header', 'Gerenciar Plantões')

@section('content')
{{-- FullCalendar CSS --}}
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">

<div class="space-y-4" x-data="detalhePlantao()">
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

    {{-- Flash --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-xl px-4 py-3">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-700 text-sm rounded-xl px-4 py-3">
            {{ session('error') }}
        </div>
    @endif

    <!-- Formulário de Alocação -->
    <form id="form-alocar-plantao" x-data="{ loading: false }" x-on:submit="loading = true" action="{{ route('admin.escalas.alocacoes.store', $escala) }}" method="POST" class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Colaborador</label>
                <select name="user_id" required class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="">Selecione...</option>
                    @foreach($colaboradores as $colab)
                        <option value="{{ $colab->id }}">{{ $colab->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Posto / Turno</label>
                <div class="flex gap-2">
                    <select name="posto_id" required class="w-1/2 bg-white border border-gray-300 rounded-lg px-2 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="">Posto...</option>
                        @foreach($postos as $posto)
                            <option value="{{ $posto->id }}">{{ $posto->nome }}</option>
                        @endforeach
                    </select>
                    <select name="turno_id" required class="w-1/2 bg-white border border-gray-300 rounded-lg px-2 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="">Turno...</option>
                        @foreach($turnos as $turno)
                            <option value="{{ $turno->id }}">{{ $turno->nome }}</option>
                        @endforeach
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

    <!-- Calendário -->
    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-gray-700">Calendário de Plantões</h3>
            <span class="text-xs text-gray-500">Clique em um plantão para ver detalhes • Arraste para alterar a data</span>
        </div>
        <div id="calendario-alocacoes"></div>
    </div>

    <!-- Lista detalhada -->
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
                        message="Tem certeza que deseja remover esta alocação?"
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

    {{-- ===================== MODAL DE DETALHES ===================== --}}
    <div x-show="aberto" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="display: none;">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50" x-on:click="fechar()"></div>

        {{-- Conteúdo do modal --}}
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden"
             x-transition.opacity.duration.150ms>
            {{-- Header --}}
            <div class="bg-indigo-600 text-white px-5 py-4 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-lg" x-text="dados.user_name || 'Detalhes do Plantão'"></h3>
                    <p class="text-xs text-indigo-100 mt-0.5">Detalhes da alocação</p>
                </div>
                <button type="button" x-on:click="fechar()"
                        class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Corpo --}}
            <div class="p-5 space-y-4">
                {{-- Colaborador --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold shrink-0">
                        <span x-text="(dados.user_name || '?').charAt(0).toUpperCase()"></span>
                    </div>
                    <div class="flex-1">
                        <p class="text-[11px] font-bold text-gray-400 uppercase">Colaborador</p>
                        <p class="font-semibold text-gray-900" x-text="dados.user_name"></p>
                    </div>
                </div>

                {{-- Posto --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9v.01M9 12v.01M9 15v.01M9 18v.01"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-[11px] font-bold text-gray-400 uppercase">Posto</p>
                        <p class="font-semibold text-gray-900" x-text="dados.posto_nome"></p>
                    </div>
                </div>

                {{-- Turno --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-700 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-[11px] font-bold text-gray-400 uppercase">Turno</p>
                        <p class="font-semibold text-gray-900">
                            <span x-text="dados.turno_nome"></span>
                            <span class="text-gray-500 text-sm font-normal"
                                  x-text="'(' + (dados.hora_inicio || '') + ' - ' + (dados.hora_fim || '') + ')'"></span>
                        </p>
                    </div>
                </div>

                {{-- Data --}}
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center text-rose-700 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-[11px] font-bold text-gray-400 uppercase">Data</p>
                        <p class="font-semibold text-gray-900" x-text="formatarData(dados.data)"></p>
                    </div>
                </div>

                {{-- Status --}}
                <div class="pt-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold"
                          x-bind:class="dados.status === 'confirmado'
                              ? 'bg-emerald-100 text-emerald-700'
                              : 'bg-amber-100 text-amber-700'">
                        <span class="w-1.5 h-1.5 rounded-full"
                              x-bind:class="dados.status === 'confirmado' ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                        <span x-text="(dados.status || '').charAt(0).toUpperCase() + (dados.status || '').slice(1)"></span>
                    </span>
                </div>
            </div>

            {{-- Footer com ações --}}
            <div class="px-5 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button" x-on:click="fechar()"
                        class="px-4 py-2 rounded-full text-sm font-bold text-gray-600 hover:bg-gray-200 transition">
                    Fechar
                </button>
                <form method="POST" x-bind:action="urlRemover()"
                      onsubmit="return confirm('Tem certeza que deseja remover esta alocação?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 rounded-full text-sm font-bold bg-rose-600 hover:bg-rose-700 text-white transition">
                        Remover plantão
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- FullCalendar JS --}}
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/locales/pt-br.global.min.js"></script>
<script>
    // Componente Alpine para controlar o modal
    function detalhePlantao() {
        return {
            aberto: false,
            dados: {},
            abrir(props) {
                this.dados = props;
                this.aberto = true;
            },
            fechar() {
                this.aberto = false;
            },
            formatarData(iso) {
                if (!iso) return '—';
                const [ano, mes, dia] = iso.split('-');
                return `${dia}/${mes}/${ano}`;
            },
            urlRemover() {
                if (!this.dados.id) return '#';
                return `{{ url('admin/escalas/'.$escala->id.'/alocacoes') }}/${this.dados.id}`;
            },
        };
    }

    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('calendario-alocacoes');
        if (!calendarEl) return;

        if (typeof FullCalendar === 'undefined') {
            console.error('FullCalendar não carregou.');
            return;
        }

        const eventos   = @json($eventos);
        const escalaId  = {{ $escala->id }};
        const csrfToken = '{{ csrf_token() }}';

        // Referência ao componente Alpine (fica no container pai)
        const root = calendarEl.closest('[x-data]');
        const abrirDetalhes = (props) => {
            const comp = Alpine.$data(root);
            comp.abrir(props);
        };

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'pt-br',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listWeek'
            },
            buttonText: { today: 'Hoje', month: 'Mês', week: 'Semana', list: 'Lista' },
            editable: true,
            droppable: false,
            events: eventos,
            eventDisplay: 'block',
            displayEventTime: true,
            eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },

            // 🖱️ CLIQUE NO EVENTO → abre modal
            eventClick: function (info) {
                info.jsEvent.preventDefault(); // evita navegação
                const ev = info.event;
                abrirDetalhes({
                    id:         ev.id,
                    user_name:  ev.extendedProps.user_name,
                    posto_nome: ev.extendedProps.posto_nome,
                    turno_nome: ev.extendedProps.turno_nome,
                    hora_inicio: ev.extendedProps.hora_inicio,
                    hora_fim:    ev.extendedProps.hora_fim,
                    data:        ev.extendedProps.data,
                    status:      ev.extendedProps.status,
                });
            },

            eventDrop: function (info) {
                const novaData   = info.event.startStr.split('T')[0];
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
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok || data.success === false) {
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