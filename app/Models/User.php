<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'cargo',
        'phone',
        'foto_path',
        'foto_thumb_path',
        'active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ✅ NOVO: Garante que este atributo computado seja incluído na serialização JSON/Array
    protected $appends = [
        'foto_thumb_url',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }

    // ---------------------------------------------------------------------
    // Relacionamentos
    // ---------------------------------------------------------------------

    public function alocacoes(): HasMany
    {
        return $this->hasMany(Alocacao::class);
    }

    public function avisosAusencia(): HasMany
    {
        return $this->hasMany(AvisoAusencia::class);
    }

    public function substituicoes(): HasMany
    {
        return $this->hasMany(AvisoAusencia::class, 'substituto_user_id');
    }

    // ---------------------------------------------------------------------
    // Helpers de perfil
    // ---------------------------------------------------------------------

    public function isAdmin(): bool
    {
        return $this->cargo === 'admin' || $this->hasRole('admin');
    }

    public function isPorteiro(): bool
    {
        return $this->cargo === 'porteiro';
    }

    public function isRecepcionista(): bool
    {
        return $this->cargo === 'recepcionista';
    }

    /**
     * URL pública da thumbnail (usando rota PHP, pois HostGator bloqueia symlink).
     */
    public function fotoThumbUrl(): ?string
    {
        return $this->foto_thumb_path
            ? route('arquivo.show', ['path' => $this->foto_thumb_path])
            : null;
    }

    /**
     * ✅ NOVO: Accessor para expor a URL da thumbnail na serialização JSON (Alpine.js)
     */
    public function getFotoThumbUrlAttribute(): ?string
    {
        return $this->fotoThumbUrl();
    }

    /**
     * URL pública da foto original (usando rota PHP, pois HostGator bloqueia symlink).
     */
    public function fotoUrl(): ?string
    {
        return $this->foto_path
            ? route('arquivo.show', ['path' => $this->foto_path])
            : null;
    }

    // ---------------------------------------------------------------------
    // Elegibilidade para substituição (ciclo 3)
    // ---------------------------------------------------------------------

    public static function elegiveisParaSubstituir(Alocacao $alocacao): Collection
    {
        $solicitante = $alocacao->user;

        return static::query()
            ->where('cargo', $solicitante->cargo)
            ->where('active', true)
            ->where('id', '!=', $solicitante->id)
            ->get()
            ->filter(fn (User $candidato) => ! static::temConflitoDeEscala($candidato->id, $alocacao))
            ->values();
    }

    protected static function temConflitoDeEscala(int $userId, Alocacao $alocacao): bool
    {
        $conflitoDireto = Alocacao::query()
            ->where('user_id', $userId)
            ->whereDate('data', $alocacao->data)
            ->where('turno_id', $alocacao->turno_id)
            ->where('status', '!=', 'substituida')
            ->exists();

        if ($conflitoDireto) {
            return true;
        }

        $propostoInicio = Carbon::parse(
            $alocacao->data->format('Y-m-d').' '.$alocacao->turno->hora_inicio
        );
        $propostoFim = Carbon::parse(
            $alocacao->data->format('Y-m-d').' '.$alocacao->turno->hora_fim
        );
        if ($propostoFim->lte($propostoInicio)) {
            $propostoFim->addDay();
        }

        $vizinhas = Alocacao::query()
            ->where('user_id', $userId)
            ->where('status', '!=', 'substituida')
            ->whereBetween('data', [
                $alocacao->data->copy()->subDays(2),
                $alocacao->data->copy()->addDays(2),
            ])
            ->with('turno')
            ->get();

        foreach ($vizinhas as $existente) {
            $existenteInicio = Carbon::parse(
                $existente->data->format('Y-m-d').' '.$existente->turno->hora_inicio
            );
            $existenteFim = Carbon::parse(
                $existente->data->format('Y-m-d').' '.$existente->turno->hora_fim
            );
            if ($existenteFim->lte($existenteInicio)) {
                $existenteFim->addDay();
            }

            if ($existenteFim->lte($propostoInicio)) {
                if (abs($existenteFim->diffInHours($propostoInicio)) < 11) {
                    return true;
                }
            } elseif ($propostoFim->lte($existenteInicio)) {
                if (abs($propostoFim->diffInHours($existenteInicio)) < 11) {
                    return true;
                }
            } else {
                return true;
            }
        }

        return false;
    }
}