<?php

use App\Http\Controllers\Admin\AlocacaoController;
use App\Http\Controllers\Admin\AusenciaController;
use App\Http\Controllers\Admin\EscalaController;
use App\Http\Controllers\Admin\PostoController;
use App\Http\Controllers\Admin\TurnoController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Colaborador\AvisoAusenciaController;
use App\Http\Controllers\Colaborador\MinhaEscalaController;
use App\Http\Controllers\PerfilController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// =====================================================================
// ROTA DEFINITIVA — serve imagens do storage sem depender de symlink
// Restrita apenas à pasta fotos/ por segurança
// =====================================================================
Route::get('/arquivo/{path}', function (string $path) {
    // Garante que só vai servir arquivos dentro da pasta fotos/
    abort_unless(str_starts_with($path, 'fotos/'), 404);
    
    $disk = Storage::disk('public');
    abort_unless($disk->exists($path), 404);
    
    return $disk->response($path);
})->where('path', '.*')->name('arquivo.show');
// =====================================================================

// Redireciona a raiz para o dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

// Rotas de Autenticação (Login)
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'store']);

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function () {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.ausencias.index')
            : redirect()->route('minha-escala');
    })->name('dashboard');

    Route::post('logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');

    // Perfil do próprio usuário
    Route::get('perfil', [PerfilController::class, 'edit'])
        ->name('perfil.edit');

    Route::post('perfil/foto', [PerfilController::class, 'updateFoto'])
        ->name('perfil.foto.update');

    Route::middleware(['role:porteiro|recepcionista'])->group(function () {
        Route::get('minha-escala', [MinhaEscalaController::class, 'index'])
            ->name('minha-escala');

        Route::get('minha-escala/{alocacao}/ausencia', [AvisoAusenciaController::class, 'create'])
            ->name('ausencia.create');

        Route::post('minha-escala/{alocacao}/ausencia', [AvisoAusenciaController::class, 'store'])
            ->name('ausencia.store');
    });

    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('ausencias', [AusenciaController::class, 'index'])
            ->name('ausencias.index');

        Route::get('ausencias/{aviso}', [AusenciaController::class, 'show'])
            ->name('ausencias.show');

        Route::post('ausencias/{aviso}/alocar', [AusenciaController::class, 'alocar'])
            ->name('ausencias.alocar');

        Route::post('ausencias/{aviso}/rejeitar', [AusenciaController::class, 'rejeitar'])
            ->name('ausencias.rejeitar');

        // --- GERENCIAMENTO DE ESCALAS ---
        Route::get('escalas', [EscalaController::class, 'index'])->name('escalas.index');
        Route::get('escalas/gerenciar', [EscalaController::class, 'gerenciar'])->name('escalas.gerenciar');
        Route::post('escalas', [EscalaController::class, 'store'])->name('escalas.store');
        Route::patch('escalas/{escala}/publicar', [EscalaController::class, 'publicar'])->name('escalas.publicar');

        // --- ALOCAÇÃO ---
        Route::get('escalas/{escala}/alocacoes', [AlocacaoController::class, 'index'])->name('escalas.alocacoes.index');
        Route::post('escalas/{escala}/alocacoes', [AlocacaoController::class, 'store'])->name('escalas.alocacoes.store');
        Route::delete('escalas/{escala}/alocacoes/{alocacao}', [AlocacaoController::class, 'destroy'])->name('escalas.alocacoes.destroy');

        // --- USUÁRIOS ---
        Route::get('usuarios', [UserController::class, 'index'])->name('usuarios.index');
        Route::get('usuarios/create', [UserController::class, 'create'])->name('usuarios.create');
        Route::post('usuarios', [UserController::class, 'store'])->name('usuarios.store');
        Route::get('usuarios/{user}/edit', [UserController::class, 'edit'])->name('usuarios.edit');
        Route::put('usuarios/{user}', [UserController::class, 'update'])->name('usuarios.update');
        Route::patch('usuarios/{user}/toggle', [UserController::class, 'toggle'])->name('usuarios.toggle');

        // Admin troca a foto de qualquer usuário
        Route::post('usuarios/{user}/foto', [PerfilController::class, 'updateFotoAdmin'])
            ->name('usuarios.foto.update');

        // --- POSTOS ---
        Route::get('postos', [PostoController::class, 'index'])->name('postos.index');
        Route::get('postos/create', [PostoController::class, 'create'])->name('postos.create');
        Route::post('postos', [PostoController::class, 'store'])->name('postos.store');
        Route::get('postos/{posto}/edit', [PostoController::class, 'edit'])->name('postos.edit');
        Route::put('postos/{posto}', [PostoController::class, 'update'])->name('postos.update');

        // --- TURNOS ---
        Route::get('turnos', [TurnoController::class, 'index'])->name('turnos.index');
        Route::get('turnos/create', [TurnoController::class, 'create'])->name('turnos.create');
        Route::post('turnos', [TurnoController::class, 'store'])->name('turnos.store');
        Route::get('turnos/{turno}/edit', [TurnoController::class, 'edit'])->name('turnos.edit');
        Route::put('turnos/{turno}', [TurnoController::class, 'update'])->name('turnos.update');
    });
});