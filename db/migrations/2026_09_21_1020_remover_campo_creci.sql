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
-- ============================================================

-- >>> UP

ALTER TABLE `branch` DROP COLUMN `creci_legal`;
ALTER TABLE `users` DROP COLUMN `creci`;

-- >>> ROLLBACK (executar manualmente se precisar reverter)
-- ALTER TABLE `branch` ADD COLUMN `creci_legal` varchar(100) DEFAULT NULL;
-- ALTER TABLE `users` ADD COLUMN `creci` varchar(50) DEFAULT NULL;
-- (dado antigo não é recuperável pelo rollback — só a coluna volta vazia)
