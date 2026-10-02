# Arquitetura e fluxo de requisição

Framework caseiro no estilo "MVC minimalista" (nomenclatura interna lembra o
projeto "MINI"). Sem ORM, sem service container, sem middleware — tudo explícito.

## Entry point

`public/index.php` → `.htaccess` de `public/` reescreve **toda** URL para
`index.php?url=<resto-da-url>` (exceto arquivos/pastas reais). O `.htaccess` da
raiz do projeto apenas redireciona para `/public` (fallback caso o vhost não
aponte direto para `public/`).

`public/index.php` faz o bootstrap:
1. Define timezone `America/Sao_Paulo` e locale `pt_BR`.
2. Define constantes `ROOT`, `APP` (`= ROOT/src/`), versões de assets
   (`PLUGINSVERSION`, `JSVERSION`, `CSSVERSION`, hoje todas `v_01`).
3. Carrega `vendor/autoload.php` (Composer, autoload PSR-4 `RR\ → src/`).
4. Carrega `src/config/config.php` (não versionado — copiado de
   `config.example.php`, ver `docs/12-ambiente-configuracao.md`).
5. Instancia `RR\core\Application`.

## Roteamento (`src/core/Application.php`)

A URL é lida de `$_GET['url']` (vinda do `.htaccess`) e splitada por `/`:

```
/<controller>/<action>/<param1>/<param2>/...
```

- Se o **primeiro segmento** for `ajax`, `api` ou `app`, ele é tratado como
  "pasta" de controller (`$this->folder`) e removido do resto do parse. Sem esse
  prefixo, a pasta padrão é `project`.
- Segmentos com hífen são convertidos para PascalCase: `bill-receive` → controller
  `BillReceiveController`. O mesmo vale para a action.
- A classe resolvida é `RR\controller\{pasta}\{Controller}Controller`, dentro de
  `src/controller/{pasta}/{Controller}Controller.php`.
- Sem controller na URL → chama `HomeController::index()` da pasta resolvida.
- Sem action → chama `index()` do controller.
- Com action e parâmetros → `call_user_func_array([$controller, $action], $params)`.
- Controller inexistente → redireciona para `error` (ver `ErrorController`).
- **O 2º segmento é sempre o nome do método.** Link `rota/{id}` (2 segmentos)
  tenta chamar um método chamado `45` → erro fatal (A2). Para passar só um id ao
  `index()`, usar `rota/index/{id}` e receber como parâmetro (`index($itemId = null)`),
  não por `$_GET['pg1']` (que nesse caso vale `index`). O nome do controller vem do
  1º segmento com `ucfirst`: `cardPDF` → `CardPDFController`; `card-pdf` viraria
  `CardPdfController`, que só é achado em disco sem diferenciar maiúsculas (macOS).

Isso significa: **para criar uma rota nova, basta criar o método público
correspondente no Controller certo** — não existe arquivo de rotas central para
editar.

## As 4 classes-base do core (`src/core/`)

### `Application.php`
Só faz o roteamento acima (ver classe `Application`).

### `Controller.php` — base de todo controller de **painel** (`project`)
Chamado no `__construct($route)` de cada controller de página. Responsabilidades:
- Abre sessão (`session_start()`).
- Exige `$_SESSION['RR']->branch->current->id` setado — senão redireciona para
  `login/logout`. Ou seja, **todo controller de painel exige login**.
- Valida a sessão (`User::checkSession()`).
- Monta e cacheia o menu lateral (recursivo, filtrado por permissão) usando
  `Symfony\Component\Cache\Adapter\FilesystemAdapter`, chave `menus_<session_id>`.
  **O cache do menu precisa ser invalidado manualmente** (`$cache->delete('menus_'
  . $sessionId)`) sempre que permissões/menus mudarem — ver como `LoginController`
  faz isso no login/logout.
- Carrega config do sistema (`system_config`, `SettingsSite`) e resolve logos por
  filial.
- Bloqueia acesso se `system_config.maintenance == 1` (exceto dev e rota `home`).
- Monta `$_GET['pg']`, `$_GET['pg1']`, `$_GET['pg2']`... a partir dos segmentos da
  URL — usado nas views para montar breadcrumbs/links ativos (ver uso de
  `$_GET['pg1']` em `BranchController`).

`FrontController` (em `src/controller/project/FrontController.php`) estende
`Controller` e é a classe que **todo controller de página realmente estende**.
Ela só adiciona os métodos de gerenciamento de assets (`addStyle`/`addScript`/
`renderStyle`/`renderScript`) e já registra os assets padrão do painel (jQuery,
Bootstrap, AdminLTE, Select2, SweetAlert2, máscaras). Ver
`docs/06-frontend-assets.md`.

### `Model.php` — base de todo Model
PDO puro, `FETCH_OBJ` como modo padrão. Tem um **query builder simples baseado
em arrays/objetos** (não é um ORM):
- `mountSqlFromColumns($columns)` — monta `SELECT` a partir de uma lista de
  objetos `{table, columns}`.
- `mountSqlFromJoins($joins, $link_tables)` — monta os `JOIN`s declarados no
  construtor do Model, só incluindo os que são referenciados pelas colunas/filtros
  pedidos (evita join desnecessário). Suporta join que depende de outro join
  (`$join->require`).
- `mountSqlFromFilters($filters)` — monta `WHERE` a partir de objetos
  `{table, columns: {coluna: {comparison, value}}, where}`. Comparações
  suportadas: `EQUAL`/`=`/`IN`, `NOT_EQUAL`/`!=`/`NOT_IN`, `LIKE`/`%`, `R%`
  (prefixo), `L%` (sufixo), `NOT_LIKE`/variações, `>`, `<`, `>=`, `<=`,
  `BETWEEN` (usa `value1`/`value2`).
- Métodos genéricos prontos: `getWithFiltersAllItems`, `getItemWithFilters`,
  `getItemById`, `insert`, `update`, `delete`, `enableItem`, `disableItem`.

Todo Model de tabela (`src/model/*.php`) estende `Model` e passa `$table` e
`$joins` no `parent::__construct()`. Ver detalhes e exemplos em
`docs/04-models.md`.

### `Ajax.php` — base de todo controller **AJAX** (pasta `ajax`)
- Também exige sessão válida (`$_SESSION['RR']->user->id`), mas responde com
  JSON (`{error, message, data}`) em vez de redirect — é chamado via
  `fetch`/`$.ajax` do front-end, não navegação de página.
- Normaliza `$_POST['filters']`/`$_POST['columns']` (arrays associativos vindos
  do JS) em objetos, prontos para passar direto para os métodos do `Model` base
  (`getWithFiltersAllItems` etc.).

## Sessão da aplicação

Tudo fica dentro de `$_SESSION['RR']` (objeto), montado no login
(`LoginController::signIn`):

```
$_SESSION['RR'] = {
  user: { id, name, email, profileURL },
  profile: { id, name, access },
  branch: { current: { id, name }, all: [...] },
  setting: { sidebar, darkMode },
  cache: { id: session_id() },
  toast: { icon, title }  // setado sob demanda para exibir 1 toast na próxima página
}
```

`toast` é o mecanismo padrão de feedback pós-redirect (flash message) — setar
`$_SESSION['RR']->toast` antes de um `redirect()`, a view lê e limpa. Ver
`GlobalController::toast()` (endpoint AJAX que a página consulta) e
`docs/08-autenticacao-permissoes.md`.

## Padrão de resposta de operação (Model)

Métodos de escrita do Model (`insert`, `update`, e a maioria dos métodos de
submit customizados) retornam um objeto padrão:

```php
(object)['error' => bool, 'message' => string, /* + campos extras como lastId, item */]
```

Controllers usam isso para decidir o toast e o redirect. **Seguir esse contrato**
ao criar novo método de escrita — não retornar `true`/`false` cru nem lançar
exceção não tratada para o controller.
