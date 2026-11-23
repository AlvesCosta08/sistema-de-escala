<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'alocacao_id',
    'user_id',
    'motivo',
    'observacao',
    'anexo_path',
    'urgente',
    'status',
    'substituto_user_id',
    'resolvido_por',
    'resolvido_em',
    'abonada',
])]
class AvisoAusencia extends Model
{
    /** @use HasFactory<\Database\Factories\AvisoAusenciaFactory> */
    use HasFactory;

    protected $table = 'aviso_ausencias';

    public const MOTIVOS = [
        'atestado_medico',
        'emergencia_pessoal',
        'imprevisto',
        'folga_negociada',
        'outro',
    ];

    public const STATUS = [
        'pendente',
        'substituto_alocado',
        'cancelado',
        'rejeitado',
        'posto_descoberto',
    ];

    protected function casts(): array
    {
        return [
            'urgente' => 'boolean',
            'abonada' => 'boolean',
            'resolvido_em' => 'datetime',
        ];
    }

    public function alocacao(): BelongsTo
    {
        return $this->belongsTo(Alocacao::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function substituto(): BelongsTo
    {
        return $this->belongsTo(User::class, 'substituto_user_id');
    }

    public function resolvidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolvido_por');
    }

    public function scopePendentes(Builder $query): Builder
    {
        return $query->where('status', 'pendente');
    }

    public function scopeUrgentes(Builder $query): Builder
    {
        return $query->where('urgente', true);
    }

    public function pendente(): bool
    {
        return $this->status === 'pendente';
    }

    public function resolvido(): bool
    {
        return in_array($this->status, ['substituto_alocado', 'cancelado', 'rejeitado', 'posto_descoberto'], true);
    }

    public function motivoRotulo(): string
    {
        return match ($this->motivo) {
            'atestado_medico' => 'Atestado médico',
            'emergencia_pessoal' => 'Emergência pessoal',
            'imprevisto' => 'Imprevisto',
            'folga_negociada' => 'Folga negociada',
            'outro' => 'Outro',
            default => $this->motivo,
        };
    }
}