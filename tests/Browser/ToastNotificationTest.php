<?php

use App\Models\Escala;
use App\Models\User;
use Database\Seeders\RolesSeeder;

beforeEach(fn() => $this->seed(RolesSeeder::class));

it('exibe toast de sucesso ao criar uma nova escala', function () {
    $admin = User::factory()->create(['password' => bcrypt('password')]);
    $admin->assignRole('admin');
    $this->actingAs($admin);

    $page = visit(route('admin.escalas.gerenciar'));

    // Preenche o formulário e submete (submit real dessa vez)
    $page->type('mes_referencia', '2027-06')
         ->click('#btn-criar-escala')
         ->wait(2) // Aguarda o redirect e o toast aparecer
         
         // Asserts do Toast
         ->assertSee('Escala criada com sucesso!')
         ->assertVisible('[data-toast]')
         ->assertNoJavaScriptErrors();
});

it('toast desaparece automaticamente apos alguns segundos', function () {
    $admin = User::factory()->create(['password' => bcrypt('password')]);
    $admin->assignRole('admin');
    $this->actingAs($admin);

    $page = visit(route('admin.escalas.gerenciar'));

    $page->type('mes_referencia', '2027-07')
         ->click('#btn-criar-escala')
         ->wait(2)
         ->assertVisible('[data-toast]')
         ->wait(5) // Aguarda o auto-dismiss (4s + margem)
         ->assertMissing('[data-toast]');
});