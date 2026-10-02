-- ============================================================
-- Migration: esconder o item de menu "Moedas" (rota `currencies`)
-- Data: 2026-10-02
-- Objetivo: M12 do PLANO-CORRECOES.md (decisão do usuário 2026-10-01): a tela
--   de Moedas depende da tabela `currencies`, que não existe; ela sai do menu
--   e a rota passa a redirecionar para a home (CurrenciesController). O dump
--   antigo db/realize_repasse.sql tem o item id 74 "Moedas" (pai 77, só
--   Superadm); no banco local e nos seeds de produção ele não existe — aí o
--   UPDATE abaixo não altera nenhuma linha. Em produção, conferir antes:
--     SELECT id, name, route, status FROM menu WHERE route = 'currencies';
--   Nada é apagado (a remoção completa é limpeza opcional pós-lançamento).
--   O menu fica em cache por sessão: o item some no próximo login.
-- Tabelas afetadas: menu
-- ============================================================

-- >>> UP

UPDATE `menu` SET `status` = 0 WHERE `route` = 'currencies';

-- >>> ROLLBACK (executar manualmente se precisar reverter)
-- UPDATE `menu` SET `status` = 1 WHERE `route` = 'currencies';
