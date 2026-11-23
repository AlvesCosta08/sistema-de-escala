<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'escala_id',
    'user_id',
    'posto_id',
    'turno_id',
    'data',
    'status',
    'aviso_id',
    'substitui_alocacao_id',
])]
class Alocacao extends Model
{
    /** @use HasFactory<\Database\Factories\AlocacaoFactory> */
    use HasFactory;

    /**
     * O Laravel pluraliza "Alocacao" para "Alocacaos" (regra de adicionar "s"),
     * mas a migration criou a tabela como "alocacoes". Declaramos explicitamente
     * para evitar o mismatch.
     */
    protected $table = 'alocacoes';

    public const STATUS = [
        'confirmado',
        'pendente',
        'ausente',
        'substituida',
        'descoberta',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'date',
        ];
    }

    public function escala(): BelongsTo
    {
        return $this->belongsTo(Escala::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function posto(): BelongsTo
    {
        return $this->belongsTo(Posto::class);
    }

    public function turno(): BelongsTo
    {
        return $this->belongsTo(Turno::class);
    }

    public function aviso(): BelongsTo
    {
        return $this->belongsTo(AvisoAusencia::class, 'aviso_id');
    }

    public function alocacaoOriginal(): BelongsTo
    {
        return $this->belongsTo(self::class, 'substitui_alocacao_id');
    }

    public function substituicoes(): HasMany
    {
        return $this->hasMany(self::class, 'substitui_alocacao_id');
    }

    public function scopeDoMes(Builder $query, string $mes): Builder
    {
        return $query
            ->whereYear('data', substr($mes, 0, 4))
            ->whereMonth('data', substr($mes, 5, 2));
    }

    public function scopeConfirmadas(Builder $query): Builder
    {
        return $query->where('status', 'confirmado');
    }

    public function scopeDoColaborador(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function estaConfirmada(): bool
    {
        return $this->status === 'confirmado';
    }

    public function foiSubstituida(): bool
    {
        return $this->status === 'substituida';
    }

    public static function existeConflito(int $userId, string $data, int $turnoId, ?int $ignorarId = null): bool
    {
        return static::query()
            ->where('user_id', $userId)
            ->where('data', $data)
            ->where('turno_id', $turnoId)
            ->when($ignorarId, fn ($q) => $q->where('id', '!=', $ignorarId))
            ->exists();
    }
}