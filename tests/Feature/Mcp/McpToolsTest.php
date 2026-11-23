<?php

namespace Tests\Feature\Mcp;

use App\Mcp\Tools\ConsultarEscalaTool;
use App\Mcp\Tools\ListarAusenciasPendentesTool;
use App\Models\Alocacao;
use App\Models\AvisoAusencia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class McpToolsTest extends TestCase
{
    use RefreshDatabase, CreatesUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    /**
     * Extrai o texto de um Response do MCP.
     */
    private function texto(Response $response): string
    {
        // O Response expõe o conteúdo via `content()`, `text` ou `__toString`.
        if (method_exists($response, 'content')) {
            return (string) $response->content();
        }

        return (string) $response;
    }

    public function test_tool_consultar_escala_retorna_alocacoes_do_colaborador(): void
    {
        $porteiro = $this->porteiro();

        Alocacao::factory()->create([
            'user_id' => $porteiro->id,
            'data' => now()->startOfMonth()->addDays(4)->toDateString(),
            'status' => 'confirmado',
        ]);

        $response = (new ConsultarEscalaTool())->handle(
            new Request([
                'user_id' => $porteiro->id,
                'mes' => now()->format('Y-m'),
            ])
        );

        $resultado = $this->texto($response);

        $this->assertStringContainsString($porteiro->name, $resultado);
        $this->assertStringContainsString('Portaria', $resultado);
    }

    public function test_tool_consultar_escala_nao_vaza_alocacao_de_outro(): void
    {
        $porteiro = $this->porteiro();
        $outro = $this->porteiro();

        Alocacao::factory()->create([
            'user_id' => $outro->id,
            'data' => now()->startOfMonth()->addDays(2)->toDateString(),
        ]);

        $response = (new ConsultarEscalaTool())->handle(
            new Request([
                'user_id' => $porteiro->id,
                'mes' => now()->format('Y-m'),
            ])
        );

        $resultado = $this->texto($response);

        $this->assertStringNotContainsString($outro->name, $resultado);
    }

    public function test_tool_listar_ausencias_pendentes_retorna_avisos_para_admin(): void
    {
        $admin = $this->admin();
        $porteiro = $this->porteiro();

        AvisoAusencia::factory()->create([
            'user_id' => $porteiro->id,
            'status' => 'pendente',
        ]);

        AvisoAusencia::factory()->create([
            'user_id' => $porteiro->id,
            'status' => 'substituto_alocado',
        ]);

        $response = (new ListarAusenciasPendentesTool())->handle(
            new Request(['admin_id' => $admin->id])
        );

        $resultado = $this->texto($response);

        $this->assertStringContainsString($porteiro->name, $resultado);
    }

    public function test_tool_listar_ausencias_pendentes_recusa_nao_admin(): void
    {
        $porteiro = $this->porteiro();

        AvisoAusencia::factory()->create([
            'user_id' => $porteiro->id,
            'status' => 'pendente',
        ]);

        $response = (new ListarAusenciasPendentesTool())->handle(
            new Request(['admin_id' => $porteiro->id])
        );

        $resultado = $this->texto($response);

        $this->assertStringContainsString('Acesso negado', $resultado);
    }
}