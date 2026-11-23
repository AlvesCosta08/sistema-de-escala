<?php

namespace App\Mcp\Tools;

use App\Models\Alocacao;
use App\Models\User;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class ConsultarEscalaTool extends Tool
{
    public function handle(Request $request): Response
    {
        $user_id = (int) $request->get('user_id');
        $mes = (string) $request->get('mes');

        $user = User::find($user_id);
        if (! $user) {
            return Response::error('Usuário não encontrado.');
        }

        if (! preg_match('/^\d{4}-\d{2}$/', $mes)) {
            return Response::error('Formato de mês inválido. Use YYYY-MM (ex.: 2026-11).');
        }

        $alocacoes = Alocacao::with(['posto', 'turno'])
            ->where('user_id', $user_id)
            ->doMes($mes)
            ->orderBy('data')
            ->get();

        if ($alocacoes->isEmpty()) {
            return Response::text("Nenhuma alocação para {$user->name} em {$mes}.");
        }

        $linhas = $alocacoes->map(function (Alocacao $a) {
            $data = $a->data->isoFormat('ddd, DD/MM');
            $inicio = substr($a->turno->hora_inicio, 0, 5);
            $fim = substr($a->turno->hora_fim, 0, 5);
            return "{$data} — {$a->posto->nome} ({$inicio}–{$fim}) [{$a->status}]";
        })->implode("\n");

        return Response::text("Escala de {$user->name} em {$mes}:\n\n{$linhas}");
    }
}