-- ============================================================
-- Migration: remover colunas de publicação no site institucional da tabela users
-- Data: 2026-09-02
-- Objetivo: as colunas `permission_publish` ("Publicar no Site") e
--   `show_team` ("Exibir na Equipe do Site") eram usadas pelo antigo
--   domínio imobiliário para controlar publicação de conteúdo num site
--   institucional externo. Esse domínio (property/construction/
--   presentations/etc.) já foi removido do código-fonte, e nenhum site
--   externo consulta mais este banco. Os campos foram removidos das
--   telas src/view/users/add.php e src/view/users/edit.php, e das
--   gravações em src/model/User.php.
-- Tabelas afetadas: users
-- ATENÇÃO: confirmado que nenhum site externo lê essas colunas deste
--   banco antes de rodar esta migration.
-- ============================================================

-- >>> UP

ALTER TABLE `users`
  DROP COLUMN `permission_publish`,
  DROP COLUMN `show_team`;

-- >>> ROLLBACK (executar manualmente se precisar reverter)
-- ALTER TABLE `users`
--   ADD COLUMN `permission_publish` BINARY(1) DEFAULT 0,
--   ADD COLUMN `show_team` BINARY(1) DEFAULT 0;
