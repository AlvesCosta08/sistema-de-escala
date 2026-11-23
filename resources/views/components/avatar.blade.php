@props(['user', 'size' => 'w-8 h-8'])

@if($user->foto_thumb_path)
    <img 
        src="{{ $user->fotoThumbUrl() }}" 
        alt="Foto de {{ $user->name }}" 
        {{ $attributes->merge(['class' => "$size rounded-full object-cover border border-gray-200 shadow-sm"]) }}
    >
@else
    <div 
        {{ $attributes->merge(['class' => "$size rounded-full bg-indigo-100 flex items-center justify-center border border-indigo-200 text-indigo-700 font-bold shadow-sm"]) }}
    >
        {{ strtoupper(substr($user->name, 0, 1)) }}
    </div>
@endif