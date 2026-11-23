<?php

use App\Models\Posto;
use App\Models\User;
use Database\Seeders\RolesSeeder;

beforeEach(function () {
    $this->seed(RolesSeeder::class);
});

it('exibe loading state e desabilita o botão ao submeter edição de posto', function () {
    $admin = User::factory()->create(['password' => bcrypt('password')]);
    $admin->assignRole('admin');
    $this->actingAs($admin);

    $posto = Posto::factory()->create(['nome' => 'Portaria Antiga']);

    $page = visit(route('admin.postos.edit', $posto));

    $page->script('document.getElementById("form-editar-posto").addEventListener("submit", function(e) { e.preventDefault(); });');

    $page->type('nome', 'Portaria Nova')
         ->click('#btn-atualizar-posto')
         ->wait(1)
         ->assertSee('Salvando...')
         ->assertDisabled('#btn-atualizar-posto')
         ->assertNoJavaScriptErrors();
});