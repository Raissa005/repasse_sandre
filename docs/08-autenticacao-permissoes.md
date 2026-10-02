# Autenticação, sessão e controle de acesso

## Login

`LoginController` (`src/controller/project/LoginController.php`):

- `index()` — mostra o form de login (limpa `$_SESSION['RR']` antes).
- `signIn()` — autentica só localmente, com `password_verify($senha, $user->password)`
  (hash bcrypt). A chamada ao serviço externo da Ydeal
  (`Authentication::centralizedAuthentication`) foi removida em 2026-09-18 —
  permitia entrar como Superadm sem senha. **Bloqueio por tentativas (A4,
  2026-10-02)**, Model `LoginAttempt` (tabela `login_attempts`): 5 erros em até
  15 minutos bloqueiam o **e-mail digitado** (exista ou não) por 15 minutos,
  inclusive com a senha certa; login certo apaga a linha; concluir a
  recuperação de senha também. Mensagens não revelam se o usuário existe
  ("E-mail ou senha invalido!" / mensagem de bloqueio / recuperação neutra).
  Para desbloquear alguém manualmente: migration com `DELETE FROM login_attempts
  WHERE email = '...'` (ou a pessoa conclui "Esqueci a senha").
- Ao autenticar, monta `$_SESSION['RR']` (ver estrutura completa em
  `docs/02-arquitetura-fluxo.md`) e **invalida o cache de menu** da sessão
  anterior (`FilesystemAdapter->delete('menus_' . $sessionId)`).
- `logout()` — limpa sessão e cache de menu, redireciona para `login/index`.
- `recoverPassword()`/`sendRecoverPasswordMail()`/`changePassWord()`/
  `handleSubmitChangePassWord()` — fluxo de recuperação de senha via token JWT
  (`src/libs/JWTWrapper.php`, tabela `tokens`), e-mail enviado via
  `MoreMailer::enviarEmail()` (`src/libs/MoreMailer.php`, sobre PHPMailer).

## Controle de acesso (`src/libs/Secure.php`)

Classe de métodos estáticos, todos seguindo o mesmo padrão:

```php
Secure::access_generic($nivelMinimo, $redirectAutomatico = false, $controllerDeVolta = 'home');
```

- Compara `$_SESSION['RR']->profile->access` (número) com um limite. **Quanto
  menor o número, maior o privilégio** — os métodos checam `access > $limite`
  (ou seja, acesso "pior" que o limite é bloqueado).
- Métodos de nível pronto (do mais para o menos privilegiado):
  `access_dev` (≤1), `access_superAdm` (≤5), `access_admin` (≤10),
  `access_manager` (≤20), `access_secretary` (≤25), `access_seller` (≤30).
  Também: `is_seller` (`== 30` exato), `seller_manager` (`== 20` exato, "não é"),
  `secretary` (`=== "25"` — comparação de string, cuidado ao usar), `access_generic($n)`
  para limite arbitrário.
- `restricted_superAdm()`/`unrestricted_superAdm()` — checam se a filial atual é
  a "Super ADM" (`branch->current->id == 0`).
- `creator($createdByUserId)` — só permite se o usuário logado for quem criou o
  registro.
- `productsBranches($branchIds)`/`customerBranches($branchIds)`/`branch($branchId)` —
  restringem acesso a um recurso pela(s) filial(is) dona(s) dele.
- `userBranches($branches, $userId, $access)` — combina `creator`+`branch`+`access`
  num único helper (usado em telas de usuário).
- `individual_menu_access($menuId)` — checa a tabela `menu_access` (permissão por
  perfil x item de menu). Linha com `status = 0` → redireciona para todos. **Sem
  linha** → liberado só para Superadm, Administrador e Desenvolvedor
  (`access_admin()`); para os demais perfis, **negado** (C3, 2026-10-02). Chamado
  automaticamente pelo `Controller` base a cada página (ver
  `docs/02-arquitetura-fluxo.md`), exceto na rota `home` (destino do bloqueio) e
  nas ações do próprio usuário em `users` (`Controller::isOwnUserAction`: Perfil,
  cartão digital, "Retornar" do "Ver como").
- `check_post_method($rotaDeVolta)` — chamar no início de todo método
  `handleSubmit*`/POST, para garantir que veio de um submit real.

**Padrão a seguir**: em toda action que deve ser restrita, chamar o `Secure::access_*`
adequado logo no início do método (com `$location = true` para redirecionar
automaticamente), em vez de reimplementar a checagem de `$_SESSION['RR']->profile->access`
na mão. Se a permissão necessária não tiver um helper pronto que sirva, usar
`access_generic($nivel)` — só criar método novo em `Secure` se o caso for
genérico o bastante para reaproveitar em outras telas, e alinhar o nível
numérico com o usuário (não inventar um threshold).

## Menu e permissão por item (`menu`, `menu_access`)

O menu lateral é montado recursivamente em `Controller::assembleMenu()` a partir
da tabela `menu` (com `id_menu_parent`), filtrando por:
- `status = 1`.
- Tipo de filial (`type_branch`), quando aplicável.
- `menu_access` — permissão explícita por perfil (`id_profile`) x item de menu
  (`id_menu`); se não houver linha em `menu_access` para aquele perfil, o acesso
  segue a regra padrão (`access_or_status`), senão respeita o `status` da linha.

O resultado é **cacheado por sessão** (`FilesystemAdapter`, chave
`menus_<session_id>`) — qualquer mudança em `menu`/`menu_access` só aparece para
o usuário depois que o cache dele for invalidado (novo login, ou expurgo manual
do cache). Ao alterar permissão de menu via seed/migration, lembrar de avisar
que o usuário afetado precisa logar de novo (ou o cache precisa ser limpo).

**Regras de permissão de tela (C3, 2026-10-02)**:
- O menu lateral mostra um item só se o perfil tem linha `status = 1`. O
  servidor segue a regra de `individual_menu_access` acima — para Gerentes,
  Secretária e Vendedor, item sem linha ("Padrão do sistema" na tela) fica
  **bloqueado**. As permissões desses perfis são configuradas pelo Administrador
  em **Configurações → Menus** (não por seed).
- A checagem é **por rota**: `getMenuByRoute` pega o primeiro menu com aquela
  rota. Os 5 itens de Clientes usam a rota `customer` e o servidor só olha o
  primeiro (Comprador) — não dá para bloquear um deles separadamente.
- Aba Menus (`settings/menus`, `settings/updateMenu`, `ajax/Settings/*`): só
  `access_admin()`. Só é possível editar os perfis que `User::getAllUsersProfilesBellow`
  lista para quem edita (Administrador não vê Superadm nem Desenvolvedor).
  Fora o Superadm, ninguém desativa "Configurações" (ou o pai dele) do próprio perfil.
- Clicar num menu pai aplica o **mesmo** estado em todos os submenus
  (`MenuAccess::setProfileMenuStatus`).
- Algumas telas têm, além do `menu_access`, um perfil mínimo **fixo no
  controller**: Veículos Vendidos, Veículos Comprados, relatório de Pedidos de
  Venda e Usuários (`access_admin(true)`); Histórico do Veículo
  (`access_secretary(true)`). Ativá-las na tela Menus para um perfil abaixo disso
  só põe o link no menu — ao clicar, volta para a Home.
- Rotas **sem item de menu** não passam pela checagem de `menu_access` (`$this->page`
  vazio), então a regra fica no próprio controller: `countries`, `networks-site`,
  `lead` e `lead-config` exigem `access_admin(true)` no construtor (N17);
  `card-pdf`/`cardPDF` exige login e só gera o cartão do próprio usuário, exceto
  para admin; `currencies` redireciona todos para a Home (M12). Rota nova sem item
  de menu precisa da sua própria checagem.
- Filiais (7) e DRE (59) aparecem no menu de Superadm/Administrador também dentro
  de uma filial (`assembleMenu`).
- "Ver como este usuário" (`users/turnUser`): só Superadm. A sessão do usuário visto
  guarda `turnBack` e `turnBackId` (quem iniciou); durante o "Ver como", só é
  aceito `users/turnUser/{turnBackId}` (Retornar).
- Tipo de usuário (cadastro/edição em `UsersController`, N14): o servidor só aceita
  os perfis que a tela oferece; ninguém altera o próprio perfil, exceto Superadm;
  quem não é Superadm não edita, ativa ou desativa usuários Superadm/Desenvolvedor.

## Toast (feedback pós-ação)

Padrão de mensagem "flash" após um redirect:

```php
$_SESSION['RR']->toast = (object)['icon' => 'success'|'error'|'warning', 'title' => 'mensagem'];
redirect($rota); // ou header('location: ...'); exit;
```

O toast é impresso pelo `Toast::render()` no `footer.php` da próxima página, que
já limpa o toast da sessão depois de ler (a antiga chamada `ajax/global/toast` do
`script.js` não existia no servidor e foi removida em 2026-10-02). Existe também
`src/libs/Toast.php` (`Toast::successToast`/`errorToast`/`warningToast`/
`genericToast`/`checkResponse`) como helper para montar esse objeto sem repetir
a estrutura na mão — preferir esses helpers a montar `(object)['icon'=>...]`
manualmente em código novo.
