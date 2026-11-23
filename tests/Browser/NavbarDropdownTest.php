<?php

use App\Models\User;
use Database\Seeders\RolesSeeder;

it('abre o dropdown do avatar no header mobile ao clicar', function () {
    $this->seed(RolesSeeder::class);

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin);

    visit('/admin/ausencias')
        ->on()->mobile()
        ->click('[data-testid="avatar-menu"]')
        ->assertSee('Meu Perfil')
        ->assertSee('Sair')
        ->assertNoJavascriptErrors();
});