# Padrão de Controllers

Pré-requisito: ler `docs/02-arquitetura-fluxo.md` (roteamento e classes-base).

## Duas famílias de controller

| | `src/controller/project/` | `src/controller/ajax/` |
|---|---|---|
| Namespace | `RR\controller\project` | `RR\Controller\ajax` (atenção: `C` maiúsculo aqui, inconsistente — copiar o `use` de um controller ajax existente em vez de digitar de cabeça) |
| Extende | `FrontController` | `RR\core\Ajax` |
| Resposta | HTML (`require` de views) ou `header('location: ...')` | `echo json_encode([...]); exit;` |
| Uso típico | navegação de página inteira, formulários tradicionais (`<form method=POST>`) | chamadas `fetch`/`$.ajax` de dentro de uma página já carregada (autocomplete, modais, grids dinâmicas, toggles) |
| Prefixo de URL | nenhum (`/branch/...`) | `ajax/` (`/ajax/branch/...`) |

Ao decidir onde colocar uma nova ação: se ela renderiza uma página nova/faz
redirect de navegação, vai em `project`. Se é chamada via JS sem sair da página
atual, vai em `ajax`.

## Anatomia de um controller de `project` (exemplo real: `BranchController`)

```php
class BranchController extends FrontController
{
    public $route;   // nome da rota (bate com o nome do controller em kebab-case)
    public $dir;     // pasta de views correspondente em src/view/<dir>/
    private $model;  // instância do Model principal da entidade
    private $table;  // nome da tabela (usado com GerenciaPost/ModelGenerico)

    public function __construct()
    {
        $this->route = 'branch';
        $this->dir = 'branch';
        $this->model = new Branch();
        $this->table = 'branch';
        parent::__construct($this->route); // dispara toda a lógica de Controller::__construct
        $this->alert = new BoxAlert();
    }
    ...
}
```

Convenções de nome de método observadas em praticamente todos os controllers de
`project` — **seguir esses nomes ao criar um CRUD novo**, não inventar variação:

| Ação | Método GET (mostra página) | Método POST (processa) |
|---|---|---|
| Listar | `index()` | — |
| Criar | `addItem()` | `handleSubmitAddItem()` |
| Editar | `editItem($itemId)` | `handleSubmitEditItem($itemId)` |
| Ativar/Inativar | — | `enableItem($itemId, $page)` / `disableItem($itemId, $page)` |
| Sub-abas de edição (ex.: imagens, posição, arranjo de pagamento) | `<nomeDaAba>($itemId)` | `handleSubmit<NomeDaAba>($itemId)` |

Todo método que processa `POST` deve começar validando o método com
`Secure::check_post_method($rotaDeVolta)` (ver `src/libs/Secure.php`) — isso
evita processar a página se o form não foi de fato submetido, e já cuida do toast
de erro genérico.

### Padrão de renderização de página

```php
require APP . 'view/_templates/header.php';
require APP . 'view/' . $this->dir . '/index.php'; // ou add.php, edit.php, etc.
require APP . 'view/_templates/footer.php';
```

Variáveis que a view vai usar (`$contentHeader`, `$table`, `$navTabs`, etc.) são
montadas **no controller antes do require**, como variáveis locais soltas — a
view herda o escopo local do `require`. Não existe passagem explícita de dados
tipo `view('x', $data)`.

### Abas de edição (`navTabs`)

Entidades com múltiplas sub-páginas de edição (dados gerais, imagens, posição...)
têm um método privado `navTabs($itemId, $active)` no controller que monta o array
de abas, e uma view `menu.php` (ex.: `src/view/branch/menu.php`) que renderiza
essas abas. Reaproveitar esse padrão para qualquer entidade nova com múltiplas
sub-telas — não inventar um menu de abas diferente.

### Upload de imagem/arquivo

Ver `BranchController::handleSubmitImages` como referência do padrão manual mais
comum (ainda existente em vários lugares, embora `src/libs/FileUploader.php`
seja a versão mais nova/genérica — checar `docs/11-duplicidades-legado.md` antes
de escolher qual usar):
0. Valida tamanho e **tipo** antes de qualquer gravação
   (`FileUploader::sizeLimitError` + `FileUploader::typeError`) e pega a extensão
   de `FileUploader::allowedExtension` — nunca do nome do arquivo
   (ver `docs/09-bibliotecas-libs.md`).
1. Cria a pasta de destino se não existir (`mkdir(..., 0777, true)`).
2. Calcula o novo contador de versão (`logo_menu_cont + 1`), usado no nome do
   arquivo para invalidar cache do navegador.
3. Redimensiona via `wideImagePhoto()` (`src/libs/wideImage/wide.php`, biblioteca
   WideImage vendorizada) se o tamanho não bater com o esperado; senão só copia.
4. **Só depois que o arquivo novo existe**: remove o antigo (`@unlink`) e persiste
   os metadados (`*_capa`, `*_cont`, `*_ext`) via `GerenciaPost::update8191`.
   Gravar o banco antes deixava o registro apontando para arquivo inexistente.

### Transação

Escritas que afetam mais de uma tabela usam `$this->db->beginTransaction()` /
`commit()` / `rollBack()` **dentro do Model**, não no Controller (ver
`Branch::submitInsertForm`, `Branch::handleFormPosition`). O Controller só chama
o método do Model e trata o `(object)['error', 'message']` de retorno.

## Anatomia de um controller `ajax` (exemplo real: `GlobalController`)

```php
class GlobalController extends Ajax
{
    public function __construct() { session_start(); }

    public function getGenericoById()
    {
        $data = (new ModelGenerico())->getItemById8161($_POST['id'], $_POST['table']);
        echo json_encode(['error' => $this->error, 'message' => $this->message, 'data' => $data]);
        exit;
    }
}
```

Sempre responder `json_encode(['error' => ..., 'message' => ..., 'data' => ...])`
e terminar com `exit`. Reaproveitar `$this->error`/`$this->message` (vêm da
classe base `Ajax`) em vez de criar variáveis novas.

## Antes de criar um controller/ação nova

1. Procurar um controller de entidade parecida (mesma "forma" de CRUD) e copiar a
   estrutura de nomes de método — não criar um padrão de nomenclatura novo.
2. Checar se a operação já existe em `GlobalController`/`ModelGenerico` (busca
   genérica por id/campo) antes de escrever uma query nova só para isso.
3. Se a tela tem listagem com filtro/paginação, seguir o padrão de `index()` do
   `BranchController` (mesmo componente de tabela, mesmo bloco de "Filtros"
   colapsável) — ver `docs/05-views-componentes.md`.
