<?php

use App\Models\Escala;
use App\Models\User;
use Database\Seeders\RolesSeeder;

beforeEach(fn() => $this->seed(RolesSeeder::class));

it('exibe loading state ao submeter criação de escala', function () {
    $admin = User::factory()->create(['password' => bcrypt('password')]);
    $admin->assignRole('admin');
    $this->actingAs($admin);

    // Cria uma escala para garantir que a lista não esteja vazia
    Escala::factory()->create(['mes_referencia' => '2026-11', 'status' => 'rascunho']);

    // ✅ CORREÇÃO: Visitar a rota de gerenciar, que é a que tem a variável $escalas
    $page = visit(route('admin.escalas.gerenciar'));
    
    $page->script('document.getElementById("form-criar-escala").addEventListener("submit", function(e) { e.preventDefault(); });');

    $page->type('mes_referencia', '2026-12')
         ->click('#btn-criar-escala')
         ->wait(1)
         ->assertSee('Criando...')
         ->assertDisabled('#btn-criar-escala')
         ->assertNoJavaScriptErrors();
});