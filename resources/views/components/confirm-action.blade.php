@props([
    'action', 
    'method' => 'POST', 
    'title' => 'Tem certeza?', 
    'message' => 'Esta ação não pode ser desfeita.', 
    'buttonText' => 'Confirmar', 
    'buttonClass' => 'bg-rose-600 hover:bg-rose-700 text-white',
    'id' => 'confirm'
])

<form x-data="{ open: false }" action="{{ $action }}" method="POST" id="form-{{ $id }}">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    {{-- Modal Overlay --}}
    <div 
        x-show="open" 
        class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" 
        style="display: none;" 
        x-transition.opacity.duration.200ms
    >
        {{-- Modal Content --}}
        <div 
            @click.outside="open = false" 
            x-transition.scale.duration.200ms 
            class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6 border border-gray-100"
        >
            <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $title }}</h3>
            <p class="text-sm text-gray-600 mb-6">{{ $message }}</p>
            
            <div class="flex justify-end gap-3">
                <button 
                    type="button" 
                    @click="open = false" 
                    class="px-4 py-2 text-sm font-bold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition"
                >
                    Cancelar
                </button>
                <button 
                    type="submit" 
                    id="btn-confirmar-acao"
                    class="px-4 py-2 text-sm font-bold rounded-lg transition {{ $buttonClass }}"
                >
                    {{ $buttonText }}
                </button>
            </div>
        </div>
    </div>

    {{-- Gatilho (Botão que abre o modal) --}}
    <div @click="open = true" id="btn-trigger-{{ $id }}">
        {{ $slot }}
    </div>
</form>