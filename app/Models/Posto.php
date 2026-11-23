<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nome', 'descricao', 'localizacao', 'ativo'])]
class Posto extends Model
{
    /** @use HasFactory<\Database\Factories\PostoFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
        ];
    }

    public function alocacoes(): HasMany
    {
        return $this->hasMany(Alocacao::class);
    }

    public function scopeAtivos(Builder $query): Builder
    {
        return $query->where('ativo', true);
    }
}