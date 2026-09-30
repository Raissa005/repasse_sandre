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

### C1 — Endpoints ajax genéricos sem autenticação + SQL injection sem bind

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

### C2 — SQL injection autenticada via filtro "Nome"/pesquisa (13 controllers)

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

### C4 — Upload de arquivo arbitrário → execução remota de código (RCE)

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

### A11 — Vazamento de erro PDO sem gate de ambiente (Contas a Pagar/Receber)

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

### M5 — Config/debug: blocos dependentes de ambiente de produção não verificável

**Fontes**: `AUDITORIA-3-seguranca.md` §8.2, §8.3.
**Arquivos**: ~20 blocos em `src/model/*.php`, `src/config/config.php:16-19`.

**Risco da correção**: BAIXO. Adicionar o `else` explícito desligando
`display_errors`/`error_reporting` fora de dev é mudança pequena e
seguramente correta — não depende de decisão de negócio. O item dos ~20
blocos "gated" não é uma correção de código em si; é uma recomendação de
**confirmar com o usuário** qual é o `ENVIRONMENT` real do `config.php` de
produção (não está neste repositório, que não é versionado).

### M6 — Sem limite de tamanho de upload + sem hardening de cookie de sessão

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

### M8 — `$_POST['created_by']` sem `isset()` no cadastro de Atendimento

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

### M11 — Templates de contrato imobiliário acessíveis via manipulação de POST

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
