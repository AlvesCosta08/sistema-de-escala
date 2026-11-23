<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesSeeder extends Seeder
{
    public function run(): void
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
}