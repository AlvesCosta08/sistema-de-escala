<?php

use App\Models\User;
use Database\Seeders\RolesSeeder;

beforeEach(fn() => $this->seed(RolesSeeder::class));

it('exibe loading state e desabilita o botão ao submeter upload de foto', function () {
    $user = User::factory()->create(['password' => bcrypt('password')]);
    $user->assignRole('porteiro');
    $this->actingAs($user);

    $page = visit(route('perfil.edit'));

    // Remove o atributo required para permitir o submit sem arquivo no teste de UI
    $page->script('document.querySelector("input[name=\'foto\']").removeAttribute("required");');
    
    // Previne o submit real para podermos assertar o estado de loading da UI
    $page->script('document.getElementById("form-upload-foto").addEventListener("submit", function(e) { e.preventDefault(); });');

    // Clica no botão de submit
    $page->click('#btn-salvar-foto')
         ->wait(1) // Aguarda o Alpine.js processar a reatividade
         
         // Valida o comportamento visual
         ->assertSee('Enviando...')
         ->assertDisabled('#btn-salvar-foto')
         ->assertNoJavaScriptErrors();
});