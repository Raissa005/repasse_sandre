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

### 🟡 C2 — SQL injection autenticada via filtro "Nome"/pesquisa (13 controllers) — lotes 1 e 2 concluídos 2026-10-01, lote 3 pendente

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

**Restam 5 controllers** (lote 3): `CheckControlController` (project
e ajax), `CustomerController` (ajax) — usar o mesmo `'where'` + `'parameters'`.
Os outros 3 que passam `limit`/`page` da requisição (ajax `Customer`, ajax
`CheckControl`, `BillsToPayInstallment`) já estão cobertos pelo N7.
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

### C3 — Escalação de privilégio self-service (`turnUser` + `updateMenu`)

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

### 🟡 C4 — Upload de arquivo arbitrário → execução remota de código (RCE) — etapa 1 concluída 2026-09-30, etapa 2 pendente

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

### C5 — Registro de pagamento de parcela corrompido (tabelas de rateio de comissão inexistentes, sem transação)

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

### A5 — Controllers administrativos/financeiros sem checagem de perfil consistente

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

### M1 — Aba "Vendas" do cliente inacessível (Sales↔products) + link de edição sem controller

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

### M2 — Demais controllers sem checagem de perfil (Veículos, DRE, Moedas, Atendimento)

**Fontes**: `AUDITORIA-3-seguranca.md` §5.5 (`VehiclesController`), §5.6
(`ReportDreController`), §5.7 (`ReportAttendanceController`, ver também
grupo B5); `AUDITORIA-2-tecnica.md` achado MÉDIO #10 (`Currencies`, falta
de checagem, além da tabela inexistente — ver grupo M11 pra parte de
schema). *Mesma causa-raiz do grupo A5 — considerar corrigir na mesma
varredura.*

**Risco da correção**: BAIXO-MÉDIO, mesmo padrão do grupo A5 — mas primeiro
decidir com o usuário qual perfil deveria acessar cada tela (Compra/Custos
de veículo, DRE, Moedas, Relatório de Atendimento).

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
mas a mensagem não explica o motivo.

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
usuário logado.

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

## Resumo de itens ⚠️ BLOQUEADOS (decisão do usuário necessária antes de qualquer código/migration)

| Grupo | Decisão pendente |
|---|---|
| C3 | Quem pode usar "Ver como este usuário" e conceder/revogar permissão de tela? |
| C5 | Criar as tabelas de rateio de comissão de verdade, ou remover a feature? |
| C7 | Qual o SMTP/e-mail de envio real da Repasse Sandré? |
| A5/M2 | Quem pode criar/desativar filial, centro de custo, e acessar Compra/Custos de veículo, DRE, Atendimento? |
| A9 | Qual o telefone de suporte e e-mail de contato reais da Repasse Sandré? |
| M1 | A aba "Vendas" do cliente deveria virar "Veículo" (usando a tabela `vehicles`) ou ser removida? |
| M12 | A feature de Moedas deveria ser implementada de verdade ou removida? |
| B2 | Pode dropar as 6 tabelas/colunas órfãs do domínio imobiliário, ou manter documentado? |

Nenhuma dessas oito decisões foi presumida neste plano — todas exigem
resposta do usuário antes de qualquer correção ou migration ser escrita,
conforme a regra do CLAUDE.md de não presumir regra de negócio.
