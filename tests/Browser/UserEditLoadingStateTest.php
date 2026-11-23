<?php

use App\Models\User;
use Database\Seeders\RolesSeeder;

beforeEach(function () {
    $this->seed(RolesSeeder::class);
});

it('exibe loading state e desabilita o botão ao submeter edição de usuário', function () {
    // 1. Arrange: Preparar o cenário
    $admin = User::factory()->create(['password' => bcrypt('password')]);
    $admin->assignRole('admin'); // ✅ CORREÇÃO: Atribui a role do Spatie!

    $user = User::factory()->create(['cargo' => 'porteiro', 'name' => 'João Silva']);

    $this->actingAs($admin);

    // 2. Act: Visitar a página e interagir
    $page = visit(route('admin.usuarios.edit', $user));

    // 🛡️ TRUQUE DE TDD FRONTEND: Previne o submit real para podermos 
    // assertar o estado de loading antes que a página navegue/recarregue.
    $page->script('document.getElementById("form-editar-usuario").addEventListener("submit", function(e) { e.preventDefault(); });');

    $page->type('name', 'João Silva Atualizado')
         ->click('#btn-salvar-usuario')
         ->wait(1) // Pequena pausa para o Alpine.js processar a reatividade
         
         // 3. Assert: Validar o comportamento visual
         ->assertSee('Salvando...')
         ->assertDisabled('#btn-salvar-usuario')
         ->assertNoJavaScriptErrors();
});