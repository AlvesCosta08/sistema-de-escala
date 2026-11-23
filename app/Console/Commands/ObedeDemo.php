<?php

namespace App\Console\Commands;

use App\Models\Alocacao;
use App\Models\Escala;
use App\Models\Posto;
use App\Models\Turno;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class ObedeDemo extends Command
{
    protected $signature = 'obede:demo
                            {--fresh : Apaga os dados demo antes de recriar}';

    protected $description = 'Popula o banco com dados de demonstração do Obede';

    public function handle(): int
    {
        if ($this->option('fresh')) {
            $this->limparDemo();
        }

        $this->garantirRoles();

        $admin = User::firstOrCreate(
            ['email' => 'admin@obede.local'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'cargo' => 'admin',
                'active' => true,
            ]
        );
        $admin->syncRoles(['admin']);

        $postos = collect([
            ['nome' => 'Portaria Principal', 'descricao' => 'Entrada principal do condomínio'],
            ['nome' => 'Recepção Bloco A', 'descricao' => 'Atendimento ao público'],
            ['nome' => 'Portaria Garagem', 'descricao' => 'Acesso de veículos'],
        ])->map(fn ($p) => Posto::firstOrCreate(['nome' => $p['nome']], $p));

        $diurno = Turno::firstOrCreate(['nome' => 'Diurno'], [
            'hora_inicio' => '07:00:00',
            'hora_fim' => '19:00:00',
            'tolerancia_atraso_min' => 10,
            'intervalo_min' => 60,
        ]);

        $noturno = Turno::firstOrCreate(['nome' => 'Noturno'], [
            'hora_inicio' => '19:00:00',
            'hora_fim' => '07:00:00',
            'tolerancia_atraso_min' => 10,
            'intervalo_min' => 60,
        ]);

        $colaboradores = [
            ['name' => 'João Silva', 'email' => 'joao@obede.local', 'cargo' => 'porteiro'],
            ['name' => 'Maria Souza', 'email' => 'maria@obede.local', 'cargo' => 'recepcionista'],
            ['name' => 'Carlos Lima', 'email' => 'carlos@obede.local', 'cargo' => 'porteiro'],
            ['name' => 'Ana Pereira', 'email' => 'ana@obede.local', 'cargo' => 'recepcionista'],
            ['name' => 'Paulo Costa', 'email' => 'paulo@obede.local', 'cargo' => 'porteiro'],
            ['name' => 'Fernanda Alves', 'email' => 'fernanda@obede.local', 'cargo' => 'recepcionista'],
        ];

        $users = collect($colaboradores)->map(function ($c) {
            $u = User::firstOrCreate(
                ['email' => $c['email']],
                array_merge($c, [
                    'password' => Hash::make('password'),
                    'active' => true,
                ])
            );
            $u->syncRoles([$c['cargo']]);
            return $u;
        });

        $escala = Escala::firstOrCreate(
            ['mes_referencia' => now()->format('Y-m')],
            ['status' => 'publicada', 'created_by' => $admin->id]
        );

        $turnos = [$diurno, $noturno];
        $totalAlocacoes = 0;

        foreach ($users as $i => $user) {
            for ($dia = 1; $dia <= 6; $dia++) {
                $data = now()->startOfMonth()->addDays($dia - 1 + ($i % 3));

                Alocacao::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'data' => $data->toDateString(),
                        'turno_id' => $turnos[$i % 2]->id,
                    ],
                    [
                        'escala_id' => $escala->id,
                        'posto_id' => $postos[$i % $postos->count()]->id,
                        'status' => 'confirmado',
                    ]
                );
                $totalAlocacoes++;
            }
        }

        $this->info('✅ Dados de demonstração criados.');
        $this->newLine();
        $this->table(
            ['Perfil', 'E-mail', 'Senha'],
            [
                ['Admin', 'admin@obede.local', 'password'],
                ['Porteiro', 'joao@obede.local', 'password'],
                ['Recepcionista', 'maria@obede.local', 'password'],
            ]
        );
        $this->line("Total de alocações geradas: {$totalAlocacoes}");
        $this->line("Postos: {$postos->count()} · Turnos: 2 · Colaboradores: {$users->count()}");

        return self::SUCCESS;
    }

    private function garantirRoles(): void
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

    private function limparDemo(): void
    {
        $emails = [
            'joao@obede.local', 'maria@obede.local', 'carlos@obede.local',
            'ana@obede.local', 'paulo@obede.local', 'fernanda@obede.local',
        ];

        $ids = User::whereIn('email', $emails)->pluck('id');

        Alocacao::whereIn('user_id', $ids)->delete();
        User::whereIn('email', $emails)->delete();

        $this->warn("Removidos: {$ids->count()} colaboradores demo + suas alocações.");
    }
}
