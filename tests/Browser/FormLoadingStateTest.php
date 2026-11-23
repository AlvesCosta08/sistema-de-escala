<?php

use App\Models\User;
use Database\Seeders\RolesSeeder;

it('exibe estado de carregamento no botão ao submeter o formulário de novo usuário', function () {
    $this->seed(RolesSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin);

    $page = visit('/admin/usuarios/create')
        ->type('name', 'João da Silva')
        ->type('email', 'joao@teste.com')
        ->type('password', 'password123');

    // Dispara o evento no form específico (pelo ID)
    $page->script('
        const form = document.getElementById("form-criar-usuario");
        form.dispatchEvent(new Event("submit", { bubbles: true, cancelable: true }));
    ');

    $page->assertSee('Salvando...')
        ->assertNoJavascriptErrors();
});