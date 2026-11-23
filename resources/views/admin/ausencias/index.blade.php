@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                
                @if($avisos->isEmpty())
                    <div class="text-center py-8 text-gray-500">
                        <p>Nenhum aviso de ausência pendente no momento.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($avisos as $aviso)
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-200 shadow-sm">
                                
                                <div class="flex items-center space-x-4">
                                    <div class="h-10 w-10 flex-shrink-0 rounded-full bg-slate-300 flex items-center justify-center text-slate-600 font-bold uppercase">
                                        {{ mb_substr($aviso->user->name ?? '?', 0, 1) }}
                                    </div>
                                    
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-semibold text-slate-900">
                                            {{ $aviso->user->name ?? 'Usuário Desconhecido' }}
                                        </p>
                                        
                                        <p class="text-xs text-slate-500">
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

                                <div class="flex items-center space-x-3">
                                    @if ($aviso->urgente)
                                        <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-800 border border-red-200">
                                            Urgente
                                        </span>
                                    @else
                                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-800 border border-gray-200">
                                            Normal
                                        </span>
                                    @endif
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