<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'nome',
    'hora_inicio',
    'hora_fim',
    'tolerancia_atraso_min',
    'intervalo_min',
])]
class Turno extends Model
{
    /** @use HasFactory<\Database\Factories\TurnoFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'tolerancia_atraso_min' => 'integer',
            'intervalo_min' => 'integer',
        ];
    }

    public function alocacoes(): HasMany
    {
        return $this->hasMany(Alocacao::class);
    }

    public function rotulo(): string
    {
        return sprintf(
            '%s (%s–%s)',
            $this->nome,
            substr($this->hora_inicio, 0, 5),
            substr($this->hora_fim, 0, 5),
        );
    }

    public function duracaoHoras(): float
    {
        $inicio = strtotime($this->hora_inicio);
        $fim = strtotime($this->hora_fim);

        if ($fim <= $inicio) {
            $fim += 86400;
        }

        return round(($fim - $inicio) / 3600, 2);
    }
}