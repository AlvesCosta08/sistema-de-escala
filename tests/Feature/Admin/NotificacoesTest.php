<?php

namespace Tests\Feature\Admin;

use App\Models\Alocacao;
use App\Models\AvisoAusencia;
use App\Notifications\AvisoAusenciaCriado;
use App\Notifications\SubstitutoAlocado;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class NotificacoesTest extends TestCase
{
    use RefreshDatabase, CreatesUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_admin_recebe_notificacao_quando_colaborador_marca_ausencia(): void
    {
        Notification::fake();

        $admin = $this->admin();
        $porteiro = $this->porteiro();

        $alocacao = Alocacao::factory()->create([
            'user_id' => $porteiro->id,
            'status' => 'confirmado',
            'data' => now()->addDays(3)->toDateString(),
        ]);

        $this->actingAs($porteiro)
            ->post(route('ausencia.store', $alocacao), [
                'motivo' => 'imprevisto',
            ]);

        Notification::assertSentTo($admin, AvisoAusenciaCriado::class);
    }

    public function test_substituto_recebe_notificacao_quando_alocado(): void
    {
        Notification::fake();

        $admin = $this->admin();
        $solicitante = $this->porteiro();
        $substituto = $this->porteiro();

        $alocacao = Alocacao::factory()->create([
            'user_id' => $solicitante->id,
            'status' => 'pendente',
        ]);
        $aviso = AvisoAusencia::factory()->create([
            'alocacao_id' => $alocacao->id,
            'user_id' => $solicitante->id,
            'status' => 'pendente',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.ausencias.alocar', $aviso), [
                'substituto_user_id' => $substituto->id,
            ]);

        Notification::assertSentTo($substituto, SubstitutoAlocado::class);
    }

    public function test_solicitante_recebe_notificacao_quando_substituto_alocado(): void
    {
        Notification::fake();

        $admin = $this->admin();
        $solicitante = $this->porteiro();
        $substituto = $this->porteiro();

        $alocacao = Alocacao::factory()->create([
            'user_id' => $solicitante->id,
            'status' => 'pendente',
        ]);
        $aviso = AvisoAusencia::factory()->create([
            'alocacao_id' => $alocacao->id,
            'user_id' => $solicitante->id,
            'status' => 'pendente',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.ausencias.alocar', $aviso), [
                'substituto_user_id' => $substituto->id,
            ]);

        Notification::assertSentTo($solicitante, SubstitutoAlocado::class);
    }
}