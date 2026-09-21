-- ============================================================
-- Migration: recriar as tabelas de atendimento (attendance_*)
-- Data: 2026-09-02
-- Objetivo: o banco local `veiculos_repasse` nunca teve essas 8 tabelas
--   criadas (confirmado via SHOW TABLES — só existem no dump
--   db/realize_repasse.sql, que está à frente do banco real nesse ponto).
--   Isso causa os erros "Base table or view not found: 1146" em
--   src/model/AttendanceStatus.php ao acessar repasse_sandre/attendance.
--   Definição copiada literalmente do dump db/realize_repasse.sql
--   (linhas 58-297), incluindo os dados de seed de `attendance_status`
--   (situações do funil: Aberto, Em Andamento, Negociando, Aguardando
--   Terceiros, Fechado, Concluído, Cancelado) e `attendance_filter_type`,
--   sem os quais a tela também não funcionaria mesmo com as tabelas
--   criadas vazias.
-- Tabelas afetadas (novas): attendance, attendance_attachments,
--   attendance_filter_type, attendance_filters_interests,
--   attendance_phones, attendance_presentation_config, attendance_status,
--   attendance_timeline
-- ATENÇÃO: rode um SELECT/SHOW TABLES antes para confirmar que essas
--   tabelas realmente não existem no seu banco (CREATE TABLE IF NOT
--   EXISTS não sobrescreve nem apaga dado se, por algum motivo, alguma
--   já existir com outro schema).
-- ============================================================

-- >>> UP

CREATE TABLE IF NOT EXISTS `attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `id_branch` int(11) DEFAULT NULL,
  `opening_date` datetime DEFAULT NULL,
  `return_date` datetime DEFAULT NULL,
  `id_communication_channel` int(11) DEFAULT NULL,
  `id_status` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` binary(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `id_city` int(11) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `classification` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `attendance_id_branch_IDX` (`id_branch`) USING BTREE,
  KEY `attendance_id_communication_channel_IDX` (`id_communication_channel`) USING BTREE,
  KEY `attendance_id_status_IDX` (`id_status`) USING BTREE,
  KEY `attendance_created_by_IDX` (`created_by`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE IF NOT EXISTS `attendance_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `extension` varchar(10) DEFAULT NULL,
  `status` binary(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `id_attendance` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendance_attachments_id_attendance_IDX` (`id_attendance`) USING BTREE,
  KEY `attendance_attachments_created_by_IDX` (`created_by`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE IF NOT EXISTS `attendance_filter_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `status` binary(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `attendance_filters_interests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_attendance` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `id_attendance_filter_type` int(11) DEFAULT NULL,
  `json` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE IF NOT EXISTS `attendance_phones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_attendance` int(11) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `whatsapp` binary(1) DEFAULT '0',
  `status` binary(1) DEFAULT '1',
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE IF NOT EXISTS `attendance_presentation_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_attendance_presentation` int(11) NOT NULL,
  `id_presentation_config` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `attendance_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `status` binary(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `icon_status` varchar(250) DEFAULT NULL,
  `box_color` varchar(20) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `standard_filter` binary(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE IF NOT EXISTS `attendance_timeline` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_attendance` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `status_icon` int(11) DEFAULT 1,
  `status` binary(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `status_timeline` int(11) DEFAULT NULL,
  `url_attachment` varchar(255) DEFAULT NULL,
  `name_attachment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendance_timeline_id_attendance_IDX` (`id_attendance`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Seed: situações do funil de atendimento (necessário para a tela funcionar,
-- não só a tabela existir). Mesmos IDs/valores do dump original.
-- INSERT IGNORE: idempotente porque `id` é PRIMARY KEY (não duplica se a
-- migration for rodada mais de uma vez ou se algum ID já existir).
INSERT IGNORE INTO `attendance_status` (`id`, `name`, `status`, `created_at`, `updated_at`, `created_by`, `updated_by`, `icon_status`, `box_color`, `order`, `standard_filter`) VALUES
    (7,'Aberto',_binary '1',NULL,NULL,NULL,NULL,'far fa-comment-dots','#11a0de',1,_binary '1'),
    (8,'Negociando',_binary '1',NULL,NULL,NULL,NULL,'fas fa-comments-dollar','#ff0422',4,_binary '1'),
    (9,'Fechado',_binary '1',NULL,NULL,NULL,NULL,'far fa-handshake','#0b1194',5,_binary '1'),
    (10,'Concluído',_binary '1',NULL,NULL,NULL,NULL,'fas fa-check-double','#15940a',6,_binary '0'),
    (11,'Cancelado',_binary '1',NULL,NULL,NULL,NULL,'far fa-times-circle','#5c5c5c',7,_binary '0'),
    (12,'Em Andamento',_binary '1',NULL,NULL,NULL,NULL,'fas fa-briefcase','#f5ed37',2,_binary '1'),
    (13,'Aguardando Terceiros',_binary '1',NULL,NULL,NULL,NULL,'fas fa-pause','#fa7210',3,_binary '1');

-- Seed: tipos de filtro de interesse (herdado do domínio imobiliário —
-- hoje não é usado no fluxo de veículo, mas está no dump original e a
-- tabela é referenciada pelo código; incluído para manter fidelidade ao
-- schema original, sem necessidade de uso imediato).
INSERT IGNORE INTO `attendance_filter_type` (`id`, `name`, `status`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
    (1,'Característica',_binary '1',NULL,NULL,NULL,NULL),
    (2,'Categoria',_binary '1',NULL,NULL,NULL,NULL),
    (3,'Localização',_binary '1',NULL,NULL,NULL,NULL),
    (4,'Preço',_binary '1',NULL,NULL,NULL,NULL),
    (5,'Tipo Imóvel',_binary '1',NULL,NULL,NULL,NULL);

-- >>> ROLLBACK (executar manualmente se precisar reverter)
-- DROP TABLE IF EXISTS `attendance_timeline`;
-- DROP TABLE IF EXISTS `attendance_status`;
-- DROP TABLE IF EXISTS `attendance_presentation_config`;
-- DROP TABLE IF EXISTS `attendance_phones`;
-- DROP TABLE IF EXISTS `attendance_filters_interests`;
-- DROP TABLE IF EXISTS `attendance_filter_type`;
-- DROP TABLE IF EXISTS `attendance_attachments`;
-- DROP TABLE IF EXISTS `attendance`;
