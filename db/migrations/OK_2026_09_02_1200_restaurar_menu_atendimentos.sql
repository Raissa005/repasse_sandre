-- ============================================================
-- Migration: restaurar o item de menu "Atendimentos" (ramo inteiro)
-- Data: 2026-09-02
-- Objetivo: o usuário reportou que "Atendimentos" não aparece no menu
--   lateral. Investigando a tabela `menu`, o ramo inteiro do menu
--   "Atendimentos" está faltando neste banco (não é resquício da
--   remoção do domínio imobiliário — nenhuma dessas rotas estava na
--   lista da migration 2026_08_25_1500):
--     id=29 "Atendimentos" (raiz do ramo, route=#)                -> AUSENTE
--     id=30 "Calendário" (route=calendar, filho de 29)             -> AUSENTE
--     id=32 "Atendimentos" (route=attendance, filho de 29)         -> AUSENTE
--                          ^ esta é a tela principal (AttendanceController)
--     id=75 "# Cadastros" (sub-pasta, filho de 29)                 -> AUSENTE
--     id=33 "Status Atendimento" (route=attendance-status, filho 75) -> AUSENTE
--     id=81 "# Relatórios" (sub-pasta, filho de 29)                -> AUSENTE
--   Isso deixa órfãos dois itens que ainda existem no banco:
--     id=34 "Canais Comunicação" (id_menu_parent=75, que não existe)
--     id=66 "Atendimentos"/report-attendance (id_menu_parent=81, que não existe)
--   Como o pai (29) e os dois sub-pais (75, 81) não existem, a árvore
--   de menu não tem onde pendurar nenhum desses itens e o ramo inteiro
--   some da sidebar — não só o item que o usuário notou.
-- Tabelas afetadas: menu, menu_access
-- Fonte dos valores: db/realize_repasse.sql (dump original), linha 2089
--   (INSERT INTO `menu`). O dump NÃO tem dados de `menu_access` (tabela
--   populada manualmente fora do dump); os profiles liberados abaixo
--   (Superadm=1, Administrador=2) espelham o padrão já usado nos itens
--   irmãos 34 e 66, que já têm acesso liberado só para esses dois perfis.
-- ATENÇÃO: rode o SELECT de conferência abaixo antes do UP, e ajuste os
--   perfis do INSERT em menu_access se outros perfis (ex: Vendedor)
--   também deverem ver "Atendimentos".
-- ============================================================

-- >>> Conferência antes de aplicar (rodar primeiro, revisar o resultado)
SELECT id, name, route, id_menu_parent, status FROM menu WHERE id IN (29,30,32,33,34,66,75,81);

-- >>> UP

INSERT IGNORE INTO `menu` (`id`, `name`, `item_order`, `route`, `icon`, `access`, `status`, `id_menu_parent`, `get`, `type_branch`) VALUES
    (29, 'Atendimentos', 2, '#', 'fa fa-comments', 30, 1, NULL, NULL, NULL),
    (75, '# Cadastros', 1, '#', 'fa fa-pencil-alt', 30, 1, 29, NULL, NULL),
    (81, '# Relatórios', 2, '#', 'fa fa-clipboard', 30, 1, 29, NULL, NULL),
    (30, 'Calendário', 4, 'calendar', 'fa fa-calendar-alt', 30, 1, 29, NULL, NULL),
    (32, 'Atendimentos', 3, 'attendance', 'fa fa-comments', 30, 1, 29, NULL, NULL),
    (33, 'Status Atendimento', 2, 'attendance-status', 'fa fa-headphones', 10, 1, 75, NULL, NULL);

-- menu_access: libera Superadm (1) e Administrador (2) para os itens
-- recém-criados, igual ao que já está liberado nos itens irmãos 34 e 66.
INSERT INTO `menu_access` (`id_profile`, `status`, `id_menu`)
SELECT profile, 1, menu_id FROM (
    SELECT 1 AS profile, 29 AS menu_id UNION ALL SELECT 2, 29 UNION ALL
    SELECT 1, 75 UNION ALL SELECT 2, 75 UNION ALL
    SELECT 1, 81 UNION ALL SELECT 2, 81 UNION ALL
    SELECT 1, 30 UNION ALL SELECT 2, 30 UNION ALL
    SELECT 1, 32 UNION ALL SELECT 2, 32 UNION ALL
    SELECT 1, 33 UNION ALL SELECT 2, 33
) AS wanted
WHERE NOT EXISTS (
    SELECT 1 FROM `menu_access` ma
    WHERE ma.id_menu = wanted.menu_id AND ma.id_profile = wanted.profile
);

-- >>> ROLLBACK (executar manualmente se precisar reverter)
-- DELETE FROM `menu_access` WHERE id_menu IN (29,30,32,33,75,81);
-- DELETE FROM `menu` WHERE id IN (29,30,32,33,75,81);
