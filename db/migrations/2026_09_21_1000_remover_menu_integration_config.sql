-- ============================================================
-- Migration: remover itens de menu órfãos de "Integrações" (Facebook Lead Ads)
-- Data: 2026-09-21
-- Objetivo: os itens de menu id=84 e id=87 (ambos "Configurações",
--   rota `integration-config`, icon `fa fa-cogs`) têm `id_menu_parent = 83`,
--   mas não existe nenhuma linha `id=83` na tabela `menu` — são órfãos
--   estruturais (a árvore de menu em src/core/Controller.php::assembleMenu()
--   nunca alcança um item cujo pai não existe, então já não aparecem na
--   navegação hoje). Além disso, a tabela `integrations` que essa tela lista
--   não existe neste banco (confirmado: SELECT * FROM integrations dá
--   ERROR 1146). A pedido do usuário, o controller/model/rotas
--   correspondentes (IntegrationConfigController, Integrations model, rota
--   settings/integrations, toggle facebookLeadAds, webhook
--   leadRedirect/leadsFaceBook/{token}) estão sendo removidos do código como
--   apêndice do módulo Lead (que já está desativado, sem previsão de uso de
--   Facebook Lead Ads). Esta migration remove os dois registros de menu e os
--   respectivos registros de liberação por perfil, já que apontam para uma
--   tela que deixará de existir.
-- Tabelas afetadas: menu, menu_access
-- ============================================================

-- >>> UP

DELETE FROM `menu_access` WHERE `id` IN (123, 124, 186, 187);
DELETE FROM `menu` WHERE `id` IN (84, 87);

-- >>> ROLLBACK (executar manualmente se precisar reverter)
-- INSERT INTO `menu` (`id`,`name`,`item_order`,`route`,`icon`,`access`,`status`,`id_menu_parent`,`get`,`type_branch`) VALUES
--     (84,'Configurações',1,'integration-config','fa fa-cogs',30,1,83,NULL,NULL),
--     (87,'Configurações',1,'integration-config','fa fa-cogs',30,1,83,NULL,NULL);
-- INSERT INTO `menu_access` (`id`,`id_profile`,`status`,`updated_by`,`id_menu`) VALUES
--     (123,1,1,'2025-07-02 08:49:08',84),
--     (124,1,1,'2025-07-02 08:49:08',87),
--     (186,2,1,'2025-07-02 08:52:12',84),
--     (187,2,1,'2025-07-02 08:52:12',87);
