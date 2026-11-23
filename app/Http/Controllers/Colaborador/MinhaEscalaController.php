<?php

namespace App\Http\Controllers\Colaborador;

use App\Http\Controllers\Controller;
use App\Models\Alocacao;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MinhaEscalaController extends Controller
{
    public function index(Request $request)
    {
        // Pega o mês da URL, ou usa o mês atual como padrão
        $mes = $request->get('mes', now()->format('Y-m'));

        // Busca as alocações do usuário logado para o mês selecionado
        $alocacoes = Alocacao::with([
                'posto:id,nome',
                'turno:id,nome,hora_inicio,hora_fim',
                'aviso:id,alocacao_id,status',
            ])
            ->where('user_id', $request->user()->id)
            ->whereYear('data', Carbon::parse($mes . '-01')->year)
            ->whereMonth('data', Carbon::parse($mes . '-01')->month)
            ->orderBy('data')
            ->get();

        return view('colaborador.minha-escala', compact('alocacoes', 'mes'));
    }
}