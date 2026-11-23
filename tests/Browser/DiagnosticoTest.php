<?php

use App\Models\User;
use Database\Seeders\RolesSeeder;

it('diagnostico: verifica atributos Alpine no form de criação', function () {
    $this->seed(RolesSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin);

    $page = visit('/admin/usuarios/create');

    // 1. Verifica se o form de criação tem x-data (pelo ID)
    $page->assertScript('document.getElementById("form-criar-usuario").hasAttribute("x-data")');

    // 2. Verifica se o form de criação tem x-on:submit
    $page->assertScript('document.getElementById("form-criar-usuario").hasAttribute("x-on:submit")');

    // 3. Verifica se o Alpine inicializou o componente
    $page->assertScript('typeof Alpine.$data(document.getElementById("form-criar-usuario")) !== "undefined"');
});