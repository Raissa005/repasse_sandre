# Banco de dados

MySQL/MariaDB (`DB_TYPE = mysql`), acesso via PDO puro (sem ORM/migrations
automáticas de framework). O dump de referência completo (schema atual, ~121
tabelas) está em `db/realize_repasse.sql`.

## ⚠️ Regra obrigatória: nunca alterar o schema direto

**Nunca executar `ALTER TABLE`/`CREATE TABLE`/`DROP`/etc. diretamente contra um
banco, e nunca editar `db/realize_repasse.sql` como se fosse uma migration.**
Esse arquivo é um dump/snapshot, não uma migration incremental.

Sempre que uma tarefa exigir mudança de schema (nova tabela, nova coluna, novo
índice, seed de dados de referência):

1. Criar um arquivo novo em `db/migrations/`, nomeado
   `AAAA_MM_DD_HHmm_descricao_curta.sql` (ordem cronológica, minúsculo,
   snake_case), por exemplo: `db/migrations/2026_08_25_1430_add_column_ativo_bill_receive.sql`.
2. O arquivo deve conter **apenas o SQL incremental** (o `ALTER`/`CREATE`
   necessário), comentado no topo explicando o objetivo, e — quando fizer
   sentido — a instrução de rollback comentada logo abaixo.
3. **Não executar o arquivo** — ele é gerado para o usuário revisar e rodar
   manualmente (`mysql -u ... < arquivo.sql` ou via phpMyAdmin/Workbench).
4. Depois que o usuário confirmar que rodou, atualizar também
   `db/realize_repasse.sql` (ou pelo menos anotar a mudança) e os
   Models/Controllers que passam a usar a coluna/tabela nova — mas isso é uma
   etapa separada, só depois da confirmação, nunca antes.
5. Se a mudança de schema tiver qualquer ambiguidade de negócio (nome de coluna,
   tipo, default, se deve ser nullable, se afeta relatório existente) —
   **perguntar antes de gerar o SQL**, não adivinhar.

Ver template pronto em `db/migrations/README.md`.

## Convenções observadas no schema atual

Seguir estas convenções ao desenhar uma tabela/coluna nova, para manter
consistência com o que já existe:

- PK sempre `id` `int(11) NOT NULL AUTO_INCREMENT`.
- Charset/collation predominante: `latin1`/`latin1_swedish_ci` (não `utf8` —
  apesar de `DB_CHARSET` na conexão PDO ser `utf8`; **não presumir** qual usar
  numa tabela nova sem checar tabelas vizinhas do mesmo módulo, ou perguntar).
- Coluna de ativo/inativo: a grande maioria (74 ocorrências) usa
  **`status` `tinyint(4)` (0/1), default `1`**. Um grupo pequeno e específico de
  tabelas de **site institucional** (`banner`, `depoimento`, `imagem`,
  `praia_sonho`, `rede_social`) usa **`ativo`** em vez de `status` — para essas,
  usar `ModelGenerico::enableItem2`/`disableItem2` (não os métodos `enableItem`/
  `disableItem` padrão da base `Model`, que assumem `status`). Ao criar tabela
  nova, usar `status` — só usar `ativo` se a tabela for claramente parte do
  grupo de site institucional e for consistente com as tabelas vizinhas.
- Auditoria: `created_at` (`timestamp`, default `current_timestamp()`),
  `updated_at` (`datetime`, nullable), `created_by`/`updated_by`
  (`int(11)`, FK lógica para `users.id`, sem constraint declarada no dump).
- Chave estrangeira lógica: prefixo `id_` + nome da tabela/entidade referenciada
  (`id_city`, `id_branch`, `id_cost_center_commission_branch`, etc.) — **sem
  `FOREIGN KEY` declarada no MySQL** (integridade é garantida só pela aplicação).
  Não adicionar `FOREIGN KEY CONSTRAINT` numa migration nova sem alinhar antes —
  quebraria o padrão existente e pode falhar contra dados legados órfãos.
- Campos de imagem/logo seguem o trio `<nome>_capa` (`binary(1)`, se tem imagem),
  `<nome>_cont` (`int`, contador/versão usado no nome do arquivo para cache-busting),
  `<nome>_ext` (`varchar`, extensão) — ver `branch.logo_menu_*` e o fluxo de
  upload em `docs/03-controllers.md`.
- Dinheiro: `decimal(10,2)`. Percentual: também `decimal(10,2)`.

## Onde encontrar a lista completa de tabelas

Não duplicar aqui a lista das ~121 tabelas (ela muda; a fonte de verdade é o
dump). Para consultar: `grep -Eo "CREATE TABLE \`?[a-zA-Z0-9_]+\`?" db/realize_repasse.sql`.
Agrupamentos principais por prefixo/tema (para achar rápido):

- Imobiliário: `property*`, `products*` (linha de produto/imóvel legado),
  `constructions`, `construction_*`, `immovable_resource`, `contracts*`,
  `displayed_properties`, `standard_contract*`.
- Veículos: **`db/realize_repasse.sql` está desatualizado em relação ao módulo de
  veículos** — o código (`src/model/Vehicles.php` e os demais `src/model/Vehicle*.php`)
  referencia tabelas como `vehicles`, `vehicle_brands`, `vehicle_models`,
  `vehicle_categories`, `vehicle_types`, `vehicle_doors`, `vehicle_colors`,
  `vehicle_fuels`, `vehicle_costs`, `vehicle_images`, `vehicle_attachments`,
  `vehicle_purchases`, `vehicle_observations`, `vehicle_transfer_observation`,
  mas **nenhuma delas existe no dump atual**. Ou seja, o dump não é a fonte de
  verdade completa do schema em produção para este módulo. Antes de assumir a
  estrutura exata dessas tabelas (colunas, tipos), **pedir ao usuário um dump
  atualizado** ou confirmar a estrutura real do banco em uso — não inferir só
  pelo nome das colunas usadas no PHP.
- Financeiro: `bill_receive*`, `bills_to_pay*`, `bank_accounts`, `cost_center`,
  `payment_status`, `payments_of_sales`, `sales_charge_payment_agreement`,
  `arrangement_payment_charges_invoice_receive_installment`.
- CRM/Atendimento: `attendance*`, `lead*`, `notification*`, `communication_channels`.
  `dashboard`, `dashboard_order`.
- Estrutura/acesso: `menu`, `menu_access`, `users`, `users_profiles`, `branch`,
  `branch_user_position`, `user_branches`, `user_position`, `tokens`.
  `manager_team`, `cities`, `states`, `countries`, `currencies`.
- Site institucional: `banner`, `depoimento`, `imagem`, `texto`, `texto_foto`,
  `rede_social`, `presentation*`, `configuracao*`, `website_filters`.

## Antes de escrever uma query nova

1. Ver se a tabela já tem um Model (`src/model/`) com método equivalente antes de
   escrever SQL do zero (`docs/04-models.md`).
2. Conferir o nome exato de colunas no dump (`db/realize_repasse.sql`) — não
   assumir nome de coluna pelo padrão de outra tabela.
3. Se a query precisar de uma coluna/tabela que não existe ainda, isso é sinal de
   que precisa de uma migration — seguir o processo no topo deste arquivo.
