# Auditoria e limpeza da migração imobiliário → revenda de veículos

Este documento registra a auditoria completa de resíduos do domínio imobiliário
antigo (quando o sistema era usado para venda de imóveis) que sobraram após a
migração para revenda de veículos, e a limpeza executada em cima desses
achados. Serve como histórico de decisão — não repetir esta auditoria do zero
sem antes ler este arquivo.

**Data**: 2026-09-21/22. **Migrations relacionadas**: `db/migrations/2026_09_21_1000_remover_menu_integration_config.sql`,
`db/migrations/2026_09_21_1010_desativar_standard_contract_imovel.sql`,
`db/migrations/2026_09_21_1020_remover_campo_creci.sql` (todas já executadas).

## Contexto

O sistema (`repasse_sandre` / banco `veiculos_repasse`) era originalmente uma
plataforma de venda de imóveis e foi migrado para revenda de veículos. A
migração trocou os módulos de negócio principais (cadastro de produto,
atendimento, vendas) para o domínio novo, mas não removeu o código, os models,
as views nem os dados do domínio antigo — eles ficaram misturados ao sistema
atual, às vezes alcançáveis, às vezes não. Esta auditoria mapeou tudo isso
antes de decidir o que remover.

## Os 8 tópicos da auditoria original

A investigação inicial rodou 4 agentes de exploração em paralelo mais
verificação direta, cobrindo 8 frentes:

1. **Bugs ativos hoje** — achado inicial: `IntegrationConfigController`
   quebrava sempre que aberto (tabela `integrations` não existe). Depois de
   checar o banco real, descobriu-se que o item de menu que levava a essa
   tela (`integration-config`, ids 84/87) estava órfão por referência de pai
   quebrada (`id_menu_parent = 83`, que não existe) — ou seja, ninguém
   chegava lá clicando, só por URL direta.
2. **Cluster `Property`** — model de ~2000 linhas (`Property.php`) e 8
   models satélite (`PropertyType`, `PropertyCategory`, `PropertyBranches`,
   `PropertyFilter`, `PropertyOwnershipFeature`, `PropertyTypeResources`,
   `ImmovableResource`, `DisplayedProperties`). A maior parte das consultas
   fazia `JOIN` com tabelas que não existem mais no banco real
   (`property_category`, `property_type`, `property_branch`,
   `immovable_resource`, `product_ownership_feature`, `displayed_properties`,
   `construction_properties`).
3. **`Sales`/contratos de imóvel** — model `Sales.php` misturava métodos
   vivos (leitura de parcelas, comissão) com métodos mortos de cadastro de
   venda de imóvel (`submitAddForm`, `submitFormEdit`, `submitContracts`),
   sem nenhum `SalesController` que os chamasse. O cluster de contrato
   (`Contract.php`, `StandardContract.php`, `ContractsVariables.php`,
   `StandardContractVariables.php`) tinha o mesmo padrão: funções de imóvel
   mortas ao lado de duas funções de recibo de parcela genuinamente em uso.
4. **Módulo Lead** — confirmado que a desativação (guards em
   `LeadController` project/ajax e `LeadRedirectController`) é proposital,
   documentada no próprio código, e funciona corretamente (nenhum caminho
   escapa do guard). Não é resíduo acidental — ficou como estava.
5. **Calendar** — migração completa, zero resíduo de imóvel encontrado em
   controller, model, view ou JS.
6. **Menu de navegação** — o dump `db/realize_repasse.sql` sugeria itens de
   menu ativos como "Todos Imóveis"/"Meus Imóveis" apontando pra controllers
   inexistentes. Checagem direta no banco real mostrou que **o menu real
   está 100% limpo**, só com rotas do domínio de veículo — o achado do dump
   não procedia.
7. **Site institucional / "Praia dos Sonhos"** — não é o site público do
   cliente final, é um painel administrativo (AdminLTE, atrás de login) para
   gerenciar um site institucional que nunca teve o front-end público
   versionado neste repositório. A tela "Praia dos Sonhos" especificamente
   estava quebrada (tabela `praia_sonho` inexistente) e nenhuma rota do
   módulo Site estava linkada no menu real.
8. **Itens que precisavam de confirmação do usuário** — 5 decisões de
   negócio identificadas como ambíguas, listadas abaixo.

### A correção do dump desatualizado

`db/realize_repasse.sql` (o dump SQL versionado no repositório) **não reflete
o schema do banco real** (`veiculos_repasse`, configurado em
`src/config/config.php`). Confirmado por checagem direta (`SHOW TABLES`,
`SHOW COLUMNS`, `SELECT COUNT(*)`) que várias tabelas presentes no dump não
existem no banco real (`lead`, `lead_config`, `lead_random`,
`lead_working_date`, `property_category`, `property_type`, `property_branch`,
`immovable_resource`, `product_ownership_feature`, `displayed_properties`,
`construction_properties`, `contracts`, `contracts_variables`, `integrations`,
`property_filter`, `home_category_link`, `praia_sonho`,
`property_authorization_contract`), e que o menu do dump não bate com o menu
real (o dump tem itens de imóvel/lead; o banco real não tem nenhum).

**Este dump não deve ser usado como fonte de verdade de schema atual.** Toda
conclusão deste documento vem de checagem direta no banco real, não do dump.
Se o dump for atualizado/regenerado no futuro, revisar esta ressalva.

## As 5 decisões de negócio tomadas

| # | Decisão | Resultado |
|---|---|---|
| 1 | Apêndice de Facebook Lead Ads (`IntegrationConfigController`, `Integrations.php`, rota `settings/integrations`, toggle `facebookLeadAds`, webhook `leadRedirect/leadsFaceBook`) — sem previsão de uso | Removido por completo, tratado como apêndice do módulo Lead |
| 2 | `standard_contract` — 8 dos 9 registros são templates de imóvel/teste, sem tela de administração | Desativados (`status=0`) via migration, preservando histórico |
| 3 | Módulo "Site" (`settings-site`, `praia-sonhos-site`, `texts-site`, `banners-site`, `images-site`, `depositions-site`) — quebrado e fora do menu | Removido por completo, **exceto** `NetworksSiteController`/`NetworksSite.php` (mantidos — alimentam o cartão digital, funcionalidade real em uso) |
| 4 | Campo "Creci" (`branch.creci_legal`, `users.creci`) — resíduo de registro de corretor de imóveis | Removido (colunas + todo o código que lia/gravava) |
| 5 | `HomeCategoryLink.php` — preso a `property_category` (tabela inexistente), ligado ao módulo Site | Removido junto com o módulo Site |

Um sexto item foi levantado durante a auditoria (não fazia parte das 5
decisões originais) e resolvido separadamente: `ClientTypeResourceTypes.php`
tem nome de resíduo imobiliário mas é a tabela ativa de tipo de cliente
(Fornecedor/Vendedor/Colaborador) — **decisão: não mexer**, só documentar o
nome confuso.

## Migrations geradas e executadas

Todas em `db/migrations/`, já revisadas e rodadas manualmente pelo usuário
(nenhuma foi executada automaticamente, conforme regra do projeto):

1. **`2026_09_21_1000_remover_menu_integration_config.sql`** — remove os 2
   itens de menu órfãos ("Configurações" → `integration-config`, ids 84 e 87,
   com `id_menu_parent` apontando para um id inexistente) e os 4 registros de
   `menu_access` associados.
2. **`2026_09_21_1010_desativar_standard_contract_imovel.sql`** — desativa
   (`status = 0`) os 8 registros de `standard_contract` do domínio imobiliário
   (ids 5, 10, 11, 12, 14, 15, 16, 18), preservando o id 17 ("Recibo Simples",
   o único em uso real).
3. **`2026_09_21_1020_remover_campo_creci.sql`** — remove as colunas
   `branch.creci_legal` e `users.creci`. Executada só depois que todo o código
   que lia/gravava essas colunas já tinha sido limpo (ordem confirmada
   explicitamente com o usuário, para nunca derrubar uma coluna enquanto
   código ainda a referencia).

## Cluster: Site institucional / "Praia dos Sonhos"

**Removido:**
- Controllers: `PraiaSonhosSiteController.php`, `SettingsSiteController.php`,
  `TextsSiteController.php`, `BannersSiteController.php`,
  `ImagesSiteController.php`, `DepositionsSiteController.php`.
- Models: `PraiaSonhos.php`, `HomeCategoryLink.php`, `WebsiteFilters.php`,
  `TextsSite.php`, `BannersSite.php`, `ImagesSite.php`, `DepositionsSite.php`.
- 30 arquivos de view (`praia-sonhos-site/`, `settings-site/`, `texts-site/`,
  `banners-site/`, `images-site/`, `depositions-site/`, completos).
- Assets: `public/js/v_01/imagesSite.js`, `site_popup.js`, `settingsSite.js`,
  `public/css/v_01/layout-site/` (2 arquivos).

**Mantido:**
- `SettingsSite.php` — **não é resíduo**, é usado por `src/core/Controller.php`
  em toda requisição do sistema (não só nas telas do módulo Site).
- `NetworksSiteController.php`/`NetworksSite.php`/views de `networks-site/` —
  mantidos a pedido do usuário porque alimentam o cartão digital
  (`CardPDFController`), funcionalidade ativa e sem relação com imóvel.

Nenhuma migration foi necessária — as tabelas que essas telas usavam
(`praia_sonho`, `home_category_link`) já não existiam no banco real.

## Cluster: `Property`

**Removido:**
- Models: `Property.php`, `PropertyType.php`, `PropertyCategory.php`,
  `PropertyBranches.php`, `PropertyFilter.php`, `PropertyOwnershipFeature.php`,
  `PropertyTypeResources.php`, `ImmovableResource.php`,
  `DisplayedProperties.php`.
- Views: `immovable-resource/index.php`, `add.php`, `edit.php`,
  `lead/productInterest.php`.
- Métodos: `Home::getTenProperties()`, `Home::getTenSales()` (zero
  chamadores).

**Edições corolário** (arquivos que ficaram, mas perderam trechos
dependentes do cluster):
- `NotificationController.php` — removido o bloco morto que tentava casar
  "tem interesse no imóvel" em notificações de atendimento.
- `SettingsController.php` — removidos os 5 métodos de gestão de
  `PropertyFilter` (tabela `property_filter` nem existe).
- `LeadController.php` (project e ajax) e `LeadRedirectController.php` —
  limpeza cirúrgica das referências a `Property` (imports e chamadas), sem
  alterar os guards nem qualquer outra lógica do módulo Lead, que continua
  desativado como estava.

Nenhuma migration foi necessária — nenhuma das tabelas satélite
(`property_category`, `property_type`, `property_branch`,
`immovable_resource`, `product_ownership_feature`) existe no banco real.

## Cluster: contratos de imóvel

**Removido:**
- Models inteiros: `ContractsVariables.php`, `StandardContractVariables.php`.
- View: `customer/print.php`.
- Métodos mortos em `Sales.php`: `submitAddForm()`, `submitFormEdit()`,
  `submitFormStatus()`, `submitContracts()` (privado),
  `getSalesByIdForContract()`.
- Métodos mortos em `Contract.php`: `getContractById()`,
  `getContractByIdSale()`, `getAllContractByIdSale()`,
  `getContractCustomerByCode()`, `getAndFiltersContractCustomer()`,
  `getContractVariablesByCostumerId()`, `getContractVariablesBySaleId()`.
- Métodos órfãos em `StandardContract.php`: `submitFormAdd()`,
  `submitEditForm()`, `getAndFilterAllStandardContract()`,
  `getAllStandardContract()`, `getActivesAndFilterStandardContract()`, e
  `getStandardContractById()` (ficou órfão como consequência direta de
  `Sales::submitContracts` ter sido removido na mesma leva).
- Métodos mortos em `Branch.php`: `getBranchIdSaleForContract()`,
  `getBranchForContractById()`.
- Método morto em `User.php`: `getUserForContractById()`.
- Duplicatas antigas em `PaymentsOfSales.php`: `addPayment()`,
  `editPayment()`, `inactiveStatus()` — superadas por 3 métodos com nomes
  diferentes que fazem o mesmo hoje (`submitAddPortionFromBillReceive`,
  `submitEditPaymentOfSaleFromInstallment`,
  `submitStatusOfPaymentFromAnInstallment`).

**Mantido** (uso real confirmado — recibo de parcela, não contrato de venda
de imóvel):
- `Contract::getInstalmentReceivePrintReceipt()`,
  `getInstalmentToPayPrintReceipt()`, `getCitiesById()` (helper interno
  compartilhado), `getStatesByUF()`.
- `StandardContract::getItemById()`/`getWithFiltersAllItems()` (herdados).
- `standard_contract` id=17 ("Recibo Simples").
- As 3 versões atuais de `PaymentsOfSales` (ver acima).
- `TypesNegotiations.php` — nunca foi resíduo, já migrado corretamente para
  o domínio de veículo (usado em `VehiclesController`/
  `RecordVehicleHistoryController`).

Nenhuma migration nova — reaproveitou a `2026_09_21_1010` já executada.

## Apêndice de Integrações (Facebook Lead Ads)

**Removido:**
- `IntegrationConfigController.php`, `Integrations.php`.
- Views: `integration-config/index.php`, `add.php`, `edit.php`,
  `settings/integrations.php`.
- JS: `public/js/v_01/config-integration/edit.js`,
  `public/js/v_01/settings/settings.js`.
- Método `leadsFaceBook()` de `LeadRedirectController.php` (o comentário do
  guard dessa classe foi atualizado para não citar mais esse webhook como
  motivo).
- Métodos `integrations()`/`handleSubmitIntegrations()` de
  `SettingsController.php`.
- Badge "Facebook Lead Ads: Ativo/Inativo" em `lead/index.php`.

**Migration**: `2026_09_21_1000` (ver seção de migrations acima).

## Item cosmético

Resíduos de nomenclatura/texto sem (ou quase sem) efeito funcional,
levantados numa varredura final por "imóvel", "CRECI", "cronograma", "obra" e
termos correlatos em todo o código visível ao usuário:

| Item | Ação tomada |
|---|---|
| `UserDropdownComponent.php` (botão "Imóveis" no dropdown do usuário) | Confirmado órfão — zero inclusões em qualquer view do projeto. **Não removido**, mantido documentado caso seja reaproveitado no futuro |
| `PaymentsOfSales.php` (tipo de pagamento "Imóvel", valor 2) | Confirmado 0 linhas históricas em `payments_of_sales` no banco real — opção removida do array `type_of_payment`; corrigido também o bug de texto hardcoded que sempre dizia "pagamento do Imóvel" mesmo para pagamento em veículo |
| `attendance/kanban.php` (botão "Imóveis Apresentados" no Kanban de atendimento) | **Bug funcional real** — o link usava `?properties=true`, mas `AttendanceController.php` só reconhece `$_GET['vehicles']`; o clique caía silenciosamente na aba Timeline em vez de "Veículos Apresentados". Corrigido: `href` trocado para `?vehicles=true` e `title` atualizado para "Veículos Apresentados" |
| `users/digital-card.php` (campo "Título Cargo" do cartão digital) | Valor padrão "Corretor de Imóveis" removido (campo fica em branco se não preenchido); placeholder trocado para "Vendedor" |
| `Contract.php` (alias de coluna `autorizacaoImovel_produtos`, dentro dos métodos de recibo que ficaram) | Avaliado — confirmado sem uso em nenhuma view downstream, sem impacto visível. Deixado como está |
| `branch/images.php`, `branch/paymentArrangement.php`, `cardPDF/index.php` (campos/CSS `fonte_creci`, `cor_fonte_creci`, "logo rodapé cartão imóvel", "Valor do Imóvel (Exemplo)") | Avaliados — só nome desatualizado, sem quebra funcional. Não alterados |
| `vehicles/purchase.php` ("Corretor Compra") | Investigado e **confirmado que não é resíduo** — é um campo genuíno do domínio de veículo (corretor que intermediou a compra do veículo), não tem relação com corretor de imóveis |

## Loose ends registrados, não tratados

Achados ao longo da auditoria que não faziam parte de nenhuma decisão
aprovada e por isso não foram mexidos. Ficam aqui para retomar no futuro se
fizer sentido:

- **`Sales::getPaymentsIdSaleForContract()`** (`src/model/Sales.php`) — zero
  chamadores em todo o projeto. Descoberto na verificação final do cluster de
  contratos de imóvel.
- **`Branch::getBranchesByProperties()`** (`src/model/Branch.php`) — zero
  chamadores, faz `JOIN` com a tabela `property_branch`, que não existe no
  banco real. Escapou da varredura original do cluster `Property` porque usa
  SQL cru direto na tabela, sem passar pelos models `Property`/
  `PropertyBranches` que foram rastreados na época.

## Verificação final

Cada remoção desta auditoria foi seguida de grep em todo `src/` e `public/`
confirmando zero referências às classes/métodos/rotas removidos, e checagem
direta no banco real (leitura apenas) para confirmar o efeito de cada
migration. Os controllers que dependiam de partes mantidas dos clusters
(`BillReceiveInstallmentController`, `BillsToPayInstallmentController`,
`CustomerController`, `CardPDFController`) foram conferidos individualmente
para garantir que só referenciam o que ficou vivo.
