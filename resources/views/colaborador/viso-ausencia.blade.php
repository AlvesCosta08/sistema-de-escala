@extends('layouts.app')

@section('title', 'Aviso de ausência')

@section('content')
    <header class="mb-4">
        <a href="{{ route('minha-escala') }}" class="text-sm text-slate-500">← Voltar</a>
        <h1 class="mt-1 text-xl font-bold">Aviso de ausência</h1>
        <p class="text-xs text-slate-500">
            {{ $alocacao->posto->nome }} ·
            {{ $alocacao->data->isoFormat('ddd, DD/MM') }} ·
            {{ substr($alocacao->turno->hora_inicio, 0, 5) }}–{{ substr($alocacao->turno->hora_fim, 0, 5) }}
        </p>
    </header>

    <form method="POST" action="{{ route('ausencia.store', $alocacao) }}"
          enctype="multipart/form-data"
          class="space-y-4 rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
        @csrf

        <div>
            <label class="mb-1 block text-sm font-medium">Motivo *</label>
            @foreach (['atestado_medico' => 'Atestado médico', 'emergencia_pessoal' => 'Emergência pessoal', 'imprevisto' => 'Imprevisto', 'folga_negociada' => 'Folga negociada', 'outro' => 'Outro'] as $valor => $label)
                <label class="flex items-center gap-2 py-1">
                    <input type="radio" name="motivo" value="{{ $valor }}" @checked(old('motivo') === $valor)>
                    <span class="text-sm">{{ $label }}</span>
                </label>
            @endforeach
            @error('motivo')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="observacao" class="mb-1 block text-sm font-medium">Observação</label>
            <textarea id="observacao" name="observacao" rows="3"
                      class="w-full rounded border border-slate-300 px-3 py-2 text-sm">{{ old('observacao') }}</textarea>
        </div>

        <div>
            <label for="anexo" class="mb-1 block text-sm font-medium">Anexo (atestado)</label>
            <input id="anexo" type="file" name="anexo" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-sm">
        </div>

        <button type="submit"
                class="w-full rounded-lg bg-red-600 py-2 text-sm font-semibold text-white hover:bg-red-700">
            Confirmar aviso
        </button>
    </form>
@endsection