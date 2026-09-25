# Duplicidades e código legado conhecido

Este projeto tem **duplicidades reais e ativas** (não é só código morto) —
mais de uma implementação para o mesmo problema convivendo no mesmo módulo, às
vezes as duas ainda em uso simultâneo. Antes de reaproveitar algo com nome
parecido a outro, ou de criar algo novo, **checar esta lista primeiro** e, se o
caso não estiver aqui, rodar `grep -r "new NomeDaClasse(" src/view` (ou
equivalente) para ver qual variante as telas vizinhas do mesmo módulo já usam —
nunca escolher no achismo.

## Components de tabela (`src/components/`)

| Classe | Usos em `src/view` | Formato de dado esperado |
|---|---|---|
| `TableComponent` | **0** | `$table->thead->tr->th[]` (estrutura aninhada, `th->link`) |
| `TableComponent5432` | **28** (padrão dominante) | `$table->thead[]` (array plano, `th->column->link`/`th->column->type`) |
| `src/libs/TableDefault.php` | terceira implementação, com método `model01()` — confirmar uso antes de escolher |

→ **Usar `TableComponent5432`** para tabela nova (ver formato completo em
`docs/05-views-componentes.md`). `TableComponent` (sem sufixo) parece não ter
nenhum uso ativo nas views atuais — não estender nem copiar dele sem confirmar
antes com `grep`, pois pode já estar morto.

## `ContentHeaderComponent` vs `ContentHeaderComponent4214`

Diferente do par de tabela, **os dois estão em uso real**: sem sufixo aparece em
24 views, `4214` aparece em 78. Não dá para presumir qual usar só pelo número de
ocorrências — **abrir os dois arquivos e comparar a assinatura esperada**, e
seguir o que as views do módulo específico que está sendo tocado já usam. Se a
tarefa for tela nova sem módulo "irmão" óbvio, perguntar ao usuário qual dos
dois é o atual antes de escolher.

## `PaginationComponent` vs `PaginationComponent1245`

`PaginationComponent` (sem sufixo) não tem uso encontrado nas views atuais;
`PaginationComponent1245` tem 37 usos — é o padrão a seguir, combinado com
`src/libs/Pagination.php::pages()` para montar o dado de entrada.

## Padrão geral do sufixo numérico

Os sufixos (`4214`, `1245`, `5432`) não têm significado semântico — parecem
identificadores de uma reescrita/versão feita em algum momento, sem que a versão
antiga tivesse sido removida. **Não criar um novo component com um sufixo
numérico "porque é o padrão"** — isso é dívida técnica existente, não uma
convenção a perpetuar. Se for necessário versionar algo de propósito, alinhar
com o usuário um nome descritivo em vez de outro número aleatório.

## `Resizer.php` — 4 funções de resize concorrentes

`resize`, `resize1`, `resize3`, `resize4` (não existe `resize2` — sinal de que
uma versão foi removida no meio do caminho). Antes de usar qualquer uma, `grep`
por qual é chamada no controller do módulo que está sendo tocado — não presumir
que a de número mais alto é "a mais nova e correta". Para upload/resize de
imagem novo, considerar também `src/libs/FuncaoImagem.php::wideImagePhoto()`
(usado no fluxo mais recente observado, ver `BranchController::handleSubmitImages`)
e `src/libs/FileUploader.php` (API genérica mais nova) antes de escolher entre as
4 variantes do `Resizer`.

## `Model` base vs `ModelGenerico` vs `GerenciaPost`

Não são bem "duplicidade" — são camadas com propósitos diferentes (ver
`docs/04-models.md`), mas é fácil confundir qual usar. Regra prática:
- Precisa de query com join/filtro complexo amarrado a uma entidade → método
  específico no Model da entidade, ou `getWithFiltersAllItems` da base `Model`.
- Precisa só de um `SELECT * FROM tabela WHERE id = ?` genérico numa tela
  auxiliar/AJAX → `ModelGenerico`.
- Precisa só de um `INSERT`/`UPDATE` genérico numa tabela sem Model dedicado (ou
  Model dedicado sem esse método) → `GerenciaPost` (`insert7181`/`update8191`,
  atenção ao parâmetro `$up` — ver `docs/04-models.md`).

## Métodos marcados `/**Descontinuar */`

Comentário encontrado acima de métodos legados dentro de alguns Models (ex.:
`Branch::getAndFilterAllBranch`, `Branch::getAllBranch`). Significa: não usar em
código novo, existe substituto mais recente no mesmo Model. Ao encontrar esse
comentário em qualquer Model, seguir a mesma regra — achar e usar o substituto,
não copiar o método marcado.

## Upload de arquivo: 2 APIs

`src/libs/UploadFiles.php` (mais antiga; contém `WOWOW()`, claramente resquício
de teste — nunca usar) vs `src/libs/FileUploader.php` (mais nova/genérica). Ver
`docs/09-bibliotecas-libs.md`.

## `ModelGenerico::disableItem`/`enableItem` vs `disableItem2`/`enableItem2` (bug real, corrigido em 2026-09-25)

`ModelGenerico` tinha `disableItem`/`enableItem` **comentados** e só deixava
`disableItem2`/`enableItem2` ativos (coluna `ativo` em vez de `status`). Como
`ModelGenerico extends Model`, e `src/core/Model.php` também define
`disableItem($id)`/`enableItem($id)` (sem parâmetro `$table`, usa
`$this->table` da própria instância), qualquer controller que chamava
`(new ModelGenerico())->disableItem($id, $this->table)` na verdade caía no
método da classe-pai `Model`, que ignorava o `$table` passado e rodava
`UPDATE {$this->table} ...` com `$this->table` **vazio** (`ModelGenerico` seta
`''` no construtor). Em `ENVIRONMENT = development` o PDO está com
`ERRMODE_WARNING`, então o UPDATE inválido falhava **silenciosamente** — a tela
redirecionava com mensagem de sucesso, mas nada era alterado no banco. Afetava
`BanksController`, `CountriesController`, `CommunicationChannelsController`,
`MaritalStatusController`, `CustomerController`, `ProfessionsController` e
`UsersController` (todos chamam `disableItem`/`enableItem` de `ModelGenerico`
passando `$this->table`).

→ **Corrigido**: `disableItem`/`enableItem` de `ModelGenerico` foram
restaurados (não estão mais comentados), usando a coluna `status` — igual ao
`Model::disableItem`/`enableItem` da base, mas respeitando o `$table` recebido
por parâmetro. Confirmado por `SHOW COLUMNS` que todas as tabelas acima usam
`status`, não `ativo`.

`disableItem2`/`enableItem2` (coluna `ativo`) continuam existindo — usados por
`NetworksSiteController` (tabela `networks_site`, que não existe no banco atual
de dev — possível resíduo do domínio imobiliário, não investigado nesta
correção). Não usar `disableItem2`/`enableItem2` para tabela nova sem antes
confirmar via `SHOW COLUMNS` se ela tem coluna `ativo` em vez de `status`.

**Pendência não corrigida**: `AttendanceController::disableAttendance`/
`enableAttendance` chamam `ModelGenerico->disableItem($attendanceId)` **sem**
o parâmetro `$table` — continuam quebrados do mesmo jeito (tabela vazia).
Fora do escopo desta correção; se for mexer em `AttendanceController`, tratar
esse caso também.

## Ao encontrar uma duplicidade nova não catalogada aqui

1. Não escolher no achismo — rodar `grep` para ver qual variante o módulo atual
   já usa, ou perguntar ao usuário.
2. Depois de decidir, **adicionar uma entrada nesta página** descrevendo as duas
   (ou mais) variantes e qual é a recomendada — para a próxima tarefa não
   repetir a investigação do zero.
