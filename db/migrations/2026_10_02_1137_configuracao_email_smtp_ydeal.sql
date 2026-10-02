-- ============================================================
-- Migration: configuração de envio de e-mail (SMTP da Ydeal)
-- Data: 2026-10-02
-- Objetivo: C7 do PLANO-CORRECOES.md. Decisão do usuário (2026-10-02): o
--   sistema continua enviando e-mail pelo SMTP da Ydeal (dona do sistema).
--   A tabela `configuracao_email` não está em nenhum seed: num banco criado a
--   partir da estrutura do banco local ela fica vazia, e o envio (só a
--   recuperação de senha usa hoje, via MoreMailer) quebra ao ler a
--   configuração. Mesmo no banco local a linha existe, mas com e-mail e senha
--   vazios — o envio não funciona em nenhum ambiente até isto ser preenchido.
-- Tabelas afetadas: configuracao_email (1 linha).
-- Valores: servidor, porta, segurança e nome copiados do banco local
--   (`mail.ydeal.net.br`, 587, seguranca '0' = sem SSL direto; o PHPMailer
--   usa STARTTLS sozinho na 587). `nome` aparece como nome do remetente
--   ("Website"); trocar aqui se a Repasse Sandré quiser outro.
--
-- >>> ANTES DE RODAR — CREDENCIAL, NÃO VERSIONAR:
--   1. Copiar este arquivo para fora do repositório (ex.: /tmp).
--   2. Na cópia, trocar <<EMAIL_SMTP>> pela conta de envio da Ydeal e
--      <<SENHA_SMTP>> pela senha dela (a senha fica em texto no banco, que é
--      como o MoreMailer a lê).
--   3. Rodar a cópia e apagá-la. Nunca commitar este arquivo com a senha.
--   Se os marcadores não forem trocados, nada é gravado (as condições abaixo
--   recusam valores que comecem com "<<").
--
-- Idempotente: insere a linha só se a tabela estiver vazia; se já houver
--   linha, só preenche e-mail/senha que estejam VAZIOS (nunca sobrescreve
--   uma configuração já preenchida).
-- Conferir depois (não mostra a senha):
--   SELECT id, nome, email, smtp, porta, (senha2 <> '') AS tem_senha FROM configuracao_email;
-- ============================================================

-- >>> UP

INSERT INTO `configuracao_email` (`nome`, `email`, `senha2`, `smtp`, `porta`, `seguranca`, `destinatario`)
SELECT 'Website', '<<EMAIL_SMTP>>', '<<SENHA_SMTP>>', 'mail.ydeal.net.br', '587', '0', ''
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `configuracao_email`)
  AND '<<EMAIL_SMTP>>' NOT LIKE '<<%'
  AND '<<SENHA_SMTP>>' NOT LIKE '<<%';

UPDATE `configuracao_email`
SET `email` = '<<EMAIL_SMTP>>'
WHERE (`email` IS NULL OR `email` = '')
  AND '<<EMAIL_SMTP>>' NOT LIKE '<<%';

UPDATE `configuracao_email`
SET `senha2` = '<<SENHA_SMTP>>'
WHERE (`senha2` IS NULL OR `senha2` = '')
  AND '<<SENHA_SMTP>>' NOT LIKE '<<%';

-- >>> ROLLBACK (executar manualmente se precisar reverter)
-- Se a linha foi inserida por esta migration (tabela estava vazia):
--   DELETE FROM `configuracao_email` WHERE `smtp` = 'mail.ydeal.net.br';
-- Se só preencheu e-mail/senha de uma linha existente:
--   UPDATE `configuracao_email` SET `email` = '', `senha2` = NULL;
