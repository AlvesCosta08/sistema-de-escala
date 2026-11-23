# 🗺️ Roadmap e Status do Projeto - Obede

Este documento serve como guia de acompanhamento do desenvolvimento do sistema Obede, detalhando o que já foi implementado e quais são os próximos passos.

## 🟢 O que já foi feito (Concluído)

### Infraestrutura e Configuração Inicial
- [x] Criação e configuração do banco de dados MySQL.
- [x] Configuração do Github Actions (CI) para rodar testes em PHP 8.4 com SQLite em memória.
- [x] Criação do `RolesSeeder` para provisionamento de permissões em produção.
- [x] Encadeamento do `RolesSeeder` no `DatabaseSeeder`.
- [x] Configuração do Vite e Tailwind no GitHub Actions.

### Autenticação e Segurança
- [x] Criação do `LoginController` (Login e Logout).
- [x] Tela de Login moderna e responsiva.
- [x] Redirecionamento inteligente pós-login (Admin vai para o painel, Colaborador vai para Minha Escala).
- [x] Middleware de proteção de rotas (`role:admin` e `role:porteiro|recepcionista`).

### CRUDs Base (Totalmente implementados com TDD)
- [x] **Usuários (Colaboradores):** Listar, Criar, Editar, Ativar/Desativar.
- [x] **Postos:** Listar, Criar, Editar (com descrição).
- [x] **Turnos:** Listar, Criar, Editar (com horários, tolerância e intervalo).
- [x] **Escalas:** Criar a escala mensal, publicar e gerenciar alocações.
- [x] **Alocações:** Escalar colaborador (com bloqueio de duplicidade) e remover.

### Regras de Negócio e Funcionalidades
- [x] Colaborador pode solicitar ausência em uma alocação.
- [x] Admin pode visualizar ausências pendentes (urgentes no topo).
- [x] Admin pode efetivar substituto (bloqueando conflitos de turno, cargo e intervalo de 11h).
- [x] Sistema de notificações no banco de dados.
- [x] Geração de thumbnail (WebP 200x200) no upload de foto de perfil.
- [x] Recuperação do Calendário Visual (para o Admin visualizar as escalas do mês).

### Melhorias de Interface (UX/UI) - CONCLUÍDO
- [x] Criação de um **Layout Master** (`app.blade.php`) com Sidebar unificada.
- [x] Substituição dos estilos HTML inline por Tailwind CSS.
- [x] Refatoração de todas as telas do Admin (Usuários, Postos, Turnos, Escalas).
- [x] Refatoração de todas as telas do Colaborador (Minha Escala, Aviso de Ausência, Perfil).
- [x] Design responsivo e moderno aplicado.
- [x] **Avatar melhorado:** Inicial colorida (indigo) quando o usuário não tem foto.

### Refatoração Frontend com TDD (Pest Browser + Alpine.js) - ✅ 100% CONCLUÍDO
- [x] Configuração do ambiente de testes de navegador (Pest v4 Browser + Playwright).
- [x] **Dropdown do usuário** no header mobile e sidebar desktop (Alpine.js, `x-show`, `x-transition`, `@click.outside`).
- [x] **Loading state padronizado** em TODOS os formulários críticos:
  - [x] Admin: Usuários (criar/editar), Postos (criar/editar), Turnos (criar/editar), Escalas (criar), Alocações (alocar plantão).
  - [x] Colaborador: Solicitação de Ausência.
- [x] **Toasts de sucesso/erro:** Componente global inline no `app.blade.php` com Alpine.js, animações suaves, auto-dismiss (4s) e 4 variantes (success, error, warning, info). Substituiu banners estáticos.
- [x] **Modal de confirmação:** Componente Blade reutilizável `<x-confirm-action>` para ações destrutivas (remover alocação, ativar/desativar usuário), com backdrop blur, mensagens dinâmicas e testes de ciclo de vida completo.
- [x] **Arquitetura Frontend Modular:** Separação de responsabilidades em `resources/js/components/` (toast, confirm-modal, loading-state, dropdown) e `resources/css/custom/` (animations, components), com build otimizado pelo Vite.
- [x] **Correção de arquitetura de views:** Separação correta entre `admin/escalas/index.blade.php` (Calendário Mensal) e `admin/escalas/gerenciar.blade.php` (Lista de Escalas).
- [x] **Blindagem de regras de negócio:** Validação `abort_if($alocacao->status !== 'confirmado')` no controller do colaborador para impedir solicitação de ausência em alocações já pendentes/substituídas.
- [x] **Suíte completa de testes Full Stack:** 80 testes passando (65 Feature + 15 Browser), 210 assertions validadas em ~47s.

---

## 🟡 O que está em andamento (Próximos Passos)

O sistema encontra-se na fase de **Homologação Local** e **Evolução de UX com TDD**.

### Homologação Local
- [ ] **Testes Manuais:** Navegar por todas as telas, testar o fluxo completo (Admin criar escala -> Porteiro pedir ausência -> Admin alocar substituto) e validar a experiência visual.
- [ ] **Ajustes Finos:** Corrigir eventuais bugs ou detalhes de layout que surjam durante os testes.

### Evolução Frontend (TDD) — Próximos Ciclos

#### 🔴 Prioridade Alta (impacto direto na UX)
- [ ] **Teste de regressão do Dropdown Desktop:** Formalizar em teste o que já foi implementado.
- [ ] **Loading state no upload de foto de perfil:** Feedback visual (spinner + botão desabilitado) em `perfil/edit.blade.php`.

#### 🟠 Prioridade Média (melhoria de produtividade)
- [ ] **Busca instantânea:** Filtragem client-side em listas (Usuários, Escalas, Postos) com Alpine (`x-model`, `x-show`).
- [ ] **Destaque "próximo turno" no Minha Escala:** Card em destaque para o colaborador, com horário e posto.
- [ ] **Empty states com CTA:** Quando não houver dados (ex: "Nenhuma escala publicada — Criar agora").

#### 🟢 Prioridade Baixa (polimento)
- [ ] **Atalhos de teclado:** `N` para novo, `Esc` para fechar modal/dropdown.
- [ ] **Skeleton loading:** Em listas longas (substituir spinners genéricos).
- [ ] **Badges numéricos na sidebar:** Contador de ausências pendentes ao lado de "Início".

---

## 🔴 O que falta fazer (Backlog / Futuro)

### Deploy em Produção (Aguardando sinal do desenvolvedor)
- [ ] Configurar Hospedagem Compartilhada (HostGator).
- [ ] Criar estrutura de pastas (`laravel_app` e `public_html`) no cPanel.
- [ ] Configurar o `.env` de produção e banco de dados MySQL.
- [ ] Rodar as migrações e o `RolesSeeder` no servidor.
- [ ] Configurar SMTP real (Gmail/SendGrid) para envio de e-mails.
- [ ] Configurar rotina de backup do banco de dados.

### Funcionalidades Adicionais (Pós-MVP)
- [ ] **Relatórios:** Tela para exportar a escala do mês em PDF ou Excel.
- [ ] **Recuperação de Senha:** Fluxo de "Esqueci minha senha" via e-mail.
- [ ] Alteração de senha pelo próprio perfil do usuário.

---

## 🧪 Padrões de Teste (Frontend)

Para manter a consistência nos próximos ciclos TDD:

### Setup e Execução
- **Assets:** rodar `npm run build` antes de executar testes Browser.
- **Cache Blade:** se o teste falhar de forma estranha, rodar `php artisan view:clear`.
- **Seeders:** incluir `$this->seed(RolesSeeder::class)` no `beforeEach()`.
- **Permissões Spatie:** sempre usar `$admin->assignRole('admin')` após criar o usuário no teste.
- **Comando completo:** `./vendor/bin/pest tests/Feature tests/Browser` para rodar Full Stack.
- **Cache de rotas/classes:** em caso de erro "Cannot redeclare class", rodar `composer dump-autoload` e `php artisan optimize:clear`.

### Sintaxe Pest Browser (Playwright)
- **Viewport mobile:** usar `->on()->mobile()` antes do `->click()`.
- **Verificação de erros JS:** sempre incluir `->assertNoJavascriptErrors()`.
- **Clique em botão:** usar `->click('#id-do-botao')` com seletor CSS (NÃO usar `->press()` para seletores CSS).
- **Pausa para Alpine:** incluir `->wait(1)` após o clique para dar tempo do Alpine.js processar a reatividade.

### Formulários e Alpine.js
- **Múltiplos `<form>` na página:** usar `id` específico (`document.getElementById("form-x")`) em vez de `document.querySelector("form")`.
- **Eventos JS no Alpine:** preferir `x-on:submit` em vez de `@submit` no Blade, para evitar conflito com diretivas do Laravel.
- **Campos required:** preencher TODOS os campos obrigatórios no teste para evitar bloqueio da validação HTML5 nativa do browser.
- **Prevenir submit real no teste:** usar `$page->script('document.getElementById("form-x").addEventListener("submit", function(e) { e.preventDefault(); });')` antes de clicar no botão.

### Arquitetura de Views e Controllers
- **Separação de responsabilidades:** cada view deve ter um propósito único (ex: `index.blade.php` para Calendário, `gerenciar.blade.php` para Lista de Escalas).
- **Variáveis do controller:** garantir que o controller passa exatamente as variáveis que a view espera via `compact()` ou array.
- **Controllers sempre estendem `Controller` base:** `class MeuController extends Controller` para ter acesso a `authorize()`, `abort_if()`, etc.
- **Namespaces corretos:** verificar se o namespace do controller corresponde ao diretório (`App\Http\Controllers\Colaborador` vs `App\Http\Controllers\Admin`).

### Toasts e Modais
- **Toasts via session flash:** o componente global captura automaticamente `session('success')`, `session('error')`, `session('warning')`, `session('info')` e `$errors->any()`.
- **Componente `<x-confirm-action>`:** usar para qualquer ação destrutiva, passando `action`, `method`, `title`, `message`, `buttonText`, `buttonClass` e `id` único.

---

## 📂 Arquivos de Referência dos Testes

### Testes de Browser (Frontend + Integração) — 15 testes

| Arquivo | O que valida |
|---------|--------------|
| `tests/Browser/NavbarDropdownTest.php` | Abertura do dropdown mobile do usuário |
| `tests/Browser/FormLoadingStateTest.php` | Loading state no botão de submit (criar usuário - legado) |
| `tests/Browser/UserCreateLoadingStateTest.php` | Loading state na criação de usuário |
| `tests/Browser/UserEditLoadingStateTest.php` | Loading state na edição de usuário |
| `tests/Browser/PostoCreateLoadingStateTest.php` | Loading state na criação de posto |
| `tests/Browser/PostoEditLoadingStateTest.php` | Loading state na edição de posto |
| `tests/Browser/TurnoCreateLoadingStateTest.php` | Loading state na criação de turno |
| `tests/Browser/TurnoEditLoadingStateTest.php` | Loading state na edição de turno |
| `tests/Browser/EscalaCreateLoadingStateTest.php` | Loading state na criação de escala |
| `tests/Browser/AlocacaoLoadingStateTest.php` | Loading state na alocação de plantão |
| `tests/Browser/ColaboradorAusenciaTest.php` | Loading state na solicitação de ausência do colaborador |
| `tests/Browser/ToastNotificationTest.php` | Toasts globais (aparecimento e auto-dismiss) |
| `tests/Browser/ConfirmModalTest.php` | Modal de confirmação ao remover alocação |
| `tests/Browser/UserToggleStatusModalTest.php` | Modal de confirmação ao ativar/desativar usuário |
| `tests/Browser/DiagnosticoTest.php` | *(Diagnóstico — pode ser removido)* |

### Testes de Feature (Backend) — 65 testes passando
Organizados em `tests/Feature/` cobrindo:
- **Admin:** Ausências Pendentes, Calendário, Detalhe de Ausência, Efetivar Substituição, Gerenciar Alocações/Escalas/Postos/Turnos/Usuários, Notificações.
- **Colaborador:** Aviso de Ausência, Minha Escala.
- **MCP Tools:** Consultar Escala, Listar Ausências Pendentes.
- **Perfil:** Upload de Foto.

---

## 📊 Status Consolidado Final

| Camada | Testes | Assertions | Status |
|--------|--------|------------|--------|
| **Backend (Feature)** | 65 | 158 | 🟢 100% |
| **Frontend (Browser)** | 15 | 52 | 🟢 100% |
| **TOTAL** | **80** | **210** | 🟢 **100%** |

**Tempo de execução total:** ~47 segundos (extremamente saudável para CI/CD).

---

**Última atualização:** 23 de Novembro de 2026.
**Status:** MVP, UI e Refatoração Frontend com TDD **100% concluídos**. Suíte Full Stack blindada (80 testes, 210 assertions). Próximos passos: Homologação Local, Preparação de Deploy ou Funcionalidades Pós-MVP.