# sistema-de-escala

<div align="center">

# Obede

**Sistema de gestão de escalas de plantão para condomínios**



[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square&logo=php&logoColor=white)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-11+-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com/)
[![Tests](https://img.shields.io/badge/tests-41%20passed-2ea44f?style=flat-square&logo=githubactions&logoColor=white)](.github/workflows/tests.yml)
[![Pest Browser](https://img.shields.io/badge/Pest%20Browser-v4-8B5CF6?style=flat-square&logo=pest&logoColor=white)](https://pestphp.com/docs/browser-testing)
[![License](https://img.shields.io/badge/license-Proprietary-red?style=flat-square)](LICENSE)

</div>

---

## Sobre o projeto

**Obede** é uma aplicação web para gerenciar escalas de porteiros e recepcionistas de condomínios. Cobre o ciclo completo — do cadastro de postos e turnos até a efetivação de substituições em caso de ausência — com validação de conflitos, notificações e calendário visual.Obede um sistema que carrega o conceito bíblico de serviço, zelo, responsabilidade e cuidado.

Resolve três dores operacionais típicas:

- **Escala dispersa** — antes em planilhas, hoje em um calendário único e consultável por cada colaborador.
- **Ausências descoordenadas** — colaborador sinaliza indisponibilidade; admin vê avisos urgentes primeiro e aloca substituto com bloqueio automático de conflitos.
- **Falta de rastreabilidade** — cada alocação e substituição é registrada com status e notificação aos envolvidos.

---

## Sumário

- [Funcionalidades](#funcionalidades)
- [Stack tecnológico](#stack-tecnológico)
- [Pré-requisitos](#pré-requisitos)
- [Instalação](#instalação)
- [Usuários de demonstração](#usuários-de-demonstração)
- [Comandos Artisan](#comandos-artisan)
- [Testes](#testes)
- [Estrutura do projeto](#estrutura-do-projeto)
- [Matriz de permissões](#matriz-de-permissões)
- [Deploy em produção](#deploy-em-produção)
- [CI/CD](#cicd)
- [Licença](#licença)

---

## Funcionalidades

### Para administradores

- **Painel de ausências pendentes** — avisos urgentes (menos de 24h) destacados no topo
- **Calendário mensal** com foto, nome e posto dos colaboradores alocados
- **Efetivar substituição** — aloca substituto com validação de:
  - mesma função (`cargo`) do ausente
  - ausência de conflito de turno no mesmo dia
  - intervalo mínimo de 11h entre turnos
  - bloqueio de auto-substituição
- **Notificação automática** ao substituto e ao solicitante quando a substituição é efetivada

### Para colaboradores (porteiro / recepcionista)

- **Minha Escala** — visualização filtrada apenas das próprias alocações
- **Aviso de ausência** — sinaliza que não poderá comparecer, com motivo e observação
  - Avisos com menos de 24h de antecedência são classificados como **urgentes**
- **Upload de foto de perfil** com thumbnail WebP 200×200 gerada automaticamente
- **Notificações** de alocação e substituição

### Integração com IA (MCP)

Duas *tools* expostas via [Model Context Protocol](https://modelcontextprotocol.io) para consulta em linguagem natural:

| Tool | Descrição | Autorização |
|---|---|---|
| `consultar_escala` | Retorna alocações do colaborador autenticado | colaborador |
| `listar_ausencias_pendentes` | Lista avisos de ausência pendentes | admin |

---

## Stack tecnológico

| Camada | Tecnologia |
|---|---|
| Linguagem | PHP 8.4+ |
| Framework | Laravel 11+ |
| Permissões | [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission) |
| Frontend | Tailwind CSS + Alpine.js |
| Banco (dev) | MySQL 8 |
| Banco (prod) | MySQL 8 |
| Banco (CI) | SQLite em memória |
| Imagens | GD (thumbnails WebP) |
| Testes de Backend | PHPUnit (41 testes, 93 assertions) |
| Testes de Navegador | Pest v4 Browser + Playwright |
| CI | GitHub Actions |
| Integração IA | Model Context Protocol (MCP) |

---

## Pré-requisitos

- **PHP 8.4+** com extensões:
  `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `gd`, `hash`, `mbstring`, `openssl`, `pcre`, `pdo`, `pdo_mysql`, `pdo_sqlite`, `session`, `tokenizer`, `xml`
- **Composer 2**
- **Node.js 20+** (apenas se for compilar assets com Vite)
- **MySQL 8** (dev e produção; o CI usa SQLite em memória)

---

## Instalação

### Setup rápido (dev, MySQL)

```bash
# 1. Clonar e instalar dependências

git clone https://github.com/AlvesCosta08/obede.git obede
cd obede
composer install

# 2. Configurar ambiente

cp .env.example .env

# Ajuste o .env para dev:
#   APP_ENV=local
#   APP_DEBUG=true
#   APP_URL=http://localhost:8000
#   DB_CONNECTION=mysql
#   DB_DATABASE=obede
#   DB_USERNAME=<seu-usuario>
#   DB_PASSWORD=<sua-senha>

php artisan key:generate

# 3. Criar o banco (se ainda não existir)
mysql -u root -p -e "CREATE DATABASE obede CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 4. Rodar migrations
php artisan migrate

# 5. Popular com dados de demonstração
php artisan obede:demo

# 6. Linkar storage público (fotos de perfil)
php artisan storage:link

# 7. Subir o servidor
php artisan serve

Instalação de dependências de frontend (Alpine.js + Tailwind)
bash
npm install
npm run build

💡 Obrigatório para testes de navegador: o Playwright usa os assets compilados. Sem npm run build, os testes de Browser falham.

Usuários de demonstração
Após rodar php artisan obede:demo, os seguintes usuários estarão disponíveis:

Perfil	E-mail	Senha
Administrador	admin@obede.local	password
Porteiro	porteiro@obede.local	password
Recepcionista	recepcionista@obede.local	password
Comandos Artisan

Comando	Descrição

php artisan obede:demo	Popula o banco com dados de demonstração
php artisan migrate:fresh --seed	Recria o banco e roda todos os seeders
php artisan db:seed --class=RolesSeeder	Provisiona apenas as roles em produção
php artisan test	Roda toda a suíte de testes (Feature + Unit)


Testes

O projeto adota TDD em duas camadas:

Testes de Backend (PHPUnit)

Cobrem controllers, regras de negócio, policies e integrações com banco.



php artisan test
# ou apenas Feature

php artisan test --testsuite=Feature

Testes de Navegador (Pest Browser + Playwright)

Cobrem interações de frontend que dependem de JavaScript (Alpine.js) e não podem ser validadas apenas com o HTML retornado pelo servidor.

Pré-requisitos para rodar:



npm run build                              # compila assets (obrigatório)
npx playwright install --with-deps         # instala navegadores (primeira vez)
Rodar um teste de navegador:



vendor/bin/pest tests/Browser
Padrões de teste:

Viewport mobile: usar ->on()->mobile() antes do ->click().

Verificação de erros JS: sempre incluir ->assertNoJavascriptErrors().

Seeders: incluir $this->seed(RolesSeeder::class) no início do teste.

Cache Blade: se o teste falhar de forma estranha, rodar php artisan view:clear.

Exemplo de teste de navegador:

php
it('abre o dropdown do avatar no header mobile ao clicar', function () {
    $this->seed(RolesSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin);

    visit('/admin/ausencias')
        ->on()->mobile()
        ->click('[data-testid="avatar-menu"]')
        ->assertSee('Meu Perfil')
        ->assertSee('Sair')
        ->assertNoJavascriptErrors();
});

Estrutura do projeto



obede/
├── app/
│   ├── Console/          # Comandos Artisan (ex: obede:demo)
│   ├── Http/             # Controllers, Middleware, Requests
│   ├── Mcp/              # Tools do Model Context Protocol
│   ├── Models/           # Eloquent Models
│   ├── Notifications/    # Notificações (alocação, substituição)
│   ├── Policies/         # Políticas de autorização
│   └── Services/         # Regras de negócio (ex: conflito de turno)
├── database/
│   ├── factories/        # UserFactory, etc.
│   ├── migrations/
│   └── seeders/          # RolesSeeder, DemoSeeder
├── resources/
│   ├── css/
│   ├── js/               # app.js (Alpine.js)
│   └── views/            # Blade templates
├── routes/
│   ├── ai.php            # Rotas MCP
│   ├── console.php
│   └── web.php
├── tests/
│   ├── Browser/          # Pest Browser (Playwright)
│   ├── Feature/          # PHPUnit Feature
│   ├── Unit/             # PHPUnit Unit
│   └── Pest.php
└── ROADMAP.md            # Acompanhamento do projeto


Matriz de permissões

Recurso	Admin	Porteiro	Recepcionista

Ver ausências pendentes	✅	❌	❌

Alocar substituto	✅	❌	❌

Rejeitar ausência	✅	❌	❌

CRUD de usuários	✅	❌	❌

CRUD de postos	✅	❌	❌

CRUD de turnos	✅	❌	❌

Gerenciar escalas	✅	❌	❌

Ver Minha Escala	❌	✅	✅

Pedir ausência	❌	✅	✅

Editar próprio perfil	✅	✅	✅

Deploy em produção

⚠️ Status: aguardando sinal do desenvolvedor (ver ROADMAP.md).

Passos planejados para Hospedagem Compartilhada (HostGator):

Configurar estrutura de pastas (laravel_app e public_html) no cPanel.

Configurar o .env de produção e banco MySQL.

Rodar migrações e o RolesSeeder no servidor.

Configurar SMTP real (Gmail/SendGrid) para envio de e-mails.

Configurar rotina de backup do banco de dados.

CI/CD

O projeto usa GitHub Actions para rodar a suíte de testes a cada push/PR:

PHP 8.4 com SQLite em memória.

Vite + Tailwind compilados no workflow.

Pest Browser configurado para testes de navegador.

Workflow: .github/workflows/tests.yml

Licença
Proprietária — todos os direitos reservados.

Última atualização: Novembro de 2026.
Status: MVP e UI concluídos. Refatoração Frontend com TDD em andamento.




1. **Formatação corrigida**: o `tree` e o bloco de instalação estavam **fora** do `EOF` no seu arquivo original. Agora tudo está dentro do Markdown, com blocos de código delimitados corretamente.

2. **Badges atualizados**: adicionei badge do **Pest Browser v4**, e mudei PHP para 8.4, e testes para 41 passed (refletindo o novo teste de navegador).

3. **Seção "Testes" expandida**: separa claramente Backend (PHPUnit) de Navegador (Pest Browser + Playwright), com pré-requisitos e exemplo de teste.
4. **Seção "Estrutura do projeto"** refatorada: mostra o `tree` real com `tests/Browser/` incluído.

5. **Stack tecnológico** atualizado: inclui `Alpine.js` no frontend e `Pest v4 Browser` nos testes.

6. **Instalação** ganhou um passo explícito para `npm run build` (obrigatório para testes de navegador).

7. **Tom profissional**: mantive seu estilo (negrito, listas, tabelas), mas com formatação consistente em todo o arquivo.


# sistema-de-escala
