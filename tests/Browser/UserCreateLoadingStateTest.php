<?php

use App\Models\User;
use Database\Seeders\RolesSeeder;

beforeEach(function () {
    $this->seed(RolesSeeder::class);
});

it('exibe loading state e desabilita o botão ao submeter criação de novo usuário', function () {
    // 1. Arrange
    $admin = User::factory()->create(['password' => bcrypt('password')]);
    $admin->assignRole('admin');

    $this->actingAs($admin);

    // 2. Act
    $page = visit(route('admin.usuarios.create'));

    // Previne o submit real para assertar o estado de loading
    $page->script('document.getElementById("form-criar-usuario").addEventListener("submit", function(e) { e.preventDefault(); });');

    $page->type('name', 'Maria Silva')
         ->type('email', 'maria@condominio.com')
         ->select('cargo', 'porteiro')
         ->type('password', 'senha123')
         ->click('#btn-salvar-novo-usuario')
         ->wait(1)
         
         // 3. Assert
         ->assertSee('Salvando...')
         ->assertDisabled('#btn-salvar-novo-usuario')
         ->assertNoJavaScriptErrors();
});