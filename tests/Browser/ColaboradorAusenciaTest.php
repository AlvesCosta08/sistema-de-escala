<?php

use App\Models\Alocacao;
use App\Models\Posto;
use App\Models\Turno;
use App\Models\User;
use Database\Seeders\RolesSeeder;

beforeEach(fn() => $this->seed(RolesSeeder::class));

it('exibe loading state e desabilita o botão ao solicitar ausência', function () {
    // 1. Arrange: Cria um colaborador e uma alocação confirmada
    $colaborador = User::factory()->create(['password' => bcrypt('password'), 'cargo' => 'porteiro']);
    $colaborador->assignRole('porteiro');
    $this->actingAs($colaborador);

    $posto = Posto::factory()->create();
    $turno = Turno::factory()->create();
    
    $alocacao = Alocacao::factory()->create([
        'user_id' => $colaborador->id,
        'posto_id' => $posto->id,
        'turno_id' => $turno->id,
        'data' => now()->addDays(2),
        'status' => 'confirmado',
    ]);

    // 2. Act: Visita a página de solicitação de ausência
    $page = visit(route('ausencia.create', $alocacao));

    // Previne o submit real para podermos assertar o estado de loading da UI
    $page->script('document.getElementById("form-solicitar-ausencia").addEventListener("submit", function(e) { e.preventDefault(); });');

    // Preenche o formulário e clica no botão
    $page->check('motivo', 'imprevisto')
         ->type('observacao', 'Motivo de força maior')
         ->click('#btn-enviar-ausencia')
         ->wait(1) // Dá tempo do Alpine.js processar a reatividade
         
         // 3. Assert: Valida o comportamento visual
         ->assertSee('Enviando...')
         ->assertDisabled('#btn-enviar-ausencia')
         ->assertNoJavaScriptErrors();
});