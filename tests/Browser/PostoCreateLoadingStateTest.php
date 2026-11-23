<?php

use App\Models\User;
use Database\Seeders\RolesSeeder;

beforeEach(function () {
    $this->seed(RolesSeeder::class);
});

it('exibe loading state e desabilita o botão ao submeter criação de posto', function () {
    $admin = User::factory()->create(['password' => bcrypt('password')]);
    $admin->assignRole('admin');
    $this->actingAs($admin);

    $page = visit(route('admin.postos.create'));

    $page->script('document.getElementById("form-criar-posto").addEventListener("submit", function(e) { e.preventDefault(); });');

    $page->type('nome', 'Portaria Principal')
         ->type('descricao', 'Acesso principal de veículos')
         ->click('#btn-salvar-posto')
         ->wait(1)
         ->assertSee('Salvando...')
         ->assertDisabled('#btn-salvar-posto')
         ->assertNoJavaScriptErrors();
});