-- ============================================================
-- Migration: desativar templates de contrato do domínio imobiliário
-- Data: 2026-09-21
-- Objetivo: a tabela `standard_contract` tem 9 registros reais; só o id=17
--   ("Recibo Simples", type_contract=4) está em uso hoje (via
--   Contract::getInstalmentReceivePrintReceipt/getInstalmentToPayPrintReceipt,
--   para recibo de parcela). Os outros 8 são templates do domínio
--   imobiliário antigo (propostas/autorizações de compra e venda de imóvel)
--   ou entradas de teste, sem tela de administração para gerenciá-los. A
--   pedido do usuário, são desativados (status=0) em vez de excluídos, para
--   preservar histórico. Nenhum caminho de código ativo lê esses registros
--   hoje (StandardContract::getAndFilterAllStandardContract/
--   getAllStandardContract/getActivesAndFilterStandardContract não têm
--   nenhum controller que as chame), então desativar não tem efeito
--   funcional observável — é só higiene de dado.
-- Tabelas afetadas: standard_contract
-- ATENÇÃO: id=10 já está com status=0 hoje (confirmado antes de escrever
--   esta migration) — o UPDATE abaixo é idempotente para ele.
-- ============================================================

-- >>> UP

UPDATE `standard_contract` SET `status` = 0 WHERE `id` IN (5, 10, 11, 12, 14, 15, 16, 18);

-- >>> ROLLBACK (executar manualmente se precisar reverter)
-- UPDATE `standard_contract` SET `status` = 1 WHERE `id` IN (5, 11, 12, 14, 15, 16, 18);
-- UPDATE `standard_contract` SET `status` = 0 WHERE `id` = 10; -- já estava 0 antes desta migration
