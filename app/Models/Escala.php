<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['mes_referencia', 'status', 'created_by'])]
class Escala extends Model
{
    /** @use HasFactory<\Database\Factories\EscalaFactory> */
    use HasFactory;

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function alocacoes(): HasMany
    {
        return $this->hasMany(Alocacao::class);
    }

    public function scopePublicadas(Builder $query): Builder
    {
        return $query->where('status', 'publicada');
    }

    public function scopeDoMes(Builder $query, string $mes): Builder
    {
        return $query->where('mes_referencia', $mes);
    }

    public function publicada(): bool
    {
        return $this->status === 'publicada';
    }
}