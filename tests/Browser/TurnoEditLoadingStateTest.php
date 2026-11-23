<?php

use App\Models\Turno;
use App\Models\User;
use Database\Seeders\RolesSeeder;

beforeEach(fn() => $this->seed(RolesSeeder::class));

it('exibe loading state ao submeter edição de turno', function () {
    $admin = User::factory()->create(['password' => bcrypt('password')]);
    $admin->assignRole('admin');
    $this->actingAs($admin);

    $turno = Turno::factory()->create([
        'nome' => 'Turno Antigo', 
        'hora_inicio' => '08:00:00', 
        'hora_fim' => '17:00:00', 
        'tolerancia_atraso_min' => 10, 
        'intervalo_min' => 60
    ]);

    $page = visit(route('admin.turnos.edit', $turno));
    $page->script('document.getElementById("form-editar-turno").addEventListener("submit", function(e) { e.preventDefault(); });');

    $page->type('nome', 'Turno Atualizado')
         ->click('#btn-atualizar-turno')
         ->wait(1)
         ->assertSee('Salvando...')
         ->assertDisabled('#btn-atualizar-turno')
         ->assertNoJavaScriptErrors();
});