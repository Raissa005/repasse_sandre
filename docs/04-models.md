# Padrão de Models

Pré-requisito: ler `docs/02-arquitetura-fluxo.md` (classe base `Model`).

## Três "camadas" de acesso a dados que convivem no projeto

1. **`RR\core\Model`** — classe base abstrata que todo Model de entidade estende.
   Dá o query-builder genérico (`getWithFiltersAllItems`, `getItemWithFilters`,
   `getItemById`, `insert`, `update`, `delete`, `enableItem`, `disableItem`) e a
   conexão PDO (`$this->db`).
2. **`RR\model\ModelGenerico`** — helpers genéricos por **nome de tabela passado
   como parâmetro** (não amarrado a uma tabela fixa), para operações simples de
   telas auxiliares e consultas ad-hoc: `getItemById8161($id, $table)`,
   `getAllItens($table)`, `getItemByGenericField($value, $table, $field, $status)`,
   `getItemByName($name, $table)`, `getItemByGenericFieldArray($array, $table)`,
   `disableItem2($id, $table)`/`enableItem2($id, $table)` (usam coluna `ativo`,
   não `status` — só usar se a tabela realmente tiver `ativo`), `pagination()`,
   `getItens($qtd, $pagina, $filters, $table)`.
   Usado principalmente pelos controllers `ajax/GlobalController` e por telas que
   fazem lookup simples de tabelas auxiliares.
3. **`RR\model\GerenciaPost`** — helpers genéricos de **escrita** por nome de
   tabela: `insert7181($arrayPost, $table, $return = null, $up = false, $json = false)`
   e `update8191($arrayPost, $table, $where_col, $where_val, $up = true, $json = false)`.
   **Atenção a um comportamento não óbvio**: por padrão (`$up = true`), esses dois
   métodos colocam os valores em **UPPERCASE** (`mb_strtoupper`) antes de salvar,
   exceto colunas `password`/`icon`/`json`. Passar `$up = false` quando o valor
   não deve virar maiúsculo (é o que a maioria dos controllers faz hoje — ver
   `BranchController::handleSubmitEditItem`). **Não presumir o valor padrão**:
   sempre passar `$up` explicitamente e verificar o efeito antes de usar em campo
   novo.

Os nomes `8161`, `8191`, `getItemById8161` etc. são sufixos arbitrários (não
seguem uma convenção semântica) — **não inventar variações do tipo `8162`**;
esses três métodos específicos são os que existem e devem ser reaproveitados.

## Model de entidade (o padrão a seguir para tabela nova)

Um Model típico (`src/model/Branch.php` é a referência mais completa):

```php
class Branch extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'branch';
        $joins = [
            (object)[
                'table' => 'user_branches',
                'join' => 'inner', // opcional, default LEFT
                'where' => "{$this->table}.id = user_branches.id_branch",
                // 'require' => 'outra_tabela', // opcional: só inclui esse join se 'outra_tabela' também for pedida
            ],
        ];
        parent::__construct($this->table, $joins);
    }
    // métodos específicos da entidade aqui
}
```

- **1 classe de Model por tabela principal**, em `src/model/<NomeSingularOuDaTabela>.php`,
  namespace `RR\model`.
- Os `$joins` declarados no construtor **não são aplicados sempre** — só entram
  na query se `mountSqlFromColumns`/`mountSqlFromFilters` referenciarem a tabela
  do join (ver `Model::mountSqlFromJoins`). Isso permite reaproveitar o mesmo
  Model para queries simples (sem join) e queries com relação, sem duplicar
  código.
- Método de listagem com filtro/paginação: `getAndFilterAllItems($filters, $options)`.
  Nem todo Model tem exatamente esse nome (só 4 no projeto todo o implementam
  hoje) — **verificar antes de assumir que existe**; se não existir, ou usar
  `getWithFiltersAllItems` da base (`Model`), ou criar seguindo o mesmo formato
  de parâmetros/retorno (`{data, count}`).
- Método de busca por id **com join(s) já resolvido(s)** costuma se chamar
  `getItemById8161($id)` **dentro do próprio Model da entidade** (não confundir
  com o `ModelGenerico::getItemById8161($id, $table)`, que é genérico e sem join).
  Ver 21 ocorrências no projeto — é o padrão dominante para "pegar 1 registro
  pronto para exibir/editar, já com nome de cidade/relacionamentos etc.".
- Métodos de escrita compostos (que tocam mais de uma tabela) ficam no Model,
  com transação (`beginTransaction`/`commit`/`rollBack`) e retornam o contrato
  `(object)['error' => bool, 'message' => string, ...]` — ver
  `Branch::submitInsertForm`, `Branch::handleFormPosition`,
  `Branch::submitPaymentOfSales`.

## Comentário `/**Descontinuar */`

Alguns métodos antigos (ex.: `Branch::getAndFilterAllBranch`,
`Branch::getAllBranch`) estão marcados com o comentário `/**Descontinuar */`
logo acima da assinatura. Isso significa: **método legado, mantido só para não
quebrar chamadas existentes, não usar em código novo** — usar o substituto mais
recente do mesmo Model (normalmente `getAndFilterAllItems`/`getItemById8161`).
Antes de reaproveitar um método de Model, `grep` por `Descontinuar` acima dele.
Ver também `docs/11-duplicidades-legado.md`.

## Como montar filtros/colunas/joins para os métodos genéricos da base `Model`

```php
(new Branch())->getWithFiltersAllItems(
    // filtros
    [
        (object)['columns' => [
            'id_profile' => (object)['comparison' => 'NOT_IN', 'value' => 5],
            'status'     => (object)['comparison' => 'EQUAL', 'value' => 1],
        ]],
        (object)['table' => 'user_branches', 'columns' => [
            'id_branch' => (object)['comparison' => 'EQUAL', 'value' => $itemId],
        ]],
    ],
    // colunas (opcional; default = "tabela.*")
    [
        (object)['columns' => ['*']],
        (object)['table' => 'users_profiles', 'columns' => ['name']],
    ],
    // options (opcional) — limit/page passam por (int) no Model (LIMIT/OFFSET não têm bind)
    ['orderBy' => 'users_profiles.access ASC', 'limit' => 20, 'page' => 1]
);
// retorna (object)['data' => [...], 'count' => int]
```

Comparações disponíveis: `EQUAL`/`=`/`IN`, `NOT_EQUAL`/`!=`/`NOT_IN`, `LIKE`/`%`,
`R%` (`LIKE 'x%'`), `L%` (`LIKE '%x'`), `NOT_LIKE`, `NOT_RLIKE`, `NOT_LLIKE`,
`>`, `<`, `>=`, `<=`, `BETWEEN` (usa `value1`/`value2` em vez de `value`).

Limitações do `columns`: as condições são sempre ligadas por `AND`, e o
placeholder é `:{tabela}_{coluna}` — duas condições na mesma coluna colidem.
Para busca "contém" em várias colunas com `OR` (ou `>=`/`<=` na mesma coluna),
use um filtro `'where'` com placeholders e passe os valores em `'parameters'`
(o Model faz o bind junto com os demais). **Nunca concatene `$_GET`/`$_POST`
dentro do `'where'`.** Use nomes de placeholder distintos por ocorrência:

```php
$busca = '%' . $_GET['name'] . '%';
$filters[] = (object)[
    'where' => "AND (ucase(this->table.name) LIKE ucase(:busca_1)
                OR ucase(vehicles.plate) LIKE ucase(:busca_2))",
    'parameters' => [':busca_1' => $busca, ':busca_2' => $busca],
];
```
Exemplos reais: `VehiclesController::index`, `RecordVehicleHistoryController::index`.

## Antes de criar um Model/método novo

1. Ver se a operação já existe em `ModelGenerico`/`GerenciaPost` (caso genérico) —
   evita criar Model só para 1-2 queries triviais.
2. Ver se o Model da entidade já tem um método equivalente (procurar por nome de
   coluna/tabela com `grep`) antes de escrever SQL novo.
3. Se for criar SQL manual (fora do query-builder), seguir o estilo dos métodos
   existentes: heredoc/string com quebras de linha, parâmetros nomeados (`:nome`),
   nunca concatenar valor de usuário direto na string (SQL injection).
4. Se a tarefa envolver alterar/criar tabela, **não mexer no schema** — gerar
   migration em `db/migrations/`. Ver `docs/07-banco-de-dados.md`.
