# Auditoria 1 — Resíduos do domínio imobiliário

**Data**: 2026-09-28. **Tipo**: auditoria somente leitura — 


## Contexto e metodologia

O sistema (`repasse_sandre` / banco `veiculos_repasse`) era originalmente uma
plataforma de venda de imóveis, migrada para revenda de veículos. Já existe
uma auditoria anterior completa, documentada em
[`docs/migracao-imobiliario-veiculos.md`](docs/migracao-imobiliario-veiculos.md)
(2026-09-21/22), que removeu a maior parte do resíduo conhecido até aquela
data (cluster `Property`, contratos de imóvel, módulo "Site institucional",
campo Creci de usuário/filial, etc.). Este documento **não substitui** aquele
— ele é uma nova varredura completa (do zero, cobrindo inclusive áreas já
revisadas), feita para detectar tanto resíduo que escapou da limpeza anterior
quanto qualquer coisa reintroduzida pelos commits mais recentes.

**Escopo coberto**: todo `src/controller/` (project e ajax), `src/model/`,
`src/libs/`, `src/core/`, `src/components/`, todo `src/view/`, `public/js/`,
`public/css/`, geração de PDF (`cardPDF`), templates de e-mail (não existem
templates HTML de e-mail na aplicação — `src/libs/Email.php` é só uma DTO), o
dump `db/realize_repasse.sql`, os arquivos em `db/migrations/`, e o **banco de
produção real** (`veiculos_repasse`, credenciais de `src/config/config.php`),
consultado via `SHOW`/`SELECT` (somente leitura).

**Termos pesquisados** (grep exaustivo, maiúsculo/minúsculo, com/sem acento,
singular/plural): `Property`, imóvel/imóveis/imobiliária/imobiliário, `Lead`,
`Calendar`, corretor, aluguel, locação, IPTU, condomínio, quartos, m²/área,
matrícula, CRECI, construção/obra/cronograma, `praia_sonho`, `immovable`,
`property_*`, `home_category_link`, `integrations`/`facebookLeadAds`,
`contracts`, `displayed_properties`, `construction_properties`.

**Como usar este documento**: seções 1-4 são achados **novos**, não cobertos
pela auditoria anterior. Seção 5 apenas reconfirma que itens já avaliados e
mantidos deliberadamente continuam como estavam (não são novidade, incluídos
por completude já que o pedido foi varrer tudo). Seção 6 lista módulos
verificados e considerados limpos. Seção 7 é a tabela-resumo.

---

## 1. Achado crítico — aba "Vendas" do cliente nunca aparece (A)

**Causa raiz**: o Model `Sales` monta, incondicionalmente, um `INNER JOIN`
contra a tabela `products` — que ainda tem o schema íntegro do domínio
imobiliário e está vazia.

- **Arquivo**: `src/model/Sales.php:15-52` (construtor)
  ```php
  $this->table = 'sales';
  $joins = [
      ...
      (object)[
          'table' => 'products',
          'join' => 'inner',
          'where' => "this->table.id = {$this->table}.id_product",
      ],
      ...
  ];
  parent::__construct($this->table, $joins);
  ```
  Esse `INNER JOIN` faz parte da construção do Model, então afeta **qualquer**
  query feita através dele, não só o método abaixo.

- **Arquivo**: `src/model/Sales.php:54-84` `getSalesForCustomers()`
  ```php
  public function getSalesForCustomers(int $customerId): object
  {
      ...
      return $this->getWithFiltersAllItems($filters, $columns, $options);
  }
  ```
  Como o `INNER JOIN products` nunca encontra correspondência (`products`
  confirmada com **0 linhas** no banco real, via `DESCRIBE`/`SELECT COUNT(*)`,
  e sem nenhuma relação com a tabela `vehicles`), o resultado é sempre vazio —
  **independente de quantas vendas reais existam em `sales`**.

- **Arquivo**: `src/controller/project/CustomerController.php:91,109-111`
  ```php
  $menuSales = (new Sales)->getSalesForCustomers($customerId)->count;
  ...
  if ($menuSales != 0) {
      array_push($navTabs, (object)['text' => 'Vendas', 'route' => URL . $this->route . '/sales/' . $customerId, ...]);
  }
  ```
  A aba "Vendas" só é adicionada ao menu do cliente se `$menuSales != 0`.
  Como esse valor é sempre `0` (efeito do bug acima), **a aba nunca aparece
  para nenhum cliente**, mesmo que ele tenha vendas de veículo reais
  registradas em `sales`.

- **Arquivo**: `src/view/customer/sales.php:22,34` (acessível por URL direta,
  `?pg1=sales`, mesmo sem a aba visível)
  ```php
  <th class="text-center">Imóvel</th>
  ...
  <td><?= $sale->products_name ?></td>
  ```
  Rótulo de coluna residual "Imóvel" — mostra na verdade o nome vindo da
  tabela `products` (que, de novo, não tem relação com o veículo vendido).

**Classificação**: **(A)** código ativo que quebra (funcionalidade real
inacessível) + **(B)** texto residual "Imóvel" na mesma tela. Achado
confirmado de forma independente por duas frentes de investigação (backend e
front-end), alta confiança.

**Não corrigido nesta auditoria** (fora do escopo, que é somente leitura).
Correção envolve decisão de negócio: a tela deveria passar a usar a tabela
`vehicles` (rótulo "Veículo") ou ser removida/repensada? Ficou pendente de
alinhamento com o usuário.

---

## 2. Achado — campos "creci" ainda são feature administrativa ativa (B)

A auditoria anterior tratou os campos `cor_fonte_creci`/`fonte_creci`/
`tamanho_fonte_creci` como **cosmético de CSS** (nome desatualizado de um
elemento visual do PDF). O que não foi coberto: o módulo `digital-card`
**inteiro** (controller + telas de cadastro) ainda cria/edita esses campos
por nome explícito — não é resíduo passivo, é tela administrativa em uso.

- **Arquivo**: `src/controller/project/DigitalCardController.php:71,80-81,175,184-185`
  ```php
  'cor_fonte_creci' => $_POST['cor_fonte_creci'],
  'fonte_creci' => $_POST['fonte_creci'],
  'tamanho_fonte_creci' => $_POST['tamanho_fonte_creci'],
  ```
  Em `handleSubmitAddItem()` e `handleSubmitEditItem()` — rotas ativas, sem
  guard, persistem esses 3 campos na tabela `digital_card`.

- **Arquivo**: `src/view/digital-card/add.php:66-173` e `edit.php:69-176` —
  labels literais visíveis: "Cor de fonte creci", "Fonte creci", "Tamanho
  fonte creci".

- **Arquivo**: `src/view/cardPDF/index.php:254` — consome `fonte_creci` de
  fato (já identificado antes: reaproveitado para estilizar a legenda do
  rodapé do cartão, sem relação funcional real com CRECI).

**Classificação**: **(B)** nome de campo/label residual em feature ativa —
não quebra nada, mas confunde quem for mexer nessa tela sem saber que
"creci" hoje não tem relação com corretor de imóveis.

---

## 3. Achados — código morto novo (C)

Métodos/queries com zero chamadores confirmados em todo o projeto, que ainda
referenciam o domínio imobiliário:

| Arquivo:linha | Método | Detalhe |
|---|---|---|
| `src/model/Customer.php:151-188` | `getAllCustomerWithProperties()` | `INNER JOIN products`, zero chamadores |
| `src/model/Attendance.php:424-435` | `deleteProductAttendance()` | `DELETE FROM displayed_properties` — tabela **inexistente**; se fosse chamada hoje, geraria `PDOException` fatal |
| `src/model/Attendance.php:437-450` | `getPresentationsIPByIdDisplayed()` | usa `presentations.id_displayed_properties` (coluna existe, resíduo de schema não migrado); zero chamadores |
| `src/model/Sales.php:250-266` | `getItemById8161()` | `INNER JOIN products`, alias `property_name`; zero chamadores |
| `src/model/Sales.php:86-169` | `getAndFilterAllItem()` | `JOIN products`; zero chamadores |
| `src/model/Sales.php:171-248` | `getAndFilterAllSales()` | já marcada `/**Descontinuar */` no próprio código; zero chamadores |
| `src/model/Branch.php:159-175` | `getBranchesByProperties()` | já era "loose end" registrado na auditoria anterior; reconfirmado — `JOIN property_branch` (tabela inexistente), zero chamadores |

Resíduo de baixa severidade (não é código morto, mas dado órfão dentro de
query ativa):

- **`payments_of_sales.id_property`** (coluna) — selecionada em
  `src/model/Sales.php:393` (dentro de `getPortionById()`, método ativo, chamado por
  `src/controller/ajax/AjaxController.php:210`), ao lado de `vehicle`/
  `license_plate`, que semanticamente a substituíram no domínio de veículo.
  Não quebra nada (só carrega um campo provavelmente sempre nulo).

---

## 4. Achados — resíduo de schema no banco real (sem código referenciando)

Confirmado via `SHOW TABLES`/`DESCRIBE`/`SELECT` direto no banco de produção
`veiculos_repasse` (não é inferência do dump, que já está comprovadamente
desatualizado):

| Tabela/coluna | Situação | Classificação |
|---|---|---|
| `variable_property_authorization_contract` | 0 linhas, FK para tabela inexistente, zero código | (C) |
| `standard_contract_variables` | existe, zero código referenciando | (C) |
| `website_filters` | **7 linhas de dado real** (filtros de busca de imóvel: Tipo/Categoria/Cidade/Bairro/Valor de/Valor até), zero código | (C) |
| `presentation_card`, `presentation_config`, `branch_presentation_config`, `user_presentation_config`, `attendance_presentation_config` | cluster de 5 tabelas — cartão de apresentação público de imóvel, zero código | (C) |
| `branch.immovable_record` (coluna) | zero código lendo/gravando | (C) |
| `customer.creci` (coluna) | **terceira coluna "creci"** no schema — além de `branch.creci_legal` e `users.creci`, já removidas pela migration `2026_09_21_1020_remover_campo_creci.sql`; esta escapou daquela limpeza | (C) |

Estes são achados de **schema**, não de código-fonte — nenhuma migration foi
gerada nesta auditoria (escopo é só leitura). Ficam registrados para decisão
futura do usuário sobre se vale dropar.

---

## 5. Itens já conhecidos — reconfirmados, sem mudança

Os itens abaixo já haviam sido avaliados e deliberadamente mantidos pela
auditoria de 2026-09-21/22. Reconfirmados nesta varredura: nenhum piorou,
nenhum voltou a quebrar. Listados por completude, não representam decisão
nova.

| Item | Situação reconfirmada |
|---|---|
| `src/view/branch/paymentArrangement.php:14,19` | "Valor do Imóvel (Exemplo)" / tooltips "Porcentagem Venda do imóvel" — cosmético, mantido (B) |
| `src/view/branch/images.php:34` | "EXCLUIR LOGO RODAPE CARTÃO IMÓVEL" — cosmético, mantido (B) |
| `src/components/UserDropdownComponent.php:36` | botão "Imóveis" — zero inclusões em qualquer view, órfão documentado (C) |
| `src/model/Contract.php:57,136` | alias `autorizacaoImovel_produtos` em métodos de recibo ativos (`getInstalmentReceivePrintReceipt`/`getInstalmentToPayPrintReceipt`), sem uso downstream (B, inofensivo) |
| `branch.example_property_value` | campo ativo (simulação de comissão), nome desatualizado (B) |
| `src/view/vehicles/purchase.php:101,103` | "Corretor Compra" — confirmado **não é resíduo**, é campo genuíno do domínio de veículo |
| `src/view/attendance/kanban.php:58,62` | bug do link `?properties=true` confirmado **corrigido**, continua `?vehicles=true` |
| `NetworksSiteController`/`NetworksSite.php` | resolvido: o model usa a tabela real `rede_social`, não existe (nem é esperada) uma tabela `networks_site` — não é bug |
| Menu de navegação real (tabela `menu`) | confirmado 100% limpo — nenhuma rota de imóvel/lead/property/integration ativa |
| `db/realize_repasse.sql` (dump versionado) | reconfirmado que **não reflete** o schema do banco de produção real (tem tabelas de imóvel/lead que não existem lá) — não usar como fonte de verdade |

---

## 6. Módulos verificados e considerados limpos

- **Calendar** (`CalendarController` project/ajax, `Calendar.php`) — zero
  ocorrência de qualquer termo do domínio imobiliário.
- **Lead** (`LeadController` project/ajax, `LeadConfigController`,
  `LeadRedirectController`) — guard de desativação confirmado íntegro nos 4
  pontos de entrada (redirect com `error=lead_disabled`, `http_response_code(404)`,
  `exit`). **Não é resíduo acidental** — é desativação proposital,
  documentada no próprio código-fonte (comentário datado de 2026-09-02). O
  código guardado (hoje inalcançável) ainda referencia tabelas inexistentes
  (`lead`, `lead_random`, `lead_working_date`, `displayed_properties`) e a
  tabela `products`; registrado por completude, não corrigido — se o guard
  for removido no futuro sem reescrever essas referências, o módulo quebra
  imediatamente.
- Termos pesquisados em todo `src/` e `public/` **sem nenhuma ocorrência
  real** (fora os casos já tratados acima): `corretor` (exceto o caso
  legítimo de `vehicles/purchase.php`), aluguel, locação, IPTU, condomínio,
  quartos, matrícula, m²/metragem, `praia_sonho`, `immovable`,
  `property_category`, `property_type`, `home_category_link`,
  `IntegrationConfig`, `facebookLeadAds`.

**Observação incidental** (fora do escopo de resíduo imobiliário, mencionada
por transparência): `src/view/notice/index.php` e
`src/view/sync-folder/index.php` parecem órfãos — nenhum controller os
inclui — mas o conteúdo de ambos não tem relação com o domínio imobiliário
(aviso de manutenção do sistema e ferramenta de sincronização de PRs,
respectivamente). Não é achado desta auditoria, só um registro para uma
eventual limpeza de código morto separada.

---

## 7. Tabela-resumo

| # | Arquivo:linha | Trecho/resumo | Classe |
|---|---|---|---|
| 1 | `src/model/Sales.php:15-52` | `INNER JOIN products` incondicional no construtor do Model | A |
| 1 | `src/model/Sales.php:54-84` | `getSalesForCustomers()` sempre retorna `count=0` | A |
| 1 | `src/controller/project/CustomerController.php:91,109-111` | aba "Vendas" nunca exibida (`$menuSales != 0` nunca verdadeiro) | A |
| 1 | `src/view/customer/sales.php:22,34` | `<th>Imóvel</th>` exibindo `products_name` | B |
| 2 | `src/controller/project/DigitalCardController.php:71,80-81,175,184-185` | leitura/gravação de `cor_fonte_creci`/`fonte_creci`/`tamanho_fonte_creci` | B |
| 2 | `src/view/digital-card/add.php:66-173`, `edit.php:69-176` | labels "Cor de fonte creci" etc. | B |
| 3 | `src/model/Customer.php:151-188` | `getAllCustomerWithProperties()` morto | C |
| 3 | `src/model/Attendance.php:424-435` | `deleteProductAttendance()` morto, tabela inexistente | C |
| 3 | `src/model/Attendance.php:437-450` | `getPresentationsIPByIdDisplayed()` morto | C |
| 3 | `src/model/Sales.php:250-266` | `getItemById8161()` morto | C |
| 3 | `src/model/Sales.php:86-169` | `getAndFilterAllItem()` morto | C |
| 3 | `src/model/Sales.php:171-248` | `getAndFilterAllSales()` morto (`/**Descontinuar */`) | C |
| 3 | `src/model/Branch.php:159-175` | `getBranchesByProperties()` morto (já era loose end) | C |
| 3 | `payments_of_sales.id_property` (coluna) | selecionada em `Sales::getPortionById()`, órfã semanticamente | C |
| 4 | `variable_property_authorization_contract` (tabela) | 0 linhas, zero código | C |
| 4 | `standard_contract_variables` (tabela) | zero código | C |
| 4 | `website_filters` (tabela) | 7 linhas de dado real, zero código | C |
| 4 | `presentation_card`/`presentation_config`/`branch_presentation_config`/`user_presentation_config`/`attendance_presentation_config` (5 tabelas) | zero código | C |
| 4 | `branch.immovable_record` (coluna) | zero código | C |
| 4 | `customer.creci` (coluna) | escapou da migration `2026_09_21_1020` | C |
| 5 | `src/view/branch/paymentArrangement.php:14,19` | "Valor do Imóvel (Exemplo)" | B |
| 5 | `src/view/branch/images.php:34` | "CARTÃO IMÓVEL" | B |
| 5 | `src/components/UserDropdownComponent.php:36` | botão "Imóveis" órfão | C |
| 5 | `src/model/Contract.php:57,136` | alias `autorizacaoImovel_produtos` | B |
| 5 | `branch.example_property_value` (coluna) | campo ativo, nome desatualizado | B |

---

## Conclusão

O item mais importante desta auditoria é o **achado 1** (seção 1): um bug
funcional real e ativo, não apenas cosmético, causado por resíduo do domínio
imobiliário — a aba "Vendas" do cadastro de cliente está inacessível para
todo cliente, devido ao `INNER JOIN` contra a tabela `products` (schema de
imóvel, vazia). Recomenda-se alinhar com o usuário se e como corrigir isso
(migrar a query para `vehicles` e renomear "Imóvel" → "Veículo", ou remover a
aba) antes de tratar os demais achados, que são de severidade bem menor
(código morto inofensivo ou nomes de campo desatualizados).

Os achados de schema (seção 4) não foram convertidos em migration nesta
auditoria (escopo é somente leitura) — ficam para decisão e execução manual
do usuário, seguindo a regra do projeto de nunca alterar o banco diretamente.
