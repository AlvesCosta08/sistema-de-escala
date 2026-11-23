<?php

use App\Mcp\Servers\EscalaServer;
use Laravel\Mcp\Facades\Mcp;

// Local — assistentes rodando no mesmo host
Mcp::local('obede', EscalaServer::class);

// Web — clientes remotos via HTTP (descomente quando for usar)
// Mcp::web('/mcp/obede', EscalaServer::class)
//     ->middleware(['auth:sanctum']);