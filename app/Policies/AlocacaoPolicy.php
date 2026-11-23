<?php

namespace App\Policies;

use App\Models\Alocacao;
use App\Models\User;

class AlocacaoPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Alocacao $alocacao): bool
    {
        return $user->isAdmin() || $user->id === $alocacao->user_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Alocacao $alocacao): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Alocacao $alocacao): bool
    {
        return $user->isAdmin();
    }

    public function marcarAusencia(User $user, Alocacao $alocacao): bool
    {
        return $user->id === $alocacao->user_id
            && $alocacao->status === 'confirmado';
    }
}