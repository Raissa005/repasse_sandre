# Módulos de negócio

Mapa de "o que existe e onde fica", por área. Cada linha: controller(s) em
`src/controller/project/`, Model(s) principal(is) em `src/model/`, pasta de view
em `src/view/`. Usar como ponto de partida para achar a tela/entidade mais
parecida antes de criar algo novo (`docs/03-controllers.md`, regra de
"evitar duplicidade").

## Cadastros de estrutura / acesso
`Branch`, `UsersController`, `UserPosition`, `Status`, `MaritalStatus`,
`Professions`, `Countries`, `Currencies`, `CommunicationChannels`,
`RequiredFieldSettings` (campos obrigatórios configuráveis por formulário).

## Imobiliário
`PropertyController` (o maior controller do projeto, ~2400 linhas — várias
sub-abas: cadastro, anexos, fotos, vídeos, mapa, progresso, preço, relatório,
site), `PropertyCategoryController`, `PropertyTypeController`,
`PropertyClassificationController`, `ConstructionController` (obras/
empreendimentos, com centro de custo vinculado — `ConstructionCostCenters`),
`ImmovableResourceController` (recursos/características do imóvel),
`ChangeLinkController`, `IntegrationDwvController` (integração com portal
imobiliário DWV, `src/libs/DwvApi.php`).

## Veículos ("repasse")
`VehiclesController` + cadastros auxiliares (`VehicleBrandsController`,
`VehicleModelsController`, `VehicleCategoriesController`, `VehicleTypesController`,
`VehicleColorsController`, `VehicleDoorsController`, `VehicleFuelsController`).
Fluxo de negociação: `PurchaseRequestsController` (requisição de compra),
`SaleRequestsController` (requisição de venda), `RecordOfPurchasedVehiclesController`,
`RecordOfSoldVehiclesController`, `RecordVehicleHistoryController`
(histórico/transferência do veículo). **Atenção**: as tabelas desse módulo não
constam em `db/realize_repasse.sql` (ver `docs/07-banco-de-dados.md`) — checar
estrutura real antes de mexer no schema.

## Clientes / CRM / Atendimento
`CustomerController` (+ `CustomerTypeController`, anexos, saldo, cônjuge,
contrato), `AttendanceController` (atendimento — kanban, call-center, timeline,
telefone, anexo), `AttendanceStatusController`, `LeadController` +
`LeadConfigController` + `LeadRedirectController` (captação/distribuição de
lead — **pausado em 2026-09-02**: tabelas `lead*`/`integrations` não existem
neste banco e os 3 controllers têm um guard que desativa todas as ações;
não presumir que o módulo funciona sem antes conferir esse guard),
`CalendarController`, `NotificationController`.

## Vendas / Financeiro
`SalesController` (venda: comissão, contrato, anexo, despesas, acordo de
pagamento, empresa construtora/imobiliária), `SaleRequestsController` (ver
Veículos), `BillReceiveController` + `BillReceiveInstallmentController`
(contas a receber e parcelas), `BillsToPayController` +
`BillsToPayInstallmentController` (contas a pagar e parcelas),
`RecordBillReceiveInstallmentController` / `RecordBillsToPayInstallmentController`
(registro/baixa de parcela), `BankAccountsController`, `BanksController`,
`CostCenterController` (usa `RecursiveCostCenter`, ver `docs/09-bibliotecas-libs.md`),
`FormOfPaymentController`, `CheckControlController` (controle de cheque, com
timeline própria — `CheckControlTimeline`), `ReportDreController` (DRE),
`ReportSalesController`, `ReportAttendanceController`.

## Cartões / apresentação
`DigitalCardController`, `PresentationCardController`, `PresentationsController`,
`CardPDFController`, `WaterMarkController` (marca d'água em foto),
`StandardContractController` (modelo de contrato com variáveis, ver
`StandardContractVariables`/`ContractsVariables`).

## Site institucional (embutido no mesmo painel)
`SettingsSiteController` (cores, layout, imagens, filtros, popup, meta tags,
script, e-mails do site), `BannersSiteController`, `DepositionsSiteController`
(depoimentos), `ImagesSiteController`, `TextsSiteController`, `NetworksSiteController`
(redes sociais), `PraiaSonhosSiteController` (site temático específico —
confirmar com o usuário o propósito exato antes de estender, nome sugere um
projeto/empreendimento específico, não um módulo genérico).

## Configuração do sistema
`SettingsController` (filtros, imagens, integrações, menus do sistema),
`IntegrationConfigController`, `CompareDatabasesController` (comparação entre
bancos — ferramenta interna, usar com cautela), `WaterMarkController`,
`ErrorController` (página 404/erro genérico).

## Antes de adicionar um módulo/entidade nova

1. Achar o módulo mais parecido na lista acima e usar como referência de
   controller + model + view (não começar do zero).
2. Se a entidade nova participa de financeiro/comissão, reaproveitar
   `CostCenter`/`FormOfPayment`/`CommissionArrangement` em vez de recriar lógica
   de pagamento.
3. Se a dúvida for "isso é imobiliário ou veículo?" ou "isso afeta o site
   institucional?", perguntar ao usuário — os dois domínios de negócio
   compartilham parte da estrutura (filial, usuário, financeiro) mas têm
   cadastros próprios.
