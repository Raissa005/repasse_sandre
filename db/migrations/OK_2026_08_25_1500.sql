-- ============================================================
-- Migration: remover itens de menu de rotas de imóvel/construtora removidas
-- Data: 2026-08-25
-- Objetivo: os controllers/rotas abaixo foram apagados do código-fonte
--   (imóvel, construtora, contrato padrão, venda de imóvel, apresentação
--   pública de imóvel, cartão de apresentação, integração DWV, catálogo
--   de status de venda de imóvel). As linhas de `menu` que apontam para
--   essas rotas ficariam como links quebrados na barra lateral (redirecionam
--   para a página de erro). Esta migration remove essas linhas e as
--   permissões associadas em `menu_access`.
-- Tabelas afetadas: menu, menu_access
-- ATENÇÃO: rode `SELECT` antes de `DELETE` para conferir contra o banco
--   real (o dump db/realize_repasse.sql pode estar desatualizado, ver
--   docs/07-banco-de-dados.md). Depois de rodar, verifique manualmente
--   se algum item "pai" (route = '#') ficou órfão sem nenhum filho — se
--   ficou, pode ser removido também, mas não está incluído aqui.
-- ============================================================

-- >>> Conferência antes de apagar (rodar primeiro, revisar o resultado)
SELECT id, name, route, id_menu_parent
FROM menu
WHERE route IN (
    'property', 'property-category', 'property-classification', 'property-type',
    'construction', 'immovable-resource', 'change-link', 'integration-dwv',
    'standard-contract', 'sales', 'report-sales', 'presentations',
    'presentation-card', 'status'
);

-- >>> UP
DELETE FROM menu_access
WHERE id_menu IN (
    SELECT id FROM menu WHERE route IN (
        'property', 'property-category', 'property-classification', 'property-type',
        'construction', 'immovable-resource', 'change-link', 'integration-dwv',
        'standard-contract', 'sales', 'report-sales', 'presentations',
        'presentation-card', 'status'
    )
);

DELETE FROM menu
WHERE route IN (
    'property', 'property-category', 'property-classification', 'property-type',
    'construction', 'immovable-resource', 'change-link', 'integration-dwv',
    'standard-contract', 'sales', 'report-sales', 'presentations',
    'presentation-card', 'status'
);

-- >>> ROLLBACK
-- Não há rollback automático (os dados originais das linhas de `menu`
-- seriam perdidos). Restaurar a partir de um backup do banco antes de
-- rodar esta migration, caso precise reverter.
