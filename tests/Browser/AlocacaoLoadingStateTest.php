<?php

use App\Models\Escala;
use App\Models\Posto;
use App\Models\Turno;
use App\Models\User;
use Database\Seeders\RolesSeeder;

beforeEach(fn() => $this->seed(RolesSeeder::class));

it('exibe loading state ao submeter alocação de plantão', function () {
    $admin = User::factory()->create(['password' => bcrypt('password')]);
    $admin->assignRole('admin');
    $this->actingAs($admin);

    $escala = Escala::factory()->create();
    $colaborador = User::factory()->create(['cargo' => 'porteiro']);
    $colaborador->assignRole('porteiro');
    $posto = Posto::factory()->create();
    $turno = Turno::factory()->create();

    $page = visit(route('admin.escalas.alocacoes.index', $escala));
    $page->script('document.getElementById("form-alocar-plantao").addEventListener("submit", function(e) { e.preventDefault(); });');

    $page->select('user_id', $colaborador->id)
         ->select('posto_id', $posto->id)
         ->select('turno_id', $turno->id)
         ->type('data', '2026-12-01')
         ->click('#btn-alocar')
         ->wait(1)
         ->assertSee('Alocando...')
         ->assertDisabled('#btn-alocar')
         ->assertNoJavaScriptErrors();
});