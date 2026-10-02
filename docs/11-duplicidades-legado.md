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
`PaginationComponent1245` é usado em 25 views de listagem mais o
`ListingCardComponent` (recontado em 2026-10-01) — é o padrão a seguir, combinado com
`src/libs/Pagination.php::pages()` para montar o dado de entrada. Os dois
escapam a URL dos links de página desde 2026-10-01 (N6 do plano de correções).

## Padrão geral do sufixo numérico

Os sufixos (`4214`, `1245`, `5432`) não têm significado semântico — parecem
identificadores de uma reescrita/versão feita em algum momento, sem que a versão
antiga tivesse sido removida. **Não criar um novo component com um sufixo
numérico "porque é o padrão"** — isso é dívida técnica existente, não uma
convenção a perpetuar. Se for necessário versionar algo de propósito, alinhar
com o usuário um nome descritivo em vez de outro número aleatório.

## Redimensionamento de imagem — `Resizer.php`/WideImage removidos (N12)

O `Resizer.php` (4 funções concorrentes `resize`, `resize1`, `resize3`,
`resize4`), o `foto.class.php`, a `ImageThumb.class.php`, o `FuncaoImagem.php` e a cópia local
`src/libs/wideImage/` foram **removidos em 2026-10-02** (N12). Para upload/resize
de imagem, usar `Util::resizeImageCrop` (dimensão exata, corte central) ou
`Util::resizeImageInside` (cabe dentro, sem ampliar), ambos com Imagine e sempre
recodificando; fotos de veículo seguem em `Util::resizeImageWithCanvas`. Não
recriar variantes. Restos conhecidos: `UploadFiles.php` (legada, sem chamador,
ainda chama `resize1`/`wideImagePhoto` inexistentes); `FileUploader::uploadImg*` usa o `smottt/wideimage` do vendor (sem chamador).

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

**Atualização 2026-09-28**: `AttendanceController::disableAttendance`/
`enableAttendance` também corrigidos (passavam a chamar `ModelGenerico->disableItem($attendanceId)`
**sem** o parâmetro `$table` — mesmo bug, tabela vazia). Ver seção de migração
do Toast abaixo para o estado final completo.

## Mensagens de sucesso/aviso/erro: `BoxAlert` (removido) → `Toast` (padrão único desde 2026-09-28)

O projeto tinha **duas** implementações concorrentes de feedback pós-ação, nenhuma delas totalmente correta:

1. **`src/libs/BoxAlert.php`** — padrão antigo: controller redireciona com flag na
   querystring (`?added=true`, `?disabled=true` etc.), a view chama
   `(new BoxAlert())->defaultItemAlerts()` manualmente e ele lê `$_GET` pra decidir
   a mensagem. Usado em ~35 controllers / ~40 views. Problema: a mensagem depende
   só da flag da URL, não do resultado real da operação no banco — e cada view
   precisa lembrar de chamar `defaultItemAlerts()` (várias views de edição, ex.
   `marital-status/editMaritalStatus.php`, nunca chamavam e por isso nunca
   mostravam nada).
2. **`src/libs/Toast.php`** — padrão mais novo (SweetAlert2, toast no canto da
   tela): `Toast::successToast($msg)` / `errorToast($msg)` / `warningToast($msg)`
   grava a mensagem em `$_SESSION['RR']->toast`. Já estava em uso em 24+
   controllers/models (`Vehicles*`, `Users`, `BillsToPay*`, `BillReceive*`,
   `CustomerType`, `CheckControl`, `Branch`, `SaleRequests`, `PurchaseRequests`,
   `Customer`, `Login`, `Settings`, `Secure`) — só que **a ponte que exibia o
   toast estava completamente quebrada e nada disso aparecia pro usuário**:
   - `public/js/v_01/toast.js` (que buscava o toast da sessão via AJAX e disparava
     `Swal`) nunca era incluído em nenhuma página.
   - Mesmo se fosse incluído, chamava `url + "api/ajax/toast"` — prefixo de rota
     inválido (`src/controller/api/` não existe; o padrão correto é
     `ajax/ajax/<método>`, ver `Application.php::splitUrl`).
   - Havia uma segunda implementação incompatível do mesmo endpoint em
     `GlobalController::toast()` (formato de resposta `data.toast` em vez de
     `toast`), também nunca corretamente roteada.

→ **Decisão**: consolidar em cima do `Toast.php` existente (não criar uma
terceira classe). A ponte de exibição foi trocada por renderização **inline no
footer comum** (`Toast::render()`, chamado em
`src/view/_templates/footer.php`, depois de `renderScript()` pra garantir que o
mixin `Toast` de `toast-config.js` já esteja carregado) — sem requisição AJAX
extra, sem depender de cada view lembrar de chamar algo. `toast.js`,
`AjaxController::toast()` e `GlobalController::toast()` foram removidos (mortos
mesmo antes da mudança). `Toast.php` ganhou métodos de mensagem padrão
(`itemAdded`, `itemEdited`, `itemDisabled` etc., espelhando os casos do
`defaultItemAlerts()`) e `infoToast()`.

**Efeito colateral (esperado, positivo)**: como a renderização agora é
automática no footer comum, os 24+ controllers que já setavam
`$_SESSION['RR']->toast` mas nunca tinham a mensagem exibida **passam a
funcionar sem precisar tocar neles** — não é regressão, é a ponte finalmente
fechando o circuito que já existia pela metade.

**`src/view/login/index.php` tinha um terceiro mecanismo de alerta próprio**
(não usa `_templates/header.php`/`footer.php`, então `Toast::render()` não
alcançava essa página) — misturava flags de querystring com uma checagem manual
de `$_SESSION['RR']->toast` que ignorava o conteúdo real e sempre mostrava o
texto fixo "E-mail ou senha invalido!". **Corrigido**: `login/index.php`,
`login/changePassword.php` ganharam os assets do SweetAlert2 + `toast-config.js`
+ `Toast::render()` próprios (já que não passam pelo footer comum), e
`LoginController` foi convertido pra `Toast::` em todos os pontos
(signIn, changePassWord, sendRecoverPasswordMail). De quebra, achado e corrigido
outro bug: `LoginController::index()` sempre resetava `$_SESSION['RR'] =
(object)[]` **antes** de checar o toast pendente, apagando qualquer mensagem
recém-setada por um redirect anterior — o toast de login inválido nunca
aparecia nem no código antigo. Agora o toast pendente é preservado através do
reset.

**Status da migração: concluída em 2026-09-28.** Todos os ~35 controllers que
usavam `BoxAlert`/flags de querystring foram migrados pra `Toast::`, incluindo
a correção de "só sucesso se a operação realmente funcionou" nas ações de
disable/enable (a maioria não checava resultado nenhum). `BoxAlert.php` foi
removido, junto com o trecho de `#box-alert` em `modals.js`. De quebra, dois
bugs de lógica invertida foram encontrados e corrigidos nesse processo:
`BillsToPayController::handleCancelInstallments` e
`BillsToPay::cancelAndUpdateInstallments` mostravam "sucesso" quando a operação
falhava e vice-versa (condição `!= false ? 'success' : 'error'` invertida).

Ficou de fora, deliberadamente, código morto/inalcançável: o corpo de
`LeadController`/`LeadConfigController` (módulo desativado por guard no
construtor, ver [[lead_module_disabled]]) mantém flags antigas nos métodos que
nunca executam — só o `BoxAlert` (que era importado mas nunca chamado) foi
removido de lá. `src/view/notice/index.php` também ficou com a chamada de
alerta removida, mas continua órfã (nenhum controller a renderiza).

## Ao encontrar uma duplicidade nova não catalogada aqui

1. Não escolher no achismo — rodar `grep` para ver qual variante o módulo atual
   já usa, ou perguntar ao usuário.
2. Depois de decidir, **adicionar uma entrada nesta página** descrevendo as duas
   (ou mais) variantes e qual é a recomendada — para a próxima tarefa não
   repetir a investigação do zero.
