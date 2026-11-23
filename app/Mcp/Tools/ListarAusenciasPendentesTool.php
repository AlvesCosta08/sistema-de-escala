<?php

namespace App\Mcp\Tools;

use App\Models\AvisoAusencia;
use App\Models\User;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class ListarAusenciasPendentesTool extends Tool
{
    public function handle(Request $request): Response
    {
        $admin_id = (int) $request->get('admin_id');
        $admin = User::find($admin_id);

        if (! $admin || ! $admin->isAdmin()) {
            return Response::error('Acesso negado. Apenas administradores podem listar ausências pendentes.');
        }

        $avisos = AvisoAusencia::with(['user', 'alocacao.posto', 'alocacao.turno'])
            ->where('status', 'pendente')
            ->orderByDesc('urgente')
            ->orderBy('created_at')
            ->get();

        if ($avisos->isEmpty()) {
            return Response::text('Nenhuma ausência pendente. 🎉');
        }

        $linhas = $avisos->map(function (AvisoAusencia $a) {
            $urgencia = $a->urgente ? ' ⚠ URGENTE' : '';
            $data = $a->alocacao->data->format('d/m');
            $inicio = substr($a->alocacao->turno->hora_inicio, 0, 5);
            $fim = substr($a->alocacao->turno->hora_fim, 0, 5);
            return "{$a->user->name} — {$a->motivoRotulo()}{$urgencia}\n  {$a->alocacao->posto->nome} em {$data} ({$inicio}–{$fim})";
        })->implode("\n\n");

        return Response::text("Ausências pendentes ({$avisos->count()}):\n\n{$linhas}");
    }
}