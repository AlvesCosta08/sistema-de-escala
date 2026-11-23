<?php

namespace App\Providers;

use App\Models\Alocacao;
use App\Policies\AlocacaoPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator; // <-- ADICIONADO AQUI

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Alocacao::class, AlocacaoPolicy::class);
        
        // Força o Laravel a usar as classes do Tailwind na paginação
        Paginator::useTailwind(); // <-- ADICIONADO AQUI
    }
}