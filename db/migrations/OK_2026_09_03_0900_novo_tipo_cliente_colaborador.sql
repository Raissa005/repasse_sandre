-- ============================================================
-- Migration: novo tipo de cliente "Colaborador"
-- Data: 2026-09-03
-- Objetivo: o usuário decidiu trazer de volta o tipo de cliente
--   "Colaborador" (funcionário da Sandré) — útil pra quando houver
--   funcionários cadastrados no sistema (ex.: aba "RH" de UsersController,
--   com admissão/salário/férias/% comissão, que hoje não aparece pra
--   ninguém porque esse tipo não existe). Diferente de "Vendedor / Antigo
--   Proprietário" (2026_09_02_1400): esse é sobre o próprio funcionário
--   da revenda, não sobre quem vende o carro usado pra Sandré.
--   Id fixo (4) escolhido deliberadamente — código PHP passa a buscar
--   pelo NOME via CustomerType::getIdByName('Colaborador'), não pelo id
--   fixo (mesma lição do bug de "Fornecedor" com id antigo/errado).
-- Tabelas afetadas: customer_type, menu, menu_access
-- ATENÇÃO: rode o SELECT de conferência antes do UP.
-- ============================================================

-- >>> Conferência antes de aplicar
SELECT id, name FROM customer_type;
SELECT id, name, route, id_menu_parent FROM menu WHERE id_menu_parent = 24;

-- >>> UP

INSERT INTO `customer_type` (`id`, `name`, `status`, `created_at`, `disableable`) VALUES
    (4, 'Colaborador', 1, NOW(), 1);

INSERT INTO `menu` (`id`, `name`, `item_order`, `route`, `icon`, `access`, `status`, `id_menu_parent`, `get`, `type_branch`) VALUES
    (202, 'Colaborador', 5, 'customer', 'fa fa-id-badge', 30, 1, 24, '?customer_type_in%5B%5D=4', NULL);

-- menu_access: mesmo conjunto de perfis já liberado nos itens irmãos
-- (Comprador/Fornecedor/Vendedor/Todos/# Cadastros sob o menu Cliente = 1, 2, 8).
INSERT INTO `menu_access` (`id_profile`, `status`, `id_menu`) VALUES
    (1, 1, 202), (2, 1, 202), (8, 1, 202);

-- >>> ROLLBACK (executar manualmente se precisar reverter)
-- DELETE FROM `menu_access` WHERE id_menu = 202;
-- DELETE FROM `menu` WHERE id = 202;
-- DELETE FROM `customer_type` WHERE id = 4;
