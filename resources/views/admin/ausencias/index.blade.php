@extends('layouts.app')

@section('title', 'Gestão de Ausências')
@section('header', 'Gestão de Ausências')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-4 sm:p-6 text-gray-900">
                
                @if($avisos->isEmpty())
                    <div class="text-center py-8 text-gray-500">
                        <p>Nenhum aviso de ausência pendente no momento.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($avisos as $aviso)
                            {{-- Adicionado flex-wrap para não cortar o botão em telas menores --}}
                            <div class="flex flex-wrap items-center justify-between gap-3 p-4 bg-slate-50 rounded-lg border border-slate-200 shadow-sm">
                                
                                {{-- Bloco de Informações --}}
                                <div class="flex items-center space-x-3 sm:space-x-4 flex-1 min-w-0">
                                    <div class="h-10 w-10 flex-shrink-0 rounded-full bg-slate-300 flex items-center justify-center text-slate-600 font-bold uppercase">
                                        {{ mb_substr($aviso->user->name ?? '?', 0, 1) }}
                                    </div>
                                    
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-semibold text-slate-900">
                                            {{ $aviso->user->name ?? 'Usuário Desconhecido' }}
                                        </p>
                                        
                                        <p class="text-xs text-slate-500 break-words">
                                            @if ($aviso->alocacao && $aviso->alocacao->posto && $aviso->alocacao->turno)
                                                {{ $aviso->alocacao->posto->nome }} &middot;
                                                {{ $aviso->alocacao->data ? $aviso->alocacao->data->isoFormat('ddd, DD/MM') : 'Data não informada' }} &middot;
                                                {{ substr($aviso->alocacao->turno->hora_inicio, 0, 5) }}h às {{ substr($aviso->alocacao->turno->hora_fim, 0, 5) }}
                                            @else
                                                <span class="text-red-500 font-medium">Alocação ou Posto não encontrado (Registro excluído)</span>
                                            @endif
                                        </p>
                                        
                                        <p class="text-xs text-slate-500 mt-1">
                                            <strong>Motivo:</strong> {{ $aviso->motivoRotulo() ?? 'Não especificado' }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Bloco de Ações (Badge + Botão) --}}
                                <div class="flex items-center space-x-2 flex-shrink-0 ml-auto">
                                    @if ($aviso->urgente)
                                        <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-800 border border-red-200">
                                            Urgente
                                        </span>
                                    @else
                                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-800 border border-gray-200">
                                            Normal
                                        </span>
                                    @endif

                                    {{-- Botão de Excluir --}}
                                    <form action="{{ route('admin.ausencias.destroy', $aviso) }}" method="POST"
                                          onsubmit="return confirm('Tem certeza que deseja excluir este aviso? Esta ação não pode ser desfeita.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-500 hover:text-red-700 p-1.5 rounded-full hover:bg-red-50 transition-colors"
                                                title="Excluir aviso">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection