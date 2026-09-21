-- ============================================================
-- Migration: reformular Atendimento (Interesses + Apresentados) para veículo
-- Data: 2026-09-02
-- Objetivo: as abas "Interesses" e "Imóveis Apresentados" do atendimento
--   eram herdadas do domínio imobiliário (filtros por Característica,
--   Categoria, Localização, Tipo Imóvel; vínculo com `products`/
--   `displayed_properties`), que não existe mais neste sistema — ver
--   investigação registrada em memória do projeto. A pedido do usuário,
--   viram:
--     - "Interesses": 3 critérios de veículo (Marca e Modelo, Faixa de
--       Preço, Ano/Km), substituindo os 5 critérios de imóvel.
--     - "Imóveis Apresentados" -> "Veículos Apresentados": vincula o
--       atendimento a veículos do cadastro local (tabela `vehicles`,
--       a mesma usada em Vendas), em vez de `products`.
--   Código correspondente (AttendanceController, Attendance model,
--   views, JS) só deve ser atualizado depois que esta migration for
--   aplicada.
-- Tabelas afetadas: attendance_filter_type (reseed), attendance_displayed_vehicles (nova)
-- ATENÇÃO: attendance_filters_interests está vazia neste banco (confirmado
--   antes de escrever esta migration), então não há registro órfão ao
--   trocar os tipos de filtro.
-- ============================================================

-- >>> UP

DELETE FROM `attendance_filter_type` WHERE `id` IN (1,2,3,4,5);

INSERT INTO `attendance_filter_type` (`id`, `name`, `status`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
    (1, 'Marca e Modelo', _binary '1', NULL, NULL, NULL, NULL),
    (2, 'Faixa de Preço', _binary '1', NULL, NULL, NULL, NULL),
    (3, 'Ano/Km', _binary '1', NULL, NULL, NULL, NULL);

CREATE TABLE IF NOT EXISTS `attendance_displayed_vehicles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_attendance` int(11) DEFAULT NULL,
  `id_vehicle` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `status` binary(1) DEFAULT '1',
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendance_displayed_vehicles_id_attendance_IDX` (`id_attendance`) USING BTREE,
  KEY `attendance_displayed_vehicles_id_vehicle_IDX` (`id_vehicle`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- >>> ROLLBACK (executar manualmente se precisar reverter)
-- DROP TABLE IF EXISTS `attendance_displayed_vehicles`;
-- DELETE FROM `attendance_filter_type` WHERE `id` IN (1,2,3);
-- INSERT INTO `attendance_filter_type` VALUES (1,'Característica',_binary '1',NULL,NULL,NULL,NULL),(2,'Categoria',_binary '1',NULL,NULL,NULL,NULL),(3,'Localização',_binary '1',NULL,NULL,NULL,NULL),(4,'Preço',_binary '1',NULL,NULL,NULL,NULL),(5,'Tipo Imóvel',_binary '1',NULL,NULL,NULL,NULL);
