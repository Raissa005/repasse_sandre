# Plano de Correções — consolidado das Auditorias 1-4

**Data**: 2026-09-29. **Status**: planejamento — **nenhum código ou dado foi
alterado ao gerar este documento.**

## Como este documento foi feito

Lidos os quatro relatórios na raiz do projeto — `AUDITORIA-1-dominio.md`
(resíduo do domínio imobiliário), `AUDITORIA-2-tecnica.md` (sintaxe,
includes, rotas, SQL vs schema, variáveis), `AUDITORIA-3-seguranca.md`
(SQLi, XSS, CSRF, upload, senhas, controle de acesso), `AUDITORIA-4-identidade.md`
(marca/contato residual do cliente e fornecedor anteriores) — somando mais
de 90 achados individuais entre os quatro. Este plano:

- **Remove duplicatas**: alguns achados apareceram em mais de um relatório
  sob óticas diferentes (ex.: a checagem hardcoded `"Suporte Ydeal"` é ao
  mesmo tempo uma falha de controle de acesso e um resíduo de marca) — cada
  um aparece **uma única vez** aqui, no grupo de maior severidade, com nota
  cruzada para o outro relatório.
- **Ordena por severidade**: CRÍTICO → ALTO → MÉDIO → BAIXO, replicando a
  escala já usada nas Auditorias 2/3 (as Auditorias 1/4 usavam classificação
  A/B/C ou "ativo/órfão"; reclassifiquei esses achados na mesma escala de
  4 níveis para consolidar num único ranking).
- **Agrupa itens relacionados**: quando duas ou mais linhas de achado devem
  ser corrigidas na mesma tacada (mesmo arquivo, mesma causa-raiz, ou mesma
  decisão de negócio pendente), viram um grupo único com um risco de
  correção só.
- **Estima o risco de cada correção** — não só "é grave", mas "o que pode
  quebrar se eu mexer nisso".
- Grupos marcados **⚠️ BLOQUEADO** precisam de uma decisão do usuário
  (dado de negócio real, ou confirmação de escopo) **antes** de qualquer
  código ou migration poder ser escrito — regra do CLAUDE.md de não presumir
  regra de negócio.

Nenhuma correção foi aplicada. Este é só o plano.

---

## Ordem de execução

Organização em ondas combinada com o usuário fora deste documento e
registrada aqui em 2026-10-01. O detalhe de cada item continua na seção dele,
mais abaixo. Itens de deploy ficam em `DEPLOY-CHECKLIST.md`.

**✅ Onda 1 — concluída** (2026-09-30 a 2026-10-01): C6, A6, A7, A8, C4
etapa 1, A11, M5, M6, M8, M11, e os achados N1 e N2 (corrigidos no mesmo
período).

**✅ Onda 2 — concluída** (2026-10-01): C1, C2 (lotes 1, 2 e 3), N4, N6, N7,
N8, C4 etapa 2.

**Onda 3 — quase concluída** (decisões do usuário de 2026-10-01/02 em cada
item; aplicada em etapas C5 → N14 → C3 + "sem linha = negado" + N15 →
A5/M2/N13, com `php -l` e teste antes × depois em cada etapa):
- ✅ M1 — aba "Vendas" do cliente tirada da navegação (2026-10-01)
- ✅ C5 — rateio herdado removido (2026-10-01) e transação opção A (2026-10-02)
- ✅ N14 — regras de tipo de usuário no servidor (2026-10-02)
- ✅ C3 + "sem linha = negado" + N15 (2026-10-02). **Não** popular
  `menu_access` com negações: o Sandré configura pela tela (DEPLOY-CHECKLIST §9)
- ✅ A5 / M2 / B5, N13, sobra do N2, N10 3º item (2026-10-02)
- ⚠️ N17 — 11 rotas sem item de menu: proposta feita, **aguardando aprovação**
- C7 — ⚠️ bloqueado (dado real)
- A9 — ⚠️ bloqueado (dado real)

**Onda 4 — antes do lançamento**:
- A2
- A3 + M3
- A4
- A10
- M4
- N9
- Chamada morta `ajax/global/toast` em `script.js:150` (N3, 2º item)
- Botões Ativar/Inativar da listagem de Cheques (N10, 1º item)
- Anexo de veículo que só grava o primeiro arquivo (N11)
- Sobra do N1: anexos de veículo e de Contas a Pagar/Receber gravam registro
  quebrado se enviados sem arquivo
- N12: trocar o WideImage pelo Imagine em avatar, cartões, logos e marca
  d'água, recodificando sempre, e corrigir o `-dp-` da foto do cartão
  (proposta no N12, ainda não aplicada), mais os demais itens do N12
- M12: esconder Moedas do menu e bloquear a rota `currencies`

**Pós-lançamento**:
- A1 completo
- Grupo B: B1, B3, B4, B6 a B11 (o B5 vai na Onda 3, com A5/M2; o B2 fica fora das ondas)
- M7, M9, M10
- M12, limpeza opcional: remover as referências a moedas no código (o
  bloqueio da tela vai na Onda 4)
- Métodos sem uso do `AjaxController` (N3, 1º item)
- N5
- Reordenação de anexos de veículo (N3, 3º item)
- `getCustomersSuppliersAndBuilders` sem chamador e com ids fixos (N10, 2º item)

**Fora das ondas**: B2 — decisão do usuário (2026-10-01): **manter
documentado, não dropar** as tabelas/colunas órfãs. Nenhuma migration.

## CRÍTICO

### ✅ C1 — Endpoints ajax genéricos sem autenticação + SQL injection sem bind — CONCLUÍDO 2026-10-01

**Nota de execução (2026-10-01)** — aplicado em 4 etapas, todas verificadas:
1. **Login obrigatório** em 4 controllers ajax (não 2 como o plano previa): o
   levantamento prévio achou `SettingsController` (ajax) e
   `BillReceiveInstallmentController` (ajax) com o mesmo bypass. Os quatro
   agora herdam a checagem da classe `Ajax` (removido o `__construct` que só
   fazia `session_start()`; `AjaxController` passou a `extends Ajax`).
   Confirmado por `curl` sem login: os 4 respondem `Invalid session!`.
2. **Whitelist no `GlobalController`**: `getGenericoById` só aceita as 8
   tabelas realmente consultadas pelo front-end e, para `users`, devolve só
   `id`+`name` (antes vazava o hash da senha a qualquer logado);
   `getItemByGenericFieldArray` só aceita `attendance_filters_interests` com
   as 2 colunas usadas. O endpoint `getItemByGenericField` (sem uso) foi
   **removido** (re-grep confirmou zero chamadas).
3. **Validação de identificador na base** (`Model::assertIdentifier`, herdada
   por `ModelGenerico` e `GerenciaPost`): todo nome de tabela/coluna
   interpolado é checado contra `^[A-Za-z_][A-Za-z0-9_]*$`;
   `getItemByGenericFieldArray` passou a usar **bind** nos valores. Testado
   por script: 5 injeções bloqueadas, 3 chamadas legítimas seguem funcionando.
4. **`AjaxController`**: `updateFilesOrder`/`updateFilesOrdem` com whitelist
   (`vehicle_images`, `user_networks`); `getAllItensFromGenericTable`
   **removido** (re-grep: zero chamadas). `updateFilesOrdem` **não** foi
   removido — o re-grep mostrou que `order/orderList.js:25` o referencia.
5. **Sessão expirada no front-end**: `script.js` ganhou um
   `$(document).ajaxComplete` que, ao ver `Invalid session!`, redireciona a
   `login/logout` — antes a tela quebrava com dado vazio. Login não carrega
   `script.js`, então não há loop.

**Pendente de teste manual** (logada): a lista no fim desta nota.

**Fontes**: `AUDITORIA-3-seguranca.md` §1.1.
**Arquivos**: `src/controller/ajax/GlobalController.php`,
`src/controller/ajax/AjaxController.php`, `src/model/ModelGenerico.php`
(`getItemByGenericFieldArray`, `getItemById8161`, `getItemByGenericField`),
`src/model/GerenciaPost.php` (`update8191`, usado por `updateFilesOrder`/
`updateFilesOrdem`/`getAllItens`).

O achado mais grave de todas as quatro auditorias: dois controllers ajax
não exigem login (`GlobalController` sobrescreve o construtor da classe-mãe
sem chamar a checagem de sessão dela) e métodos genéricos aceitam nome de
tabela/coluna direto de `$_POST`, um deles sem nenhum bind de parâmetro.

**Risco da correção**: MÉDIO-ALTO. Duas frentes que precisam andar juntas:
1. Adicionar autenticação real a `GlobalController`/`AjaxController` — antes
   de fazer isso, é preciso mapear **todo** uso legítimo hoje de
   `ajax/global/*` e `ajax/ajax/*` no front-end (`public/js/v_01/**`), pois
   se algum fluxo depender de chamar isso sem sessão ativa (pouco provável,
   mas não descartado), a correção quebraria essa tela.
2. Whitelistar/validar nome de tabela e coluna em `ModelGenerico`/
   `GerenciaPost` — essas classes são usadas em **dezenas** de lugares do
   projeto; qualquer mudança de assinatura ou validação interna precisa ser
   testada contra todos os call sites existentes, não só os vulneráveis.

### ✅ C2 — SQL injection autenticada via filtro "Nome"/pesquisa (13 controllers) — CONCLUÍDO 2026-10-01 (lotes 1, 2 e 3)

**✅ Lote 1 CONCLUÍDO 2026-10-01** — `VehiclesController`,
`RecordOfSoldVehiclesController`, `RecordOfPurchasedVehiclesController`,
`RecordVehicleHistoryController` (todos em `src/controller/project/`).

**Nota de execução**: o `columns` do Model **não serve** para essas buscas —
ele só liga condições com `AND` e usa placeholder `:{tabela}_{coluna}` (duas
condições na mesma coluna colidem, ex.: `plate` com e sem hífen). Trocar por
`columns` transformaria a busca "contém em qualquer coluna" (OR) em "contém em
todas" (AND). **Decisão do usuário**: estender o filtro `'where'` com uma chave
opcional `'parameters'` (`[':busca_1' => '%x%', ...]`), cujos valores
`mountSqlFromFilters` junta aos binds do `execute()` — mudança aditiva em
`src/core/Model.php`; sem a chave, nada muda para os demais models. Os
controllers mantêm o mesmo SQL (OR, `ucase`, variante de placa com hífen),
só que com placeholders. **Também por decisão do usuário**, entraram no lote
as demais concatenações de `$_GET` no `'where'` do `RecordVehicleHistory`
(`data_de`, `data_ate` nos 3 tipos de data, e `transfer`), em `index()` e
`print()`. Os `print()` de Vendidos/Comprados tinham o mesmo bloco de busca do
`index()` e também foram corrigidos. Padrão documentado em
`docs/04-models.md`.

**Verificação**: `php -l` ok nos 5 arquivos. Teste antes × depois (código
original via `git stash` × código corrigido), com os mesmos termos:
`Veículos` por `curl` logado; os 3 relatórios exigem perfil admin/secretária e
a sessão local disponível não tinha esse perfil, então foram executados pelo
controller real via CLI com uma sessão de Administrador **só em memória**
(nenhuma escrita no banco). Resultado: para termos normais (`jdj`, `JDJ9323`,
`cliente`, `a`, vazio), status ativo/inativo, os 3 tipos de data (de, até,
de+até) e `transfer`, o número de linhas é **idêntico** antes e depois. Com
apóstrofo (`d'a`), antes dava `SQLSTATE[42000] 1064` em todas as 4 telas;
agora lista vazia sem erro. Injeções que funcionavam antes
(`data_de=2020-01-01' OR '1'='1` e `transfer=1 OR 1=1` devolviam as 7 linhas)
agora se comportam como o valor literal.

**✅ Lote 2 CONCLUÍDO 2026-10-01** — `PurchaseRequestsController` e
`SaleRequestsController`, project e ajax.
- **SQL injection**: 5 pontos, todos de busca (não havia outro `$_GET`/`$_POST`
  no `'where'` desses arquivos; `competence` vai para `insert()`, que já faz
  bind): `index()` de Compra e Venda (cliente/razão/fantasia/id/placa com e
  sem hífen; a remoção de espaços da busca foi mantida),
  `getCustomersSellerAndBuyer` (ajax, Compra e Venda) e `getVehiclesForSale`
  (ajax, Venda). Mesmo padrão `'where'` + `'parameters'`.
- **LIMIT/OFFSET (N7)**: os ajax passavam `$_POST['limit']`/`['page']` crus ao
  Model, que concatenava `LIMIT`. Corrigido no Model (ver N7).
- **XSS**: `purchase-requests/index.php` e `sale-requests/index.php` devolviam
  busca, `data_de` e `data_ate` sem escapar; agora com `htmlspecialchars` (6
  pontos). Essas telas não têm link de impressão montado de `REQUEST_URI`. As
  respostas ajax são JSON e não ecoam o termo.
- **Impressão sem checagem**: não há `print()`. Os 4 métodos de impressão de
  cada controller (`vehiclePrinting`, `installmentPrinting`,
  `vehicleInstallmentPrinting`, `commissionInstallmentPrinting`) não tinham
  checagem nenhuma; receberam a mesma do `index()`
  (`Secure::individual_menu_access` do menu da rota). **Efeito prático hoje:
  nenhum**: `menu_access` só tem linhas `status=1` para esses menus (modelo
  opt-out), então ninguém é bloqueado no `index()` nem na impressão. Ver N8
  para a lacuna real.
- **Teste antes × depois** (código original via `git stash`, `curl` com
  sessão real admin): busca no `index()` (9 termos × ativo/inativo + 3
  combinações de data), busca ajax (6 termos, p. 1/2, `limit` 3/5, exclusão
  de veículos já escolhidos), 16 impressões. Mesmas linhas/ids/conteúdo em
  tudo. Com apóstrofo e `x' OR '1'='1`, antes `SQLSTATE 1064` nas 5 buscas (no
  ajax o JSON vinha quebrado); agora lista vazia, JSON válido. Payload XSS
  aparecia cru em busca e datas das 2 telas; agora escapado. A sonda
  `limit=5 PROCEDURE ANALYSE()` dava erro SQL antes; agora não chega ao SQL
  (sobra um `Warning: A non-numeric value` do `Pagination::pages()` no
  controller, só em dev e só com valor não numérico, que o JS nunca envia).
  **Não deu para testar o bloqueio** da impressão por perfil: exigiria uma
  linha `status=0` em `menu_access` (escrita no banco, proibida). A chamada é
  idêntica à do `index()`.

**✅ Lote 3 CONCLUÍDO 2026-10-01** — `CheckControlController` (project e
ajax) e `CustomerController` (ajax).
- **SQL injection**: 4 pontos, todos de busca (os demais valores da requisição
  no SQL já iam por `columns`, com bind: `statusCheck`, `customerId`,
  `deleteItems`/`NOT_IN`; `limit`/`page` pelo N7): `index()` da tela Cheques,
  `ajax/CheckControl/getCustomersChecks` e `getAllChecks`,
  `ajax/Customer/getCustomersSuppliersAndBuilders`. Mesmo padrão `'where'` +
  `'parameters'`.
- **Busca da tela Cheques estava quebrada** (achado no lote): o SQL usava
  `check_control.forwarded_by_name`, coluna que não existe, então **qualquer**
  busca dava `SQLSTATE 1054` e lista vazia; e faltava parêntese (`AND titular
  LIKE x OR ...` furaria o filtro de status). **Decisão do usuário**: buscar no
  titular (`owner_check`) **ou** no nome/razão/fantasia do cliente que repassou
  (coluna "Repassado Por", `forwarded_by` → `customer`, por subquery), tudo
  entre parênteses — mesma lógica dos 2 endpoints ajax de cheque.
- **XSS**: `check-control/index.php` devolvia a busca crua; agora com
  `htmlspecialchars`. A tela não tem filtro de data nem link de impressão
  montado de `REQUEST_URI` (`statusCheck` e `b` só são comparados). Os ajax
  devolvem JSON e não ecoam o termo.
- **Impressão**: não há `print()` nem outro relatório nesses 3 controllers.
- **Gravações sem checagem de perfil**: `handleSubmitAddItem` e
  `handleSubmitEditItem` só checavam o método POST; agora
  `Secure::access_admin(true)` (1ª instrução), igual a `addItem`/`editItem`/
  `index()`. `handleDeleteCheckTimeline` não tinha checagem nenhuma; o botão
  de excluir comentário em `edit.php` só aparece para `access_superAdm()`, e
  **por decisão do usuário** o servidor agora exige o mesmo
  (`Secure::access_superAdm(true)`, 1ª instrução; Administrador passa a ser
  recusado se chamar a rota direto). `ajax/CheckControl/addNewInstallment`
  (cheque devolvido: grava cheque, linha do tempo, parcela/conta) só é chamado
  da tela de edição (admin); agora recusa com "Sem permissão para esta ação."
  em JSON (padrão N8; o `checkControl.js` já trata `error`). O ajax
  `Customer` não tem método que grave.
- **Teste antes × depois** (código original via `git stash`, controller real
  via CLI com sessão de Administrador **só em memória**; saídas comparadas
  normalizando o `?v=` dos assets): listagem sem busca e com status
  1/0/2, edição do cheque 1 como Superadm/Administrador (e redirect para
  access 18/30), 9 termos × 2 endpoints ajax de cheque (`limit` 5), exclusão
  por `deleteItems`, cliente sem cheques, 8 termos × 2 páginas no ajax de
  cliente: **idênticos**. Diferenças só onde esperado: na tela Cheques, as
  buscas antes davam `SQLSTATE 1054` e agora listam (`alf`/`ALF`/`reis` acham
  pelo titular; `silvia`/`SILV`/`fisica` pela cliente que repassou;
  `catarinense`/`tito`/`zzz` nada; `a` com status inativo, nada — o status é
  respeitado). Com apóstrofo, antes `SQLSTATE 1064` nos 3 ajax (JSON
  quebrado); agora lista vazia, JSON válido. A sonda `zz%') OR 1=1 OR ('` em
  `getAllChecks` devolvia o cheque 1; agora nada. Payload XSS no campo de
  busca saía cru; agora escapado.
  **Limite**: `getCustomersSuppliersAndBuilders` devolve vazio para qualquer
  termo no banco local (nenhum cliente da filial com tipo 9/11, ids fixos, ver
  N10); a busca dele foi conferida pelo model `Customer` com o mesmo filtro
  sem o de tipo (só `SELECT`): antigo × novo iguais em 10 termos × 3 páginas.
  **Não executados** (gravam no banco): `handleSubmitAddItem`,
  `handleSubmitEditItem`, `handleDeleteCheckTimeline`, `addNewInstallment`;
  conferido estaticamente que a checagem é a 1ª instrução.

**Nos lotes 2 e 3, verificar também em cada controller** (pedido do usuário,
2026-10-01): (a) XSS refletido no campo de busca e em qualquer filtro devolvido
na tela, inclusive datas e link de impressão montado de `REQUEST_URI` (ver
N6); (b) `print()` (ou outro método de relatório) sem a mesma checagem de
perfil do `index()` da tela (ver N4).

**Fontes**: `AUDITORIA-3-seguranca.md` §1.2.
**Arquivos**: `RecordOfSoldVehiclesController.php`,
`RecordOfPurchasedVehiclesController.php`, `RecordVehicleHistoryController.php`,
`VehiclesController.php`, `CheckControlController.php` (project),
`PurchaseRequestsController.php`/`SaleRequestsController.php` (project e
ajax), `CustomerController.php` (ajax), `CheckControlController.php` (ajax).

Concatenação direta de `$_GET`/`$_POST` na cláusula `WHERE` via mecanismo
`'where' => "..."` do query-builder, em vez de usar o `filters`/`columns`
parametrizado que o mesmo Model já suporta.

**Risco da correção**: BAIXO por arquivo (trocar a concatenação por um
filtro com bind é um padrão mecânico, já usado em outros pontos do mesmo
Model), mas o volume é grande (13 arquivos) — cada um precisa de teste
manual da busca depois da troca, para garantir que o comportamento de busca
("contém", `LIKE %x%`) continua idêntico.

### ✅ C3 — Escalação de privilégio self-service (`turnUser` + `updateMenu`) — CONCLUÍDO 2026-10-02 (com "sem linha = negado" e N15)

**Fontes**: `AUDITORIA-3-seguranca.md` §5.1, §5.2; mesma linha de código
também aparece em `AUDITORIA-4-identidade.md` §1.4 e §1.6 (checagem
hardcoded pelo nome "Suporte Ydeal" e a conta em si) — tratado aqui como um
único grupo, já que corrigir o controle de acesso resolve as duas
perspectivas.
**Arquivos**: `src/controller/project/UsersController.php:148,255-339,257`,
`src/controller/project/SettingsController.php:318-327`,
`src/model/MenuAccess.php:31-79`.

Qualquer usuário autenticado, de qualquer perfil, pode virar Superadmin
(`turnUser`) ou conceder a si mesmo acesso a qualquer tela (`updateMenu`) —
confirmado explorável com os dados reais de `menu_access`. A única
"proteção" hoje é ocultar um botão na tela comparando o nome de exibição do
usuário com a string `"Suporte Ydeal"`.

**Risco da correção**: MÉDIO. Tecnicamente simples (adicionar
`Secure::access_superAdm()` ou equivalente nos dois métodos é uma mudança
pequena e isolada), mas **⚠️ precisa de decisão do usuário antes**: quem
deveria realmente poder usar "Ver como este usuário" (só Superadmin?
Administrador também?) e conceder/revogar permissão de tela (só
Superadmin?). Depois de definido, também vale popular `menu_access` com
linhas explícitas de negação para os demais perfis, em vez de confiar só no
código — o modelo atual é opt-out (sem linha = liberado), então a proteção
no código sozinha não cobre o caso de outra rota nova esquecer a mesma
checagem no futuro.

**Decisões do usuário (2026-10-01, versão final — substituem a anterior do
mesmo dia)**:
1. "Suporte Ydeal" (id 1, Superadm) é a conta do dono do sistema (Ydeal é a
   empresa dona): **manter**. Trocar só a checagem pelo **nome** por checagem
   de **perfil** (Superadm), na tela (botão em `UsersController::editItem`) e
   no servidor (`turnUser` e `updateMenu`).
2. **Permissões de tela (aba Menus): Superadm e Administrador** — o Sandré
   (Administrador) vai configurar os perfis em produção. O Administrador
   **não pode** editar as permissões dos perfis Superadm e Desenvolvedor.
   **"Ver como este usuário": só Superadm.** Desenvolvedor = igual ao Superadm.
3. **Não** popular `menu_access` com negações. Gerentes, Vendedor e Secretária:
   o Sandré define pela tela.
4. Verificar como a tela grava ao desmarcar e propor o servidor tratar **"sem
   linha = negado"** para os perfis que não são Superadm/Administrador/
   Desenvolvedor, mostrando o impacto antes de aplicar.
5. `DEPLOY-CHECKLIST.md`: o Sandré configura as permissões de cada perfil antes
   de liberar os usuários (adicionado na seção 9 do checklist).

**Como o controle de telas funciona hoje (levantado 2026-10-01, só leitura)**:
- **Menu lateral** (`Controller::assembleMenu` + `MenusComponent`): mostra um
  item só se o perfil tem linha em `menu_access` com `status = 1`. Além disso,
  `assembleMenu` **esconde Filiais (7) e DRE (59) de todo mundo**, exceto
  Superadm no modo "todas as filiais" (filial 0) — por isso o Administrador
  hoje não vê esses dois itens, mesmo tendo linha liberada.
- **Servidor** (`Controller::__construct` → `Secure::individual_menu_access`
  com o menu da rota): só bloqueia se existir linha com `status = 0`. Sem linha
  = liberado. Hoje **não existe nenhuma linha `status = 0`**, então ninguém é
  bloqueado por menu. `menu.access` (nível do menu) não é usado pelo servidor.
- Linhas liberadas hoje: Superadm 66, Administrador 66, Vendedor 29,
  Secretária 26. **Gerente Geral, Gerente de Filial e Gerente de Vendas (4
  usuários ativos) não têm nenhuma linha** — o menu deles é vazio, mas pela URL
  abrem tudo.
- A checagem é por **rota**, não por item de menu: `getMenuByRoute` pega o
  primeiro menu com aquela rota. `customer` tem 5 itens (Comprador, Fornecedor,
  Vendedor, Colaborador, Todos) e o servidor só olha um deles (o primeiro,
  Comprador); desativar "Fornecedor" para um perfil só tira o link do menu.
- Rotas **sem item de menu** (fora de qualquer configuração da tela): `card-pdf`,
  `countries`, `currencies`, `error`, `front`, `lead`, `lead-config`,
  `lead-redirect`, `login`, `networks-site`, `notification`. Endpoints `ajax/*`
  também não passam pela checagem de menu.

**Como a tela de permissões grava** (`settings/menus` → `settings/updateMenu`
→ `MenuAccess::updateProfileMenu`, `public/js/v_01/settings/menu.js`):
- A tela mostra 3 estados: **Ativo** (linha `status = 1`, botão vermelho ×),
  **Inativo** (linha `status = 0`, botão verde ✓) e **"Padrão do sistema"**
  (sem linha, botão azul ?).
- Clicar **inverte**: linha `1` → `0` (grava **"negado"**, não apaga a linha);
  linha `0` → `1`; sem linha → **insere `status = 1`** (e insere também o pai
  com `1`, se o pai não tiver linha). Nunca apaga linha.
- **Defeito (N15)**: ao clicar num menu **pai**, cada submenu é **invertido
  individualmente** (não recebe o estado do pai), e submenu sem linha é sempre
  **inserido como liberado**. Ex.: desativar o pai "Financeiro" de um perfil
  sem linhas libera todos os submenus de Financeiro.
- A lista de perfis da tela (`User::getAllUsersProfilesBellow`) já mostra só
  perfis com `access >=` o do usuário e esconde o Desenvolvedor — o
  Administrador já não vê Superadm/Desenvolvedor na lista, mas o servidor
  aceita qualquer `profileId` na URL de `updateMenu`. O Superadm também não vê
  o Desenvolvedor.
- Os endpoints `ajax/Settings/getMenus` e `getMenuAccess` (usados pela tela)
  não checam perfil.

**Pontos que a implementação precisa tratar** (levantados, ainda não aplicados):
- **"Retornar às permissões de suporte"** (`header.php`, link fixo
  `users/turnUser/1`): durante o "Ver como", a sessão tem o perfil do usuário
  visto; se `turnUser` exigir Superadm pelo perfil da sessão, o retorno fica
  bloqueado. O retorno deve ser permitido quando `turnBack` estiver na sessão.
  O link fixo para o id 1 também deve virar o id de quem iniciou.
- O **Administrador pode desativar telas do próprio perfil** (inclusive
  Configuração) e se trancar para fora — só um Superadm desfaz. Ver pergunta
  em aberto na proposta abaixo.

**Proposta "sem linha = negado" (item 4) — ⚠️ aguardando aprovação, nada
aplicado**:
- `Secure::individual_menu_access`: sem linha → **libera** só para Superadm,
  Administrador e Desenvolvedor (`access <= 10`); para os demais perfis →
  **nega**. Linha `status = 0` continua negando para todos.
- **Exceções obrigatórias**: `home` (o próprio redirect de bloqueio vai para
  `home`; sem a exceção, um Gerente sem linhas entra em **loop infinito de
  redirecionamento**) e as ações do próprio usuário em `users` (botão
  "Perfil", cartão digital, "Retornar").
- Corrigir junto o N15 (pai aplica o mesmo estado nos submenus).
- Impacto com os dados de hoje, antes do Sandré configurar: Gerentes (4
  usuários) só acessam Home e o próprio perfil; Vendedor e Secretária passam a
  ser bloqueados pela URL exatamente nas telas que já não aparecem no menu
  deles:
  - **Vendedor**: Configuração, Campos Obrigatórios, Cargos, Cartão Digital
    (cadastro), Filiais, Estado Civil, Calendário, Canais de Comunicação,
    Status de Atendimento, Controle de Cheques, Contas a Pagar/Receber,
    Lançamentos a Pagar/Receber, relatórios financeiros, DRE, Bancos, Centro de
    Custo, Contas, Formas de Pagamento, Marca d'água, Pedidos de Compra.
  - **Secretária**: Atendimentos (lista, Calendário, Canais, Status, relatório),
    Profissões, Tipo de Cliente, Configuração, Campos Obrigatórios, Cargos,
    Cartão Digital (cadastro), Filiais, Estado Civil, todo o Financeiro, DRE,
    Bancos, Centro de Custo, Contas, Formas de Pagamento, Marca d'água,
    relatórios Veículos Vendidos/Comprados e Pedidos de Venda.
  - Superadm, Administrador e Desenvolvedor: sem mudança.

**✅ Aprovado e aplicado (2026-10-02)**. Decisões complementares do usuário:
"sem linha = negado" com as exceções Home e ações do próprio usuário; corrigir
o N15; o Administrador **edita o próprio perfil**, mas **não desativa
"Configurações"** dele.
- `UsersController::editItem`: botão "Ver como este usuário" por
  `Secure::access_superAdm()` (antes: nome "Suporte Ydeal").
- `UsersController::turnUser`: com `turnBack` na sessão, só aceita
  `turnUser/{turnBackId}` (retorno a quem iniciou); sem `turnBack`,
  `access_superAdm(true)`. A sessão do "Ver como" passa a guardar `turnBackId`.
  `header.php`: o "Retornar" usa `turnBackId` (antes: id 1 fixo).
- `SettingsController`: aba Menus só para `access_admin()`; `menus()` e
  `updateMenu()` com `access_admin(true)`; `updateMenu` só aceita perfis da
  lista de `getAllUsersProfilesBellow` e recusa, para quem não é Superadm,
  desativar "Configurações" (menu da rota `settings` ou o pai dele) do próprio
  perfil. `ajax/Settings/getMenus` e `getMenuAccess`: mesmas regras, resposta
  JSON "Sem permissão para esta ação.".
- `Secure::individual_menu_access`: sem linha → libera só `access_admin()`;
  demais perfis → `home`.
- `Controller::__construct`: a checagem de menu não roda na rota `home` nem nas
  ações do próprio usuário em `users` (`isOwnUserAction`: `editItem`,
  `handleSubmitEditItem`, `digitalCard`, `handleSubmitDigitalCard`,
  `deleteImageProfileId`, `deleteImageDigitalCardId` com o próprio id, e
  `turnUser/{turnBackId}` durante o "Ver como").
- `PurchaseRequestsController::index`: removida a linha
  `individual_menu_access(true)` (virava menu id 1, inexistente — com a regra
  nova bloquearia a Secretária, que tem Pedidos de Compra liberado).
- N15: `MenuAccess::updateProfileMenu` + `setProfileMenuStatus` (abaixo).
- Docs: `docs/08-autenticacao-permissoes.md` atualizado.

**Testes (2026-10-02)**: harness no scratchpad — sessão de teste em arquivo
(sem banco) + `php-cgi` chamando o `public/index.php` real, **só rotas de
leitura**; "antes" = cópia do HEAD extraída com `git archive`.
- `turnUser/1` pelo endereço: **antes**, Administrador, Vendedor e Gerente Geral
  **viravam Superadm** (sessão do id 1); **depois**, redirecionados e sessão
  intacta. Superadm: "ver como" Vendedor ok; durante o "ver como",
  `turnUser/6` recusado; `turnUser/1` (Retornar) volta ao Superadm.
- Matriz 7 usuários × 22 telas (154 requisições), antes × depois:
  Superadm e Administrador **iguais** (só muda o destino do redirect de
  `users/edit-item/1`, do N14); Gerentes passam a só abrir Home e o próprio
  perfil; Secretária e Vendedor bloqueados exatamente nas telas fora do menu
  deles. Nenhum erro PHP novo (os existentes estão no N16).
- `ajax/Settings`: antes, Vendedor e Secretária liam todas as permissões e o
  Administrador lia as do Superadm; depois, "Sem permissão". Aba Menus aparece
  para Superadm e Administrador.
- N15: lógica antiga × nova com `menu_access` **em memória** (menus lidos do
  banco): antes, desativar "Financeiro" **liberava** os 13 submenus; depois,
  todos seguem o pai.
- **Não executado** (grava no banco): `updateMenu`. Regras conferidas por
  leitura.

### ✅ C4 — Upload de arquivo arbitrário → execução remota de código (RCE) — CONCLUÍDO (etapa 1 2026-09-30, etapa 2 2026-10-01)

**Fontes**: `AUDITORIA-3-seguranca.md` §7 (causa-raiz + achados 7.1-7.4).
**Arquivos**: `public/.htaccess` (+ pastas de upload sem `.htaccess`
próprio), `src/model/VehicleAttachments.php:25-37`,
`src/model/VehicleImages.php:20-52`, `src/libs/FileUploader.php:117-166`
(+ 4 controllers que o chamam: `BillsToPayInstallmentController.php:853`,
`BillReceiveInstallmentController.php:1120`, `AttendanceController.php:818`,
`CustomerController.php:737`), e o padrão "dimensão exata → `copy()` cru"
em `User.php`, `UsersController.php`, `SettingsController.php`,
`BranchController.php`, `DigitalCardController.php`, `CustomerController.php`.

**Risco da correção**: dividir em duas etapas de risco bem diferente:
1. **Causa-raiz (fazer primeiro)** — adicionar `.htaccess` com
   `php_flag engine off` (ou equivalente) em cada pasta de upload dentro de
   `public/`. Risco **BAIXO**: é mudança de infraestrutura, não toca em
   nenhum PHP, e mitiga todos os 4 vetores de uma vez só como defesa em
   profundidade. Único cuidado: confirmar que o `AllowOverride` do Apache
   de produção permite `.htaccess` nessas pastas (se o vhost usar
   `AllowOverride None`, a regra não teria efeito e precisaria ir direto no
   `httpd.conf`/vhost).
   **✅ Etapa 1 CONCLUÍDA 2026-09-30** — com ajuste de abordagem: em vez
   de um `.htaccess` por pasta, a regra foi posta em `public/.htaccess`
   (`RewriteRule ^(img|attachments|vehicle)/...\.php... - [F]`), porque
   `public/attachments/` e `public/vehicle/` pertencem ao usuário do Apache
   e não aceitam escrita sem `sudo`. Usa só `mod_rewrite`, que o próprio
   arquivo já exigia, então não há risco de erro 500 por `AllowOverride`
   restrito em produção. Testado localmente: um `.php` de teste em
   `public/img/` **executava** antes (HTTP 200) e passou a dar 403;
   imagens e rotas do sistema seguem com 200/302 normais, pelas duas
   formas de acesso (com e sem `/public`). Ressalva: se alguma pasta de
   upload ganhar um `.htaccess` próprio com `RewriteEngine`, a regra deixa
   de valer nela.
2. **Whitelist de extensão por vetor** — risco **MÉDIO**: restringir
   extensão aceita pode rejeitar algum tipo de arquivo que hoje "funciona"
   por acidente (extensão não padrão já usada por algum usuário/tela).
   Levantar quais extensões são realmente esperadas em cada tela
   (anexo de veículo, foto de veículo, anexos financeiros, logos) antes de
   travar a whitelist, e testar upload real de cada tipo esperado depois.
   **✅ Etapa 2 CONCLUÍDA 2026-10-01.**
   - **Levantamento** (só leitura): 14 handlers em 12 telas. Nenhum validava
     tipo; todos tiravam a extensão do **nome enviado** (`pathinfo`,
     `getFileExtension` ou `substr(nome, -4)`, que transformava `foto.phtml` em
     `html`). Os nomes de arquivo já eram gerados pelo sistema (`uniqid` ou
     nome fixo + contador), só a extensão vinha do usuário. Banco local e
     pastas só têm dado de teste; o dump/seed não tem nenhum anexo.
   - **Decisões do usuário**: anexos (veículo, Contas a Pagar/Receber,
     cliente, atendimento) = pdf, jpg/jpeg, png, xml, docx, xlsx
     (`application/zip` aceito como MIME **só** para docx/xlsx; zip, heic, txt
     e csv recusados); logos, avatar, cartões e logo do cliente = jpg/jpeg,
     png; marca d'água = png; foto de veículo = jpg/jpeg, png.
   - **Implementado** em `src/libs/FileUploader.php`: whitelists
     `ALLOWED_ATTACHMENT`/`ALLOWED_IMAGE`/`ALLOWED_PNG`;
     `allowedExtension()` confere extensão **e** MIME real (`finfo`), abre a
     imagem (`getimagesize`), recusa `php*`/`phtml`/`phar`/`pht`/`phps`/
     `html`/`htm`/`svg`/`js`/executáveis **em qualquer parte do nome**
     (`foto.php.jpg`) e devolve a extensão normalizada (`jpeg` → `jpg`), que
     é a gravada no disco e no banco; `typeError()` devolve a mensagem do
     Toast (mesmo uso do `sizeLimitError` do M6). Aplicado nos 14 handlers,
     logo depois da checagem de tamanho e antes de qualquer gravação, e na
     extensão gravada por `uploadFiles`, `VehicleAttachments`,
     `VehicleImages`, `User` (avatar), `UsersController` (foto do cartão),
     `DigitalCardController` (fundo/logo, cadastro e edição),
     `CustomerController` (logo), `SettingsController` (5 logos),
     `BranchController` (3 logos) e `WaterMarkController`. Padrão documentado
     em `docs/09-bibliotecas-libs.md` e `docs/03-controllers.md`.
   - **Cabeçalhos dos anexos** (`public/.htaccess`, dentro de
     `<IfModule mod_headers.c>`): `X-Content-Type-Options: nosniff` em
     `attachments/<tipo>/<id>/<arquivo>` e `vehicle/<id>/attachments/<arquivo>`;
     `Content-Disposition: attachment` nesses mesmos caminhos para tudo que não
     é jpg/jpeg/png (pdf, xml, docx, xlsx e também arquivos antigos como
     txt/zip). Expressão presa ao caminho exato, para não pegar rotas como
     `vehicles/attachments/1`. `mod_headers` está ativo no Apache local
     (2.4.56); **precisa ser testado no servidor** (ver `DEPLOY-CHECKLIST.md`
     §3 e §7.2).
   - **Corrigido junto (pedido do usuário)**: avatar 160×160 era gravado como
     `-dc-` em vez de `-profile-` (`User.php`) e a foto não aparecia; avatar,
     foto do cartão, cartão digital (fundo/logo) e logo do cliente gravavam
     `*_ext`/`*_cont` no banco **antes** de salvar o arquivo (o registro
     ficava apontando para arquivo inexistente, ex.: `digital_card.ext_fundo =
     'avif'` sem arquivo). Agora o banco só é atualizado, e o arquivo antigo
     só é apagado, depois que o novo existe. Configurações, Filial e marca
     d'água já gravavam depois.
   - **Teste antes × depois** (sem gravar no banco): 33 arquivos de teste
     (válidos, disfarçados e maliciosos) passados pela lógica antiga e pela
     nova. Antes, todos os caminhos aceitavam `php`, `html`, `svg`, `exe`,
     `js`, `zip`, e as telas de logo (que já checavam `getimagesize`) gravavam
     um JPEG válido com extensão `html`. Depois: aceitos só os tipos decididos,
     com extensão normalizada; recusados php disfarçado de jpg/pdf, `foto.php.jpg`,
     `foto.phtml`, html, svg, js, exe, zip, heic (mesmo com conteúdo JPEG), txt,
     csv, PNG renomeado para `.txt`, JPEG com nome `.png` (e vice-versa), PDF
     vazio e arquivo de zeros. Um JPEG válido com PHP no fim (polyglota)
     passa, mas é gravado como `.jpg` e não executa. `typeError` conferido
     com campo múltiplo, campo vazio, campo ausente e nome com XSS (escapado).
     Redimensionadores com a extensão normalizada: Imagine (fotos de veículo),
     cópia de dimensão exata e `resize1` (marca d'água) ok. Cabeçalhos por
     `curl` antes × depois, com e sem `/public`: 9 anexos mudaram como
     esperado; fotos de veículo, logos, rotas do sistema e login ficaram
     idênticos (16 linhas); bloqueio de `.php` da etapa 1 continua 403.
     **Não executados** (gravam no banco): os 14 handlers; conferido
     estaticamente que a checagem vem antes de qualquer gravação.
     **Limites**: o WideImage local não funciona no PHP 8 (ver N12), então o
     redimensionamento de avatar/logos/cartões fora da dimensão exata não pôde
     ser testado — já falhava antes; XML sem a linha `<?xml ...?>` é visto pelo
     `finfo` como `text/plain` e é recusado.

### ✅ C5 — Registro de pagamento de parcela corrompido (tabelas de rateio de comissão inexistentes, sem transação) — CONCLUÍDO 2026-10-02 (rateio removido + transação opção A)

**Fontes**: `AUDITORIA-2-tecnica.md`, achado CRÍTICO #1.
**Arquivos**: `src/controller/project/BillReceiveInstallmentController.php:564-660`,
`src/model/SalesChargePaymentAgreement.php`,
`src/model/ArrangementPaymentChargesInvoiceReceiveInstallment.php`.

**Risco da correção**: MÉDIO-ALTO. Fluxo financeiro ativo e frequente.
**⚠️ Precisa de decisão do usuário**: as tabelas de rateio de comissão
devem ser criadas (migration nova, já que a feature de "adicionar
participante" existe no código mas está inalcançável pela UI hoje) ou o
trecho de rateio deve ser removido/desativado (já que ninguém usa)? Depois
de decidido, a correção técnica em si — envolver a operação numa transação
(`beginTransaction`/`commit`/`rollBack`) — é padrão já usado em outros
métodos do mesmo arquivo, mas o fluxo de "Registrar Pagamento" precisa ser
testado exaustivamente (parcela integral, parcela parcial, com e sem
cheque) antes e depois, por ser um lançamento financeiro real.

**Reavaliação (2026-10-01) — a premissa da auditoria não vale hoje.** A
auditoria supôs que "toda `bill_receive` nasce de uma venda" na tabela `sales`.
Não é assim: `sales` é a tabela de **vendas de imóveis** e está **vazia**
(0 linhas, assim como `products`, `summary_sale`, `summary_involved` e
`payments_of_sales`); o Pedido de Venda de veículos grava em `sale_requests` e
nenhum fluxo atual grava em `sales`. Logo, a busca da venda no pagamento
(`$sale`) sempre volta vazia e **o trecho do rateio nunca roda** — nem o
`SELECT` em `sales_charge_payment_agreement`, nem a geração de contas a pagar
de comissão por `summary_sale`/`summary_involved`.

**Em produção (`ERRMODE_EXCEPTION`) — verificado.** O `Model` `Sales` tem um
`LEFT JOIN construction_properties` (tabela de imóveis que **não existe** no
banco), mas o query builder só inclui um join quando a consulta usa aquela
tabela. Executadas as consultas reais (só `SELECT`, via os Models da aplicação,
com `ERRMODE_EXCEPTION` forçado, script no scratchpad): a busca da venda do
pagamento, `PaymentsOfSales::checkSaleFromBillReceive` e
`Sales::getSalesForCustomers` (aba Vendas do cliente) **rodam sem erro** e
voltam vazias. Portanto: **registrar pagamento de parcela funciona hoje, em
desenvolvimento e em produção**; o rateio simplesmente não acontece. (Correção:
na análise de 2026-10-01 eu tinha concluído que em produção a página quebraria
antes de gravar — conclusão errada, desfeita por este teste.) O risco real
restante era (1) código morto que quebraria se alguém voltasse a gravar em
`sales`, e (2) falta de transação.

**Para quem era a comissão do rateio**: no sistema imobiliário, ao receber
cada parcela, o sistema dividia a comissão entre o vendedor da venda e outros
cargos cadastrados e criava uma conta a pagar para cada um. **A aba Comissão do
Pedido de Venda não substitui isso**: ela registra uma **conta a receber**
(dinheiro que a loja recebe), em nome do cliente do pedido, com o campo
"Repassador". Nada no fluxo de veículos gera conta a pagar de comissão para
vendedor; se isso for necessário, é uma feature nova (decisão de negócio
separada).

**✅ Aplicado (2026-10-01, decisão do usuário: retirar o rateio herdado)** em
`BillReceiveInstallmentController::handleSubmitPayment`: removidos a busca em
`Sales`, a cópia dos campos de comissão do vendedor para a parcela nova, o
`foreach` de `SalesChargePaymentAgreement` →
`ArrangementPaymentChargesInvoiceReceiveInstallment` e o bloco `if ($sale)`
que gerava contas a pagar de comissão (vendedor e cargos); removidos os 8 `use`
que ficaram sem uso. **Comportamento atual não muda** (o trecho nunca rodava).
Mantidos: a chamada a `PaymentsOfSales::submitStatusOfPaymentFromAnInstallment`
(também usada em editar, estornar, cancelar e ativar parcela; volta sem fazer
nada porque `sales` está vazia — limpeza junto com B1), os Models
`SalesChargePaymentAgreement`/`ArrangementPayment…` e
`ajax/PaymentAgreementController` (sem chamador na UI — B1). Verificação:
`php -l` ok, nenhuma referência restante a `$sale`. O método grava no banco e
não foi executado.

**⚠️ Pendente — transação.** Cada `Model` abre **a sua própria conexão PDO**
(`Model::__construct`). Uma transação em `$this->model->db` (padrão do
`handleSubmitReverseInstallment`, no mesmo arquivo) só cobre o que é gravado
por `$this->model` (parcela nova, parcela paga, `id_check`); cheque
(`CheckControl`), linha do tempo do cheque e log de saldo do cliente usam
outras conexões e ficariam fora. Além disso, em desenvolvimento
(`ERRMODE_WARNING`) erro de gravação não lança exceção — é preciso conferir o
`->error` de cada gravação e lançar manualmente. Opções:
- **A** (padrão existente): transação em `$this->model->db` + checagem de
  `->error` nas gravações da parcela. Protege o principal (parcela dividida pela
  metade), não o cheque.
- **B** (estilo novo): no método, fazer os outros Models usarem a mesma conexão
  (`$m->db = $this->model->db`), cobrindo tudo. É uma forma nova no projeto —
  precisa de aprovação (regra 2 do CLAUDE.md). Atenção: `CustomerBalanceLog::insertLogPay`
  (forma de pagamento 9, crédito de fornecedor) abre a **própria** transação e
  grava o saldo do cliente por outro `Model` (`Customer`); com conexão
  compartilhada daria "transação já ativa" — teria de ficar fora ou ser adaptado.

**✅ Decisão do usuário (2026-10-02): opção A. Aplicada.**
`handleSubmitPayment`: `beginTransaction` em `$this->model->db` antes da parcela
nova; `->error` da parcela nova e da atualização da parcela paga lança
`PDOException`; `commit` logo depois dessas duas gravações; no `catch`,
`rollBack` se a transação estiver aberta. Efeito colateral corrigido: antes, se
a atualização da parcela falhasse, a tela mostrava "Pagamento registrado com
sucesso" mesmo assim; agora mostra erro e desfaz a parcela nova.
**Limitação registrada**: cheque (`CheckControl`), linha do tempo do cheque,
log de saldo do cliente (forma 9) e o vínculo `id_check` da parcela são gravados
**depois do commit**, fora da transação — se um deles falhar, o pagamento da
parcela fica gravado sem o cheque/saldo correspondente. Gravá-los antes do
commit, por outra conexão, também travaria o InnoDB (o cheque referencia a
parcela bloqueada pela transação). Tabelas confirmadas InnoDB. **Teste**: `php -l`
ok; telas `bill-receive-installment/edit/1` e `/5` abrem sem erro; o
"Registrar Pagamento" **não foi executado** (grava no banco).

### ✅ C6 — Token de redefinição de senha nunca invalidado (tabela errada) — CONCLUÍDO 2026-09-30

**Fontes**: `AUDITORIA-2-tecnica.md`, achado CRÍTICO/ALTO #2 (contexto de
risco ampliado por `AUDITORIA-3-seguranca.md` §4.3, que nota a combinação
com a ausência de rate-limiting no login).
**Arquivos**: `src/controller/project/LoginController.php:179`.

**Risco da correção**: BAIXO. Trocar `"token"` por `"tokens"` e adicionar
`try/catch` ao método — mudança pequena e localizada. Testar o fluxo
completo (solicitar recuperação → usar o link → confirmar que o link não
funciona mais numa segunda tentativa) antes de considerar resolvido.

### C7 — Servidor de e-mail (SMTP) do sistema é do fornecedor antigo

**Fontes**: `AUDITORIA-4-identidade.md` §1.1.
**Onde**: tabela `configuracao_email` (dado, não código) +
`src/libs/MoreMailer.php`, `src/model/Mail.php`.

**Risco da correção**: BAIXO tecnicamente (é um `UPDATE` de configuração
via migration). **⚠️ BLOQUEADO** — precisa que o usuário informe o
SMTP/e-mail de envio real da Repasse Sandré antes de qualquer migration ser
gerada. Como hoje não existe nenhuma tela de admin para editar essa
configuração, vale decidir junto se a correção deste incidente já deve
incluir criar essa tela (escopo maior) ou só corrigir o dado via migration
por enquanto.

---

## ALTO

### A1 — CSRF ausente em todo o sistema

**Fontes**: `AUDITORIA-3-seguranca.md` §3.

**Risco da correção**: ALTO — a correção mais invasiva de todo este plano.
Implica adicionar geração e validação de token em praticamente todo
formulário POST do sistema (~445 forms catalogados pela Auditoria 2), o que
toca virtualmente toda view e todo controller de escrita. Um mecanismo
central (token único validado automaticamente no `Controller`/`Ajax` base,
em vez de em cada view manualmente) reduz bastante esse risco, mas ainda
assim exige teste extensivo de todo o sistema depois — recomenda-se rollout
incremental (ex.: por módulo) em vez de uma mudança única.

### A2 — Bug de roteamento: link de 2 segmentos chama método com nome literal do id

**Fontes**: `AUDITORIA-2-tecnica.md`, achados ALTO #3 e #4.
**Arquivos**: `src/view/notification/index.php:21`,
`src/view/users/digital-card.php:80`, `src/controller/project/CardPDFController.php`,
`src/core/Application.php:35-66` (causa-raiz, opcional).

Hoje quebra 100%: clicar em qualquer notificação, e gerar o PDF do Cartão
Digital.

**Risco da correção**: BAIXO se corrigidos os 2 links específicos (trocar
para URL de 3 segmentos, ex. `notification/view/45`, criando o método
correspondente no controller). MÉDIO se decidir corrigir a causa-raiz no
roteador (`Application.php`) — mudança central que afeta toda URL do
sistema; não recomendado sem suíte de testes automatizados cobrindo rotas,
dado que o projeto não parece ter testes automatizados hoje.

### A3 — Stored XSS em múltiplos módulos (comentários e descrições de texto livre)

**Fontes**: `AUDITORIA-3-seguranca.md` §2.1, §2.2, §2.3, §2.4, §2.6, §2.7.
**Arquivos**: `AttendanceController.php` (comentário de timeline, descrição
de atendimento) + `call-center/timeline.php`, `call-center/global.php`,
`editAttendance.php`; `CheckControlController.php` (comentário de cheque) +
`check-control/edit.php`; `RecordVehicleHistoryController.php` (observação
de transferência) + `vehicleTransfer.php`; descrição de lançamento
financeiro em `bill-receive/entry.php`, `bills-to-pay/entry.php`,
`bill-receive-installment/edit.php`, `bills-to-pay-installment/edit.php` e
os 4 relatórios/impressões correspondentes; `vehicles/attachments.php` e
`photos.php` (descrição de anexo/foto).

**Risco da correção**: BAIXO-MÉDIO por ponto (envolver a variável em
`htmlspecialchars()` na view é mudança mínima e localizada), mas precisa
ser feito em **todos os pontos de uma vez** — corrigir só parte deixa XSS
ativo em outro lugar do mesmo tipo de dado. Antes de aplicar em massa,
confirmar caso a caso que nenhum desses campos depende hoje de HTML sendo
interpretado de propósito (ex.: algum comentário automático do sistema que
insere `<a>`/`<strong>` formatado, não digitado pelo usuário) — nesse caso
usar uma função de escape seletivo em vez de `htmlspecialchars()` cru.

### A4 — Login sem rate-limiting/lockout contra força bruta

**Fontes**: `AUDITORIA-3-seguranca.md` §4.2.
**Arquivos**: `src/controller/project/LoginController.php:45-101`.

**Risco da correção**: MÉDIO. Precisa de nova coluna/tabela para contar
tentativas (migration) mais lógica no `signIn()`. Risco real de bloquear
usuário legítimo por engano se o limite for mal calibrado (ex.: usuário que
erra a senha 3x seguidas por esquecimento normal) — calibrar o limite e o
tempo de bloqueio com o usuário, e testar o fluxo de "esqueci a senha"
combinado com o lockout (recuperar senha deveria resetar o contador).

### ✅ A5 — Controllers administrativos/financeiros sem checagem de perfil consistente — CONCLUÍDO 2026-10-02

**Fontes**: `AUDITORIA-3-seguranca.md` §5.3 (`BranchController`), §5.4
(`CostCenterController`). *Ver também grupo M2, mesmo padrão em
controllers de menor criticidade — considerar corrigir tudo na mesma
varredura.*

**Risco da correção**: BAIXO-MÉDIO. Adicionar `Secure::access_admin(true)`
nos métodos que faltam (`BranchController` inteiro; `handleSubmitAddItem`/
`disableItem`/`enableItem` de `CostCenterController`) é mecânico, mas
**⚠️ precisa confirmar com o usuário** qual perfil deveria de fato poder
criar/desativar filial e centro de custo — hoje isso nunca foi decidido
explicitamente no código (a inconsistência dentro do próprio
`CostCenterController` é sinal disso).

**Decisão do usuário (2026-10-01)**: Filial e Centro de Custo **só
Administrador e Superadm** (e Desenvolvedor, igual ao Superadm). **Filiais e
DRE devem aparecer no menu do Administrador** — hoje `Controller::assembleMenu`
esconde os ids 7 e 59 de todos, exceto Superadm no modo "todas as filiais".
Estado hoje: `BranchController` não tem **nenhuma** checagem (qualquer perfil
cadastra, edita, ativa/desativa filial, imagens, cargos, arranjo de pagamento
pela URL); `CostCenterController` exige admin em listar/editar/clonar, mas não
em cadastrar/ativar/desativar.

**✅ Aplicado (2026-10-02)**: `BranchController` com `Secure::access_admin(true)`
no construtor (mesmo padrão do `CountriesController`), cobrindo todas as ações;
`CostCenterController::handleSubmitAddItem`, `disableItem` e `enableItem` com
`access_admin(true)`; `Controller::assembleMenu` mostra Filiais (7) e DRE (59)
para `access_admin()` também dentro de uma filial. `ajax/costCenter`
(árvore de centros de custo, só leitura) não foi alterado. **Teste**: Administrador
abre Filiais, Filial 1 e Centro de Custo igual a antes, e agora vê Filiais e DRE
no menu lateral (antes não via); Vendedor continua sem.

### ✅ A6 — Recibo financeiro corrompido para cliente PJ/cadastro incompleto — CONCLUÍDO 2026-09-30

**Fontes**: `AUDITORIA-2-tecnica.md`, achado ALTO #5.
**Arquivos**: `src/model/Contract.php` (`getInstalmentReceivePrintReceipt`,
`getInstalmentToPayPrintReceipt`).

**Nota de execução (2026-09-30)**: ao preparar o teste manual, confirmei
que o cenário é **menos comum do que a Auditoria 2 descreveu**: o formulário
de cadastro de cliente (`customer/add.php`) sempre envia profissão (padrão
"AUTÔNOMO") e estado civil (padrão id 1) como campos obrigatórios, inclusive
para PJ, e hoje nenhum cliente no banco tem esses campos nulos. O bug só
aconteceria com dado importado/migrado sem esses campos. A correção continua
válida como defesa, mas não é reproduzível pela tela hoje.

**Risco da correção**: BAIXO. Trocar os dois `INNER JOIN` (profissão,
estado civil) por `LEFT JOIN` é mudança pequena e seguramente mais
permissiva, não mais restritiva. Testar a geração do recibo para os 3
cenários (PF completo, PF incompleto, PJ) antes e depois, por ser documento
financeiro entregue a terceiros.

### ✅ A7 — String PHP colada literalmente como JS (botão de excluir veículo quebrado) — CONCLUÍDO 2026-09-30

**Fontes**: `AUDITORIA-2-tecnica.md`, achado ALTO #6.
**Arquivos**: `public/js/v_01/purchase-requests/purchaseRequests.js:300,326`,
`public/js/v_01/sale-requests/saleRequests.js:359,385`.

**Risco da correção**: BAIXO. Trocar a string fixa por um template literal
JS com a variável real (ex. `id`/`rota` já disponíveis no escopo do
callback) — mudança isolada em 4 linhas. Testar manualmente finalizando um
pedido de compra/venda e conferindo o botão "excluir".

### ✅ A8 — Link de relatório cruzado (Contas a Receber abre Contas a Pagar) — CONCLUÍDO 2026-09-30

**Fontes**: `AUDITORIA-2-tecnica.md`, achado ALTO #7.
**Arquivos**: `src/view/record-bill-receive-installment/index.php:196`.

**Risco da correção**: BAIXO. Troca de uma palavra no `href`
(`bills-to-pay-installment` → `bill-receive-installment`). Testar o
relatório de Contas a Receber depois.

**Nota de execução (2026-09-30)**: a primeira versão da correção manteve o
método `editItem` do link original, mas o controller de Contas a Receber
chama esse método de `edit` (só o de Contas a Pagar usa `editItem`) — o
teste manual pegou um fatal error. Corrigido para
`bill-receive-installment/edit/{id}`, mesmo padrão já usado por todos os
outros links do sistema para parcela a receber.

### A9 — Contato de suporte hardcoded do fornecedor antigo (telefone + e-mail de filial)

**Fontes**: `AUDITORIA-4-identidade.md` §1.2 (telefone), §1.3 (e-mail da
filial).
**Arquivos**: `src/view/_templates/header.php:56`, `src/core/Model.php:18`
(telefone), `branch.email` + `db/migrations/2026_09_25_1000_seed_inicial_producao.sql`
(e-mail).

**Risco da correção**: BAIXO tecnicamente. **⚠️ BLOQUEADO** — precisa que o
usuário informe o telefone de suporte e o e-mail de contato reais da
Repasse Sandré. Corrigir o código (`header.php`, `Model.php`) e o dado
(`branch.email`, inclusive na migration de seed de produção que ainda não
rodou) na mesma leva, para não corrigir um e esquecer o outro.

### A10 — Bypass de parametrização via chave `'json'` em `GerenciaPost`

**Fontes**: `AUDITORIA-3-seguranca.md` §1.3.
**Arquivos**: `src/model/GerenciaPost.php:19-48,50-74`,
`src/controller/project/AttendanceController.php:604-653,696`
(`handleSubmitInterestFilter`, `deleteInterestFilterById`).

**Risco da correção**: MÉDIO se mexer na lib genérica (`GerenciaPost` é
usada em muitos lugares — qualquer chamada existente com chave `json` seria
afetada, precisa mapear todas antes). BAIXO se a correção for pontual só no
`AttendanceController` (ex.: validar/sanitizar `id_brand`/`id_model` antes
de montar o JSON, sem tocar na lib) — abordagem recomendada por ser mais
isolada.

### ✅ A11 — Vazamento de erro PDO sem gate de ambiente (Contas a Pagar/Receber) — CONCLUÍDO 2026-09-30

**Fontes**: `AUDITORIA-3-seguranca.md` §8.1.
**Arquivos**: `src/model/BillsToPay.php:256-259`,
`src/model/BillReceive.php:296-300`.

**Risco da correção**: BAIXO. Envolver com o mesmo padrão
`if (ENVIRONMENT === 'development')` já usado em ~20 pontos irmãos do
próprio projeto — copiar o padrão existente, não inventar um novo.
Descomentar/adicionar o `exit;` que falta em `BillReceive.php`.

---

## MÉDIO

### ✅ M1 — Aba "Vendas" do cliente inacessível (Sales↔products) + link de edição sem controller — CONCLUÍDO 2026-10-01 (aba escondida)

**Fontes**: `AUDITORIA-1-dominio.md` achado 1 (principal) +
`AUDITORIA-2-tecnica.md` achado MÉDIO #9 (mesmo tema, link de edição).
**Arquivos**: `src/model/Sales.php:15-84`,
`src/controller/project/CustomerController.php:91,109-111`,
`src/view/customer/sales.php`.

**Risco da correção**: **⚠️ BLOQUEADO** — decisão de negócio necessária
antes de estimar risco técnico com precisão: a tela deveria passar a usar
`vehicles` (rótulo "Veículo") ou ser removida por completo? Depois de
decidido, o risco técnico é MÉDIO: o `INNER JOIN products` está no
**construtor** do Model `Sales`, então afeta qualquer query feita através
dele — trocar/remover pode revelar vendas que hoje "somem" silenciosamente
em outros relatórios que também usam esse Model, não só na aba Vendas.
Testar todos os usos de `Sales` (não só `getSalesForCustomers`) antes de
fechar a correção.

**✅ CONCLUÍDO 2026-10-01 — decisão do usuário: esconder a aba, sem remover
código.** Na prática a aba já não aparecia: só era montada se
`getSalesForCustomers()->count != 0`, e `sales` está vazia. Aplicado em
`CustomerController::navTabs` o mesmo padrão da aba "Fotos" do veículo: a aba
saiu da navegação (comentário no lugar) e a contagem em `Sales` deixou de ser
executada a cada aba do cliente. **Mantidos**: o método `sales()`, a rota
`customer/sales/{id}`, a view e o `Model` `Sales` (o `INNER JOIN products` e o
`LEFT JOIN construction_properties` continuam lá, sem efeito porque o query
builder só inclui joins usados — ver C5). `php -l` ok.

### ✅ M2 — Demais controllers sem checagem de perfil (Veículos, DRE, Moedas, Atendimento) — CONCLUÍDO 2026-10-02 (Moedas fica no M12/N17)

**Fontes**: `AUDITORIA-3-seguranca.md` §5.5 (`VehiclesController`), §5.6
(`ReportDreController`), §5.7 (`ReportAttendanceController`, ver também
grupo B5); `AUDITORIA-2-tecnica.md` achado MÉDIO #10 (`Currencies`, falta
de checagem, além da tabela inexistente — ver grupo M11 pra parte de
schema). *Mesma causa-raiz do grupo A5 — considerar corrigir na mesma
varredura.*

**Risco da correção**: BAIXO-MÉDIO, mesmo padrão do grupo A5 — mas primeiro
decidir com o usuário qual perfil deveria acessar cada tela (Compra/Custos
de veículo, DRE, Moedas, Relatório de Atendimento).

**Decisões do usuário (2026-10-01)**:
- **DRE**: só Administrador e Superadm, e **visível no menu do Administrador**
  (ver A5).
- **Compra/Custos de veículo**: só Administrador e Superadm "por enquanto" —
  as abas já só aparecem para admin, mas `purchaseVehicles`,
  `handleSubmitAddPurchase`, `vehicleCosts` e os 5 endpoints
  `ajax/Vehicles/*Cost*`/`getCustomerById` não checam perfil.
- **Relatório de Atendimento (B5)**: segue o menu (hoje liberado para
  Superadm, Administrador e Vendedor); o Sandré ajusta depois pela tela.
  Depende da proposta "sem linha = negado" do C3.
- **Moedas**: já decidido no M12 (Onda 4).
- Mesma lógica ("o servidor segue o que a tela permite", só Admin): excluir
  anexo de atendimento (sobra do N2), excluir anexo de veículo (N13) e
  endpoints de leitura de cheques (N10, 3º item).

**✅ Aplicado (2026-10-02)**:
- `ReportDreController`: `access_admin(true)` no construtor.
- `VehiclesController`: `access_admin(true)` em `purchaseVehicles`,
  `handleSubmitAddPurchase`, `vehicleCosts` e `handleDeleteAttachment` (N13).
- `ajax/VehiclesController`: os 5 métodos (`getCustomerById`, `addCosts`,
  `getCosts`, `editCostList`, `deleteCost`) são todos da aba Custos — recusa
  JSON no construtor.
- `AttendanceController::handleSubmitDeleteAttachment` (sobra do N2):
  `access_admin(true)` como 1ª instrução.
- `ajax/CheckControlController` (N10, 3º item): recusa JSON no construtor para
  quem não é admin (os 7 métodos: as 6 leituras + `addNewInstallment`, que já
  exigia admin). `getBillReceiveById` continua sem chamador e sem efeito
  (limpeza no B1).
- Relatório de Atendimento (B5): sem código — segue o menu pela regra "sem linha
  = negado" do C3.
**Teste antes × depois**: Secretária e Vendedor abriam as abas Compra e Custos
do veículo e liam custos e cheques pelos endpoints; agora são redirecionados ou
recebem "Sem permissão". Administrador: mesmas telas e respostas de antes.
**Não executados** (gravam): exclusão de anexo de atendimento e de veículo,
`handleSubmitAddPurchase`, adicionar/editar/excluir custo, cadastrar e
ativar/desativar centro de custo — checagem conferida por leitura como 1ª
instrução (ou no construtor).

### M3 — XSS de alcance/severidade menor

**Fontes**: `AUDITORIA-3-seguranca.md` §2.5 (observação do cliente), §2.8
(DOM-based via AJAX), §2.9 (notificações, dormente).

**Risco da correção**: BAIXO — mesmo tratamento do grupo A3, em pontos de
menor alcance. Recomenda-se corrigir na mesma leva que A3 por serem a mesma
classe de problema.

### M4 — Falta de política de senha + chave JWT hardcoded

**Fontes**: `AUDITORIA-3-seguranca.md` §4.1 (política de complexidade),
§4.3 (chave JWT).
**Arquivos**: `src/view/users/add.php:40-48`/`edit.php:45-54`,
`src/model/User.php`, `src/libs/JWTWrapper.php:9`.

**Risco da correção**: BAIXO. Validação de tamanho mínimo no
cadastro/edição de usuário é mudança isolada e segura. Mover a chave JWT
para variável de ambiente/config é mecânico, mas **invalida qualquer link
de recuperação de senha já enviado e não usado** no momento do deploy —
comunicar isso ao time antes de trocar, ainda que o risco técnico seja
baixo.

### ✅ M5 — Config/debug: blocos dependentes de ambiente de produção não verificável — CONCLUÍDO 2026-09-30 (parte de código)

**Nota de execução**: `else` adicionado em `config.example.php` (versionado)
e no `config.php` local. Em vez de `error_reporting(0)`, o `else` desliga só
a **exibição** (`display_errors=0`) e mantém o **log** (`log_errors=1`), para
os erros continuarem diagnosticáveis no log do servidor. **Pendente, fora do
código**: o `config.php` de produção não é versionado — aplicar o mesmo
`else` lá manualmente e confirmar que `ENVIRONMENT` está como `'production'`.

**Fontes**: `AUDITORIA-3-seguranca.md` §8.2, §8.3.
**Arquivos**: ~20 blocos em `src/model/*.php`, `src/config/config.php:16-19`.

**Risco da correção**: BAIXO. Adicionar o `else` explícito desligando
`display_errors`/`error_reporting` fora de dev é mudança pequena e
seguramente correta — não depende de decisão de negócio. O item dos ~20
blocos "gated" não é uma correção de código em si; é uma recomendação de
**confirmar com o usuário** qual é o `ENVIRONMENT` real do `config.php` de
produção (não está neste repositório, que não é versionado).

### ✅ M6 — Sem limite de tamanho de upload + sem hardening de cookie de sessão — CONCLUÍDO 2026-09-30

**Limite de upload (decidido pelo usuário)**: fotos de veículo 8 MB;
logos/avatar/cartão digital 2 MB; anexos de veículo e financeiros 20 MB.
Verificação única em `FileUploader::sizeLimitError()` (constantes
`MAX_SIZE_VEHICLE_PHOTO`/`MAX_SIZE_IMAGE`/`MAX_SIZE_ATTACHMENT`), chamada no
início de 11 handlers, **antes** de qualquer gravação: Vehicles (fotos,
anexos), BillsToPay/BillReceive Installment (anexos), Users (avatar, foto do
cartão), Settings (5 logos), Branch (3 logos), DigitalCard (fundo/logo, add e
edit), Customer (logo). Mensagem com nome do arquivo (escapado) e o limite;
se o `upload_max_filesize` do servidor for menor que o limite do sistema, a
mensagem usa o menor. **Complemento (decisão do usuário)**: marca d'água 2 MB
(`WaterMarkController`), anexos de cliente e de atendimento 20 MB
(`CustomerController::handleSubmitAddAttachment`,
`AttendanceController::handleSubmitAddAttachments`) — agora os 14 handlers
de upload do sistema têm limite. **Limitação**: se o
envio inteiro passar do `post_max_size` do servidor (40M local), o PHP descarta
o formulário antes de chegar ao código e aparece o erro genérico "Tente
novamente".

**Nota de execução**: flags de cookie aplicadas em `public/index.php` (versionado,
roda antes de todos os `session_start()`): `HttpOnly`, `SameSite=Lax`, e `secure`
**só quando a requisição é HTTPS** (inclusive atrás de proxy via
`X-Forwarded-Proto`) — ligar `secure` sempre quebraria o login em `http://`.
Verificado no cabeçalho `Set-Cookie`. Nenhum JS do projeto lê o cookie de
sessão, então `HttpOnly` não quebra nada.

**Fontes**: `AUDITORIA-3-seguranca.md` §7.5, §9.

**Risco da correção**: BAIXO. Ambas são adições (cap de tamanho no código,
`session_set_cookie_params` com `httponly`/`secure`/`samesite`) sem remover
nada existente. Testar upload de arquivo grande e o ciclo login/logout
depois da mudança.

### M7 — Assets/links quebrados dormentes (favicon fallback, prefixo `public/` inconsistente)

**Fontes**: `AUDITORIA-2-tecnica.md`, achados MÉDIO #11, #12.
**Arquivos**: `src/core/Controller.php:71`,
`src/view/vehicles/attachments.php:56`.

**Risco da correção**: BAIXO. Corrigir caminho/prefixo é mudança de uma
linha cada; ambos dormentes hoje (só afetam cenário de fallback/vhost
específico ainda não usado em produção) — testar no ambiente de deploy real
(não só XAMPP local), já que o achado #12 é justamente sobre diferença de
topologia de servidor.

### ✅ M8 — `$_POST['created_by']` sem `isset()` no cadastro de Atendimento — CONCLUÍDO 2026-09-30

**Nota de execução**: em vez de `?? null` (que ainda gravaria `NULL`, que é
justamente o que esconde o atendimento), a sobrescrita agora só acontece se
vier valor (`!empty($_POST['created_by'])`); sem valor, fica o usuário logado,
que já era o padrão da linha 157.

**✅ Edição também corrigida (2026-09-30, pedido do usuário)**:
`handleSubmitEditAttendance()` (`AttendanceController.php:402`) agora só
troca o dono se vier valor; campo vazio não entra no `UPDATE` e o dono atual é
mantido. **✅ Inconsistência resolvida (decisão do usuário)**: Secretária,
Gerentes e Administrador podem reatribuir — o controller de edição passou de
`access_admin()` para `access_secretary()`, a mesma regra que a tela já usava
para mostrar o campo (Vendedor continua sem o campo e sem poder trocar).

**Fontes**: `AUDITORIA-2-tecnica.md`, achado MÉDIO #13.
**Arquivos**: `src/controller/project/AttendanceController.php:168-170`.

**Risco da correção**: BAIXO. Adicionar `?? null`/`isset()`. Testar
cadastro de atendimento logado como perfil "Secretária".

### M9 — Identidade residual de baixo risco (link morto no Cartão Digital + dados órfãos de config)

**Fontes**: `AUDITORIA-4-identidade.md` §1.5 (link `localhost` morto),
§2 (dados órfãos em `configuracao`/`texto`).

**Risco da correção**: BAIXO. Remover o bloco condicional do link morto no
PDF do Cartão Digital é mudança isolada e seguramente segura (hoje já é um
link quebrado; removê-lo só melhora a experiência). Limpar os campos
órfãos de `configuracao`/`texto` é opcional — nenhum código lê essas
colunas hoje, então não afeta nada em produção; pode virar uma migration de
higiene de baixa prioridade quando sobrar tempo.

### M10 — Digital-card: nomenclatura "creci" confusa em feature ativa

**Fontes**: `AUDITORIA-1-dominio.md` achado 2.
**Arquivos**: `src/controller/project/DigitalCardController.php`,
`src/view/digital-card/add.php`/`edit.php`.

**Risco da correção**: MÉDIO se decidir renomear os campos (`fonte_creci`
estiliza de verdade a legenda do rodapé do cartão hoje — renomear exige
migration de coluna + atualizar coerentemente 4 arquivos, testando a
geração do cartão digital depois). BAIXO/zero se optar só por documentar a
confusão sem renomear (nenhuma ação de código).

### ✅ M11 — Templates de contrato imobiliário acessíveis via manipulação de POST — CONCLUÍDO 2026-09-30

**Nota de execução**: em vez de mudar a consulta, a validação foi feita logo
após buscar o modelo, nos dois `printReceipt()`: se o id não existir, não for
`type_contract = 4` (mesmo filtro do dropdown) ou estiver com `status = 0`,
mostra "Modelo de recibo inválido." e volta para a parcela
(`bill-receive-installment/edit/{id}` / `bills-to-pay-installment/editItem/{id}`,
nomes de rota conferidos). `redirect()` encerra com `exit`, então o contrato
não chega a ser montado.

**Fontes**: `AUDITORIA-4-identidade.md` §3.
**Arquivos**: `BillReceiveInstallmentController.php:962`,
`BillsToPayInstallmentController.php:699`, `src/model/StandardContract.php`.

**Risco da correção**: BAIXO. Adicionar `type_contract = 4 AND status = 1`
na busca do `standard_contract` dentro de `printReceipt()` (nos dois
controllers) é mudança pequena e puramente defensiva — restringe uma opção
que já não deveria estar disponível, não remove nada que funcione hoje.
Testar emissão de recibo normal ("Recibo Simples") depois.

### M12 — `CurrenciesController`: tabela inexistente (decisão de escopo, além da checagem de perfil do grupo M2)

**Fontes**: `AUDITORIA-2-tecnica.md`, achado MÉDIO #10 (parte de schema).

**Risco da correção**: **⚠️ BLOQUEADO** — decidir se a feature de moedas
deve ser implementada de verdade (criar a tabela `currencies` via migration
+ terminar o CRUD) ou removida por completo (controller, model, rotas
órfãs). Não é uma correção de bug, é uma decisão de escopo de produto.

**✅ Decisão do usuário (2026-10-01, revista no mesmo dia)**: **esconder a
tela de Moedas do menu e bloquear a rota** (Onda 4). A remoção completa das
referências fica como **limpeza opcional** pós-lançamento.

**Estado hoje (levantado 2026-10-01)**:
- Menu: o dump antigo `db/realize_repasse.sql` tem o item **id 74 "Moedas"**
  (rota `currencies`, `access` 5 = só Superadm, `status` 1, pai 77). No
  banco local e nos dois seeds de produção esse item **não existe**; em
  produção, conferir. Esconder = migration com `UPDATE menu SET status = 0
  WHERE route = 'currencies'` (sem efeito se a linha não existir; rollback
  `status = 1`), a ser escrita na execução.
- Rota: `CurrenciesController` **não tem nenhuma checagem de perfil** (só
  `check_post_method` nos dois `handleSubmit*`); qualquer usuário logado abre
  `currencies/` e cai no erro da tabela inexistente. Bloquear = todos os
  métodos públicos redirecionarem para `home` logo na entrada (ex.: no
  `__construct`, antes de instanciar o model), sem apagar arquivos. Forma
  exata a definir na execução, seguindo o padrão de redirect do projeto.

**Limpeza opcional — levantamento das referências**: a tabela
`currencies` e um item de menu para ela não existem no banco local, mas 16
arquivos citam moeda — além de `CurrenciesController`, `Currencies` (model) e
`src/view/currencies/`, também **Países** (`CountriesController`, `Countries`
model e as 3 views de `countries/`, com o filtro `currency_name`),
`Util.php:244` (`use RR\model\Currencies`), `AjaxController`
(`getValueCurrency`, já listado no N3), `ajax/PaymentAgreementController`,
`SalesChargePaymentAgreement`, `SummarySale` e `script.js`/`application.js`.
Conferir cada um antes de apagar: a parte de Países está em uso.

---

## BAIXO

### B1 — Código morto referenciando domínio imobiliário/tabelas inexistentes

**Fontes**: `AUDITORIA-1-dominio.md` §3 (7 métodos +
`payments_of_sales.id_property`), `AUDITORIA-2-tecnica.md` §14-15 (nomes de
tabela errados em código morto: `Sales.php`, `SettingsSite.php`,
`CustomerType.php`, `Contract.php:13`).

**Risco da correção**: MUITO BAIXO — todos com zero chamadores confirmados
por grep em múltiplas auditorias independentes. Único cuidado: rodar
`grep` de novo imediatamente antes de apagar, já que o código pode ter
mudado entre a auditoria e a correção.

### B2 — Resíduo de schema órfão no banco (sem código nenhum referenciando)

**Fontes**: `AUDITORIA-1-dominio.md` §4 (6 tabelas/colunas:
`variable_property_authorization_contract`, `standard_contract_variables`,
`website_filters`, cluster de 5 tabelas `presentation_*`,
`branch.immovable_record`, `customer.creci`).

**Risco da correção**: **⚠️ BLOQUEADO para `DROP` de tabela/coluna** —
operação irreversível (perda de dado, inclusive as 7 linhas reais de
`website_filters`); precisa confirmação explícita do usuário antes de
qualquer migration de remoção ser sequer redigida, mesmo com zero código
apontando pra elas hoje (regra do CLAUDE.md vale ainda mais para operação
destrutiva). Manter documentado como loose end é a opção de risco zero.

**✅ Decisão do usuário (2026-10-01): manter documentado, não dropar.**
Nenhuma migration de remoção será escrita.

### B3 — Namespace/case inconsistente (`ManagerPost.php`, 3 controllers ajax)

**Fontes**: `AUDITORIA-2-tecnica.md` §17, §18.

**Risco da correção**: MUITO BAIXO. `ManagerPost.php` é código morto
(corrigir o case é só profilaxia contra reativação futura em produção
Linux). Os 3 controllers ajax funcionam hoje por acaso (o roteamento monta
a string sempre em minúsculo, então o `namespace` declarado dentro do
arquivo nunca é comparado) — corrigi-los não muda nenhum comportamento
observável.

### B4 — Views/scripts órfãos confirmados inofensivos

**Fontes**: `AUDITORIA-2-tecnica.md` §19 (`sync-folder/index.php`,
`spouse.js` raiz, `application.js`, `inputStar.js`), `AUDITORIA-1-dominio.md`
(observação incidental sobre `notice/index.php`).

**Risco da correção**: MUITO BAIXO. Confirmado por múltiplas auditorias
independentes que nada os inclui.

### B5 — `ReportAttendanceController` sem checagem de perfil

**Fontes**: `AUDITORIA-3-seguranca.md` §5.7. *Mesmo padrão do grupo M2 —
considerar corrigir junto.*

**Risco da correção**: BAIXO — mesma correção do grupo M2, menor
sensibilidade (não é dado financeiro).

### B6 — Bibliotecas de upload legadas perigosas se reativadas

**Fontes**: `AUDITORIA-3-seguranca.md` §7.6.
**Arquivos**: `src/libs/UploadFiles.php`.

**Risco da correção**: MUITO BAIXO — código morto (zero chamadores
confirmados por 3 auditorias diferentes). Seguro remover o arquivo inteiro
ou documentar como "nunca reativar sem reescrever".

### B7 — `Home.php::compareStructureFromTwoDatabase` — credencial hardcoded + auto-DDL perigoso

**Fontes**: `AUDITORIA-3-seguranca.md` §8.4.
**Arquivos**: `src/model/Home.php:152-283`.

**Risco da correção**: MUITO BAIXO — inalcançável hoje (zero chamadores).
Mesmo padrão do `CompareDatabases.php` já removido antes; seguro remover.

### B8 — Credencial/config de baixo risco (token Facilvel, `config.example.php`)

**Fontes**: `AUDITORIA-3-seguranca.md` §6, §8.4.

**Risco da correção**: BAIXO. Mover o token Facilvel para variável de
ambiente é mecânico; integração está documentada como adiada, então zero
urgência funcional. Trocar as credenciais de exemplo em
`config.example.php` é cosmético.

### B9 — `composer.json` com nome de fornecedor/cliente anterior

**Fontes**: `AUDITORIA-4-identidade.md` §5.

**Risco da correção**: MUITO BAIXO — metadado, autoload usa o namespace
`RR\`, não o campo `name`. Seguro renomear a qualquer momento.

### B10 — Ícone padrão de avatar tematicamente errado (operário de construção)

**Fontes**: `AUDITORIA-4-identidade.md` §4 (achado incidental).
**Arquivos**: `public/img/customer/default/default.png`.

**Risco da correção**: MUITO BAIXO — troca de um arquivo de imagem
estático. Só precisa de um ícone substituto neutro.

### B11 — Observação de schema sem ação de código

**Fontes**: `AUDITORIA-2-tecnica.md`, observação de schema
(`sales.currency_value` com `DEFAULT` inválido).

**Risco da correção**: BAIXO — corrigir o `DEFAULT` via migration é
opcional, nenhum código depende do valor atual.

### Não recomendado mexer agora (registro, não é item de ação)

`namespace RR\`/`$_SESSION['RR']` (sigla do cliente imobiliário anterior,
entranhada em 219 arquivos/476 usos) — `AUDITORIA-4-identidade.md` §5. As
próprias auditorias recomendam **não tocar** sem alinhamento explícito de
escopo e risco com o usuário — é um refactor estrutural grande, não uma
correção pontual. Incluído aqui só por completude do rastreamento, não como
pendência a agendar.

---

## Achados novos durante a execução

### ✅ N1 — Anexo de atendimento gravado sem arquivo — CONCLUÍDO 2026-09-30

Reportado em uso real: o registro nº 3 de `attendance_attachments`
(atendimento 1) foi gravado sem arquivo (extensão `NULL`, nada no disco), e
entrou na linha do tempo como "Adicionou um novo anexo". Causa: um campo de
arquivo vazio chega como `tmp_name = ['']`, e a checagem
`empty($_FILES['attachment']['tmp_name'])` não barrava isso. Corrigido em
`AttendanceController::handleSubmitAddAttachments` com a nova
`FileUploader::hasSelectedFile()`: sem arquivo escolhido, nada é gravado e
aparece mensagem clara. Os registros nº 3 e nº 4 (sem arquivo) podem ser
removidos pela lixeira da própria tela, depois da correção N2 — sem migration. **Mesmo defeito, NÃO corrigido**: anexos
de veículo (`VehiclesController::handleSubmitAddAttachments`), de Contas a
Pagar e de Contas a Receber (`handleSubmitAddAttachmentItem`) também gravam
registro quebrado quando enviados sem arquivo; anexos de cliente não gravam,
mas a mensagem não explica o motivo. **Sobra agendada para a Onda 4**
(decisão do usuário, 2026-10-01).

### ✅ N2 — Excluir anexo de atendimento nunca funcionava e travava a tela — CONCLUÍDO 2026-09-30

Reportado em uso real (erro fatal ao clicar na lixeira). Dois defeitos
antigos somados: (1) a lixeira era um link GET, mas
`handleSubmitDeleteAttachment` exige POST (`check_post_method`) — a exclusão
nunca acontecia; (2) nessa falha, redirecionava para
`attendance/attendance/{id}` usando o **id do anexo** (parâmetro mal nomeado
`$attendanceId`), abrindo um atendimento inexistente, e `attendance()` não
tratava id inexistente → fatal. Corrigido: lixeira no padrão do projeto
(`btn-disable-item` + modal de confirmação POST, igual aos anexos de Contas a
Pagar/Receber); método com `$attachmentId` e redirecionando sempre para o
atendimento dono do anexo; `attendance()` volta para a listagem com aviso se o
id não existir. Os anexos sem arquivo (nº 3 e nº 4) podem ser excluídos pela
própria tela (`DeleteFile` usa `@unlink`), dispensando migration. **Não
corrigido (mesma família do grupo M2)**: o método de exclusão não checa
perfil — a tela só mostra a lixeira para admin, mas a rota aceita qualquer
usuário logado. **Sobra agendada para a Onda 3, junto com C3/M2** (decisão do
usuário, 2026-10-01).

**Causa do botão "Escolha um Anexo" não abrir — resolvido, não era código**:
o diagnóstico no navegador provou o HTML correto (1 campo, rótulo ligado, nada
cobrindo). Era uma janela de seleção de arquivo presa naquela aba do Chrome;
em aba nova abriu normalmente. **Arquivo "sumindo" no envio — também não era
código**: log temporário (já removido) mostrou `error=4`/`name=""` ao escolher
`Google Chrome.app` — no macOS um `.app` é uma pasta, e navegadores não enviam
pastas por campo de arquivo; a mensagem "Nenhum arquivo foi escolhido" estava
correta. Provavelmente a mesma origem dos anexos nº 3 e nº 4 sem arquivo.

### N3 — Pendências de limpeza registradas durante o C1 (não tratadas)

Levantadas ao executar o C1, deixadas para depois do lançamento:

- **21 métodos sem uso no front-end em `AjaxController`** (depois de exigirem
  login, estão inertes, mas são superfície morta a remover numa limpeza
  separada): `compareDate`, `getAccounts`, `getAllBanksPortion`,
  `getAmountPortionsBySale`, `getAndFilterAccountsPortion`,
  `getAndFilterAllBankAccounts`, `getAndFilterAllBanks`,
  `getAndFilterAllCustomer`, `getAndFilterAllUsers`, `getAndFilterBanksPortion`,
  `getAttendanceById`, `getCustomerByBranch`, `getCustomerTypeResources`,
  `getItemById8161`, `getLogStatusByIdContrato`, `getPortionById`,
  `getValueCurrency`, `recursiveCostCenterTree`, `recursiveCostCenterView`,
  `sumDate`, `updatePortion`. Confirmar caso a caso antes de apagar (alguns
  podem ser chamados por outro controller PHP, não só pelo front-end).
- **Chamada morta `ajax/global/toast`** em `public/js/v_01/script.js:150` (e
  cópia em `application.js`, arquivo órfão): dispara a cada carregamento de
  página e cai em erro no servidor (`GlobalController` não tem método
  `toast`) — era a origem do spam `Call to undefined method ...::toast()` no
  log do Apache. Hoje sem efeito visível (o toast real sai pelo
  `Toast::render()` no footer). Remover o bloco `$(document).ready` do
  `script.js` e o `application.js` órfão numa limpeza separada.
- **Reordenar anexos de veículo está quebrado** (independente do C1):
  `src/view/vehicles/attachments.php` e `record-vehicle-history/vehicle.php`
  usam `data-table="products_attachments"`, tabela que não existe, e
  `vehicle_attachments` não tem coluna de ordenação. Com a whitelist do C1
  continua sem funcionar (não há regressão). Corrigir exige decidir se a
  reordenação de anexos de veículo deve existir e, se sim, criar a coluna via
  migration.

### ✅ N4 — Impressão do Histórico de Veículos sem checagem de perfil — CONCLUÍDO 2026-10-01

Achado ao testar o C2 lote 1: `RecordVehicleHistoryController::print()` não
chamava nenhum `Secure::access_*`, enquanto `index()` exige
`access_secretary(true)`. Um usuário logado **sem** acesso à tela recebia HTTP
200 com o relatório em `record-vehicle-history/print/`. **Corrigido**: `print()`
agora chama `Secure::access_secretary(true)`, a mesma regra do `index()`.
Conferido: os `print()` de Vendidos/Comprados **já** tinham
`access_admin(true)`, igual ao `index()` deles; não precisaram de mudança.
**Teste antes × depois** (controller real, sessão só em memória, perfis com
`access` 5/10/20/30/40): antes, 30 e 40 recebiam o relatório; depois, só ≤ 25
(Superadm, Administrador, Gerente, Secretária), igual ao `index()`. Por `curl`
com sessão real não-admin: antes 200, depois 302 → `home`.

### N5 — Aviso `Undefined property: stdClass::$text` na impressão de Veículos Vendidos (só registrado)

Visto ao testar o C2 lote 1. Já existia antes da correção, sem relação com o
C2. Aparece em `record-of-sold-vehicles/print` quando há resultados. Não tratado
(decisão do usuário: só registrar).

### ✅ N6 — XSS refletido nos filtros das telas de listagem — CONCLUÍDO 2026-10-01 (4 telas + paginação)

Achado ao testar o C2 lote 1: as views devolviam o valor dos filtros sem
escapar (`value="<?= $_GET['name'] ?>"`); um link com
`?name="><script>...` executava script na sessão de quem clicasse. **Corrigido
nas 4 telas do C2 lote 1** (`vehicles`, `record-of-sold-vehicles`,
`record-of-purchased-vehicles`, `record-vehicle-history`, todas em
`src/view/<tela>/index.php`): `htmlspecialchars($x, ENT_QUOTES, 'UTF-8')` no
campo de busca (`name`/`pesquisa`) e nas datas (`data_de`, `data_ate`), 12
pontos. Os demais filtros (`status`, `transfer`, `data_tipo`, `b`) só são
comparados, nunca impressos. Também corrigido o botão **"Imprimir Relatório"**
dos 3 relatórios: o `href` era montado com a query crua de `REQUEST_URI`
(`ButtonComponent` não escapa), agora com `htmlspecialchars` no próprio
controller. **Teste antes × depois**: o payload aparecia cru em busca, datas e
link de impressão nas 4 telas; depois, só escapado. Busca normal sem mudança
(mesmas linhas nas 79 consultas da matriz do C2).

**✅ Paginação também corrigida (2026-10-01, pedido do usuário)**:
`PaginationComponent1245` e `PaginationComponent` imprimiam
`$_SERVER['REQUEST_URI']` cru nos links de página. Agora tiram o `&page=N` da
URL crua e **depois** a escapam com `htmlspecialchars` (nessa ordem: escapando
antes, o `&amp;page=` não seria removido e a página duplicaria); o separador
virou `&amp;page=`. **Levantamento prévio**: `PaginationComponent1245` é usado
por 25 views de listagem (`bill-receive`, `branch`, `check-control`,
`countries`, `currencies`, `customer`, `digital-card`, `lead`, `networks-site`,
`purchase-requests`, `record-of-purchased-vehicles`, `record-of-sold-vehicles`,
`record-sale-requests`, `record-vehicle-history`, `sale-requests`,
`user-position`, `users`, `vehicle-brands`, `vehicle-categories`,
`vehicle-colors`, `vehicle-doors`, `vehicle-fuels`, `vehicle-models`,
`vehicle-types`, `vehicles`) e pelo `ListingCardComponent`
(`bill-receive-installment`); `PaginationComponent` tem **zero usos**
(corrigido mesmo assim). **Sem risco de escape duplo**: nenhuma tela passa
URL ao componente, os dois leem `REQUEST_URI` direto e nenhum código de
`src/` ou `public/` reescreve `REQUEST_URI`. **Teste por `curl` antes ×
depois** (sessão real): Países (`name=á `, 2 páginas; `currency_name=Euro` +
`name=ç`), Modelos (`name=é`, 4 páginas; `brandName=Volkswagen` abrindo na
p. 2), Marcas (com e sem filtro, 2 páginas), e o link da p. 1 com datas em
Veículos, Vendidos e Histórico. Os links de página continuam com todos os
filtros; seguindo p. 2/3/4, as mesmas linhas e o campo de busca preenchido
(`á `, `é`), idênticos ao antes. Uma URL com aspas cruas
(`?x="onmouseover=...`) escapava do `href` antes; agora sai como `&quot;`.
Nenhum `&amp;amp;` (escape duplo) em nenhuma tela testada.
**Limite do teste**: nenhuma tela com filtro de data tem mais de 20 registros
no banco local, então a navegação para a p. 2 com datas não pôde ser exercida,
só o link da p. 1. Em `bill-receive-installment` o componente renderiza sem
erro, mas sem links (sem parcelas no filtro padrão).
**Comportamentos antigos mantidos (não mexi)**: sem filtro na URL, o link sai
como `/vehicle-brands/&page=2` (sem `?`), e funciona; se `page` for o
**primeiro** parâmetro (`?page=2&name=x`), o regex não o remove e o link fica
com `page` duplicado (o último vence no PHP, então a navegação funciona).

### ✅ N7 — SQL injection via `limit`/`page` (LIMIT/OFFSET sem bind) — CONCLUÍDO 2026-10-01

Achado no C2 lote 2: `Model::getWithFiltersAllItems` e `getItemWithFilters`
montavam `LIMIT {$options['limit']} OFFSET ...` concatenando o valor, e 13
chamadas em 5 controllers (ajax `PurchaseRequests`, ajax `SaleRequests`, ajax
`Customer`, ajax `CheckControl`, `BillsToPayInstallment`) passam `limit`/`page`
direto de `$_POST`/`$_GET`. **Decisão do usuário**: corrigir no Model.
`limit` e `page` agora passam por `(int)` antes de montar `LIMIT`/`OFFSET`
(`src/core/Model.php`, os 2 métodos); com valor numérico, nada muda. Testado
nos 3 endpoints ajax do lote 2 (ver C2). **Não tratado**:
`ModelGenerico::getItens` também concatena `LIMIT $qtd` e `ORDER BY
$filters['order']`, mas tem **zero chamadores** (código morto, ver B1).

### ✅ N8 — Pedidos de Compra/Venda: métodos sem checagem de perfil — CONCLUÍDO 2026-10-01

**✅ Parte 1 (decisão do usuário: "Financeiro, Comissão e as impressões deles:
só Administrador e Superadmin", a mesma regra que a tela já usa).** A aba usa
`Secure::access_admin()` (access ≤ 10: Superadm, Administrador e também
Desenvolvedor, perfil sem usuários ativos). Aplicado `Secure::access_admin(true)`
(redireciona para `home`) em `purchaseFinancial`/`saleFinancial`,
`purchaseCommission`/`saleCommission`, `installmentPrinting`,
`commissionInstallmentPrinting` e `vehicleInstallmentPrinting` (impressão de
parcelas cujo botão só aparece para admin em "Dados Gerais"), nos 2
controllers. Também nos 4 endpoints ajax que só essas abas chamam
(`financial.js`) e que **gravam** conta/parcela:
`ajax/PurchaseRequests/handleSubmitAddBillsToPay` e `addBillsToPayInstallment`,
`ajax/SaleRequests/handleSubmitAddBillReceive` e `addBillReceiveInstallment`.
Sem permissão, devolvem `error=true` com "Sem permissão para esta ação." pelo
`sendResponse()` (que o `financial.js` já mostra em Toast), **antes** de
qualquer leitura/gravação. **Teste antes × depois** (controller real via CLI,
perfis de access 5/10/18/25/30, pedido 1): Superadm e Administrador com saída
idêntica byte a byte; Gerente Geral, Secretária e Vendedor abriam as 2 telas e
as 3 impressões e agora são redirecionados. `vehiclePrinting` e `editItem`
ficaram iguais (controles). **Os 4 endpoints ajax não foram executados**: eles
gravam no banco, e se a checagem falhasse o próprio teste gravaria. Verificação
estática: a checagem é a 1ª instrução dos 4 métodos, e `access_admin()` devolve
`false` para access 18–30 e `true` para ≤ 10.

**✅ Parte 2 (aprovada pelo usuário)**: quem edita o pedido hoje é
`access_admin(true)` no `editItem` (na listagem, os botões Editar e
Ativar/Inativar só habilitam para os perfis id 1 e 2, Superadm e Administrador;
a única diferença é o Desenvolvedor, sem usuários). Aplicado
`Secure::access_admin(true)` em `deleteVehiclesPurchased`/`deleteVehiclesSale`,
`disableItem`, `enableItem` e (extra pedido pelo usuário)
`handleSubmitEditItem`, nos 2 controllers, como 1ª instrução. Corrigido o
botão de excluir veículo (`purchase-requests/vehicles.php`,
`sale-requests/vehicles.php`): o atributo inválido `desabled` virou a classe
`disabled`, padrão do projeto. No Bootstrap 3.3.7 só a **classe** bloqueia o
clique em `<a>` (`a.btn.disabled{pointer-events:none}`); o atributo só
esmaeceria. Observação: o JS da aba (`removeClass('disabled')` depois de
editar um veículo) pode reabilitar o botão visualmente, mas o servidor agora
bloqueia. **Teste**: a aba Veículos renderizada como Administrador mostra o botão
normal e como Vendedor com `disabled`. Os 5 métodos **gravam** e não foram
executados (se a checagem falhasse o teste gravaria); conferido estaticamente
que a checagem é a 1ª instrução e que `access_admin(true)` redireciona com `exit`.

**✅ Extras (aprovados pelo usuário depois do levantamento abaixo)**: `handleSubmitAddItem`, aba Veículos
e ajax de adicionar/editar veículo. Levantamento do que a tela mostra hoje, igual
em Compra e Venda:
- Menu e listagem: `menu.access = 30`, então todos os perfis veem.
- Botão "Adicionar": aparece para todos, mas só **habilitado** para Superadm e
  Administrador (`profile->id` 1 ou 2); o `addItem` exige `access_admin(true)`.
- Aba Veículos: dentro do pedido aberto não tem condição de perfil, mas só se chega
  a ela por caminhos admin: `editItem` (admin), redirect depois de
  salvar/adicionar pedido (admin) e links dos relatórios Veículos
  Comprados/Vendidos (`access_admin`). Os botões Cadastrar/Editar/Salvar veículo
  dentro da aba (que chamam `ajax/*/addVehicles*` e `editVehicles*`) não têm
  condição.

**Aplicado** (regra do servidor igual à da tela): `Secure::access_admin(true)` em
`handleSubmitAddItem`, `purchaseVehicles` e `saleVehicles`; recusa em JSON
("Sem permissão para esta ação.", 1ª instrução) em `ajax/PurchaseRequests/addVehiclesPurchase`
e `editVehiclesPurchase`, e em `ajax/SaleRequests/addVehiclesSale` e `editVehiclesSale`.
**Teste antes × depois** (perfis de access 5/10/18/25/30): a aba Veículos abre igual
para Superadm e Administrador (diferença de 1–2 bytes = espaço a menos por botão de
excluir, da correção do `desabled`); Gerente Geral, Secretária e Vendedor abriam pela
URL e agora são redirecionados. `handleSubmitAddItem` e os 4 ajax gravam e não foram
executados; conferido estaticamente que a checagem é a 1ª instrução.

Achado no C2 lote 2. Em `PurchaseRequestsController` e `SaleRequestsController`
(project), **não chamam nenhum `Secure::`**: `purchaseVehicles`/`saleVehicles`,
`deleteVehiclesPurchased`/`deleteVehiclesSale`, `purchaseFinancial`/
`saleFinancial`, `purchaseCommission`/`saleCommission`, `disableItem`,
`enableItem`. `handleSubmitAddItem`/`handleSubmitEditItem` só checam o método
POST, enquanto o formulário (`addItem`/`editItem`) exige `access_admin`. As abas
Financeiro e Comissão só aparecem para admin (`navTabs`), mas as rotas abrem
para qualquer logado pela URL, e as impressões de parcela/comissão também. A
checagem aplicada no lote 2 (`individual_menu_access`) não fecha isso, porque
`menu_access` não bloqueia ninguém hoje. Mesma família de A5/M2:
**⚠️ decidir o perfil** de cada ação (ex.: Financeiro/Comissão e suas
impressões só admin, igual à aba?) antes de corrigir. Também: o `index()` de
Compra chama `Secure::individual_menu_access(true)` (vira menu id 1, que não
existe), uma linha sem efeito; não mexi.

### N9 — Erros já existentes vistos nos testes do C2 lote 2 (só registrado)

Já aconteciam antes das correções; não têm relação com o C2:
- `sale-requests` (listagem): `Warning: Undefined variable $formOfPayments` +
  `foreach() argument must be of type array|object` em toda carga da tela.
- `purchase-requests/commissionInstallmentPrinting/4`: **fatal error**
  (`Attempt to read property "id" on bool` e `Attempt to assign property
  "numberOfInstallment"`) — a impressão de comissão quebra para o pedido 4.
- `sale-requests` impressões (`installmentPrinting`/
  `vehicleInstallmentPrinting` do pedido 7, `commissionInstallmentPrinting`
  dos pedidos 1 e 7): `Undefined variable $installments`/`$totalInstallments`
  e `foreach()` em null — imprimem sem as parcelas.

### N10 — Coisas já existentes vistas no C2 lote 3 (só registrado)

Já aconteciam antes; não têm relação com o C2:
- **Cheques: botões Ativar/Inativar da listagem não funcionam** — apontam para
  `check-control/disableItem`/`enableItem`, que não existem no
  `CheckControlController` (a rota cai em `error`).
- **`ajax/Customer/getCustomersSuppliersAndBuilders` sem chamador** em
  `public/js`, e filtra tipos de cliente por ids fixos `[9, 11]`; no banco
  local nenhum cliente tem esses tipos, então sempre volta vazio. Mesma
  família dos ids fixos de tipo de cliente já tratados em outros arquivos.
- Os endpoints ajax de **leitura** de cheque (`getCustomersChecks`,
  `getAllChecks`, `getCheckById`, `getBankById`, `getAccountsById`,
  `getBillReceiveById`) não checam perfil além do login (C1); são chamados das
  telas de pagamento de parcelas e da edição do cheque. Fora do escopo do C2
  (que pediu checagem em impressões e gravações). `getBillReceiveById` não tem
  chamador e não faz nada (monta um array e não usa).

### N11 — Anexo de veículo grava só o primeiro arquivo (só registrado, Onda 4)

Visto no levantamento do C4 etapa 2. O campo de anexo de veículo
(`src/view/vehicles/attachments.php`) é `attachments[]`, mas
`VehiclesController::handleSubmitAddAttachments` só lê
`$_FILES['attachments']['name'][0]`/`['tmp_name'][0]`: se o usuário escolher
vários arquivos, só o primeiro é gravado, sem aviso. A validação de tipo do
C4 confere **todos** os arquivos enviados (recusa o envio se qualquer um for
inválido), mas continua gravando só o primeiro.

### N12 — Coisas já existentes vistas no C4 etapa 2 (só registrado)

Já aconteciam antes; não têm relação com a whitelist:
- **WideImage local não funciona no PHP 8** (o mais grave): `wideImagePhoto()`
  (`src/libs/wideImage/wide.php`) usa a cópia antiga em
  `src/libs/wideImage/lib/`, cujo `isValidImageHandle()` exige `is_resource`;
  no PHP 8 o GD devolve objeto `GdImage`, então **toda** carga de imagem cai
  num erro fatal (`Class "WideImage_vendor_de77_BMP" not found`). Efeito:
  avatar, foto do cartão do usuário, cartão digital e logo do cliente **só
  funcionam com imagem na dimensão exata** (caminho de cópia); fora dela, erro
  fatal. Configurações e Filial capturam o erro e mostram "extensão
  inválida". A cópia do `vendor/smottt/wideimage` (namespace `WideImage\`)
  já trata `GdImage` e é a que o `FileUploader` usa. Depois do C4, pelo menos
  o banco não fica mais apontando para arquivo que não foi criado.
- Foto do cartão do usuário (`UsersController::handleSubmitDigitalCard`):
  apaga a foto antiga com o nome `-dp-`, mas grava com `-dc-`, então a antiga
  nunca é apagada (sobra arquivo no disco).
- Marca d'água: `resize1()` grava o conteúdo em JPEG dentro de um arquivo
  `.png` (perde a transparência).
- Banco local × disco divergem (teste): `customer_attachments` tem 10 registros
  e 9 arquivos; `system_config` diz que há logo mini e rodapé, sem os arquivos.

**Decisão do usuário (2026-10-01)**: Onda 4. Trocar o WideImage pelo Imagine
(`imagine/imagine`, já no `composer.json` e usado nas fotos de veículo por
`Util::resizeImageWithCanvas`). **Proposta, ainda não aplicada:**
- **Onde**: as 21 chamadas de `wideImagePhoto()` — `User.php` (avatar, 2),
  `UsersController::handleSubmitDigitalCard` (2), `DigitalCardController`
  (fundo/logo, cadastro e edição, 8), `CustomerController::handleSubmitImage`
  (1), `SettingsController::handleSubmitImages` (5), `BranchController::handleSubmitImages`
  (3) — e a de `resize1()` em `WaterMarkController` (1).
- **Como**: um método novo em `src/libs/Util.php`, ao lado do
  `resizeImageWithCanvas` (mesmo `Imagine\Gd\Imagine`), em vez de uma 5ª
  função no `Resizer.php`. Para as imagens: `thumbnail(new Box($w, $h),
  THUMBNAIL_OUTBOUND | THUMBNAIL_FLAG_UPSCALE)`, que equivale ao
  `resize('outside') + crop('center')` do `wideImagePhoto`; qualidade pelos
  mesmos valores de hoje (`jpeg_quality` 100, `png_compression_level` 9). Para a
  marca d'água: `thumbnail(new Box(200, 100), THUMBNAIL_INSET)` (cabe dentro
  de 200×100, mantém proporção).
- **Prova de conceito** (só no scratchpad, 2026-10-01): JPG → 230×50 e
  600×280, PNG → 1490×2130 saíram nas dimensões exatas; marca d'água PNG com
  transparência → 133×100, **transparência preservada** (hoje o `resize1`
  grava JPEG dentro do `.png` e perde a transparência; com o Imagine isso se
  resolve junto).
- **Decidido para a execução (usuário, 2026-10-01)**: (1) **recodificar
  sempre** — remover o caminho "dimensão exata → `copy()` do arquivo cru" nas
  12 telas, para que todo arquivo gravado passe pelo Imagine (descarta
  metadados e qualquer conteúdo extra embutido na imagem); (2) **corrigir junto
  o `-dp-` → `-dc-`** na exclusão da foto antiga do cartão
  (`UsersController::handleSubmitDigitalCard`), para a foto anterior ser
  apagada de verdade.
- **Fora desta troca**: `FileUploader::uploadImg`/`uploadImgSingle` usam o
  WideImage do `vendor/` (que funciona no PHP 8) e não têm chamador;
  `UploadFiles.php` é a lib legada do B6. `src/libs/wideImage/` só pode ser
  removida depois de confirmar que nada mais chama `wide.php`.
- **Teste depois**: upload em cada uma das 12 telas com imagem fora da
  dimensão exata (hoje erro fatal), na dimensão exata, JPG e PNG, e marca
  d'água com PNG transparente.

### ✅ N13 — Excluir anexo de veículo sem checagem de perfil — CONCLUÍDO 2026-10-02

Visto no levantamento de 2026-10-01. A lixeira de anexo
(`src/view/vehicles/attachments.php`) só aparece para admin
(`Secure::access_admin()`), mas `VehiclesController::handleDeleteAttachment`
não checa perfil e é um link GET (`vehicles/handleDeleteAttachment/{veículo}/{anexo}`,
também em `record-vehicle-history/vehicle.php`): qualquer usuário logado apaga
anexo pela URL. **Decisão do usuário (2026-10-01)**: só Admin, junto com C3/M2.

### ✅ N14 — Qualquer usuário pode trocar o próprio perfil para Superadm (CRÍTICO) — CONCLUÍDO 2026-10-02

Visto no levantamento de 2026-10-01 (**verificado só por leitura de código**;
não executado porque grava no banco). O botão "Perfil" do topo abre
`users/edit-item/{próprio id}`; o formulário tem o campo `id_profile` (a lista
mostra só perfis de nível igual ou abaixo, via `getAllUsersProfilesBellow`).
No servidor, `UsersController::handleSubmitEditItem` exige só
`access_seller` (qualquer perfil) e `User::submitEditForm` passa por
`Secure::userBranches`, que **permite o próprio usuário** (`creator`), e grava
`$post['id_profile']` **sem validar**. Alterando o valor enviado (ex.:
`id_profile=1`), um Vendedor vira Superadm. Mesma família do C3, não listada
nas auditorias. **Proposta**: no servidor, aceitar só `id_profile` que esteja
na lista que a tela oferece para quem está editando, e não permitir que o
usuário altere o **próprio** perfil (exceto Superadm). ⚠️ Confirmar com o
usuário antes de aplicar.

**✅ Aplicado (2026-10-02), regras do usuário**: (1) só aceita perfis que a tela
oferece (`getAllUsersProfilesBellow` de quem edita — nunca Desenvolvedor, e
para o Administrador nunca Superadm); (2) ninguém altera o próprio perfil,
exceto Superadm; (3) quem não é Superadm não cria, edita, ativa ou desativa
usuários Superadm/Desenvolvedor (ativar/desativar incluídos como "editar").
Implementado em `UsersController::profileRuleError()` e chamado em
`handleSubmitAddItem`, `editItem`, `handleSubmitEditItem`, `disableUser`,
`enableUser`, antes de qualquer gravação (aviso + redirect). Tipo vazio ou
ausente no POST é recusado. **Teste**: 22 cenários chamando a regra com sessões
reais (só leitura) — todos passaram (ex.: Vendedor → `id_profile=1` recusado;
Secretária mudando o próprio tipo recusado; Administrador → Superadm/Dev
recusado; Superadm mudando o próprio tipo aceito). Telas "Perfil" de todos os
perfis abrem como antes. Não executados os envios (gravam). **Limite**: um
usuário Desenvolvedor (não há nenhum) não conseguiria salvar o próprio cadastro,
porque a tela nunca oferece o tipo Desenvolvedor.

### ✅ N15 — Tela de permissões inverte submenus ao clicar no menu pai — CONCLUÍDO 2026-10-02

Visto no levantamento de 2026-10-01. `MenuAccess::updateProfileMenu` inverte o
estado do menu clicado e chama a si mesmo para cada submenu, **invertendo cada
um individualmente**; submenu sem linha é sempre **inserido como liberado**.
Desativar um menu pai pode, portanto, **liberar** submenus. Corrigir junto com
a proposta "sem linha = negado" do C3: o estado do pai deve ser aplicado igual
em todos os submenus.

**✅ Aplicado (2026-10-02)**: o novo estado é decidido uma vez no menu clicado
(Ativo → Inativo; Inativo ou sem linha → Ativo) e `setProfileMenuStatus` grava o
mesmo estado no menu e em todos os descendentes, numa única transação (antes
uma por menu). Mantido: ativar um submenu cujo pai não tem linha ativa o pai.
Teste no C3.

### N16 — Avisos PHP já existentes vistos na matriz do C3 (só registrado, Onda 4)

Iguais antes e depois das correções; aparecem em desenvolvimento (em
produção vão só para o log):
- `vehicles/editItem` e `vehicles/vehicleCosts`: `Undefined variable $customers`
  / `$vehicleBrands` + `foreach` em null (`src/view/vehicles/modals.php`).
- `vehicles/attachments`: `Undefined variable $permission` (`attachments.php`).
- `report-dre`: `number_format()` recebendo null.
- `attendance` (listagem): `strtotime()` recebendo null.
- `sale-requests`: `$formOfPayments` (já no N9).
- `check-control` (listagem): 10 avisos em `check-control/modals.php`
  (`$costCenters` indefinida, propriedades lidas de null).

### N17 — 11 rotas sem item de menu (proposta, ⚠️ aguardando aprovação)

Decisão do usuário (2026-10-02): as rotas sem item de menu "passam a exigir
Admin no servidor; listar antes de aplicar". Levantamento — várias **não podem**
exigir Admin:

| Rota | O que é | Hoje | Proposta |
|---|---|---|---|
| `login` | Tela de login | Pública (não passa pelo `Controller`) | **Não aplicar** — ninguém entraria |
| `error` | Página de erro/404 | Qualquer logado | **Não aplicar** — é para onde cai endereço errado |
| `front` | Classe base dos controllers, não é tela | — | Nada |
| `countries` | Países | Já exige Admin (construtor) | Nada |
| `currencies` | Moedas (tabela inexistente) | 2 métodos exigem Desenvolvedor, resto aberto | Aplicar Admin no construtor agora (o M12 esconde do menu na Onda 4) |
| `networks-site` | Redes sociais do site/cartão digital | **Sem checagem**: qualquer logado cadastra, edita, ativa/desativa | Aplicar Admin |
| `notification` | Notificações | Cada usuário vê as próprias; gerente, as da filial | **Não aplicar** — tiraria as notificações dos demais perfis |
| `lead`, `lead-config` | Leads (módulo desativado) | Já redirecionam todos para a home | Aplicar Admin (sem efeito prático) ou nada |
| `lead-redirect` | Distribuição de leads (desativado) | Responde 404 a todos | Nada |
| `card-pdf` | PDF do cartão digital de um usuário, por id na URL | **Sem login nenhum** (não estende `Controller`): qualquer pessoa na internet baixa o cartão de qualquer usuário trocando o id | ⚠️ Decidir: é para clientes (público)? Senão, exigir login |

## Resumo de itens ⚠️ BLOQUEADOS (decisão do usuário necessária antes de qualquer código/migration)

| Grupo | Decisão pendente |
|---|---|
| ~~C3~~ | ✅ Decidido e aplicado 2026-10-02 (com "sem linha = negado" e N15). |
| ~~C5~~ | ✅ Decidido e aplicado 2026-10-02 (rateio removido + transação opção A). |
| C7 | Qual o SMTP/e-mail de envio real da Repasse Sandré? |
| ~~A5/M2~~ | ✅ Decidido e aplicado 2026-10-02 (ver A5 e M2). |
| A9 | Qual o telefone de suporte e e-mail de contato reais da Repasse Sandré? |
| ~~M1~~ | ✅ Decidido e aplicado 2026-10-01: aba escondida, código mantido. |
| ~~N14~~ | ✅ Decidido e aplicado 2026-10-02. |
| N17 | Aprovar a proposta para as 11 rotas sem item de menu; o `card-pdf` deve ser público? |
| ~~M12~~ | ✅ Decidido 2026-10-01: esconder do menu e bloquear a rota (Onda 4); remoção completa é limpeza opcional. |
| ~~B2~~ | ✅ Decidido 2026-10-01: manter documentado, não dropar. |

Nenhuma das decisões ainda pendentes foi presumida neste plano — todas exigem
resposta do usuário antes de qualquer correção ou migration ser escrita,
conforme a regra do CLAUDE.md de não presumir regra de negócio.
