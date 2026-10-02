-- ============================================================
-- Migration: criar tabela login_attempts
-- Data: 2026-10-02
-- Objetivo: A4 do PLANO-CORRECOES.md — limitar tentativas de login contra
--   força bruta. Regra decidida pelo usuário: 5 tentativas erradas em até
--   15 minutos bloqueiam o login daquele e-mail por 15 minutos; concluir a
--   recuperação de senha zera o contador; a mensagem não revela se o
--   usuário existe.
-- Tabelas afetadas: nova tabela `login_attempts`.
-- email: o e-mail digitado no login (minúsculo, sem espaços), exista ou não
--   um usuário com ele — contar também e-mails inexistentes é o que impede
--   descobrir quais e-mails têm conta. varchar(191) para caber no índice
--   único em utf8mb4. Sem `id_user` justamente por isso.
-- attempts / last_attempt_at: erros seguidos; o contador recomeça se o
--   último erro foi há mais de 15 minutos.
-- locked_until: até quando o e-mail fica bloqueado (NULL = não bloqueado).
-- Sem `status`/`created_by`: é controle interno do login (não há usuário
--   logado no momento da gravação) e a linha é apagada ao logar com sucesso
--   ou ao concluir a recuperação de senha.
-- ORDEM DE DEPLOY: rodar ESTA migration ANTES de publicar o código do A4 —
--   o login passa a ler/gravar nesta tabela e, em produção
--   (ERRMODE_EXCEPTION), quebraria sem ela.
-- ============================================================

-- >>> UP
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(191) NOT NULL,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `last_attempt_at` datetime DEFAULT NULL,
  `locked_until` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_login_attempts_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- >>> ROLLBACK (executar manualmente se precisar reverter; publicar antes o
-- código sem o A4, senão o login quebra)
-- DROP TABLE `login_attempts`;
