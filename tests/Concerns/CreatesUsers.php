<?php

namespace Tests\Concerns;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

trait CreatesUsers
{
    /**
     * Cria as roles e permissões do Obede no banco de teste.
     * Chamado automaticamente no setUp() de quem usa este trait.
     */
    protected function seedRoles(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'usuarios.gerenciar', 'postos.gerenciar', 'turnos.gerenciar',
            'escalas.gerenciar', 'escalas.publicar', 'ausencias.resolver',
            'relatorios.ver', 'minha-escala.ver', 'ausencia.solicitar',
        ];

        foreach ($permissions as $name) {
            Permission::findOrCreate($name, 'web');
        }

        Role::findOrCreate('admin', 'web')->syncPermissions(Permission::all());
        Role::findOrCreate('porteiro', 'web')->syncPermissions([
            'minha-escala.ver', 'ausencia.solicitar',
        ]);
        Role::findOrCreate('recepcionista', 'web')->syncPermissions([
            'minha-escala.ver', 'ausencia.solicitar',
        ]);
    }

    protected function admin(): User
    {
        $u = User::factory()->create(['cargo' => 'admin', 'active' => true]);
        $u->assignRole('admin');
        return $u;
    }

    protected function porteiro(): User
    {
        $u = User::factory()->create(['cargo' => 'porteiro', 'active' => true]);
        $u->assignRole('porteiro');
        return $u;
    }

    protected function recepcionista(): User
    {
        $u = User::factory()->create(['cargo' => 'recepcionista', 'active' => true]);
        $u->assignRole('recepcionista');
        return $u;
    }
}