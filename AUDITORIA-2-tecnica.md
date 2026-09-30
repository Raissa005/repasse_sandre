# Auditoria 2 — Técnica (sintaxe, includes, rotas, SQL, variáveis)

**Data**: 2026-09-28/29. **Tipo**: auditoria somente leitura — nenhum arquivo
de código ou registro de banco foi alterado durante a investigação.

## Metodologia

`php -l` direto em todos os arquivos (item 1), mais 3 agentes de exploração em
paralelo para os itens 2-5 (includes/classes/funções, rotas/links/forms, SQL
vs schema real + variáveis/caminhos). Cada achado de severidade CRÍTICO/ALTO
listado abaixo foi **reverificado pessoalmente** (leitura direta do arquivo
citado) antes de entrar neste documento — não é relato cego de agente.

---

## 1. Sintaxe (`php -l`) — CONCLUÍDO

`php -l` rodado nos 729 arquivos `.php` do projeto (fora `vendor/`).
**Resultado: zero erros de sintaxe.**

---

## 2. Includes/classes/funções inexistentes — CONCLUÍDO

Cobertura: ~500 `require`/`include` (todos os 48 controllers com padrão
dinâmico `require APP . 'view/' . $this->dir . '/xxx.php'` cross-referenciados
contra `src/view/*` real), 112 imports `use RR\...` únicos, toda instanciação
`new NomeClasse(...)`, todo `extends`/`implements`, e mais de 2.500 chamadas
de método (`$this->model->metodo()`, `(new X())->metodo()`) verificadas
contra os métodos reais de cada classe.

### Achado — BAIXO — `src/model/ManagerPost.php` — namespace/`use` com case errado

- Linha 3: `namespace RR\Model;` (deveria ser `RR\model`, pasta real
  `src/model` em minúsculo).
- Linha 5: `use RR\Core\Model;` (deveria ser `RR\core\Model`, pasta real
  `src/core` em minúsculo).
- **Por que importa**: em filesystem case-sensitive (Linux, típico de
  produção), o autoload PSR-4 resolveria por `file_exists()` no caminho
  literal e falharia (`Class "RR\Core\Model" not found`). No macOS/Windows
  (case-insensitive) passa despercebido — é por isso que não quebra neste
  ambiente de desenvolvimento.
- **Alcançabilidade**: `ManagerPost` não é referenciado em nenhum outro lugar
  do projeto (zero `use`/`new` apontando pra ele) — parece versão antiga
  duplicada de `GerenciaPost` (que é a classe realmente usada em todo o
  projeto). Código morto hoje, mas armadilha real se for reativado sem
  perceber o case.
- **Severidade: BAIXO** (inalcançável hoje).

### Achado — BAIXO/informativo — case de namespace inconsistente em 3 controllers ajax

`src/controller/ajax/GlobalController.php:3`,
`BillReceiveInstallmentController.php:3`, `SettingsController.php:3`
declaram `namespace RR\Controller\ajax;` (C maiúsculo) em vez de
`RR\controller\ajax;` (os outros 22 controllers ajax usam o certo). **Não
quebra em runtime** — o roteamento (`Application.php`) monta a string da
classe com `controller`/`ajax` sempre minúsculo, literal no código-fonte, e
PHP trata namespace como case-insensitive internamente; nenhum outro arquivo
importa essas 3 classes via `use`. Reportado por consistência de padrão, não
como bug funcional.

### Confirmado limpo (sem achados)

- `BoxAlert`: zero referências em todo o projeto — migração pra `Toast`
  completa.
- `GerenciaPost`, `ModelGenerico`, `Secure`, `Util`, `Toast`: todas as
  chamadas espalhadas pelo projeto batem com os métodos reais.
- Nenhuma chamada para método removido nas limpezas anteriores (domínio
  imobiliário, migração BoxAlert→Toast).
- Componentes numéricos duplicados (`TableComponent5432`,
  `PaginationComponent1245`, `ContentHeaderComponent4214`) existem no disco e
  são de fato as versões ativas — consistente com
  `docs/11-duplicidades-legado.md`.

---

## 3. Rotas/links/forms quebrados — CONCLUÍDO

Cobertura: 445 `href`/`action` em `src/view/**/*.php`, todas as chamadas
`$.ajax`/`.post`/`.get` em `public/js/v_01/**/*.js` (projeto não usa
`fetch(`), e a tabela `menu` real (`veiculos_repasse`, via `SELECT`).

### Achado — CRÍTICO/ALTO — bug sistêmico de roteamento em links "controller/id" sem verbo de ação

**Causa raiz** (`src/core/Application.php:35-66`, `splitUrl()` linhas
75-110): o roteador trata o **segundo segmento** da URL sempre como nome de
**método**, nunca como parâmetro posicional de `index()`. Quando uma view
monta um link de exatamente 2 segmentos (`controller/{id}`, sem um nome de
método no meio), `$this->url_action` vira o próprio id (ex.: `"45"`) e, como
`$this->url_params` fica vazio, o código cai em
`$this->url_controller->{$this->url_action}()` (`Application.php:56`) — ou
seja, tenta chamar um método **literalmente chamado `"45"`**. Resultado:
`Fatal error: Call to undefined method Controller::45()`. Verificado
pessoalmente lendo `Application.php` linha a linha — o branch "sem ação →
chama index()" (linhas 58-66) é código morto nesse cenário, porque só é
alcançado quando `$this->url_action` está vazio, o que nunca é o caso quando
existe um 2º segmento.

Dois casos reais confirmados (dos únicos 2 existentes no projeto, segundo
varredura pelo padrão `$this->route . "/" . $var` / `"controller/$var"` de 2
segmentos):

1. **`src/view/notification/index.php:21`**
   ```php
   <a href="<?= URL . $this->route . "/" . $item->id ?>">
   ```
   Alcançável via sininho do header → "Visualizar tudo" → clicar em qualquer
   notificação. `NotificationController` (confirmado, `grep`) só tem
   `index()`, `disableItem($itemId, $page)`, `enableItem($itemId, $page)` —
   nenhum método aceita um id posicional sozinho. Clicar em qualquer
   notificação = erro fatal. **ALTO** (alcançável por qualquer usuário, 2
   cliques).

2. **`src/view/users/digital-card.php:80`**
   ```php
   <a href="<?= URL . "cardPDF/$itemId" ?>" ... >Gerar Cartão Digital</a>
   ```
   Confirmado lendo `src/controller/project/CardPDFController.php`: a classe
   tem só `index($userId)` — espera o id como **parâmetro de `index`**, não
   como nome de ação. O link de 2 segmentos gera exatamente o mesmo bug:
   `Fatal error: Call to undefined method CardPDFController::123()`. Botão
   "Gerar Cartão Digital" (visível quando o cadastro do cartão do vendedor
   está completo) está **100% quebrado** hoje — geração de PDF do cartão
   digital nunca funciona por esse caminho. **ALTO** (feature de negócio
   ativa).

### Achado — ALTO — PHP colado literalmente como string JS (não interpolado)

`public/js/v_01/purchase-requests/purchaseRequests.js:300,326` e
`public/js/v_01/sale-requests/saleRequests.js:359,385` (confirmado lendo o
arquivo):
```js
$('.disableDeleteButton').attr('href', 'URL . $this->route . "/deleteVehiclesPurchased/$vehicle->id').removeClass('disabled');
```
Código PHP copiado dentro de uma string JS de aspas simples, sem nenhuma
interpolação — vira um `href` literal sem sentido
(`URL . $this->route . "/deleteVehiclesPurchased/$vehicle->id`, string crua).
Ocorre ao reabilitar o botão de excluir veículo da lista, depois de finalizar
um pedido de compra/venda. No caminho de sucesso o `location.reload()` (1s
depois) mascara o problema; no branch "sem veículos restantes" **não há
reload**, então o botão fica com `href` quebrado até o usuário atualizar a
página manualmente. Módulo central do negócio (compra/venda de veículo).
**ALTO**.

### Achado — ALTO — link de relatório aponta pro módulo errado (copy-paste)

**`src/view/record-bill-receive-installment/index.php:196`** (relatório de
**Contas a Receber**) — confirmado lendo e comparando lado a lado com o
relatório irmão:
```php
<a ... href="<?= URL . 'bills-to-pay-installment' . "/editItem/$item->id" ?>" ...><?= $item->id ?></a>
```
Isso é literalmente a mesma linha de `src/view/record-bills-to-pay-installment/index.php:196`
(relatório de **Contas a Pagar**, onde está correta), copiada sem trocar o
módulo. O controller/rota de destino (`BillsToPayInstallmentController`)
*existe* (por isso não dá 404) — mas abre a tela de edição de **Contas a
Pagar** usando um id que na verdade é de um `bill_receive_installment`. Como
`BillsToPayInstallmentController` existe mas o id pertence a outra tabela,
abre um registro errado ou inexistente. Confirmado: `BillReceiveInstallmentController.php`
existe como controller correto e não é o que está referenciado no link.
**ALTO** (relatório financeiro de uso comum).

### Achado — ALTO (alcance total, impacto visível zero hoje) — chamada AJAX morta em toda página

`public/js/v_01/script.js:150` (confirmado, carregado em **toda** página do
domínio `project` via `src/controller/project/FrontController.php:56`,
`addScript(... "script.js")`, base de praticamente todos os controllers
"project"):
```js
$(document).ready(function () {
    $.post({ url: `${url}ajax/global/toast`, ... });
});
```
Confirmado que `GlobalController` (`src/controller/ajax/GlobalController.php`)
**não tem mais** método `toast()` (só `getGenericoById`,
`getItemByGenericField`, `getItemByGenericFieldArray`) — foi removido na
migração BoxAlert→Toast (`docs/11-duplicidades-legado.md`). Esse `$.post`
dispara em **toda** página, toda vez, e cai em
`Fatal error: Call to undefined method GlobalController::toast()` no
servidor (log de erro), embora o impacto visível pro usuário seja **zero**
hoje (não há `error:`/`.fail()` no JS, e o toast real já funciona via
`Toast::render()` inline no footer — mecanismo diferente e não afetado).
Vale limpar porque roda o tempo todo sem necessidade e polui log de erro do
servidor. **ALTO por alcance, mas sem sintoma visível hoje.**

### Achado — MÉDIO — resíduo de domínio imobiliário não coberto pela auditoria de migração

`src/view/customer/sales.php:38` → `href="<?= URL . "sales/edit-item/" . $sale->id ?>"`.
Não existe `SalesController` em `src/controller/project/` (só o Model
`Sales.php`). A aba "Vendas" só aparece se `count != 0` — confirmado
`SELECT COUNT(*) FROM sales` = 0 linhas hoje no banco de dev, então
dormente na prática, mas é resíduo genuíno (relacionado ao achado 1 do
`AUDITORIA-1-dominio.md`, que já cobre o bug do `INNER JOIN products`, mas
não tinha notado que o link de edição também não tem controller de
destino). **MÉDIO** — dormente hoje, mas quebraria assim que houvesse 1 venda
registrada.

### Achado — MÉDIO — asset estático ausente (dormente)

`src/core/Controller.php:71` (`$this->logoFavicon`, usado em `header.php` e
`login/index.php:13`, `recoverPassword.php:12`, `changePassword.php:13`) —
fallback `'img/more/favicon.png'` quando `system_config.logo_favicon_capa` é
falso. **`public/img/more/` não existe.** Hoje não é acionado
(`logo_favicon_capa = 1` no banco real + arquivo real existe), mas quebra o
favicon de todo o painel se o logo for removido ou em instalação nova.
**MÉDIO**.

### Achado — MÉDIO — link de download com prefixo inconsistente

`src/view/vehicles/attachments.php:56` — único link (entre ~4 análogos) que
usa `URL . "public/vehicle/" . ...` em vez de `URL . "vehicle/..."`. Funciona
hoje só por coincidência de como o XAMPP serve `htdocs/repasse_sandre/public/`
como pasta real; quebraria (duplicaria `public/public/`) num vhost configurado
apontando direto pra `/public` (setup que o próprio `.htaccess` do projeto
descreve como o correto). **MÉDIO**.

### Achados BAIXO (confirmados órfãos/inofensivos)

- `src/view/sync-folder/index.php` — sem controller, 100% inalcançável
  (ferramenta interna de dev).
- `public/js/v_01/spouse.js` (raiz) — nunca incluído (só
  `customer/spouse.js` é carregado); usa prefixo inválido `api/ajax/...` e
  case errado. Inofensivo por ser órfão.
- `public/js/v_01/application.js` — nunca incluído, duplicata de
  `script.js`, mesma chamada morta de toast. Registrar pra não ser
  reaproveitado por engano.
- `public/js/v_01/inputStar.js` — nunca incluído, referencia
  `img/star0.png`/`star1.png` inexistentes. Feature real de estrelas
  (`ReportAttendanceController::doStar()`) usa Font Awesome, não afetada.

### Confirmado sem regressão

- `attendance/kanban.php:62` — `?vehicles=true` continua correto.
- Menu real (tabela `menu`, `veiculos_repasse`) — todas as 38 rotas ativas
  (`status=1`) apontam pra controllers/métodos `index()` existentes.
- ~90 chamadas AJAX (`ajax/ajax/...`, `ajax/global/...`, etc., fora a de
  toast já listada) resolvem pra métodos reais.
- ~400 `href`/`action` restantes das views resolvem corretamente.

---

## 4. SQL vs schema real — CONCLUÍDO

**Metodologia**: `SHOW TABLES` (105 tabelas) + `DESCRIBE` de todas no banco
real `veiculos_repasse`. Todo `$this->table = 'x'` e `'table' => 'x'` (joins)
dos 82 arquivos de `src/model/` + controllers, e todo `FROM`/`JOIN` de SQL
cru, extraídos e comparados contra a lista real de tabelas; toda tabela fora
da lista foi inspecionada manualmente. Os 13 arquivos de `db/migrations/`
foram lidos em busca de `ALTER`/`DROP COLUMN`/`RENAME` cruzados com o código
atual. Nenhum item abaixo repete achado já coberto por
`AUDITORIA-1-dominio.md`.

### Achado — CRÍTICO — subsistema de rateio de comissão referencia 2 tabelas inexistentes; corrompe o registro de pagamento de parcela de venda

Duas tabelas usadas ativamente pelo código **não existem no banco real**
(confirmado `SHOW TABLES`; só existem no dump desatualizado
`db/realize_repasse.sql`, já registrado como não confiável pelo
`AUDITORIA-1-dominio.md`):
- `sales_charge_payment_agreement` (`src/model/SalesChargePaymentAgreement.php:13`)
- `arrangement_payment_charges_invoice_receive_installment` (`src/model/ArrangementPaymentChargesInvoiceReceiveInstallment.php:14`)

Cadeia de impacto **verificada pessoalmente** lendo
`src/controller/project/BillReceiveInstallmentController.php:564-674`
(`handleSubmitPayment`, o método real de "Registrar Pagamento" de uma
parcela de Contas a Receber):

- Linha 614-618: busca a `sale` vinculada à parcela — verdadeira no caso
  comum (toda `bill_receive` nasce de uma venda de veículo).
- Do `switch ($_POST['payment_transaction'])` (linha 621), só o `case '1'`
  ("Nova parcela") processa algo — é o caminho funcional principal, não um
  extra raro.
- Linha 645: `$this->model->insert($arrayPostNewPosition)` grava a nova
  posição da parcela. **Confirmado por grep (`beginTransaction`/`commit`/
  `rollBack`/`catch`) que não há transação nenhuma envolvendo este método**
  — os únicos `beginTransaction()`/`commit()`/`rollBack()` do arquivo estão
  num método totalmente diferente (linhas 930/949/954-955). O `catch
  (PDOException)` deste método só existe na linha 906, bem depois.
- Linhas 647-660, logo em seguida, ainda dentro do mesmo `try`: um
  `SELECT ... FROM sales_charge_payment_agreement` (tabela inexistente) —
  **falha sempre**, independente de haver ou não linhas.
- Resultado real: em produção (`PDO::ERRMODE_EXCEPTION`), a exceção é
  capturada no `catch` da linha 906 — mas **depois** que a parcela nova já
  foi inserida (linha 645, sem transação). O usuário registra o pagamento de
  fato, mas a tela mostra toast de erro genérico em vez de sucesso —
  acredita que falhou e pode tentar de novo, arriscando duplicar a parcela.
  No ambiente de dev atual (`ERRMODE_WARNING`), não há exceção — só warning
  silencioso — e o rateio de comissão simplesmente nunca é gravado, sem
  qualquer sinal de erro.
- O restante do "subsistema de rateio" (adicionar participante de comissão
  numa venda) está hoje **inalcançável pela UI** — zero chamadas em
  `public/js/v_01/**`/`src/view/**` aos métodos de
  `src/controller/ajax/PaymentAgreementController.php` que o exporiam. Isso
  não muda a severidade: o crash acontece no `SELECT`, que falha
  independente de haver linha.

**Severidade: CRÍTICO** — fluxo financeiro comum e frequente (registrar
recebimento de parcela de venda de veículo), efeito mascarado como falha e
com risco real de duplicidade por retentativa do usuário.

### Achado — CRÍTICO/ALTO — `LoginController.php:179` grava em tabela errada ("token" em vez de "tokens") ao confirmar troca de senha

**Verificado pessoalmente**, lendo `src/controller/project/LoginController.php:153-189`
(`handleSubmitChangePassWord`):
```php
165: $jwt = (new ModelGenerico())->getItemByGenericField($token, "tokens", "id_unique", 1);
...
178: (new GerenciaPost())->update8191($arrPost, "users", "id", $decodedToken->userData->id, false);
179: (new GerenciaPost())->update8191(["status" => 0], "token", "id", $jwt[0]->id, false);
```
A linha 165 (leitura) usa corretamente `"tokens"` (plural); a linha 179
(gravação, para invalidar o token após o uso) usa `"token"` (singular).
**Confirmado via `SHOW TABLES LIKE 'token%'` no banco real: só existe
`tokens`.** `GerenciaPost::update8191()` (`src/model/GerenciaPost.php:50-74`,
lido por completo) não tem try/catch interno, e o método
`handleSubmitChangePassWord` inteiro **não tem nenhum try/catch** — padrão
diferente do resto do projeto, onde toda gravação fica envolvida em `try {}
catch (PDOException)`.

- A senha do usuário já foi trocada com sucesso na linha 178 (tabela certa)
  quando a linha 179 (tabela errada) executa.
- Em produção (`ERRMODE_EXCEPTION`): `PDOException` não capturada — fatal
  error / tela em branco, depois que a senha já foi alterada. O usuário
  nunca vê o toast de sucesso nem é redirecionado.
- No ambiente atual (`ERRMODE_WARNING`): sem exceção, só warning silencioso —
  mascara o bug, mesmo padrão "funciona em dev, quebra em produção" já
  registrado noutros achados deste documento.
- Efeito colateral independente do ambiente: o token JWT de redefinição de
  senha **nunca é invalidado** (`status = 0` nunca é setado na tabela certa)
  — fica "ativo" no banco até expirar só pela checagem de tempo (1h).

**Severidade: CRÍTICO/ALTO** — fluxo de autenticação (recuperação de senha),
alcançável por qualquer usuário, pior em produção do que em dev.

### Achado — ALTO — recibo de pagamento sai corrompido para cliente sem profissão/estado civil cadastrado

`src/model/Contract.php` — `getInstalmentReceivePrintReceipt()` (linhas
19-96, chamada por `BillReceiveInstallmentController.php:964`,
`printReceipt`) e `getInstalmentToPayPrintReceipt()` (linhas 98-175, chamada
por `BillsToPayInstallmentController.php:701`) montam o SQL com:
```sql
INNER JOIN professions pro ON pro.id = cust.id_profession
INNER JOIN marital_status ms ON ms.id = cust.id_marital_status
```
`DESCRIBE customer` confirma que `id_profession` e `id_marital_status` são
**nullable** — e a tabela comporta cliente PJ (`company_name`, `cnpj`,
`fancy_name_company`) ou cadastro incompleto. Para esses clientes, o
`INNER JOIN` zera o resultado: `$installment` vira `false`, o
`foreach ($installment as ...)` que substitui os placeholders do texto do
recibo (`{%nome_cliente%}`, `{%valor_parcela%}` etc.) só gera um Warning e é
pulado — o recibo (Contas a Receber e Contas a Pagar) sai impresso com os
tokens crus não substituídos, sem qualquer aviso pro operador.

**Severidade: ALTO** — não trava a página, mas corrompe silenciosamente um
documento financeiro real, em cenário plausível e comum (cliente PJ ou
cadastro incompleto).

### Achado — MÉDIO — tela órfã de moedas (`currencies`) sem tabela e sem checagem de permissão

Tabela `currencies` **não existe** no banco real (confirmado `SHOW TABLES`)
mas é usada por `src/model/Currencies.php`, `CurrenciesController.php`
(CRUD completo) e `AjaxController::getValueCurrency()`. Gap já **conhecido e
decidido** pelo próprio time — documentado em comentário na migration
`db/migrations/2026_09_25_1500_seed_dados_faltantes.sql` ("criação de
tabela, fora do escopo desta migration por decisão do usuário"); incluído
aqui só por completude. Achado novo, porém: `CurrenciesController` **não
tem nenhuma entrada na tabela `menu`** — como
`Controller::__construct()` só aplica checagem de permissão quando a rota
está associada a um item de menu, essa tela fica acessível por URL direta
**sem checagem de permissão nenhuma** (não é escalação de privilégio — só
um crash — mas qualquer perfil logado, não só admin, cai no mesmo erro).
`AjaxController::getValueCurrency()` confirmado sem nenhuma chamada de
front-end — código morto.

**Severidade: MÉDIO**.

### Achados BAIXO — nomes de tabela errados em código morto (zero/quase zero chamadores)

| Arquivo:linha | Tabela usada | Tabela real | Chamadores |
|---|---|---|---|
| `src/model/Sales.php:410-424` `getLastNumberPortionByBillsToReceiveId()` | `bill_receive_installments` (plural) | `bill_receive_installment` (singular, confirmado `SHOW TABLES`) | zero |
| `src/model/SettingsSite.php:113-126` `getSettingMetaTagsById()` | `configuracao_metatags` | não existe | zero |
| `src/model/CustomerType.php:181-233` (3 métodos) | `customer_resource_client`, `customer_type_resources`, `customer_resource` | nenhuma existe (equivalente real: `client_type_resource_types`/`customer_required_field`) | 1 dos 3 tem chamador (`AjaxController.php:365`), mas esse endpoint ajax não tem nenhuma chamada de front-end — órfão na prática |
| `src/model/Contract.php:13` | `$this->table = 'contracts'` (inexistente) | — | nunca efetivamente consultado — os 4 métodos reais de `Contract.php` usam SQL cru contra tabelas reais, não o método genérico herdado |

Parece um mecanismo antigo de "recursos por tipo de cliente" substituído por
tabelas reais em uso (`client_type_resource_types`), mas o código velho
nunca foi removido. **Severidade: BAIXO** para todos — inalcançáveis hoje,
mas armadilhas reais se reaproveitados sem perceber.

### Observação de schema (sem ação de código)

`DESCRIBE sales` mostra `currency_value` com `Default` = a string literal
`` `currency_id` `` (não `NULL`) — parece resíduo de DDL malformada, não
causado por código PHP. Nenhum código usa esse campo incorretamente.
Registrado para avaliação futura do usuário.

### Migrations cruzadas contra código atual

Os únicos `DROP COLUMN` nos 13 arquivos de `db/migrations/`: 1)
`branch.creci_legal`/`users.creci` — já coberto pelo AUDITORIA-1; 2)
`users.permission_publish`/`users.show_team` — confirmado, sem nenhuma
referência residual no código atual. Limpeza correta, não é achado.

---

## 5. Variáveis indefinidas / caminhos de arquivo quebrados — CONCLUÍDO

**Metodologia**: amostra representativa, não exaustiva (o projeto tem ~480
`require` de view espalhados em 48 controllers `project`) — pares
view↔controller cobrindo módulos centrais (login, atendimento, contas a
receber, relatório de vendas), mais grep de
`file_exists()`/`unlink()`/`move_uploaded_file()`/`fopen()` no código da
aplicação (excluindo bibliotecas vendored), mais amostra de
`$_GET`/`$_POST`/`$_SESSION` sem `isset()`/`??` em login/atendimento/pagamento.

### Pares view↔controller verificados — limpos

- `login/index.php`, `recoverPassword.php`, `changePassword.php` ↔
  `LoginController` — todas as variáveis definidas antes do `require`.
- `attendance/addItem.php` ↔ `AttendanceController::addItem()` — variáveis
  batem; a view reaproveita o nome `$city` num `foreach` por cima do `$city`
  original, mas o único uso do original acontece antes do loop — sem
  corrupção de dado na prática.
- `record-sale-requests/print.php` ↔ `RecordSaleRequestsController` — todas
  as variáveis acessadas com `isset()`. Bem defendido.
- `bill-receive-installment/printReceipt.php` ↔ `printReceipt()` — só usa
  `$contractText` (sempre definida), então não há variável indefinida aqui;
  o **conteúdo** dela é que fica corrompido no cenário já descrito na seção 4.

### Achado — MÉDIO — `$_POST['created_by']` sem `isset()` em `AttendanceController::handleSubmitAddAttendance()`

`src/controller/project/AttendanceController.php:168-170`:
```php
if (Secure::access_secretary()) {
    $arrPost["created_by"] = $_POST['created_by'];
}
```
Se o formulário de cadastro de atendimento (perfil "secretária") não enviar
esse campo, gera "Undefined array key" e grava `NULL` em
`attendance.created_by` — campo usado depois em `Secure::creator()` para
decidir quem pode ver o atendimento. Um `created_by` nulo pode tornar o
atendimento invisível para quem deveria vê-lo. Amostra, não aprofundado.

**Severidade: MÉDIO**.

### Achado — BAIXO — `src/libs/UploadFiles.php` referencia diretório inexistente, mas é código morto

Linha 30: `file_exists("image/{$page}/{$array['id']}")` — `public/image/`
não existe (só `public/img/`). Confirmado `grep -rln "UploadFiles"` em
`src/controller/`, `src/model/`, `src/view/` = zero resultados — órfã,
superada por `src/libs/FileUploader.php` (5 usos reais confirmados).

**Severidade: BAIXO**.

### Confirmado sem problema

- `WaterMarkController.php:52-53` referencia o mesmo `img/more/` ausente da
  seção 3 (favicon), mas faz `mkdir()` antes de escrever e `@unlink()`
  silenciado — não quebra.
- Padrão `img/{modulo}/{id}/...` em `UsersController`, `SettingsController`,
  `CustomerController`, `BranchController`, `DigitalCardController` — todos
  os pontos amostrados fazem `mkdir()` antes de gravar e `@unlink()` do
  arquivo antigo. Padrão seguro nos casos verificados.

---

## Tabela-resumo final (todas as seções, CRÍTICO → BAIXO)

| # | Arquivo:linha | Achado | Severidade |
|---|---|---|---|
| 1 | `BillReceiveInstallmentController.php:564-660` + `SalesChargePaymentAgreement.php`/`ArrangementPaymentChargesInvoiceReceiveInstallment.php` | tabelas de rateio de comissão inexistentes corrompem registro de pagamento de parcela de venda (sem transação) | **CRÍTICO** |
| 2 | `LoginController.php:179` | `update8191(..., "token", ...)` — tabela errada (real é `tokens`), sem try/catch | **CRÍTICO/ALTO** |
| 3 | `src/view/notification/index.php:21` + `src/core/Application.php:35-66` | bug de roteamento: link de 2 segmentos chama método com nome literal do id — clicar em notificação = fatal error | ALTO |
| 4 | `src/view/users/digital-card.php:80` + `CardPDFController.php` | mesmo bug de roteamento — "Gerar Cartão Digital" 100% quebrado | ALTO |
| 5 | `Contract.php:70-72,150-151` | INNER JOIN professions/marital_status corrompe recibo pra cliente PJ/incompleto | ALTO |
| 6 | `purchaseRequests.js:300,326`, `saleRequests.js:359,385` | string PHP colada como JS, `href` quebrado sem reload em 1 branch | ALTO |
| 7 | `record-bill-receive-installment/index.php:196` | link de relatório aponta pro módulo errado (Contas a Pagar em vez de Receber), copy-paste | ALTO |
| 8 | `public/js/v_01/script.js:150` + `GlobalController.php` | chamada AJAX morta (`ajax/global/toast`) em toda página; sem sintoma visível hoje | ALTO (alcance) |
| 9 | `src/view/customer/sales.php:38` | link de "Vendas" sem `SalesController`; dormente (0 vendas hoje) | MÉDIO |
| 10 | `Currencies.php`/`CurrenciesController.php` | tabela inexistente (gap já decidido), tela sem entrada no menu e sem checagem de permissão | MÉDIO |
| 11 | `src/core/Controller.php:71` + `public/img/more/` | favicon fallback aponta pra diretório inexistente, dormente | MÉDIO |
| 12 | `src/view/vehicles/attachments.php:56` | prefixo `public/` inconsistente no link de anexo, funciona só por coincidência de ambiente | MÉDIO |
| 13 | `AttendanceController.php:169` | `$_POST['created_by']` sem `isset()`, pode gravar `NULL` | MÉDIO |
| 14 | `Sales.php:410-424`, `SettingsSite.php:113-126`, `CustomerType.php:181-233` | nomes de tabela errados em métodos/endpoints órfãos | BAIXO |
| 15 | `Contract.php:13` | `$this->table='contracts'` (inexistente), nunca efetivamente consultado | BAIXO |
| 16 | `src/libs/UploadFiles.php:30` | caminho inexistente, classe sem chamador | BAIXO |
| 17 | `src/model/ManagerPost.php:3,5` | namespace/`use` com case errado — armadilha de produção Linux, código morto | BAIXO |
| 18 | 3 controllers ajax (`GlobalController.php` e outros 2) | namespace com `C` maiúsculo, inconsistente mas inofensivo | BAIXO |
| 19 | `sync-folder/index.php`, `spouse.js` (raiz), `application.js`, `inputStar.js` | views/scripts órfãos confirmados inofensivos | BAIXO |
| — | `sales.currency_value` (coluna) | `DEFAULT` inválido (string `` `currency_id` ``) — observação de schema | informativo |

## Conclusão

Os dois achados **CRÍTICO** (registro de pagamento de parcela de venda, e
invalidação de token de redefinição de senha) são os que merecem atenção
imediata: ambos gravam dado real com sucesso e só falham **depois**, então o
sintoma visível ao usuário (erro genérico) não corresponde ao que de fato
aconteceu no banco — o tipo de bug mais perigoso de diagnosticar depois,
porque os logs de erro não deixam claro que a operação principal teve
sucesso. Os achados ALTO de roteamento (notificação, cartão digital) e o
copy-paste do relatório de contas a receber também valem correção rápida por
serem simples e bem localizados. O restante é código morto/dormente ou
inconsistência cosmética, sem necessidade de ação imediata.

Nenhuma correção foi aplicada nesta auditoria — só o relatório, como pedido
("SOMENTE LEITURA").

## Sanity-check final

Todos os arquivo:linha citados nas seções 4 e 5 foram lidos diretamente
nesta sessão (`Read`/`grep`/consulta ao banco real) antes de entrar neste
documento — incluindo os dois achados CRÍTICO, verificados byte a byte
contra o código atual e contra `SHOW TABLES` do banco de produção
`veiculos_repasse`. As seções 1-3 já haviam passado pelo mesmo processo no
dia anterior.
