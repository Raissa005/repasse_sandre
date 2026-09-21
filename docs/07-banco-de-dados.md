# Banco de dados

MySQL/MariaDB (`DB_TYPE = mysql`), acesso via PDO puro (sem ORM/migrations
automáticas de framework).

## ⚠️ `db/realize_repasse.sql` está obsoleto — não é a base de nada

Esse arquivo é um dump antigo, de quando o sistema ainda era só imobiliário
(schema com ~121 tabelas). **Não é mais a fonte de verdade do schema e não
deve ser usado para criar nenhum ambiente novo** (nem produção, nem um dev
novo) — ele tem tabelas/dados que já foram removidos do sistema (domínio
imobiliário) e **ids diferentes** dos que este banco de desenvolvimento usa
hoje para tabelas de referência como `customer_type` (ex.: no dump,
`Fornecedor` é id=10; neste banco, é id=2 — ver migration
`2026_09_02_1400_novos_tipos_cliente_fornecedor_vendedor.sql`). Confirmado
com o usuário em 2026-09-03: **o banco de produção será criado a partir da
estrutura deste banco de desenvolvimento local** (não do dump), então todo
código deve continuar assumindo esse banco (e as migrations em
`db/migrations/`) como referência real — nunca `db/realize_repasse.sql`.
Ideal, quando sobrar tempo: gerar um dump atualizado a partir deste banco e
substituir/aposentar o arquivo antigo, ou pelo menos renomeá-lo deixando
claro que é histórico.

## ⚠️ Regra obrigatória: nunca alterar o banco direto — nem schema, nem dado

**Nunca executar `ALTER`/`CREATE`/`DROP`/`TRUNCATE`/`RENAME TABLE`/`UPDATE`/
`INSERT`/`DELETE`/`REPLACE INTO`/`LOAD DATA`/`GRANT`/`REVOKE`/`SET PASSWORD`
diretamente contra um banco (nenhum ambiente, incluindo local/dev), e nunca
editar `db/realize_repasse.sql` como se fosse uma migration.** Esse arquivo é
um dump/snapshot, não uma migration incremental. A regra vale também pra
qualquer **caminho indireto** que produza o mesmo efeito de escrita — rodar um
script PHP avulso que usa a camada de Model/PDO da própria aplicação (`php -r`,
`php arquivo.php`), ou fazer uma requisição HTTP/curl contra um endpoint da
aplicação rodando que escreva no banco como efeito colateral (ex.: "testar"
uma feature de cadastro submetendo o formulário via `curl` em vez de pedir pro
usuário testar). Isso vale pra **qualquer** alteração no banco — schema
(tabela/coluna/índice), seed de referência, correção pontual de uma linha,
dado de teste, reset de senha de usuário —, não importa o motivo nem o tamanho
da mudança, **e não existe exceção mesmo que o usuário peça explicitamente pra
rodar direto "só dessa vez"**.

Sempre que uma tarefa exigir alguma alteração no banco:

1. Criar um arquivo novo em `db/migrations/`, nomeado
   `AAAA_MM_DD_HHmm_descricao_curta.sql` (ordem cronológica, minúsculo,
   snake_case), por exemplo: `db/migrations/2026_08_25_1430_add_column_ativo_bill_receive.sql`.
2. O arquivo deve conter **apenas o SQL incremental** (o `ALTER`/`CREATE`/
   `UPDATE`/`INSERT`/`DELETE` necessário), comentado no topo explicando o
   objetivo, e — quando fizer sentido — a instrução de rollback comentada logo
   abaixo.
3. **Não executar o arquivo** — ele é gerado para o usuário revisar e rodar
   manualmente (`mysql -u ... < arquivo.sql` ou via phpMyAdmin/Workbench),
   mesmo que seja um `UPDATE` de uma linha só, ou algo pedido pra rodar "só
   local, rapidinho, pra testar".
4. Depois que o usuário confirmar que rodou, atualizar também
   `db/realize_repasse.sql` (ou pelo menos anotar a mudança) e os
   Models/Controllers que passam a usar a coluna/tabela nova — mas isso é uma
   etapa separada, só depois da confirmação, nunca antes.
5. Se a mudança tiver qualquer ambiguidade de negócio (nome de coluna, tipo,
   default, se deve ser nullable, se afeta relatório existente, qual linha
   exatamente deve ser afetada) — **perguntar antes de gerar o SQL**, não
   adivinhar.

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

A fonte de verdade é o **banco de desenvolvimento local em uso** (não o dump —
ver aviso no topo deste arquivo). Para consultar a lista real de tabelas:
`SHOW TABLES;` direto no banco (ex.: `mysql -u root veiculos_repasse -e "SHOW TABLES;"`).
Não presumir estrutura de coluna pelo nome usado no PHP nem pelo dump —
conferir com `SHOW COLUMNS FROM <tabela>;` antes de escrever uma query nova
contra tabela que ainda não foi tocada nesta sessão de trabalho.

Agrupamentos principais por prefixo/tema (para achar rápido):

- Imobiliário: removido do código nesta sessão de trabalho (2026-09) — as
  tabelas (`property*`, `products*`, `constructions`, `immovable_resource`,
  `contracts*`, `displayed_properties`, `standard_contract*`, etc.) podem
  ainda existir fisicamente no banco (nunca foram dropadas), mas não são mais
  referenciadas pelo código ativo. Não reaproveitar sem antes checar
  `docs/11-duplicidades-legado.md` e a memória do projeto sobre a remoção do
  domínio imobiliário.
- Veículos: `vehicles`, `vehicle_brands`, `vehicle_models`, `vehicle_categories`,
  `vehicle_types`, `vehicle_doors`, `vehicle_colors`, `vehicle_fuels`,
  `vehicle_costs`, `vehicle_images`, `vehicle_attachments`, `vehicle_purchases`,
  `vehicle_observations`, `vehicle_transfer_observation`, `vehicles_request_sale`.
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
2. Conferir o nome exato de colunas direto no banco (`SHOW COLUMNS FROM <tabela>;`)
   — não assumir nome de coluna pelo padrão de outra tabela nem pelo dump
   (`db/realize_repasse.sql` está obsoleto, ver aviso no topo deste arquivo).
3. Se a query precisar de uma coluna/tabela que não existe ainda, isso é sinal de
   que precisa de uma migration — seguir o processo no topo deste arquivo.
4. Para tabelas "tipo-enum" (linhas de referência com significado no código,
   ex.: `customer_type`, `attendance_status`): **nunca hardcodar o id numérico**
   no PHP — o id não é garantido ser igual entre bancos (foi exatamente isso
   que causou o bug do filtro "Fornecedor" com id antigo/errado, ver memória do
   projeto "customer-type-hardcoded-ids-swept"). Buscar pelo nome
   (`CustomerType::getIdByName()` é o exemplo já existente) ou, se realmente
   precisar do id fixo, deixar isso bem comentado e confirmado contra o banco
   atual.
