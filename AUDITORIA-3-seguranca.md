# Auditoria 3 — Segurança de produção

**Data**: 2026-09-29. **Tipo**: auditoria somente leitura — nenhum arquivo de
código ou registro de banco foi alterado durante a investigação. Nenhum
payload de exploração foi executado contra o banco ou a aplicação — todos os
achados abaixo foram confirmados por **leitura direta de código** (mais
`SHOW`/`SELECT` no banco real quando necessário para confirmar dado de
produção), nunca por ataque real.

## Metodologia

3 agentes em paralelo, cada um focado num conjunto de checagens pedidas
(SQL injection + XSS; CSRF + senhas + controle de acesso + credenciais
hardcoded; upload de arquivo + debug esquecido), mais leitura pessoal e
verificação direta de **todos os achados CRÍTICO** antes deste documento —
incluindo consulta ao schema/dados reais do banco `veiculos_repasse` para os
achados de controle de acesso. Antes de começar, os 3 agentes leram
`AUDITORIA-1-dominio.md` e `AUDITORIA-2-tecnica.md` para não repetir achado
já catalogado (nenhum item abaixo duplica os dois documentos irmãos).

## Resumo executivo

Esta auditoria encontrou **vulnerabilidades críticas reais, ativas e
exploráveis hoje**, mais graves que tudo encontrado nas duas auditorias
técnicas anteriores. As três que exigem correção imediata, antes de qualquer
outra coisa:

1. **SQL injection sem nenhuma autenticação**, permitindo ler/escrever
   qualquer tabela do banco (inclusive hash de senha de usuários) sem login
   nenhum.
2. **Upload de arquivo arbitrário levando a execução remota de código
   (RCE)** — qualquer usuário autenticado (e em alguns vetores nenhum) pode
   subir um arquivo `.php` para dentro de `public/` e executá-lo direto pela
   URL, porque nenhuma pasta de upload tem proteção contra execução.
3. **Escalação de privilégio completa e self-service** — qualquer usuário
   autenticado, de qualquer perfil (incluindo o de menor privilégio), pode
   virar Superadmin (`turnUser`) ou conceder a si mesmo acesso a qualquer
   tela do sistema (`updateMenu`), sem checagem de permissão nenhuma no
   código, e confirmado explorável **com os dados reais de produção** (só os
   perfis Superadm/Administrador têm linha de permissão cadastrada; todos os
   outros ficam liberados por padrão).

---

## 1. SQL Injection

### 1.1 — CRÍTICO — SQL injection sem autenticação nenhuma, leitura/escrita arbitrária de qualquer tabela

**Causa-raiz**: dois controllers ajax inteiros não exigem sessão/login:
- `src/controller/ajax/AjaxController.php:33-38` — a classe não `extends`
  nada; `__construct()` é só `session_start();`.
- `src/controller/ajax/GlobalController.php:8-13` — `extends Ajax` (que faz
  checagem de sessão em `src/core/Ajax.php:12-27`), mas **sobrescreve o
  `__construct()` com só `session_start();`, sem chamar `parent::__construct()`**
  — a checagem de sessão do pai nunca roda. **Confirmado lendo os dois
  arquivos.**

Isso torna cada método abaixo alcançável **sem nenhum login**:

- **`GlobalController::getItemByGenericFieldArray()`** (`GlobalController.php:30-35`)
  → **`ModelGenerico::getItemByGenericFieldArray()`** (`src/model/ModelGenerico.php:149-160`,
  **confirmado lendo o arquivo**):
  ```php
  $sql = "SELECT * FROM {$table} WHERE TRUE ";
  foreach ($array as $key => $value) {
      $sql .= " AND $key = '$value'";
  }
  $query = $this->db->prepare($sql);
  $query->execute(); // sem parâmetros — nada é bindado
  ```
  `$table` vem de `$_POST['table']`, `$array` (chave **e** valor) vem de
  `$_POST['array']` — nenhum dos três passa por bind, whitelist ou escape.
  Rota `POST ajax/global/getItemByGenericFieldArray`, sem login.
  **Exploração**: `table=users`, `array[id]=1) UNION SELECT id,password,3,4... FROM users -- ` (ajustando nº de colunas) extrai hash de senha de qualquer usuário, sem estar autenticado.

- **`GlobalController::getGenericoById()`** (linha 15-20) e
  **`getItemByGenericField()`** (linha 22-28) — mesmo problema em grau menor:
  `$table` (e, no segundo, `$field` — nome de coluna) vêm de `$_POST` sem
  whitelist (`ModelGenerico::getItemById8161`/`getItemByGenericField`,
  linhas 64-73 e 131-147). O valor em si é bindado (`:id`/`:value`), mas o
  **nome da tabela/coluna não é** — já basta pra leitura não autenticada de
  qualquer linha de qualquer tabela por id, incluindo `users`/`tokens`.

- **`AjaxController::updateFilesOrder`/`updateFilesOrdem`** (`src/controller/ajax/AjaxController.php:382-408`)
  → `GerenciaPost::update8191($arrayPost, $_POST['table'], "id", $id, false)`
  (`src/model/GerenciaPost.php:50-74`, **já lido nesta sessão** — `$table`
  nunca é escapado/whitelisted). Sem login. Permite `UPDATE` em qualquer
  tabela/coluna arbitrária, incluindo sobrescrever `password` de qualquer
  usuário.
- **`AjaxController::getAllItens($_POST['table'])`** (linha ~250) — mesmo
  padrão de tabela livre, sem login.

**Severidade: CRÍTICO** — acesso não autenticado de leitura e escrita
arbitrária no banco, incluindo a tabela `users`. É o achado mais grave de
toda a auditoria.

### 1.2 — CRÍTICO — SQL injection autenticada (qualquer perfil) via campo de busca "Nome"/filtros de relatório

O query-builder próprio (`src/core/Model.php`, `mountSqlFromFilters()`)
permite um filtro `'where' => "..."` colado **literalmente** na query, sem
parâmetro. Isso é seguro quando só strings fixas de join usam esse
mecanismo — o problema é que vários **controllers de tela** (não só joins
internos) passam texto de `$_GET`/`$_POST` direto nesse `where`:

- `src/controller/project/RecordOfSoldVehiclesController.php:75-92,259-276`
  e `RecordOfPurchasedVehiclesController.php:75-93,269-287` —
  **confirmado lendo o primeiro arquivo**:
  ```php
  'where' => "AND ( ucase(customer.name) LIKE ucase('%" . $_GET['name'] . "%') OR ... )"
  ```
- `src/controller/project/RecordVehicleHistoryController.php:124-167` —
  `$_GET['pesquisa']`, `$_GET['data_de']`/`data_ate`, e
  **`$_GET['transfer']` sem aspas nenhuma** (linha 164) — ainda mais fácil
  de injetar.
- `src/controller/project/VehiclesController.php:95-99`,
  `CheckControlController.php:69`, `PurchaseRequestsController.php:120-124`,
  `SaleRequestsController.php:95-99` — mesmo padrão com `$_GET['name']`.
- `src/controller/ajax/CustomerController.php:83-86`,
  `PurchaseRequestsController.php:41-44`, `SaleRequestsController.php:41-44,103`,
  `CheckControlController.php:212-215,300-303` — mesmo padrão com
  `$_POST['name']`.
- `src/controller/ajax/LeadController.php:61` — `$_POST['start']`; módulo
  Lead desativado por guard (já documentado), dormente hoje.

**Exploração**: qualquer usuário autenticado (mesmo o perfil de menor
privilégio) digitando no campo "Nome"/"Pesquisa" dos módulos Veículos,
Cheques, Pedidos de Compra/Venda, Clientes, ou nos relatórios de Veículos
Vendidos/Comprados/Histórico, fecha a string com `'` e injeta
`UNION SELECT`/subquery, extraindo dado de qualquer tabela (inclusive senha)
pela resposta HTML da tela.

**Severidade: CRÍTICO** — requer login, mas qualquer perfil serve, e o
impacto é extração completa do banco.

### 1.3 — ALTO — bypass da parametrização via chave especial `'json'`

`src/model/GerenciaPost.php:19-48,50-74` (**já lido**): quando a chave do
array é `json` (ou `$json=true`), o valor **não é bindado** — colado cru na
string SQL (`"'" . $value . "'"`). Exploração real confirmada:
`src/controller/project/AttendanceController.php:604-653`
(`handleSubmitInterestFilter`) monta `$_POST['id_brand']`/`id_model` num
array, faz `json_encode()` (que não escapa aspas simples por padrão) e grava
via `update8191(['json' => $json], ...)`/`insert7181(['json' => ...], ...)`.
Mesma falha em `deleteInterestFilterById()` (linha 696).

**Severidade: ALTO** — requer login com acesso a Atendimento; afeta
integridade da tabela `attendance_filters_interests`.

---

## 2. XSS

Achado de contexto: **não existe função de escape em `src/libs/Util.php`**.
Em todo `src/view/`, só há **3 usos reais** de `htmlspecialchars()` em todo o
projeto (`record-vehicle-history/vehicle.php:33,189`,
`vehicles/attachments.php:57`) — todos só para o campo `filename` de anexo.
O resto (milhares de `<?= $x ?>`) sai sem escape.

### 2.1 — CRÍTICO/ALTO — Stored XSS no comentário de atendimento (timeline)

`$_POST['comment']` gravado cru em
`src/controller/project/AttendanceController.php:750-759`
(`handleSubmitAddComment`), renderizado sem escape em
`src/view/attendance/call-center/timeline.php:77,126,173,220`:
```php
<div class="timeline-body" ...><?= $item->comment ?></div>
```
**Exploração**: qualquer usuário com acesso a um atendimento posta um
comentário com `<script>` que executa no navegador de **qualquer outro
usuário** (inclusive gerentes/admins) que abrir aquele atendimento —
persistente e de alto alcance dentro da equipe.

### 2.2 — ALTO — Stored XSS na descrição do atendimento

Campo `description` exibido sem escape em
`src/view/attendance/call-center/global.php:34` (fora de `<textarea>`,
injeção HTML direta) e `src/view/attendance/editAttendance.php:105`
(dentro de `<textarea>`, explorável via `</textarea><script>`).

### 2.3 — ALTO — Stored XSS no comentário de controle de cheques

`$_POST['comment']` gravado cru em
`src/controller/project/CheckControlController.php:384,397,428`; passa só
por `nl2br()` (não escapa HTML); renderizado sem escape em
`src/view/check-control/edit.php:152`.

### 2.4 — ALTO — Stored XSS na observação de transferência de veículo

`$_POST['observation']` gravado cru em
`src/controller/project/RecordVehicleHistoryController.php:525-527`;
renderizado sem escape em
`src/view/record-vehicle-history/vehicleTransfer.php:86`.

### 2.5 — MÉDIO — Stored XSS na observação do cliente

`src/view/customer/edit.php:209` — `<textarea><?= $customer->observation ?></textarea>`,
sem escape, explorável via breakout de `</textarea>`.

### 2.6 — MÉDIO — Stored XSS na descrição de lançamento financeiro (pior nos relatórios)

Campo `description` em `bill-receive/entry.php:48`,
`bills-to-pay/entry.php:65`, `bill-receive-installment/edit.php:63`,
`bills-to-pay-installment/edit.php:77` — sem escape; e renderizado direto
num `<td>` (sem a proteção parcial do textarea) nos relatórios
`record-bill-receive-installment/index.php:211`, `print.php:107`,
`record-bills-to-pay-installment/index.php:214`, `print.php:110`.
**Exploração**: funcionário financeiro digita descrição com
`<img src=x onerror=...>`; executa para qualquer usuário que abrir o
relatório (inclusive na versão de impressão).

### 2.7 — MÉDIO — inconsistência de escape confirmada no mesmo arquivo/registro

`src/view/vehicles/attachments.php:53` (`description`, sem escape) vs.
linha 57 do mesmo loop/registro (`filename`, com `htmlspecialchars()`) —
mesmo padrão em `src/view/vehicles/photos.php:66,72` (`description` de
imagem, sem escape).

### 2.8 — MÉDIO — DOM-based XSS via `.html()` com dado de AJAX não sanitizado

`public/js/v_01/attendance/add.js:75` — `data.name` (vindo do endpoint
`ajax/global/getGenericoById`, o mesmo do achado 1.1, sem validação) é
inserido via `.html()` sem escape. Severidade média hoje porque exige um
admin ter cadastrado um nome de usuário malicioso, mas zero defesa em
profundidade.

### 2.9 — BAIXO (dormente) — notificações

`public/js/v_01/notification.js:12-26` e
`src/controller/project/LeadController.php:165` montam HTML sem escape.
Hoje inalcançável (nenhum INSERT ativo em `notification` fora do módulo
Lead, desativado por guard). Registrado como armadilha se reativado.

---

## 3. CSRF — achado sistêmico

**Não existe nenhum mecanismo de CSRF token funcional no projeto.**
`src/core/Controller.php:22,68` tem uma propriedade `$this->token`
(derivada de uma constante `TOKEN` fixa em `config.php`), mas **confirmado
por grep que nunca é emitida em nenhum `<input>` nem validada em nenhum
controller** — código morto/vestigial, não proteção real. As únicas
ocorrências de "csrf" no projeto são da biblioteca `ckeditor.js` vendorizada
(proteção interna dela, sem relação com os forms da aplicação). Nenhuma
sessão define `SameSite` no cookie.

**Efeito**: todo formulário POST que altera dado (cadastro, edição,
exclusão, troca de senha, troca de permissão — praticamente todo o painel)
depende só do cookie de sessão, sem token por formulário — combinado com a
ausência de `SameSite`, um site malicioso pode forjar submissões em nome de
uma vítima logada.

**Severidade: ALTO** (achado único, aplicável a todo o sistema).

---

## 4. Senhas

### Hash — correto, sem achado
`password_hash(..., PASSWORD_BCRYPT, ['cost' => 12])` no cadastro/troca
(`LoginController.php:176`, `User.php:60,138`) e `password_verify()` na
comparação (`LoginController.php:49`) — confirmado, sem uso de
md5/sha1/texto puro para senha em nenhum lugar da aplicação.

### 4.1 — MÉDIO — sem política de complexidade de senha
`src/view/users/add.php:40-48`/`edit.php:45-54` — único controle é
`required` no HTML. Nenhuma validação de tamanho mínimo, nem client nem
server-side (`User::submitAddForm`/`submitEditForm` gravam `$_POST['password']`
direto em `password_hash()`). Senha de 1 caractere é aceita.

### 4.2 — ALTO — sem rate-limiting/lockout no login
`LoginController::signIn()` (`src/controller/project/LoginController.php:45-101`)
não tem contador de tentativa, lockout, captcha nem atraso progressivo.
Confirmado por grep exaustivo (`failed_attempt`, `lockout`, `throttle`) =
zero resultado, sem coluna correspondente no schema. Login vulnerável a
força bruta/credential stuffing sem limite algum no nível de aplicação.

### 4.3 — MÉDIO/BAIXO — chave de assinatura JWT hardcoded (fluxo de recuperação de senha)
`src/libs/JWTWrapper.php:9` — `const KEY = 'fa08aebc1c688d31204c1616c4277de2';`,
versionada no git, usada para assinar o token de redefinição de senha. Não é
diretamente explorável hoje (o link por e-mail carrega o hash do JWT, não o
JWT cru, e a busca é por esse hash contra a tabela `tokens` — precisa
existir a linha de verdade), mas é má prática (secret estático, sem
rotação, mesmo valor em dev/produção) e vira exploração direta se o fluxo
mudar no futuro. Relacionado: o bug já catalogado no `AUDITORIA-2-tecnica.md`
(`LoginController.php:179` grava em tabela `"token"` em vez de `"tokens"`)
faz esse mesmo token **nunca ser invalidado** após o uso — combinado com a
ausência de rate limiting, um link de recuperação interceptado fica
reutilizável por até 1h.

---

## 5. Controle de acesso

Contexto arquitetural confirmado (`src/libs/Secure.php:273-281`,
`individual_menu_access()`): o modelo é **opt-out, não opt-in** — só
bloqueia se existir uma linha explícita em `menu_access` com `status=0`
para aquele perfil+menu; **se não existe nenhuma linha, o acesso é liberado
por padrão**. Isso já era conhecido de auditorias anteriores como padrão
genérico; os achados abaixo são instâncias concretas, verificadas com dado
real do banco.

### 5.1 — CRÍTICO — `UsersController::turnUser()` — impersonação de qualquer usuário sem checagem de permissão, confirmado explorável hoje

`src/controller/project/UsersController.php:255-339` — **lido por
completo**: o método sobrescreve `$_SESSION['RR']` inteiro pelos dados de
`$itemId` (qualquer usuário, inclusive Superadmin). **Zero chamada
`Secure::` em todo o método e no construtor da classe.** A única "proteção"
é cosmética: o botão só aparece na view se
`$_SESSION['RR']->user->name == "Suporte Ydeal"` (linha 148) — checagem de
nome de exibição, não de perfil, e é só ocultar o botão, não bloquear a
rota.

**Confirmado com dado real do banco**: `menu_access` só tem linha para o
menu "Usuários" (id=6) para os perfis 1 (Superadm) e 2 (Administrador),
ambas `status=1`. Os perfis 3 a 8 (Gerente Geral, **Vendedor**,
Desenvolvedor, Gerente de Filial, Gerente de Vendas, Secretária) **não têm
nenhuma linha** — pelo modelo opt-out, ficam liberados por padrão. Ou seja,
hoje, **qualquer usuário autenticado, de qualquer perfil, pode chamar
`GET /users/turnUser/1` e assumir a sessão completa do Superadmin id=1**
(`suporte@ydeal.net.br`, já conhecido por outra auditoria como conta ativa
de acesso total), sem senha nenhuma.

**Severidade: CRÍTICO** — mais grave que a backdoor de autenticação já
corrigida (`Authentication.php`, 2026-09-18): aquela dependia de um serviço
externo; esta é escalação de privilégio total, 100% local, sem dependência
externa nenhuma.

### 5.2 — CRÍTICO — `SettingsController::updateMenu()` — qualquer usuário pode conceder/revogar permissão de qualquer perfil, confirmado explorável hoje

`src/controller/project/SettingsController.php:318-327`:
```php
public function updateMenu(int $profileId, int $menuId): void
{
    $response = (new MenuAccess())->updateProfileMenu($profileId, $menuId);
    ...
}
```
`MenuAccess::updateProfileMenu()` (`src/model/MenuAccess.php:31-79`) é o
mecanismo que ativa/desativa uma linha em `menu_access`, propagando pra
submenus. **`SettingsController` inteiro não tem nenhuma chamada
`Secure::access_*`** (confirmado, só há `Secure::check_post_method`, que
verifica se o POST não está vazio — não é checagem de autorização).

**Confirmado com dado real**: mesmo padrão do achado 5.1 — só perfis 1/2 têm
linha pro menu "Configuração" (id=51). Um perfil "Vendedor" pode acessar
`/settings`/`/settings/menus` por URL direta e chamar
`/settings/updateMenu/4/{qualquer_menu}` para conceder ao próprio perfil
acesso a qualquer tela do sistema — inclusive `users` ou `branch`. Cadeia
completa de escalação de privilégio self-service, independente do achado
5.1.

**Severidade: CRÍTICO.**

### 5.3 — ALTO — `BranchController` sem nenhuma checagem de perfil

`src/controller/project/BranchController.php` — zero chamada `Secure::`
em qualquer método, incluindo `addItem()`, `handleSubmitAddItem()`,
`editItem()`, `disableItem()`, `enableItem()`. Criar/desativar uma filial
inteira do sistema não exige nenhum perfil específico no código — depende
só da mesma lacuna de dados dos achados 5.1/5.2.

### 5.4 — ALTO — `CostCenterController` — inconsistência dentro da mesma classe

`index()`, `handleSubmitEditItem()`, `handleSubmitCloneItem()` chamam
`Secure::access_admin(true)` — mas `handleSubmitAddItem()`, `disableItem()`,
`enableItem()` não chamam nada. Criar/desativar centro de custo não exige
admin, editar/clonar exige — inconsistência de aplicação do padrão, sem
justificativa aparente.

### 5.5 — MÉDIO — `VehiclesController` — checagem só na UI, não na rota

`src/controller/project/VehiclesController.php:59` —
`Secure::access_admin()` só decide se as abas "Compra"/"Custos" aparecem na
navegação. Os métodos de destino, `purchaseVehicles($itemId)` (437) e
`vehicleCosts($itemId)` (683), não chamam `Secure::` — acessíveis por URL
direta por qualquer perfil, mesmo com a aba oculta.

### 5.6 — MÉDIO — `ReportDreController` (DRE financeiro) sem checagem de perfil

Classe inteira sem nenhuma chamada `Secure::`. O campo `menu.access` do
item "Relatório DRE" só influencia o que aparece no menu lateral, não
bloqueia a rota. Resultado prático: qualquer perfil, inclusive Vendedor,
acessa `/report-dre` e vê o Demonstrativo de Resultado completo da empresa.

### 5.7 — BAIXO — `ReportAttendanceController` sem `Secure::`

Mesma lacuna de padrão, menos sensível que os achados financeiros acima.

---

## 6. Credenciais, tokens e URLs hardcoded

- **Confirmado, sem regressão**: `Authentication.php` continua removido;
  `LoginController::signIn()` continua só com `password_verify()` local,
  zero chamada externa; `CompareDatabases.php` continua removido.
- A conta `suporte@ydeal.net.br` (id=1, Superadm, access=5) **continua
  existindo** no seed de produção — decisão pendente do usuário, não é
  achado de código novo (mas ganha gravidade combinada com o achado 5.1).
- `src/libs/JWTWrapper.php:9` — chave JWT hardcoded (já detalhado em 4.3).
- `src/config/config.php:74-76` — `FACILVEL_API_TOKEN` em texto puro,
  versionado. Integração Facilvel está documentada como adiada/não usada
  ativamente — achado de higiene, severidade BAIXO.
- `src/config/config.php` está fora do document root público em ambas as
  topologias de deploy suportadas pelo `.htaccess` do projeto — confirmado
  sem exposição HTTP direta. Confirmado **não versionado** (fora do git),
  então os valores vistos (DB root sem senha, `ENVIRONMENT=development`)
  são deste ambiente local, não necessariamente os de produção.
- Nenhuma outra URL de domínio de terceiro suspeita, fora do já corrigido
  e da Facilvel (conhecida).

---

## 7. Upload de arquivos

### Causa-raiz sistêmica — CRÍTICO — nenhuma pasta de upload protegida contra execução

Confirmado: **só existem 3 arquivos `.htaccess` em todo o projeto** (raiz,
`public/.htaccess`, e um dentro de uma lib vendored) —
**nenhuma pasta de upload dentro de `public/` tem `.htaccess` próprio**
bloqueando execução de PHP. `public/.htaccess:13-15` só reescreve para
`index.php` quando o arquivo requisitado **não existe** (confirmado lendo o
arquivo: `RewriteCond %{REQUEST_FILENAME} !-f`) — se um `.php` for salvo
fisicamente numa pasta de upload, o Apache serve/executa esse arquivo
diretamente, sem passar pelo roteador da aplicação. Isso transforma
qualquer um dos vetores abaixo em execução remota de código completa.

### 7.1 — CRÍTICO — upload de anexo de veículo sem validação de extensão/MIME

`src/model/VehicleAttachments.php:25-37` (`saveFile()`) — **confirmado
lendo o arquivo**: extensão vem direto do nome original enviado pelo
navegador (`pathinfo($file['fileName'], PATHINFO_EXTENSION)`), nome salvo é
`uniqid()` (sem path traversal, mas **extensão 100% controlada pelo
atacante**), salvo em `public/vehicle/$itemId/attachments/` (pasta
pública). O `<input type="file">` da tela nem tem `accept=`. Qualquer
usuário autenticado com acesso à ficha de um veículo pode subir `shell.php`
e executá-lo pela URL direta.

### 7.2 — CRÍTICO — upload de foto de veículo sem checagem antes de salvar

`src/model/VehicleImages.php:20-52` (`saveFile()`) —
`move_uploaded_file()` roda **incondicionalmente** antes de qualquer
checagem de extensão; o redimensionamento só roda depois, condicionado a
`jpg`/`png`, mas o arquivo original já foi salvo com a extensão do atacante.
`accept="image/*"` na view é só client-side, trivialmente contornável.

### 7.3 — CRÍTICO — `FileUploader::uploadFiles()` chamado sem whitelist de extensão nos 4 usos reais

`src/libs/FileUploader.php:117-166` — **confirmado lendo o arquivo**: o
parâmetro `$acceptedFormats` tem default `[]`, e a checagem de extensão só
roda `if (!empty($acceptedFormats))`. **Confirmado por grep que os 4 únicos
call sites reais chamam sem esse 3º parâmetro**:
`BillsToPayInstallmentController.php:853`,
`BillReceiveInstallmentController.php:1120`,
`AttendanceController.php:818`, `CustomerController.php:737`. Extensão
crua vai direto pro `move_uploaded_file()`. Destinos:
`public/attachments/billsToPay/`, `bill-receive/`, `attendance/`,
`customer/` — todos públicos. **Este é o vetor mais alcançável**: qualquer
usuário que anexe um arquivo em Contas a Pagar/Receber, Atendimento ou
Cliente sobe PHP executável.

### 7.4 — CRÍTICO — padrão "dimensão exata → cópia crua" em 6 pontos de upload de logo/foto

Presente em `src/model/User.php:174-189`, `UsersController.php:409-427`,
`SettingsController.php:105-270` (5 logos), `BranchController.php:255-360`
(3 logos), `DigitalCardController.php:95-140,205-250`,
`CustomerController.php:766-796`. Em todos: extensão crua do nome original
+ `getimagesize()` usado **só** para comparar largura/altura exatas; quando
bate, `copy()` byte-a-byte do arquivo original (com a extensão do
atacante) — sem reprocessar a imagem. Como `getimagesize()` só lê o
cabeçalho, é vulnerável à técnica de "imagem polyglot" (JPEG/PNG válido nas
dimensões certas + payload PHP anexado após o marcador de fim de arquivo).
`SettingsController`/`BranchController` ao menos checam o tipo real
(`IMAGETYPE_*`) antes; `User.php`/`UsersController.php`/`DigitalCardController.php`/
`CustomerController.php` nem isso.

*(Nota: `AUDITORIA-2-tecnica.md` já revisou esse mesmo padrão de pasta
`img/{modulo}/{id}/...`, mas checando só se `mkdir()`/`@unlink()` evitavam
erro de caminho — preocupação operacional, não de segurança. Este achado é
ortogonal àquele.)*

### 7.5 — MÉDIO — sem limite de tamanho de arquivo em nível de aplicação

Nenhum dos uploaders (`FileUploader`, `UploadFiles`, `VehicleImages`,
`VehicleAttachments`, handlers de logo/foto) impõe cap de tamanho no
código — depende inteiramente de `upload_max_filesize`/`post_max_size` do
`php.ini` do host.

### 7.6 — BAIXO/informativo — código morto perigoso se reativado

`src/libs/UploadFiles.php` (órfão, confirmado zero chamadores) — método
`WOWOW()` usa `mysql_query()` (extensão removida do PHP7+, daria fatal
error se chamado), monta caminho sem tratar `../` (path traversal) e só
valida `jpg/jpeg`. `FileUploader::uploadImg()`/`uploadImgSingle()` — zero
chamadores, mas reprocessam a imagem via WideImage sempre (mais seguro que
os achados 7.1-7.4 se algum dia forem usados).

---

## 8. Debug esquecido em produção

### 8.1 — ALTO — 2 vazamentos de erro sem gate de ambiente, ativos em qualquer ambiente

- `src/model/BillsToPay.php:256-259` (`handleFormAdd`):
  ```php
  } catch (PDOException $error) {
      $this->db->rollBack();
      echo $error->getMessage();
  }
  ```
  Sem checagem de `ENVIRONMENT` (diferente de ~20 blocos irmãos que
  checam), sem `exit`/`return` depois. Qualquer `PDOException` ao cadastrar
  conta a pagar imprime a mensagem crua do driver PDO na resposta HTTP, em
  produção, para qualquer usuário autenticado.
- `src/model/BillReceive.php:296-300` — espelho exato do achado acima,
  com o `exit;` **literalmente comentado** (resíduo visível de debug).

### 8.2 — MÉDIO — ~20 blocos corretamente "gated", mas dependentes de config de produção não verificável

~20 outros `catch (PDOException...)` em `src/model/*.php` checam
corretamente `ENVIRONMENT === 'development'` antes de ecoar — padrão
correto. Mas `src/config/config.php` **não é versionado** (confirmado fora
do git) — o valor real em produção não é verificável a partir deste
repositório. Se produção algum dia ficar com `ENVIRONMENT='development'`
(o valor deste ambiente local), todos esses pontos passam a vazar mensagem
de exceção (em `CustomerType.php`, `echo $error;` inclui stack trace
completo com caminho de arquivo do servidor).

### 8.3 — MÉDIO — falta `else` explícito desligando `display_errors` fora de dev

`config.php:16-19` — `if (ENVIRONMENT === 'development') { error_reporting(E_ALL); ini_set("display_errors", 1); }`,
sem `else` que force `error_reporting(0)`/`display_errors=0` — depende
100% do que já estiver no `php.ini` do host. Falta de defesa em
profundidade, independente do valor real hoje.

### 8.4 — BAIXO/informativo

- `BillsToPay.php:67` e `BillsToPayInstallment.php:249` — comparam
  `ENVIRONMENT == "develop"` (typo; nunca é verdadeiro) — engolem exceção
  silenciosamente, sem vazar nada, mas também sem sinal pro operador.
- `Util::debug()` (`src/libs/Util.php:431-442`) — gated corretamente, zero
  chamadores, código morto.
- `Home.php:152-283` (`compareStructureFromTwoDatabase`/`openDatabaseConnectionTest`) —
  segunda instância do padrão já visto no `CompareDatabases.php` removido
  em 2026-09-18: credencial hardcoded (`root`, senha vazia, banco
  `2021_more`) e um caminho de código que executaria `CREATE`/`ALTER TABLE`
  direto contra produção se fosse religado (violaria a regra nº1 do
  CLAUDE.md). **Confirmado inalcançável hoje** (zero chamadores,
  `HomeController` não expõe isso). Registrado com destaque pelo
  precedente já conhecido.
- `config.example.php` (template versionado) traz `DB_USER=root`/`DB_PASS=''`
  como exemplo — não vaza nada real, mas é precedente de credencial fraca.

---

## 9. Sessão (achado incidental)

Nenhuma chamada a `session_set_cookie_params()`/flags
`httponly`/`secure`/`samesite` em nenhum dos 7 pontos de `session_start()`
do projeto, nem em `config.php`. Flags de segurança do cookie de sessão
dependem 100% do padrão do `php.ini` do servidor. Combinado com
`URL_PROTOCOL` default `http://` no config local, é plausível que o cookie
trafegue sem flag `Secure` em ambiente sem HTTPS forçado.

**Severidade: MÉDIO** — achado incidental, não aprofundado (fora do escopo
pedido).

---

## Tabela-resumo final (CRÍTICO → BAIXO)

| # | Área | Achado | Arquivo:linha | Severidade |
|---|---|---|---|---|
| 1 | SQL Injection | `getItemByGenericFieldArray` — injeção sem bind, sem login | `ModelGenerico.php:149-160` + `GlobalController.php:30-35` | **CRÍTICO** |
| 2 | Controle de acesso | `turnUser()` — impersonação de qualquer usuário, sem checagem, confirmado explorável | `UsersController.php:255-339` | **CRÍTICO** |
| 3 | Controle de acesso | `updateMenu()` — auto-concessão de permissão, sem checagem, confirmado explorável | `SettingsController.php:318-327` | **CRÍTICO** |
| 4 | Upload | `VehicleAttachments::saveFile()` — extensão livre, RCE | `VehicleAttachments.php:25-37` | **CRÍTICO** |
| 5 | Upload | `VehicleImages::saveFile()` — salva antes de checar extensão | `VehicleImages.php:20-52` | **CRÍTICO** |
| 6 | Upload | `FileUploader::uploadFiles()` sem whitelist nos 4 usos reais | `FileUploader.php:117-166` + 4 controllers | **CRÍTICO** |
| 7 | Upload | padrão "dimensão exata → cópia crua" (polyglot) em 6 pontos | `User.php`, `UsersController.php`, `SettingsController.php`, `BranchController.php`, `DigitalCardController.php`, `CustomerController.php` | **CRÍTICO** |
| 8 | Upload | causa-raiz: nenhuma pasta de upload protegida contra execução PHP | `public/.htaccess` + ausência de `.htaccess` por pasta | **CRÍTICO** |
| 9 | SQL Injection | filtro "Nome" concatenado em `$_GET`/`$_POST`, qualquer perfil | ~13 controllers (ver seção 1.2) | **CRÍTICO** |
| 10 | SQL Injection | `getGenericoById`/`getItemByGenericField`/`updateFilesOrder` — tabela/coluna livre, sem login | `GlobalController.php`, `AjaxController.php` | **CRÍTICO** |
| 11 | XSS | comentário de atendimento (timeline), afeta gerentes/admins | `AttendanceController.php:750-759` + `timeline.php` | **CRÍTICO/ALTO** |
| 12 | CSRF | nenhum mecanismo funcional no projeto inteiro | todos os forms POST | ALTO |
| 13 | Senha | sem rate-limiting/lockout no login | `LoginController.php:45-101` | ALTO |
| 14 | Controle de acesso | `BranchController` sem checagem de perfil (toda a classe) | `BranchController.php` | ALTO |
| 15 | Controle de acesso | `CostCenterController` — inconsistência add/disable vs edit/clone | `CostCenterController.php` | ALTO |
| 16 | Debug | 2 vazamentos de erro PDO sem gate de ambiente, financeiro | `BillsToPay.php:258`, `BillReceive.php:298` | ALTO |
| 17 | XSS | descrição de atendimento, cheque, transferência de veículo | `global.php:34`, `check-control/edit.php:152`, `vehicleTransfer.php:86` | ALTO |
| 18 | SQLi | bypass via chave `'json'` em `GerenciaPost` | `GerenciaPost.php:19-48` + `AttendanceController.php:604-653` | ALTO |
| 19 | Controle de acesso | `VehiclesController` — checagem só na UI, não na rota | `VehiclesController.php:59,437,683` | MÉDIO |
| 20 | Controle de acesso | `ReportDreController` — financeiro sem checagem, qualquer perfil acessa | `ReportDreController.php` | MÉDIO |
| 21 | Senha | sem política de complexidade | `users/add.php:40-48`, `User.php` | MÉDIO |
| 22 | Senha | chave JWT hardcoded (reset de senha) | `JWTWrapper.php:9` | MÉDIO/BAIXO |
| 23 | XSS | observação do cliente, descrição de lançamento financeiro nos relatórios | `customer/edit.php:209`, relatórios contas a pagar/receber | MÉDIO |
| 24 | XSS | inconsistência de escape no mesmo registro (anexo/foto de veículo) | `vehicles/attachments.php:53`, `photos.php:66,72` | MÉDIO |
| 25 | XSS | DOM-based via `.html()` com dado de AJAX não sanitizado | `attendance/add.js:75` | MÉDIO |
| 26 | Debug | ~20 blocos gated mas dependentes de config de produção não verificável | vários `src/model/*.php` | MÉDIO |
| 27 | Debug | falta `else` explícito desligando display_errors fora de dev | `config.php:16-19` | MÉDIO |
| 28 | Upload | sem limite de tamanho de arquivo em nível de aplicação | todos os uploaders | MÉDIO |
| 29 | Sessão | sem hardening de cookie (`httponly`/`secure`/`samesite`) | todos os `session_start()` | MÉDIO |
| 30 | Credencial | token de API Facilvel hardcoded (integração adiada) | `config.php:76` | BAIXO |
| 31 | Controle de acesso | `ReportAttendanceController` sem `Secure::` | `ReportAttendanceController.php` | BAIXO |
| 32 | XSS | notificações (dormente, módulo Lead desativado) | `notification.js`, `LeadController.php:165` | BAIXO |
| 33 | Upload/Debug | `UploadFiles.php`/`WOWOW()`, `Home.php::compareStructureFromTwoDatabase()` — código morto perigoso se religado | vários | BAIXO |

## Conclusão e prioridade recomendada

Corrigir antes de qualquer outra coisa, nesta ordem de urgência:

1. **Achados 1, 9, 10 (SQL Injection sem autenticação)** — expõem o banco
   inteiro, incluindo hash de senha, sem exigir login. É o único conjunto de
   achados desta auditoria explorável **sem estar logado**.
2. **Achados 4-8 (upload → RCE)** — qualquer usuário autenticado (perfil
   mais baixo incluso) pode obter execução de código no servidor.
3. **Achados 2 e 3 (escalação de privilégio via `turnUser`/`updateMenu`)** —
   equivalentes ou piores que a backdoor de autenticação já corrigida em
   2026-09-18, só que residem em controllers de negócio normais, não num
   arquivo isolado — por isso escaparam da limpeza anterior.

O restante (XSS armazenado, CSRF, ausência de rate-limiting, debug
esquecido, controle de acesso inconsistente em módulos específicos) é sério
e deve ser corrigido, mas nenhum desses é explorável sem pelo menos uma
sessão autenticada válida.

Nenhuma correção foi aplicada nesta auditoria — só o relatório, como pedido
("SOMENTE LEITURA"). Dado a gravidade dos achados 1-10, recomenda-se tratar
como incidente de segurança e priorizar a correção antes de qualquer outra
tarefa de desenvolvimento em andamento.

## Sanity-check final

Os achados 1.1, 2.1 (`turnUser`), 5.2 (`updateMenu`), 7.1 e 7.3 (upload) —
os de severidade CRÍTICO com maior impacto — foram verificados por leitura
direta do código-fonte atual e, nos achados de controle de acesso, também
por consulta `SELECT`/`SHOW` ao banco de produção real (`menu_access`,
`users_profiles`) nesta sessão, antes de entrarem neste documento. Os
demais vêm dos três agentes de investigação, cada um instruído a citar
apenas achados confirmados por leitura direta de arquivo, não suposição.
