-- ============================================================
-- Migration: criar tabela linked_check_control
-- Data: 2026-09-18
-- Objetivo: a tabela `linked_check_control` nunca existiu no banco, mas o
--   código de BillsToPayInstallmentController.php já depende dela desde
--   antes desta sessão de trabalho (registrar/desvincular qual cheque
--   recebido foi usado para pagar uma parcela de Contas a Pagar). Sem essa
--   tabela, o fluxo de pagamento com cheque falha (warning de SQL em dev,
--   provavelmente erro fatal em produção com ERRMODE_EXCEPTION) e o modal
--   "Lista de Cheques" quebra a resposta JSON esperada pelo front-end.
-- Tabelas afetadas: nova tabela `linked_check_control`.
-- Colunas id_check/id_installment/id_bills_to_pay: NOT NULL porque todo
--   INSERT existente no código sempre fornece os três juntos (ver
--   BillsToPayInstallmentController.php, handleSubmitInstallment).
-- created_by: NOT NULL, seguindo o padrão da tabela irmã
--   check_control_timeline — o código em BillsToPayInstallmentController.php
--   foi ajustado nesta mesma tarefa para sempre enviar created_by/created_at
--   no INSERT.
-- ============================================================

-- >>> UP
CREATE TABLE `linked_check_control` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_check` int(11) NOT NULL,
  `id_installment` int(11) NOT NULL,
  `id_bills_to_pay` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- >>> ROLLBACK (executar manualmente se precisar reverter)
-- DROP TABLE `linked_check_control`;
