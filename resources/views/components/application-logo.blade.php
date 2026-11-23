@props(['class' => 'h-10 w-auto'])

<div {{ $attributes->merge(['class' => "flex items-center gap-2 $class"]) }}>
    <!-- O "O" estilizado com o relógio dentro -->
    <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-9 w-9">
        <!-- Fundo do "O" (Círculo com gradiente) -->
        <circle cx="20" cy="20" r="18" fill="url(#logo-gradient)" />
        
        <!-- Abertura do "O" (Corta o círculo para parecer um O) -->
        <path d="M20 2C10.0589 2 2 10.0589 2 20C2 29.9411 10.0589 38 20 38C29.9411 38 38 29.9411 38 20C38 10.0589 29.9411 2 20 2ZM20 32C13.3726 32 8 26.6274 8 20C8 13.3726 13.3726 8 20 8C26.6274 8 32 13.3726 32 20C32 26.6274 26.6274 32 20 32Z" fill="white" fill-opacity="0.2"/>
        
        <!-- Ponteiros do relógio -->
        <path d="M20 12V20L25 23" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
        
        <!-- Definição do Gradiente -->
        <defs>
            <linearGradient id="logo-gradient" x1="2" y1="2" x2="38" y2="38" gradientUnits="userSpaceOnUse">
                <stop stop-color="#4F46E5" /> <!-- Indigo 600 -->
                <stop offset="1" stop-color="#06B6D4" /> <!-- Cyan 500 -->
            </linearGradient>
        </defs>
    </svg>

    <!-- Texto "bede" -->
    <span class="text-2xl font-black tracking-tighter text-gray-900">
        bede
    </span>
</div>
