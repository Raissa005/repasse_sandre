# Migrations manuais

Esta pasta guarda **SQL incremental gerado para execução manual** — não existe
runner automático de migration neste projeto (sem Doctrine/Phinx/etc.).

## Regras

- 1 arquivo por mudança lógica, nomeado `AAAA_MM_DD_HHmm_descricao_curta.sql`
  (data/hora de criação do arquivo, não da execução).
- Ninguém (nem Claude) executa o arquivo automaticamente contra um banco — ele é
  gerado para o usuário revisar e rodar manualmente
  (`mysql -u usuario -p nome_do_banco < db/migrations/arquivo.sql`, ou via
  phpMyAdmin/Workbench).
- Cada arquivo deve ser **idempotente na medida do possível** (`IF NOT EXISTS`
  onde o dialeto do MySQL em uso permitir) e conter, comentado no topo:
  - Objetivo da mudança.
  - Tabelas/colunas afetadas.
  - Instrução de rollback (comentada), quando fizer sentido reverter.
- Seguir as convenções de coluna documentadas em `docs/07-banco-de-dados.md`
  (nome de PK, `status` vs `ativo`, `created_at`/`updated_at`/`created_by`/
  `updated_by`, prefixo `id_` para FK lógica, sem `FOREIGN KEY` declarada).
- Depois que o usuário confirmar que rodou a migration, atualizar o Model/
  Controller/View que passam a depender da mudança — nunca antes.

## Template

```sql
-- ============================================================
-- Migration: <descrição curta>
-- Data: <AAAA-MM-DD>
-- Objetivo: <por que essa mudança é necessária>
-- Tabelas afetadas: <lista>
-- ============================================================

-- >>> UP
ALTER TABLE `nome_da_tabela`
  ADD COLUMN `nova_coluna` VARCHAR(255) DEFAULT NULL AFTER `coluna_existente`;

-- >>> ROLLBACK (executar manualmente se precisar reverter)
-- ALTER TABLE `nome_da_tabela` DROP COLUMN `nova_coluna`;
```
