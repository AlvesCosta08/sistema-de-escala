<?php

use App\Models\User;
use Database\Seeders\RolesSeeder;

beforeEach(fn() => $this->seed(RolesSeeder::class));

it('exibe loading state ao submeter criação de turno', function () {
    $admin = User::factory()->create(['password' => bcrypt('password')]);
    $admin->assignRole('admin');
    $this->actingAs($admin);

    $page = visit(route('admin.turnos.create'));
    
    // Previne o submit real para podermos assertar o estado de loading
    $page->script('document.getElementById("form-criar-turno").addEventListener("submit", function(e) { e.preventDefault(); });');
    
    // Dá um tempinho para o Alpine.js hidratar a página
    $page->wait(1);

    // ✅ Preenche TODOS os campos required para o browser não bloquear o submit
    $page->type('nome', 'Turno da Madrugada')
         ->type('hora_inicio', '08:00')
         ->type('hora_fim', '17:00')
         ->type('tolerancia_atraso_min', '10')
         ->type('intervalo_min', '60')
         
         // ✅ Usa click() com seletor CSS (press() é para teclas/texto)
         ->click('#btn-salvar-turno')
         ->wait(1)
         
         ->assertSee('Salvando...')
         ->assertDisabled('#btn-salvar-turno')
         ->assertNoJavaScriptErrors();
});