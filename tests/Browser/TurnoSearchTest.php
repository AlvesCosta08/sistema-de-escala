<?php

use App\Models\Turno;
use App\Models\User;
use Database\Seeders\RolesSeeder;

beforeEach(fn() => $this->seed(RolesSeeder::class));

it('filtra a lista de turnos em tempo real ao digitar no campo de busca', function () {
    $admin = User::factory()->create(['password' => bcrypt('password')]);
    $admin->assignRole('admin');
    $this->actingAs($admin);

    // Cria 3 turnos com nomes e horários distintos
    Turno::factory()->create(['nome' => 'Diurno', 'hora_inicio' => '07:00:00', 'hora_fim' => '19:00:00']);
    Turno::factory()->create(['nome' => 'Noturno', 'hora_inicio' => '19:00:00', 'hora_fim' => '07:00:00']);
    Turno::factory()->create(['nome' => 'Madrugada', 'hora_inicio' => '23:00:00', 'hora_fim' => '07:00:00']);

    $page = visit(route('admin.turnos.index'));

    // 1. Inicialmente, todos os 3 turnos devem estar visíveis
    $page->assertSee('Diurno')
         ->assertSee('Noturno')
         ->assertSee('Madrugada')
         ->assertSee('3 turnos encontrados');

    // 2. Digitar "not" no campo de busca
    $page->type('#search', 'not')
         ->wait(1)
         ->assertSee('Noturno')
         ->assertDontSee('Diurno')
         ->assertDontSee('Madrugada')
         ->assertSee('1 turno encontrado');

    // 3. Buscar por horário
    $page->clear('#search')
         ->type('#search', '23:00')
         ->wait(1)
         ->assertSee('Madrugada')
         ->assertDontSee('Diurno')
         ->assertDontSee('Noturno')
         ->assertSee('1 turno encontrado');

    // 4. Buscar por algo que não existe
    $page->clear('#search')
         ->type('#search', 'xyz')
         ->wait(1)
         ->assertDontSee('Diurno')
         ->assertDontSee('Noturno')
         ->assertDontSee('Madrugada')
         ->assertSee('0 turnos encontrados')
         ->assertSee('Nenhum turno encontrado');

    // 5. Limpar a busca e todos voltam
    $page->clear('#search')
         ->wait(1)
         ->assertSee('Diurno')
         ->assertSee('Noturno')
         ->assertSee('Madrugada')
         ->assertSee('3 turnos encontrados');

    $page->assertNoJavaScriptErrors();
});