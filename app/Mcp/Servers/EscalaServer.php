<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\ConsultarEscalaTool;
use App\Mcp\Tools\ListarAusenciasPendentesTool;
use Laravel\Mcp\Server;

class EscalaServer extends Server
{
    protected array $tools = [
        ConsultarEscalaTool::class,
        ListarAusenciasPendentesTool::class,
    ];
}