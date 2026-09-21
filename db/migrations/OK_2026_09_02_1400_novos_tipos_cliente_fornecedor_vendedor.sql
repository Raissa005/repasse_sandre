-- ============================================================
-- Migration: novos tipos de cliente "Fornecedor" e "Vendedor / Antigo Proprietário"
-- Data: 2026-09-02
-- Objetivo: o menu "Cliente" só tinha "Comprador" e "Todos" porque
--   `customer_type` só tinha 1 linha neste banco. O usuário pediu 2 tipos
--   novos pro domínio de veículo: "Fornecedor" (peças/serviços) e
--   "Vendedor / Antigo Proprietário" (pessoa que vende o carro usado pro
--   Sandré — já existe `vehicle_purchases.id_former_owner` referenciando
--   `customer`, mas sem tipo próprio até agora).
--   Ids fixos (2 e 3) escolhidos deliberadamente — o código PHP
--   (CustomerController.php) passa a checar esses ids explicitamente
--   (troca do antigo id=10 "Fornecedor" do esquema imobiliário, que não
--   existe mais neste banco, pelo novo id=2).
-- Tabelas afetadas: customer_type, menu, menu_access
-- ATENÇÃO: rode o SELECT de conferência antes do UP.
-- ============================================================

-- >>> Conferência antes de aplicar
SELECT id, name FROM customer_type;
SELECT id, name, route, id_menu_parent FROM menu WHERE id_menu_parent = 24;

-- >>> UP

INSERT INTO `customer_type` (`id`, `name`, `status`, `created_at`, `disableable`) VALUES
    (2, 'Fornecedor', 1, NOW(), 1),
    (3, 'Vendedor / Antigo Proprietário', 1, NOW(), 1);

INSERT INTO `menu` (`id`, `name`, `item_order`, `route`, `icon`, `access`, `status`, `id_menu_parent`, `get`, `type_branch`) VALUES
    (200, 'Fornecedor', 3, 'customer', 'fa fa-dolly', 30, 1, 24, '?customer_type_in%5B%5D=2', NULL),
    (201, 'Vendedor', 4, 'customer', 'fa fa-handshake', 30, 1, 24, '?customer_type_in%5B%5D=3', NULL);

-- menu_access: mesmo conjunto de perfis já liberado nos itens irmãos
-- (Comprador/Todos/# Cadastros sob o menu Cliente = 1, 2, 8).
INSERT INTO `menu_access` (`id_profile`, `status`, `id_menu`) VALUES
    (1, 1, 200), (2, 1, 200), (8, 1, 200),
    (1, 1, 201), (2, 1, 201), (8, 1, 201);

-- >>> ROLLBACK (executar manualmente se precisar reverter)
-- DELETE FROM `menu_access` WHERE id_menu IN (200, 201);
-- DELETE FROM `menu` WHERE id IN (200, 201);
-- DELETE FROM `customer_type` WHERE id IN (2, 3);
