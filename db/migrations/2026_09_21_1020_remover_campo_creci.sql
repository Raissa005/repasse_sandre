-- ============================================================
-- Migration: remover campo "Creci" de Filial e Usuário
-- Data: 2026-09-21
-- Objetivo: CRECI é o registro obrigatório de corretor de imóveis no
--   Brasil, resíduo do domínio imobiliário antigo. Os campos ficavam
--   visíveis (não obrigatórios) nos formulários de Filial e Usuário, e o
--   valor de `branch.creci_legal` era impresso em recibos/contratos
--   (Contract::getInstalmentReceivePrintReceipt/getInstalmentToPayPrintReceipt,
--   alias `creci_filial`) e `users.creci` no cartão digital (cardPDF). A
--   pedido do usuário, os campos são removidos. O código que lê essas
--   colunas (models Branch.php/Contract.php/User.php, views
--   branch/add.php, branch/edit.php, users/add.php, users/edit.php,
--   users/rh.php, settings-site/index.php, cardPDF/index.php) precisa ser
--   atualizado para parar de referenciá-las ANTES desta migration ser
--   executada, ou as telas quebram (coluna inexistente). Ver etapa de
--   remoção de código correspondente.
-- Tabelas afetadas: branch, users
-- NÃO afeta `branch.example_property_value` (campo relacionado, mas de
--   decisão separada, não incluída nas 5 confirmações desta rodada).
-- Idempotente (2026-10-02): cada coluna só é removida se existir (consulta ao
--   information_schema + PREPARE, que funciona em MySQL e MariaDB — o
--   `DROP COLUMN IF EXISTS` só existe no MariaDB). Num banco criado a partir
--   da estrutura do banco local (sem as colunas) não faz nada.
-- ORDEM DE DEPLOY: num banco que já existe, rodar DEPOIS de publicar o código
--   novo (o código antigo ainda lê essas colunas).
-- ============================================================

-- >>> UP

SET @sql := (SELECT IF(COUNT(*) > 0, 'ALTER TABLE `branch` DROP COLUMN `creci_legal`', 'DO 0')
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'branch' AND COLUMN_NAME = 'creci_legal');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := (SELECT IF(COUNT(*) > 0, 'ALTER TABLE `users` DROP COLUMN `creci`', 'DO 0')
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'creci');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- >>> ROLLBACK (executar manualmente se precisar reverter)
-- ALTER TABLE `branch` ADD COLUMN `creci_legal` varchar(100) DEFAULT NULL;
-- ALTER TABLE `users` ADD COLUMN `creci` varchar(50) DEFAULT NULL;
-- (dado antigo não é recuperável pelo rollback — só a coluna volta vazia)
