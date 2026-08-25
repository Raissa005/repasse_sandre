-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: realize_repasse
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `arrangement_payment_charges_invoice_receive_installment`
--

DROP TABLE IF EXISTS `arrangement_payment_charges_invoice_receive_installment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `arrangement_payment_charges_invoice_receive_installment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_bill_receive_installment` int(11) NOT NULL,
  `id_user_position` int(11) NOT NULL,
  `id_customer` int(11) DEFAULT NULL,
  `percentage_commission` decimal(10,2) DEFAULT 0.00,
  `origin_commission` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `id_cost_center` int(11) DEFAULT NULL,
  `id_form_payment` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arrangement_payment_charges_invoice_receive_installment`
--

LOCK TABLES `arrangement_payment_charges_invoice_receive_installment` WRITE;
/*!40000 ALTER TABLE `arrangement_payment_charges_invoice_receive_installment` DISABLE KEYS */;
/*!40000 ALTER TABLE `arrangement_payment_charges_invoice_receive_installment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendance` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance`
--

LOCK TABLES `attendance` WRITE;
/*!40000 ALTER TABLE `attendance` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance_attachments`
--

DROP TABLE IF EXISTS `attendance_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendance_attachments` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_attachments`
--

LOCK TABLES `attendance_attachments` WRITE;
/*!40000 ALTER TABLE `attendance_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance_filter_type`
--

DROP TABLE IF EXISTS `attendance_filter_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendance_filter_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `status` binary(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_filter_type`
--

LOCK TABLES `attendance_filter_type` WRITE;
/*!40000 ALTER TABLE `attendance_filter_type` DISABLE KEYS */;
INSERT INTO `attendance_filter_type` VALUES (1,'Característica',_binary '1',NULL,NULL,NULL,NULL),(2,'Categoria',_binary '1',NULL,NULL,NULL,NULL),(3,'Localização',_binary '1',NULL,NULL,NULL,NULL),(4,'Preço',_binary '1',NULL,NULL,NULL,NULL),(5,'Tipo Imóvel',_binary '1',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `attendance_filter_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance_filters_interests`
--

DROP TABLE IF EXISTS `attendance_filters_interests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendance_filters_interests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_attendance` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `id_attendance_filter_type` int(11) DEFAULT NULL,
  `json` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_filters_interests`
--

LOCK TABLES `attendance_filters_interests` WRITE;
/*!40000 ALTER TABLE `attendance_filters_interests` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance_filters_interests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance_phones`
--

DROP TABLE IF EXISTS `attendance_phones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendance_phones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_attendance` int(11) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `whatsapp` binary(1) DEFAULT '0',
  `status` binary(1) DEFAULT '1',
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_phones`
--

LOCK TABLES `attendance_phones` WRITE;
/*!40000 ALTER TABLE `attendance_phones` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance_phones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance_presentation_config`
--

DROP TABLE IF EXISTS `attendance_presentation_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendance_presentation_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_attendance_presentation` int(11) NOT NULL,
  `id_presentation_config` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_presentation_config`
--

LOCK TABLES `attendance_presentation_config` WRITE;
/*!40000 ALTER TABLE `attendance_presentation_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance_presentation_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance_status`
--

DROP TABLE IF EXISTS `attendance_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendance_status` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_status`
--

LOCK TABLES `attendance_status` WRITE;
/*!40000 ALTER TABLE `attendance_status` DISABLE KEYS */;
INSERT INTO `attendance_status` VALUES (7,'Aberto',_binary '1',NULL,NULL,NULL,NULL,'far fa-comment-dots','#11a0de',1,_binary '1'),(8,'Negociando',_binary '1',NULL,NULL,NULL,NULL,'fas fa-comments-dollar','#ff0422',4,_binary '1'),(9,'Fechado',_binary '1',NULL,NULL,NULL,NULL,'far fa-handshake','#0b1194',5,_binary '1'),(10,'Concluído',_binary '1',NULL,NULL,NULL,NULL,'fas fa-check-double','#15940a',6,_binary '0'),(11,'Cancelado',_binary '1',NULL,NULL,NULL,NULL,'far fa-times-circle','#5c5c5c',7,_binary '0'),(12,'Em Andamento',_binary '1',NULL,NULL,NULL,NULL,'fas fa-briefcase','#f5ed37',2,_binary '1'),(13,'Aguardando Terceiros',_binary '1',NULL,NULL,NULL,NULL,'fas fa-pause','#fa7210',3,_binary '1');
/*!40000 ALTER TABLE `attendance_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance_timeline`
--

DROP TABLE IF EXISTS `attendance_timeline`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendance_timeline` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_timeline`
--

LOCK TABLES `attendance_timeline` WRITE;
/*!40000 ALTER TABLE `attendance_timeline` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance_timeline` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bank_accounts`
--

DROP TABLE IF EXISTS `bank_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bank_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_bank` int(11) NOT NULL,
  `agency` varchar(255) NOT NULL,
  `account_number` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bank_accounts`
--

LOCK TABLES `bank_accounts` WRITE;
/*!40000 ALTER TABLE `bank_accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `bank_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `banks`
--

DROP TABLE IF EXISTS `banks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `banks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `bank_code` varchar(20) NOT NULL,
  `finance_bank` tinyint(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=256 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banks`
--

LOCK TABLES `banks` WRITE;
/*!40000 ALTER TABLE `banks` DISABLE KEYS */;
INSERT INTO `banks` VALUES (3,'BANCO DO BRASIL S/A',1,'1',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(4,'BANCO CENTRAL DO BRASIL',1,'2',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(5,'BANCO DA AMAZONIA S.A',1,'3',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(6,'BANCO DO NORDESTE DO BRASIL S.A',1,'4',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(7,'BANCO NAC DESENV. ECO. SOCIAL S.A',1,'7',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(8,'BANCO MERIDIONAL DO BRASIL',1,'8',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(9,'BANCO DO ESTADO DE ALAGOAS S.A',1,'20',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(10,'BANCO DO ESTADO DO ESPIRITO SANTO S.A',1,'21',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(11,'BANCO DE CREDITO REAL DE MINAS GERAIS SA',1,'22',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(12,'BANCO DO ESTADO DE PERNAMBUCO',1,'24',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(13,'BANCO ALFA S/A',1,'25',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(14,'BANCO DO ESTADO DO ACRE S.A',1,'26',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(15,'BANCO DO ESTADO DE SANTA CATARINA S.A',1,'27',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(16,'BANCO DO ESTADO DA BAHIA S.A',1,'28',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(17,'BANCO DO ESTADO DO RIO DE JANEIRO S.A',1,'29',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(18,'BANCO DO ESTADO DA PARAIBA S.A',1,'30',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(19,'BANCO DO ESTADO DE GOIAS S.A',1,'31',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(20,'BANCO DO ESTADO DO MATO GROSSO S.A.',1,'32',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(21,'BANCO DO ESTADO DE SAO PAULO S.A',1,'33',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(22,'BANCO DO ESADO DO AMAZONAS S.A',1,'34',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(23,'BANCO DO ESTADO DO CEARA S.A',1,'35',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(24,'BANCO DO ESTADO DO MARANHAO S.A',1,'36',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(25,'BANCO DO ESTADO DO PARA S.A',1,'37',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(26,'BANCO DO ESTADO DO PARANA S.A',1,'38',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(27,'BANCO DO ESTADO DO PIAUI S.A',1,'39',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(28,'BANCO DO ESTADO DO RIO GRANDE DO SUL S.A',1,'41',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(29,'BANCO DO ESTADO DE SERGIPE S.A',1,'47',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(30,'BANCO DO ESTADO DE MINAS GERAIS S.A',1,'48',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(31,'BANCO DO ESTADO DE RONDONIA S.A',1,'59',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(32,'BANCO MORGAN STANLEY S.A',1,'66',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(33,'BANCO DE BRASILIA S.A',1,'70',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(34,'BANCO DE INTER S.A',1,'77',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(35,'CAIXA ECONOMICA FEDERAL',1,'104',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(36,'BANCO ITABANCO S.A.',1,'106',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(37,'BANCO BBM S.A',1,'107',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(38,'BANCO CREDIBANCO S.A',1,'109',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(39,'BANCO B.N.L DO BRASIL S.A',1,'116',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(40,'UBS BRASIL BANCO DE INVESTIMENTO S.A.',1,'129',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(41,'MULTI BANCO S.A',1,'148',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(42,'CAIXA ECONOMICA DO ESTADO DE SAO PAULO',1,'151',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(43,'CAIXA ECONOMICA DO ESTADO DO R.G.SUL',1,'153',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(44,'BANCO NORCHEM S.A',1,'165',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(45,'BANCO INTER-ATLANTICO S.A',1,'166',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(46,'BANCO C.C.F. BRASIL S.A',1,'168',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(47,'CONTINENTAL BANCO S.A',1,'175',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(48,'BBA - CREDITANSTALT S.A',1,'184',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(49,'BANCO FINANCIAL PORTUGUES',1,'199',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(50,'BANCO FRICRISA AXELRUD S.A',1,'200',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(51,'BANCO AUGUSTA INDUSTRIA E COMERCIAL S.A',1,'201',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(52,'BANCO S.R.L S.A',1,'204',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(53,'BANCO SUL AMERICA S.A',1,'205',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(54,'BANCO MARTINELLI S.A',1,'206',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(55,'BANCO PACTUAL S.A',1,'208',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(56,'DEUTSCH SUDAMERIKANICHE BANK AG',1,'210',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(57,'BANCO SISTEMA S.A',1,'211',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(58,'BANCO ORIGINAL S.A',1,'212',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(59,'BANCO ARBI S.A',1,'213',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(60,'BANCO DIBENS S.A',1,'214',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(61,'BANCO AMERICA DO SUL S.A',1,'215',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(62,'BANCO REGIONAL MALCON S.A',1,'216',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(63,'BANCO AGROINVEST S.A',1,'217',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(64,'BBS - BANCO BONSUCESSO S.A.',1,'218',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(65,'BANCO DE CREDITO DE SAO PAULO S.A',1,'219',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(66,'BANCO CREFISUL',1,'220',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(67,'BANCO GRAPHUS S.A',1,'221',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(68,'BANCO AGF BRASIL S. A.',1,'222',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(69,'BANCO INTERUNION S.A',1,'223',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(70,'BANCO FIBRA S.A',1,'224',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(71,'BANCO BRASCAN S.A',1,'225',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(72,'BANCO ICATU S.A',1,'228',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(73,'BANCO CRUZEIRO S.A',1,'229',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(74,'BANCO BANDEIRANTES S.A',1,'230',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(75,'BANCO BOAVISTA S.A',1,'231',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(76,'BANCO INTERPART S.A',1,'232',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(77,'BANCO MAPPIN S.A',1,'233',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(78,'BANCO LAVRA S.A.',1,'234',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(79,'BANCO LIBERAL S.A',1,'235',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(80,'BANCO CAMBIAL S.A',1,'236',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(81,'BANCO BRADESCO S.A',1,'237',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(82,'BANCO BANCRED S.A',1,'239',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(83,'BANCO DE CREDITO REAL DE MINAS GERAIS S.',1,'240',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(84,'BANCO CLASSICO S.A',1,'241',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(85,'BANCO EUROINVEST S.A',1,'242',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(86,'BANCO STOCK S.A',1,'243',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(87,'BANCO CIDADE S.A',1,'244',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(88,'BANCO EMPRESARIAL S.A',1,'245',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(89,'BANCO ABC ROMA S.A',1,'246',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(90,'BANCO OMEGA S.A',1,'247',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(91,'BANCO INVESTCRED S.A',1,'249',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(92,'BANCO SCHAHIN CURY S.A',1,'250',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(93,'BANCO SAO JORGE S.A.',1,'251',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(94,'BANCO FININVEST S.A',1,'252',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(95,'BANCO PARANA BANCO S.A',1,'254',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(96,'MILBANCO S.A.',1,'255',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(97,'BANCO GULVINVEST S.A',1,'256',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(98,'BANCO INDUSCRED S.A',1,'258',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(99,'NU PAGAMENTOS S.A',1,'260',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(100,'BANCO VARIG S.A',1,'261',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(101,'BANCO BOREAL S.A',1,'262',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(102,'BANCO CACIQUE',1,'263',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(103,'BANCO PERFORMANCE S.A',1,'264',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(104,'BANCO FATOR S.A',1,'265',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(105,'BANCO CEDULA S.A',1,'266',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(106,'BANCO BBM-COM.C.IMOB.CFI S.A.',1,'267',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(107,'BANCO REAL S.A',1,'275',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(108,'BANCO PLANIBANC S.A',1,'277',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(109,'BANCO BRASILEIRO COMERCIAL',1,'282',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(110,'BANCO DE CREDITO NACIONAL S.A',1,'291',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(111,'BCR - BANCO DE CREDITO REAL S.A',1,'294',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(112,'BANCO CREDIPLAN S.A',1,'295',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(113,'BANCO DE LA NACION ARGENTINA S.A',1,'300',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(114,'BANCO DO PROGRESSO S.A',1,'302',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(115,'BANCO HNF S.A.',1,'303',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(116,'BANCO PONTUAL S.A',1,'304',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(117,'BANCO COMERCIAL BANCESA S.A.',1,'308',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(118,'BANCO B.M.G. S.A',1,'318',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(119,'BANCO INDUSTRIAL E COMERCIAL',1,'320',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(120,'BANCO ITAU S.A',1,'341',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(121,'BANCO FRANCES E BRASILEIRO S.A',1,'346',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(122,'BANCO SUDAMERIS BRASIL S.A',1,'347',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(123,'BANCO BOZANO SIMONSEN S.A',1,'351',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(124,'BANCO GERAL DO COMERCIO S.A',1,'353',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(125,'ABN AMRO S.A',1,'356',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(126,'BANCO SOGERAL S.A',1,'366',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(127,'PONTUAL',1,'369',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(128,'BEAL - BANCO EUROPEU PARA AMERICA LATINA',1,'370',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(129,'BANCO ITAMARATI S.A',1,'372',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(130,'BANCO FENICIA S.A',1,'375',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(131,'CHASE MANHATTAN BANK S.A',1,'376',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(132,'BANCO MERCANTIL DE DESCONTOS S/A',1,'388',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(133,'BANCO MERCANTIL DO BRASIL S.A',1,'389',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(134,'BANCO MERCANTIL DE SAO PAULO S.A',1,'392',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(135,'BANCO B.M.C. S.A',1,'394',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(136,'HSBC BANK BRASIL S.A. – BANCO MÚLTIPLO',1,'399',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(137,'UNIBANCO - UNIAO DOS BANCOS BRASILEIROS',1,'409',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(138,'BANCO NACIONAL DA BAHIA S.A',1,'412',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(139,'BANCO NACIONAL S.A',1,'415',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(140,'BANCO NACIONAL DO NORTE S.A',1,'420',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(141,'BANCO SAFRA S.A',1,'422',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(142,'BANCO NOROESTE S.A',1,'424',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(143,'BANCO FORTALEZA S.A',1,'434',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(144,'BANCO RURAL S.A',1,'453',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(145,'BANCO TOKIO S.A',1,'456',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(146,'BANCO SUMITOMO BRASILEIRO S.A',1,'464',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(147,'BANCO MITSUBISHI BRASILEIRO S.A',1,'466',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(148,'LLOYDS BANK PLC',1,'472',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(149,'BANCO FINANCIAL PORTUGUES S.A',1,'473',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(150,'CITIBANK N.A',1,'477',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(151,'BANCO DE BOSTON S.A',1,'479',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(152,'BANCO PORTUGUES DO ATLANTICO-BRASIL S.A',1,'480',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(153,'BANCO AGRIMISA S.A.',1,'483',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(154,'DEUTSCHE BANK S.A - BANCO ALEMAO',1,'487',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(155,'BANCO J. P. MORGAN S.A',1,'488',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(156,'BANESTO BANCO URUGAUAY S.A',1,'489',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(157,'INTERNATIONALE NEDERLANDEN BANK N.V.',1,'492',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(158,'BANCO UNION S.A.C.A',1,'493',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(159,'BANCO LA REP. ORIENTAL DEL URUGUAY',1,'494',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(160,'BANCO LA PROVINCIA DE BUENOS AIRES',1,'495',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(161,'BANCO EXTERIOR DE ESPANA S.A',1,'496',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(162,'CENTRO HISPANO BANCO',1,'498',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(163,'BANCO IOCHPE S.A',1,'499',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(164,'BANCO BRASILEIRO IRAQUIANO S.A.',1,'501',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(165,'BANCO SANTANDER S.A',1,'502',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(166,'BANCO MULTIPLIC S.A',1,'504',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(167,'BANCO GARANTIA S.A',1,'505',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(168,'BANCO LUSO BRASILEIRO S.A',1,'600',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(169,'BFC BANCO S.A.',1,'601',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(170,'BANCO PATENTE S.A',1,'602',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(171,'BANCO INDUSTRIAL DO BRASIL S.A',1,'604',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(172,'BANCO SANTOS NEVES S.A',1,'607',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(173,'BANCO OPEN S.A',1,'608',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(174,'BANCO V.R. S.A',1,'610',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(175,'BANCO PAULISTA S.A',1,'611',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(176,'BANCO GUANABARA S.A',1,'612',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(177,'BANCO PECUNIA S.A',1,'613',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(178,'BANCO INTERPACIFICO S.A',1,'616',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(179,'BANCO INVESTOR S.A.',1,'617',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(180,'BANCO TENDENCIA S.A',1,'618',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(181,'BANCO APLICAP S.A.',1,'621',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(182,'BANCO DRACMA S.A',1,'622',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(183,'BANCO PANAMERICANO S.A',1,'623',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(184,'BANCO GENERAL MOTORS S.A',1,'624',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(185,'BANCO ARAUCARIA S.A',1,'625',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(186,'BANCO FICSA S.A',1,'626',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(187,'BANCO DESTAK S.A',1,'627',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(188,'BANCO CRITERIUM S.A',1,'628',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(189,'BANCORP BANCO COML. E. DE INVESTMENTO',1,'629',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(190,'BANCO INTERCAP S.A',1,'630',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(191,'BANCO REDIMENTO S.A',1,'633',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(192,'BANCO TRIANGULO S.A',1,'634',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(193,'BANCO DO ESTADO DO AMAPA S.A',1,'635',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(194,'BANCO SOFISA S.A',1,'637',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(195,'BANCO PROSPER S.A',1,'638',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(196,'BIG S.A. - BANCO IRMAOS GUIMARAES',1,'639',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(197,'BANCO DE CREDITO METROPOLITANO S.A',1,'640',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(198,'BANCO EXCEL ECONOMICO S/A',1,'641',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(199,'BANCO SEGMENTO S.A',1,'643',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(200,'BANCO DO ESTADO DE RORAIMA S.A',1,'645',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(201,'BANCO MARKA S.A',1,'647',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(202,'BANCO ATLANTIS S.A',1,'648',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(203,'BANCO DIMENSAO S.A',1,'649',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(204,'BANCO PEBB S.A',1,'650',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(205,'ITAÚ UNIBANCO HOLDING S.A.',1,'652',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(206,'BANCO INDUSVAL S.A',1,'653',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(207,'BANCO A. J. RENNER S.A',1,'654',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(208,'BANCO VOTORANTIM S.A.',1,'655',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(209,'BANCO MATRIX S.A',1,'656',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(210,'BANCO TECNICORP S.A',1,'657',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(211,'BANCO PORTO REAL S.A',1,'658',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(212,'BANCO SANTOS S.A',1,'702',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(213,'BANCO INVESTCORP S.A.',1,'705',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(214,'BANCO DAYCOVAL S.A',1,'707',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(215,'BANCO VETOR S.A.',1,'711',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(216,'BANCO CINDAM S.A',1,'713',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(217,'BANCO VEGA S.A',1,'715',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(218,'BANCO OPERADOR S.A',1,'718',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(219,'BANCO PRIMUS S.A',1,'719',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(220,'BANCO MAXINVEST S.A',1,'720',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(221,'BANCO CREDIBEL S.A',1,'721',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(222,'BANCO INTERIOR DE SAO PAULO S.A',1,'722',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(223,'BANCO PORTO SEGURO S.A',1,'724',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(224,'BANCO FINABANCO S.A',1,'725',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(225,'BANCO UNIVERSAL S.A',1,'726',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(226,'BANCO FITAL S.A',1,'728',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(227,'BANCO FONTE S.A',1,'729',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(228,'BANCO COMERCIAL PARAGUAYO S.A',1,'730',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(229,'BANCO GNPP S.A.',1,'731',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(230,'BANCO PREMIER S.A.',1,'732',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(231,'BANCO NACOES S.A.',1,'733',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(232,'BANCO GERDAU S.A',1,'734',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(233,'BACO POTENCIAL',1,'735',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(234,'BANCO UNITED S.A',1,'736',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(235,'THECA',1,'737',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(236,'MARADA',1,'738',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(237,'BGN',1,'739',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(238,'BCN BARCLAYS',1,'740',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(239,'BRP',1,'741',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(240,'EQUATORIAL',1,'742',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(241,'BANCO EMBLEMA S.A',1,'743',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(242,'THE FIRST NATIONAL BANK OF BOSTON',1,'744',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(243,'CITIBAN N.A.',1,'745',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(244,'MODAL SA',1,'746',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(245,'RAIBOBANK DO BRASIL',1,'747',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(246,'SICREDI',1,'748',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(247,'BRMSANTIL SA',1,'749',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(248,'BANCO REPUBLIC NATIONAL OF NEW YORK (BRA',1,'750',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(249,'DRESDNER BANK LATEINAMERIKA-BRASIL S/A',1,'751',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(250,'BANCO BANQUE NATIONALE DE PARIS BRASIL S',1,'752',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(251,'BANCO COMERCIAL URUGUAI S.A.',1,'753',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(252,'BANCO MERRILL LYNCH S.A',1,'755',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(253,'BANCO COOPERATIVO DO BRASIL S.A.',1,'756',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(254,'BANCO KEB DO BRASIL S.A.',1,'757',1,'2021-03-30 16:49:53',NULL,NULL,NULL),(255,'ViaCredi',1,'085',1,'2022-10-20 13:21:22',NULL,9,NULL);
/*!40000 ALTER TABLE `banks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `banner`
--

DROP TABLE IF EXISTS `banner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `ordem` varchar(255) NOT NULL,
  `link` text DEFAULT NULL,
  `link_externo` text DEFAULT NULL,
  `ativo` binary(1) NOT NULL DEFAULT '1',
  `cont` int(11) DEFAULT 1,
  `cont2` int(11) DEFAULT 1,
  `capa` binary(1) DEFAULT '0',
  `capa2` binary(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banner`
--

LOCK TABLES `banner` WRITE;
/*!40000 ALTER TABLE `banner` DISABLE KEYS */;
/*!40000 ALTER TABLE `banner` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bill_receive`
--

DROP TABLE IF EXISTS `bill_receive`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bill_receive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_branch` int(11) DEFAULT NULL,
  `id_cost_center` int(11) DEFAULT NULL,
  `id_customer` int(11) NOT NULL,
  `id_form_of_payment` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` binary(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `id_sale` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bill_receive`
--

LOCK TABLES `bill_receive` WRITE;
/*!40000 ALTER TABLE `bill_receive` DISABLE KEYS */;
/*!40000 ALTER TABLE `bill_receive` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bill_receive_installment`
--

DROP TABLE IF EXISTS `bill_receive_installment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bill_receive_installment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_bill_receive` int(11) DEFAULT NULL,
  `id_form_of_payment` int(11) DEFAULT NULL,
  `number_portion` varchar(10) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `value_installment` decimal(12,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status_payment` int(11) DEFAULT 1,
  `pay_day` date DEFAULT NULL,
  `amount_paid` decimal(12,2) DEFAULT NULL,
  `payment_transaction` int(11) DEFAULT NULL,
  `id_account` int(11) DEFAULT NULL,
  `number_account` varchar(100) DEFAULT NULL,
  `id_bank` int(11) DEFAULT NULL,
  `own_check` binary(1) DEFAULT '1',
  `owner_check` varchar(255) DEFAULT NULL,
  `cpfcnpj_check` varchar(50) DEFAULT NULL,
  `agency` varchar(100) DEFAULT NULL,
  `number_check` varchar(100) DEFAULT NULL,
  `status` binary(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `percentage_commission_seller` decimal(10,2) DEFAULT 0.00,
  `origin_commission_seller` int(11) DEFAULT NULL,
  `id_customer_seller` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bill_receive_installment`
--

LOCK TABLES `bill_receive_installment` WRITE;
/*!40000 ALTER TABLE `bill_receive_installment` DISABLE KEYS */;
/*!40000 ALTER TABLE `bill_receive_installment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bill_receive_installment_attachment`
--

DROP TABLE IF EXISTS `bill_receive_installment_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bill_receive_installment_attachment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_bill_receive_installment` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `filename` varchar(350) DEFAULT NULL,
  `extension` varchar(20) DEFAULT NULL,
  `status` binary(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bill_receive_installment_attachment`
--

LOCK TABLES `bill_receive_installment_attachment` WRITE;
/*!40000 ALTER TABLE `bill_receive_installment_attachment` DISABLE KEYS */;
/*!40000 ALTER TABLE `bill_receive_installment_attachment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bills_to_pay`
--

DROP TABLE IF EXISTS `bills_to_pay`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bills_to_pay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_customer` int(11) NOT NULL,
  `id_branch` int(11) DEFAULT NULL,
  `id_cost_center` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `competence` date DEFAULT NULL,
  `status` binary(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `id_form_of_payment` int(11) DEFAULT NULL,
  `id_bill_receive` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bills_to_pay`
--

LOCK TABLES `bills_to_pay` WRITE;
/*!40000 ALTER TABLE `bills_to_pay` DISABLE KEYS */;
/*!40000 ALTER TABLE `bills_to_pay` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bills_to_pay_installments`
--

DROP TABLE IF EXISTS `bills_to_pay_installments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bills_to_pay_installments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_bills_to_pay` int(11) DEFAULT NULL,
  `id_form_of_payment` int(11) DEFAULT NULL,
  `number_portion` varchar(10) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `value_of_installments` decimal(12,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status_payment` int(11) DEFAULT 1,
  `status` binary(1) DEFAULT '1',
  `pay_day` date DEFAULT NULL,
  `amount_paid` decimal(12,2) DEFAULT NULL,
  `payment_transaction` int(11) DEFAULT 0,
  `id_account` int(11) DEFAULT NULL,
  `number_account` varchar(100) DEFAULT NULL,
  `id_bank` int(11) DEFAULT NULL,
  `own_check` binary(1) DEFAULT '1',
  `owner_check` varchar(255) DEFAULT NULL,
  `cpfcnpj_check` varchar(50) DEFAULT NULL,
  `agency` varchar(100) DEFAULT NULL,
  `number_check` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `id_bill_receive_installment` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bills_to_pay_installments`
--

LOCK TABLES `bills_to_pay_installments` WRITE;
/*!40000 ALTER TABLE `bills_to_pay_installments` DISABLE KEYS */;
/*!40000 ALTER TABLE `bills_to_pay_installments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bills_to_pay_installments_attachment`
--

DROP TABLE IF EXISTS `bills_to_pay_installments_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bills_to_pay_installments_attachment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_bills_to_pay_installments` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `filename` varchar(350) DEFAULT NULL,
  `extension` varchar(20) DEFAULT NULL,
  `status` binary(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bills_to_pay_installments_attachment`
--

LOCK TABLES `bills_to_pay_installments_attachment` WRITE;
/*!40000 ALTER TABLE `bills_to_pay_installments_attachment` DISABLE KEYS */;
/*!40000 ALTER TABLE `bills_to_pay_installments_attachment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branch`
--

DROP TABLE IF EXISTS `branch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `branch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(350) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `address` varchar(255) DEFAULT NULL,
  `neighborhood` varchar(255) DEFAULT NULL,
  `number` varchar(100) DEFAULT NULL,
  `complement` varchar(100) DEFAULT NULL,
  `cnpj` varchar(100) DEFAULT NULL,
  `cep` varchar(100) DEFAULT NULL,
  `id_city` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `restrict_owner_data` tinyint(4) DEFAULT 0,
  `immovable_record` varchar(255) DEFAULT NULL,
  `logo_menu_capa` binary(1) DEFAULT '0',
  `logo_menu_cont` int(11) DEFAULT 0,
  `logo_menu_ext` varchar(20) DEFAULT NULL,
  `logo_mini_capa` binary(1) DEFAULT '0',
  `logo_mini_cont` int(11) DEFAULT 0,
  `logo_mini_ext` varchar(20) DEFAULT NULL,
  `logo_rodape_capa` binary(1) DEFAULT '0',
  `logo_rodape_cont` int(11) DEFAULT 0,
  `logo_rodape_ext` varchar(20) DEFAULT NULL,
  `creci_legal` varchar(100) DEFAULT NULL,
  `name_legal` varchar(255) DEFAULT NULL,
  `commission` int(11) DEFAULT 6,
  `example_property_value` decimal(10,2) DEFAULT 100000.00,
  `percentage_commission_sale` decimal(10,2) DEFAULT 5.00,
  `percentage_commission_virtual_rate` decimal(10,2) DEFAULT 0.00,
  `percentage_commission_real_rate` decimal(10,2) DEFAULT 0.00,
  `percentage_commission_seller` decimal(10,2) DEFAULT 0.00,
  `origin_commission_seller` int(11) DEFAULT 3,
  `id_cost_center_commission_branch` int(11) DEFAULT NULL,
  `id_cost_center_commission_rate` int(11) DEFAULT NULL,
  `id_cost_center_commission_seller` int(11) DEFAULT NULL,
  `id_cost_center_tax` int(11) DEFAULT NULL,
  `id_cost_center_receive_commission` int(11) DEFAULT NULL,
  `id_cost_center_pay_commission` int(11) DEFAULT NULL,
  `id_form_payment_seller` int(11) DEFAULT NULL,
  `id_form_payment_tax` int(11) DEFAULT NULL,
  `days_after_seller` int(11) DEFAULT NULL,
  `days_after_tax` int(11) DEFAULT NULL,
  `id_customer_tax` int(11) DEFAULT NULL,
  `id_form_payment_single` int(11) DEFAULT NULL,
  `days_after_single` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branch`
--

LOCK TABLES `branch` WRITE;
/*!40000 ALTER TABLE `branch` DISABLE KEYS */;
INSERT INTO `branch` VALUES (1,'Realize Repasse','suporte@ydealtecnologia.com.br',1,'Rua: Manoel Luís dos Santos','Centro','190',NULL,'00000000000000','88200000',4579,'2024-02-22 16:51:06',NULL,1,NULL,0,'6',_binary '0',0,'',_binary '0',0,'',_binary '0',0,'',NULL,'Realize Repasse',6,100000.00,5.00,12.00,8.00,40.00,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1);
/*!40000 ALTER TABLE `branch` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branch_presentation_config`
--

DROP TABLE IF EXISTS `branch_presentation_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `branch_presentation_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_branch` int(11) NOT NULL,
  `id_presentation_config` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branch_presentation_config`
--

LOCK TABLES `branch_presentation_config` WRITE;
/*!40000 ALTER TABLE `branch_presentation_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `branch_presentation_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `branch_user_position`
--

DROP TABLE IF EXISTS `branch_user_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `branch_user_position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_branch` int(11) NOT NULL,
  `id_user_position` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `percentage_commission` decimal(10,3) DEFAULT 0.000,
  `origin_commission` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `id_form_payment` int(11) DEFAULT NULL,
  `id_cost_center` int(11) DEFAULT NULL,
  `days_after` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branch_user_position`
--

LOCK TABLES `branch_user_position` WRITE;
/*!40000 ALTER TABLE `branch_user_position` DISABLE KEYS */;
/*!40000 ALTER TABLE `branch_user_position` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `change_link`
--

DROP TABLE IF EXISTS `change_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `change_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_send` int(11) DEFAULT NULL,
  `user_get` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `status` binary(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `change_link`
--

LOCK TABLES `change_link` WRITE;
/*!40000 ALTER TABLE `change_link` DISABLE KEYS */;
/*!40000 ALTER TABLE `change_link` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `change_link_item`
--

DROP TABLE IF EXISTS `change_link_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `change_link_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_change` int(11) DEFAULT NULL,
  `id_item` int(11) DEFAULT NULL,
  `table_item` varchar(255) DEFAULT NULL,
  `status` binary(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `change_link_item`
--

LOCK TABLES `change_link_item` WRITE;
/*!40000 ALTER TABLE `change_link_item` DISABLE KEYS */;
/*!40000 ALTER TABLE `change_link_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cities`
--

DROP TABLE IF EXISTS `cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ibge_code` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `uf` char(2) NOT NULL,
  `slugify` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5571 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cities`
--

LOCK TABLES `cities` WRITE;
/*!40000 ALTER TABLE `cities` DISABLE KEYS */;
INSERT INTO `cities` VALUES (1,1100015,'ALTA FLORESTA D\'OESTE','RO','alta-floresta-doeste'),(2,1100023,'ARIQUEMES','RO','ariquemes'),(3,1100031,'CABIXI','RO','cabixi'),(4,1100049,'CACOAL','RO','cacoal'),(5,1100056,'CEREJEIRAS','RO','cerejeiras'),(6,1100064,'COLORADO DO OESTE','RO','colorado-do-oeste'),(7,1100072,'CORUMBIARA','RO','corumbiara'),(8,1100080,'COSTA MARQUES','RO','costa-marques'),(9,1100098,'ESPIGÃO D\'OESTE','RO','espigao-doeste'),(10,1100106,'GUAJARÁ-MIRIM','RO','guajara-mirim'),(11,1100114,'JARU','RO','jaru'),(12,1100122,'JI-PARANÁ','RO','ji-parana'),(13,1100130,'MACHADINHO D\'OESTE','RO','machadinho-doeste'),(14,1100148,'NOVA BRASILÂNDIA D\'OESTE','RO','nova-brasilandia-doeste'),(15,1100155,'OURO PRETO DO OESTE','RO','ouro-preto-do-oeste'),(16,1100189,'PIMENTA BUENO','RO','pimenta-bueno'),(17,1100205,'PORTO VELHO','RO','porto-velho'),(18,1100254,'PRESIDENTE MÉDICI','RO','presidente-medici'),(19,1100262,'RIO CRESPO','RO','rio-crespo'),(20,1100288,'ROLIM DE MOURA','RO','rolim-de-moura'),(21,1100296,'SANTA LUZIA D\'OESTE','RO','santa-luzia-doeste'),(22,1100304,'VILHENA','RO','vilhena'),(23,1100320,'SÃO MIGUEL DO GUAPORÉ','RO','sao-miguel-do-guapore'),(24,1100338,'NOVA MAMORÉ','RO','nova-mamore'),(25,1100346,'ALVORADA D\'OESTE','RO','alvorada-doeste'),(26,1100379,'ALTO ALEGRE DOS PARECIS','RO','alto-alegre-dos-parecis'),(27,1100403,'ALTO PARAÍSO','RO','alto-paraiso'),(28,1100452,'BURITIS','RO','buritis'),(29,1100502,'NOVO HORIZONTE DO OESTE','RO','novo-horizonte-do-oeste'),(30,1100601,'CACAULÂNDIA','RO','cacaulandia'),(31,1100700,'CAMPO NOVO DE RONDÔNIA','RO','campo-novo-de-rondonia'),(32,1100809,'CANDEIAS DO JAMARI','RO','candeias-do-jamari'),(33,1100908,'CASTANHEIRAS','RO','castanheiras'),(34,1100924,'CHUPINGUAIA','RO','chupinguaia'),(35,1100940,'CUJUBIM','RO','cujubim'),(36,1101005,'GOVERNADOR JORGE TEIXEIRA','RO','governador-jorge-teixeira'),(37,1101104,'ITAPUÃ DO OESTE','RO','itapua-do-oeste'),(38,1101203,'MINISTRO ANDREAZZA','RO','ministro-andreazza'),(39,1101302,'MIRANTE DA SERRA','RO','mirante-da-serra'),(40,1101401,'MONTE NEGRO','RO','monte-negro'),(41,1101435,'NOVA UNIÃO','RO','nova-uniao'),(42,1101450,'PARECIS','RO','parecis'),(43,1101468,'PIMENTEIRAS DO OESTE','RO','pimenteiras-do-oeste'),(44,1101476,'PRIMAVERA DE RONDÔNIA','RO','primavera-de-rondonia'),(45,1101484,'SÃO FELIPE D\'OESTE','RO','sao-felipe-doeste'),(46,1101492,'SÃO FRANCISCO DO GUAPORÉ','RO','sao-francisco-do-guapore'),(47,1101500,'SERINGUEIRAS','RO','seringueiras'),(48,1101559,'TEIXEIRÓPOLIS','RO','teixeiropolis'),(49,1101609,'THEOBROMA','RO','theobroma'),(50,1101708,'URUPÁ','RO','urupa'),(51,1101757,'VALE DO ANARI','RO','vale-do-anari'),(52,1101807,'VALE DO PARAÍSO','RO','vale-do-paraiso'),(53,1200013,'ACRELÂNDIA','AC','acrelandia'),(54,1200054,'ASSIS BRASIL','AC','assis-brasil'),(55,1200104,'BRASILÉIA','AC','brasileia'),(56,1200138,'BUJARI','AC','bujari'),(57,1200179,'CAPIXABA','AC','capixaba'),(58,1200203,'CRUZEIRO DO SUL','AC','cruzeiro-do-sul'),(59,1200252,'EPITACIOLÂNDIA','AC','epitaciolandia'),(60,1200302,'FEIJÓ','AC','feijo'),(61,1200328,'JORDÃO','AC','jordao'),(62,1200336,'MÂNCIO LIMA','AC','mancio-lima'),(63,1200344,'MANOEL URBANO','AC','manoel-urbano'),(64,1200351,'MARECHAL THAUMATURGO','AC','marechal-thaumaturgo'),(65,1200385,'PLÁCIDO DE CASTRO','AC','placido-de-castro'),(66,1200393,'PORTO WALTER','AC','porto-walter'),(67,1200401,'RIO BRANCO','AC','rio-branco'),(68,1200427,'RODRIGUES ALVES','AC','rodrigues-alves'),(69,1200435,'SANTA ROSA DO PURUS','AC','santa-rosa-do-purus'),(70,1200450,'SENADOR GUIOMARD','AC','senador-guiomard'),(71,1200500,'SENA MADUREIRA','AC','sena-madureira'),(72,1200609,'TARAUACÁ','AC','tarauaca'),(73,1200708,'XAPURI','AC','xapuri'),(74,1200807,'PORTO ACRE','AC','porto-acre'),(75,1300029,'ALVARÃES','AM','alvaraes'),(76,1300060,'AMATURÁ','AM','amatura'),(77,1300086,'ANAMÃ','AM','anama'),(78,1300102,'ANORI','AM','anori'),(79,1300144,'APUÍ','AM','apui'),(80,1300201,'ATALAIA DO NORTE','AM','atalaia-do-norte'),(81,1300300,'AUTAZES','AM','autazes'),(82,1300409,'BARCELOS','AM','barcelos'),(83,1300508,'BARREIRINHA','AM','barreirinha'),(84,1300607,'BENJAMIN CONSTANT','AM','benjamin-constant'),(85,1300631,'BERURI','AM','beruri'),(86,1300680,'BOA VISTA DO RAMOS','AM','boa-vista-do-ramos'),(87,1300706,'BOCA DO ACRE','AM','boca-do-acre'),(88,1300805,'BORBA','AM','borba'),(89,1300839,'CAAPIRANGA','AM','caapiranga'),(90,1300904,'CANUTAMA','AM','canutama'),(91,1301001,'CARAUARI','AM','carauari'),(92,1301100,'CAREIRO','AM','careiro'),(93,1301159,'CAREIRO DA VÁRZEA','AM','careiro-da-varzea'),(94,1301209,'COARI','AM','coari'),(95,1301308,'CODAJÁS','AM','codajas'),(96,1301407,'EIRUNEPÉ','AM','eirunepe'),(97,1301506,'ENVIRA','AM','envira'),(98,1301605,'FONTE BOA','AM','fonte-boa'),(99,1301654,'GUAJARÁ','AM','guajara'),(100,1301704,'HUMAITÁ','AM','humaita'),(101,1301803,'IPIXUNA','AM','ipixuna'),(102,1301852,'IRANDUBA','AM','iranduba'),(103,1301902,'ITACOATIARA','AM','itacoatiara'),(104,1301951,'ITAMARATI','AM','itamarati'),(105,1302009,'ITAPIRANGA','AM','itapiranga'),(106,1302108,'JAPURÁ','AM','japura'),(107,1302207,'JURUÁ','AM','jurua'),(108,1302306,'JUTAÍ','AM','jutai'),(109,1302405,'LÁBREA','AM','labrea'),(110,1302504,'MANACAPURU','AM','manacapuru'),(111,1302553,'MANAQUIRI','AM','manaquiri'),(112,1302603,'MANAUS','AM','manaus'),(113,1302702,'MANICORÉ','AM','manicore'),(114,1302801,'MARAÃ','AM','maraa'),(115,1302900,'MAUÉS','AM','maues'),(116,1303007,'NHAMUNDÁ','AM','nhamunda'),(117,1303106,'NOVA OLINDA DO NORTE','AM','nova-olinda-do-norte'),(118,1303205,'NOVO AIRÃO','AM','novo-airao'),(119,1303304,'NOVO ARIPUANÃ','AM','novo-aripuana'),(120,1303403,'PARINTINS','AM','parintins'),(121,1303502,'PAUINI','AM','pauini'),(122,1303536,'PRESIDENTE FIGUEIREDO','AM','presidente-figueiredo'),(123,1303569,'RIO PRETO DA EVA','AM','rio-preto-da-eva'),(124,1303601,'SANTA ISABEL DO RIO NEGRO','AM','santa-isabel-do-rio-negro'),(125,1303700,'SANTO ANTÔNIO DO IÇÁ','AM','santo-antonio-do-ica'),(126,1303809,'SÃO GABRIEL DA CACHOEIRA','AM','sao-gabriel-da-cachoeira'),(127,1303908,'SÃO PAULO DE OLIVENÇA','AM','sao-paulo-de-olivenca'),(128,1303957,'SÃO SEBASTIÃO DO UATUMÃ','AM','sao-sebastiao-do-uatuma'),(129,1304005,'SILVES','AM','silves'),(130,1304062,'TABATINGA','AM','tabatinga'),(131,1304104,'TAPAUÁ','AM','tapaua'),(132,1304203,'TEFÉ','AM','tefe'),(133,1304237,'TONANTINS','AM','tonantins'),(134,1304260,'UARINI','AM','uarini'),(135,1304302,'URUCARÁ','AM','urucara'),(136,1304401,'URUCURITUBA','AM','urucurituba'),(137,1400027,'AMAJARI','RR','amajari'),(138,1400050,'ALTO ALEGRE','RR','alto-alegre'),(139,1400100,'BOA VISTA','RR','boa-vista'),(140,1400159,'BONFIM','RR','bonfim'),(141,1400175,'CANTÁ','RR','canta'),(142,1400209,'CARACARAÍ','RR','caracarai'),(143,1400233,'CAROEBE','RR','caroebe'),(144,1400282,'IRACEMA','RR','iracema'),(145,1400308,'MUCAJAÍ','RR','mucajai'),(146,1400407,'NORMANDIA','RR','normandia'),(147,1400456,'PACARAIMA','RR','pacaraima'),(148,1400472,'RORAINÓPOLIS','RR','rorainopolis'),(149,1400506,'SÃO JOÃO DA BALIZA','RR','sao-joao-da-baliza'),(150,1400605,'SÃO LUIZ','RR','sao-luiz'),(151,1400704,'UIRAMUTÃ','RR','uiramuta'),(152,1500107,'ABAETETUBA','PA','abaetetuba'),(153,1500131,'ABEL FIGUEIREDO','PA','abel-figueiredo'),(154,1500206,'ACARÁ','PA','acara'),(155,1500305,'AFUÁ','PA','afua'),(156,1500347,'ÁGUA AZUL DO NORTE','PA','agua-azul-do-norte'),(157,1500404,'ALENQUER','PA','alenquer'),(158,1500503,'ALMEIRIM','PA','almeirim'),(159,1500602,'ALTAMIRA','PA','altamira'),(160,1500701,'ANAJÁS','PA','anajas'),(161,1500800,'ANANINDEUA','PA','ananindeua'),(162,1500859,'ANAPU','PA','anapu'),(163,1500909,'AUGUSTO CORRÊA','PA','augusto-correa'),(164,1500958,'AURORA DO PARÁ','PA','aurora-do-para'),(165,1501006,'AVEIRO','PA','aveiro'),(166,1501105,'BAGRE','PA','bagre'),(167,1501204,'BAIÃO','PA','baiao'),(168,1501253,'BANNACH','PA','bannach'),(169,1501303,'BARCARENA','PA','barcarena'),(170,1501402,'BELÉM','PA','belem'),(171,1501451,'BELTERRA','PA','belterra'),(172,1501501,'BENEVIDES','PA','benevides'),(173,1501576,'BOM JESUS DO TOCANTINS','PA','bom-jesus-do-tocantins'),(174,1501600,'BONITO','PA','bonito'),(175,1501709,'BRAGANÇA','PA','braganca'),(176,1501725,'BRASIL NOVO','PA','brasil-novo'),(177,1501758,'BREJO GRANDE DO ARAGUAIA','PA','brejo-grande-do-araguaia'),(178,1501782,'BREU BRANCO','PA','breu-branco'),(179,1501808,'BREVES','PA','breves'),(180,1501907,'BUJARU','PA','bujaru'),(181,1501956,'CACHOEIRA DO PIRIÁ','PA','cachoeira-do-piria'),(182,1502004,'CACHOEIRA DO ARARI','PA','cachoeira-do-arari'),(183,1502103,'CAMETÁ','PA','cameta'),(184,1502152,'CANAÃ DOS CARAJÁS','PA','canaa-dos-carajas'),(185,1502202,'CAPANEMA','PA','capanema'),(186,1502301,'CAPITÃO POÇO','PA','capitao-poco'),(187,1502400,'CASTANHAL','PA','castanhal'),(188,1502509,'CHAVES','PA','chaves'),(189,1502608,'COLARES','PA','colares'),(190,1502707,'CONCEIÇÃO DO ARAGUAIA','PA','conceicao-do-araguaia'),(191,1502756,'CONCÓRDIA DO PARÁ','PA','concordia-do-para'),(192,1502764,'CUMARU DO NORTE','PA','cumaru-do-norte'),(193,1502772,'CURIONÓPOLIS','PA','curionopolis'),(194,1502806,'CURRALINHO','PA','curralinho'),(195,1502855,'CURUÁ','PA','curua'),(196,1502905,'CURUÇÁ','PA','curuca'),(197,1502939,'DOM ELISEU','PA','dom-eliseu'),(198,1502954,'ELDORADO DOS CARAJÁS','PA','eldorado-dos-carajas'),(199,1503002,'FARO','PA','faro'),(200,1503044,'FLORESTA DO ARAGUAIA','PA','floresta-do-araguaia'),(201,1503077,'GARRAFÃO DO NORTE','PA','garrafao-do-norte'),(202,1503093,'GOIANÉSIA DO PARÁ','PA','goianesia-do-para'),(203,1503101,'GURUPÁ','PA','gurupa'),(204,1503200,'IGARAPÉ-AÇU','PA','igarape-acu'),(205,1503309,'IGARAPÉ-MIRI','PA','igarape-miri'),(206,1503408,'INHANGAPI','PA','inhangapi'),(207,1503457,'IPIXUNA DO PARÁ','PA','ipixuna-do-para'),(208,1503507,'IRITUIA','PA','irituia'),(209,1503606,'ITAITUBA','PA','itaituba'),(210,1503705,'ITUPIRANGA','PA','itupiranga'),(211,1503754,'JACAREACANGA','PA','jacareacanga'),(212,1503804,'JACUNDÁ','PA','jacunda'),(213,1503903,'JURUTI','PA','juruti'),(214,1504000,'LIMOEIRO DO AJURU','PA','limoeiro-do-ajuru'),(215,1504059,'MÃE DO RIO','PA','mae-do-rio'),(216,1504109,'MAGALHÃES BARATA','PA','magalhaes-barata'),(217,1504208,'MARABÁ','PA','maraba'),(218,1504307,'MARACANÃ','PA','maracana'),(219,1504406,'MARAPANIM','PA','marapanim'),(220,1504422,'MARITUBA','PA','marituba'),(221,1504455,'MEDICILÂNDIA','PA','medicilandia'),(222,1504505,'MELGAÇO','PA','melgaco'),(223,1504604,'MOCAJUBA','PA','mocajuba'),(224,1504703,'MOJU','PA','moju'),(225,1504752,'MOJUÍ DOS CAMPOS','PA','mojui-dos-campos'),(226,1504802,'MONTE ALEGRE','PA','monte-alegre'),(227,1504901,'MUANÁ','PA','muana'),(228,1504950,'NOVA ESPERANÇA DO PIRIÁ','PA','nova-esperanca-do-piria'),(229,1504976,'NOVA IPIXUNA','PA','nova-ipixuna'),(230,1505007,'NOVA TIMBOTEUA','PA','nova-timboteua'),(231,1505031,'NOVO PROGRESSO','PA','novo-progresso'),(232,1505064,'NOVO REPARTIMENTO','PA','novo-repartimento'),(233,1505106,'ÓBIDOS','PA','obidos'),(234,1505205,'OEIRAS DO PARÁ','PA','oeiras-do-para'),(235,1505304,'ORIXIMINÁ','PA','oriximina'),(236,1505403,'OURÉM','PA','ourem'),(237,1505437,'OURILÂNDIA DO NORTE','PA','ourilandia-do-norte'),(238,1505486,'PACAJÁ','PA','pacaja'),(239,1505494,'PALESTINA DO PARÁ','PA','palestina-do-para'),(240,1505502,'PARAGOMINAS','PA','paragominas'),(241,1505536,'PARAUAPEBAS','PA','parauapebas'),(242,1505551,'PAU D\'ARCO','PA','pau-darco'),(243,1505601,'PEIXE-BOI','PA','peixe-boi'),(244,1505635,'PIÇARRA','PA','picarra'),(245,1505650,'PLACAS','PA','placas'),(246,1505700,'PONTA DE PEDRAS','PA','ponta-de-pedras'),(247,1505809,'PORTEL','PA','portel'),(248,1505908,'PORTO DE MOZ','PA','porto-de-moz'),(249,1506005,'PRAINHA','PA','prainha'),(250,1506104,'PRIMAVERA','PA','primavera'),(251,1506112,'QUATIPURU','PA','quatipuru'),(252,1506138,'REDENÇÃO','PA','redencao'),(253,1506161,'RIO MARIA','PA','rio-maria'),(254,1506187,'RONDON DO PARÁ','PA','rondon-do-para'),(255,1506195,'RURÓPOLIS','PA','ruropolis'),(256,1506203,'SALINÓPOLIS','PA','salinopolis'),(257,1506302,'SALVATERRA','PA','salvaterra'),(258,1506351,'SANTA BÁRBARA DO PARÁ','PA','santa-barbara-do-para'),(259,1506401,'SANTA CRUZ DO ARARI','PA','santa-cruz-do-arari'),(260,1506500,'SANTA ISABEL DO PARÁ','PA','santa-isabel-do-para'),(261,1506559,'SANTA LUZIA DO PARÁ','PA','santa-luzia-do-para'),(262,1506583,'SANTA MARIA DAS BARREIRAS','PA','santa-maria-das-barreiras'),(263,1506609,'SANTA MARIA DO PARÁ','PA','santa-maria-do-para'),(264,1506708,'SANTANA DO ARAGUAIA','PA','santana-do-araguaia'),(265,1506807,'SANTARÉM','PA','santarem'),(266,1506906,'SANTARÉM NOVO','PA','santarem-novo'),(267,1507003,'SANTO ANTÔNIO DO TAUÁ','PA','santo-antonio-do-taua'),(268,1507102,'SÃO CAETANO DE ODIVELAS','PA','sao-caetano-de-odivelas'),(269,1507151,'SÃO DOMINGOS DO ARAGUAIA','PA','sao-domingos-do-araguaia'),(270,1507201,'SÃO DOMINGOS DO CAPIM','PA','sao-domingos-do-capim'),(271,1507300,'SÃO FÉLIX DO XINGU','PA','sao-felix-do-xingu'),(272,1507409,'SÃO FRANCISCO DO PARÁ','PA','sao-francisco-do-para'),(273,1507458,'SÃO GERALDO DO ARAGUAIA','PA','sao-geraldo-do-araguaia'),(274,1507466,'SÃO JOÃO DA PONTA','PA','sao-joao-da-ponta'),(275,1507474,'SÃO JOÃO DE PIRABAS','PA','sao-joao-de-pirabas'),(276,1507508,'SÃO JOÃO DO ARAGUAIA','PA','sao-joao-do-araguaia'),(277,1507607,'SÃO MIGUEL DO GUAMÁ','PA','sao-miguel-do-guama'),(278,1507706,'SÃO SEBASTIÃO DA BOA VISTA','PA','sao-sebastiao-da-boa-vista'),(279,1507755,'SAPUCAIA','PA','sapucaia'),(280,1507805,'SENADOR JOSÉ PORFÍRIO','PA','senador-jose-porfirio'),(281,1507904,'SOURE','PA','soure'),(282,1507953,'TAILÂNDIA','PA','tailandia'),(283,1507961,'TERRA ALTA','PA','terra-alta'),(284,1507979,'TERRA SANTA','PA','terra-santa'),(285,1508001,'TOMÉ-AÇU','PA','tome-acu'),(286,1508035,'TRACUATEUA','PA','tracuateua'),(287,1508050,'TRAIRÃO','PA','trairao'),(288,1508084,'TUCUMÃ','PA','tucuma'),(289,1508100,'TUCURUÍ','PA','tucurui'),(290,1508126,'ULIANÓPOLIS','PA','ulianopolis'),(291,1508159,'URUARÁ','PA','uruara'),(292,1508209,'VIGIA','PA','vigia'),(293,1508308,'VISEU','PA','viseu'),(294,1508357,'VITÓRIA DO XINGU','PA','vitoria-do-xingu'),(295,1508407,'XINGUARA','PA','xinguara'),(296,1600055,'SERRA DO NAVIO','AP','serra-do-navio'),(297,1600105,'AMAPÁ','AP','amapa'),(298,1600154,'PEDRA BRANCA DO AMAPARI','AP','pedra-branca-do-amapari'),(299,1600204,'CALÇOENE','AP','calcoene'),(300,1600212,'CUTIAS','AP','cutias'),(301,1600238,'FERREIRA GOMES','AP','ferreira-gomes'),(302,1600253,'ITAUBAL','AP','itaubal'),(303,1600279,'LARANJAL DO JARI','AP','laranjal-do-jari'),(304,1600303,'MACAPÁ','AP','macapa'),(305,1600402,'MAZAGÃO','AP','mazagao'),(306,1600501,'OIAPOQUE','AP','oiapoque'),(307,1600535,'PORTO GRANDE','AP','porto-grande'),(308,1600550,'PRACUÚBA','AP','pracuuba'),(309,1600600,'SANTANA','AP','santana'),(310,1600709,'TARTARUGALZINHO','AP','tartarugalzinho'),(311,1600808,'VITÓRIA DO JARI','AP','vitoria-do-jari'),(312,1700251,'ABREULÂNDIA','TO','abreulandia'),(313,1700301,'AGUIARNÓPOLIS','TO','aguiarnopolis'),(314,1700350,'ALIANÇA DO TOCANTINS','TO','alianca-do-tocantins'),(315,1700400,'ALMAS','TO','almas'),(316,1700707,'ALVORADA','TO','alvorada'),(317,1701002,'ANANÁS','TO','ananas'),(318,1701051,'ANGICO','TO','angico'),(319,1701101,'APARECIDA DO RIO NEGRO','TO','aparecida-do-rio-negro'),(320,1701309,'ARAGOMINAS','TO','aragominas'),(321,1701903,'ARAGUACEMA','TO','araguacema'),(322,1702000,'ARAGUAÇU','TO','araguacu'),(323,1702109,'ARAGUAÍNA','TO','araguaina'),(324,1702158,'ARAGUANÃ','TO','araguana'),(325,1702208,'ARAGUATINS','TO','araguatins'),(326,1702307,'ARAPOEMA','TO','arapoema'),(327,1702406,'ARRAIAS','TO','arraias'),(328,1702554,'AUGUSTINÓPOLIS','TO','augustinopolis'),(329,1702703,'AURORA DO TOCANTINS','TO','aurora-do-tocantins'),(330,1702901,'AXIXÁ DO TOCANTINS','TO','axixa-do-tocantins'),(331,1703008,'BABAÇULÂNDIA','TO','babaculandia'),(332,1703057,'BANDEIRANTES DO TOCANTINS','TO','bandeirantes-do-tocantins'),(333,1703073,'BARRA DO OURO','TO','barra-do-ouro'),(334,1703107,'BARROLÂNDIA','TO','barrolandia'),(335,1703206,'BERNARDO SAYÃO','TO','bernardo-sayao'),(336,1703305,'BOM JESUS DO TOCANTINS','TO','bom-jesus-do-tocantins'),(337,1703602,'BRASILÂNDIA DO TOCANTINS','TO','brasilandia-do-tocantins'),(338,1703701,'BREJINHO DE NAZARÉ','TO','brejinho-de-nazare'),(339,1703800,'BURITI DO TOCANTINS','TO','buriti-do-tocantins'),(340,1703826,'CACHOEIRINHA','TO','cachoeirinha'),(341,1703842,'CAMPOS LINDOS','TO','campos-lindos'),(342,1703867,'CARIRI DO TOCANTINS','TO','cariri-do-tocantins'),(343,1703883,'CARMOLÂNDIA','TO','carmolandia'),(344,1703891,'CARRASCO BONITO','TO','carrasco-bonito'),(345,1703909,'CASEARA','TO','caseara'),(346,1704105,'CENTENÁRIO','TO','centenario'),(347,1704600,'CHAPADA DE AREIA','TO','chapada-de-areia'),(348,1705102,'CHAPADA DA NATIVIDADE','TO','chapada-da-natividade'),(349,1705508,'COLINAS DO TOCANTINS','TO','colinas-do-tocantins'),(350,1705557,'COMBINADO','TO','combinado'),(351,1705607,'CONCEIÇÃO DO TOCANTINS','TO','conceicao-do-tocantins'),(352,1706001,'COUTO MAGALHÃES','TO','couto-magalhaes'),(353,1706100,'CRISTALÂNDIA','TO','cristalandia'),(354,1706258,'CRIXÁS DO TOCANTINS','TO','crixas-do-tocantins'),(355,1706506,'DARCINÓPOLIS','TO','darcinopolis'),(356,1707009,'DIANÓPOLIS','TO','dianopolis'),(357,1707108,'DIVINÓPOLIS DO TOCANTINS','TO','divinopolis-do-tocantins'),(358,1707207,'DOIS IRMÃOS DO TOCANTINS','TO','dois-irmaos-do-tocantins'),(359,1707306,'DUERÉ','TO','duere'),(360,1707405,'ESPERANTINA','TO','esperantina'),(361,1707553,'FÁTIMA','TO','fatima'),(362,1707652,'FIGUEIRÓPOLIS','TO','figueiropolis'),(363,1707702,'FILADÉLFIA','TO','filadelfia'),(364,1708205,'FORMOSO DO ARAGUAIA','TO','formoso-do-araguaia'),(365,1708254,'FORTALEZA DO TABOCÃO','TO','fortaleza-do-tabocao'),(366,1708304,'GOIANORTE','TO','goianorte'),(367,1709005,'GOIATINS','TO','goiatins'),(368,1709302,'GUARAÍ','TO','guarai'),(369,1709500,'GURUPI','TO','gurupi'),(370,1709807,'IPUEIRAS','TO','ipueiras'),(371,1710508,'ITACAJÁ','TO','itacaja'),(372,1710706,'ITAGUATINS','TO','itaguatins'),(373,1710904,'ITAPIRATINS','TO','itapiratins'),(374,1711100,'ITAPORÃ DO TOCANTINS','TO','itapora-do-tocantins'),(375,1711506,'JAÚ DO TOCANTINS','TO','jau-do-tocantins'),(376,1711803,'JUARINA','TO','juarina'),(377,1711902,'LAGOA DA CONFUSÃO','TO','lagoa-da-confusao'),(378,1711951,'LAGOA DO TOCANTINS','TO','lagoa-do-tocantins'),(379,1712009,'LAJEADO','TO','lajeado'),(380,1712157,'LAVANDEIRA','TO','lavandeira'),(381,1712405,'LIZARDA','TO','lizarda'),(382,1712454,'LUZINÓPOLIS','TO','luzinopolis'),(383,1712504,'MARIANÓPOLIS DO TOCANTINS','TO','marianopolis-do-tocantins'),(384,1712702,'MATEIROS','TO','mateiros'),(385,1712801,'MAURILÂNDIA DO TOCANTINS','TO','maurilandia-do-tocantins'),(386,1713205,'MIRACEMA DO TOCANTINS','TO','miracema-do-tocantins'),(387,1713304,'MIRANORTE','TO','miranorte'),(388,1713601,'MONTE DO CARMO','TO','monte-do-carmo'),(389,1713700,'MONTE SANTO DO TOCANTINS','TO','monte-santo-do-tocantins'),(390,1713809,'PALMEIRAS DO TOCANTINS','TO','palmeiras-do-tocantins'),(391,1713957,'MURICILÂNDIA','TO','muricilandia'),(392,1714203,'NATIVIDADE','TO','natividade'),(393,1714302,'NAZARÉ','TO','nazare'),(394,1714880,'NOVA OLINDA','TO','nova-olinda'),(395,1715002,'NOVA ROSALÂNDIA','TO','nova-rosalandia'),(396,1715101,'NOVO ACORDO','TO','novo-acordo'),(397,1715150,'NOVO ALEGRE','TO','novo-alegre'),(398,1715259,'NOVO JARDIM','TO','novo-jardim'),(399,1715507,'OLIVEIRA DE FÁTIMA','TO','oliveira-de-fatima'),(400,1715705,'PALMEIRANTE','TO','palmeirante'),(401,1715754,'PALMEIRÓPOLIS','TO','palmeiropolis'),(402,1716109,'PARAÍSO DO TOCANTINS','TO','paraiso-do-tocantins'),(403,1716208,'PARANÃ','TO','parana'),(404,1716307,'PAU D\'ARCO','TO','pau-darco'),(405,1716505,'PEDRO AFONSO','TO','pedro-afonso'),(406,1716604,'PEIXE','TO','peixe'),(407,1716653,'PEQUIZEIRO','TO','pequizeiro'),(408,1716703,'COLMÉIA','TO','colmeia'),(409,1717008,'PINDORAMA DO TOCANTINS','TO','pindorama-do-tocantins'),(410,1717206,'PIRAQUÊ','TO','piraque'),(411,1717503,'PIUM','TO','pium'),(412,1717800,'PONTE ALTA DO BOM JESUS','TO','ponte-alta-do-bom-jesus'),(413,1717909,'PONTE ALTA DO TOCANTINS','TO','ponte-alta-do-tocantins'),(414,1718006,'PORTO ALEGRE DO TOCANTINS','TO','porto-alegre-do-tocantins'),(415,1718204,'PORTO NACIONAL','TO','porto-nacional'),(416,1718303,'PRAIA NORTE','TO','praia-norte'),(417,1718402,'PRESIDENTE KENNEDY','TO','presidente-kennedy'),(418,1718451,'PUGMIL','TO','pugmil'),(419,1718501,'RECURSOLÂNDIA','TO','recursolandia'),(420,1718550,'RIACHINHO','TO','riachinho'),(421,1718659,'RIO DA CONCEIÇÃO','TO','rio-da-conceicao'),(422,1718709,'RIO DOS BOIS','TO','rio-dos-bois'),(423,1718758,'RIO SONO','TO','rio-sono'),(424,1718808,'SAMPAIO','TO','sampaio'),(425,1718840,'SANDOLÂNDIA','TO','sandolandia'),(426,1718865,'SANTA FÉ DO ARAGUAIA','TO','santa-fe-do-araguaia'),(427,1718881,'SANTA MARIA DO TOCANTINS','TO','santa-maria-do-tocantins'),(428,1718899,'SANTA RITA DO TOCANTINS','TO','santa-rita-do-tocantins'),(429,1718907,'SANTA ROSA DO TOCANTINS','TO','santa-rosa-do-tocantins'),(430,1719004,'SANTA TEREZA DO TOCANTINS','TO','santa-tereza-do-tocantins'),(431,1720002,'SANTA TEREZINHA DO TOCANTINS','TO','santa-terezinha-do-tocantins'),(432,1720101,'SÃO BENTO DO TOCANTINS','TO','sao-bento-do-tocantins'),(433,1720150,'SÃO FÉLIX DO TOCANTINS','TO','sao-felix-do-tocantins'),(434,1720200,'SÃO MIGUEL DO TOCANTINS','TO','sao-miguel-do-tocantins'),(435,1720259,'SÃO SALVADOR DO TOCANTINS','TO','sao-salvador-do-tocantins'),(436,1720309,'SÃO SEBASTIÃO DO TOCANTINS','TO','sao-sebastiao-do-tocantins'),(437,1720499,'SÃO VALÉRIO','TO','sao-valerio'),(438,1720655,'SILVANÓPOLIS','TO','silvanopolis'),(439,1720804,'SÍTIO NOVO DO TOCANTINS','TO','sitio-novo-do-tocantins'),(440,1720853,'SUCUPIRA','TO','sucupira'),(441,1720903,'TAGUATINGA','TO','taguatinga'),(442,1720937,'TAIPAS DO TOCANTINS','TO','taipas-do-tocantins'),(443,1720978,'TALISMÃ','TO','talisma'),(444,1721000,'PALMAS','TO','palmas'),(445,1721109,'TOCANTÍNIA','TO','tocantinia'),(446,1721208,'TOCANTINÓPOLIS','TO','tocantinopolis'),(447,1721257,'TUPIRAMA','TO','tupirama'),(448,1721307,'TUPIRATINS','TO','tupiratins'),(449,1722081,'WANDERLÂNDIA','TO','wanderlandia'),(450,1722107,'XAMBIOÁ','TO','xambioa'),(451,2100055,'AÇAILÂNDIA','MA','acailandia'),(452,2100105,'AFONSO CUNHA','MA','afonso-cunha'),(453,2100154,'ÁGUA DOCE DO MARANHÃO','MA','agua-doce-do-maranhao'),(454,2100204,'ALCÂNTARA','MA','alcantara'),(455,2100303,'ALDEIAS ALTAS','MA','aldeias-altas'),(456,2100402,'ALTAMIRA DO MARANHÃO','MA','altamira-do-maranhao'),(457,2100436,'ALTO ALEGRE DO MARANHÃO','MA','alto-alegre-do-maranhao'),(458,2100477,'ALTO ALEGRE DO PINDARÉ','MA','alto-alegre-do-pindare'),(459,2100501,'ALTO PARNAÍBA','MA','alto-parnaiba'),(460,2100550,'AMAPÁ DO MARANHÃO','MA','amapa-do-maranhao'),(461,2100600,'AMARANTE DO MARANHÃO','MA','amarante-do-maranhao'),(462,2100709,'ANAJATUBA','MA','anajatuba'),(463,2100808,'ANAPURUS','MA','anapurus'),(464,2100832,'APICUM-AÇU','MA','apicum-acu'),(465,2100873,'ARAGUANÃ','MA','araguana'),(466,2100907,'ARAIOSES','MA','araioses'),(467,2100956,'ARAME','MA','arame'),(468,2101004,'ARARI','MA','arari'),(469,2101103,'AXIXÁ','MA','axixa'),(470,2101202,'BACABAL','MA','bacabal'),(471,2101251,'BACABEIRA','MA','bacabeira'),(472,2101301,'BACURI','MA','bacuri'),(473,2101350,'BACURITUBA','MA','bacurituba'),(474,2101400,'BALSAS','MA','balsas'),(475,2101509,'BARÃO DE GRAJAÚ','MA','barao-de-grajau'),(476,2101608,'BARRA DO CORDA','MA','barra-do-corda'),(477,2101707,'BARREIRINHAS','MA','barreirinhas'),(478,2101731,'BELÁGUA','MA','belagua'),(479,2101772,'BELA VISTA DO MARANHÃO','MA','bela-vista-do-maranhao'),(480,2101806,'BENEDITO LEITE','MA','benedito-leite'),(481,2101905,'BEQUIMÃO','MA','bequimao'),(482,2101939,'BERNARDO DO MEARIM','MA','bernardo-do-mearim'),(483,2101970,'BOA VISTA DO GURUPI','MA','boa-vista-do-gurupi'),(484,2102002,'BOM JARDIM','MA','bom-jardim'),(485,2102036,'BOM JESUS DAS SELVAS','MA','bom-jesus-das-selvas'),(486,2102077,'BOM LUGAR','MA','bom-lugar'),(487,2102101,'BREJO','MA','brejo'),(488,2102150,'BREJO DE AREIA','MA','brejo-de-areia'),(489,2102200,'BURITI','MA','buriti'),(490,2102309,'BURITI BRAVO','MA','buriti-bravo'),(491,2102325,'BURITICUPU','MA','buriticupu'),(492,2102358,'BURITIRANA','MA','buritirana'),(493,2102374,'CACHOEIRA GRANDE','MA','cachoeira-grande'),(494,2102408,'CAJAPIÓ','MA','cajapio'),(495,2102507,'CAJARI','MA','cajari'),(496,2102556,'CAMPESTRE DO MARANHÃO','MA','campestre-do-maranhao'),(497,2102606,'CÂNDIDO MENDES','MA','candido-mendes'),(498,2102705,'CANTANHEDE','MA','cantanhede'),(499,2102754,'CAPINZAL DO NORTE','MA','capinzal-do-norte'),(500,2102804,'CAROLINA','MA','carolina'),(501,2102903,'CARUTAPERA','MA','carutapera'),(502,2103000,'CAXIAS','MA','caxias'),(503,2103109,'CEDRAL','MA','cedral'),(504,2103125,'CENTRAL DO MARANHÃO','MA','central-do-maranhao'),(505,2103158,'CENTRO DO GUILHERME','MA','centro-do-guilherme'),(506,2103174,'CENTRO NOVO DO MARANHÃO','MA','centro-novo-do-maranhao'),(507,2103208,'CHAPADINHA','MA','chapadinha'),(508,2103257,'CIDELÂNDIA','MA','cidelandia'),(509,2103307,'CODÓ','MA','codo'),(510,2103406,'COELHO NETO','MA','coelho-neto'),(511,2103505,'COLINAS','MA','colinas'),(512,2103554,'CONCEIÇÃO DO LAGO-AÇU','MA','conceicao-do-lago-acu'),(513,2103604,'COROATÁ','MA','coroata'),(514,2103703,'CURURUPU','MA','cururupu'),(515,2103752,'DAVINÓPOLIS','MA','davinopolis'),(516,2103802,'DOM PEDRO','MA','dom-pedro'),(517,2103901,'DUQUE BACELAR','MA','duque-bacelar'),(518,2104008,'ESPERANTINÓPOLIS','MA','esperantinopolis'),(519,2104057,'ESTREITO','MA','estreito'),(520,2104073,'FEIRA NOVA DO MARANHÃO','MA','feira-nova-do-maranhao'),(521,2104081,'FERNANDO FALCÃO','MA','fernando-falcao'),(522,2104099,'FORMOSA DA SERRA NEGRA','MA','formosa-da-serra-negra'),(523,2104107,'FORTALEZA DOS NOGUEIRAS','MA','fortaleza-dos-nogueiras'),(524,2104206,'FORTUNA','MA','fortuna'),(525,2104305,'GODOFREDO VIANA','MA','godofredo-viana'),(526,2104404,'GONÇALVES DIAS','MA','goncalves-dias'),(527,2104503,'GOVERNADOR ARCHER','MA','governador-archer'),(528,2104552,'GOVERNADOR EDISON LOBÃO','MA','governador-edison-lobao'),(529,2104602,'GOVERNADOR EUGÊNIO BARROS','MA','governador-eugenio-barros'),(530,2104628,'GOVERNADOR LUIZ ROCHA','MA','governador-luiz-rocha'),(531,2104651,'GOVERNADOR NEWTON BELLO','MA','governador-newton-bello'),(532,2104677,'GOVERNADOR NUNES FREIRE','MA','governador-nunes-freire'),(533,2104701,'GRAÇA ARANHA','MA','graca-aranha'),(534,2104800,'GRAJAÚ','MA','grajau'),(535,2104909,'GUIMARÃES','MA','guimaraes'),(536,2105005,'HUMBERTO DE CAMPOS','MA','humberto-de-campos'),(537,2105104,'ICATU','MA','icatu'),(538,2105153,'IGARAPÉ DO MEIO','MA','igarape-do-meio'),(539,2105203,'IGARAPÉ GRANDE','MA','igarape-grande'),(540,2105302,'IMPERATRIZ','MA','imperatriz'),(541,2105351,'ITAIPAVA DO GRAJAÚ','MA','itaipava-do-grajau'),(542,2105401,'ITAPECURU MIRIM','MA','itapecuru-mirim'),(543,2105427,'ITINGA DO MARANHÃO','MA','itinga-do-maranhao'),(544,2105450,'JATOBÁ','MA','jatoba'),(545,2105476,'JENIPAPO DOS VIEIRAS','MA','jenipapo-dos-vieiras'),(546,2105500,'JOÃO LISBOA','MA','joao-lisboa'),(547,2105609,'JOSELÂNDIA','MA','joselandia'),(548,2105658,'JUNCO DO MARANHÃO','MA','junco-do-maranhao'),(549,2105708,'LAGO DA PEDRA','MA','lago-da-pedra'),(550,2105807,'LAGO DO JUNCO','MA','lago-do-junco'),(551,2105906,'LAGO VERDE','MA','lago-verde'),(552,2105922,'LAGOA DO MATO','MA','lagoa-do-mato'),(553,2105948,'LAGO DOS RODRIGUES','MA','lago-dos-rodrigues'),(554,2105963,'LAGOA GRANDE DO MARANHÃO','MA','lagoa-grande-do-maranhao'),(555,2105989,'LAJEADO NOVO','MA','lajeado-novo'),(556,2106003,'LIMA CAMPOS','MA','lima-campos'),(557,2106102,'LORETO','MA','loreto'),(558,2106201,'LUÍS DOMINGUES','MA','luis-domingues'),(559,2106300,'MAGALHÃES DE ALMEIDA','MA','magalhaes-de-almeida'),(560,2106326,'MARACAÇUMÉ','MA','maracacume'),(561,2106359,'MARAJÁ DO SENA','MA','maraja-do-sena'),(562,2106375,'MARANHÃOZINHO','MA','maranhaozinho'),(563,2106409,'MATA ROMA','MA','mata-roma'),(564,2106508,'MATINHA','MA','matinha'),(565,2106607,'MATÕES','MA','matoes'),(566,2106631,'MATÕES DO NORTE','MA','matoes-do-norte'),(567,2106672,'MILAGRES DO MARANHÃO','MA','milagres-do-maranhao'),(568,2106706,'MIRADOR','MA','mirador'),(569,2106755,'MIRANDA DO NORTE','MA','miranda-do-norte'),(570,2106805,'MIRINZAL','MA','mirinzal'),(571,2106904,'MONÇÃO','MA','moncao'),(572,2107001,'MONTES ALTOS','MA','montes-altos'),(573,2107100,'MORROS','MA','morros'),(574,2107209,'NINA RODRIGUES','MA','nina-rodrigues'),(575,2107258,'NOVA COLINAS','MA','nova-colinas'),(576,2107308,'NOVA IORQUE','MA','nova-iorque'),(577,2107357,'NOVA OLINDA DO MARANHÃO','MA','nova-olinda-do-maranhao'),(578,2107407,'OLHO D\'ÁGUA DAS CUNHÃS','MA','olho-dagua-das-cunhas'),(579,2107456,'OLINDA NOVA DO MARANHÃO','MA','olinda-nova-do-maranhao'),(580,2107506,'PAÇO DO LUMIAR','MA','paco-do-lumiar'),(581,2107605,'PALMEIRÂNDIA','MA','palmeirandia'),(582,2107704,'PARAIBANO','MA','paraibano'),(583,2107803,'PARNARAMA','MA','parnarama'),(584,2107902,'PASSAGEM FRANCA','MA','passagem-franca'),(585,2108009,'PASTOS BONS','MA','pastos-bons'),(586,2108058,'PAULINO NEVES','MA','paulino-neves'),(587,2108108,'PAULO RAMOS','MA','paulo-ramos'),(588,2108207,'PEDREIRAS','MA','pedreiras'),(589,2108256,'PEDRO DO ROSÁRIO','MA','pedro-do-rosario'),(590,2108306,'PENALVA','MA','penalva'),(591,2108405,'PERI MIRIM','MA','peri-mirim'),(592,2108454,'PERITORÓ','MA','peritoro'),(593,2108504,'PINDARÉ-MIRIM','MA','pindare-mirim'),(594,2108603,'PINHEIRO','MA','pinheiro'),(595,2108702,'PIO XII','MA','pio-xii'),(596,2108801,'PIRAPEMAS','MA','pirapemas'),(597,2108900,'POÇÃO DE PEDRAS','MA','pocao-de-pedras'),(598,2109007,'PORTO FRANCO','MA','porto-franco'),(599,2109056,'PORTO RICO DO MARANHÃO','MA','porto-rico-do-maranhao'),(600,2109106,'PRESIDENTE DUTRA','MA','presidente-dutra'),(601,2109205,'PRESIDENTE JUSCELINO','MA','presidente-juscelino'),(602,2109239,'PRESIDENTE MÉDICI','MA','presidente-medici'),(603,2109270,'PRESIDENTE SARNEY','MA','presidente-sarney'),(604,2109304,'PRESIDENTE VARGAS','MA','presidente-vargas'),(605,2109403,'PRIMEIRA CRUZ','MA','primeira-cruz'),(606,2109452,'RAPOSA','MA','raposa'),(607,2109502,'RIACHÃO','MA','riachao'),(608,2109551,'RIBAMAR FIQUENE','MA','ribamar-fiquene'),(609,2109601,'ROSÁRIO','MA','rosario'),(610,2109700,'SAMBAÍBA','MA','sambaiba'),(611,2109759,'SANTA FILOMENA DO MARANHÃO','MA','santa-filomena-do-maranhao'),(612,2109809,'SANTA HELENA','MA','santa-helena'),(613,2109908,'SANTA INÊS','MA','santa-ines'),(614,2110005,'SANTA LUZIA','MA','santa-luzia'),(615,2110039,'SANTA LUZIA DO PARUÁ','MA','santa-luzia-do-parua'),(616,2110104,'SANTA QUITÉRIA DO MARANHÃO','MA','santa-quiteria-do-maranhao'),(617,2110203,'SANTA RITA','MA','santa-rita'),(618,2110237,'SANTANA DO MARANHÃO','MA','santana-do-maranhao'),(619,2110278,'SANTO AMARO DO MARANHÃO','MA','santo-amaro-do-maranhao'),(620,2110302,'SANTO ANTÔNIO DOS LOPES','MA','santo-antonio-dos-lopes'),(621,2110401,'SÃO BENEDITO DO RIO PRETO','MA','sao-benedito-do-rio-preto'),(622,2110500,'SÃO BENTO','MA','sao-bento'),(623,2110609,'SÃO BERNARDO','MA','sao-bernardo'),(624,2110658,'SÃO DOMINGOS DO AZEITÃO','MA','sao-domingos-do-azeitao'),(625,2110708,'SÃO DOMINGOS DO MARANHÃO','MA','sao-domingos-do-maranhao'),(626,2110807,'SÃO FÉLIX DE BALSAS','MA','sao-felix-de-balsas'),(627,2110856,'SÃO FRANCISCO DO BREJÃO','MA','sao-francisco-do-brejao'),(628,2110906,'SÃO FRANCISCO DO MARANHÃO','MA','sao-francisco-do-maranhao'),(629,2111003,'SÃO JOÃO BATISTA','MA','sao-joao-batista'),(630,2111029,'SÃO JOÃO DO CARÚ','MA','sao-joao-do-caru'),(631,2111052,'SÃO JOÃO DO PARAÍSO','MA','sao-joao-do-paraiso'),(632,2111078,'SÃO JOÃO DO SOTER','MA','sao-joao-do-soter'),(633,2111102,'SÃO JOÃO DOS PATOS','MA','sao-joao-dos-patos'),(634,2111201,'SÃO JOSÉ DE RIBAMAR','MA','sao-jose-de-ribamar'),(635,2111250,'SÃO JOSÉ DOS BASÍLIOS','MA','sao-jose-dos-basilios'),(636,2111300,'SÃO LUÍS','MA','sao-luis'),(637,2111409,'SÃO LUÍS GONZAGA DO MARANHÃO','MA','sao-luis-gonzaga-do-maranhao'),(638,2111508,'SÃO MATEUS DO MARANHÃO','MA','sao-mateus-do-maranhao'),(639,2111532,'SÃO PEDRO DA ÁGUA BRANCA','MA','sao-pedro-da-agua-branca'),(640,2111573,'SÃO PEDRO DOS CRENTES','MA','sao-pedro-dos-crentes'),(641,2111607,'SÃO RAIMUNDO DAS MANGABEIRAS','MA','sao-raimundo-das-mangabeiras'),(642,2111631,'SÃO RAIMUNDO DO DOCA BEZERRA','MA','sao-raimundo-do-doca-bezerra'),(643,2111672,'SÃO ROBERTO','MA','sao-roberto'),(644,2111706,'SÃO VICENTE FERRER','MA','sao-vicente-ferrer'),(645,2111722,'SATUBINHA','MA','satubinha'),(646,2111748,'SENADOR ALEXANDRE COSTA','MA','senador-alexandre-costa'),(647,2111763,'SENADOR LA ROCQUE','MA','senador-la-rocque'),(648,2111789,'SERRANO DO MARANHÃO','MA','serrano-do-maranhao'),(649,2111805,'SÍTIO NOVO','MA','sitio-novo'),(650,2111904,'SUCUPIRA DO NORTE','MA','sucupira-do-norte'),(651,2111953,'SUCUPIRA DO RIACHÃO','MA','sucupira-do-riachao'),(652,2112001,'TASSO FRAGOSO','MA','tasso-fragoso'),(653,2112100,'TIMBIRAS','MA','timbiras'),(654,2112209,'TIMON','MA','timon'),(655,2112233,'TRIZIDELA DO VALE','MA','trizidela-do-vale'),(656,2112274,'TUFILÂNDIA','MA','tufilandia'),(657,2112308,'TUNTUM','MA','tuntum'),(658,2112407,'TURIAÇU','MA','turiacu'),(659,2112456,'TURILÂNDIA','MA','turilandia'),(660,2112506,'TUTÓIA','MA','tutoia'),(661,2112605,'URBANO SANTOS','MA','urbano-santos'),(662,2112704,'VARGEM GRANDE','MA','vargem-grande'),(663,2112803,'VIANA','MA','viana'),(664,2112852,'VILA NOVA DOS MARTÍRIOS','MA','vila-nova-dos-martirios'),(665,2112902,'VITÓRIA DO MEARIM','MA','vitoria-do-mearim'),(666,2113009,'VITORINO FREIRE','MA','vitorino-freire'),(667,2114007,'ZÉ DOCA','MA','ze-doca'),(668,2200053,'ACAUÃ','PI','acaua'),(669,2200103,'AGRICOLÂNDIA','PI','agricolandia'),(670,2200202,'ÁGUA BRANCA','PI','agua-branca'),(671,2200251,'ALAGOINHA DO PIAUÍ','PI','alagoinha-do-piaui'),(672,2200277,'ALEGRETE DO PIAUÍ','PI','alegrete-do-piaui'),(673,2200301,'ALTO LONGÁ','PI','alto-longa'),(674,2200400,'ALTOS','PI','altos'),(675,2200459,'ALVORADA DO GURGUÉIA','PI','alvorada-do-gurgueia'),(676,2200509,'AMARANTE','PI','amarante'),(677,2200608,'ANGICAL DO PIAUÍ','PI','angical-do-piaui'),(678,2200707,'ANÍSIO DE ABREU','PI','anisio-de-abreu'),(679,2200806,'ANTÔNIO ALMEIDA','PI','antonio-almeida'),(680,2200905,'AROAZES','PI','aroazes'),(681,2200954,'AROEIRAS DO ITAIM','PI','aroeiras-do-itaim'),(682,2201002,'ARRAIAL','PI','arraial'),(683,2201051,'ASSUNÇÃO DO PIAUÍ','PI','assuncao-do-piaui'),(684,2201101,'AVELINO LOPES','PI','avelino-lopes'),(685,2201150,'BAIXA GRANDE DO RIBEIRO','PI','baixa-grande-do-ribeiro'),(686,2201176,'BARRA D\'ALCÂNTARA','PI','barra-dalcantara'),(687,2201200,'BARRAS','PI','barras'),(688,2201309,'BARREIRAS DO PIAUÍ','PI','barreiras-do-piaui'),(689,2201408,'BARRO DURO','PI','barro-duro'),(690,2201507,'BATALHA','PI','batalha'),(691,2201556,'BELA VISTA DO PIAUÍ','PI','bela-vista-do-piaui'),(692,2201572,'BELÉM DO PIAUÍ','PI','belem-do-piaui'),(693,2201606,'BENEDITINOS','PI','beneditinos'),(694,2201705,'BERTOLÍNIA','PI','bertolinia'),(695,2201739,'BETÂNIA DO PIAUÍ','PI','betania-do-piaui'),(696,2201770,'BOA HORA','PI','boa-hora'),(697,2201804,'BOCAINA','PI','bocaina'),(698,2201903,'BOM JESUS','PI','bom-jesus'),(699,2201919,'BOM PRINCÍPIO DO PIAUÍ','PI','bom-principio-do-piaui'),(700,2201929,'BONFIM DO PIAUÍ','PI','bonfim-do-piaui'),(701,2201945,'BOQUEIRÃO DO PIAUÍ','PI','boqueirao-do-piaui'),(702,2201960,'BRASILEIRA','PI','brasileira'),(703,2201988,'BREJO DO PIAUÍ','PI','brejo-do-piaui'),(704,2202000,'BURITI DOS LOPES','PI','buriti-dos-lopes'),(705,2202026,'BURITI DOS MONTES','PI','buriti-dos-montes'),(706,2202059,'CABECEIRAS DO PIAUÍ','PI','cabeceiras-do-piaui'),(707,2202075,'CAJAZEIRAS DO PIAUÍ','PI','cajazeiras-do-piaui'),(708,2202083,'CAJUEIRO DA PRAIA','PI','cajueiro-da-praia'),(709,2202091,'CALDEIRÃO GRANDE DO PIAUÍ','PI','caldeirao-grande-do-piaui'),(710,2202109,'CAMPINAS DO PIAUÍ','PI','campinas-do-piaui'),(711,2202117,'CAMPO ALEGRE DO FIDALGO','PI','campo-alegre-do-fidalgo'),(712,2202133,'CAMPO GRANDE DO PIAUÍ','PI','campo-grande-do-piaui'),(713,2202174,'CAMPO LARGO DO PIAUÍ','PI','campo-largo-do-piaui'),(714,2202208,'CAMPO MAIOR','PI','campo-maior'),(715,2202251,'CANAVIEIRA','PI','canavieira'),(716,2202307,'CANTO DO BURITI','PI','canto-do-buriti'),(717,2202406,'CAPITÃO DE CAMPOS','PI','capitao-de-campos'),(718,2202455,'CAPITÃO GERVÁSIO OLIVEIRA','PI','capitao-gervasio-oliveira'),(719,2202505,'CARACOL','PI','caracol'),(720,2202539,'CARAÚBAS DO PIAUÍ','PI','caraubas-do-piaui'),(721,2202554,'CARIDADE DO PIAUÍ','PI','caridade-do-piaui'),(722,2202604,'CASTELO DO PIAUÍ','PI','castelo-do-piaui'),(723,2202653,'CAXINGÓ','PI','caxingo'),(724,2202703,'COCAL','PI','cocal'),(725,2202711,'COCAL DE TELHA','PI','cocal-de-telha'),(726,2202729,'COCAL DOS ALVES','PI','cocal-dos-alves'),(727,2202737,'COIVARAS','PI','coivaras'),(728,2202752,'COLÔNIA DO GURGUÉIA','PI','colonia-do-gurgueia'),(729,2202778,'COLÔNIA DO PIAUÍ','PI','colonia-do-piaui'),(730,2202802,'CONCEIÇÃO DO CANINDÉ','PI','conceicao-do-caninde'),(731,2202851,'CORONEL JOSÉ DIAS','PI','coronel-jose-dias'),(732,2202901,'CORRENTE','PI','corrente'),(733,2203008,'CRISTALÂNDIA DO PIAUÍ','PI','cristalandia-do-piaui'),(734,2203107,'CRISTINO CASTRO','PI','cristino-castro'),(735,2203206,'CURIMATÁ','PI','curimata'),(736,2203230,'CURRAIS','PI','currais'),(737,2203255,'CURRALINHOS','PI','curralinhos'),(738,2203271,'CURRAL NOVO DO PIAUÍ','PI','curral-novo-do-piaui'),(739,2203305,'DEMERVAL LOBÃO','PI','demerval-lobao'),(740,2203354,'DIRCEU ARCOVERDE','PI','dirceu-arcoverde'),(741,2203404,'DOM EXPEDITO LOPES','PI','dom-expedito-lopes'),(742,2203420,'DOMINGOS MOURÃO','PI','domingos-mourao'),(743,2203453,'DOM INOCÊNCIO','PI','dom-inocencio'),(744,2203503,'ELESBÃO VELOSO','PI','elesbao-veloso'),(745,2203602,'ELISEU MARTINS','PI','eliseu-martins'),(746,2203701,'ESPERANTINA','PI','esperantina'),(747,2203750,'FARTURA DO PIAUÍ','PI','fartura-do-piaui'),(748,2203800,'FLORES DO PIAUÍ','PI','flores-do-piaui'),(749,2203859,'FLORESTA DO PIAUÍ','PI','floresta-do-piaui'),(750,2203909,'FLORIANO','PI','floriano'),(751,2204006,'FRANCINÓPOLIS','PI','francinopolis'),(752,2204105,'FRANCISCO AYRES','PI','francisco-ayres'),(753,2204154,'FRANCISCO MACEDO','PI','francisco-macedo'),(754,2204204,'FRANCISCO SANTOS','PI','francisco-santos'),(755,2204303,'FRONTEIRAS','PI','fronteiras'),(756,2204352,'GEMINIANO','PI','geminiano'),(757,2204402,'GILBUÉS','PI','gilbues'),(758,2204501,'GUADALUPE','PI','guadalupe'),(759,2204550,'GUARIBAS','PI','guaribas'),(760,2204600,'HUGO NAPOLEÃO','PI','hugo-napoleao'),(761,2204659,'ILHA GRANDE','PI','ilha-grande'),(762,2204709,'INHUMA','PI','inhuma'),(763,2204808,'IPIRANGA DO PIAUÍ','PI','ipiranga-do-piaui'),(764,2204907,'ISAÍAS COELHO','PI','isaias-coelho'),(765,2205003,'ITAINÓPOLIS','PI','itainopolis'),(766,2205102,'ITAUEIRA','PI','itaueira'),(767,2205151,'JACOBINA DO PIAUÍ','PI','jacobina-do-piaui'),(768,2205201,'JAICÓS','PI','jaicos'),(769,2205250,'JARDIM DO MULATO','PI','jardim-do-mulato'),(770,2205276,'JATOBÁ DO PIAUÍ','PI','jatoba-do-piaui'),(771,2205300,'JERUMENHA','PI','jerumenha'),(772,2205359,'JOÃO COSTA','PI','joao-costa'),(773,2205409,'JOAQUIM PIRES','PI','joaquim-pires'),(774,2205458,'JOCA MARQUES','PI','joca-marques'),(775,2205508,'JOSÉ DE FREITAS','PI','jose-de-freitas'),(776,2205516,'JUAZEIRO DO PIAUÍ','PI','juazeiro-do-piaui'),(777,2205524,'JÚLIO BORGES','PI','julio-borges'),(778,2205532,'JUREMA','PI','jurema'),(779,2205540,'LAGOINHA DO PIAUÍ','PI','lagoinha-do-piaui'),(780,2205557,'LAGOA ALEGRE','PI','lagoa-alegre'),(781,2205565,'LAGOA DO BARRO DO PIAUÍ','PI','lagoa-do-barro-do-piaui'),(782,2205573,'LAGOA DE SÃO FRANCISCO','PI','lagoa-de-sao-francisco'),(783,2205581,'LAGOA DO PIAUÍ','PI','lagoa-do-piaui'),(784,2205599,'LAGOA DO SÍTIO','PI','lagoa-do-sitio'),(785,2205607,'LANDRI SALES','PI','landri-sales'),(786,2205706,'LUÍS CORREIA','PI','luis-correia'),(787,2205805,'LUZILÂNDIA','PI','luzilandia'),(788,2205854,'MADEIRO','PI','madeiro'),(789,2205904,'MANOEL EMÍDIO','PI','manoel-emidio'),(790,2205953,'MARCOLÂNDIA','PI','marcolandia'),(791,2206001,'MARCOS PARENTE','PI','marcos-parente'),(792,2206050,'MASSAPÊ DO PIAUÍ','PI','massape-do-piaui'),(793,2206100,'MATIAS OLÍMPIO','PI','matias-olimpio'),(794,2206209,'MIGUEL ALVES','PI','miguel-alves'),(795,2206308,'MIGUEL LEÃO','PI','miguel-leao'),(796,2206357,'MILTON BRANDÃO','PI','milton-brandao'),(797,2206407,'MONSENHOR GIL','PI','monsenhor-gil'),(798,2206506,'MONSENHOR HIPÓLITO','PI','monsenhor-hipolito'),(799,2206605,'MONTE ALEGRE DO PIAUÍ','PI','monte-alegre-do-piaui'),(800,2206654,'MORRO CABEÇA NO TEMPO','PI','morro-cabeca-no-tempo'),(801,2206670,'MORRO DO CHAPÉU DO PIAUÍ','PI','morro-do-chapeu-do-piaui'),(802,2206696,'MURICI DOS PORTELAS','PI','murici-dos-portelas'),(803,2206704,'NAZARÉ DO PIAUÍ','PI','nazare-do-piaui'),(804,2206720,'NAZÁRIA','PI','nazaria'),(805,2206753,'NOSSA SENHORA DE NAZARÉ','PI','nossa-senhora-de-nazare'),(806,2206803,'NOSSA SENHORA DOS REMÉDIOS','PI','nossa-senhora-dos-remedios'),(807,2206902,'NOVO ORIENTE DO PIAUÍ','PI','novo-oriente-do-piaui'),(808,2206951,'NOVO SANTO ANTÔNIO','PI','novo-santo-antonio'),(809,2207009,'OEIRAS','PI','oeiras'),(810,2207108,'OLHO D\'ÁGUA DO PIAUÍ','PI','olho-dagua-do-piaui'),(811,2207207,'PADRE MARCOS','PI','padre-marcos'),(812,2207306,'PAES LANDIM','PI','paes-landim'),(813,2207355,'PAJEÚ DO PIAUÍ','PI','pajeu-do-piaui'),(814,2207405,'PALMEIRA DO PIAUÍ','PI','palmeira-do-piaui'),(815,2207504,'PALMEIRAIS','PI','palmeirais'),(816,2207553,'PAQUETÁ','PI','paqueta'),(817,2207603,'PARNAGUÁ','PI','parnagua'),(818,2207702,'PARNAÍBA','PI','parnaiba'),(819,2207751,'PASSAGEM FRANCA DO PIAUÍ','PI','passagem-franca-do-piaui'),(820,2207777,'PATOS DO PIAUÍ','PI','patos-do-piaui'),(821,2207793,'PAU D\'ARCO DO PIAUÍ','PI','pau-darco-do-piaui'),(822,2207801,'PAULISTANA','PI','paulistana'),(823,2207850,'PAVUSSU','PI','pavussu'),(824,2207900,'PEDRO II','PI','pedro-ii'),(825,2207934,'PEDRO LAURENTINO','PI','pedro-laurentino'),(826,2207959,'NOVA SANTA RITA','PI','nova-santa-rita'),(827,2208007,'PICOS','PI','picos'),(828,2208106,'PIMENTEIRAS','PI','pimenteiras'),(829,2208205,'PIO IX','PI','pio-ix'),(830,2208304,'PIRACURUCA','PI','piracuruca'),(831,2208403,'PIRIPIRI','PI','piripiri'),(832,2208502,'PORTO','PI','porto'),(833,2208551,'PORTO ALEGRE DO PIAUÍ','PI','porto-alegre-do-piaui'),(834,2208601,'PRATA DO PIAUÍ','PI','prata-do-piaui'),(835,2208650,'QUEIMADA NOVA','PI','queimada-nova'),(836,2208700,'REDENÇÃO DO GURGUÉIA','PI','redencao-do-gurgueia'),(837,2208809,'REGENERAÇÃO','PI','regeneracao'),(838,2208858,'RIACHO FRIO','PI','riacho-frio'),(839,2208874,'RIBEIRA DO PIAUÍ','PI','ribeira-do-piaui'),(840,2208908,'RIBEIRO GONÇALVES','PI','ribeiro-goncalves'),(841,2209005,'RIO GRANDE DO PIAUÍ','PI','rio-grande-do-piaui'),(842,2209104,'SANTA CRUZ DO PIAUÍ','PI','santa-cruz-do-piaui'),(843,2209153,'SANTA CRUZ DOS MILAGRES','PI','santa-cruz-dos-milagres'),(844,2209203,'SANTA FILOMENA','PI','santa-filomena'),(845,2209302,'SANTA LUZ','PI','santa-luz'),(846,2209351,'SANTANA DO PIAUÍ','PI','santana-do-piaui'),(847,2209377,'SANTA ROSA DO PIAUÍ','PI','santa-rosa-do-piaui'),(848,2209401,'SANTO ANTÔNIO DE LISBOA','PI','santo-antonio-de-lisboa'),(849,2209450,'SANTO ANTÔNIO DOS MILAGRES','PI','santo-antonio-dos-milagres'),(850,2209500,'SANTO INÁCIO DO PIAUÍ','PI','santo-inacio-do-piaui'),(851,2209559,'SÃO BRAZ DO PIAUÍ','PI','sao-braz-do-piaui'),(852,2209609,'SÃO FÉLIX DO PIAUÍ','PI','sao-felix-do-piaui'),(853,2209658,'SÃO FRANCISCO DE ASSIS DO PIAUÍ','PI','sao-francisco-de-assis-do-piaui'),(854,2209708,'SÃO FRANCISCO DO PIAUÍ','PI','sao-francisco-do-piaui'),(855,2209757,'SÃO GONÇALO DO GURGUÉIA','PI','sao-goncalo-do-gurgueia'),(856,2209807,'SÃO GONÇALO DO PIAUÍ','PI','sao-goncalo-do-piaui'),(857,2209856,'SÃO JOÃO DA CANABRAVA','PI','sao-joao-da-canabrava'),(858,2209872,'SÃO JOÃO DA FRONTEIRA','PI','sao-joao-da-fronteira'),(859,2209906,'SÃO JOÃO DA SERRA','PI','sao-joao-da-serra'),(860,2209955,'SÃO JOÃO DA VARJOTA','PI','sao-joao-da-varjota'),(861,2209971,'SÃO JOÃO DO ARRAIAL','PI','sao-joao-do-arraial'),(862,2210003,'SÃO JOÃO DO PIAUÍ','PI','sao-joao-do-piaui'),(863,2210052,'SÃO JOSÉ DO DIVINO','PI','sao-jose-do-divino'),(864,2210102,'SÃO JOSÉ DO PEIXE','PI','sao-jose-do-peixe'),(865,2210201,'SÃO JOSÉ DO PIAUÍ','PI','sao-jose-do-piaui'),(866,2210300,'SÃO JULIÃO','PI','sao-juliao'),(867,2210359,'SÃO LOURENÇO DO PIAUÍ','PI','sao-lourenco-do-piaui'),(868,2210375,'SÃO LUIS DO PIAUÍ','PI','sao-luis-do-piaui'),(869,2210383,'SÃO MIGUEL DA BAIXA GRANDE','PI','sao-miguel-da-baixa-grande'),(870,2210391,'SÃO MIGUEL DO FIDALGO','PI','sao-miguel-do-fidalgo'),(871,2210409,'SÃO MIGUEL DO TAPUIO','PI','sao-miguel-do-tapuio'),(872,2210508,'SÃO PEDRO DO PIAUÍ','PI','sao-pedro-do-piaui'),(873,2210607,'SÃO RAIMUNDO NONATO','PI','sao-raimundo-nonato'),(874,2210623,'SEBASTIÃO BARROS','PI','sebastiao-barros'),(875,2210631,'SEBASTIÃO LEAL','PI','sebastiao-leal'),(876,2210656,'SIGEFREDO PACHECO','PI','sigefredo-pacheco'),(877,2210706,'SIMÕES','PI','simoes'),(878,2210805,'SIMPLÍCIO MENDES','PI','simplicio-mendes'),(879,2210904,'SOCORRO DO PIAUÍ','PI','socorro-do-piaui'),(880,2210938,'SUSSUAPARA','PI','sussuapara'),(881,2210953,'TAMBORIL DO PIAUÍ','PI','tamboril-do-piaui'),(882,2210979,'TANQUE DO PIAUÍ','PI','tanque-do-piaui'),(883,2211001,'TERESINA','PI','teresina'),(884,2211100,'UNIÃO','PI','uniao'),(885,2211209,'URUÇUÍ','PI','urucui'),(886,2211308,'VALENÇA DO PIAUÍ','PI','valenca-do-piaui'),(887,2211357,'VÁRZEA BRANCA','PI','varzea-branca'),(888,2211407,'VÁRZEA GRANDE','PI','varzea-grande'),(889,2211506,'VERA MENDES','PI','vera-mendes'),(890,2211605,'VILA NOVA DO PIAUÍ','PI','vila-nova-do-piaui'),(891,2211704,'WALL FERRAZ','PI','wall-ferraz'),(892,2300101,'ABAIARA','CE','abaiara'),(893,2300150,'ACARAPE','CE','acarape'),(894,2300200,'ACARAÚ','CE','acarau'),(895,2300309,'ACOPIARA','CE','acopiara'),(896,2300408,'AIUABA','CE','aiuaba'),(897,2300507,'ALCÂNTARAS','CE','alcantaras'),(898,2300606,'ALTANEIRA','CE','altaneira'),(899,2300705,'ALTO SANTO','CE','alto-santo'),(900,2300754,'AMONTADA','CE','amontada'),(901,2300804,'ANTONINA DO NORTE','CE','antonina-do-norte'),(902,2300903,'APUIARÉS','CE','apuiares'),(903,2301000,'AQUIRAZ','CE','aquiraz'),(904,2301109,'ARACATI','CE','aracati'),(905,2301208,'ARACOIABA','CE','aracoiaba'),(906,2301257,'ARARENDÁ','CE','ararenda'),(907,2301307,'ARARIPE','CE','araripe'),(908,2301406,'ARATUBA','CE','aratuba'),(909,2301505,'ARNEIROZ','CE','arneiroz'),(910,2301604,'ASSARÉ','CE','assare'),(911,2301703,'AURORA','CE','aurora'),(912,2301802,'BAIXIO','CE','baixio'),(913,2301851,'BANABUIÚ','CE','banabuiu'),(914,2301901,'BARBALHA','CE','barbalha'),(915,2301950,'BARREIRA','CE','barreira'),(916,2302008,'BARRO','CE','barro'),(917,2302057,'BARROQUINHA','CE','barroquinha'),(918,2302107,'BATURITÉ','CE','baturite'),(919,2302206,'BEBERIBE','CE','beberibe'),(920,2302305,'BELA CRUZ','CE','bela-cruz'),(921,2302404,'BOA VIAGEM','CE','boa-viagem'),(922,2302503,'BREJO SANTO','CE','brejo-santo'),(923,2302602,'CAMOCIM','CE','camocim'),(924,2302701,'CAMPOS SALES','CE','campos-sales'),(925,2302800,'CANINDÉ','CE','caninde'),(926,2302909,'CAPISTRANO','CE','capistrano'),(927,2303006,'CARIDADE','CE','caridade'),(928,2303105,'CARIRÉ','CE','carire'),(929,2303204,'CARIRIAÇU','CE','caririacu'),(930,2303303,'CARIÚS','CE','carius'),(931,2303402,'CARNAUBAL','CE','carnaubal'),(932,2303501,'CASCAVEL','CE','cascavel'),(933,2303600,'CATARINA','CE','catarina'),(934,2303659,'CATUNDA','CE','catunda'),(935,2303709,'CAUCAIA','CE','caucaia'),(936,2303808,'CEDRO','CE','cedro'),(937,2303907,'CHAVAL','CE','chaval'),(938,2303931,'CHORÓ','CE','choro'),(939,2303956,'CHOROZINHO','CE','chorozinho'),(940,2304004,'COREAÚ','CE','coreau'),(941,2304103,'CRATEÚS','CE','crateus'),(942,2304202,'CRATO','CE','crato'),(943,2304236,'CROATÁ','CE','croata'),(944,2304251,'CRUZ','CE','cruz'),(945,2304269,'DEPUTADO IRAPUAN PINHEIRO','CE','deputado-irapuan-pinheiro'),(946,2304277,'ERERÊ','CE','erere'),(947,2304285,'EUSÉBIO','CE','eusebio'),(948,2304301,'FARIAS BRITO','CE','farias-brito'),(949,2304350,'FORQUILHA','CE','forquilha'),(950,2304400,'FORTALEZA','CE','fortaleza'),(951,2304459,'FORTIM','CE','fortim'),(952,2304509,'FRECHEIRINHA','CE','frecheirinha'),(953,2304608,'GENERAL SAMPAIO','CE','general-sampaio'),(954,2304657,'GRAÇA','CE','graca'),(955,2304707,'GRANJA','CE','granja'),(956,2304806,'GRANJEIRO','CE','granjeiro'),(957,2304905,'GROAÍRAS','CE','groairas'),(958,2304954,'GUAIÚBA','CE','guaiuba'),(959,2305001,'GUARACIABA DO NORTE','CE','guaraciaba-do-norte'),(960,2305100,'GUARAMIRANGA','CE','guaramiranga'),(961,2305209,'HIDROLÂNDIA','CE','hidrolandia'),(962,2305233,'HORIZONTE','CE','horizonte'),(963,2305266,'IBARETAMA','CE','ibaretama'),(964,2305308,'IBIAPINA','CE','ibiapina'),(965,2305332,'IBICUITINGA','CE','ibicuitinga'),(966,2305357,'ICAPUÍ','CE','icapui'),(967,2305407,'ICÓ','CE','ico'),(968,2305506,'IGUATU','CE','iguatu'),(969,2305605,'INDEPENDÊNCIA','CE','independencia'),(970,2305654,'IPAPORANGA','CE','ipaporanga'),(971,2305704,'IPAUMIRIM','CE','ipaumirim'),(972,2305803,'IPU','CE','ipu'),(973,2305902,'IPUEIRAS','CE','ipueiras'),(974,2306009,'IRACEMA','CE','iracema'),(975,2306108,'IRAUÇUBA','CE','iraucuba'),(976,2306207,'ITAIÇABA','CE','itaicaba'),(977,2306256,'ITAITINGA','CE','itaitinga'),(978,2306306,'ITAPAGÉ','CE','itapage'),(979,2306405,'ITAPIPOCA','CE','itapipoca'),(980,2306504,'ITAPIÚNA','CE','itapiuna'),(981,2306553,'ITAREMA','CE','itarema'),(982,2306603,'ITATIRA','CE','itatira'),(983,2306702,'JAGUARETAMA','CE','jaguaretama'),(984,2306801,'JAGUARIBARA','CE','jaguaribara'),(985,2306900,'JAGUARIBE','CE','jaguaribe'),(986,2307007,'JAGUARUANA','CE','jaguaruana'),(987,2307106,'JARDIM','CE','jardim'),(988,2307205,'JATI','CE','jati'),(989,2307254,'JIJOCA DE JERICOACOARA','CE','jijoca-de-jericoacoara'),(990,2307304,'JUAZEIRO DO NORTE','CE','juazeiro-do-norte'),(991,2307403,'JUCÁS','CE','jucas'),(992,2307502,'LAVRAS DA MANGABEIRA','CE','lavras-da-mangabeira'),(993,2307601,'LIMOEIRO DO NORTE','CE','limoeiro-do-norte'),(994,2307635,'MADALENA','CE','madalena'),(995,2307650,'MARACANAÚ','CE','maracanau'),(996,2307700,'MARANGUAPE','CE','maranguape'),(997,2307809,'MARCO','CE','marco'),(998,2307908,'MARTINÓPOLE','CE','martinopole'),(999,2308005,'MASSAPÊ','CE','massape'),(1000,2308104,'MAURITI','CE','mauriti'),(1001,2308203,'MERUOCA','CE','meruoca'),(1002,2308302,'MILAGRES','CE','milagres'),(1003,2308351,'MILHÃ','CE','milha'),(1004,2308377,'MIRAÍMA','CE','miraima'),(1005,2308401,'MISSÃO VELHA','CE','missao-velha'),(1006,2308500,'MOMBAÇA','CE','mombaca'),(1007,2308609,'MONSENHOR TABOSA','CE','monsenhor-tabosa'),(1008,2308708,'MORADA NOVA','CE','morada-nova'),(1009,2308807,'MORAÚJO','CE','moraujo'),(1010,2308906,'MORRINHOS','CE','morrinhos'),(1011,2309003,'MUCAMBO','CE','mucambo'),(1012,2309102,'MULUNGU','CE','mulungu'),(1013,2309201,'NOVA OLINDA','CE','nova-olinda'),(1014,2309300,'NOVA RUSSAS','CE','nova-russas'),(1015,2309409,'NOVO ORIENTE','CE','novo-oriente'),(1016,2309458,'OCARA','CE','ocara'),(1017,2309508,'ORÓS','CE','oros'),(1018,2309607,'PACAJUS','CE','pacajus'),(1019,2309706,'PACATUBA','CE','pacatuba'),(1020,2309805,'PACOTI','CE','pacoti'),(1021,2309904,'PACUJÁ','CE','pacuja'),(1022,2310001,'PALHANO','CE','palhano'),(1023,2310100,'PALMÁCIA','CE','palmacia'),(1024,2310209,'PARACURU','CE','paracuru'),(1025,2310258,'PARAIPABA','CE','paraipaba'),(1026,2310308,'PARAMBU','CE','parambu'),(1027,2310407,'PARAMOTI','CE','paramoti'),(1028,2310506,'PEDRA BRANCA','CE','pedra-branca'),(1029,2310605,'PENAFORTE','CE','penaforte'),(1030,2310704,'PENTECOSTE','CE','pentecoste'),(1031,2310803,'PEREIRO','CE','pereiro'),(1032,2310852,'PINDORETAMA','CE','pindoretama'),(1033,2310902,'PIQUET CARNEIRO','CE','piquet-carneiro'),(1034,2310951,'PIRES FERREIRA','CE','pires-ferreira'),(1035,2311009,'PORANGA','CE','poranga'),(1036,2311108,'PORTEIRAS','CE','porteiras'),(1037,2311207,'POTENGI','CE','potengi'),(1038,2311231,'POTIRETAMA','CE','potiretama'),(1039,2311264,'QUITERIANÓPOLIS','CE','quiterianopolis'),(1040,2311306,'QUIXADÁ','CE','quixada'),(1041,2311355,'QUIXELÔ','CE','quixelo'),(1042,2311405,'QUIXERAMOBIM','CE','quixeramobim'),(1043,2311504,'QUIXERÉ','CE','quixere'),(1044,2311603,'REDENÇÃO','CE','redencao'),(1045,2311702,'RERIUTABA','CE','reriutaba'),(1046,2311801,'RUSSAS','CE','russas'),(1047,2311900,'SABOEIRO','CE','saboeiro'),(1048,2311959,'SALITRE','CE','salitre'),(1049,2312007,'SANTANA DO ACARAÚ','CE','santana-do-acarau'),(1050,2312106,'SANTANA DO CARIRI','CE','santana-do-cariri'),(1051,2312205,'SANTA QUITÉRIA','CE','santa-quiteria'),(1052,2312304,'SÃO BENEDITO','CE','sao-benedito'),(1053,2312403,'SÃO GONÇALO DO AMARANTE','CE','sao-goncalo-do-amarante'),(1054,2312502,'SÃO JOÃO DO JAGUARIBE','CE','sao-joao-do-jaguaribe'),(1055,2312601,'SÃO LUÍS DO CURU','CE','sao-luis-do-curu'),(1056,2312700,'SENADOR POMPEU','CE','senador-pompeu'),(1057,2312809,'SENADOR SÁ','CE','senador-sa'),(1058,2312908,'SOBRAL','CE','sobral'),(1059,2313005,'SOLONÓPOLE','CE','solonopole'),(1060,2313104,'TABULEIRO DO NORTE','CE','tabuleiro-do-norte'),(1061,2313203,'TAMBORIL','CE','tamboril'),(1062,2313252,'TARRAFAS','CE','tarrafas'),(1063,2313302,'TAUÁ','CE','taua'),(1064,2313351,'TEJUÇUOCA','CE','tejucuoca'),(1065,2313401,'TIANGUÁ','CE','tiangua'),(1066,2313500,'TRAIRI','CE','trairi'),(1067,2313559,'TURURU','CE','tururu'),(1068,2313609,'UBAJARA','CE','ubajara'),(1069,2313708,'UMARI','CE','umari'),(1070,2313757,'UMIRIM','CE','umirim'),(1071,2313807,'URUBURETAMA','CE','uruburetama'),(1072,2313906,'URUOCA','CE','uruoca'),(1073,2313955,'VARJOTA','CE','varjota'),(1074,2314003,'VÁRZEA ALEGRE','CE','varzea-alegre'),(1075,2314102,'VIÇOSA DO CEARÁ','CE','vicosa-do-ceara'),(1076,2400109,'ACARI','RN','acari'),(1077,2400208,'AÇU','RN','acu'),(1078,2400307,'AFONSO BEZERRA','RN','afonso-bezerra'),(1079,2400406,'ÁGUA NOVA','RN','agua-nova'),(1080,2400505,'ALEXANDRIA','RN','alexandria'),(1081,2400604,'ALMINO AFONSO','RN','almino-afonso'),(1082,2400703,'ALTO DO RODRIGUES','RN','alto-do-rodrigues'),(1083,2400802,'ANGICOS','RN','angicos'),(1084,2400901,'ANTÔNIO MARTINS','RN','antonio-martins'),(1085,2401008,'APODI','RN','apodi'),(1086,2401107,'AREIA BRANCA','RN','areia-branca'),(1087,2401206,'ARÊS','RN','ares'),(1088,2401305,'AUGUSTO SEVERO','RN','augusto-severo'),(1089,2401404,'BAÍA FORMOSA','RN','baia-formosa'),(1090,2401453,'BARAÚNA','RN','barauna'),(1091,2401503,'BARCELONA','RN','barcelona'),(1092,2401602,'BENTO FERNANDES','RN','bento-fernandes'),(1093,2401651,'BODÓ','RN','bodo'),(1094,2401701,'BOM JESUS','RN','bom-jesus'),(1095,2401800,'BREJINHO','RN','brejinho'),(1096,2401859,'CAIÇARA DO NORTE','RN','caicara-do-norte'),(1097,2401909,'CAIÇARA DO RIO DO VENTO','RN','caicara-do-rio-do-vento'),(1098,2402006,'CAICÓ','RN','caico'),(1099,2402105,'CAMPO REDONDO','RN','campo-redondo'),(1100,2402204,'CANGUARETAMA','RN','canguaretama'),(1101,2402303,'CARAÚBAS','RN','caraubas'),(1102,2402402,'CARNAÚBA DOS DANTAS','RN','carnauba-dos-dantas'),(1103,2402501,'CARNAUBAIS','RN','carnaubais'),(1104,2402600,'CEARÁ-MIRIM','RN','ceara-mirim'),(1105,2402709,'CERRO CORÁ','RN','cerro-cora'),(1106,2402808,'CORONEL EZEQUIEL','RN','coronel-ezequiel'),(1107,2402907,'CORONEL JOÃO PESSOA','RN','coronel-joao-pessoa'),(1108,2403004,'CRUZETA','RN','cruzeta'),(1109,2403103,'CURRAIS NOVOS','RN','currais-novos'),(1110,2403202,'DOUTOR SEVERIANO','RN','doutor-severiano'),(1111,2403251,'PARNAMIRIM','RN','parnamirim'),(1112,2403301,'ENCANTO','RN','encanto'),(1113,2403400,'EQUADOR','RN','equador'),(1114,2403509,'ESPÍRITO SANTO','RN','espirito-santo'),(1115,2403608,'EXTREMOZ','RN','extremoz'),(1116,2403707,'FELIPE GUERRA','RN','felipe-guerra'),(1117,2403756,'FERNANDO PEDROZA','RN','fernando-pedroza'),(1118,2403806,'FLORÂNIA','RN','florania'),(1119,2403905,'FRANCISCO DANTAS','RN','francisco-dantas'),(1120,2404002,'FRUTUOSO GOMES','RN','frutuoso-gomes'),(1121,2404101,'GALINHOS','RN','galinhos'),(1122,2404200,'GOIANINHA','RN','goianinha'),(1123,2404309,'GOVERNADOR DIX-SEPT ROSADO','RN','governador-dix-sept-rosado'),(1124,2404408,'GROSSOS','RN','grossos'),(1125,2404507,'GUAMARÉ','RN','guamare'),(1126,2404606,'IELMO MARINHO','RN','ielmo-marinho'),(1127,2404705,'IPANGUAÇU','RN','ipanguacu'),(1128,2404804,'IPUEIRA','RN','ipueira'),(1129,2404853,'ITAJÁ','RN','itaja'),(1130,2404903,'ITAÚ','RN','itau'),(1131,2405009,'JAÇANÃ','RN','jacana'),(1132,2405108,'JANDAÍRA','RN','jandaira'),(1133,2405207,'JANDUÍS','RN','janduis'),(1134,2405306,'JANUÁRIO CICCO','RN','januario-cicco'),(1135,2405405,'JAPI','RN','japi'),(1136,2405504,'JARDIM DE ANGICOS','RN','jardim-de-angicos'),(1137,2405603,'JARDIM DE PIRANHAS','RN','jardim-de-piranhas'),(1138,2405702,'JARDIM DO SERIDÓ','RN','jardim-do-serido'),(1139,2405801,'JOÃO CÂMARA','RN','joao-camara'),(1140,2405900,'JOÃO DIAS','RN','joao-dias'),(1141,2406007,'JOSÉ DA PENHA','RN','jose-da-penha'),(1142,2406106,'JUCURUTU','RN','jucurutu'),(1143,2406155,'JUNDIÁ','RN','jundia'),(1144,2406205,'LAGOA D\'ANTA','RN','lagoa-danta'),(1145,2406304,'LAGOA DE PEDRAS','RN','lagoa-de-pedras'),(1146,2406403,'LAGOA DE VELHOS','RN','lagoa-de-velhos'),(1147,2406502,'LAGOA NOVA','RN','lagoa-nova'),(1148,2406601,'LAGOA SALGADA','RN','lagoa-salgada'),(1149,2406700,'LAJES','RN','lajes'),(1150,2406809,'LAJES PINTADAS','RN','lajes-pintadas'),(1151,2406908,'LUCRÉCIA','RN','lucrecia'),(1152,2407005,'LUÍS GOMES','RN','luis-gomes'),(1153,2407104,'MACAÍBA','RN','macaiba'),(1154,2407203,'MACAU','RN','macau'),(1155,2407252,'MAJOR SALES','RN','major-sales'),(1156,2407302,'MARCELINO VIEIRA','RN','marcelino-vieira'),(1157,2407401,'MARTINS','RN','martins'),(1158,2407500,'MAXARANGUAPE','RN','maxaranguape'),(1159,2407609,'MESSIAS TARGINO','RN','messias-targino'),(1160,2407708,'MONTANHAS','RN','montanhas'),(1161,2407807,'MONTE ALEGRE','RN','monte-alegre'),(1162,2407906,'MONTE DAS GAMELEIRAS','RN','monte-das-gameleiras'),(1163,2408003,'MOSSORÓ','RN','mossoro'),(1164,2408102,'NATAL','RN','natal'),(1165,2408201,'NÍSIA FLORESTA','RN','nisia-floresta'),(1166,2408300,'NOVA CRUZ','RN','nova-cruz'),(1167,2408409,'OLHO-D\'ÁGUA DO BORGES','RN','olho-dagua-do-borges'),(1168,2408508,'OURO BRANCO','RN','ouro-branco'),(1169,2408607,'PARANÁ','RN','parana'),(1170,2408706,'PARAÚ','RN','parau'),(1171,2408805,'PARAZINHO','RN','parazinho'),(1172,2408904,'PARELHAS','RN','parelhas'),(1173,2408953,'RIO DO FOGO','RN','rio-do-fogo'),(1174,2409100,'PASSA E FICA','RN','passa-e-fica'),(1175,2409209,'PASSAGEM','RN','passagem'),(1176,2409308,'PATU','RN','patu'),(1177,2409332,'SANTA MARIA','RN','santa-maria'),(1178,2409407,'PAU DOS FERROS','RN','pau-dos-ferros'),(1179,2409506,'PEDRA GRANDE','RN','pedra-grande'),(1180,2409605,'PEDRA PRETA','RN','pedra-preta'),(1181,2409704,'PEDRO AVELINO','RN','pedro-avelino'),(1182,2409803,'PEDRO VELHO','RN','pedro-velho'),(1183,2409902,'PENDÊNCIAS','RN','pendencias'),(1184,2410009,'PILÕES','RN','piloes'),(1185,2410108,'POÇO BRANCO','RN','poco-branco'),(1186,2410207,'PORTALEGRE','RN','portalegre'),(1187,2410256,'PORTO DO MANGUE','RN','porto-do-mangue'),(1188,2410306,'PRESIDENTE JUSCELINO','RN','presidente-juscelino'),(1189,2410405,'PUREZA','RN','pureza'),(1190,2410504,'RAFAEL FERNANDES','RN','rafael-fernandes'),(1191,2410603,'RAFAEL GODEIRO','RN','rafael-godeiro'),(1192,2410702,'RIACHO DA CRUZ','RN','riacho-da-cruz'),(1193,2410801,'RIACHO DE SANTANA','RN','riacho-de-santana'),(1194,2410900,'RIACHUELO','RN','riachuelo'),(1195,2411007,'RODOLFO FERNANDES','RN','rodolfo-fernandes'),(1196,2411056,'TIBAU','RN','tibau'),(1197,2411106,'RUY BARBOSA','RN','ruy-barbosa'),(1198,2411205,'SANTA CRUZ','RN','santa-cruz'),(1199,2411403,'SANTANA DO MATOS','RN','santana-do-matos'),(1200,2411429,'SANTANA DO SERIDÓ','RN','santana-do-serido'),(1201,2411502,'SANTO ANTÔNIO','RN','santo-antonio'),(1202,2411601,'SÃO BENTO DO NORTE','RN','sao-bento-do-norte'),(1203,2411700,'SÃO BENTO DO TRAIRÍ','RN','sao-bento-do-trairi'),(1204,2411809,'SÃO FERNANDO','RN','sao-fernando'),(1205,2411908,'SÃO FRANCISCO DO OESTE','RN','sao-francisco-do-oeste'),(1206,2412005,'SÃO GONÇALO DO AMARANTE','RN','sao-goncalo-do-amarante'),(1207,2412104,'SÃO JOÃO DO SABUGI','RN','sao-joao-do-sabugi'),(1208,2412203,'SÃO JOSÉ DE MIPIBU','RN','sao-jose-de-mipibu'),(1209,2412302,'SÃO JOSÉ DO CAMPESTRE','RN','sao-jose-do-campestre'),(1210,2412401,'SÃO JOSÉ DO SERIDÓ','RN','sao-jose-do-serido'),(1211,2412500,'SÃO MIGUEL','RN','sao-miguel'),(1212,2412559,'SÃO MIGUEL DO GOSTOSO','RN','sao-miguel-do-gostoso'),(1213,2412609,'SÃO PAULO DO POTENGI','RN','sao-paulo-do-potengi'),(1214,2412708,'SÃO PEDRO','RN','sao-pedro'),(1215,2412807,'SÃO RAFAEL','RN','sao-rafael'),(1216,2412906,'SÃO TOMÉ','RN','sao-tome'),(1217,2413003,'SÃO VICENTE','RN','sao-vicente'),(1218,2413102,'SENADOR ELÓI DE SOUZA','RN','senador-eloi-de-souza'),(1219,2413201,'SENADOR GEORGINO AVELINO','RN','senador-georgino-avelino'),(1220,2413300,'SERRA DE SÃO BENTO','RN','serra-de-sao-bento'),(1221,2413359,'SERRA DO MEL','RN','serra-do-mel'),(1222,2413409,'SERRA NEGRA DO NORTE','RN','serra-negra-do-norte'),(1223,2413508,'SERRINHA','RN','serrinha'),(1224,2413557,'SERRINHA DOS PINTOS','RN','serrinha-dos-pintos'),(1225,2413607,'SEVERIANO MELO','RN','severiano-melo'),(1226,2413706,'SÍTIO NOVO','RN','sitio-novo'),(1227,2413805,'TABOLEIRO GRANDE','RN','taboleiro-grande'),(1228,2413904,'TAIPU','RN','taipu'),(1229,2414001,'TANGARÁ','RN','tangara'),(1230,2414100,'TENENTE ANANIAS','RN','tenente-ananias'),(1231,2414159,'TENENTE LAURENTINO CRUZ','RN','tenente-laurentino-cruz'),(1232,2414209,'TIBAU DO SUL','RN','tibau-do-sul'),(1233,2414308,'TIMBAÚBA DOS BATISTAS','RN','timbauba-dos-batistas'),(1234,2414407,'TOUROS','RN','touros'),(1235,2414456,'TRIUNFO POTIGUAR','RN','triunfo-potiguar'),(1236,2414506,'UMARIZAL','RN','umarizal'),(1237,2414605,'UPANEMA','RN','upanema'),(1238,2414704,'VÁRZEA','RN','varzea'),(1239,2414753,'VENHA-VER','RN','venha-ver'),(1240,2414803,'VERA CRUZ','RN','vera-cruz'),(1241,2414902,'VIÇOSA','RN','vicosa'),(1242,2415008,'VILA FLOR','RN','vila-flor'),(1243,2500106,'ÁGUA BRANCA','PB','agua-branca'),(1244,2500205,'AGUIAR','PB','aguiar'),(1245,2500304,'ALAGOA GRANDE','PB','alagoa-grande'),(1246,2500403,'ALAGOA NOVA','PB','alagoa-nova'),(1247,2500502,'ALAGOINHA','PB','alagoinha'),(1248,2500536,'ALCANTIL','PB','alcantil'),(1249,2500577,'ALGODÃO DE JANDAÍRA','PB','algodao-de-jandaira'),(1250,2500601,'ALHANDRA','PB','alhandra'),(1251,2500700,'SÃO JOÃO DO RIO DO PEIXE','PB','sao-joao-do-rio-do-peixe'),(1252,2500734,'AMPARO','PB','amparo'),(1253,2500775,'APARECIDA','PB','aparecida'),(1254,2500809,'ARAÇAGI','PB','aracagi'),(1255,2500908,'ARARA','PB','arara'),(1256,2501005,'ARARUNA','PB','araruna'),(1257,2501104,'AREIA','PB','areia'),(1258,2501153,'AREIA DE BARAÚNAS','PB','areia-de-baraunas'),(1259,2501203,'AREIAL','PB','areial'),(1260,2501302,'AROEIRAS','PB','aroeiras'),(1261,2501351,'ASSUNÇÃO','PB','assuncao'),(1262,2501401,'BAÍA DA TRAIÇÃO','PB','baia-da-traicao'),(1263,2501500,'BANANEIRAS','PB','bananeiras'),(1264,2501534,'BARAÚNA','PB','barauna'),(1265,2501575,'BARRA DE SANTANA','PB','barra-de-santana'),(1266,2501609,'BARRA DE SANTA ROSA','PB','barra-de-santa-rosa'),(1267,2501708,'BARRA DE SÃO MIGUEL','PB','barra-de-sao-miguel'),(1268,2501807,'BAYEUX','PB','bayeux'),(1269,2501906,'BELÉM','PB','belem'),(1270,2502003,'BELÉM DO BREJO DO CRUZ','PB','belem-do-brejo-do-cruz'),(1271,2502052,'BERNARDINO BATISTA','PB','bernardino-batista'),(1272,2502102,'BOA VENTURA','PB','boa-ventura'),(1273,2502151,'BOA VISTA','PB','boa-vista'),(1274,2502201,'BOM JESUS','PB','bom-jesus'),(1275,2502300,'BOM SUCESSO','PB','bom-sucesso'),(1276,2502409,'BONITO DE SANTA FÉ','PB','bonito-de-santa-fe'),(1277,2502508,'BOQUEIRÃO','PB','boqueirao'),(1278,2502607,'IGARACY','PB','igaracy'),(1279,2502706,'BORBOREMA','PB','borborema'),(1280,2502805,'BREJO DO CRUZ','PB','brejo-do-cruz'),(1281,2502904,'BREJO DOS SANTOS','PB','brejo-dos-santos'),(1282,2503001,'CAAPORÃ','PB','caapora'),(1283,2503100,'CABACEIRAS','PB','cabaceiras'),(1284,2503209,'CABEDELO','PB','cabedelo'),(1285,2503308,'CACHOEIRA DOS ÍNDIOS','PB','cachoeira-dos-indios'),(1286,2503407,'CACIMBA DE AREIA','PB','cacimba-de-areia'),(1287,2503506,'CACIMBA DE DENTRO','PB','cacimba-de-dentro'),(1288,2503555,'CACIMBAS','PB','cacimbas'),(1289,2503605,'CAIÇARA','PB','caicara'),(1290,2503704,'CAJAZEIRAS','PB','cajazeiras'),(1291,2503753,'CAJAZEIRINHAS','PB','cajazeirinhas'),(1292,2503803,'CALDAS BRANDÃO','PB','caldas-brandao'),(1293,2503902,'CAMALAÚ','PB','camalau'),(1294,2504009,'CAMPINA GRANDE','PB','campina-grande'),(1295,2504033,'CAPIM','PB','capim'),(1296,2504074,'CARAÚBAS','PB','caraubas'),(1297,2504108,'CARRAPATEIRA','PB','carrapateira'),(1298,2504157,'CASSERENGUE','PB','casserengue'),(1299,2504207,'CATINGUEIRA','PB','catingueira'),(1300,2504306,'CATOLÉ DO ROCHA','PB','catole-do-rocha'),(1301,2504355,'CATURITÉ','PB','caturite'),(1302,2504405,'CONCEIÇÃO','PB','conceicao'),(1303,2504504,'CONDADO','PB','condado'),(1304,2504603,'CONDE','PB','conde'),(1305,2504702,'CONGO','PB','congo'),(1306,2504801,'COREMAS','PB','coremas'),(1307,2504850,'COXIXOLA','PB','coxixola'),(1308,2504900,'CRUZ DO ESPÍRITO SANTO','PB','cruz-do-espirito-santo'),(1309,2505006,'CUBATI','PB','cubati'),(1310,2505105,'CUITÉ','PB','cuite'),(1311,2505204,'CUITEGI','PB','cuitegi'),(1312,2505238,'CUITÉ DE MAMANGUAPE','PB','cuite-de-mamanguape'),(1313,2505279,'CURRAL DE CIMA','PB','curral-de-cima'),(1314,2505303,'CURRAL VELHO','PB','curral-velho'),(1315,2505352,'DAMIÃO','PB','damiao'),(1316,2505402,'DESTERRO','PB','desterro'),(1317,2505501,'VISTA SERRANA','PB','vista-serrana'),(1318,2505600,'DIAMANTE','PB','diamante'),(1319,2505709,'DONA INÊS','PB','dona-ines'),(1320,2505808,'DUAS ESTRADAS','PB','duas-estradas'),(1321,2505907,'EMAS','PB','emas'),(1322,2506004,'ESPERANÇA','PB','esperanca'),(1323,2506103,'FAGUNDES','PB','fagundes'),(1324,2506202,'FREI MARTINHO','PB','frei-martinho'),(1325,2506251,'GADO BRAVO','PB','gado-bravo'),(1326,2506301,'GUARABIRA','PB','guarabira'),(1327,2506400,'GURINHÉM','PB','gurinhem'),(1328,2506509,'GURJÃO','PB','gurjao'),(1329,2506608,'IBIARA','PB','ibiara'),(1330,2506707,'IMACULADA','PB','imaculada'),(1331,2506806,'INGÁ','PB','inga'),(1332,2506905,'ITABAIANA','PB','itabaiana'),(1333,2507002,'ITAPORANGA','PB','itaporanga'),(1334,2507101,'ITAPOROROCA','PB','itapororoca'),(1335,2507200,'ITATUBA','PB','itatuba'),(1336,2507309,'JACARAÚ','PB','jacarau'),(1337,2507408,'JERICÓ','PB','jerico'),(1338,2507507,'JOÃO PESSOA','PB','joao-pessoa'),(1339,2507606,'JUAREZ TÁVORA','PB','juarez-tavora'),(1340,2507705,'JUAZEIRINHO','PB','juazeirinho'),(1341,2507804,'JUNCO DO SERIDÓ','PB','junco-do-serido'),(1342,2507903,'JURIPIRANGA','PB','juripiranga'),(1343,2508000,'JURU','PB','juru'),(1344,2508109,'LAGOA','PB','lagoa'),(1345,2508208,'LAGOA DE DENTRO','PB','lagoa-de-dentro'),(1346,2508307,'LAGOA SECA','PB','lagoa-seca'),(1347,2508406,'LASTRO','PB','lastro'),(1348,2508505,'LIVRAMENTO','PB','livramento'),(1349,2508554,'LOGRADOURO','PB','logradouro'),(1350,2508604,'LUCENA','PB','lucena'),(1351,2508703,'MÃE D\'ÁGUA','PB','mae-dagua'),(1352,2508802,'MALTA','PB','malta'),(1353,2508901,'MAMANGUAPE','PB','mamanguape'),(1354,2509008,'MANAÍRA','PB','manaira'),(1355,2509057,'MARCAÇÃO','PB','marcacao'),(1356,2509107,'MARI','PB','mari'),(1357,2509156,'MARIZÓPOLIS','PB','marizopolis'),(1358,2509206,'MASSARANDUBA','PB','massaranduba'),(1359,2509305,'MATARACA','PB','mataraca'),(1360,2509339,'MATINHAS','PB','matinhas'),(1361,2509370,'MATO GROSSO','PB','mato-grosso'),(1362,2509396,'MATURÉIA','PB','matureia'),(1363,2509404,'MOGEIRO','PB','mogeiro'),(1364,2509503,'MONTADAS','PB','montadas'),(1365,2509602,'MONTE HOREBE','PB','monte-horebe'),(1366,2509701,'MONTEIRO','PB','monteiro'),(1367,2509800,'MULUNGU','PB','mulungu'),(1368,2509909,'NATUBA','PB','natuba'),(1369,2510006,'NAZAREZINHO','PB','nazarezinho'),(1370,2510105,'NOVA FLORESTA','PB','nova-floresta'),(1371,2510204,'NOVA OLINDA','PB','nova-olinda'),(1372,2510303,'NOVA PALMEIRA','PB','nova-palmeira'),(1373,2510402,'OLHO D\'ÁGUA','PB','olho-dagua'),(1374,2510501,'OLIVEDOS','PB','olivedos'),(1375,2510600,'OURO VELHO','PB','ouro-velho'),(1376,2510659,'PARARI','PB','parari'),(1377,2510709,'PASSAGEM','PB','passagem'),(1378,2510808,'PATOS','PB','patos'),(1379,2510907,'PAULISTA','PB','paulista'),(1380,2511004,'PEDRA BRANCA','PB','pedra-branca'),(1381,2511103,'PEDRA LAVRADA','PB','pedra-lavrada'),(1382,2511202,'PEDRAS DE FOGO','PB','pedras-de-fogo'),(1383,2511301,'PIANCÓ','PB','pianco'),(1384,2511400,'PICUÍ','PB','picui'),(1385,2511509,'PILAR','PB','pilar'),(1386,2511608,'PILÕES','PB','piloes'),(1387,2511707,'PILÕEZINHOS','PB','piloezinhos'),(1388,2511806,'PIRPIRITUBA','PB','pirpirituba'),(1389,2511905,'PITIMBU','PB','pitimbu'),(1390,2512002,'POCINHOS','PB','pocinhos'),(1391,2512036,'POÇO DANTAS','PB','poco-dantas'),(1392,2512077,'POÇO DE JOSÉ DE MOURA','PB','poco-de-jose-de-moura'),(1393,2512101,'POMBAL','PB','pombal'),(1394,2512200,'PRATA','PB','prata'),(1395,2512309,'PRINCESA ISABEL','PB','princesa-isabel'),(1396,2512408,'PUXINANÃ','PB','puxinana'),(1397,2512507,'QUEIMADAS','PB','queimadas'),(1398,2512606,'QUIXABÁ','PB','quixaba'),(1399,2512705,'REMÍGIO','PB','remigio'),(1400,2512721,'PEDRO RÉGIS','PB','pedro-regis'),(1401,2512747,'RIACHÃO','PB','riachao'),(1402,2512754,'RIACHÃO DO BACAMARTE','PB','riachao-do-bacamarte'),(1403,2512762,'RIACHÃO DO POÇO','PB','riachao-do-poco'),(1404,2512788,'RIACHO DE SANTO ANTÔNIO','PB','riacho-de-santo-antonio'),(1405,2512804,'RIACHO DOS CAVALOS','PB','riacho-dos-cavalos'),(1406,2512903,'RIO TINTO','PB','rio-tinto'),(1407,2513000,'SALGADINHO','PB','salgadinho'),(1408,2513109,'SALGADO DE SÃO FÉLIX','PB','salgado-de-sao-felix'),(1409,2513158,'SANTA CECÍLIA','PB','santa-cecilia'),(1410,2513208,'SANTA CRUZ','PB','santa-cruz'),(1411,2513307,'SANTA HELENA','PB','santa-helena'),(1412,2513356,'SANTA INÊS','PB','santa-ines'),(1413,2513406,'SANTA LUZIA','PB','santa-luzia'),(1414,2513505,'SANTANA DE MANGUEIRA','PB','santana-de-mangueira'),(1415,2513604,'SANTANA DOS GARROTES','PB','santana-dos-garrotes'),(1416,2513653,'JOCA CLAUDINO','PB','joca-claudino'),(1417,2513703,'SANTA RITA','PB','santa-rita'),(1418,2513802,'SANTA TERESINHA','PB','santa-teresinha'),(1419,2513851,'SANTO ANDRÉ','PB','santo-andre'),(1420,2513901,'SÃO BENTO','PB','sao-bento'),(1421,2513927,'SÃO BENTINHO','PB','sao-bentinho'),(1422,2513943,'SÃO DOMINGOS DO CARIRI','PB','sao-domingos-do-cariri'),(1423,2513968,'SÃO DOMINGOS','PB','sao-domingos'),(1424,2513984,'SÃO FRANCISCO','PB','sao-francisco'),(1425,2514008,'SÃO JOÃO DO CARIRI','PB','sao-joao-do-cariri'),(1426,2514107,'SÃO JOÃO DO TIGRE','PB','sao-joao-do-tigre'),(1427,2514206,'SÃO JOSÉ DA LAGOA TAPADA','PB','sao-jose-da-lagoa-tapada'),(1428,2514305,'SÃO JOSÉ DE CAIANA','PB','sao-jose-de-caiana'),(1429,2514404,'SÃO JOSÉ DE ESPINHARAS','PB','sao-jose-de-espinharas'),(1430,2514453,'SÃO JOSÉ DOS RAMOS','PB','sao-jose-dos-ramos'),(1431,2514503,'SÃO JOSÉ DE PIRANHAS','PB','sao-jose-de-piranhas'),(1432,2514552,'SÃO JOSÉ DE PRINCESA','PB','sao-jose-de-princesa'),(1433,2514602,'SÃO JOSÉ DO BONFIM','PB','sao-jose-do-bonfim'),(1434,2514651,'SÃO JOSÉ DO BREJO DO CRUZ','PB','sao-jose-do-brejo-do-cruz'),(1435,2514701,'SÃO JOSÉ DO SABUGI','PB','sao-jose-do-sabugi'),(1436,2514800,'SÃO JOSÉ DOS CORDEIROS','PB','sao-jose-dos-cordeiros'),(1437,2514909,'SÃO MAMEDE','PB','sao-mamede'),(1438,2515005,'SÃO MIGUEL DE TAIPU','PB','sao-miguel-de-taipu'),(1439,2515104,'SÃO SEBASTIÃO DE LAGOA DE ROÇA','PB','sao-sebastiao-de-lagoa-de-roca'),(1440,2515203,'SÃO SEBASTIÃO DO UMBUZEIRO','PB','sao-sebastiao-do-umbuzeiro'),(1441,2515302,'SAPÉ','PB','sape'),(1442,2515401,'SÃO VICENTE DO SERIDÓ','PB','sao-vicente-do-serido'),(1443,2515500,'SERRA BRANCA','PB','serra-branca'),(1444,2515609,'SERRA DA RAIZ','PB','serra-da-raiz'),(1445,2515708,'SERRA GRANDE','PB','serra-grande'),(1446,2515807,'SERRA REDONDA','PB','serra-redonda'),(1447,2515906,'SERRARIA','PB','serraria'),(1448,2515930,'SERTÃOZINHO','PB','sertaozinho'),(1449,2515971,'SOBRADO','PB','sobrado'),(1450,2516003,'SOLÂNEA','PB','solanea'),(1451,2516102,'SOLEDADE','PB','soledade'),(1452,2516151,'SOSSÊGO','PB','sossego'),(1453,2516201,'SOUSA','PB','sousa'),(1454,2516300,'SUMÉ','PB','sume'),(1455,2516409,'TACIMA','PB','tacima'),(1456,2516508,'TAPEROÁ','PB','taperoa'),(1457,2516607,'TAVARES','PB','tavares'),(1458,2516706,'TEIXEIRA','PB','teixeira'),(1459,2516755,'TENÓRIO','PB','tenorio'),(1460,2516805,'TRIUNFO','PB','triunfo'),(1461,2516904,'UIRAÚNA','PB','uirauna'),(1462,2517001,'UMBUZEIRO','PB','umbuzeiro'),(1463,2517100,'VÁRZEA','PB','varzea'),(1464,2517209,'VIEIRÓPOLIS','PB','vieiropolis'),(1465,2517407,'ZABELÊ','PB','zabele'),(1466,2600054,'ABREU E LIMA','PE','abreu-e-lima'),(1467,2600104,'AFOGADOS DA INGAZEIRA','PE','afogados-da-ingazeira'),(1468,2600203,'AFRÂNIO','PE','afranio'),(1469,2600302,'AGRESTINA','PE','agrestina'),(1470,2600401,'ÁGUA PRETA','PE','agua-preta'),(1471,2600500,'ÁGUAS BELAS','PE','aguas-belas'),(1472,2600609,'ALAGOINHA','PE','alagoinha'),(1473,2600708,'ALIANÇA','PE','alianca'),(1474,2600807,'ALTINHO','PE','altinho'),(1475,2600906,'AMARAJI','PE','amaraji'),(1476,2601003,'ANGELIM','PE','angelim'),(1477,2601052,'ARAÇOIABA','PE','aracoiaba'),(1478,2601102,'ARARIPINA','PE','araripina'),(1479,2601201,'ARCOVERDE','PE','arcoverde'),(1480,2601300,'BARRA DE GUABIRABA','PE','barra-de-guabiraba'),(1481,2601409,'BARREIROS','PE','barreiros'),(1482,2601508,'BELÉM DE MARIA','PE','belem-de-maria'),(1483,2601607,'BELÉM DO SÃO FRANCISCO','PE','belem-do-sao-francisco'),(1484,2601706,'BELO JARDIM','PE','belo-jardim'),(1485,2601805,'BETÂNIA','PE','betania'),(1486,2601904,'BEZERROS','PE','bezerros'),(1487,2602001,'BODOCÓ','PE','bodoco'),(1488,2602100,'BOM CONSELHO','PE','bom-conselho'),(1489,2602209,'BOM JARDIM','PE','bom-jardim'),(1490,2602308,'BONITO','PE','bonito'),(1491,2602407,'BREJÃO','PE','brejao'),(1492,2602506,'BREJINHO','PE','brejinho'),(1493,2602605,'BREJO DA MADRE DE DEUS','PE','brejo-da-madre-de-deus'),(1494,2602704,'BUENOS AIRES','PE','buenos-aires'),(1495,2602803,'BUÍQUE','PE','buique'),(1496,2602902,'CABO DE SANTO AGOSTINHO','PE','cabo-de-santo-agostinho'),(1497,2603009,'CABROBÓ','PE','cabrobo'),(1498,2603108,'CACHOEIRINHA','PE','cachoeirinha'),(1499,2603207,'CAETÉS','PE','caetes'),(1500,2603306,'CALÇADO','PE','calcado'),(1501,2603405,'CALUMBI','PE','calumbi'),(1502,2603454,'CAMARAGIBE','PE','camaragibe'),(1503,2603504,'CAMOCIM DE SÃO FÉLIX','PE','camocim-de-sao-felix'),(1504,2603603,'CAMUTANGA','PE','camutanga'),(1505,2603702,'CANHOTINHO','PE','canhotinho'),(1506,2603801,'CAPOEIRAS','PE','capoeiras'),(1507,2603900,'CARNAÍBA','PE','carnaiba'),(1508,2603926,'CARNAUBEIRA DA PENHA','PE','carnaubeira-da-penha'),(1509,2604007,'CARPINA','PE','carpina'),(1510,2604106,'CARUARU','PE','caruaru'),(1511,2604155,'CASINHAS','PE','casinhas'),(1512,2604205,'CATENDE','PE','catende'),(1513,2604304,'CEDRO','PE','cedro'),(1514,2604403,'CHÃ DE ALEGRIA','PE','cha-de-alegria'),(1515,2604502,'CHÃ GRANDE','PE','cha-grande'),(1516,2604601,'CONDADO','PE','condado'),(1517,2604700,'CORRENTES','PE','correntes'),(1518,2604809,'CORTÊS','PE','cortes'),(1519,2604908,'CUMARU','PE','cumaru'),(1520,2605004,'CUPIRA','PE','cupira'),(1521,2605103,'CUSTÓDIA','PE','custodia'),(1522,2605152,'DORMENTES','PE','dormentes'),(1523,2605202,'ESCADA','PE','escada'),(1524,2605301,'EXU','PE','exu'),(1525,2605400,'FEIRA NOVA','PE','feira-nova'),(1526,2605459,'FERNANDO DE NORONHA','PE','fernando-de-noronha'),(1527,2605509,'FERREIROS','PE','ferreiros'),(1528,2605608,'FLORES','PE','flores'),(1529,2605707,'FLORESTA','PE','floresta'),(1530,2605806,'FREI MIGUELINHO','PE','frei-miguelinho'),(1531,2605905,'GAMELEIRA','PE','gameleira'),(1532,2606002,'GARANHUNS','PE','garanhuns'),(1533,2606101,'GLÓRIA DO GOITÁ','PE','gloria-do-goita'),(1534,2606200,'GOIANA','PE','goiana'),(1535,2606309,'GRANITO','PE','granito'),(1536,2606408,'GRAVATÁ','PE','gravata'),(1537,2606507,'IATI','PE','iati'),(1538,2606606,'IBIMIRIM','PE','ibimirim'),(1539,2606705,'IBIRAJUBA','PE','ibirajuba'),(1540,2606804,'IGARASSU','PE','igarassu'),(1541,2606903,'IGUARACI','PE','iguaraci'),(1542,2607000,'INAJÁ','PE','inaja'),(1543,2607109,'INGAZEIRA','PE','ingazeira'),(1544,2607208,'IPOJUCA','PE','ipojuca'),(1545,2607307,'IPUBI','PE','ipubi'),(1546,2607406,'ITACURUBA','PE','itacuruba'),(1547,2607505,'ITAÍBA','PE','itaiba'),(1548,2607604,'ILHA DE ITAMARACÁ','PE','ilha-de-itamaraca'),(1549,2607653,'ITAMBÉ','PE','itambe'),(1550,2607703,'ITAPETIM','PE','itapetim'),(1551,2607752,'ITAPISSUMA','PE','itapissuma'),(1552,2607802,'ITAQUITINGA','PE','itaquitinga'),(1553,2607901,'JABOATÃO DOS GUARARAPES','PE','jaboatao-dos-guararapes'),(1554,2607950,'JAQUEIRA','PE','jaqueira'),(1555,2608008,'JATAÚBA','PE','jatauba'),(1556,2608057,'JATOBÁ','PE','jatoba'),(1557,2608107,'JOÃO ALFREDO','PE','joao-alfredo'),(1558,2608206,'JOAQUIM NABUCO','PE','joaquim-nabuco'),(1559,2608255,'JUCATI','PE','jucati'),(1560,2608305,'JUPI','PE','jupi'),(1561,2608404,'JUREMA','PE','jurema'),(1562,2608453,'LAGOA DO CARRO','PE','lagoa-do-carro'),(1563,2608503,'LAGOA DE ITAENGA','PE','lagoa-de-itaenga'),(1564,2608602,'LAGOA DO OURO','PE','lagoa-do-ouro'),(1565,2608701,'LAGOA DOS GATOS','PE','lagoa-dos-gatos'),(1566,2608750,'LAGOA GRANDE','PE','lagoa-grande'),(1567,2608800,'LAJEDO','PE','lajedo'),(1568,2608909,'LIMOEIRO','PE','limoeiro'),(1569,2609006,'MACAPARANA','PE','macaparana'),(1570,2609105,'MACHADOS','PE','machados'),(1571,2609154,'MANARI','PE','manari'),(1572,2609204,'MARAIAL','PE','maraial'),(1573,2609303,'MIRANDIBA','PE','mirandiba'),(1574,2609402,'MORENO','PE','moreno'),(1575,2609501,'NAZARÉ DA MATA','PE','nazare-da-mata'),(1576,2609600,'OLINDA','PE','olinda'),(1577,2609709,'OROBÓ','PE','orobo'),(1578,2609808,'OROCÓ','PE','oroco'),(1579,2609907,'OURICURI','PE','ouricuri'),(1580,2610004,'PALMARES','PE','palmares'),(1581,2610103,'PALMEIRINA','PE','palmeirina'),(1582,2610202,'PANELAS','PE','panelas'),(1583,2610301,'PARANATAMA','PE','paranatama'),(1584,2610400,'PARNAMIRIM','PE','parnamirim'),(1585,2610509,'PASSIRA','PE','passira'),(1586,2610608,'PAUDALHO','PE','paudalho'),(1587,2610707,'PAULISTA','PE','paulista'),(1588,2610806,'PEDRA','PE','pedra'),(1589,2610905,'PESQUEIRA','PE','pesqueira'),(1590,2611002,'PETROLÂNDIA','PE','petrolandia'),(1591,2611101,'PETROLINA','PE','petrolina'),(1592,2611200,'POÇÃO','PE','pocao'),(1593,2611309,'POMBOS','PE','pombos'),(1594,2611408,'PRIMAVERA','PE','primavera'),(1595,2611507,'QUIPAPÁ','PE','quipapa'),(1596,2611533,'QUIXABA','PE','quixaba'),(1597,2611606,'RECIFE','PE','recife'),(1598,2611705,'RIACHO DAS ALMAS','PE','riacho-das-almas'),(1599,2611804,'RIBEIRÃO','PE','ribeirao'),(1600,2611903,'RIO FORMOSO','PE','rio-formoso'),(1601,2612000,'SAIRÉ','PE','saire'),(1602,2612109,'SALGADINHO','PE','salgadinho'),(1603,2612208,'SALGUEIRO','PE','salgueiro'),(1604,2612307,'SALOÁ','PE','saloa'),(1605,2612406,'SANHARÓ','PE','sanharo'),(1606,2612455,'SANTA CRUZ','PE','santa-cruz'),(1607,2612471,'SANTA CRUZ DA BAIXA VERDE','PE','santa-cruz-da-baixa-verde'),(1608,2612505,'SANTA CRUZ DO CAPIBARIBE','PE','santa-cruz-do-capibaribe'),(1609,2612554,'SANTA FILOMENA','PE','santa-filomena'),(1610,2612604,'SANTA MARIA DA BOA VISTA','PE','santa-maria-da-boa-vista'),(1611,2612703,'SANTA MARIA DO CAMBUCÁ','PE','santa-maria-do-cambuca'),(1612,2612802,'SANTA TEREZINHA','PE','santa-terezinha'),(1613,2612901,'SÃO BENEDITO DO SUL','PE','sao-benedito-do-sul'),(1614,2613008,'SÃO BENTO DO UNA','PE','sao-bento-do-una'),(1615,2613107,'SÃO CAITANO','PE','sao-caitano'),(1616,2613206,'SÃO JOÃO','PE','sao-joao'),(1617,2613305,'SÃO JOAQUIM DO MONTE','PE','sao-joaquim-do-monte'),(1618,2613404,'SÃO JOSÉ DA COROA GRANDE','PE','sao-jose-da-coroa-grande'),(1619,2613503,'SÃO JOSÉ DO BELMONTE','PE','sao-jose-do-belmonte'),(1620,2613602,'SÃO JOSÉ DO EGITO','PE','sao-jose-do-egito'),(1621,2613701,'SÃO LOURENÇO DA MATA','PE','sao-lourenco-da-mata'),(1622,2613800,'SÃO VICENTE FERRER','PE','sao-vicente-ferrer'),(1623,2613909,'SERRA TALHADA','PE','serra-talhada'),(1624,2614006,'SERRITA','PE','serrita'),(1625,2614105,'SERTÂNIA','PE','sertania'),(1626,2614204,'SIRINHAÉM','PE','sirinhaem'),(1627,2614303,'MOREILÂNDIA','PE','moreilandia'),(1628,2614402,'SOLIDÃO','PE','solidao'),(1629,2614501,'SURUBIM','PE','surubim'),(1630,2614600,'TABIRA','PE','tabira'),(1631,2614709,'TACAIMBÓ','PE','tacaimbo'),(1632,2614808,'TACARATU','PE','tacaratu'),(1633,2614857,'TAMANDARÉ','PE','tamandare'),(1634,2615003,'TAQUARITINGA DO NORTE','PE','taquaritinga-do-norte'),(1635,2615102,'TEREZINHA','PE','terezinha'),(1636,2615201,'TERRA NOVA','PE','terra-nova'),(1637,2615300,'TIMBAÚBA','PE','timbauba'),(1638,2615409,'TORITAMA','PE','toritama'),(1639,2615508,'TRACUNHAÉM','PE','tracunhaem'),(1640,2615607,'TRINDADE','PE','trindade'),(1641,2615706,'TRIUNFO','PE','triunfo'),(1642,2615805,'TUPANATINGA','PE','tupanatinga'),(1643,2615904,'TUPARETAMA','PE','tuparetama'),(1644,2616001,'VENTUROSA','PE','venturosa'),(1645,2616100,'VERDEJANTE','PE','verdejante'),(1646,2616183,'VERTENTE DO LÉRIO','PE','vertente-do-lerio'),(1647,2616209,'VERTENTES','PE','vertentes'),(1648,2616308,'VICÊNCIA','PE','vicencia'),(1649,2616407,'VITÓRIA DE SANTO ANTÃO','PE','vitoria-de-santo-antao'),(1650,2616506,'XEXÉU','PE','xexeu'),(1651,2700102,'ÁGUA BRANCA','AL','agua-branca'),(1652,2700201,'ANADIA','AL','anadia'),(1653,2700300,'ARAPIRACA','AL','arapiraca'),(1654,2700409,'ATALAIA','AL','atalaia'),(1655,2700508,'BARRA DE SANTO ANTÔNIO','AL','barra-de-santo-antonio'),(1656,2700607,'BARRA DE SÃO MIGUEL','AL','barra-de-sao-miguel'),(1657,2700706,'BATALHA','AL','batalha'),(1658,2700805,'BELÉM','AL','belem'),(1659,2700904,'BELO MONTE','AL','belo-monte'),(1660,2701001,'BOCA DA MATA','AL','boca-da-mata'),(1661,2701100,'BRANQUINHA','AL','branquinha'),(1662,2701209,'CACIMBINHAS','AL','cacimbinhas'),(1663,2701308,'CAJUEIRO','AL','cajueiro'),(1664,2701357,'CAMPESTRE','AL','campestre'),(1665,2701407,'CAMPO ALEGRE','AL','campo-alegre'),(1666,2701506,'CAMPO GRANDE','AL','campo-grande'),(1667,2701605,'CANAPI','AL','canapi'),(1668,2701704,'CAPELA','AL','capela'),(1669,2701803,'CARNEIROS','AL','carneiros'),(1670,2701902,'CHÃ PRETA','AL','cha-preta'),(1671,2702009,'COITÉ DO NÓIA','AL','coite-do-noia'),(1672,2702108,'COLÔNIA LEOPOLDINA','AL','colonia-leopoldina'),(1673,2702207,'COQUEIRO SECO','AL','coqueiro-seco'),(1674,2702306,'CORURIPE','AL','coruripe'),(1675,2702355,'CRAÍBAS','AL','craibas'),(1676,2702405,'DELMIRO GOUVEIA','AL','delmiro-gouveia'),(1677,2702504,'DOIS RIACHOS','AL','dois-riachos'),(1678,2702553,'ESTRELA DE ALAGOAS','AL','estrela-de-alagoas'),(1679,2702603,'FEIRA GRANDE','AL','feira-grande'),(1680,2702702,'FELIZ DESERTO','AL','feliz-deserto'),(1681,2702801,'FLEXEIRAS','AL','flexeiras'),(1682,2702900,'GIRAU DO PONCIANO','AL','girau-do-ponciano'),(1683,2703007,'IBATEGUARA','AL','ibateguara'),(1684,2703106,'IGACI','AL','igaci'),(1685,2703205,'IGREJA NOVA','AL','igreja-nova'),(1686,2703304,'INHAPI','AL','inhapi'),(1687,2703403,'JACARÉ DOS HOMENS','AL','jacare-dos-homens'),(1688,2703502,'JACUÍPE','AL','jacuipe'),(1689,2703601,'JAPARATINGA','AL','japaratinga'),(1690,2703700,'JARAMATAIA','AL','jaramataia'),(1691,2703759,'JEQUIÁ DA PRAIA','AL','jequia-da-praia'),(1692,2703809,'JOAQUIM GOMES','AL','joaquim-gomes'),(1693,2703908,'JUNDIÁ','AL','jundia'),(1694,2704005,'JUNQUEIRO','AL','junqueiro'),(1695,2704104,'LAGOA DA CANOA','AL','lagoa-da-canoa'),(1696,2704203,'LIMOEIRO DE ANADIA','AL','limoeiro-de-anadia'),(1697,2704302,'MACEIÓ','AL','maceio'),(1698,2704401,'MAJOR ISIDORO','AL','major-isidoro'),(1699,2704500,'MARAGOGI','AL','maragogi'),(1700,2704609,'MARAVILHA','AL','maravilha'),(1701,2704708,'MARECHAL DEODORO','AL','marechal-deodoro'),(1702,2704807,'MARIBONDO','AL','maribondo'),(1703,2704906,'MAR VERMELHO','AL','mar-vermelho'),(1704,2705002,'MATA GRANDE','AL','mata-grande'),(1705,2705101,'MATRIZ DE CAMARAGIBE','AL','matriz-de-camaragibe'),(1706,2705200,'MESSIAS','AL','messias'),(1707,2705309,'MINADOR DO NEGRÃO','AL','minador-do-negrao'),(1708,2705408,'MONTEIRÓPOLIS','AL','monteiropolis'),(1709,2705507,'MURICI','AL','murici'),(1710,2705606,'NOVO LINO','AL','novo-lino'),(1711,2705705,'OLHO D\'ÁGUA DAS FLORES','AL','olho-dagua-das-flores'),(1712,2705804,'OLHO D\'ÁGUA DO CASADO','AL','olho-dagua-do-casado'),(1713,2705903,'OLHO D\'ÁGUA GRANDE','AL','olho-dagua-grande'),(1714,2706000,'OLIVENÇA','AL','olivenca'),(1715,2706109,'OURO BRANCO','AL','ouro-branco'),(1716,2706208,'PALESTINA','AL','palestina'),(1717,2706307,'PALMEIRA DOS ÍNDIOS','AL','palmeira-dos-indios'),(1718,2706406,'PÃO DE AÇÚCAR','AL','pao-de-acucar'),(1719,2706422,'PARICONHA','AL','pariconha'),(1720,2706448,'PARIPUEIRA','AL','paripueira'),(1721,2706505,'PASSO DE CAMARAGIBE','AL','passo-de-camaragibe'),(1722,2706604,'PAULO JACINTO','AL','paulo-jacinto'),(1723,2706703,'PENEDO','AL','penedo'),(1724,2706802,'PIAÇABUÇU','AL','piacabucu'),(1725,2706901,'PILAR','AL','pilar'),(1726,2707008,'PINDOBA','AL','pindoba'),(1727,2707107,'PIRANHAS','AL','piranhas'),(1728,2707206,'POÇO DAS TRINCHEIRAS','AL','poco-das-trincheiras'),(1729,2707305,'PORTO CALVO','AL','porto-calvo'),(1730,2707404,'PORTO DE PEDRAS','AL','porto-de-pedras'),(1731,2707503,'PORTO REAL DO COLÉGIO','AL','porto-real-do-colegio'),(1732,2707602,'QUEBRANGULO','AL','quebrangulo'),(1733,2707701,'RIO LARGO','AL','rio-largo'),(1734,2707800,'ROTEIRO','AL','roteiro'),(1735,2707909,'SANTA LUZIA DO NORTE','AL','santa-luzia-do-norte'),(1736,2708006,'SANTANA DO IPANEMA','AL','santana-do-ipanema'),(1737,2708105,'SANTANA DO MUNDAÚ','AL','santana-do-mundau'),(1738,2708204,'SÃO BRÁS','AL','sao-bras'),(1739,2708303,'SÃO JOSÉ DA LAJE','AL','sao-jose-da-laje'),(1740,2708402,'SÃO JOSÉ DA TAPERA','AL','sao-jose-da-tapera'),(1741,2708501,'SÃO LUÍS DO QUITUNDE','AL','sao-luis-do-quitunde'),(1742,2708600,'SÃO MIGUEL DOS CAMPOS','AL','sao-miguel-dos-campos'),(1743,2708709,'SÃO MIGUEL DOS MILAGRES','AL','sao-miguel-dos-milagres'),(1744,2708808,'SÃO SEBASTIÃO','AL','sao-sebastiao'),(1745,2708907,'SATUBA','AL','satuba'),(1746,2708956,'SENADOR RUI PALMEIRA','AL','senador-rui-palmeira'),(1747,2709004,'TANQUE D\'ARCA','AL','tanque-darca'),(1748,2709103,'TAQUARANA','AL','taquarana'),(1749,2709152,'TEOTÔNIO VILELA','AL','teotonio-vilela'),(1750,2709202,'TRAIPU','AL','traipu'),(1751,2709301,'UNIÃO DOS PALMARES','AL','uniao-dos-palmares'),(1752,2709400,'VIÇOSA','AL','vicosa'),(1753,2800100,'AMPARO DE SÃO FRANCISCO','SE','amparo-de-sao-francisco'),(1754,2800209,'AQUIDABÃ','SE','aquidaba'),(1755,2800308,'ARACAJU','SE','aracaju'),(1756,2800407,'ARAUÁ','SE','araua'),(1757,2800506,'AREIA BRANCA','SE','areia-branca'),(1758,2800605,'BARRA DOS COQUEIROS','SE','barra-dos-coqueiros'),(1759,2800670,'BOQUIM','SE','boquim'),(1760,2800704,'BREJO GRANDE','SE','brejo-grande'),(1761,2801009,'CAMPO DO BRITO','SE','campo-do-brito'),(1762,2801108,'CANHOBA','SE','canhoba'),(1763,2801207,'CANINDÉ DE SÃO FRANCISCO','SE','caninde-de-sao-francisco'),(1764,2801306,'CAPELA','SE','capela'),(1765,2801405,'CARIRA','SE','carira'),(1766,2801504,'CARMÓPOLIS','SE','carmopolis'),(1767,2801603,'CEDRO DE SÃO JOÃO','SE','cedro-de-sao-joao'),(1768,2801702,'CRISTINÁPOLIS','SE','cristinapolis'),(1769,2801900,'CUMBE','SE','cumbe'),(1770,2802007,'DIVINA PASTORA','SE','divina-pastora'),(1771,2802106,'ESTÂNCIA','SE','estancia'),(1772,2802205,'FEIRA NOVA','SE','feira-nova'),(1773,2802304,'FREI PAULO','SE','frei-paulo'),(1774,2802403,'GARARU','SE','gararu'),(1775,2802502,'GENERAL MAYNARD','SE','general-maynard'),(1776,2802601,'GRACHO CARDOSO','SE','gracho-cardoso'),(1777,2802700,'ILHA DAS FLORES','SE','ilha-das-flores'),(1778,2802809,'INDIAROBA','SE','indiaroba'),(1779,2802908,'ITABAIANA','SE','itabaiana'),(1780,2803005,'ITABAIANINHA','SE','itabaianinha'),(1781,2803104,'ITABI','SE','itabi'),(1782,2803203,'ITAPORANGA D\'AJUDA','SE','itaporanga-dajuda'),(1783,2803302,'JAPARATUBA','SE','japaratuba'),(1784,2803401,'JAPOATÃ','SE','japoata'),(1785,2803500,'LAGARTO','SE','lagarto'),(1786,2803609,'LARANJEIRAS','SE','laranjeiras'),(1787,2803708,'MACAMBIRA','SE','macambira'),(1788,2803807,'MALHADA DOS BOIS','SE','malhada-dos-bois'),(1789,2803906,'MALHADOR','SE','malhador'),(1790,2804003,'MARUIM','SE','maruim'),(1791,2804102,'MOITA BONITA','SE','moita-bonita'),(1792,2804201,'MONTE ALEGRE DE SERGIPE','SE','monte-alegre-de-sergipe'),(1793,2804300,'MURIBECA','SE','muribeca'),(1794,2804409,'NEÓPOLIS','SE','neopolis'),(1795,2804458,'NOSSA SENHORA APARECIDA','SE','nossa-senhora-aparecida'),(1796,2804508,'NOSSA SENHORA DA GLÓRIA','SE','nossa-senhora-da-gloria'),(1797,2804607,'NOSSA SENHORA DAS DORES','SE','nossa-senhora-das-dores'),(1798,2804706,'NOSSA SENHORA DE LOURDES','SE','nossa-senhora-de-lourdes'),(1799,2804805,'NOSSA SENHORA DO SOCORRO','SE','nossa-senhora-do-socorro'),(1800,2804904,'PACATUBA','SE','pacatuba'),(1801,2805000,'PEDRA MOLE','SE','pedra-mole'),(1802,2805109,'PEDRINHAS','SE','pedrinhas'),(1803,2805208,'PINHÃO','SE','pinhao'),(1804,2805307,'PIRAMBU','SE','pirambu'),(1805,2805406,'POÇO REDONDO','SE','poco-redondo'),(1806,2805505,'POÇO VERDE','SE','poco-verde'),(1807,2805604,'PORTO DA FOLHA','SE','porto-da-folha'),(1808,2805703,'PROPRIÁ','SE','propria'),(1809,2805802,'RIACHÃO DO DANTAS','SE','riachao-do-dantas'),(1810,2805901,'RIACHUELO','SE','riachuelo'),(1811,2806008,'RIBEIRÓPOLIS','SE','ribeiropolis'),(1812,2806107,'ROSÁRIO DO CATETE','SE','rosario-do-catete'),(1813,2806206,'SALGADO','SE','salgado'),(1814,2806305,'SANTA LUZIA DO ITANHY','SE','santa-luzia-do-itanhy'),(1815,2806404,'SANTANA DO SÃO FRANCISCO','SE','santana-do-sao-francisco'),(1816,2806503,'SANTA ROSA DE LIMA','SE','santa-rosa-de-lima'),(1817,2806602,'SANTO AMARO DAS BROTAS','SE','santo-amaro-das-brotas'),(1818,2806701,'SÃO CRISTÓVÃO','SE','sao-cristovao'),(1819,2806800,'SÃO DOMINGOS','SE','sao-domingos'),(1820,2806909,'SÃO FRANCISCO','SE','sao-francisco'),(1821,2807006,'SÃO MIGUEL DO ALEIXO','SE','sao-miguel-do-aleixo'),(1822,2807105,'SIMÃO DIAS','SE','simao-dias'),(1823,2807204,'SIRIRI','SE','siriri'),(1824,2807303,'TELHA','SE','telha'),(1825,2807402,'TOBIAS BARRETO','SE','tobias-barreto'),(1826,2807501,'TOMAR DO GERU','SE','tomar-do-geru'),(1827,2807600,'UMBAÚBA','SE','umbauba'),(1828,2900108,'ABAÍRA','BA','abaira'),(1829,2900207,'ABARÉ','BA','abare'),(1830,2900306,'ACAJUTIBA','BA','acajutiba'),(1831,2900355,'ADUSTINA','BA','adustina'),(1832,2900405,'ÁGUA FRIA','BA','agua-fria'),(1833,2900504,'ÉRICO CARDOSO','BA','erico-cardoso'),(1834,2900603,'AIQUARA','BA','aiquara'),(1835,2900702,'ALAGOINHAS','BA','alagoinhas'),(1836,2900801,'ALCOBAÇA','BA','alcobaca'),(1837,2900900,'ALMADINA','BA','almadina'),(1838,2901007,'AMARGOSA','BA','amargosa'),(1839,2901106,'AMÉLIA RODRIGUES','BA','amelia-rodrigues'),(1840,2901155,'AMÉRICA DOURADA','BA','america-dourada'),(1841,2901205,'ANAGÉ','BA','anage'),(1842,2901304,'ANDARAÍ','BA','andarai'),(1843,2901353,'ANDORINHA','BA','andorinha'),(1844,2901403,'ANGICAL','BA','angical'),(1845,2901502,'ANGUERA','BA','anguera'),(1846,2901601,'ANTAS','BA','antas'),(1847,2901700,'ANTÔNIO CARDOSO','BA','antonio-cardoso'),(1848,2901809,'ANTÔNIO GONÇALVES','BA','antonio-goncalves'),(1849,2901908,'APORÁ','BA','apora'),(1850,2901957,'APUAREMA','BA','apuarema'),(1851,2902005,'ARACATU','BA','aracatu'),(1852,2902054,'ARAÇAS','BA','aracas'),(1853,2902104,'ARACI','BA','araci'),(1854,2902203,'ARAMARI','BA','aramari'),(1855,2902252,'ARATACA','BA','arataca'),(1856,2902302,'ARATUÍPE','BA','aratuipe'),(1857,2902401,'AURELINO LEAL','BA','aurelino-leal'),(1858,2902500,'BAIANÓPOLIS','BA','baianopolis'),(1859,2902609,'BAIXA GRANDE','BA','baixa-grande'),(1860,2902658,'BANZAÊ','BA','banzae'),(1861,2902708,'BARRA','BA','barra'),(1862,2902807,'BARRA DA ESTIVA','BA','barra-da-estiva'),(1863,2902906,'BARRA DO CHOÇA','BA','barra-do-choca'),(1864,2903003,'BARRA DO MENDES','BA','barra-do-mendes'),(1865,2903102,'BARRA DO ROCHA','BA','barra-do-rocha'),(1866,2903201,'BARREIRAS','BA','barreiras'),(1867,2903235,'BARRO ALTO','BA','barro-alto'),(1868,2903276,'BARROCAS','BA','barrocas'),(1869,2903300,'BARRO PRETO','BA','barro-preto'),(1870,2903409,'BELMONTE','BA','belmonte'),(1871,2903508,'BELO CAMPO','BA','belo-campo'),(1872,2903607,'BIRITINGA','BA','biritinga'),(1873,2903706,'BOA NOVA','BA','boa-nova'),(1874,2903805,'BOA VISTA DO TUPIM','BA','boa-vista-do-tupim'),(1875,2903904,'BOM JESUS DA LAPA','BA','bom-jesus-da-lapa'),(1876,2903953,'BOM JESUS DA SERRA','BA','bom-jesus-da-serra'),(1877,2904001,'BONINAL','BA','boninal'),(1878,2904050,'BONITO','BA','bonito'),(1879,2904100,'BOQUIRA','BA','boquira'),(1880,2904209,'BOTUPORÃ','BA','botupora'),(1881,2904308,'BREJÕES','BA','brejoes'),(1882,2904407,'BREJOLÂNDIA','BA','brejolandia'),(1883,2904506,'BROTAS DE MACAÚBAS','BA','brotas-de-macaubas'),(1884,2904605,'BRUMADO','BA','brumado'),(1885,2904704,'BUERAREMA','BA','buerarema'),(1886,2904753,'BURITIRAMA','BA','buritirama'),(1887,2904803,'CAATIBA','BA','caatiba'),(1888,2904852,'CABACEIRAS DO PARAGUAÇU','BA','cabaceiras-do-paraguacu'),(1889,2904902,'CACHOEIRA','BA','cachoeira'),(1890,2905008,'CACULÉ','BA','cacule'),(1891,2905107,'CAÉM','BA','caem'),(1892,2905156,'CAETANOS','BA','caetanos'),(1893,2905206,'CAETITÉ','BA','caetite'),(1894,2905305,'CAFARNAUM','BA','cafarnaum'),(1895,2905404,'CAIRU','BA','cairu'),(1896,2905503,'CALDEIRÃO GRANDE','BA','caldeirao-grande'),(1897,2905602,'CAMACAN','BA','camacan'),(1898,2905701,'CAMAÇARI','BA','camacari'),(1899,2905800,'CAMAMU','BA','camamu'),(1900,2905909,'CAMPO ALEGRE DE LOURDES','BA','campo-alegre-de-lourdes'),(1901,2906006,'CAMPO FORMOSO','BA','campo-formoso'),(1902,2906105,'CANÁPOLIS','BA','canapolis'),(1903,2906204,'CANARANA','BA','canarana'),(1904,2906303,'CANAVIEIRAS','BA','canavieiras'),(1905,2906402,'CANDEAL','BA','candeal'),(1906,2906501,'CANDEIAS','BA','candeias'),(1907,2906600,'CANDIBA','BA','candiba'),(1908,2906709,'CÂNDIDO SALES','BA','candido-sales'),(1909,2906808,'CANSANÇÃO','BA','cansancao'),(1910,2906824,'CANUDOS','BA','canudos'),(1911,2906857,'CAPELA DO ALTO ALEGRE','BA','capela-do-alto-alegre'),(1912,2906873,'CAPIM GROSSO','BA','capim-grosso'),(1913,2906899,'CARAÍBAS','BA','caraibas'),(1914,2906907,'CARAVELAS','BA','caravelas'),(1915,2907004,'CARDEAL DA SILVA','BA','cardeal-da-silva'),(1916,2907103,'CARINHANHA','BA','carinhanha'),(1917,2907202,'CASA NOVA','BA','casa-nova'),(1918,2907301,'CASTRO ALVES','BA','castro-alves'),(1919,2907400,'CATOLÂNDIA','BA','catolandia'),(1920,2907509,'CATU','BA','catu'),(1921,2907558,'CATURAMA','BA','caturama'),(1922,2907608,'CENTRAL','BA','central'),(1923,2907707,'CHORROCHÓ','BA','chorrocho'),(1924,2907806,'CÍCERO DANTAS','BA','cicero-dantas'),(1925,2907905,'CIPÓ','BA','cipo'),(1926,2908002,'COARACI','BA','coaraci'),(1927,2908101,'COCOS','BA','cocos'),(1928,2908200,'CONCEIÇÃO DA FEIRA','BA','conceicao-da-feira'),(1929,2908309,'CONCEIÇÃO DO ALMEIDA','BA','conceicao-do-almeida'),(1930,2908408,'CONCEIÇÃO DO COITÉ','BA','conceicao-do-coite'),(1931,2908507,'CONCEIÇÃO DO JACUÍPE','BA','conceicao-do-jacuipe'),(1932,2908606,'CONDE','BA','conde'),(1933,2908705,'CONDEÚBA','BA','condeuba'),(1934,2908804,'CONTENDAS DO SINCORÁ','BA','contendas-do-sincora'),(1935,2908903,'CORAÇÃO DE MARIA','BA','coracao-de-maria'),(1936,2909000,'CORDEIROS','BA','cordeiros'),(1937,2909109,'CORIBE','BA','coribe'),(1938,2909208,'CORONEL JOÃO SÁ','BA','coronel-joao-sa'),(1939,2909307,'CORRENTINA','BA','correntina'),(1940,2909406,'COTEGIPE','BA','cotegipe'),(1941,2909505,'CRAVOLÂNDIA','BA','cravolandia'),(1942,2909604,'CRISÓPOLIS','BA','crisopolis'),(1943,2909703,'CRISTÓPOLIS','BA','cristopolis'),(1944,2909802,'CRUZ DAS ALMAS','BA','cruz-das-almas'),(1945,2909901,'CURAÇÁ','BA','curaca'),(1946,2910008,'DÁRIO MEIRA','BA','dario-meira'),(1947,2910057,'DIAS D\'ÁVILA','BA','dias-davila'),(1948,2910107,'DOM BASÍLIO','BA','dom-basilio'),(1949,2910206,'DOM MACEDO COSTA','BA','dom-macedo-costa'),(1950,2910305,'ELÍSIO MEDRADO','BA','elisio-medrado'),(1951,2910404,'ENCRUZILHADA','BA','encruzilhada'),(1952,2910503,'ENTRE RIOS','BA','entre-rios'),(1953,2910602,'ESPLANADA','BA','esplanada'),(1954,2910701,'EUCLIDES DA CUNHA','BA','euclides-da-cunha'),(1955,2910727,'EUNÁPOLIS','BA','eunapolis'),(1956,2910750,'FÁTIMA','BA','fatima'),(1957,2910776,'FEIRA DA MATA','BA','feira-da-mata'),(1958,2910800,'FEIRA DE SANTANA','BA','feira-de-santana'),(1959,2910859,'FILADÉLFIA','BA','filadelfia'),(1960,2910909,'FIRMINO ALVES','BA','firmino-alves'),(1961,2911006,'FLORESTA AZUL','BA','floresta-azul'),(1962,2911105,'FORMOSA DO RIO PRETO','BA','formosa-do-rio-preto'),(1963,2911204,'GANDU','BA','gandu'),(1964,2911253,'GAVIÃO','BA','gaviao'),(1965,2911303,'GENTIO DO OURO','BA','gentio-do-ouro'),(1966,2911402,'GLÓRIA','BA','gloria'),(1967,2911501,'GONGOGI','BA','gongogi'),(1968,2911600,'GOVERNADOR MANGABEIRA','BA','governador-mangabeira'),(1969,2911659,'GUAJERU','BA','guajeru'),(1970,2911709,'GUANAMBI','BA','guanambi'),(1971,2911808,'GUARATINGA','BA','guaratinga'),(1972,2911857,'HELIÓPOLIS','BA','heliopolis'),(1973,2911907,'IAÇU','BA','iacu'),(1974,2912004,'IBIASSUCÊ','BA','ibiassuce'),(1975,2912103,'IBICARAÍ','BA','ibicarai'),(1976,2912202,'IBICOARA','BA','ibicoara'),(1977,2912301,'IBICUÍ','BA','ibicui'),(1978,2912400,'IBIPEBA','BA','ibipeba'),(1979,2912509,'IBIPITANGA','BA','ibipitanga'),(1980,2912608,'IBIQUERA','BA','ibiquera'),(1981,2912707,'IBIRAPITANGA','BA','ibirapitanga'),(1982,2912806,'IBIRAPUÃ','BA','ibirapua'),(1983,2912905,'IBIRATAIA','BA','ibirataia'),(1984,2913002,'IBITIARA','BA','ibitiara'),(1985,2913101,'IBITITÁ','BA','ibitita'),(1986,2913200,'IBOTIRAMA','BA','ibotirama'),(1987,2913309,'ICHU','BA','ichu'),(1988,2913408,'IGAPORÃ','BA','igapora'),(1989,2913457,'IGRAPIÚNA','BA','igrapiuna'),(1990,2913507,'IGUAÍ','BA','iguai'),(1991,2913606,'ILHÉUS','BA','ilheus'),(1992,2913705,'INHAMBUPE','BA','inhambupe'),(1993,2913804,'IPECAETÁ','BA','ipecaeta'),(1994,2913903,'IPIAÚ','BA','ipiau'),(1995,2914000,'IPIRÁ','BA','ipira'),(1996,2914109,'IPUPIARA','BA','ipupiara'),(1997,2914208,'IRAJUBA','BA','irajuba'),(1998,2914307,'IRAMAIA','BA','iramaia'),(1999,2914406,'IRAQUARA','BA','iraquara'),(2000,2914505,'IRARÁ','BA','irara'),(2001,2914604,'IRECÊ','BA','irece'),(2002,2914653,'ITABELA','BA','itabela'),(2003,2914703,'ITABERABA','BA','itaberaba'),(2004,2914802,'ITABUNA','BA','itabuna'),(2005,2914901,'ITACARÉ','BA','itacare'),(2006,2915007,'ITAETÉ','BA','itaete'),(2007,2915106,'ITAGI','BA','itagi'),(2008,2915205,'ITAGIBÁ','BA','itagiba'),(2009,2915304,'ITAGIMIRIM','BA','itagimirim'),(2010,2915353,'ITAGUAÇU DA BAHIA','BA','itaguacu-da-bahia'),(2011,2915403,'ITAJU DO COLÔNIA','BA','itaju-do-colonia'),(2012,2915502,'ITAJUÍPE','BA','itajuipe'),(2013,2915601,'ITAMARAJU','BA','itamaraju'),(2014,2915700,'ITAMARI','BA','itamari'),(2015,2915809,'ITAMBÉ','BA','itambe'),(2016,2915908,'ITANAGRA','BA','itanagra'),(2017,2916005,'ITANHÉM','BA','itanhem'),(2018,2916104,'ITAPARICA','BA','itaparica'),(2019,2916203,'ITAPÉ','BA','itape'),(2020,2916302,'ITAPEBI','BA','itapebi'),(2021,2916401,'ITAPETINGA','BA','itapetinga'),(2022,2916500,'ITAPICURU','BA','itapicuru'),(2023,2916609,'ITAPITANGA','BA','itapitanga'),(2024,2916708,'ITAQUARA','BA','itaquara'),(2025,2916807,'ITARANTIM','BA','itarantim'),(2026,2916856,'ITATIM','BA','itatim'),(2027,2916906,'ITIRUÇU','BA','itirucu'),(2028,2917003,'ITIÚBA','BA','itiuba'),(2029,2917102,'ITORORÓ','BA','itororo'),(2030,2917201,'ITUAÇU','BA','ituacu'),(2031,2917300,'ITUBERÁ','BA','itubera'),(2032,2917334,'IUIÚ','BA','iuiu'),(2033,2917359,'JABORANDI','BA','jaborandi'),(2034,2917409,'JACARACI','BA','jacaraci'),(2035,2917508,'JACOBINA','BA','jacobina'),(2036,2917607,'JAGUAQUARA','BA','jaguaquara'),(2037,2917706,'JAGUARARI','BA','jaguarari'),(2038,2917805,'JAGUARIPE','BA','jaguaripe'),(2039,2917904,'JANDAÍRA','BA','jandaira'),(2040,2918001,'JEQUIÉ','BA','jequie'),(2041,2918100,'JEREMOABO','BA','jeremoabo'),(2042,2918209,'JIQUIRIÇÁ','BA','jiquirica'),(2043,2918308,'JITAÚNA','BA','jitauna'),(2044,2918357,'JOÃO DOURADO','BA','joao-dourado'),(2045,2918407,'JUAZEIRO','BA','juazeiro'),(2046,2918456,'JUCURUÇU','BA','jucurucu'),(2047,2918506,'JUSSARA','BA','jussara'),(2048,2918555,'JUSSARI','BA','jussari'),(2049,2918605,'JUSSIAPE','BA','jussiape'),(2050,2918704,'LAFAIETE COUTINHO','BA','lafaiete-coutinho'),(2051,2918753,'LAGOA REAL','BA','lagoa-real'),(2052,2918803,'LAJE','BA','laje'),(2053,2918902,'LAJEDÃO','BA','lajedao'),(2054,2919009,'LAJEDINHO','BA','lajedinho'),(2055,2919058,'LAJEDO DO TABOCAL','BA','lajedo-do-tabocal'),(2056,2919108,'LAMARÃO','BA','lamarao'),(2057,2919157,'LAPÃO','BA','lapao'),(2058,2919207,'LAURO DE FREITAS','BA','lauro-de-freitas'),(2059,2919306,'LENÇÓIS','BA','lencois'),(2060,2919405,'LICÍNIO DE ALMEIDA','BA','licinio-de-almeida'),(2061,2919504,'LIVRAMENTO DE NOSSA SENHORA','BA','livramento-de-nossa-senhora'),(2062,2919553,'LUÍS EDUARDO MAGALHÃES','BA','luis-eduardo-magalhaes'),(2063,2919603,'MACAJUBA','BA','macajuba'),(2064,2919702,'MACARANI','BA','macarani'),(2065,2919801,'MACAÚBAS','BA','macaubas'),(2066,2919900,'MACURURÉ','BA','macurure'),(2067,2919926,'MADRE DE DEUS','BA','madre-de-deus'),(2068,2919959,'MAETINGA','BA','maetinga'),(2069,2920007,'MAIQUINIQUE','BA','maiquinique'),(2070,2920106,'MAIRI','BA','mairi'),(2071,2920205,'MALHADA','BA','malhada'),(2072,2920304,'MALHADA DE PEDRAS','BA','malhada-de-pedras'),(2073,2920403,'MANOEL VITORINO','BA','manoel-vitorino'),(2074,2920452,'MANSIDÃO','BA','mansidao'),(2075,2920502,'MARACÁS','BA','maracas'),(2076,2920601,'MARAGOGIPE','BA','maragogipe'),(2077,2920700,'MARAÚ','BA','marau'),(2078,2920809,'MARCIONÍLIO SOUZA','BA','marcionilio-souza'),(2079,2920908,'MASCOTE','BA','mascote'),(2080,2921005,'MATA DE SÃO JOÃO','BA','mata-de-sao-joao'),(2081,2921054,'MATINA','BA','matina'),(2082,2921104,'MEDEIROS NETO','BA','medeiros-neto'),(2083,2921203,'MIGUEL CALMON','BA','miguel-calmon'),(2084,2921302,'MILAGRES','BA','milagres'),(2085,2921401,'MIRANGABA','BA','mirangaba'),(2086,2921450,'MIRANTE','BA','mirante'),(2087,2921500,'MONTE SANTO','BA','monte-santo'),(2088,2921609,'MORPARÁ','BA','morpara'),(2089,2921708,'MORRO DO CHAPÉU','BA','morro-do-chapeu'),(2090,2921807,'MORTUGABA','BA','mortugaba'),(2091,2921906,'MUCUGÊ','BA','mucuge'),(2092,2922003,'MUCURI','BA','mucuri'),(2093,2922052,'MULUNGU DO MORRO','BA','mulungu-do-morro'),(2094,2922102,'MUNDO NOVO','BA','mundo-novo'),(2095,2922201,'MUNIZ FERREIRA','BA','muniz-ferreira'),(2096,2922250,'MUQUÉM DE SÃO FRANCISCO','BA','muquem-de-sao-francisco'),(2097,2922300,'MURITIBA','BA','muritiba'),(2098,2922409,'MUTUÍPE','BA','mutuipe'),(2099,2922508,'NAZARÉ','BA','nazare'),(2100,2922607,'NILO PEÇANHA','BA','nilo-pecanha'),(2101,2922656,'NORDESTINA','BA','nordestina'),(2102,2922706,'NOVA CANAÃ','BA','nova-canaa'),(2103,2922730,'NOVA FÁTIMA','BA','nova-fatima'),(2104,2922755,'NOVA IBIÁ','BA','nova-ibia'),(2105,2922805,'NOVA ITARANA','BA','nova-itarana'),(2106,2922854,'NOVA REDENÇÃO','BA','nova-redencao'),(2107,2922904,'NOVA SOURE','BA','nova-soure'),(2108,2923001,'NOVA VIÇOSA','BA','nova-vicosa'),(2109,2923035,'NOVO HORIZONTE','BA','novo-horizonte'),(2110,2923050,'NOVO TRIUNFO','BA','novo-triunfo'),(2111,2923100,'OLINDINA','BA','olindina'),(2112,2923209,'OLIVEIRA DOS BREJINHOS','BA','oliveira-dos-brejinhos'),(2113,2923308,'OURIÇANGAS','BA','ouricangas'),(2114,2923357,'OUROLÂNDIA','BA','ourolandia'),(2115,2923407,'PALMAS DE MONTE ALTO','BA','palmas-de-monte-alto'),(2116,2923506,'PALMEIRAS','BA','palmeiras'),(2117,2923605,'PARAMIRIM','BA','paramirim'),(2118,2923704,'PARATINGA','BA','paratinga'),(2119,2923803,'PARIPIRANGA','BA','paripiranga'),(2120,2923902,'PAU BRASIL','BA','pau-brasil'),(2121,2924009,'PAULO AFONSO','BA','paulo-afonso'),(2122,2924058,'PÉ DE SERRA','BA','pe-de-serra'),(2123,2924108,'PEDRÃO','BA','pedrao'),(2124,2924207,'PEDRO ALEXANDRE','BA','pedro-alexandre'),(2125,2924306,'PIATÃ','BA','piata'),(2126,2924405,'PILÃO ARCADO','BA','pilao-arcado'),(2127,2924504,'PINDAÍ','BA','pindai'),(2128,2924603,'PINDOBAÇU','BA','pindobacu'),(2129,2924652,'PINTADAS','BA','pintadas'),(2130,2924678,'PIRAÍ DO NORTE','BA','pirai-do-norte'),(2131,2924702,'PIRIPÁ','BA','piripa'),(2132,2924801,'PIRITIBA','BA','piritiba'),(2133,2924900,'PLANALTINO','BA','planaltino'),(2134,2925006,'PLANALTO','BA','planalto'),(2135,2925105,'POÇÕES','BA','pocoes'),(2136,2925204,'POJUCA','BA','pojuca'),(2137,2925253,'PONTO NOVO','BA','ponto-novo'),(2138,2925303,'PORTO SEGURO','BA','porto-seguro'),(2139,2925402,'POTIRAGUÁ','BA','potiragua'),(2140,2925501,'PRADO','BA','prado'),(2141,2925600,'PRESIDENTE DUTRA','BA','presidente-dutra'),(2142,2925709,'PRESIDENTE JÂNIO QUADROS','BA','presidente-janio-quadros'),(2143,2925758,'PRESIDENTE TANCREDO NEVES','BA','presidente-tancredo-neves'),(2144,2925808,'QUEIMADAS','BA','queimadas'),(2145,2925907,'QUIJINGUE','BA','quijingue'),(2146,2925931,'QUIXABEIRA','BA','quixabeira'),(2147,2925956,'RAFAEL JAMBEIRO','BA','rafael-jambeiro'),(2148,2926004,'REMANSO','BA','remanso'),(2149,2926103,'RETIROLÂNDIA','BA','retirolandia'),(2150,2926202,'RIACHÃO DAS NEVES','BA','riachao-das-neves'),(2151,2926301,'RIACHÃO DO JACUÍPE','BA','riachao-do-jacuipe'),(2152,2926400,'RIACHO DE SANTANA','BA','riacho-de-santana'),(2153,2926509,'RIBEIRA DO AMPARO','BA','ribeira-do-amparo'),(2154,2926608,'RIBEIRA DO POMBAL','BA','ribeira-do-pombal'),(2155,2926657,'RIBEIRÃO DO LARGO','BA','ribeirao-do-largo'),(2156,2926707,'RIO DE CONTAS','BA','rio-de-contas'),(2157,2926806,'RIO DO ANTÔNIO','BA','rio-do-antonio'),(2158,2926905,'RIO DO PIRES','BA','rio-do-pires'),(2159,2927002,'RIO REAL','BA','rio-real'),(2160,2927101,'RODELAS','BA','rodelas'),(2161,2927200,'RUY BARBOSA','BA','ruy-barbosa'),(2162,2927309,'SALINAS DA MARGARIDA','BA','salinas-da-margarida'),(2163,2927408,'SALVADOR','BA','salvador'),(2164,2927507,'SANTA BÁRBARA','BA','santa-barbara'),(2165,2927606,'SANTA BRÍGIDA','BA','santa-brigida'),(2166,2927705,'SANTA CRUZ CABRÁLIA','BA','santa-cruz-cabralia'),(2167,2927804,'SANTA CRUZ DA VITÓRIA','BA','santa-cruz-da-vitoria'),(2168,2927903,'SANTA INÊS','BA','santa-ines'),(2169,2928000,'SANTALUZ','BA','santaluz'),(2170,2928059,'SANTA LUZIA','BA','santa-luzia'),(2171,2928109,'SANTA MARIA DA VITÓRIA','BA','santa-maria-da-vitoria'),(2172,2928208,'SANTANA','BA','santana'),(2173,2928307,'SANTANÓPOLIS','BA','santanopolis'),(2174,2928406,'SANTA RITA DE CÁSSIA','BA','santa-rita-de-cassia'),(2175,2928505,'SANTA TERESINHA','BA','santa-teresinha'),(2176,2928604,'SANTO AMARO','BA','santo-amaro'),(2177,2928703,'SANTO ANTÔNIO DE JESUS','BA','santo-antonio-de-jesus'),(2178,2928802,'SANTO ESTÊVÃO','BA','santo-estevao'),(2179,2928901,'SÃO DESIDÉRIO','BA','sao-desiderio'),(2180,2928950,'SÃO DOMINGOS','BA','sao-domingos'),(2181,2929008,'SÃO FÉLIX','BA','sao-felix'),(2182,2929057,'SÃO FÉLIX DO CORIBE','BA','sao-felix-do-coribe'),(2183,2929107,'SÃO FELIPE','BA','sao-felipe'),(2184,2929206,'SÃO FRANCISCO DO CONDE','BA','sao-francisco-do-conde'),(2185,2929255,'SÃO GABRIEL','BA','sao-gabriel'),(2186,2929305,'SÃO GONÇALO DOS CAMPOS','BA','sao-goncalo-dos-campos'),(2187,2929354,'SÃO JOSÉ DA VITÓRIA','BA','sao-jose-da-vitoria'),(2188,2929370,'SÃO JOSÉ DO JACUÍPE','BA','sao-jose-do-jacuipe'),(2189,2929404,'SÃO MIGUEL DAS MATAS','BA','sao-miguel-das-matas'),(2190,2929503,'SÃO SEBASTIÃO DO PASSÉ','BA','sao-sebastiao-do-passe'),(2191,2929602,'SAPEAÇU','BA','sapeacu'),(2192,2929701,'SÁTIRO DIAS','BA','satiro-dias'),(2193,2929750,'SAUBARA','BA','saubara'),(2194,2929800,'SAÚDE','BA','saude'),(2195,2929909,'SEABRA','BA','seabra'),(2196,2930006,'SEBASTIÃO LARANJEIRAS','BA','sebastiao-laranjeiras'),(2197,2930105,'SENHOR DO BONFIM','BA','senhor-do-bonfim'),(2198,2930154,'SERRA DO RAMALHO','BA','serra-do-ramalho'),(2199,2930204,'SENTO SÉ','BA','sento-se'),(2200,2930303,'SERRA DOURADA','BA','serra-dourada'),(2201,2930402,'SERRA PRETA','BA','serra-preta'),(2202,2930501,'SERRINHA','BA','serrinha'),(2203,2930600,'SERROLÂNDIA','BA','serrolandia'),(2204,2930709,'SIMÕES FILHO','BA','simoes-filho'),(2205,2930758,'SÍTIO DO MATO','BA','sitio-do-mato'),(2206,2930766,'SÍTIO DO QUINTO','BA','sitio-do-quinto'),(2207,2930774,'SOBRADINHO','BA','sobradinho'),(2208,2930808,'SOUTO SOARES','BA','souto-soares'),(2209,2930907,'TABOCAS DO BREJO VELHO','BA','tabocas-do-brejo-velho'),(2210,2931004,'TANHAÇU','BA','tanhacu'),(2211,2931053,'TANQUE NOVO','BA','tanque-novo'),(2212,2931103,'TANQUINHO','BA','tanquinho'),(2213,2931202,'TAPEROÁ','BA','taperoa'),(2214,2931301,'TAPIRAMUTÁ','BA','tapiramuta'),(2215,2931350,'TEIXEIRA DE FREITAS','BA','teixeira-de-freitas'),(2216,2931400,'TEODORO SAMPAIO','BA','teodoro-sampaio'),(2217,2931509,'TEOFILÂNDIA','BA','teofilandia'),(2218,2931608,'TEOLÂNDIA','BA','teolandia'),(2219,2931707,'TERRA NOVA','BA','terra-nova'),(2220,2931806,'TREMEDAL','BA','tremedal'),(2221,2931905,'TUCANO','BA','tucano'),(2222,2932002,'UAUÁ','BA','uaua'),(2223,2932101,'UBAÍRA','BA','ubaira'),(2224,2932200,'UBAITABA','BA','ubaitaba'),(2225,2932309,'UBATÃ','BA','ubata'),(2226,2932408,'UIBAÍ','BA','uibai'),(2227,2932457,'UMBURANAS','BA','umburanas'),(2228,2932507,'UNA','BA','una'),(2229,2932606,'URANDI','BA','urandi'),(2230,2932705,'URUÇUCA','BA','urucuca'),(2231,2932804,'UTINGA','BA','utinga'),(2232,2932903,'VALENÇA','BA','valenca'),(2233,2933000,'VALENTE','BA','valente'),(2234,2933059,'VÁRZEA DA ROÇA','BA','varzea-da-roca'),(2235,2933109,'VÁRZEA DO POÇO','BA','varzea-do-poco'),(2236,2933158,'VÁRZEA NOVA','BA','varzea-nova'),(2237,2933174,'VARZEDO','BA','varzedo'),(2238,2933208,'VERA CRUZ','BA','vera-cruz'),(2239,2933257,'VEREDA','BA','vereda'),(2240,2933307,'VITÓRIA DA CONQUISTA','BA','vitoria-da-conquista'),(2241,2933406,'WAGNER','BA','wagner'),(2242,2933455,'WANDERLEY','BA','wanderley'),(2243,2933505,'WENCESLAU GUIMARÃES','BA','wenceslau-guimaraes'),(2244,2933604,'XIQUE-XIQUE','BA','xique-xique'),(2245,3100104,'ABADIA DOS DOURADOS','MG','abadia-dos-dourados'),(2246,3100203,'ABAETÉ','MG','abaete'),(2247,3100302,'ABRE CAMPO','MG','abre-campo'),(2248,3100401,'ACAIACA','MG','acaiaca'),(2249,3100500,'AÇUCENA','MG','acucena'),(2250,3100609,'ÁGUA BOA','MG','agua-boa'),(2251,3100708,'ÁGUA COMPRIDA','MG','agua-comprida'),(2252,3100807,'AGUANIL','MG','aguanil'),(2253,3100906,'ÁGUAS FORMOSAS','MG','aguas-formosas'),(2254,3101003,'ÁGUAS VERMELHAS','MG','aguas-vermelhas'),(2255,3101102,'AIMORÉS','MG','aimores'),(2256,3101201,'AIURUOCA','MG','aiuruoca'),(2257,3101300,'ALAGOA','MG','alagoa'),(2258,3101409,'ALBERTINA','MG','albertina'),(2259,3101508,'ALÉM PARAÍBA','MG','alem-paraiba'),(2260,3101607,'ALFENAS','MG','alfenas'),(2261,3101631,'ALFREDO VASCONCELOS','MG','alfredo-vasconcelos'),(2262,3101706,'ALMENARA','MG','almenara'),(2263,3101805,'ALPERCATA','MG','alpercata'),(2264,3101904,'ALPINÓPOLIS','MG','alpinopolis'),(2265,3102001,'ALTEROSA','MG','alterosa'),(2266,3102050,'ALTO CAPARAÓ','MG','alto-caparao'),(2267,3102100,'ALTO RIO DOCE','MG','alto-rio-doce'),(2268,3102209,'ALVARENGA','MG','alvarenga'),(2269,3102308,'ALVINÓPOLIS','MG','alvinopolis'),(2270,3102407,'ALVORADA DE MINAS','MG','alvorada-de-minas'),(2271,3102506,'AMPARO DO SERRA','MG','amparo-do-serra'),(2272,3102605,'ANDRADAS','MG','andradas'),(2273,3102704,'CACHOEIRA DE PAJEÚ','MG','cachoeira-de-pajeu'),(2274,3102803,'ANDRELÂNDIA','MG','andrelandia'),(2275,3102852,'ANGELÂNDIA','MG','angelandia'),(2276,3102902,'ANTÔNIO CARLOS','MG','antonio-carlos'),(2277,3103009,'ANTÔNIO DIAS','MG','antonio-dias'),(2278,3103108,'ANTÔNIO PRADO DE MINAS','MG','antonio-prado-de-minas'),(2279,3103207,'ARAÇAÍ','MG','aracai'),(2280,3103306,'ARACITABA','MG','aracitaba'),(2281,3103405,'ARAÇUAÍ','MG','aracuai'),(2282,3103504,'ARAGUARI','MG','araguari'),(2283,3103603,'ARANTINA','MG','arantina'),(2284,3103702,'ARAPONGA','MG','araponga'),(2285,3103751,'ARAPORÃ','MG','arapora'),(2286,3103801,'ARAPUÁ','MG','arapua'),(2287,3103900,'ARAÚJOS','MG','araujos'),(2288,3104007,'ARAXÁ','MG','araxa'),(2289,3104106,'ARCEBURGO','MG','arceburgo'),(2290,3104205,'ARCOS','MG','arcos'),(2291,3104304,'AREADO','MG','areado'),(2292,3104403,'ARGIRITA','MG','argirita'),(2293,3104452,'ARICANDUVA','MG','aricanduva'),(2294,3104502,'ARINOS','MG','arinos'),(2295,3104601,'ASTOLFO DUTRA','MG','astolfo-dutra'),(2296,3104700,'ATALÉIA','MG','ataleia'),(2297,3104809,'AUGUSTO DE LIMA','MG','augusto-de-lima'),(2298,3104908,'BAEPENDI','MG','baependi'),(2299,3105004,'BALDIM','MG','baldim'),(2300,3105103,'BAMBUÍ','MG','bambui'),(2301,3105202,'BANDEIRA','MG','bandeira'),(2302,3105301,'BANDEIRA DO SUL','MG','bandeira-do-sul'),(2303,3105400,'BARÃO DE COCAIS','MG','barao-de-cocais'),(2304,3105509,'BARÃO DE MONTE ALTO','MG','barao-de-monte-alto'),(2305,3105608,'BARBACENA','MG','barbacena'),(2306,3105707,'BARRA LONGA','MG','barra-longa'),(2307,3105905,'BARROSO','MG','barroso'),(2308,3106002,'BELA VISTA DE MINAS','MG','bela-vista-de-minas'),(2309,3106101,'BELMIRO BRAGA','MG','belmiro-braga'),(2310,3106200,'BELO HORIZONTE','MG','belo-horizonte'),(2311,3106309,'BELO ORIENTE','MG','belo-oriente'),(2312,3106408,'BELO VALE','MG','belo-vale'),(2313,3106507,'BERILO','MG','berilo'),(2314,3106606,'BERTÓPOLIS','MG','bertopolis'),(2315,3106655,'BERIZAL','MG','berizal'),(2316,3106705,'BETIM','MG','betim'),(2317,3106804,'BIAS FORTES','MG','bias-fortes'),(2318,3106903,'BICAS','MG','bicas'),(2319,3107000,'BIQUINHAS','MG','biquinhas'),(2320,3107109,'BOA ESPERANÇA','MG','boa-esperanca'),(2321,3107208,'BOCAINA DE MINAS','MG','bocaina-de-minas'),(2322,3107307,'BOCAIÚVA','MG','bocaiuva'),(2323,3107406,'BOM DESPACHO','MG','bom-despacho'),(2324,3107505,'BOM JARDIM DE MINAS','MG','bom-jardim-de-minas'),(2325,3107604,'BOM JESUS DA PENHA','MG','bom-jesus-da-penha'),(2326,3107703,'BOM JESUS DO AMPARO','MG','bom-jesus-do-amparo'),(2327,3107802,'BOM JESUS DO GALHO','MG','bom-jesus-do-galho'),(2328,3107901,'BOM REPOUSO','MG','bom-repouso'),(2329,3108008,'BOM SUCESSO','MG','bom-sucesso'),(2330,3108107,'BONFIM','MG','bonfim'),(2331,3108206,'BONFINÓPOLIS DE MINAS','MG','bonfinopolis-de-minas'),(2332,3108255,'BONITO DE MINAS','MG','bonito-de-minas'),(2333,3108305,'BORDA DA MATA','MG','borda-da-mata'),(2334,3108404,'BOTELHOS','MG','botelhos'),(2335,3108503,'BOTUMIRIM','MG','botumirim'),(2336,3108552,'BRASILÂNDIA DE MINAS','MG','brasilandia-de-minas'),(2337,3108602,'BRASÍLIA DE MINAS','MG','brasilia-de-minas'),(2338,3108701,'BRÁS PIRES','MG','bras-pires'),(2339,3108800,'BRAÚNAS','MG','braunas'),(2340,3108909,'BRAZÓPOLIS','MG','brazopolis'),(2341,3109006,'BRUMADINHO','MG','brumadinho'),(2342,3109105,'BUENO BRANDÃO','MG','bueno-brandao'),(2343,3109204,'BUENÓPOLIS','MG','buenopolis'),(2344,3109253,'BUGRE','MG','bugre'),(2345,3109303,'BURITIS','MG','buritis'),(2346,3109402,'BURITIZEIRO','MG','buritizeiro'),(2347,3109451,'CABECEIRA GRANDE','MG','cabeceira-grande'),(2348,3109501,'CABO VERDE','MG','cabo-verde'),(2349,3109600,'CACHOEIRA DA PRATA','MG','cachoeira-da-prata'),(2350,3109709,'CACHOEIRA DE MINAS','MG','cachoeira-de-minas'),(2351,3109808,'CACHOEIRA DOURADA','MG','cachoeira-dourada'),(2352,3109907,'CAETANÓPOLIS','MG','caetanopolis'),(2353,3110004,'CAETÉ','MG','caete'),(2354,3110103,'CAIANA','MG','caiana'),(2355,3110202,'CAJURI','MG','cajuri'),(2356,3110301,'CALDAS','MG','caldas'),(2357,3110400,'CAMACHO','MG','camacho'),(2358,3110509,'CAMANDUCAIA','MG','camanducaia'),(2359,3110608,'CAMBUÍ','MG','cambui'),(2360,3110707,'CAMBUQUIRA','MG','cambuquira'),(2361,3110806,'CAMPANÁRIO','MG','campanario'),(2362,3110905,'CAMPANHA','MG','campanha'),(2363,3111002,'CAMPESTRE','MG','campestre'),(2364,3111101,'CAMPINA VERDE','MG','campina-verde'),(2365,3111150,'CAMPO AZUL','MG','campo-azul'),(2366,3111200,'CAMPO BELO','MG','campo-belo'),(2367,3111309,'CAMPO DO MEIO','MG','campo-do-meio'),(2368,3111408,'CAMPO FLORIDO','MG','campo-florido'),(2369,3111507,'CAMPOS ALTOS','MG','campos-altos'),(2370,3111606,'CAMPOS GERAIS','MG','campos-gerais'),(2371,3111705,'CANAÃ','MG','canaa'),(2372,3111804,'CANÁPOLIS','MG','canapolis'),(2373,3111903,'CANA VERDE','MG','cana-verde'),(2374,3112000,'CANDEIAS','MG','candeias'),(2375,3112059,'CANTAGALO','MG','cantagalo'),(2376,3112109,'CAPARAÓ','MG','caparao'),(2377,3112208,'CAPELA NOVA','MG','capela-nova'),(2378,3112307,'CAPELINHA','MG','capelinha'),(2379,3112406,'CAPETINGA','MG','capetinga'),(2380,3112505,'CAPIM BRANCO','MG','capim-branco'),(2381,3112604,'CAPINÓPOLIS','MG','capinopolis'),(2382,3112653,'CAPITÃO ANDRADE','MG','capitao-andrade'),(2383,3112703,'CAPITÃO ENÉAS','MG','capitao-eneas'),(2384,3112802,'CAPITÓLIO','MG','capitolio'),(2385,3112901,'CAPUTIRA','MG','caputira'),(2386,3113008,'CARAÍ','MG','carai'),(2387,3113107,'CARANAÍBA','MG','caranaiba'),(2388,3113206,'CARANDAÍ','MG','carandai'),(2389,3113305,'CARANGOLA','MG','carangola'),(2390,3113404,'CARATINGA','MG','caratinga'),(2391,3113503,'CARBONITA','MG','carbonita'),(2392,3113602,'CAREAÇU','MG','careacu'),(2393,3113701,'CARLOS CHAGAS','MG','carlos-chagas'),(2394,3113800,'CARMÉSIA','MG','carmesia'),(2395,3113909,'CARMO DA CACHOEIRA','MG','carmo-da-cachoeira'),(2396,3114006,'CARMO DA MATA','MG','carmo-da-mata'),(2397,3114105,'CARMO DE MINAS','MG','carmo-de-minas'),(2398,3114204,'CARMO DO CAJURU','MG','carmo-do-cajuru'),(2399,3114303,'CARMO DO PARANAÍBA','MG','carmo-do-paranaiba'),(2400,3114402,'CARMO DO RIO CLARO','MG','carmo-do-rio-claro'),(2401,3114501,'CARMÓPOLIS DE MINAS','MG','carmopolis-de-minas'),(2402,3114550,'CARNEIRINHO','MG','carneirinho'),(2403,3114600,'CARRANCAS','MG','carrancas'),(2404,3114709,'CARVALHÓPOLIS','MG','carvalhopolis'),(2405,3114808,'CARVALHOS','MG','carvalhos'),(2406,3114907,'CASA GRANDE','MG','casa-grande'),(2407,3115003,'CASCALHO RICO','MG','cascalho-rico'),(2408,3115102,'CÁSSIA','MG','cassia'),(2409,3115201,'CONCEIÇÃO DA BARRA DE MINAS','MG','conceicao-da-barra-de-minas'),(2410,3115300,'CATAGUASES','MG','cataguases'),(2411,3115359,'CATAS ALTAS','MG','catas-altas'),(2412,3115409,'CATAS ALTAS DA NORUEGA','MG','catas-altas-da-noruega'),(2413,3115458,'CATUJI','MG','catuji'),(2414,3115474,'CATUTI','MG','catuti'),(2415,3115508,'CAXAMBU','MG','caxambu'),(2416,3115607,'CEDRO DO ABAETÉ','MG','cedro-do-abaete'),(2417,3115706,'CENTRAL DE MINAS','MG','central-de-minas'),(2418,3115805,'CENTRALINA','MG','centralina'),(2419,3115904,'CHÁCARA','MG','chacara'),(2420,3116001,'CHALÉ','MG','chale'),(2421,3116100,'CHAPADA DO NORTE','MG','chapada-do-norte'),(2422,3116159,'CHAPADA GAÚCHA','MG','chapada-gaucha'),(2423,3116209,'CHIADOR','MG','chiador'),(2424,3116308,'CIPOTÂNEA','MG','cipotanea'),(2425,3116407,'CLARAVAL','MG','claraval'),(2426,3116506,'CLARO DOS POÇÕES','MG','claro-dos-pocoes'),(2427,3116605,'CLÁUDIO','MG','claudio'),(2428,3116704,'COIMBRA','MG','coimbra'),(2429,3116803,'COLUNA','MG','coluna'),(2430,3116902,'COMENDADOR GOMES','MG','comendador-gomes'),(2431,3117009,'COMERCINHO','MG','comercinho'),(2432,3117108,'CONCEIÇÃO DA APARECIDA','MG','conceicao-da-aparecida'),(2433,3117207,'CONCEIÇÃO DAS PEDRAS','MG','conceicao-das-pedras'),(2434,3117306,'CONCEIÇÃO DAS ALAGOAS','MG','conceicao-das-alagoas'),(2435,3117405,'CONCEIÇÃO DE IPANEMA','MG','conceicao-de-ipanema'),(2436,3117504,'CONCEIÇÃO DO MATO DENTRO','MG','conceicao-do-mato-dentro'),(2437,3117603,'CONCEIÇÃO DO PARÁ','MG','conceicao-do-para'),(2438,3117702,'CONCEIÇÃO DO RIO VERDE','MG','conceicao-do-rio-verde'),(2439,3117801,'CONCEIÇÃO DOS OUROS','MG','conceicao-dos-ouros'),(2440,3117836,'CÔNEGO MARINHO','MG','conego-marinho'),(2441,3117876,'CONFINS','MG','confins'),(2442,3117900,'CONGONHAL','MG','congonhal'),(2443,3118007,'CONGONHAS','MG','congonhas'),(2444,3118106,'CONGONHAS DO NORTE','MG','congonhas-do-norte'),(2445,3118205,'CONQUISTA','MG','conquista'),(2446,3118304,'CONSELHEIRO LAFAIETE','MG','conselheiro-lafaiete'),(2447,3118403,'CONSELHEIRO PENA','MG','conselheiro-pena'),(2448,3118502,'CONSOLAÇÃO','MG','consolacao'),(2449,3118601,'CONTAGEM','MG','contagem'),(2450,3118700,'COQUEIRAL','MG','coqueiral'),(2451,3118809,'CORAÇÃO DE JESUS','MG','coracao-de-jesus'),(2452,3118908,'CORDISBURGO','MG','cordisburgo'),(2453,3119005,'CORDISLÂNDIA','MG','cordislandia'),(2454,3119104,'CORINTO','MG','corinto'),(2455,3119203,'COROACI','MG','coroaci'),(2456,3119302,'COROMANDEL','MG','coromandel'),(2457,3119401,'CORONEL FABRICIANO','MG','coronel-fabriciano'),(2458,3119500,'CORONEL MURTA','MG','coronel-murta'),(2459,3119609,'CORONEL PACHECO','MG','coronel-pacheco'),(2460,3119708,'CORONEL XAVIER CHAVES','MG','coronel-xavier-chaves'),(2461,3119807,'CÓRREGO DANTA','MG','corrego-danta'),(2462,3119906,'CÓRREGO DO BOM JESUS','MG','corrego-do-bom-jesus'),(2463,3119955,'CÓRREGO FUNDO','MG','corrego-fundo'),(2464,3120003,'CÓRREGO NOVO','MG','corrego-novo'),(2465,3120102,'COUTO DE MAGALHÃES DE MINAS','MG','couto-de-magalhaes-de-minas'),(2466,3120151,'CRISÓLITA','MG','crisolita'),(2467,3120201,'CRISTAIS','MG','cristais'),(2468,3120300,'CRISTÁLIA','MG','cristalia'),(2469,3120409,'CRISTIANO OTONI','MG','cristiano-otoni'),(2470,3120508,'CRISTINA','MG','cristina'),(2471,3120607,'CRUCILÂNDIA','MG','crucilandia'),(2472,3120706,'CRUZEIRO DA FORTALEZA','MG','cruzeiro-da-fortaleza'),(2473,3120805,'CRUZÍLIA','MG','cruzilia'),(2474,3120839,'CUPARAQUE','MG','cuparaque'),(2475,3120870,'CURRAL DE DENTRO','MG','curral-de-dentro'),(2476,3120904,'CURVELO','MG','curvelo'),(2477,3121001,'DATAS','MG','datas'),(2478,3121100,'DELFIM MOREIRA','MG','delfim-moreira'),(2479,3121209,'DELFINÓPOLIS','MG','delfinopolis'),(2480,3121258,'DELTA','MG','delta'),(2481,3121308,'DESCOBERTO','MG','descoberto'),(2482,3121407,'DESTERRO DE ENTRE RIOS','MG','desterro-de-entre-rios'),(2483,3121506,'DESTERRO DO MELO','MG','desterro-do-melo'),(2484,3121605,'DIAMANTINA','MG','diamantina'),(2485,3121704,'DIOGO DE VASCONCELOS','MG','diogo-de-vasconcelos'),(2486,3121803,'DIONÍSIO','MG','dionisio'),(2487,3121902,'DIVINÉSIA','MG','divinesia'),(2488,3122009,'DIVINO','MG','divino'),(2489,3122108,'DIVINO DAS LARANJEIRAS','MG','divino-das-laranjeiras'),(2490,3122207,'DIVINOLÂNDIA DE MINAS','MG','divinolandia-de-minas'),(2491,3122306,'DIVINÓPOLIS','MG','divinopolis'),(2492,3122355,'DIVISA ALEGRE','MG','divisa-alegre'),(2493,3122405,'DIVISA NOVA','MG','divisa-nova'),(2494,3122454,'DIVISÓPOLIS','MG','divisopolis'),(2495,3122470,'DOM BOSCO','MG','dom-bosco'),(2496,3122504,'DOM CAVATI','MG','dom-cavati'),(2497,3122603,'DOM JOAQUIM','MG','dom-joaquim'),(2498,3122702,'DOM SILVÉRIO','MG','dom-silverio'),(2499,3122801,'DOM VIÇOSO','MG','dom-vicoso'),(2500,3122900,'DONA EUSÉBIA','MG','dona-eusebia'),(2501,3123007,'DORES DE CAMPOS','MG','dores-de-campos'),(2502,3123106,'DORES DE GUANHÃES','MG','dores-de-guanhaes'),(2503,3123205,'DORES DO INDAIÁ','MG','dores-do-indaia'),(2504,3123304,'DORES DO TURVO','MG','dores-do-turvo'),(2505,3123403,'DORESÓPOLIS','MG','doresopolis'),(2506,3123502,'DOURADOQUARA','MG','douradoquara'),(2507,3123528,'DURANDÉ','MG','durande'),(2508,3123601,'ELÓI MENDES','MG','eloi-mendes'),(2509,3123700,'ENGENHEIRO CALDAS','MG','engenheiro-caldas'),(2510,3123809,'ENGENHEIRO NAVARRO','MG','engenheiro-navarro'),(2511,3123858,'ENTRE FOLHAS','MG','entre-folhas'),(2512,3123908,'ENTRE RIOS DE MINAS','MG','entre-rios-de-minas'),(2513,3124005,'ERVÁLIA','MG','ervalia'),(2514,3124104,'ESMERALDAS','MG','esmeraldas'),(2515,3124203,'ESPERA FELIZ','MG','espera-feliz'),(2516,3124302,'ESPINOSA','MG','espinosa'),(2517,3124401,'ESPÍRITO SANTO DO DOURADO','MG','espirito-santo-do-dourado'),(2518,3124500,'ESTIVA','MG','estiva'),(2519,3124609,'ESTRELA DALVA','MG','estrela-dalva'),(2520,3124708,'ESTRELA DO INDAIÁ','MG','estrela-do-indaia'),(2521,3124807,'ESTRELA DO SUL','MG','estrela-do-sul'),(2522,3124906,'EUGENÓPOLIS','MG','eugenopolis'),(2523,3125002,'EWBANK DA CÂMARA','MG','ewbank-da-camara'),(2524,3125101,'EXTREMA','MG','extrema'),(2525,3125200,'FAMA','MG','fama'),(2526,3125309,'FARIA LEMOS','MG','faria-lemos'),(2527,3125408,'FELÍCIO DOS SANTOS','MG','felicio-dos-santos'),(2528,3125507,'SÃO GONÇALO DO RIO PRETO','MG','sao-goncalo-do-rio-preto'),(2529,3125606,'FELISBURGO','MG','felisburgo'),(2530,3125705,'FELIXLÂNDIA','MG','felixlandia'),(2531,3125804,'FERNANDES TOURINHO','MG','fernandes-tourinho'),(2532,3125903,'FERROS','MG','ferros'),(2533,3125952,'FERVEDOURO','MG','fervedouro'),(2534,3126000,'FLORESTAL','MG','florestal'),(2535,3126109,'FORMIGA','MG','formiga'),(2536,3126208,'FORMOSO','MG','formoso'),(2537,3126307,'FORTALEZA DE MINAS','MG','fortaleza-de-minas'),(2538,3126406,'FORTUNA DE MINAS','MG','fortuna-de-minas'),(2539,3126505,'FRANCISCO BADARÓ','MG','francisco-badaro'),(2540,3126604,'FRANCISCO DUMONT','MG','francisco-dumont'),(2541,3126703,'FRANCISCO SÁ','MG','francisco-sa'),(2542,3126752,'FRANCISCÓPOLIS','MG','franciscopolis'),(2543,3126802,'FREI GASPAR','MG','frei-gaspar'),(2544,3126901,'FREI INOCÊNCIO','MG','frei-inocencio'),(2545,3126950,'FREI LAGONEGRO','MG','frei-lagonegro'),(2546,3127008,'FRONTEIRA','MG','fronteira'),(2547,3127057,'FRONTEIRA DOS VALES','MG','fronteira-dos-vales'),(2548,3127073,'FRUTA DE LEITE','MG','fruta-de-leite'),(2549,3127107,'FRUTAL','MG','frutal'),(2550,3127206,'FUNILÂNDIA','MG','funilandia'),(2551,3127305,'GALILÉIA','MG','galileia'),(2552,3127339,'GAMELEIRAS','MG','gameleiras'),(2553,3127354,'GLAUCILÂNDIA','MG','glaucilandia'),(2554,3127370,'GOIABEIRA','MG','goiabeira'),(2555,3127388,'GOIANÁ','MG','goiana'),(2556,3127404,'GONÇALVES','MG','goncalves'),(2557,3127503,'GONZAGA','MG','gonzaga'),(2558,3127602,'GOUVEIA','MG','gouveia'),(2559,3127701,'GOVERNADOR VALADARES','MG','governador-valadares'),(2560,3127800,'GRÃO MOGOL','MG','grao-mogol'),(2561,3127909,'GRUPIARA','MG','grupiara'),(2562,3128006,'GUANHÃES','MG','guanhaes'),(2563,3128105,'GUAPÉ','MG','guape'),(2564,3128204,'GUARACIABA','MG','guaraciaba'),(2565,3128253,'GUARACIAMA','MG','guaraciama'),(2566,3128303,'GUARANÉSIA','MG','guaranesia'),(2567,3128402,'GUARANI','MG','guarani'),(2568,3128501,'GUARARÁ','MG','guarara'),(2569,3128600,'GUARDA-MOR','MG','guarda-mor'),(2570,3128709,'GUAXUPÉ','MG','guaxupe'),(2571,3128808,'GUIDOVAL','MG','guidoval'),(2572,3128907,'GUIMARÂNIA','MG','guimarania'),(2573,3129004,'GUIRICEMA','MG','guiricema'),(2574,3129103,'GURINHATÃ','MG','gurinhata'),(2575,3129202,'HELIODORA','MG','heliodora'),(2576,3129301,'IAPU','MG','iapu'),(2577,3129400,'IBERTIOGA','MG','ibertioga'),(2578,3129509,'IBIÁ','MG','ibia'),(2579,3129608,'IBIAÍ','MG','ibiai'),(2580,3129657,'IBIRACATU','MG','ibiracatu'),(2581,3129707,'IBIRACI','MG','ibiraci'),(2582,3129806,'IBIRITÉ','MG','ibirite'),(2583,3129905,'IBITIÚRA DE MINAS','MG','ibitiura-de-minas'),(2584,3130002,'IBITURUNA','MG','ibituruna'),(2585,3130051,'ICARAÍ DE MINAS','MG','icarai-de-minas'),(2586,3130101,'IGARAPÉ','MG','igarape'),(2587,3130200,'IGARATINGA','MG','igaratinga'),(2588,3130309,'IGUATAMA','MG','iguatama'),(2589,3130408,'IJACI','MG','ijaci'),(2590,3130507,'ILICÍNEA','MG','ilicinea'),(2591,3130556,'IMBÉ DE MINAS','MG','imbe-de-minas'),(2592,3130606,'INCONFIDENTES','MG','inconfidentes'),(2593,3130655,'INDAIABIRA','MG','indaiabira'),(2594,3130705,'INDIANÓPOLIS','MG','indianopolis'),(2595,3130804,'INGAÍ','MG','ingai'),(2596,3130903,'INHAPIM','MG','inhapim'),(2597,3131000,'INHAÚMA','MG','inhauma'),(2598,3131109,'INIMUTABA','MG','inimutaba'),(2599,3131158,'IPABA','MG','ipaba'),(2600,3131208,'IPANEMA','MG','ipanema'),(2601,3131307,'IPATINGA','MG','ipatinga'),(2602,3131406,'IPIAÇU','MG','ipiacu'),(2603,3131505,'IPUIÚNA','MG','ipuiuna'),(2604,3131604,'IRAÍ DE MINAS','MG','irai-de-minas'),(2605,3131703,'ITABIRA','MG','itabira'),(2606,3131802,'ITABIRINHA','MG','itabirinha'),(2607,3131901,'ITABIRITO','MG','itabirito'),(2608,3132008,'ITACAMBIRA','MG','itacambira'),(2609,3132107,'ITACARAMBI','MG','itacarambi'),(2610,3132206,'ITAGUARA','MG','itaguara'),(2611,3132305,'ITAIPÉ','MG','itaipe'),(2612,3132404,'ITAJUBÁ','MG','itajuba'),(2613,3132503,'ITAMARANDIBA','MG','itamarandiba'),(2614,3132602,'ITAMARATI DE MINAS','MG','itamarati-de-minas'),(2615,3132701,'ITAMBACURI','MG','itambacuri'),(2616,3132800,'ITAMBÉ DO MATO DENTRO','MG','itambe-do-mato-dentro'),(2617,3132909,'ITAMOGI','MG','itamogi'),(2618,3133006,'ITAMONTE','MG','itamonte'),(2619,3133105,'ITANHANDU','MG','itanhandu'),(2620,3133204,'ITANHOMI','MG','itanhomi'),(2621,3133303,'ITAOBIM','MG','itaobim'),(2622,3133402,'ITAPAGIPE','MG','itapagipe'),(2623,3133501,'ITAPECERICA','MG','itapecerica'),(2624,3133600,'ITAPEVA','MG','itapeva'),(2625,3133709,'ITATIAIUÇU','MG','itatiaiucu'),(2626,3133758,'ITAÚ DE MINAS','MG','itau-de-minas'),(2627,3133808,'ITAÚNA','MG','itauna'),(2628,3133907,'ITAVERAVA','MG','itaverava'),(2629,3134004,'ITINGA','MG','itinga'),(2630,3134103,'ITUETA','MG','itueta'),(2631,3134202,'ITUIUTABA','MG','ituiutaba'),(2632,3134301,'ITUMIRIM','MG','itumirim'),(2633,3134400,'ITURAMA','MG','iturama'),(2634,3134509,'ITUTINGA','MG','itutinga'),(2635,3134608,'JABOTICATUBAS','MG','jaboticatubas'),(2636,3134707,'JACINTO','MG','jacinto'),(2637,3134806,'JACUÍ','MG','jacui'),(2638,3134905,'JACUTINGA','MG','jacutinga'),(2639,3135001,'JAGUARAÇU','MG','jaguaracu'),(2640,3135050,'JAÍBA','MG','jaiba'),(2641,3135076,'JAMPRUCA','MG','jampruca'),(2642,3135100,'JANAÚBA','MG','janauba'),(2643,3135209,'JANUÁRIA','MG','januaria'),(2644,3135308,'JAPARAÍBA','MG','japaraiba'),(2645,3135357,'JAPONVAR','MG','japonvar'),(2646,3135407,'JECEABA','MG','jeceaba'),(2647,3135456,'JENIPAPO DE MINAS','MG','jenipapo-de-minas'),(2648,3135506,'JEQUERI','MG','jequeri'),(2649,3135605,'JEQUITAÍ','MG','jequitai'),(2650,3135704,'JEQUITIBÁ','MG','jequitiba'),(2651,3135803,'JEQUITINHONHA','MG','jequitinhonha'),(2652,3135902,'JESUÂNIA','MG','jesuania'),(2653,3136009,'JOAÍMA','MG','joaima'),(2654,3136108,'JOANÉSIA','MG','joanesia'),(2655,3136207,'JOÃO MONLEVADE','MG','joao-monlevade'),(2656,3136306,'JOÃO PINHEIRO','MG','joao-pinheiro'),(2657,3136405,'JOAQUIM FELÍCIO','MG','joaquim-felicio'),(2658,3136504,'JORDÂNIA','MG','jordania'),(2659,3136520,'JOSÉ GONÇALVES DE MINAS','MG','jose-goncalves-de-minas'),(2660,3136553,'JOSÉ RAYDAN','MG','jose-raydan'),(2661,3136579,'JOSENÓPOLIS','MG','josenopolis'),(2662,3136603,'NOVA UNIÃO','MG','nova-uniao'),(2663,3136652,'JUATUBA','MG','juatuba'),(2664,3136702,'JUIZ DE FORA','MG','juiz-de-fora'),(2665,3136801,'JURAMENTO','MG','juramento'),(2666,3136900,'JURUAIA','MG','juruaia'),(2667,3136959,'JUVENÍLIA','MG','juvenilia'),(2668,3137007,'LADAINHA','MG','ladainha'),(2669,3137106,'LAGAMAR','MG','lagamar'),(2670,3137205,'LAGOA DA PRATA','MG','lagoa-da-prata'),(2671,3137304,'LAGOA DOS PATOS','MG','lagoa-dos-patos'),(2672,3137403,'LAGOA DOURADA','MG','lagoa-dourada'),(2673,3137502,'LAGOA FORMOSA','MG','lagoa-formosa'),(2674,3137536,'LAGOA GRANDE','MG','lagoa-grande'),(2675,3137601,'LAGOA SANTA','MG','lagoa-santa'),(2676,3137700,'LAJINHA','MG','lajinha'),(2677,3137809,'LAMBARI','MG','lambari'),(2678,3137908,'LAMIM','MG','lamim'),(2679,3138005,'LARANJAL','MG','laranjal'),(2680,3138104,'LASSANCE','MG','lassance'),(2681,3138203,'LAVRAS','MG','lavras'),(2682,3138302,'LEANDRO FERREIRA','MG','leandro-ferreira'),(2683,3138351,'LEME DO PRADO','MG','leme-do-prado'),(2684,3138401,'LEOPOLDINA','MG','leopoldina'),(2685,3138500,'LIBERDADE','MG','liberdade'),(2686,3138609,'LIMA DUARTE','MG','lima-duarte'),(2687,3138625,'LIMEIRA DO OESTE','MG','limeira-do-oeste'),(2688,3138658,'LONTRA','MG','lontra'),(2689,3138674,'LUISBURGO','MG','luisburgo'),(2690,3138682,'LUISLÂNDIA','MG','luislandia'),(2691,3138708,'LUMINÁRIAS','MG','luminarias'),(2692,3138807,'LUZ','MG','luz'),(2693,3138906,'MACHACALIS','MG','machacalis'),(2694,3139003,'MACHADO','MG','machado'),(2695,3139102,'MADRE DE DEUS DE MINAS','MG','madre-de-deus-de-minas'),(2696,3139201,'MALACACHETA','MG','malacacheta'),(2697,3139250,'MAMONAS','MG','mamonas'),(2698,3139300,'MANGA','MG','manga'),(2699,3139409,'MANHUAÇU','MG','manhuacu'),(2700,3139508,'MANHUMIRIM','MG','manhumirim'),(2701,3139607,'MANTENA','MG','mantena'),(2702,3139706,'MARAVILHAS','MG','maravilhas'),(2703,3139805,'MAR DE ESPANHA','MG','mar-de-espanha'),(2704,3139904,'MARIA DA FÉ','MG','maria-da-fe'),(2705,3140001,'MARIANA','MG','mariana'),(2706,3140100,'MARILAC','MG','marilac'),(2707,3140159,'MÁRIO CAMPOS','MG','mario-campos'),(2708,3140209,'MARIPÁ DE MINAS','MG','maripa-de-minas'),(2709,3140308,'MARLIÉRIA','MG','marlieria'),(2710,3140407,'MARMELÓPOLIS','MG','marmelopolis'),(2711,3140506,'MARTINHO CAMPOS','MG','martinho-campos'),(2712,3140530,'MARTINS SOARES','MG','martins-soares'),(2713,3140555,'MATA VERDE','MG','mata-verde'),(2714,3140605,'MATERLÂNDIA','MG','materlandia'),(2715,3140704,'MATEUS LEME','MG','mateus-leme'),(2716,3140803,'MATIAS BARBOSA','MG','matias-barbosa'),(2717,3140852,'MATIAS CARDOSO','MG','matias-cardoso'),(2718,3140902,'MATIPÓ','MG','matipo'),(2719,3141009,'MATO VERDE','MG','mato-verde'),(2720,3141108,'MATOZINHOS','MG','matozinhos'),(2721,3141207,'MATUTINA','MG','matutina'),(2722,3141306,'MEDEIROS','MG','medeiros'),(2723,3141405,'MEDINA','MG','medina'),(2724,3141504,'MENDES PIMENTEL','MG','mendes-pimentel'),(2725,3141603,'MERCÊS','MG','merces'),(2726,3141702,'MESQUITA','MG','mesquita'),(2727,3141801,'MINAS NOVAS','MG','minas-novas'),(2728,3141900,'MINDURI','MG','minduri'),(2729,3142007,'MIRABELA','MG','mirabela'),(2730,3142106,'MIRADOURO','MG','miradouro'),(2731,3142205,'MIRAÍ','MG','mirai'),(2732,3142254,'MIRAVÂNIA','MG','miravania'),(2733,3142304,'MOEDA','MG','moeda'),(2734,3142403,'MOEMA','MG','moema'),(2735,3142502,'MONJOLOS','MG','monjolos'),(2736,3142601,'MONSENHOR PAULO','MG','monsenhor-paulo'),(2737,3142700,'MONTALVÂNIA','MG','montalvania'),(2738,3142809,'MONTE ALEGRE DE MINAS','MG','monte-alegre-de-minas'),(2739,3142908,'MONTE AZUL','MG','monte-azul'),(2740,3143005,'MONTE BELO','MG','monte-belo'),(2741,3143104,'MONTE CARMELO','MG','monte-carmelo'),(2742,3143153,'MONTE FORMOSO','MG','monte-formoso'),(2743,3143203,'MONTE SANTO DE MINAS','MG','monte-santo-de-minas'),(2744,3143302,'MONTES CLAROS','MG','montes-claros'),(2745,3143401,'MONTE SIÃO','MG','monte-siao'),(2746,3143450,'MONTEZUMA','MG','montezuma'),(2747,3143500,'MORADA NOVA DE MINAS','MG','morada-nova-de-minas'),(2748,3143609,'MORRO DA GARÇA','MG','morro-da-garca'),(2749,3143708,'MORRO DO PILAR','MG','morro-do-pilar'),(2750,3143807,'MUNHOZ','MG','munhoz'),(2751,3143906,'MURIAÉ','MG','muriae'),(2752,3144003,'MUTUM','MG','mutum'),(2753,3144102,'MUZAMBINHO','MG','muzambinho'),(2754,3144201,'NACIP RAYDAN','MG','nacip-raydan'),(2755,3144300,'NANUQUE','MG','nanuque'),(2756,3144359,'NAQUE','MG','naque'),(2757,3144375,'NATALÂNDIA','MG','natalandia'),(2758,3144409,'NATÉRCIA','MG','natercia'),(2759,3144508,'NAZARENO','MG','nazareno'),(2760,3144607,'NEPOMUCENO','MG','nepomuceno'),(2761,3144656,'NINHEIRA','MG','ninheira'),(2762,3144672,'NOVA BELÉM','MG','nova-belem'),(2763,3144706,'NOVA ERA','MG','nova-era'),(2764,3144805,'NOVA LIMA','MG','nova-lima'),(2765,3144904,'NOVA MÓDICA','MG','nova-modica'),(2766,3145000,'NOVA PONTE','MG','nova-ponte'),(2767,3145059,'NOVA PORTEIRINHA','MG','nova-porteirinha'),(2768,3145109,'NOVA RESENDE','MG','nova-resende'),(2769,3145208,'NOVA SERRANA','MG','nova-serrana'),(2770,3145307,'NOVO CRUZEIRO','MG','novo-cruzeiro'),(2771,3145356,'NOVO ORIENTE DE MINAS','MG','novo-oriente-de-minas'),(2772,3145372,'NOVORIZONTE','MG','novorizonte'),(2773,3145406,'OLARIA','MG','olaria'),(2774,3145455,'OLHOS-D\'ÁGUA','MG','olhos-dagua'),(2775,3145505,'OLÍMPIO NORONHA','MG','olimpio-noronha'),(2776,3145604,'OLIVEIRA','MG','oliveira'),(2777,3145703,'OLIVEIRA FORTES','MG','oliveira-fortes'),(2778,3145802,'ONÇA DE PITANGUI','MG','onca-de-pitangui'),(2779,3145851,'ORATÓRIOS','MG','oratorios'),(2780,3145877,'ORIZÂNIA','MG','orizania'),(2781,3145901,'OURO BRANCO','MG','ouro-branco'),(2782,3146008,'OURO FINO','MG','ouro-fino'),(2783,3146107,'OURO PRETO','MG','ouro-preto'),(2784,3146206,'OURO VERDE DE MINAS','MG','ouro-verde-de-minas'),(2785,3146255,'PADRE CARVALHO','MG','padre-carvalho'),(2786,3146305,'PADRE PARAÍSO','MG','padre-paraiso'),(2787,3146404,'PAINEIRAS','MG','paineiras'),(2788,3146503,'PAINS','MG','pains'),(2789,3146552,'PAI PEDRO','MG','pai-pedro'),(2790,3146602,'PAIVA','MG','paiva'),(2791,3146701,'PALMA','MG','palma'),(2792,3146750,'PALMÓPOLIS','MG','palmopolis'),(2793,3146909,'PAPAGAIOS','MG','papagaios'),(2794,3147006,'PARACATU','MG','paracatu'),(2795,3147105,'PARÁ DE MINAS','MG','para-de-minas'),(2796,3147204,'PARAGUAÇU','MG','paraguacu'),(2797,3147303,'PARAISÓPOLIS','MG','paraisopolis'),(2798,3147402,'PARAOPEBA','MG','paraopeba'),(2799,3147501,'PASSABÉM','MG','passabem'),(2800,3147600,'PASSA QUATRO','MG','passa-quatro'),(2801,3147709,'PASSA TEMPO','MG','passa-tempo'),(2802,3147808,'PASSA-VINTE','MG','passa-vinte'),(2803,3147907,'PASSOS','MG','passos'),(2804,3147956,'PATIS','MG','patis'),(2805,3148004,'PATOS DE MINAS','MG','patos-de-minas'),(2806,3148103,'PATROCÍNIO','MG','patrocinio'),(2807,3148202,'PATROCÍNIO DO MURIAÉ','MG','patrocinio-do-muriae'),(2808,3148301,'PAULA CÂNDIDO','MG','paula-candido'),(2809,3148400,'PAULISTAS','MG','paulistas'),(2810,3148509,'PAVÃO','MG','pavao'),(2811,3148608,'PEÇANHA','MG','pecanha'),(2812,3148707,'PEDRA AZUL','MG','pedra-azul'),(2813,3148756,'PEDRA BONITA','MG','pedra-bonita'),(2814,3148806,'PEDRA DO ANTA','MG','pedra-do-anta'),(2815,3148905,'PEDRA DO INDAIÁ','MG','pedra-do-indaia'),(2816,3149002,'PEDRA DOURADA','MG','pedra-dourada'),(2817,3149101,'PEDRALVA','MG','pedralva'),(2818,3149150,'PEDRAS DE MARIA DA CRUZ','MG','pedras-de-maria-da-cruz'),(2819,3149200,'PEDRINÓPOLIS','MG','pedrinopolis'),(2820,3149309,'PEDRO LEOPOLDO','MG','pedro-leopoldo'),(2821,3149408,'PEDRO TEIXEIRA','MG','pedro-teixeira'),(2822,3149507,'PEQUERI','MG','pequeri'),(2823,3149606,'PEQUI','MG','pequi'),(2824,3149705,'PERDIGÃO','MG','perdigao'),(2825,3149804,'PERDIZES','MG','perdizes'),(2826,3149903,'PERDÕES','MG','perdoes'),(2827,3149952,'PERIQUITO','MG','periquito'),(2828,3150000,'PESCADOR','MG','pescador'),(2829,3150109,'PIAU','MG','piau'),(2830,3150158,'PIEDADE DE CARATINGA','MG','piedade-de-caratinga'),(2831,3150208,'PIEDADE DE PONTE NOVA','MG','piedade-de-ponte-nova'),(2832,3150307,'PIEDADE DO RIO GRANDE','MG','piedade-do-rio-grande'),(2833,3150406,'PIEDADE DOS GERAIS','MG','piedade-dos-gerais'),(2834,3150505,'PIMENTA','MG','pimenta'),(2835,3150539,'PINGO-D\'ÁGUA','MG','pingo-dagua'),(2836,3150570,'PINTÓPOLIS','MG','pintopolis'),(2837,3150604,'PIRACEMA','MG','piracema'),(2838,3150703,'PIRAJUBA','MG','pirajuba'),(2839,3150802,'PIRANGA','MG','piranga'),(2840,3150901,'PIRANGUÇU','MG','pirangucu'),(2841,3151008,'PIRANGUINHO','MG','piranguinho'),(2842,3151107,'PIRAPETINGA','MG','pirapetinga'),(2843,3151206,'PIRAPORA','MG','pirapora'),(2844,3151305,'PIRAÚBA','MG','pirauba'),(2845,3151404,'PITANGUI','MG','pitangui'),(2846,3151503,'PIUMHI','MG','piumhi'),(2847,3151602,'PLANURA','MG','planura'),(2848,3151701,'POÇO FUNDO','MG','poco-fundo'),(2849,3151800,'POÇOS DE CALDAS','MG','pocos-de-caldas'),(2850,3151909,'POCRANE','MG','pocrane'),(2851,3152006,'POMPÉU','MG','pompeu'),(2852,3152105,'PONTE NOVA','MG','ponte-nova'),(2853,3152131,'PONTO CHIQUE','MG','ponto-chique'),(2854,3152170,'PONTO DOS VOLANTES','MG','ponto-dos-volantes'),(2855,3152204,'PORTEIRINHA','MG','porteirinha'),(2856,3152303,'PORTO FIRME','MG','porto-firme'),(2857,3152402,'POTÉ','MG','pote'),(2858,3152501,'POUSO ALEGRE','MG','pouso-alegre'),(2859,3152600,'POUSO ALTO','MG','pouso-alto'),(2860,3152709,'PRADOS','MG','prados'),(2861,3152808,'PRATA','MG','prata'),(2862,3152907,'PRATÁPOLIS','MG','pratapolis'),(2863,3153004,'PRATINHA','MG','pratinha'),(2864,3153103,'PRESIDENTE BERNARDES','MG','presidente-bernardes'),(2865,3153202,'PRESIDENTE JUSCELINO','MG','presidente-juscelino'),(2866,3153301,'PRESIDENTE KUBITSCHEK','MG','presidente-kubitschek'),(2867,3153400,'PRESIDENTE OLEGÁRIO','MG','presidente-olegario'),(2868,3153509,'ALTO JEQUITIBÁ','MG','alto-jequitiba'),(2869,3153608,'PRUDENTE DE MORAIS','MG','prudente-de-morais'),(2870,3153707,'QUARTEL GERAL','MG','quartel-geral'),(2871,3153806,'QUELUZITO','MG','queluzito'),(2872,3153905,'RAPOSOS','MG','raposos'),(2873,3154002,'RAUL SOARES','MG','raul-soares'),(2874,3154101,'RECREIO','MG','recreio'),(2875,3154150,'REDUTO','MG','reduto'),(2876,3154200,'RESENDE COSTA','MG','resende-costa'),(2877,3154309,'RESPLENDOR','MG','resplendor'),(2878,3154408,'RESSAQUINHA','MG','ressaquinha'),(2879,3154457,'RIACHINHO','MG','riachinho'),(2880,3154507,'RIACHO DOS MACHADOS','MG','riacho-dos-machados'),(2881,3154606,'RIBEIRÃO DAS NEVES','MG','ribeirao-das-neves'),(2882,3154705,'RIBEIRÃO VERMELHO','MG','ribeirao-vermelho'),(2883,3154804,'RIO ACIMA','MG','rio-acima'),(2884,3154903,'RIO CASCA','MG','rio-casca'),(2885,3155009,'RIO DOCE','MG','rio-doce'),(2886,3155108,'RIO DO PRADO','MG','rio-do-prado'),(2887,3155207,'RIO ESPERA','MG','rio-espera'),(2888,3155306,'RIO MANSO','MG','rio-manso'),(2889,3155405,'RIO NOVO','MG','rio-novo'),(2890,3155504,'RIO PARANAÍBA','MG','rio-paranaiba'),(2891,3155603,'RIO PARDO DE MINAS','MG','rio-pardo-de-minas'),(2892,3155702,'RIO PIRACICABA','MG','rio-piracicaba'),(2893,3155801,'RIO POMBA','MG','rio-pomba'),(2894,3155900,'RIO PRETO','MG','rio-preto'),(2895,3156007,'RIO VERMELHO','MG','rio-vermelho'),(2896,3156106,'RITÁPOLIS','MG','ritapolis'),(2897,3156205,'ROCHEDO DE MINAS','MG','rochedo-de-minas'),(2898,3156304,'RODEIRO','MG','rodeiro'),(2899,3156403,'ROMARIA','MG','romaria'),(2900,3156452,'ROSÁRIO DA LIMEIRA','MG','rosario-da-limeira'),(2901,3156502,'RUBELITA','MG','rubelita'),(2902,3156601,'RUBIM','MG','rubim'),(2903,3156700,'SABARÁ','MG','sabara'),(2904,3156809,'SABINÓPOLIS','MG','sabinopolis'),(2905,3156908,'SACRAMENTO','MG','sacramento'),(2906,3157005,'SALINAS','MG','salinas'),(2907,3157104,'SALTO DA DIVISA','MG','salto-da-divisa'),(2908,3157203,'SANTA BÁRBARA','MG','santa-barbara'),(2909,3157252,'SANTA BÁRBARA DO LESTE','MG','santa-barbara-do-leste'),(2910,3157278,'SANTA BÁRBARA DO MONTE VERDE','MG','santa-barbara-do-monte-verde'),(2911,3157302,'SANTA BÁRBARA DO TUGÚRIO','MG','santa-barbara-do-tugurio'),(2912,3157336,'SANTA CRUZ DE MINAS','MG','santa-cruz-de-minas'),(2913,3157377,'SANTA CRUZ DE SALINAS','MG','santa-cruz-de-salinas'),(2914,3157401,'SANTA CRUZ DO ESCALVADO','MG','santa-cruz-do-escalvado'),(2915,3157500,'SANTA EFIGÊNIA DE MINAS','MG','santa-efigenia-de-minas'),(2916,3157609,'SANTA FÉ DE MINAS','MG','santa-fe-de-minas'),(2917,3157658,'SANTA HELENA DE MINAS','MG','santa-helena-de-minas'),(2918,3157708,'SANTA JULIANA','MG','santa-juliana'),(2919,3157807,'SANTA LUZIA','MG','santa-luzia'),(2920,3157906,'SANTA MARGARIDA','MG','santa-margarida'),(2921,3158003,'SANTA MARIA DE ITABIRA','MG','santa-maria-de-itabira'),(2922,3158102,'SANTA MARIA DO SALTO','MG','santa-maria-do-salto'),(2923,3158201,'SANTA MARIA DO SUAÇUÍ','MG','santa-maria-do-suacui'),(2924,3158300,'SANTANA DA VARGEM','MG','santana-da-vargem'),(2925,3158409,'SANTANA DE CATAGUASES','MG','santana-de-cataguases'),(2926,3158508,'SANTANA DE PIRAPAMA','MG','santana-de-pirapama'),(2927,3158607,'SANTANA DO DESERTO','MG','santana-do-deserto'),(2928,3158706,'SANTANA DO GARAMBÉU','MG','santana-do-garambeu'),(2929,3158805,'SANTANA DO JACARÉ','MG','santana-do-jacare'),(2930,3158904,'SANTANA DO MANHUAÇU','MG','santana-do-manhuacu'),(2931,3158953,'SANTANA DO PARAÍSO','MG','santana-do-paraiso'),(2932,3159001,'SANTANA DO RIACHO','MG','santana-do-riacho'),(2933,3159100,'SANTANA DOS MONTES','MG','santana-dos-montes'),(2934,3159209,'SANTA RITA DE CALDAS','MG','santa-rita-de-caldas'),(2935,3159308,'SANTA RITA DE JACUTINGA','MG','santa-rita-de-jacutinga'),(2936,3159357,'SANTA RITA DE MINAS','MG','santa-rita-de-minas'),(2937,3159407,'SANTA RITA DE IBITIPOCA','MG','santa-rita-de-ibitipoca'),(2938,3159506,'SANTA RITA DO ITUETO','MG','santa-rita-do-itueto'),(2939,3159605,'SANTA RITA DO SAPUCAÍ','MG','santa-rita-do-sapucai'),(2940,3159704,'SANTA ROSA DA SERRA','MG','santa-rosa-da-serra'),(2941,3159803,'SANTA VITÓRIA','MG','santa-vitoria'),(2942,3159902,'SANTO ANTÔNIO DO AMPARO','MG','santo-antonio-do-amparo'),(2943,3160009,'SANTO ANTÔNIO DO AVENTUREIRO','MG','santo-antonio-do-aventureiro'),(2944,3160108,'SANTO ANTÔNIO DO GRAMA','MG','santo-antonio-do-grama'),(2945,3160207,'SANTO ANTÔNIO DO ITAMBÉ','MG','santo-antonio-do-itambe'),(2946,3160306,'SANTO ANTÔNIO DO JACINTO','MG','santo-antonio-do-jacinto'),(2947,3160405,'SANTO ANTÔNIO DO MONTE','MG','santo-antonio-do-monte'),(2948,3160454,'SANTO ANTÔNIO DO RETIRO','MG','santo-antonio-do-retiro'),(2949,3160504,'SANTO ANTÔNIO DO RIO ABAIXO','MG','santo-antonio-do-rio-abaixo'),(2950,3160603,'SANTO HIPÓLITO','MG','santo-hipolito'),(2951,3160702,'SANTOS DUMONT','MG','santos-dumont'),(2952,3160801,'SÃO BENTO ABADE','MG','sao-bento-abade'),(2953,3160900,'SÃO BRÁS DO SUAÇUÍ','MG','sao-bras-do-suacui'),(2954,3160959,'SÃO DOMINGOS DAS DORES','MG','sao-domingos-das-dores'),(2955,3161007,'SÃO DOMINGOS DO PRATA','MG','sao-domingos-do-prata'),(2956,3161056,'SÃO FÉLIX DE MINAS','MG','sao-felix-de-minas'),(2957,3161106,'SÃO FRANCISCO','MG','sao-francisco'),(2958,3161205,'SÃO FRANCISCO DE PAULA','MG','sao-francisco-de-paula'),(2959,3161304,'SÃO FRANCISCO DE SALES','MG','sao-francisco-de-sales'),(2960,3161403,'SÃO FRANCISCO DO GLÓRIA','MG','sao-francisco-do-gloria'),(2961,3161502,'SÃO GERALDO','MG','sao-geraldo'),(2962,3161601,'SÃO GERALDO DA PIEDADE','MG','sao-geraldo-da-piedade'),(2963,3161650,'SÃO GERALDO DO BAIXIO','MG','sao-geraldo-do-baixio'),(2964,3161700,'SÃO GONÇALO DO ABAETÉ','MG','sao-goncalo-do-abaete'),(2965,3161809,'SÃO GONÇALO DO PARÁ','MG','sao-goncalo-do-para'),(2966,3161908,'SÃO GONÇALO DO RIO ABAIXO','MG','sao-goncalo-do-rio-abaixo'),(2967,3162005,'SÃO GONÇALO DO SAPUCAÍ','MG','sao-goncalo-do-sapucai'),(2968,3162104,'SÃO GOTARDO','MG','sao-gotardo'),(2969,3162203,'SÃO JOÃO BATISTA DO GLÓRIA','MG','sao-joao-batista-do-gloria'),(2970,3162252,'SÃO JOÃO DA LAGOA','MG','sao-joao-da-lagoa'),(2971,3162302,'SÃO JOÃO DA MATA','MG','sao-joao-da-mata'),(2972,3162401,'SÃO JOÃO DA PONTE','MG','sao-joao-da-ponte'),(2973,3162450,'SÃO JOÃO DAS MISSÕES','MG','sao-joao-das-missoes'),(2974,3162500,'SÃO JOÃO DEL REI','MG','sao-joao-del-rei'),(2975,3162559,'SÃO JOÃO DO MANHUAÇU','MG','sao-joao-do-manhuacu'),(2976,3162575,'SÃO JOÃO DO MANTENINHA','MG','sao-joao-do-manteninha'),(2977,3162609,'SÃO JOÃO DO ORIENTE','MG','sao-joao-do-oriente'),(2978,3162658,'SÃO JOÃO DO PACUÍ','MG','sao-joao-do-pacui'),(2979,3162708,'SÃO JOÃO DO PARAÍSO','MG','sao-joao-do-paraiso'),(2980,3162807,'SÃO JOÃO EVANGELISTA','MG','sao-joao-evangelista'),(2981,3162906,'SÃO JOÃO NEPOMUCENO','MG','sao-joao-nepomuceno'),(2982,3162922,'SÃO JOAQUIM DE BICAS','MG','sao-joaquim-de-bicas'),(2983,3162948,'SÃO JOSÉ DA BARRA','MG','sao-jose-da-barra'),(2984,3162955,'SÃO JOSÉ DA LAPA','MG','sao-jose-da-lapa'),(2985,3163003,'SÃO JOSÉ DA SAFIRA','MG','sao-jose-da-safira'),(2986,3163102,'SÃO JOSÉ DA VARGINHA','MG','sao-jose-da-varginha'),(2987,3163201,'SÃO JOSÉ DO ALEGRE','MG','sao-jose-do-alegre'),(2988,3163300,'SÃO JOSÉ DO DIVINO','MG','sao-jose-do-divino'),(2989,3163409,'SÃO JOSÉ DO GOIABAL','MG','sao-jose-do-goiabal'),(2990,3163508,'SÃO JOSÉ DO JACURI','MG','sao-jose-do-jacuri'),(2991,3163607,'SÃO JOSÉ DO MANTIMENTO','MG','sao-jose-do-mantimento'),(2992,3163706,'SÃO LOURENÇO','MG','sao-lourenco'),(2993,3163805,'SÃO MIGUEL DO ANTA','MG','sao-miguel-do-anta'),(2994,3163904,'SÃO PEDRO DA UNIÃO','MG','sao-pedro-da-uniao'),(2995,3164001,'SÃO PEDRO DOS FERROS','MG','sao-pedro-dos-ferros'),(2996,3164100,'SÃO PEDRO DO SUAÇUÍ','MG','sao-pedro-do-suacui'),(2997,3164209,'SÃO ROMÃO','MG','sao-romao'),(2998,3164308,'SÃO ROQUE DE MINAS','MG','sao-roque-de-minas'),(2999,3164407,'SÃO SEBASTIÃO DA BELA VISTA','MG','sao-sebastiao-da-bela-vista'),(3000,3164431,'SÃO SEBASTIÃO DA VARGEM ALEGRE','MG','sao-sebastiao-da-vargem-alegre'),(3001,3164472,'SÃO SEBASTIÃO DO ANTA','MG','sao-sebastiao-do-anta'),(3002,3164506,'SÃO SEBASTIÃO DO MARANHÃO','MG','sao-sebastiao-do-maranhao'),(3003,3164605,'SÃO SEBASTIÃO DO OESTE','MG','sao-sebastiao-do-oeste'),(3004,3164704,'SÃO SEBASTIÃO DO PARAÍSO','MG','sao-sebastiao-do-paraiso'),(3005,3164803,'SÃO SEBASTIÃO DO RIO PRETO','MG','sao-sebastiao-do-rio-preto'),(3006,3164902,'SÃO SEBASTIÃO DO RIO VERDE','MG','sao-sebastiao-do-rio-verde'),(3007,3165008,'SÃO TIAGO','MG','sao-tiago'),(3008,3165107,'SÃO TOMÁS DE AQUINO','MG','sao-tomas-de-aquino'),(3009,3165206,'SÃO THOMÉ DAS LETRAS','MG','sao-thome-das-letras'),(3010,3165305,'SÃO VICENTE DE MINAS','MG','sao-vicente-de-minas'),(3011,3165404,'SAPUCAÍ-MIRIM','MG','sapucai-mirim'),(3012,3165503,'SARDOÁ','MG','sardoa'),(3013,3165537,'SARZEDO','MG','sarzedo'),(3014,3165552,'SETUBINHA','MG','setubinha'),(3015,3165560,'SEM-PEIXE','MG','sem-peixe'),(3016,3165578,'SENADOR AMARAL','MG','senador-amaral'),(3017,3165602,'SENADOR CORTES','MG','senador-cortes'),(3018,3165701,'SENADOR FIRMINO','MG','senador-firmino'),(3019,3165800,'SENADOR JOSÉ BENTO','MG','senador-jose-bento'),(3020,3165909,'SENADOR MODESTINO GONÇALVES','MG','senador-modestino-goncalves'),(3021,3166006,'SENHORA DE OLIVEIRA','MG','senhora-de-oliveira'),(3022,3166105,'SENHORA DO PORTO','MG','senhora-do-porto'),(3023,3166204,'SENHORA DOS REMÉDIOS','MG','senhora-dos-remedios'),(3024,3166303,'SERICITA','MG','sericita'),(3025,3166402,'SERITINGA','MG','seritinga'),(3026,3166501,'SERRA AZUL DE MINAS','MG','serra-azul-de-minas'),(3027,3166600,'SERRA DA SAUDADE','MG','serra-da-saudade'),(3028,3166709,'SERRA DOS AIMORÉS','MG','serra-dos-aimores'),(3029,3166808,'SERRA DO SALITRE','MG','serra-do-salitre'),(3030,3166907,'SERRANIA','MG','serrania'),(3031,3166956,'SERRANÓPOLIS DE MINAS','MG','serranopolis-de-minas'),(3032,3167004,'SERRANOS','MG','serranos'),(3033,3167103,'SERRO','MG','serro'),(3034,3167202,'SETE LAGOAS','MG','sete-lagoas'),(3035,3167301,'SILVEIRÂNIA','MG','silveirania'),(3036,3167400,'SILVIANÓPOLIS','MG','silvianopolis'),(3037,3167509,'SIMÃO PEREIRA','MG','simao-pereira'),(3038,3167608,'SIMONÉSIA','MG','simonesia'),(3039,3167707,'SOBRÁLIA','MG','sobralia'),(3040,3167806,'SOLEDADE DE MINAS','MG','soledade-de-minas'),(3041,3167905,'TABULEIRO','MG','tabuleiro'),(3042,3168002,'TAIOBEIRAS','MG','taiobeiras'),(3043,3168051,'TAPARUBA','MG','taparuba'),(3044,3168101,'TAPIRA','MG','tapira'),(3045,3168200,'TAPIRAÍ','MG','tapirai'),(3046,3168309,'TAQUARAÇU DE MINAS','MG','taquaracu-de-minas'),(3047,3168408,'TARUMIRIM','MG','tarumirim'),(3048,3168507,'TEIXEIRAS','MG','teixeiras'),(3049,3168606,'TEÓFILO OTONI','MG','teofilo-otoni'),(3050,3168705,'TIMÓTEO','MG','timoteo'),(3051,3168804,'TIRADENTES','MG','tiradentes'),(3052,3168903,'TIROS','MG','tiros'),(3053,3169000,'TOCANTINS','MG','tocantins'),(3054,3169059,'TOCOS DO MOJI','MG','tocos-do-moji'),(3055,3169109,'TOLEDO','MG','toledo'),(3056,3169208,'TOMBOS','MG','tombos'),(3057,3169307,'TRÊS CORAÇÕES','MG','tres-coracoes'),(3058,3169356,'TRÊS MARIAS','MG','tres-marias'),(3059,3169406,'TRÊS PONTAS','MG','tres-pontas'),(3060,3169505,'TUMIRITINGA','MG','tumiritinga'),(3061,3169604,'TUPACIGUARA','MG','tupaciguara'),(3062,3169703,'TURMALINA','MG','turmalina'),(3063,3169802,'TURVOLÂNDIA','MG','turvolandia'),(3064,3169901,'UBÁ','MG','uba'),(3065,3170008,'UBAÍ','MG','ubai'),(3066,3170057,'UBAPORANGA','MG','ubaporanga'),(3067,3170107,'UBERABA','MG','uberaba'),(3068,3170206,'UBERLÂNDIA','MG','uberlandia'),(3069,3170305,'UMBURATIBA','MG','umburatiba'),(3070,3170404,'UNAÍ','MG','unai'),(3071,3170438,'UNIÃO DE MINAS','MG','uniao-de-minas'),(3072,3170479,'URUANA DE MINAS','MG','uruana-de-minas'),(3073,3170503,'URUCÂNIA','MG','urucania'),(3074,3170529,'URUCUIA','MG','urucuia'),(3075,3170578,'VARGEM ALEGRE','MG','vargem-alegre'),(3076,3170602,'VARGEM BONITA','MG','vargem-bonita'),(3077,3170651,'VARGEM GRANDE DO RIO PARDO','MG','vargem-grande-do-rio-pardo'),(3078,3170701,'VARGINHA','MG','varginha'),(3079,3170750,'VARJÃO DE MINAS','MG','varjao-de-minas'),(3080,3170800,'VÁRZEA DA PALMA','MG','varzea-da-palma'),(3081,3170909,'VARZELÂNDIA','MG','varzelandia'),(3082,3171006,'VAZANTE','MG','vazante'),(3083,3171030,'VERDELÂNDIA','MG','verdelandia'),(3084,3171071,'VEREDINHA','MG','veredinha'),(3085,3171105,'VERÍSSIMO','MG','verissimo'),(3086,3171154,'VERMELHO NOVO','MG','vermelho-novo'),(3087,3171204,'VESPASIANO','MG','vespasiano'),(3088,3171303,'VIÇOSA','MG','vicosa'),(3089,3171402,'VIEIRAS','MG','vieiras'),(3090,3171501,'MATHIAS LOBATO','MG','mathias-lobato'),(3091,3171600,'VIRGEM DA LAPA','MG','virgem-da-lapa'),(3092,3171709,'VIRGÍNIA','MG','virginia'),(3093,3171808,'VIRGINÓPOLIS','MG','virginopolis'),(3094,3171907,'VIRGOLÂNDIA','MG','virgolandia'),(3095,3172004,'VISCONDE DO RIO BRANCO','MG','visconde-do-rio-branco'),(3096,3172103,'VOLTA GRANDE','MG','volta-grande'),(3097,3172202,'WENCESLAU BRAZ','MG','wenceslau-braz'),(3098,3200102,'AFONSO CLÁUDIO','ES','afonso-claudio'),(3099,3200136,'ÁGUIA BRANCA','ES','aguia-branca'),(3100,3200169,'ÁGUA DOCE DO NORTE','ES','agua-doce-do-norte'),(3101,3200201,'ALEGRE','ES','alegre'),(3102,3200300,'ALFREDO CHAVES','ES','alfredo-chaves'),(3103,3200359,'ALTO RIO NOVO','ES','alto-rio-novo'),(3104,3200409,'ANCHIETA','ES','anchieta'),(3105,3200508,'APIACÁ','ES','apiaca'),(3106,3200607,'ARACRUZ','ES','aracruz'),(3107,3200706,'ATILIO VIVACQUA','ES','atilio-vivacqua'),(3108,3200805,'BAIXO GUANDU','ES','baixo-guandu'),(3109,3200904,'BARRA DE SÃO FRANCISCO','ES','barra-de-sao-francisco'),(3110,3201001,'BOA ESPERANÇA','ES','boa-esperanca'),(3111,3201100,'BOM JESUS DO NORTE','ES','bom-jesus-do-norte'),(3112,3201159,'BREJETUBA','ES','brejetuba'),(3113,3201209,'CACHOEIRO DE ITAPEMIRIM','ES','cachoeiro-de-itapemirim'),(3114,3201308,'CARIACICA','ES','cariacica'),(3115,3201407,'CASTELO','ES','castelo'),(3116,3201506,'COLATINA','ES','colatina'),(3117,3201605,'CONCEIÇÃO DA BARRA','ES','conceicao-da-barra'),(3118,3201704,'CONCEIÇÃO DO CASTELO','ES','conceicao-do-castelo'),(3119,3201803,'DIVINO DE SÃO LOURENÇO','ES','divino-de-sao-lourenco'),(3120,3201902,'DOMINGOS MARTINS','ES','domingos-martins'),(3121,3202009,'DORES DO RIO PRETO','ES','dores-do-rio-preto'),(3122,3202108,'ECOPORANGA','ES','ecoporanga'),(3123,3202207,'FUNDÃO','ES','fundao'),(3124,3202256,'GOVERNADOR LINDENBERG','ES','governador-lindenberg'),(3125,3202306,'GUAÇUÍ','ES','guacui'),(3126,3202405,'GUARAPARI','ES','guarapari'),(3127,3202454,'IBATIBA','ES','ibatiba'),(3128,3202504,'IBIRAÇU','ES','ibiracu'),(3129,3202553,'IBITIRAMA','ES','ibitirama'),(3130,3202603,'ICONHA','ES','iconha'),(3131,3202652,'IRUPI','ES','irupi'),(3132,3202702,'ITAGUAÇU','ES','itaguacu'),(3133,3202801,'ITAPEMIRIM','ES','itapemirim'),(3134,3202900,'ITARANA','ES','itarana'),(3135,3203007,'IÚNA','ES','iuna'),(3136,3203056,'JAGUARÉ','ES','jaguare'),(3137,3203106,'JERÔNIMO MONTEIRO','ES','jeronimo-monteiro'),(3138,3203130,'JOÃO NEIVA','ES','joao-neiva'),(3139,3203163,'LARANJA DA TERRA','ES','laranja-da-terra'),(3140,3203205,'LINHARES','ES','linhares'),(3141,3203304,'MANTENÓPOLIS','ES','mantenopolis'),(3142,3203320,'MARATAÍZES','ES','marataizes'),(3143,3203346,'MARECHAL FLORIANO','ES','marechal-floriano'),(3144,3203353,'MARILÂNDIA','ES','marilandia'),(3145,3203403,'MIMOSO DO SUL','ES','mimoso-do-sul'),(3146,3203502,'MONTANHA','ES','montanha'),(3147,3203601,'MUCURICI','ES','mucurici'),(3148,3203700,'MUNIZ FREIRE','ES','muniz-freire'),(3149,3203809,'MUQUI','ES','muqui'),(3150,3203908,'NOVA VENÉCIA','ES','nova-venecia'),(3151,3204005,'PANCAS','ES','pancas'),(3152,3204054,'PEDRO CANÁRIO','ES','pedro-canario'),(3153,3204104,'PINHEIROS','ES','pinheiros'),(3154,3204203,'PIÚMA','ES','piuma'),(3155,3204252,'PONTO BELO','ES','ponto-belo'),(3156,3204302,'PRESIDENTE KENNEDY','ES','presidente-kennedy'),(3157,3204351,'RIO BANANAL','ES','rio-bananal'),(3158,3204401,'RIO NOVO DO SUL','ES','rio-novo-do-sul'),(3159,3204500,'SANTA LEOPOLDINA','ES','santa-leopoldina'),(3160,3204559,'SANTA MARIA DE JETIBÁ','ES','santa-maria-de-jetiba'),(3161,3204609,'SANTA TERESA','ES','santa-teresa'),(3162,3204658,'SÃO DOMINGOS DO NORTE','ES','sao-domingos-do-norte'),(3163,3204708,'SÃO GABRIEL DA PALHA','ES','sao-gabriel-da-palha'),(3164,3204807,'SÃO JOSÉ DO CALÇADO','ES','sao-jose-do-calcado'),(3165,3204906,'SÃO MATEUS','ES','sao-mateus'),(3166,3204955,'SÃO ROQUE DO CANAÃ','ES','sao-roque-do-canaa'),(3167,3205002,'SERRA','ES','serra'),(3168,3205010,'SOORETAMA','ES','sooretama'),(3169,3205036,'VARGEM ALTA','ES','vargem-alta'),(3170,3205069,'VENDA NOVA DO IMIGRANTE','ES','venda-nova-do-imigrante'),(3171,3205101,'VIANA','ES','viana'),(3172,3205150,'VILA PAVÃO','ES','vila-pavao'),(3173,3205176,'VILA VALÉRIO','ES','vila-valerio'),(3174,3205200,'VILA VELHA','ES','vila-velha'),(3175,3205309,'VITÓRIA','ES','vitoria'),(3176,3300100,'ANGRA DOS REIS','RJ','angra-dos-reis'),(3177,3300159,'APERIBÉ','RJ','aperibe'),(3178,3300209,'ARARUAMA','RJ','araruama'),(3179,3300225,'AREAL','RJ','areal'),(3180,3300233,'ARMAÇÃO DOS BÚZIOS','RJ','armacao-dos-buzios'),(3181,3300258,'ARRAIAL DO CABO','RJ','arraial-do-cabo'),(3182,3300308,'BARRA DO PIRAÍ','RJ','barra-do-pirai'),(3183,3300407,'BARRA MANSA','RJ','barra-mansa'),(3184,3300456,'BELFORD ROXO','RJ','belford-roxo'),(3185,3300506,'BOM JARDIM','RJ','bom-jardim'),(3186,3300605,'BOM JESUS DO ITABAPOANA','RJ','bom-jesus-do-itabapoana'),(3187,3300704,'CABO FRIO','RJ','cabo-frio'),(3188,3300803,'CACHOEIRAS DE MACACU','RJ','cachoeiras-de-macacu'),(3189,3300902,'CAMBUCI','RJ','cambuci'),(3190,3300936,'CARAPEBUS','RJ','carapebus'),(3191,3300951,'COMENDADOR LEVY GASPARIAN','RJ','comendador-levy-gasparian'),(3192,3301009,'CAMPOS DOS GOYTACAZES','RJ','campos-dos-goytacazes'),(3193,3301108,'CANTAGALO','RJ','cantagalo'),(3194,3301157,'CARDOSO MOREIRA','RJ','cardoso-moreira'),(3195,3301207,'CARMO','RJ','carmo'),(3196,3301306,'CASIMIRO DE ABREU','RJ','casimiro-de-abreu'),(3197,3301405,'CONCEIÇÃO DE MACABU','RJ','conceicao-de-macabu'),(3198,3301504,'CORDEIRO','RJ','cordeiro'),(3199,3301603,'DUAS BARRAS','RJ','duas-barras'),(3200,3301702,'DUQUE DE CAXIAS','RJ','duque-de-caxias'),(3201,3301801,'ENGENHEIRO PAULO DE FRONTIN','RJ','engenheiro-paulo-de-frontin'),(3202,3301850,'GUAPIMIRIM','RJ','guapimirim'),(3203,3301876,'IGUABA GRANDE','RJ','iguaba-grande'),(3204,3301900,'ITABORAÍ','RJ','itaborai'),(3205,3302007,'ITAGUAÍ','RJ','itaguai'),(3206,3302056,'ITALVA','RJ','italva'),(3207,3302106,'ITAOCARA','RJ','itaocara'),(3208,3302205,'ITAPERUNA','RJ','itaperuna'),(3209,3302254,'ITATIAIA','RJ','itatiaia'),(3210,3302270,'JAPERI','RJ','japeri'),(3211,3302304,'LAJE DO MURIAÉ','RJ','laje-do-muriae'),(3212,3302403,'MACAÉ','RJ','macae'),(3213,3302452,'MACUCO','RJ','macuco'),(3214,3302502,'MAGÉ','RJ','mage'),(3215,3302601,'MANGARATIBA','RJ','mangaratiba'),(3216,3302700,'MARICÁ','RJ','marica'),(3217,3302809,'MENDES','RJ','mendes'),(3218,3302858,'MESQUITA','RJ','mesquita'),(3219,3302908,'MIGUEL PEREIRA','RJ','miguel-pereira'),(3220,3303005,'MIRACEMA','RJ','miracema'),(3221,3303104,'NATIVIDADE','RJ','natividade'),(3222,3303203,'NILÓPOLIS','RJ','nilopolis'),(3223,3303302,'NITERÓI','RJ','niteroi'),(3224,3303401,'NOVA FRIBURGO','RJ','nova-friburgo'),(3225,3303500,'NOVA IGUAÇU','RJ','nova-iguacu'),(3226,3303609,'PARACAMBI','RJ','paracambi'),(3227,3303708,'PARAÍBA DO SUL','RJ','paraiba-do-sul'),(3228,3303807,'PARATY','RJ','paraty'),(3229,3303856,'PATY DO ALFERES','RJ','paty-do-alferes'),(3230,3303906,'PETRÓPOLIS','RJ','petropolis'),(3231,3303955,'PINHEIRAL','RJ','pinheiral'),(3232,3304003,'PIRAÍ','RJ','pirai'),(3233,3304102,'PORCIÚNCULA','RJ','porciuncula'),(3234,3304110,'PORTO REAL','RJ','porto-real'),(3235,3304128,'QUATIS','RJ','quatis'),(3236,3304144,'QUEIMADOS','RJ','queimados'),(3237,3304151,'QUISSAMÃ','RJ','quissama'),(3238,3304201,'RESENDE','RJ','resende'),(3239,3304300,'RIO BONITO','RJ','rio-bonito'),(3240,3304409,'RIO CLARO','RJ','rio-claro'),(3241,3304508,'RIO DAS FLORES','RJ','rio-das-flores'),(3242,3304524,'RIO DAS OSTRAS','RJ','rio-das-ostras'),(3243,3304557,'RIO DE JANEIRO','RJ','rio-de-janeiro'),(3244,3304607,'SANTA MARIA MADALENA','RJ','santa-maria-madalena'),(3245,3304706,'SANTO ANTÔNIO DE PÁDUA','RJ','santo-antonio-de-padua'),(3246,3304755,'SÃO FRANCISCO DE ITABAPOANA','RJ','sao-francisco-de-itabapoana'),(3247,3304805,'SÃO FIDÉLIS','RJ','sao-fidelis'),(3248,3304904,'SÃO GONÇALO','RJ','sao-goncalo'),(3249,3305000,'SÃO JOÃO DA BARRA','RJ','sao-joao-da-barra'),(3250,3305109,'SÃO JOÃO DE MERITI','RJ','sao-joao-de-meriti'),(3251,3305133,'SÃO JOSÉ DE UBÁ','RJ','sao-jose-de-uba'),(3252,3305158,'SÃO JOSÉ DO VALE DO RIO PRETO','RJ','sao-jose-do-vale-do-rio-preto'),(3253,3305208,'SÃO PEDRO DA ALDEIA','RJ','sao-pedro-da-aldeia'),(3254,3305307,'SÃO SEBASTIÃO DO ALTO','RJ','sao-sebastiao-do-alto'),(3255,3305406,'SAPUCAIA','RJ','sapucaia'),(3256,3305505,'SAQUAREMA','RJ','saquarema'),(3257,3305554,'SEROPÉDICA','RJ','seropedica'),(3258,3305604,'SILVA JARDIM','RJ','silva-jardim'),(3259,3305703,'SUMIDOURO','RJ','sumidouro'),(3260,3305752,'TANGUÁ','RJ','tangua'),(3261,3305802,'TERESÓPOLIS','RJ','teresopolis'),(3262,3305901,'TRAJANO DE MORAES','RJ','trajano-de-moraes'),(3263,3306008,'TRÊS RIOS','RJ','tres-rios'),(3264,3306107,'VALENÇA','RJ','valenca'),(3265,3306156,'VARRE-SAI','RJ','varre-sai'),(3266,3306206,'VASSOURAS','RJ','vassouras'),(3267,3306305,'VOLTA REDONDA','RJ','volta-redonda'),(3268,3500105,'ADAMANTINA','SP','adamantina'),(3269,3500204,'ADOLFO','SP','adolfo'),(3270,3500303,'AGUAÍ','SP','aguai'),(3271,3500402,'ÁGUAS DA PRATA','SP','aguas-da-prata'),(3272,3500501,'ÁGUAS DE LINDÓIA','SP','aguas-de-lindoia'),(3273,3500550,'ÁGUAS DE SANTA BÁRBARA','SP','aguas-de-santa-barbara'),(3274,3500600,'ÁGUAS DE SÃO PEDRO','SP','aguas-de-sao-pedro'),(3275,3500709,'AGUDOS','SP','agudos'),(3276,3500758,'ALAMBARI','SP','alambari'),(3277,3500808,'ALFREDO MARCONDES','SP','alfredo-marcondes'),(3278,3500907,'ALTAIR','SP','altair'),(3279,3501004,'ALTINÓPOLIS','SP','altinopolis'),(3280,3501103,'ALTO ALEGRE','SP','alto-alegre'),(3281,3501152,'ALUMÍNIO','SP','aluminio'),(3282,3501202,'ÁLVARES FLORENCE','SP','alvares-florence'),(3283,3501301,'ÁLVARES MACHADO','SP','alvares-machado'),(3284,3501400,'ÁLVARO DE CARVALHO','SP','alvaro-de-carvalho'),(3285,3501509,'ALVINLÂNDIA','SP','alvinlandia'),(3286,3501608,'AMERICANA','SP','americana'),(3287,3501707,'AMÉRICO BRASILIENSE','SP','americo-brasiliense'),(3288,3501806,'AMÉRICO DE CAMPOS','SP','americo-de-campos'),(3289,3501905,'AMPARO','SP','amparo'),(3290,3502002,'ANALÂNDIA','SP','analandia'),(3291,3502101,'ANDRADINA','SP','andradina'),(3292,3502200,'ANGATUBA','SP','angatuba'),(3293,3502309,'ANHEMBI','SP','anhembi'),(3294,3502408,'ANHUMAS','SP','anhumas'),(3295,3502507,'APARECIDA','SP','aparecida'),(3296,3502606,'APARECIDA D\'OESTE','SP','aparecida-doeste'),(3297,3502705,'APIAÍ','SP','apiai'),(3298,3502754,'ARAÇARIGUAMA','SP','aracariguama'),(3299,3502804,'ARAÇATUBA','SP','aracatuba'),(3300,3502903,'ARAÇOIABA DA SERRA','SP','aracoiaba-da-serra'),(3301,3503000,'ARAMINA','SP','aramina'),(3302,3503109,'ARANDU','SP','arandu'),(3303,3503158,'ARAPEÍ','SP','arapei'),(3304,3503208,'ARARAQUARA','SP','araraquara'),(3305,3503307,'ARARAS','SP','araras'),(3306,3503356,'ARCO-ÍRIS','SP','arco-iris'),(3307,3503406,'AREALVA','SP','arealva'),(3308,3503505,'AREIAS','SP','areias'),(3309,3503604,'AREIÓPOLIS','SP','areiopolis'),(3310,3503703,'ARIRANHA','SP','ariranha'),(3311,3503802,'ARTUR NOGUEIRA','SP','artur-nogueira'),(3312,3503901,'ARUJÁ','SP','aruja'),(3313,3503950,'ASPÁSIA','SP','aspasia'),(3314,3504008,'ASSIS','SP','assis'),(3315,3504107,'ATIBAIA','SP','atibaia'),(3316,3504206,'AURIFLAMA','SP','auriflama'),(3317,3504305,'AVAÍ','SP','avai'),(3318,3504404,'AVANHANDAVA','SP','avanhandava'),(3319,3504503,'AVARÉ','SP','avare'),(3320,3504602,'BADY BASSITT','SP','bady-bassitt'),(3321,3504701,'BALBINOS','SP','balbinos'),(3322,3504800,'BÁLSAMO','SP','balsamo'),(3323,3504909,'BANANAL','SP','bananal'),(3324,3505005,'BARÃO DE ANTONINA','SP','barao-de-antonina'),(3325,3505104,'BARBOSA','SP','barbosa'),(3326,3505203,'BARIRI','SP','bariri'),(3327,3505302,'BARRA BONITA','SP','barra-bonita'),(3328,3505351,'BARRA DO CHAPÉU','SP','barra-do-chapeu'),(3329,3505401,'BARRA DO TURVO','SP','barra-do-turvo'),(3330,3505500,'BARRETOS','SP','barretos'),(3331,3505609,'BARRINHA','SP','barrinha'),(3332,3505708,'BARUERI','SP','barueri'),(3333,3505807,'BASTOS','SP','bastos'),(3334,3505906,'BATATAIS','SP','batatais'),(3335,3506003,'BAURU','SP','bauru'),(3336,3506102,'BEBEDOURO','SP','bebedouro'),(3337,3506201,'BENTO DE ABREU','SP','bento-de-abreu'),(3338,3506300,'BERNARDINO DE CAMPOS','SP','bernardino-de-campos'),(3339,3506359,'BERTIOGA','SP','bertioga'),(3340,3506409,'BILAC','SP','bilac'),(3341,3506508,'BIRIGUI','SP','birigui'),(3342,3506607,'BIRITIBA-MIRIM','SP','biritiba-mirim'),(3343,3506706,'BOA ESPERANÇA DO SUL','SP','boa-esperanca-do-sul'),(3344,3506805,'BOCAINA','SP','bocaina'),(3345,3506904,'BOFETE','SP','bofete'),(3346,3507001,'BOITUVA','SP','boituva'),(3347,3507100,'BOM JESUS DOS PERDÕES','SP','bom-jesus-dos-perdoes'),(3348,3507159,'BOM SUCESSO DE ITARARÉ','SP','bom-sucesso-de-itarare'),(3349,3507209,'BORÁ','SP','bora'),(3350,3507308,'BORACÉIA','SP','boraceia'),(3351,3507407,'BORBOREMA','SP','borborema'),(3352,3507456,'BOREBI','SP','borebi'),(3353,3507506,'BOTUCATU','SP','botucatu'),(3354,3507605,'BRAGANÇA PAULISTA','SP','braganca-paulista'),(3355,3507704,'BRAÚNA','SP','brauna'),(3356,3507753,'BREJO ALEGRE','SP','brejo-alegre'),(3357,3507803,'BRODOWSKI','SP','brodowski'),(3358,3507902,'BROTAS','SP','brotas'),(3359,3508009,'BURI','SP','buri'),(3360,3508108,'BURITAMA','SP','buritama'),(3361,3508207,'BURITIZAL','SP','buritizal'),(3362,3508306,'CABRÁLIA PAULISTA','SP','cabralia-paulista'),(3363,3508405,'CABREÚVA','SP','cabreuva'),(3364,3508504,'CAÇAPAVA','SP','cacapava'),(3365,3508603,'CACHOEIRA PAULISTA','SP','cachoeira-paulista'),(3366,3508702,'CACONDE','SP','caconde'),(3367,3508801,'CAFELÂNDIA','SP','cafelandia'),(3368,3508900,'CAIABU','SP','caiabu'),(3369,3509007,'CAIEIRAS','SP','caieiras'),(3370,3509106,'CAIUÁ','SP','caiua'),(3371,3509205,'CAJAMAR','SP','cajamar'),(3372,3509254,'CAJATI','SP','cajati'),(3373,3509304,'CAJOBI','SP','cajobi'),(3374,3509403,'CAJURU','SP','cajuru'),(3375,3509452,'CAMPINA DO MONTE ALEGRE','SP','campina-do-monte-alegre'),(3376,3509502,'CAMPINAS','SP','campinas'),(3377,3509601,'CAMPO LIMPO PAULISTA','SP','campo-limpo-paulista'),(3378,3509700,'CAMPOS DO JORDÃO','SP','campos-do-jordao'),(3379,3509809,'CAMPOS NOVOS PAULISTA','SP','campos-novos-paulista'),(3380,3509908,'CANANÉIA','SP','cananeia'),(3381,3509957,'CANAS','SP','canas'),(3382,3510005,'CÂNDIDO MOTA','SP','candido-mota'),(3383,3510104,'CÂNDIDO RODRIGUES','SP','candido-rodrigues'),(3384,3510153,'CANITAR','SP','canitar'),(3385,3510203,'CAPÃO BONITO','SP','capao-bonito'),(3386,3510302,'CAPELA DO ALTO','SP','capela-do-alto'),(3387,3510401,'CAPIVARI','SP','capivari'),(3388,3510500,'CARAGUATATUBA','SP','caraguatatuba'),(3389,3510609,'CARAPICUÍBA','SP','carapicuiba'),(3390,3510708,'CARDOSO','SP','cardoso'),(3391,3510807,'CASA BRANCA','SP','casa-branca'),(3392,3510906,'CÁSSIA DOS COQUEIROS','SP','cassia-dos-coqueiros'),(3393,3511003,'CASTILHO','SP','castilho'),(3394,3511102,'CATANDUVA','SP','catanduva'),(3395,3511201,'CATIGUÁ','SP','catigua'),(3396,3511300,'CEDRAL','SP','cedral'),(3397,3511409,'CERQUEIRA CÉSAR','SP','cerqueira-cesar'),(3398,3511508,'CERQUILHO','SP','cerquilho'),(3399,3511607,'CESÁRIO LANGE','SP','cesario-lange'),(3400,3511706,'CHARQUEADA','SP','charqueada'),(3401,3511904,'CLEMENTINA','SP','clementina'),(3402,3512001,'COLINA','SP','colina'),(3403,3512100,'COLÔMBIA','SP','colombia'),(3404,3512209,'CONCHAL','SP','conchal'),(3405,3512308,'CONCHAS','SP','conchas'),(3406,3512407,'CORDEIRÓPOLIS','SP','cordeiropolis'),(3407,3512506,'COROADOS','SP','coroados'),(3408,3512605,'CORONEL MACEDO','SP','coronel-macedo'),(3409,3512704,'CORUMBATAÍ','SP','corumbatai'),(3410,3512803,'COSMÓPOLIS','SP','cosmopolis'),(3411,3512902,'COSMORAMA','SP','cosmorama'),(3412,3513009,'COTIA','SP','cotia'),(3413,3513108,'CRAVINHOS','SP','cravinhos'),(3414,3513207,'CRISTAIS PAULISTA','SP','cristais-paulista'),(3415,3513306,'CRUZÁLIA','SP','cruzalia'),(3416,3513405,'CRUZEIRO','SP','cruzeiro'),(3417,3513504,'CUBATÃO','SP','cubatao'),(3418,3513603,'CUNHA','SP','cunha'),(3419,3513702,'DESCALVADO','SP','descalvado'),(3420,3513801,'DIADEMA','SP','diadema'),(3421,3513850,'DIRCE REIS','SP','dirce-reis'),(3422,3513900,'DIVINOLÂNDIA','SP','divinolandia'),(3423,3514007,'DOBRADA','SP','dobrada'),(3424,3514106,'DOIS CÓRREGOS','SP','dois-corregos'),(3425,3514205,'DOLCINÓPOLIS','SP','dolcinopolis'),(3426,3514304,'DOURADO','SP','dourado'),(3427,3514403,'DRACENA','SP','dracena'),(3428,3514502,'DUARTINA','SP','duartina'),(3429,3514601,'DUMONT','SP','dumont'),(3430,3514700,'ECHAPORÃ','SP','echapora'),(3431,3514809,'ELDORADO','SP','eldorado'),(3432,3514908,'ELIAS FAUSTO','SP','elias-fausto'),(3433,3514924,'ELISIÁRIO','SP','elisiario'),(3434,3514957,'EMBAÚBA','SP','embauba'),(3435,3515004,'EMBU DAS ARTES','SP','embu-das-artes'),(3436,3515103,'EMBU-GUAÇU','SP','embu-guacu'),(3437,3515129,'EMILIANÓPOLIS','SP','emilianopolis'),(3438,3515152,'ENGENHEIRO COELHO','SP','engenheiro-coelho'),(3439,3515186,'ESPÍRITO SANTO DO PINHAL','SP','espirito-santo-do-pinhal'),(3440,3515194,'ESPÍRITO SANTO DO TURVO','SP','espirito-santo-do-turvo'),(3441,3515202,'ESTRELA D\'OESTE','SP','estrela-doeste'),(3442,3515301,'ESTRELA DO NORTE','SP','estrela-do-norte'),(3443,3515350,'EUCLIDES DA CUNHA PAULISTA','SP','euclides-da-cunha-paulista'),(3444,3515400,'FARTURA','SP','fartura'),(3445,3515509,'FERNANDÓPOLIS','SP','fernandopolis'),(3446,3515608,'FERNANDO PRESTES','SP','fernando-prestes'),(3447,3515657,'FERNÃO','SP','fernao'),(3448,3515707,'FERRAZ DE VASCONCELOS','SP','ferraz-de-vasconcelos'),(3449,3515806,'FLORA RICA','SP','flora-rica'),(3450,3515905,'FLOREAL','SP','floreal'),(3451,3516002,'FLÓRIDA PAULISTA','SP','florida-paulista'),(3452,3516101,'FLORÍNIA','SP','florinia'),(3453,3516200,'FRANCA','SP','franca'),(3454,3516309,'FRANCISCO MORATO','SP','francisco-morato'),(3455,3516408,'FRANCO DA ROCHA','SP','franco-da-rocha'),(3456,3516507,'GABRIEL MONTEIRO','SP','gabriel-monteiro'),(3457,3516606,'GÁLIA','SP','galia'),(3458,3516705,'GARÇA','SP','garca'),(3459,3516804,'GASTÃO VIDIGAL','SP','gastao-vidigal'),(3460,3516853,'GAVIÃO PEIXOTO','SP','gaviao-peixoto'),(3461,3516903,'GENERAL SALGADO','SP','general-salgado'),(3462,3517000,'GETULINA','SP','getulina'),(3463,3517109,'GLICÉRIO','SP','glicerio'),(3464,3517208,'GUAIÇARA','SP','guaicara'),(3465,3517307,'GUAIMBÊ','SP','guaimbe'),(3466,3517406,'GUAÍRA','SP','guaira'),(3467,3517505,'GUAPIAÇU','SP','guapiacu'),(3468,3517604,'GUAPIARA','SP','guapiara'),(3469,3517703,'GUARÁ','SP','guara'),(3470,3517802,'GUARAÇAÍ','SP','guaracai'),(3471,3517901,'GUARACI','SP','guaraci'),(3472,3518008,'GUARANI D\'OESTE','SP','guarani-doeste'),(3473,3518107,'GUARANTÃ','SP','guaranta'),(3474,3518206,'GUARARAPES','SP','guararapes'),(3475,3518305,'GUARAREMA','SP','guararema'),(3476,3518404,'GUARATINGUETÁ','SP','guaratingueta'),(3477,3518503,'GUAREÍ','SP','guarei'),(3478,3518602,'GUARIBA','SP','guariba'),(3479,3518701,'GUARUJÁ','SP','guaruja'),(3480,3518800,'GUARULHOS','SP','guarulhos'),(3481,3518859,'GUATAPARÁ','SP','guatapara'),(3482,3518909,'GUZOLÂNDIA','SP','guzolandia'),(3483,3519006,'HERCULÂNDIA','SP','herculandia'),(3484,3519055,'HOLAMBRA','SP','holambra'),(3485,3519071,'HORTOLÂNDIA','SP','hortolandia'),(3486,3519105,'IACANGA','SP','iacanga'),(3487,3519204,'IACRI','SP','iacri'),(3488,3519253,'IARAS','SP','iaras'),(3489,3519303,'IBATÉ','SP','ibate'),(3490,3519402,'IBIRÁ','SP','ibira'),(3491,3519501,'IBIRAREMA','SP','ibirarema'),(3492,3519600,'IBITINGA','SP','ibitinga'),(3493,3519709,'IBIÚNA','SP','ibiuna'),(3494,3519808,'ICÉM','SP','icem'),(3495,3519907,'IEPÊ','SP','iepe'),(3496,3520004,'IGARAÇU DO TIETÊ','SP','igaracu-do-tiete'),(3497,3520103,'IGARAPAVA','SP','igarapava'),(3498,3520202,'IGARATÁ','SP','igarata'),(3499,3520301,'IGUAPE','SP','iguape'),(3500,3520400,'ILHABELA','SP','ilhabela'),(3501,3520426,'ILHA COMPRIDA','SP','ilha-comprida'),(3502,3520442,'ILHA SOLTEIRA','SP','ilha-solteira'),(3503,3520509,'INDAIATUBA','SP','indaiatuba'),(3504,3520608,'INDIANA','SP','indiana'),(3505,3520707,'INDIAPORÃ','SP','indiapora'),(3506,3520806,'INÚBIA PAULISTA','SP','inubia-paulista'),(3507,3520905,'IPAUSSU','SP','ipaussu'),(3508,3521002,'IPERÓ','SP','ipero'),(3509,3521101,'IPEÚNA','SP','ipeuna'),(3510,3521150,'IPIGUÁ','SP','ipigua'),(3511,3521200,'IPORANGA','SP','iporanga'),(3512,3521309,'IPUÃ','SP','ipua'),(3513,3521408,'IRACEMÁPOLIS','SP','iracemapolis'),(3514,3521507,'IRAPUÃ','SP','irapua'),(3515,3521606,'IRAPURU','SP','irapuru'),(3516,3521705,'ITABERÁ','SP','itabera'),(3517,3521804,'ITAÍ','SP','itai'),(3518,3521903,'ITAJOBI','SP','itajobi'),(3519,3522000,'ITAJU','SP','itaju'),(3520,3522109,'ITANHAÉM','SP','itanhaem'),(3521,3522158,'ITAÓCA','SP','itaoca'),(3522,3522208,'ITAPECERICA DA SERRA','SP','itapecerica-da-serra'),(3523,3522307,'ITAPETININGA','SP','itapetininga'),(3524,3522406,'ITAPEVA','SP','itapeva'),(3525,3522505,'ITAPEVI','SP','itapevi'),(3526,3522604,'ITAPIRA','SP','itapira'),(3527,3522653,'ITAPIRAPUÃ PAULISTA','SP','itapirapua-paulista'),(3528,3522703,'ITÁPOLIS','SP','itapolis'),(3529,3522802,'ITAPORANGA','SP','itaporanga'),(3530,3522901,'ITAPUÍ','SP','itapui'),(3531,3523008,'ITAPURA','SP','itapura'),(3532,3523107,'ITAQUAQUECETUBA','SP','itaquaquecetuba'),(3533,3523206,'ITARARÉ','SP','itarare'),(3534,3523305,'ITARIRI','SP','itariri'),(3535,3523404,'ITATIBA','SP','itatiba'),(3536,3523503,'ITATINGA','SP','itatinga'),(3537,3523602,'ITIRAPINA','SP','itirapina'),(3538,3523701,'ITIRAPUÃ','SP','itirapua'),(3539,3523800,'ITOBI','SP','itobi'),(3540,3523909,'ITU','SP','itu'),(3541,3524006,'ITUPEVA','SP','itupeva'),(3542,3524105,'ITUVERAVA','SP','ituverava'),(3543,3524204,'JABORANDI','SP','jaborandi'),(3544,3524303,'JABOTICABAL','SP','jaboticabal'),(3545,3524402,'JACAREÍ','SP','jacarei'),(3546,3524501,'JACI','SP','jaci'),(3547,3524600,'JACUPIRANGA','SP','jacupiranga'),(3548,3524709,'JAGUARIÚNA','SP','jaguariuna'),(3549,3524808,'JALES','SP','jales'),(3550,3524907,'JAMBEIRO','SP','jambeiro'),(3551,3525003,'JANDIRA','SP','jandira'),(3552,3525102,'JARDINÓPOLIS','SP','jardinopolis'),(3553,3525201,'JARINU','SP','jarinu'),(3554,3525300,'JAÚ','SP','jau'),(3555,3525409,'JERIQUARA','SP','jeriquara'),(3556,3525508,'JOANÓPOLIS','SP','joanopolis'),(3557,3525607,'JOÃO RAMALHO','SP','joao-ramalho'),(3558,3525706,'JOSÉ BONIFÁCIO','SP','jose-bonifacio'),(3559,3525805,'JÚLIO MESQUITA','SP','julio-mesquita'),(3560,3525854,'JUMIRIM','SP','jumirim'),(3561,3525904,'JUNDIAÍ','SP','jundiai'),(3562,3526001,'JUNQUEIRÓPOLIS','SP','junqueiropolis'),(3563,3526100,'JUQUIÁ','SP','juquia'),(3564,3526209,'JUQUITIBA','SP','juquitiba'),(3565,3526308,'LAGOINHA','SP','lagoinha'),(3566,3526407,'LARANJAL PAULISTA','SP','laranjal-paulista'),(3567,3526506,'LAVÍNIA','SP','lavinia'),(3568,3526605,'LAVRINHAS','SP','lavrinhas'),(3569,3526704,'LEME','SP','leme'),(3570,3526803,'LENÇÓIS PAULISTA','SP','lencois-paulista'),(3571,3526902,'LIMEIRA','SP','limeira'),(3572,3527009,'LINDÓIA','SP','lindoia'),(3573,3527108,'LINS','SP','lins'),(3574,3527207,'LORENA','SP','lorena'),(3575,3527256,'LOURDES','SP','lourdes'),(3576,3527306,'LOUVEIRA','SP','louveira'),(3577,3527405,'LUCÉLIA','SP','lucelia'),(3578,3527504,'LUCIANÓPOLIS','SP','lucianopolis'),(3579,3527603,'LUÍS ANTÔNIO','SP','luis-antonio'),(3580,3527702,'LUIZIÂNIA','SP','luiziania'),(3581,3527801,'LUPÉRCIO','SP','lupercio'),(3582,3527900,'LUTÉCIA','SP','lutecia'),(3583,3528007,'MACATUBA','SP','macatuba'),(3584,3528106,'MACAUBAL','SP','macaubal'),(3585,3528205,'MACEDÔNIA','SP','macedonia'),(3586,3528304,'MAGDA','SP','magda'),(3587,3528403,'MAIRINQUE','SP','mairinque'),(3588,3528502,'MAIRIPORÃ','SP','mairipora'),(3589,3528601,'MANDURI','SP','manduri'),(3590,3528700,'MARABÁ PAULISTA','SP','maraba-paulista'),(3591,3528809,'MARACAÍ','SP','maracai'),(3592,3528858,'MARAPOAMA','SP','marapoama'),(3593,3528908,'MARIÁPOLIS','SP','mariapolis'),(3594,3529005,'MARÍLIA','SP','marilia'),(3595,3529104,'MARINÓPOLIS','SP','marinopolis'),(3596,3529203,'MARTINÓPOLIS','SP','martinopolis'),(3597,3529302,'MATÃO','SP','matao'),(3598,3529401,'MAUÁ','SP','maua'),(3599,3529500,'MENDONÇA','SP','mendonca'),(3600,3529609,'MERIDIANO','SP','meridiano'),(3601,3529658,'MESÓPOLIS','SP','mesopolis'),(3602,3529708,'MIGUELÓPOLIS','SP','miguelopolis'),(3603,3529807,'MINEIROS DO TIETÊ','SP','mineiros-do-tiete'),(3604,3529906,'MIRACATU','SP','miracatu'),(3605,3530003,'MIRA ESTRELA','SP','mira-estrela'),(3606,3530102,'MIRANDÓPOLIS','SP','mirandopolis'),(3607,3530201,'MIRANTE DO PARANAPANEMA','SP','mirante-do-paranapanema'),(3608,3530300,'MIRASSOL','SP','mirassol'),(3609,3530409,'MIRASSOLÂNDIA','SP','mirassolandia'),(3610,3530508,'MOCOCA','SP','mococa'),(3611,3530607,'MOGI DAS CRUZES','SP','mogi-das-cruzes'),(3612,3530706,'MOGI GUAÇU','SP','mogi-guacu'),(3613,3530805,'MOJI MIRIM','SP','moji-mirim'),(3614,3530904,'MOMBUCA','SP','mombuca'),(3615,3531001,'MONÇÕES','SP','moncoes'),(3616,3531100,'MONGAGUÁ','SP','mongagua'),(3617,3531209,'MONTE ALEGRE DO SUL','SP','monte-alegre-do-sul'),(3618,3531308,'MONTE ALTO','SP','monte-alto'),(3619,3531407,'MONTE APRAZÍVEL','SP','monte-aprazivel'),(3620,3531506,'MONTE AZUL PAULISTA','SP','monte-azul-paulista'),(3621,3531605,'MONTE CASTELO','SP','monte-castelo'),(3622,3531704,'MONTEIRO LOBATO','SP','monteiro-lobato'),(3623,3531803,'MONTE MOR','SP','monte-mor'),(3624,3531902,'MORRO AGUDO','SP','morro-agudo'),(3625,3532009,'MORUNGABA','SP','morungaba'),(3626,3532058,'MOTUCA','SP','motuca'),(3627,3532108,'MURUTINGA DO SUL','SP','murutinga-do-sul'),(3628,3532157,'NANTES','SP','nantes'),(3629,3532207,'NARANDIBA','SP','narandiba'),(3630,3532306,'NATIVIDADE DA SERRA','SP','natividade-da-serra'),(3631,3532405,'NAZARÉ PAULISTA','SP','nazare-paulista'),(3632,3532504,'NEVES PAULISTA','SP','neves-paulista'),(3633,3532603,'NHANDEARA','SP','nhandeara'),(3634,3532702,'NIPOÃ','SP','nipoa'),(3635,3532801,'NOVA ALIANÇA','SP','nova-alianca'),(3636,3532827,'NOVA CAMPINA','SP','nova-campina'),(3637,3532843,'NOVA CANAÃ PAULISTA','SP','nova-canaa-paulista'),(3638,3532868,'NOVA CASTILHO','SP','nova-castilho'),(3639,3532900,'NOVA EUROPA','SP','nova-europa'),(3640,3533007,'NOVA GRANADA','SP','nova-granada'),(3641,3533106,'NOVA GUATAPORANGA','SP','nova-guataporanga'),(3642,3533205,'NOVA INDEPENDÊNCIA','SP','nova-independencia'),(3643,3533254,'NOVAIS','SP','novais'),(3644,3533304,'NOVA LUZITÂNIA','SP','nova-luzitania'),(3645,3533403,'NOVA ODESSA','SP','nova-odessa'),(3646,3533502,'NOVO HORIZONTE','SP','novo-horizonte'),(3647,3533601,'NUPORANGA','SP','nuporanga'),(3648,3533700,'OCAUÇU','SP','ocaucu'),(3649,3533809,'ÓLEO','SP','oleo'),(3650,3533908,'OLÍMPIA','SP','olimpia'),(3651,3534005,'ONDA VERDE','SP','onda-verde'),(3652,3534104,'ORIENTE','SP','oriente'),(3653,3534203,'ORINDIÚVA','SP','orindiuva'),(3654,3534302,'ORLÂNDIA','SP','orlandia'),(3655,3534401,'OSASCO','SP','osasco'),(3656,3534500,'OSCAR BRESSANE','SP','oscar-bressane'),(3657,3534609,'OSVALDO CRUZ','SP','osvaldo-cruz'),(3658,3534708,'OURINHOS','SP','ourinhos'),(3659,3534757,'OUROESTE','SP','ouroeste'),(3660,3534807,'OURO VERDE','SP','ouro-verde'),(3661,3534906,'PACAEMBU','SP','pacaembu'),(3662,3535002,'PALESTINA','SP','palestina'),(3663,3535101,'PALMARES PAULISTA','SP','palmares-paulista'),(3664,3535200,'PALMEIRA D\'OESTE','SP','palmeira-doeste'),(3665,3535309,'PALMITAL','SP','palmital'),(3666,3535408,'PANORAMA','SP','panorama'),(3667,3535507,'PARAGUAÇU PAULISTA','SP','paraguacu-paulista'),(3668,3535606,'PARAIBUNA','SP','paraibuna'),(3669,3535705,'PARAÍSO','SP','paraiso'),(3670,3535804,'PARANAPANEMA','SP','paranapanema'),(3671,3535903,'PARANAPUÃ','SP','paranapua'),(3672,3536000,'PARAPUÃ','SP','parapua'),(3673,3536109,'PARDINHO','SP','pardinho'),(3674,3536208,'PARIQUERA-AÇU','SP','pariquera-acu'),(3675,3536257,'PARISI','SP','parisi'),(3676,3536307,'PATROCÍNIO PAULISTA','SP','patrocinio-paulista'),(3677,3536406,'PAULICÉIA','SP','pauliceia'),(3678,3536505,'PAULÍNIA','SP','paulinia'),(3679,3536570,'PAULISTÂNIA','SP','paulistania'),(3680,3536604,'PAULO DE FARIA','SP','paulo-de-faria'),(3681,3536703,'PEDERNEIRAS','SP','pederneiras'),(3682,3536802,'PEDRA BELA','SP','pedra-bela'),(3683,3536901,'PEDRANÓPOLIS','SP','pedranopolis'),(3684,3537008,'PEDREGULHO','SP','pedregulho'),(3685,3537107,'PEDREIRA','SP','pedreira'),(3686,3537156,'PEDRINHAS PAULISTA','SP','pedrinhas-paulista'),(3687,3537206,'PEDRO DE TOLEDO','SP','pedro-de-toledo'),(3688,3537305,'PENÁPOLIS','SP','penapolis'),(3689,3537404,'PEREIRA BARRETO','SP','pereira-barreto'),(3690,3537503,'PEREIRAS','SP','pereiras'),(3691,3537602,'PERUÍBE','SP','peruibe'),(3692,3537701,'PIACATU','SP','piacatu'),(3693,3537800,'PIEDADE','SP','piedade'),(3694,3537909,'PILAR DO SUL','SP','pilar-do-sul'),(3695,3538006,'PINDAMONHANGABA','SP','pindamonhangaba'),(3696,3538105,'PINDORAMA','SP','pindorama'),(3697,3538204,'PINHALZINHO','SP','pinhalzinho'),(3698,3538303,'PIQUEROBI','SP','piquerobi'),(3699,3538501,'PIQUETE','SP','piquete'),(3700,3538600,'PIRACAIA','SP','piracaia'),(3701,3538709,'PIRACICABA','SP','piracicaba'),(3702,3538808,'PIRAJU','SP','piraju'),(3703,3538907,'PIRAJUÍ','SP','pirajui'),(3704,3539004,'PIRANGI','SP','pirangi'),(3705,3539103,'PIRAPORA DO BOM JESUS','SP','pirapora-do-bom-jesus'),(3706,3539202,'PIRAPOZINHO','SP','pirapozinho'),(3707,3539301,'PIRASSUNUNGA','SP','pirassununga'),(3708,3539400,'PIRATININGA','SP','piratininga'),(3709,3539509,'PITANGUEIRAS','SP','pitangueiras'),(3710,3539608,'PLANALTO','SP','planalto'),(3711,3539707,'PLATINA','SP','platina'),(3712,3539806,'POÁ','SP','poa'),(3713,3539905,'POLONI','SP','poloni'),(3714,3540002,'POMPÉIA','SP','pompeia'),(3715,3540101,'PONGAÍ','SP','pongai'),(3716,3540200,'PONTAL','SP','pontal'),(3717,3540259,'PONTALINDA','SP','pontalinda'),(3718,3540309,'PONTES GESTAL','SP','pontes-gestal'),(3719,3540408,'POPULINA','SP','populina'),(3720,3540507,'PORANGABA','SP','porangaba'),(3721,3540606,'PORTO FELIZ','SP','porto-feliz'),(3722,3540705,'PORTO FERREIRA','SP','porto-ferreira'),(3723,3540754,'POTIM','SP','potim'),(3724,3540804,'POTIRENDABA','SP','potirendaba'),(3725,3540853,'PRACINHA','SP','pracinha'),(3726,3540903,'PRADÓPOLIS','SP','pradopolis'),(3727,3541000,'PRAIA GRANDE','SP','praia-grande'),(3728,3541059,'PRATÂNIA','SP','pratania'),(3729,3541109,'PRESIDENTE ALVES','SP','presidente-alves'),(3730,3541208,'PRESIDENTE BERNARDES','SP','presidente-bernardes'),(3731,3541307,'PRESIDENTE EPITÁCIO','SP','presidente-epitacio'),(3732,3541406,'PRESIDENTE PRUDENTE','SP','presidente-prudente'),(3733,3541505,'PRESIDENTE VENCESLAU','SP','presidente-venceslau'),(3734,3541604,'PROMISSÃO','SP','promissao'),(3735,3541653,'QUADRA','SP','quadra'),(3736,3541703,'QUATÁ','SP','quata'),(3737,3541802,'QUEIROZ','SP','queiroz'),(3738,3541901,'QUELUZ','SP','queluz'),(3739,3542008,'QUINTANA','SP','quintana'),(3740,3542107,'RAFARD','SP','rafard'),(3741,3542206,'RANCHARIA','SP','rancharia'),(3742,3542305,'REDENÇÃO DA SERRA','SP','redencao-da-serra'),(3743,3542404,'REGENTE FEIJÓ','SP','regente-feijo'),(3744,3542503,'REGINÓPOLIS','SP','reginopolis'),(3745,3542602,'REGISTRO','SP','registro'),(3746,3542701,'RESTINGA','SP','restinga'),(3747,3542800,'RIBEIRA','SP','ribeira'),(3748,3542909,'RIBEIRÃO BONITO','SP','ribeirao-bonito'),(3749,3543006,'RIBEIRÃO BRANCO','SP','ribeirao-branco'),(3750,3543105,'RIBEIRÃO CORRENTE','SP','ribeirao-corrente'),(3751,3543204,'RIBEIRÃO DO SUL','SP','ribeirao-do-sul'),(3752,3543238,'RIBEIRÃO DOS ÍNDIOS','SP','ribeirao-dos-indios'),(3753,3543253,'RIBEIRÃO GRANDE','SP','ribeirao-grande'),(3754,3543303,'RIBEIRÃO PIRES','SP','ribeirao-pires'),(3755,3543402,'RIBEIRÃO PRETO','SP','ribeirao-preto'),(3756,3543501,'RIVERSUL','SP','riversul'),(3757,3543600,'RIFAINA','SP','rifaina'),(3758,3543709,'RINCÃO','SP','rincao'),(3759,3543808,'RINÓPOLIS','SP','rinopolis'),(3760,3543907,'RIO CLARO','SP','rio-claro'),(3761,3544004,'RIO DAS PEDRAS','SP','rio-das-pedras'),(3762,3544103,'RIO GRANDE DA SERRA','SP','rio-grande-da-serra'),(3763,3544202,'RIOLÂNDIA','SP','riolandia'),(3764,3544251,'ROSANA','SP','rosana'),(3765,3544301,'ROSEIRA','SP','roseira'),(3766,3544400,'RUBIÁCEA','SP','rubiacea'),(3767,3544509,'RUBINÉIA','SP','rubineia'),(3768,3544608,'SABINO','SP','sabino'),(3769,3544707,'SAGRES','SP','sagres'),(3770,3544806,'SALES','SP','sales'),(3771,3544905,'SALES OLIVEIRA','SP','sales-oliveira'),(3772,3545001,'SALESÓPOLIS','SP','salesopolis'),(3773,3545100,'SALMOURÃO','SP','salmourao'),(3774,3545159,'SALTINHO','SP','saltinho'),(3775,3545209,'SALTO','SP','salto'),(3776,3545308,'SALTO DE PIRAPORA','SP','salto-de-pirapora'),(3777,3545407,'SALTO GRANDE','SP','salto-grande'),(3778,3545506,'SANDOVALINA','SP','sandovalina'),(3779,3545605,'SANTA ADÉLIA','SP','santa-adelia'),(3780,3545704,'SANTA ALBERTINA','SP','santa-albertina'),(3781,3545803,'SANTA BÁRBARA D\'OESTE','SP','santa-barbara-doeste'),(3782,3546009,'SANTA BRANCA','SP','santa-branca'),(3783,3546108,'SANTA CLARA D\'OESTE','SP','santa-clara-doeste'),(3784,3546207,'SANTA CRUZ DA CONCEIÇÃO','SP','santa-cruz-da-conceicao'),(3785,3546256,'SANTA CRUZ DA ESPERANÇA','SP','santa-cruz-da-esperanca'),(3786,3546306,'SANTA CRUZ DAS PALMEIRAS','SP','santa-cruz-das-palmeiras'),(3787,3546405,'SANTA CRUZ DO RIO PARDO','SP','santa-cruz-do-rio-pardo'),(3788,3546504,'SANTA ERNESTINA','SP','santa-ernestina'),(3789,3546603,'SANTA FÉ DO SUL','SP','santa-fe-do-sul'),(3790,3546702,'SANTA GERTRUDES','SP','santa-gertrudes'),(3791,3546801,'SANTA ISABEL','SP','santa-isabel'),(3792,3546900,'SANTA LÚCIA','SP','santa-lucia'),(3793,3547007,'SANTA MARIA DA SERRA','SP','santa-maria-da-serra'),(3794,3547106,'SANTA MERCEDES','SP','santa-mercedes'),(3795,3547205,'SANTANA DA PONTE PENSA','SP','santana-da-ponte-pensa'),(3796,3547304,'SANTANA DE PARNAÍBA','SP','santana-de-parnaiba'),(3797,3547403,'SANTA RITA D\'OESTE','SP','santa-rita-doeste'),(3798,3547502,'SANTA RITA DO PASSA QUATRO','SP','santa-rita-do-passa-quatro'),(3799,3547601,'SANTA ROSA DE VITERBO','SP','santa-rosa-de-viterbo'),(3800,3547650,'SANTA SALETE','SP','santa-salete'),(3801,3547700,'SANTO ANASTÁCIO','SP','santo-anastacio'),(3802,3547809,'SANTO ANDRÉ','SP','santo-andre'),(3803,3547908,'SANTO ANTÔNIO DA ALEGRIA','SP','santo-antonio-da-alegria'),(3804,3548005,'SANTO ANTÔNIO DE POSSE','SP','santo-antonio-de-posse'),(3805,3548054,'SANTO ANTÔNIO DO ARACANGUÁ','SP','santo-antonio-do-aracangua'),(3806,3548104,'SANTO ANTÔNIO DO JARDIM','SP','santo-antonio-do-jardim'),(3807,3548203,'SANTO ANTÔNIO DO PINHAL','SP','santo-antonio-do-pinhal'),(3808,3548302,'SANTO EXPEDITO','SP','santo-expedito'),(3809,3548401,'SANTÓPOLIS DO AGUAPEÍ','SP','santopolis-do-aguapei'),(3810,3548500,'SANTOS','SP','santos'),(3811,3548609,'SÃO BENTO DO SAPUCAÍ','SP','sao-bento-do-sapucai'),(3812,3548708,'SÃO BERNARDO DO CAMPO','SP','sao-bernardo-do-campo'),(3813,3548807,'SÃO CAETANO DO SUL','SP','sao-caetano-do-sul'),(3814,3548906,'SÃO CARLOS','SP','sao-carlos'),(3815,3549003,'SÃO FRANCISCO','SP','sao-francisco'),(3816,3549102,'SÃO JOÃO DA BOA VISTA','SP','sao-joao-da-boa-vista'),(3817,3549201,'SÃO JOÃO DAS DUAS PONTES','SP','sao-joao-das-duas-pontes'),(3818,3549250,'SÃO JOÃO DE IRACEMA','SP','sao-joao-de-iracema'),(3819,3549300,'SÃO JOÃO DO PAU D\'ALHO','SP','sao-joao-do-pau-dalho'),(3820,3549409,'SÃO JOAQUIM DA BARRA','SP','sao-joaquim-da-barra'),(3821,3549508,'SÃO JOSÉ DA BELA VISTA','SP','sao-jose-da-bela-vista'),(3822,3549607,'SÃO JOSÉ DO BARREIRO','SP','sao-jose-do-barreiro'),(3823,3549706,'SÃO JOSÉ DO RIO PARDO','SP','sao-jose-do-rio-pardo'),(3824,3549805,'SÃO JOSÉ DO RIO PRETO','SP','sao-jose-do-rio-preto'),(3825,3549904,'SÃO JOSÉ DOS CAMPOS','SP','sao-jose-dos-campos'),(3826,3549953,'SÃO LOURENÇO DA SERRA','SP','sao-lourenco-da-serra'),(3827,3550001,'SÃO LUÍS DO PARAITINGA','SP','sao-luis-do-paraitinga'),(3828,3550100,'SÃO MANUEL','SP','sao-manuel'),(3829,3550209,'SÃO MIGUEL ARCANJO','SP','sao-miguel-arcanjo'),(3830,3550308,'SÃO PAULO','SP','sao-paulo'),(3831,3550407,'SÃO PEDRO','SP','sao-pedro'),(3832,3550506,'SÃO PEDRO DO TURVO','SP','sao-pedro-do-turvo'),(3833,3550605,'SÃO ROQUE','SP','sao-roque'),(3834,3550704,'SÃO SEBASTIÃO','SP','sao-sebastiao'),(3835,3550803,'SÃO SEBASTIÃO DA GRAMA','SP','sao-sebastiao-da-grama'),(3836,3550902,'SÃO SIMÃO','SP','sao-simao'),(3837,3551009,'SÃO VICENTE','SP','sao-vicente'),(3838,3551108,'SARAPUÍ','SP','sarapui'),(3839,3551207,'SARUTAIÁ','SP','sarutaia'),(3840,3551306,'SEBASTIANÓPOLIS DO SUL','SP','sebastianopolis-do-sul'),(3841,3551405,'SERRA AZUL','SP','serra-azul'),(3842,3551504,'SERRANA','SP','serrana'),(3843,3551603,'SERRA NEGRA','SP','serra-negra'),(3844,3551702,'SERTÃOZINHO','SP','sertaozinho'),(3845,3551801,'SETE BARRAS','SP','sete-barras'),(3846,3551900,'SEVERÍNIA','SP','severinia'),(3847,3552007,'SILVEIRAS','SP','silveiras'),(3848,3552106,'SOCORRO','SP','socorro'),(3849,3552205,'SOROCABA','SP','sorocaba'),(3850,3552304,'SUD MENNUCCI','SP','sud-mennucci'),(3851,3552403,'SUMARÉ','SP','sumare'),(3852,3552502,'SUZANO','SP','suzano'),(3853,3552551,'SUZANÁPOLIS','SP','suzanapolis'),(3854,3552601,'TABAPUÃ','SP','tabapua'),(3855,3552700,'TABATINGA','SP','tabatinga'),(3856,3552809,'TABOÃO DA SERRA','SP','taboao-da-serra'),(3857,3552908,'TACIBA','SP','taciba'),(3858,3553005,'TAGUAÍ','SP','taguai'),(3859,3553104,'TAIAÇU','SP','taiacu'),(3860,3553203,'TAIÚVA','SP','taiuva'),(3861,3553302,'TAMBAÚ','SP','tambau'),(3862,3553401,'TANABI','SP','tanabi'),(3863,3553500,'TAPIRAÍ','SP','tapirai'),(3864,3553609,'TAPIRATIBA','SP','tapiratiba'),(3865,3553658,'TAQUARAL','SP','taquaral'),(3866,3553708,'TAQUARITINGA','SP','taquaritinga'),(3867,3553807,'TAQUARITUBA','SP','taquarituba'),(3868,3553856,'TAQUARIVAÍ','SP','taquarivai'),(3869,3553906,'TARABAI','SP','tarabai'),(3870,3553955,'TARUMÃ','SP','taruma'),(3871,3554003,'TATUÍ','SP','tatui'),(3872,3554102,'TAUBATÉ','SP','taubate'),(3873,3554201,'TEJUPÁ','SP','tejupa'),(3874,3554300,'TEODORO SAMPAIO','SP','teodoro-sampaio'),(3875,3554409,'TERRA ROXA','SP','terra-roxa'),(3876,3554508,'TIETÊ','SP','tiete'),(3877,3554607,'TIMBURI','SP','timburi'),(3878,3554656,'TORRE DE PEDRA','SP','torre-de-pedra'),(3879,3554706,'TORRINHA','SP','torrinha'),(3880,3554755,'TRABIJU','SP','trabiju'),(3881,3554805,'TREMEMBÉ','SP','tremembe'),(3882,3554904,'TRÊS FRONTEIRAS','SP','tres-fronteiras'),(3883,3554953,'TUIUTI','SP','tuiuti'),(3884,3555000,'TUPÃ','SP','tupa'),(3885,3555109,'TUPI PAULISTA','SP','tupi-paulista'),(3886,3555208,'TURIÚBA','SP','turiuba'),(3887,3555307,'TURMALINA','SP','turmalina'),(3888,3555356,'UBARANA','SP','ubarana'),(3889,3555406,'UBATUBA','SP','ubatuba'),(3890,3555505,'UBIRAJARA','SP','ubirajara'),(3891,3555604,'UCHOA','SP','uchoa'),(3892,3555703,'UNIÃO PAULISTA','SP','uniao-paulista'),(3893,3555802,'URÂNIA','SP','urania'),(3894,3555901,'URU','SP','uru'),(3895,3556008,'URUPÊS','SP','urupes'),(3896,3556107,'VALENTIM GENTIL','SP','valentim-gentil'),(3897,3556206,'VALINHOS','SP','valinhos'),(3898,3556305,'VALPARAÍSO','SP','valparaiso'),(3899,3556354,'VARGEM','SP','vargem'),(3900,3556404,'VARGEM GRANDE DO SUL','SP','vargem-grande-do-sul'),(3901,3556453,'VARGEM GRANDE PAULISTA','SP','vargem-grande-paulista'),(3902,3556503,'VÁRZEA PAULISTA','SP','varzea-paulista'),(3903,3556602,'VERA CRUZ','SP','vera-cruz'),(3904,3556701,'VINHEDO','SP','vinhedo'),(3905,3556800,'VIRADOURO','SP','viradouro'),(3906,3556909,'VISTA ALEGRE DO ALTO','SP','vista-alegre-do-alto'),(3907,3556958,'VITÓRIA BRASIL','SP','vitoria-brasil'),(3908,3557006,'VOTORANTIM','SP','votorantim'),(3909,3557105,'VOTUPORANGA','SP','votuporanga'),(3910,3557154,'ZACARIAS','SP','zacarias'),(3911,3557204,'CHAVANTES','SP','chavantes'),(3912,3557303,'ESTIVA GERBI','SP','estiva-gerbi'),(3913,4100103,'ABATIÁ','PR','abatia'),(3914,4100202,'ADRIANÓPOLIS','PR','adrianopolis'),(3915,4100301,'AGUDOS DO SUL','PR','agudos-do-sul'),(3916,4100400,'ALMIRANTE TAMANDARÉ','PR','almirante-tamandare'),(3917,4100459,'ALTAMIRA DO PARANÁ','PR','altamira-do-parana'),(3918,4100509,'ALTÔNIA','PR','altonia'),(3919,4100608,'ALTO PARANÁ','PR','alto-parana'),(3920,4100707,'ALTO PIQUIRI','PR','alto-piquiri'),(3921,4100806,'ALVORADA DO SUL','PR','alvorada-do-sul'),(3922,4100905,'AMAPORÃ','PR','amapora'),(3923,4101002,'AMPÉRE','PR','ampere'),(3924,4101051,'ANAHY','PR','anahy'),(3925,4101101,'ANDIRÁ','PR','andira'),(3926,4101150,'ÂNGULO','PR','angulo'),(3927,4101200,'ANTONINA','PR','antonina'),(3928,4101309,'ANTÔNIO OLINTO','PR','antonio-olinto'),(3929,4101408,'APUCARANA','PR','apucarana'),(3930,4101507,'ARAPONGAS','PR','arapongas'),(3931,4101606,'ARAPOTI','PR','arapoti'),(3932,4101655,'ARAPUÃ','PR','arapua'),(3933,4101705,'ARARUNA','PR','araruna'),(3934,4101804,'ARAUCÁRIA','PR','araucaria'),(3935,4101853,'ARIRANHA DO IVAÍ','PR','ariranha-do-ivai'),(3936,4101903,'ASSAÍ','PR','assai'),(3937,4102000,'ASSIS CHATEAUBRIAND','PR','assis-chateaubriand'),(3938,4102109,'ASTORGA','PR','astorga'),(3939,4102208,'ATALAIA','PR','atalaia'),(3940,4102307,'BALSA NOVA','PR','balsa-nova'),(3941,4102406,'BANDEIRANTES','PR','bandeirantes'),(3942,4102505,'BARBOSA FERRAZ','PR','barbosa-ferraz'),(3943,4102604,'BARRACÃO','PR','barracao'),(3944,4102703,'BARRA DO JACARÉ','PR','barra-do-jacare'),(3945,4102752,'BELA VISTA DA CAROBA','PR','bela-vista-da-caroba'),(3946,4102802,'BELA VISTA DO PARAÍSO','PR','bela-vista-do-paraiso'),(3947,4102901,'BITURUNA','PR','bituruna'),(3948,4103008,'BOA ESPERANÇA','PR','boa-esperanca'),(3949,4103024,'BOA ESPERANÇA DO IGUAÇU','PR','boa-esperanca-do-iguacu'),(3950,4103040,'BOA VENTURA DE SÃO ROQUE','PR','boa-ventura-de-sao-roque'),(3951,4103057,'BOA VISTA DA APARECIDA','PR','boa-vista-da-aparecida'),(3952,4103107,'BOCAIÚVA DO SUL','PR','bocaiuva-do-sul'),(3953,4103156,'BOM JESUS DO SUL','PR','bom-jesus-do-sul'),(3954,4103206,'BOM SUCESSO','PR','bom-sucesso'),(3955,4103222,'BOM SUCESSO DO SUL','PR','bom-sucesso-do-sul'),(3956,4103305,'BORRAZÓPOLIS','PR','borrazopolis'),(3957,4103354,'BRAGANEY','PR','braganey'),(3958,4103370,'BRASILÂNDIA DO SUL','PR','brasilandia-do-sul'),(3959,4103404,'CAFEARA','PR','cafeara'),(3960,4103453,'CAFELÂNDIA','PR','cafelandia'),(3961,4103479,'CAFEZAL DO SUL','PR','cafezal-do-sul'),(3962,4103503,'CALIFÓRNIA','PR','california'),(3963,4103602,'CAMBARÁ','PR','cambara'),(3964,4103701,'CAMBÉ','PR','cambe'),(3965,4103800,'CAMBIRA','PR','cambira'),(3966,4103909,'CAMPINA DA LAGOA','PR','campina-da-lagoa'),(3967,4103958,'CAMPINA DO SIMÃO','PR','campina-do-simao'),(3968,4104006,'CAMPINA GRANDE DO SUL','PR','campina-grande-do-sul'),(3969,4104055,'CAMPO BONITO','PR','campo-bonito'),(3970,4104105,'CAMPO DO TENENTE','PR','campo-do-tenente'),(3971,4104204,'CAMPO LARGO','PR','campo-largo'),(3972,4104253,'CAMPO MAGRO','PR','campo-magro'),(3973,4104303,'CAMPO MOURÃO','PR','campo-mourao'),(3974,4104402,'CÂNDIDO DE ABREU','PR','candido-de-abreu'),(3975,4104428,'CANDÓI','PR','candoi'),(3976,4104451,'CANTAGALO','PR','cantagalo'),(3977,4104501,'CAPANEMA','PR','capanema'),(3978,4104600,'CAPITÃO LEÔNIDAS MARQUES','PR','capitao-leonidas-marques'),(3979,4104659,'CARAMBEÍ','PR','carambei'),(3980,4104709,'CARLÓPOLIS','PR','carlopolis'),(3981,4104808,'CASCAVEL','PR','cascavel'),(3982,4104907,'CASTRO','PR','castro'),(3983,4105003,'CATANDUVAS','PR','catanduvas'),(3984,4105102,'CENTENÁRIO DO SUL','PR','centenario-do-sul'),(3985,4105201,'CERRO AZUL','PR','cerro-azul'),(3986,4105300,'CÉU AZUL','PR','ceu-azul'),(3987,4105409,'CHOPINZINHO','PR','chopinzinho'),(3988,4105508,'CIANORTE','PR','cianorte'),(3989,4105607,'CIDADE GAÚCHA','PR','cidade-gaucha'),(3990,4105706,'CLEVELÂNDIA','PR','clevelandia'),(3991,4105805,'COLOMBO','PR','colombo'),(3992,4105904,'COLORADO','PR','colorado'),(3993,4106001,'CONGONHINHAS','PR','congonhinhas'),(3994,4106100,'CONSELHEIRO MAIRINCK','PR','conselheiro-mairinck'),(3995,4106209,'CONTENDA','PR','contenda'),(3996,4106308,'CORBÉLIA','PR','corbelia'),(3997,4106407,'CORNÉLIO PROCÓPIO','PR','cornelio-procopio'),(3998,4106456,'CORONEL DOMINGOS SOARES','PR','coronel-domingos-soares'),(3999,4106506,'CORONEL VIVIDA','PR','coronel-vivida'),(4000,4106555,'CORUMBATAÍ DO SUL','PR','corumbatai-do-sul'),(4001,4106571,'CRUZEIRO DO IGUAÇU','PR','cruzeiro-do-iguacu'),(4002,4106605,'CRUZEIRO DO OESTE','PR','cruzeiro-do-oeste'),(4003,4106704,'CRUZEIRO DO SUL','PR','cruzeiro-do-sul'),(4004,4106803,'CRUZ MACHADO','PR','cruz-machado'),(4005,4106852,'CRUZMALTINA','PR','cruzmaltina'),(4006,4106902,'CURITIBA','PR','curitiba'),(4007,4107009,'CURIÚVA','PR','curiuva'),(4008,4107108,'DIAMANTE DO NORTE','PR','diamante-do-norte'),(4009,4107124,'DIAMANTE DO SUL','PR','diamante-do-sul'),(4010,4107157,'DIAMANTE D\'OESTE','PR','diamante-doeste'),(4011,4107207,'DOIS VIZINHOS','PR','dois-vizinhos'),(4012,4107256,'DOURADINA','PR','douradina'),(4013,4107306,'DOUTOR CAMARGO','PR','doutor-camargo'),(4014,4107405,'ENÉAS MARQUES','PR','eneas-marques'),(4015,4107504,'ENGENHEIRO BELTRÃO','PR','engenheiro-beltrao'),(4016,4107520,'ESPERANÇA NOVA','PR','esperanca-nova'),(4017,4107538,'ENTRE RIOS DO OESTE','PR','entre-rios-do-oeste'),(4018,4107546,'ESPIGÃO ALTO DO IGUAÇU','PR','espigao-alto-do-iguacu'),(4019,4107553,'FAROL','PR','farol'),(4020,4107603,'FAXINAL','PR','faxinal'),(4021,4107652,'FAZENDA RIO GRANDE','PR','fazenda-rio-grande'),(4022,4107702,'FÊNIX','PR','fenix'),(4023,4107736,'FERNANDES PINHEIRO','PR','fernandes-pinheiro'),(4024,4107751,'FIGUEIRA','PR','figueira'),(4025,4107801,'FLORAÍ','PR','florai'),(4026,4107850,'FLOR DA SERRA DO SUL','PR','flor-da-serra-do-sul'),(4027,4107900,'FLORESTA','PR','floresta'),(4028,4108007,'FLORESTÓPOLIS','PR','florestopolis'),(4029,4108106,'FLÓRIDA','PR','florida'),(4030,4108205,'FORMOSA DO OESTE','PR','formosa-do-oeste'),(4031,4108304,'FOZ DO IGUAÇU','PR','foz-do-iguacu'),(4032,4108320,'FRANCISCO ALVES','PR','francisco-alves'),(4033,4108403,'FRANCISCO BELTRÃO','PR','francisco-beltrao'),(4034,4108452,'FOZ DO JORDÃO','PR','foz-do-jordao'),(4035,4108502,'GENERAL CARNEIRO','PR','general-carneiro'),(4036,4108551,'GODOY MOREIRA','PR','godoy-moreira'),(4037,4108601,'GOIOERÊ','PR','goioere'),(4038,4108650,'GOIOXIM','PR','goioxim'),(4039,4108700,'GRANDES RIOS','PR','grandes-rios'),(4040,4108809,'GUAÍRA','PR','guaira'),(4041,4108908,'GUAIRAÇÁ','PR','guairaca'),(4042,4108957,'GUAMIRANGA','PR','guamiranga'),(4043,4109005,'GUAPIRAMA','PR','guapirama'),(4044,4109104,'GUAPOREMA','PR','guaporema'),(4045,4109203,'GUARACI','PR','guaraci'),(4046,4109302,'GUARANIAÇU','PR','guaraniacu'),(4047,4109401,'GUARAPUAVA','PR','guarapuava'),(4048,4109500,'GUARAQUEÇABA','PR','guaraquecaba'),(4049,4109609,'GUARATUBA','PR','guaratuba'),(4050,4109658,'HONÓRIO SERPA','PR','honorio-serpa'),(4051,4109708,'IBAITI','PR','ibaiti'),(4052,4109757,'IBEMA','PR','ibema'),(4053,4109807,'IBIPORÃ','PR','ibipora'),(4054,4109906,'ICARAÍMA','PR','icaraima'),(4055,4110003,'IGUARAÇU','PR','iguaracu'),(4056,4110052,'IGUATU','PR','iguatu'),(4057,4110078,'IMBAÚ','PR','imbau'),(4058,4110102,'IMBITUVA','PR','imbituva'),(4059,4110201,'INÁCIO MARTINS','PR','inacio-martins'),(4060,4110300,'INAJÁ','PR','inaja'),(4061,4110409,'INDIANÓPOLIS','PR','indianopolis'),(4062,4110508,'IPIRANGA','PR','ipiranga'),(4063,4110607,'IPORÃ','PR','ipora'),(4064,4110656,'IRACEMA DO OESTE','PR','iracema-do-oeste'),(4065,4110706,'IRATI','PR','irati'),(4066,4110805,'IRETAMA','PR','iretama'),(4067,4110904,'ITAGUAJÉ','PR','itaguaje'),(4068,4110953,'ITAIPULÂNDIA','PR','itaipulandia'),(4069,4111001,'ITAMBARACÁ','PR','itambaraca'),(4070,4111100,'ITAMBÉ','PR','itambe'),(4071,4111209,'ITAPEJARA D\'OESTE','PR','itapejara-doeste'),(4072,4111258,'ITAPERUÇU','PR','itaperucu'),(4073,4111308,'ITAÚNA DO SUL','PR','itauna-do-sul'),(4074,4111407,'IVAÍ','PR','ivai'),(4075,4111506,'IVAIPORÃ','PR','ivaipora'),(4076,4111555,'IVATÉ','PR','ivate'),(4077,4111605,'IVATUBA','PR','ivatuba'),(4078,4111704,'JABOTI','PR','jaboti'),(4079,4111803,'JACAREZINHO','PR','jacarezinho'),(4080,4111902,'JAGUAPITÃ','PR','jaguapita'),(4081,4112009,'JAGUARIAÍVA','PR','jaguariaiva'),(4082,4112108,'JANDAIA DO SUL','PR','jandaia-do-sul'),(4083,4112207,'JANIÓPOLIS','PR','janiopolis'),(4084,4112306,'JAPIRA','PR','japira'),(4085,4112405,'JAPURÁ','PR','japura'),(4086,4112504,'JARDIM ALEGRE','PR','jardim-alegre'),(4087,4112603,'JARDIM OLINDA','PR','jardim-olinda'),(4088,4112702,'JATAIZINHO','PR','jataizinho'),(4089,4112751,'JESUÍTAS','PR','jesuitas'),(4090,4112801,'JOAQUIM TÁVORA','PR','joaquim-tavora'),(4091,4112900,'JUNDIAÍ DO SUL','PR','jundiai-do-sul'),(4092,4112959,'JURANDA','PR','juranda'),(4093,4113007,'JUSSARA','PR','jussara'),(4094,4113106,'KALORÉ','PR','kalore'),(4095,4113205,'LAPA','PR','lapa'),(4096,4113254,'LARANJAL','PR','laranjal'),(4097,4113304,'LARANJEIRAS DO SUL','PR','laranjeiras-do-sul'),(4098,4113403,'LEÓPOLIS','PR','leopolis'),(4099,4113429,'LIDIANÓPOLIS','PR','lidianopolis'),(4100,4113452,'LINDOESTE','PR','lindoeste'),(4101,4113502,'LOANDA','PR','loanda'),(4102,4113601,'LOBATO','PR','lobato'),(4103,4113700,'LONDRINA','PR','londrina'),(4104,4113734,'LUIZIANA','PR','luiziana'),(4105,4113759,'LUNARDELLI','PR','lunardelli'),(4106,4113809,'LUPIONÓPOLIS','PR','lupionopolis'),(4107,4113908,'MALLET','PR','mallet'),(4108,4114005,'MAMBORÊ','PR','mambore'),(4109,4114104,'MANDAGUAÇU','PR','mandaguacu'),(4110,4114203,'MANDAGUARI','PR','mandaguari'),(4111,4114302,'MANDIRITUBA','PR','mandirituba'),(4112,4114351,'MANFRINÓPOLIS','PR','manfrinopolis'),(4113,4114401,'MANGUEIRINHA','PR','mangueirinha'),(4114,4114500,'MANOEL RIBAS','PR','manoel-ribas'),(4115,4114609,'MARECHAL CÂNDIDO RONDON','PR','marechal-candido-rondon'),(4116,4114708,'MARIA HELENA','PR','maria-helena'),(4117,4114807,'MARIALVA','PR','marialva'),(4118,4114906,'MARILÂNDIA DO SUL','PR','marilandia-do-sul'),(4119,4115002,'MARILENA','PR','marilena'),(4120,4115101,'MARILUZ','PR','mariluz'),(4121,4115200,'MARINGÁ','PR','maringa'),(4122,4115309,'MARIÓPOLIS','PR','mariopolis'),(4123,4115358,'MARIPÁ','PR','maripa'),(4124,4115408,'MARMELEIRO','PR','marmeleiro'),(4125,4115457,'MARQUINHO','PR','marquinho'),(4126,4115507,'MARUMBI','PR','marumbi'),(4127,4115606,'MATELÂNDIA','PR','matelandia'),(4128,4115705,'MATINHOS','PR','matinhos'),(4129,4115739,'MATO RICO','PR','mato-rico'),(4130,4115754,'MAUÁ DA SERRA','PR','maua-da-serra'),(4131,4115804,'MEDIANEIRA','PR','medianeira'),(4132,4115853,'MERCEDES','PR','mercedes'),(4133,4115903,'MIRADOR','PR','mirador'),(4134,4116000,'MIRASELVA','PR','miraselva'),(4135,4116059,'MISSAL','PR','missal'),(4136,4116109,'MOREIRA SALES','PR','moreira-sales'),(4137,4116208,'MORRETES','PR','morretes'),(4138,4116307,'MUNHOZ DE MELO','PR','munhoz-de-melo'),(4139,4116406,'NOSSA SENHORA DAS GRAÇAS','PR','nossa-senhora-das-gracas'),(4140,4116505,'NOVA ALIANÇA DO IVAÍ','PR','nova-alianca-do-ivai'),(4141,4116604,'NOVA AMÉRICA DA COLINA','PR','nova-america-da-colina'),(4142,4116703,'NOVA AURORA','PR','nova-aurora'),(4143,4116802,'NOVA CANTU','PR','nova-cantu'),(4144,4116901,'NOVA ESPERANÇA','PR','nova-esperanca'),(4145,4116950,'NOVA ESPERANÇA DO SUDOESTE','PR','nova-esperanca-do-sudoeste'),(4146,4117008,'NOVA FÁTIMA','PR','nova-fatima'),(4147,4117057,'NOVA LARANJEIRAS','PR','nova-laranjeiras'),(4148,4117107,'NOVA LONDRINA','PR','nova-londrina'),(4149,4117206,'NOVA OLÍMPIA','PR','nova-olimpia'),(4150,4117214,'NOVA SANTA BÁRBARA','PR','nova-santa-barbara'),(4151,4117222,'NOVA SANTA ROSA','PR','nova-santa-rosa'),(4152,4117255,'NOVA PRATA DO IGUAÇU','PR','nova-prata-do-iguacu'),(4153,4117271,'NOVA TEBAS','PR','nova-tebas'),(4154,4117297,'NOVO ITACOLOMI','PR','novo-itacolomi'),(4155,4117305,'ORTIGUEIRA','PR','ortigueira'),(4156,4117404,'OURIZONA','PR','ourizona'),(4157,4117453,'OURO VERDE DO OESTE','PR','ouro-verde-do-oeste'),(4158,4117503,'PAIÇANDU','PR','paicandu'),(4159,4117602,'PALMAS','PR','palmas'),(4160,4117701,'PALMEIRA','PR','palmeira'),(4161,4117800,'PALMITAL','PR','palmital'),(4162,4117909,'PALOTINA','PR','palotina'),(4163,4118006,'PARAÍSO DO NORTE','PR','paraiso-do-norte'),(4164,4118105,'PARANACITY','PR','paranacity'),(4165,4118204,'PARANAGUÁ','PR','paranagua'),(4166,4118303,'PARANAPOEMA','PR','paranapoema'),(4167,4118402,'PARANAVAÍ','PR','paranavai'),(4168,4118451,'PATO BRAGADO','PR','pato-bragado'),(4169,4118501,'PATO BRANCO','PR','pato-branco'),(4170,4118600,'PAULA FREITAS','PR','paula-freitas'),(4171,4118709,'PAULO FRONTIN','PR','paulo-frontin'),(4172,4118808,'PEABIRU','PR','peabiru'),(4173,4118857,'PEROBAL','PR','perobal'),(4174,4118907,'PÉROLA','PR','perola'),(4175,4119004,'PÉROLA D\'OESTE','PR','perola-doeste'),(4176,4119103,'PIÊN','PR','pien'),(4177,4119152,'PINHAIS','PR','pinhais'),(4178,4119202,'PINHALÃO','PR','pinhalao'),(4179,4119251,'PINHAL DE SÃO BENTO','PR','pinhal-de-sao-bento'),(4180,4119301,'PINHÃO','PR','pinhao'),(4181,4119400,'PIRAÍ DO SUL','PR','pirai-do-sul'),(4182,4119509,'PIRAQUARA','PR','piraquara'),(4183,4119608,'PITANGA','PR','pitanga'),(4184,4119657,'PITANGUEIRAS','PR','pitangueiras'),(4185,4119707,'PLANALTINA DO PARANÁ','PR','planaltina-do-parana'),(4186,4119806,'PLANALTO','PR','planalto'),(4187,4119905,'PONTA GROSSA','PR','ponta-grossa'),(4188,4119954,'PONTAL DO PARANÁ','PR','pontal-do-parana'),(4189,4120002,'PORECATU','PR','porecatu'),(4190,4120101,'PORTO AMAZONAS','PR','porto-amazonas'),(4191,4120150,'PORTO BARREIRO','PR','porto-barreiro'),(4192,4120200,'PORTO RICO','PR','porto-rico'),(4193,4120309,'PORTO VITÓRIA','PR','porto-vitoria'),(4194,4120333,'PRADO FERREIRA','PR','prado-ferreira'),(4195,4120358,'PRANCHITA','PR','pranchita'),(4196,4120408,'PRESIDENTE CASTELO BRANCO','PR','presidente-castelo-branco'),(4197,4120507,'PRIMEIRO DE MAIO','PR','primeiro-de-maio'),(4198,4120606,'PRUDENTÓPOLIS','PR','prudentopolis'),(4199,4120655,'QUARTO CENTENÁRIO','PR','quarto-centenario'),(4200,4120705,'QUATIGUÁ','PR','quatigua'),(4201,4120804,'QUATRO BARRAS','PR','quatro-barras'),(4202,4120853,'QUATRO PONTES','PR','quatro-pontes'),(4203,4120903,'QUEDAS DO IGUAÇU','PR','quedas-do-iguacu'),(4204,4121000,'QUERÊNCIA DO NORTE','PR','querencia-do-norte'),(4205,4121109,'QUINTA DO SOL','PR','quinta-do-sol'),(4206,4121208,'QUITANDINHA','PR','quitandinha'),(4207,4121257,'RAMILÂNDIA','PR','ramilandia'),(4208,4121307,'RANCHO ALEGRE','PR','rancho-alegre'),(4209,4121356,'RANCHO ALEGRE D\'OESTE','PR','rancho-alegre-doeste'),(4210,4121406,'REALEZA','PR','realeza'),(4211,4121505,'REBOUÇAS','PR','reboucas'),(4212,4121604,'RENASCENÇA','PR','renascenca'),(4213,4121703,'RESERVA','PR','reserva'),(4214,4121752,'RESERVA DO IGUAÇU','PR','reserva-do-iguacu'),(4215,4121802,'RIBEIRÃO CLARO','PR','ribeirao-claro'),(4216,4121901,'RIBEIRÃO DO PINHAL','PR','ribeirao-do-pinhal'),(4217,4122008,'RIO AZUL','PR','rio-azul'),(4218,4122107,'RIO BOM','PR','rio-bom'),(4219,4122156,'RIO BONITO DO IGUAÇU','PR','rio-bonito-do-iguacu'),(4220,4122172,'RIO BRANCO DO IVAÍ','PR','rio-branco-do-ivai'),(4221,4122206,'RIO BRANCO DO SUL','PR','rio-branco-do-sul'),(4222,4122305,'RIO NEGRO','PR','rio-negro'),(4223,4122404,'ROLÂNDIA','PR','rolandia'),(4224,4122503,'RONCADOR','PR','roncador'),(4225,4122602,'RONDON','PR','rondon'),(4226,4122651,'ROSÁRIO DO IVAÍ','PR','rosario-do-ivai'),(4227,4122701,'SABÁUDIA','PR','sabaudia'),(4228,4122800,'SALGADO FILHO','PR','salgado-filho'),(4229,4122909,'SALTO DO ITARARÉ','PR','salto-do-itarare'),(4230,4123006,'SALTO DO LONTRA','PR','salto-do-lontra'),(4231,4123105,'SANTA AMÉLIA','PR','santa-amelia'),(4232,4123204,'SANTA CECÍLIA DO PAVÃO','PR','santa-cecilia-do-pavao'),(4233,4123303,'SANTA CRUZ DE MONTE CASTELO','PR','santa-cruz-de-monte-castelo'),(4234,4123402,'SANTA FÉ','PR','santa-fe'),(4235,4123501,'SANTA HELENA','PR','santa-helena'),(4236,4123600,'SANTA INÊS','PR','santa-ines'),(4237,4123709,'SANTA ISABEL DO IVAÍ','PR','santa-isabel-do-ivai'),(4238,4123808,'SANTA IZABEL DO OESTE','PR','santa-izabel-do-oeste'),(4239,4123824,'SANTA LÚCIA','PR','santa-lucia'),(4240,4123857,'SANTA MARIA DO OESTE','PR','santa-maria-do-oeste'),(4241,4123907,'SANTA MARIANA','PR','santa-mariana'),(4242,4123956,'SANTA MÔNICA','PR','santa-monica'),(4243,4124004,'SANTANA DO ITARARÉ','PR','santana-do-itarare'),(4244,4124020,'SANTA TEREZA DO OESTE','PR','santa-tereza-do-oeste'),(4245,4124053,'SANTA TEREZINHA DE ITAIPU','PR','santa-terezinha-de-itaipu'),(4246,4124103,'SANTO ANTÔNIO DA PLATINA','PR','santo-antonio-da-platina'),(4247,4124202,'SANTO ANTÔNIO DO CAIUÁ','PR','santo-antonio-do-caiua'),(4248,4124301,'SANTO ANTÔNIO DO PARAÍSO','PR','santo-antonio-do-paraiso'),(4249,4124400,'SANTO ANTÔNIO DO SUDOESTE','PR','santo-antonio-do-sudoeste'),(4250,4124509,'SANTO INÁCIO','PR','santo-inacio'),(4251,4124608,'SÃO CARLOS DO IVAÍ','PR','sao-carlos-do-ivai'),(4252,4124707,'SÃO JERÔNIMO DA SERRA','PR','sao-jeronimo-da-serra'),(4253,4124806,'SÃO JOÃO','PR','sao-joao'),(4254,4124905,'SÃO JOÃO DO CAIUÁ','PR','sao-joao-do-caiua'),(4255,4125001,'SÃO JOÃO DO IVAÍ','PR','sao-joao-do-ivai'),(4256,4125100,'SÃO JOÃO DO TRIUNFO','PR','sao-joao-do-triunfo'),(4257,4125209,'SÃO JORGE D\'OESTE','PR','sao-jorge-doeste'),(4258,4125308,'SÃO JORGE DO IVAÍ','PR','sao-jorge-do-ivai'),(4259,4125357,'SÃO JORGE DO PATROCÍNIO','PR','sao-jorge-do-patrocinio'),(4260,4125407,'SÃO JOSÉ DA BOA VISTA','PR','sao-jose-da-boa-vista'),(4261,4125456,'SÃO JOSÉ DAS PALMEIRAS','PR','sao-jose-das-palmeiras'),(4262,4125506,'SÃO JOSÉ DOS PINHAIS','PR','sao-jose-dos-pinhais'),(4263,4125555,'SÃO MANOEL DO PARANÁ','PR','sao-manoel-do-parana'),(4264,4125605,'SÃO MATEUS DO SUL','PR','sao-mateus-do-sul'),(4265,4125704,'SÃO MIGUEL DO IGUAÇU','PR','sao-miguel-do-iguacu'),(4266,4125753,'SÃO PEDRO DO IGUAÇU','PR','sao-pedro-do-iguacu'),(4267,4125803,'SÃO PEDRO DO IVAÍ','PR','sao-pedro-do-ivai'),(4268,4125902,'SÃO PEDRO DO PARANÁ','PR','sao-pedro-do-parana'),(4269,4126009,'SÃO SEBASTIÃO DA AMOREIRA','PR','sao-sebastiao-da-amoreira'),(4270,4126108,'SÃO TOMÉ','PR','sao-tome'),(4271,4126207,'SAPOPEMA','PR','sapopema'),(4272,4126256,'SARANDI','PR','sarandi'),(4273,4126272,'SAUDADE DO IGUAÇU','PR','saudade-do-iguacu'),(4274,4126306,'SENGÉS','PR','senges'),(4275,4126355,'SERRANÓPOLIS DO IGUAÇU','PR','serranopolis-do-iguacu'),(4276,4126405,'SERTANEJA','PR','sertaneja'),(4277,4126504,'SERTANÓPOLIS','PR','sertanopolis'),(4278,4126603,'SIQUEIRA CAMPOS','PR','siqueira-campos'),(4279,4126652,'SULINA','PR','sulina'),(4280,4126678,'TAMARANA','PR','tamarana'),(4281,4126702,'TAMBOARA','PR','tamboara'),(4282,4126801,'TAPEJARA','PR','tapejara'),(4283,4126900,'TAPIRA','PR','tapira'),(4284,4127007,'TEIXEIRA SOARES','PR','teixeira-soares'),(4285,4127106,'TELÊMACO BORBA','PR','telemaco-borba'),(4286,4127205,'TERRA BOA','PR','terra-boa'),(4287,4127304,'TERRA RICA','PR','terra-rica'),(4288,4127403,'TERRA ROXA','PR','terra-roxa'),(4289,4127502,'TIBAGI','PR','tibagi'),(4290,4127601,'TIJUCAS DO SUL','PR','tijucas-do-sul'),(4291,4127700,'TOLEDO','PR','toledo'),(4292,4127809,'TOMAZINA','PR','tomazina'),(4293,4127858,'TRÊS BARRAS DO PARANÁ','PR','tres-barras-do-parana'),(4294,4127882,'TUNAS DO PARANÁ','PR','tunas-do-parana'),(4295,4127908,'TUNEIRAS DO OESTE','PR','tuneiras-do-oeste'),(4296,4127957,'TUPÃSSI','PR','tupassi'),(4297,4127965,'TURVO','PR','turvo'),(4298,4128005,'UBIRATÃ','PR','ubirata'),(4299,4128104,'UMUARAMA','PR','umuarama'),(4300,4128203,'UNIÃO DA VITÓRIA','PR','uniao-da-vitoria'),(4301,4128302,'UNIFLOR','PR','uniflor'),(4302,4128401,'URAÍ','PR','urai'),(4303,4128500,'WENCESLAU BRAZ','PR','wenceslau-braz'),(4304,4128534,'VENTANIA','PR','ventania'),(4305,4128559,'VERA CRUZ DO OESTE','PR','vera-cruz-do-oeste'),(4306,4128609,'VERÊ','PR','vere'),(4307,4128625,'ALTO PARAÍSO','PR','alto-paraiso'),(4308,4128633,'DOUTOR ULYSSES','PR','doutor-ulysses'),(4309,4128658,'VIRMOND','PR','virmond'),(4310,4128708,'VITORINO','PR','vitorino'),(4311,4128807,'XAMBRÊ','PR','xambre'),(4312,4200051,'ABDON BATISTA','SC','abdon-batista'),(4313,4200101,'ABELARDO LUZ','SC','abelardo-luz'),(4314,4200200,'AGROLÂNDIA','SC','agrolandia'),(4315,4200309,'AGRONÔMICA','SC','agronomica'),(4316,4200408,'ÁGUA DOCE','SC','agua-doce'),(4317,4200507,'ÁGUAS DE CHAPECÓ','SC','aguas-de-chapeco'),(4318,4200556,'ÁGUAS FRIAS','SC','aguas-frias'),(4319,4200606,'ÁGUAS MORNAS','SC','aguas-mornas'),(4320,4200705,'ALFREDO WAGNER','SC','alfredo-wagner'),(4321,4200754,'ALTO BELA VISTA','SC','alto-bela-vista'),(4322,4200804,'ANCHIETA','SC','anchieta'),(4323,4200903,'ANGELINA','SC','angelina'),(4324,4201000,'ANITA GARIBALDI','SC','anita-garibaldi'),(4325,4201109,'ANITÁPOLIS','SC','anitapolis'),(4326,4201208,'ANTÔNIO CARLOS','SC','antonio-carlos'),(4327,4201257,'APIÚNA','SC','apiuna'),(4328,4201273,'ARABUTÃ','SC','arabuta'),(4329,4201307,'ARAQUARI','SC','araquari'),(4330,4201406,'ARARANGUÁ','SC','ararangua'),(4331,4201505,'ARMAZÉM','SC','armazem'),(4332,4201604,'ARROIO TRINTA','SC','arroio-trinta'),(4333,4201653,'ARVOREDO','SC','arvoredo'),(4334,4201703,'ASCURRA','SC','ascurra'),(4335,4201802,'ATALANTA','SC','atalanta'),(4336,4201901,'AURORA','SC','aurora'),(4337,4201950,'BALNEÁRIO ARROIO DO SILVA','SC','balneario-arroio-do-silva'),(4338,4202008,'BALNEÁRIO CAMBORIÚ','SC','balneario-camboriu'),(4339,4202057,'BALNEÁRIO BARRA DO SUL','SC','balneario-barra-do-sul'),(4340,4202073,'BALNEÁRIO GAIVOTA','SC','balneario-gaivota'),(4341,4202081,'BANDEIRANTE','SC','bandeirante'),(4342,4202099,'BARRA BONITA','SC','barra-bonita'),(4343,4202107,'BARRA VELHA','SC','barra-velha'),(4344,4202131,'BELA VISTA DO TOLDO','SC','bela-vista-do-toldo'),(4345,4202156,'BELMONTE','SC','belmonte'),(4346,4202206,'BENEDITO NOVO','SC','benedito-novo'),(4347,4202305,'BIGUAÇU','SC','biguacu'),(4348,4202404,'BLUMENAU','SC','blumenau'),(4349,4202438,'BOCAINA DO SUL','SC','bocaina-do-sul'),(4350,4202453,'BOMBINHAS','SC','bombinhas'),(4351,4202503,'BOM JARDIM DA SERRA','SC','bom-jardim-da-serra'),(4352,4202537,'BOM JESUS','SC','bom-jesus'),(4353,4202578,'BOM JESUS DO OESTE','SC','bom-jesus-do-oeste'),(4354,4202602,'BOM RETIRO','SC','bom-retiro'),(4355,4202701,'BOTUVERÁ','SC','botuvera'),(4356,4202800,'BRAÇO DO NORTE','SC','braco-do-norte'),(4357,4202859,'BRAÇO DO TROMBUDO','SC','braco-do-trombudo'),(4358,4202875,'BRUNÓPOLIS','SC','brunopolis'),(4359,4202909,'BRUSQUE','SC','brusque'),(4360,4203006,'CAÇADOR','SC','cacador'),(4361,4203105,'CAIBI','SC','caibi'),(4362,4203154,'CALMON','SC','calmon'),(4363,4203204,'CAMBORIÚ','SC','camboriu'),(4364,4203253,'CAPÃO ALTO','SC','capao-alto'),(4365,4203303,'CAMPO ALEGRE','SC','campo-alegre'),(4366,4203402,'CAMPO BELO DO SUL','SC','campo-belo-do-sul'),(4367,4203501,'CAMPO ERÊ','SC','campo-ere'),(4368,4203600,'CAMPOS NOVOS','SC','campos-novos'),(4369,4203709,'CANELINHA','SC','canelinha'),(4370,4203808,'CANOINHAS','SC','canoinhas'),(4371,4203907,'CAPINZAL','SC','capinzal'),(4372,4203956,'CAPIVARI DE BAIXO','SC','capivari-de-baixo'),(4373,4204004,'CATANDUVAS','SC','catanduvas'),(4374,4204103,'CAXAMBU DO SUL','SC','caxambu-do-sul'),(4375,4204152,'CELSO RAMOS','SC','celso-ramos'),(4376,4204178,'CERRO NEGRO','SC','cerro-negro'),(4377,4204194,'CHAPADÃO DO LAGEADO','SC','chapadao-do-lageado'),(4378,4204202,'CHAPECÓ','SC','chapeco'),(4379,4204251,'COCAL DO SUL','SC','cocal-do-sul'),(4380,4204301,'CONCÓRDIA','SC','concordia'),(4381,4204350,'CORDILHEIRA ALTA','SC','cordilheira-alta'),(4382,4204400,'CORONEL FREITAS','SC','coronel-freitas'),(4383,4204459,'CORONEL MARTINS','SC','coronel-martins'),(4384,4204509,'CORUPÁ','SC','corupa'),(4385,4204558,'CORREIA PINTO','SC','correia-pinto'),(4386,4204608,'CRICIÚMA','SC','criciuma'),(4387,4204707,'CUNHA PORÃ','SC','cunha-pora'),(4388,4204756,'CUNHATAÍ','SC','cunhatai'),(4389,4204806,'CURITIBANOS','SC','curitibanos'),(4390,4204905,'DESCANSO','SC','descanso'),(4391,4205001,'DIONÍSIO CERQUEIRA','SC','dionisio-cerqueira'),(4392,4205100,'DONA EMMA','SC','dona-emma'),(4393,4205159,'DOUTOR PEDRINHO','SC','doutor-pedrinho'),(4394,4205175,'ENTRE RIOS','SC','entre-rios'),(4395,4205191,'ERMO','SC','ermo'),(4396,4205209,'ERVAL VELHO','SC','erval-velho'),(4397,4205308,'FAXINAL DOS GUEDES','SC','faxinal-dos-guedes'),(4398,4205357,'FLOR DO SERTÃO','SC','flor-do-sertao'),(4399,4205407,'FLORIANÓPOLIS','SC','florianopolis'),(4400,4205431,'FORMOSA DO SUL','SC','formosa-do-sul'),(4401,4205456,'FORQUILHINHA','SC','forquilhinha'),(4402,4205506,'FRAIBURGO','SC','fraiburgo'),(4403,4205555,'FREI ROGÉRIO','SC','frei-rogerio'),(4404,4205605,'GALVÃO','SC','galvao'),(4405,4205704,'GAROPABA','SC','garopaba'),(4406,4205803,'GARUVA','SC','garuva'),(4407,4205902,'GASPAR','SC','gaspar'),(4408,4206009,'GOVERNADOR CELSO RAMOS','SC','governador-celso-ramos'),(4409,4206108,'GRÃO PARÁ','SC','grao-para'),(4410,4206207,'GRAVATAL','SC','gravatal'),(4411,4206306,'GUABIRUBA','SC','guabiruba'),(4412,4206405,'GUARACIABA','SC','guaraciaba'),(4413,4206504,'GUARAMIRIM','SC','guaramirim'),(4414,4206603,'GUARUJÁ DO SUL','SC','guaruja-do-sul'),(4415,4206652,'GUATAMBÚ','SC','guatambu'),(4416,4206702,'HERVAL D\'OESTE','SC','herval-doeste'),(4417,4206751,'IBIAM','SC','ibiam'),(4418,4206801,'IBICARÉ','SC','ibicare'),(4419,4206900,'IBIRAMA','SC','ibirama'),(4420,4207007,'IÇARA','SC','icara'),(4421,4207106,'ILHOTA','SC','ilhota'),(4422,4207205,'IMARUÍ','SC','imarui'),(4423,4207304,'IMBITUBA','SC','imbituba'),(4424,4207403,'IMBUIA','SC','imbuia'),(4425,4207502,'INDAIAL','SC','indaial'),(4426,4207577,'IOMERÊ','SC','iomere'),(4427,4207601,'IPIRA','SC','ipira'),(4428,4207650,'IPORÃ DO OESTE','SC','ipora-do-oeste'),(4429,4207684,'IPUAÇU','SC','ipuacu'),(4430,4207700,'IPUMIRIM','SC','ipumirim'),(4431,4207759,'IRACEMINHA','SC','iraceminha'),(4432,4207809,'IRANI','SC','irani'),(4433,4207858,'IRATI','SC','irati'),(4434,4207908,'IRINEÓPOLIS','SC','irineopolis'),(4435,4208005,'ITÁ','SC','ita'),(4436,4208104,'ITAIÓPOLIS','SC','itaiopolis'),(4437,4208203,'ITAJAÍ','SC','itajai'),(4438,4208302,'ITAPEMA','SC','itapema'),(4439,4208401,'ITAPIRANGA','SC','itapiranga'),(4440,4208450,'ITAPOÁ','SC','itapoa'),(4441,4208500,'ITUPORANGA','SC','ituporanga'),(4442,4208609,'JABORÁ','SC','jabora'),(4443,4208708,'JACINTO MACHADO','SC','jacinto-machado'),(4444,4208807,'JAGUARUNA','SC','jaguaruna'),(4445,4208906,'JARAGUÁ DO SUL','SC','jaragua-do-sul'),(4446,4208955,'JARDINÓPOLIS','SC','jardinopolis'),(4447,4209003,'JOAÇABA','SC','joacaba'),(4448,4209102,'JOINVILLE','SC','joinville'),(4449,4209151,'JOSÉ BOITEUX','SC','jose-boiteux'),(4450,4209177,'JUPIÁ','SC','jupia'),(4451,4209201,'LACERDÓPOLIS','SC','lacerdopolis'),(4452,4209300,'LAGES','SC','lages'),(4453,4209409,'LAGUNA','SC','laguna'),(4454,4209458,'LAJEADO GRANDE','SC','lajeado-grande'),(4455,4209508,'LAURENTINO','SC','laurentino'),(4456,4209607,'LAURO MULLER','SC','lauro-muller'),(4457,4209706,'LEBON RÉGIS','SC','lebon-regis'),(4458,4209805,'LEOBERTO LEAL','SC','leoberto-leal'),(4459,4209854,'LINDÓIA DO SUL','SC','lindoia-do-sul'),(4460,4209904,'LONTRAS','SC','lontras'),(4461,4210001,'LUIZ ALVES','SC','luiz-alves'),(4462,4210035,'LUZERNA','SC','luzerna'),(4463,4210050,'MACIEIRA','SC','macieira'),(4464,4210100,'MAFRA','SC','mafra'),(4465,4210209,'MAJOR GERCINO','SC','major-gercino'),(4466,4210308,'MAJOR VIEIRA','SC','major-vieira'),(4467,4210407,'MARACAJÁ','SC','maracaja'),(4468,4210506,'MARAVILHA','SC','maravilha'),(4469,4210555,'MAREMA','SC','marema'),(4470,4210605,'MASSARANDUBA','SC','massaranduba'),(4471,4210704,'MATOS COSTA','SC','matos-costa'),(4472,4210803,'MELEIRO','SC','meleiro'),(4473,4210852,'MIRIM DOCE','SC','mirim-doce'),(4474,4210902,'MODELO','SC','modelo'),(4475,4211009,'MONDAÍ','SC','mondai'),(4476,4211058,'MONTE CARLO','SC','monte-carlo'),(4477,4211108,'MONTE CASTELO','SC','monte-castelo'),(4478,4211207,'MORRO DA FUMAÇA','SC','morro-da-fumaca'),(4479,4211256,'MORRO GRANDE','SC','morro-grande'),(4480,4211306,'NAVEGANTES','SC','navegantes'),(4481,4211405,'NOVA ERECHIM','SC','nova-erechim'),(4482,4211454,'NOVA ITABERABA','SC','nova-itaberaba'),(4483,4211504,'NOVA TRENTO','SC','nova-trento'),(4484,4211603,'NOVA VENEZA','SC','nova-veneza'),(4485,4211652,'NOVO HORIZONTE','SC','novo-horizonte'),(4486,4211702,'ORLEANS','SC','orleans'),(4487,4211751,'OTACÍLIO COSTA','SC','otacilio-costa'),(4488,4211801,'OURO','SC','ouro'),(4489,4211850,'OURO VERDE','SC','ouro-verde'),(4490,4211876,'PAIAL','SC','paial'),(4491,4211892,'PAINEL','SC','painel'),(4492,4211900,'PALHOÇA','SC','palhoca'),(4493,4212007,'PALMA SOLA','SC','palma-sola'),(4494,4212056,'PALMEIRA','SC','palmeira'),(4495,4212106,'PALMITOS','SC','palmitos'),(4496,4212205,'PAPANDUVA','SC','papanduva'),(4497,4212239,'PARAÍSO','SC','paraiso'),(4498,4212254,'PASSO DE TORRES','SC','passo-de-torres'),(4499,4212270,'PASSOS MAIA','SC','passos-maia'),(4500,4212304,'PAULO LOPES','SC','paulo-lopes'),(4501,4212403,'PEDRAS GRANDES','SC','pedras-grandes'),(4502,4212502,'PENHA','SC','penha'),(4503,4212601,'PERITIBA','SC','peritiba'),(4504,4212650,'PESCARIA BRAVA','SC','pescaria-brava'),(4505,4212700,'PETROLÂNDIA','SC','petrolandia'),(4506,4212809,'BALNEÁRIO PIÇARRAS','SC','balneario-picarras'),(4507,4212908,'PINHALZINHO','SC','pinhalzinho'),(4508,4213005,'PINHEIRO PRETO','SC','pinheiro-preto'),(4509,4213104,'PIRATUBA','SC','piratuba'),(4510,4213153,'PLANALTO ALEGRE','SC','planalto-alegre'),(4511,4213203,'POMERODE','SC','pomerode'),(4512,4213302,'PONTE ALTA','SC','ponte-alta'),(4513,4213351,'PONTE ALTA DO NORTE','SC','ponte-alta-do-norte'),(4514,4213401,'PONTE SERRADA','SC','ponte-serrada'),(4515,4213500,'PORTO BELO','SC','porto-belo'),(4516,4213609,'PORTO UNIÃO','SC','porto-uniao'),(4517,4213708,'POUSO REDONDO','SC','pouso-redondo'),(4518,4213807,'PRAIA GRANDE','SC','praia-grande'),(4519,4213906,'PRESIDENTE CASTELLO BRANCO','SC','presidente-castello-branco'),(4520,4214003,'PRESIDENTE GETÚLIO','SC','presidente-getulio'),(4521,4214102,'PRESIDENTE NEREU','SC','presidente-nereu'),(4522,4214151,'PRINCESA','SC','princesa'),(4523,4214201,'QUILOMBO','SC','quilombo'),(4524,4214300,'RANCHO QUEIMADO','SC','rancho-queimado'),(4525,4214409,'RIO DAS ANTAS','SC','rio-das-antas'),(4526,4214508,'RIO DO CAMPO','SC','rio-do-campo'),(4527,4214607,'RIO DO OESTE','SC','rio-do-oeste'),(4528,4214706,'RIO DOS CEDROS','SC','rio-dos-cedros'),(4529,4214805,'RIO DO SUL','SC','rio-do-sul'),(4530,4214904,'RIO FORTUNA','SC','rio-fortuna'),(4531,4215000,'RIO NEGRINHO','SC','rio-negrinho'),(4532,4215059,'RIO RUFINO','SC','rio-rufino'),(4533,4215075,'RIQUEZA','SC','riqueza'),(4534,4215109,'RODEIO','SC','rodeio'),(4535,4215208,'ROMELÂNDIA','SC','romelandia'),(4536,4215307,'SALETE','SC','salete'),(4537,4215356,'SALTINHO','SC','saltinho'),(4538,4215406,'SALTO VELOSO','SC','salto-veloso'),(4539,4215455,'SANGÃO','SC','sangao'),(4540,4215505,'SANTA CECÍLIA','SC','santa-cecilia'),(4541,4215554,'SANTA HELENA','SC','santa-helena'),(4542,4215604,'SANTA ROSA DE LIMA','SC','santa-rosa-de-lima'),(4543,4215653,'SANTA ROSA DO SUL','SC','santa-rosa-do-sul'),(4544,4215679,'SANTA TEREZINHA','SC','santa-terezinha'),(4545,4215687,'SANTA TEREZINHA DO PROGRESSO','SC','santa-terezinha-do-progresso'),(4546,4215695,'SANTIAGO DO SUL','SC','santiago-do-sul'),(4547,4215703,'SANTO AMARO DA IMPERATRIZ','SC','santo-amaro-da-imperatriz'),(4548,4215752,'SÃO BERNARDINO','SC','sao-bernardino'),(4549,4215802,'SÃO BENTO DO SUL','SC','sao-bento-do-sul'),(4550,4215901,'SÃO BONIFÁCIO','SC','sao-bonifacio'),(4551,4216008,'SÃO CARLOS','SC','sao-carlos'),(4552,4216057,'SÃO CRISTOVÃO DO SUL','SC','sao-cristovao-do-sul'),(4553,4216107,'SÃO DOMINGOS','SC','sao-domingos'),(4554,4216206,'SÃO FRANCISCO DO SUL','SC','sao-francisco-do-sul'),(4555,4216255,'SÃO JOÃO DO OESTE','SC','sao-joao-do-oeste'),(4556,4216305,'SÃO JOÃO BATISTA','SC','sao-joao-batista'),(4557,4216354,'SÃO JOÃO DO ITAPERIÚ','SC','sao-joao-do-itaperiu'),(4558,4216404,'SÃO JOÃO DO SUL','SC','sao-joao-do-sul'),(4559,4216503,'SÃO JOAQUIM','SC','sao-joaquim'),(4560,4216602,'SÃO JOSÉ','SC','sao-jose'),(4561,4216701,'SÃO JOSÉ DO CEDRO','SC','sao-jose-do-cedro'),(4562,4216800,'SÃO JOSÉ DO CERRITO','SC','sao-jose-do-cerrito'),(4563,4216909,'SÃO LOURENÇO DO OESTE','SC','sao-lourenco-do-oeste'),(4564,4217006,'SÃO LUDGERO','SC','sao-ludgero'),(4565,4217105,'SÃO MARTINHO','SC','sao-martinho'),(4566,4217154,'SÃO MIGUEL DA BOA VISTA','SC','sao-miguel-da-boa-vista'),(4567,4217204,'SÃO MIGUEL DO OESTE','SC','sao-miguel-do-oeste'),(4568,4217253,'SÃO PEDRO DE ALCÂNTARA','SC','sao-pedro-de-alcantara'),(4569,4217303,'SAUDADES','SC','saudades'),(4570,4217402,'SCHROEDER','SC','schroeder'),(4571,4217501,'SEARA','SC','seara'),(4572,4217550,'SERRA ALTA','SC','serra-alta'),(4573,4217600,'SIDERÓPOLIS','SC','sideropolis'),(4574,4217709,'SOMBRIO','SC','sombrio'),(4575,4217758,'SUL BRASIL','SC','sul-brasil'),(4576,4217808,'TAIÓ','SC','taio'),(4577,4217907,'TANGARÁ','SC','tangara'),(4578,4217956,'TIGRINHOS','SC','tigrinhos'),(4579,4218004,'TIJUCAS','SC','tijucas'),(4580,4218103,'TIMBÉ DO SUL','SC','timbe-do-sul'),(4581,4218202,'TIMBÓ','SC','timbo'),(4582,4218251,'TIMBÓ GRANDE','SC','timbo-grande'),(4583,4218301,'TRÊS BARRAS','SC','tres-barras'),(4584,4218350,'TREVISO','SC','treviso'),(4585,4218400,'TREZE DE MAIO','SC','treze-de-maio'),(4586,4218509,'TREZE TÍLIAS','SC','treze-tilias'),(4587,4218608,'TROMBUDO CENTRAL','SC','trombudo-central'),(4588,4218707,'TUBARÃO','SC','tubarao'),(4589,4218756,'TUNÁPOLIS','SC','tunapolis'),(4590,4218806,'TURVO','SC','turvo'),(4591,4218855,'UNIÃO DO OESTE','SC','uniao-do-oeste'),(4592,4218905,'URUBICI','SC','urubici'),(4593,4218954,'URUPEMA','SC','urupema'),(4594,4219002,'URUSSANGA','SC','urussanga'),(4595,4219101,'VARGEÃO','SC','vargeao'),(4596,4219150,'VARGEM','SC','vargem'),(4597,4219176,'VARGEM BONITA','SC','vargem-bonita'),(4598,4219200,'VIDAL RAMOS','SC','vidal-ramos'),(4599,4219309,'VIDEIRA','SC','videira'),(4600,4219358,'VITOR MEIRELES','SC','vitor-meireles'),(4601,4219408,'WITMARSUM','SC','witmarsum'),(4602,4219507,'XANXERÊ','SC','xanxere'),(4603,4219606,'XAVANTINA','SC','xavantina'),(4604,4219705,'XAXIM','SC','xaxim'),(4605,4219853,'ZORTÉA','SC','zortea'),(4606,4220000,'BALNEÁRIO RINCÃO','SC','balneario-rincao'),(4607,4300034,'ACEGUÁ','RS','acegua'),(4608,4300059,'ÁGUA SANTA','RS','agua-santa'),(4609,4300109,'AGUDO','RS','agudo'),(4610,4300208,'AJURICABA','RS','ajuricaba'),(4611,4300307,'ALECRIM','RS','alecrim'),(4612,4300406,'ALEGRETE','RS','alegrete'),(4613,4300455,'ALEGRIA','RS','alegria'),(4614,4300471,'ALMIRANTE TAMANDARÉ DO SUL','RS','almirante-tamandare-do-sul'),(4615,4300505,'ALPESTRE','RS','alpestre'),(4616,4300554,'ALTO ALEGRE','RS','alto-alegre'),(4617,4300570,'ALTO FELIZ','RS','alto-feliz'),(4618,4300604,'ALVORADA','RS','alvorada'),(4619,4300638,'AMARAL FERRADOR','RS','amaral-ferrador'),(4620,4300646,'AMETISTA DO SUL','RS','ametista-do-sul'),(4621,4300661,'ANDRÉ DA ROCHA','RS','andre-da-rocha'),(4622,4300703,'ANTA GORDA','RS','anta-gorda'),(4623,4300802,'ANTÔNIO PRADO','RS','antonio-prado'),(4624,4300851,'ARAMBARÉ','RS','arambare'),(4625,4300877,'ARARICÁ','RS','ararica'),(4626,4300901,'ARATIBA','RS','aratiba'),(4627,4301008,'ARROIO DO MEIO','RS','arroio-do-meio'),(4628,4301057,'ARROIO DO SAL','RS','arroio-do-sal'),(4629,4301073,'ARROIO DO PADRE','RS','arroio-do-padre'),(4630,4301107,'ARROIO DOS RATOS','RS','arroio-dos-ratos'),(4631,4301206,'ARROIO DO TIGRE','RS','arroio-do-tigre'),(4632,4301305,'ARROIO GRANDE','RS','arroio-grande'),(4633,4301404,'ARVOREZINHA','RS','arvorezinha'),(4634,4301503,'AUGUSTO PESTANA','RS','augusto-pestana'),(4635,4301552,'ÁUREA','RS','aurea'),(4636,4301602,'BAGÉ','RS','bage'),(4637,4301636,'BALNEÁRIO PINHAL','RS','balneario-pinhal'),(4638,4301651,'BARÃO','RS','barao'),(4639,4301701,'BARÃO DE COTEGIPE','RS','barao-de-cotegipe'),(4640,4301750,'BARÃO DO TRIUNFO','RS','barao-do-triunfo'),(4641,4301800,'BARRACÃO','RS','barracao'),(4642,4301859,'BARRA DO GUARITA','RS','barra-do-guarita'),(4643,4301875,'BARRA DO QUARAÍ','RS','barra-do-quarai'),(4644,4301909,'BARRA DO RIBEIRO','RS','barra-do-ribeiro'),(4645,4301925,'BARRA DO RIO AZUL','RS','barra-do-rio-azul'),(4646,4301958,'BARRA FUNDA','RS','barra-funda'),(4647,4302006,'BARROS CASSAL','RS','barros-cassal'),(4648,4302055,'BENJAMIN CONSTANT DO SUL','RS','benjamin-constant-do-sul'),(4649,4302105,'BENTO GONÇALVES','RS','bento-goncalves'),(4650,4302154,'BOA VISTA DAS MISSÕES','RS','boa-vista-das-missoes'),(4651,4302204,'BOA VISTA DO BURICÁ','RS','boa-vista-do-burica'),(4652,4302220,'BOA VISTA DO CADEADO','RS','boa-vista-do-cadeado'),(4653,4302238,'BOA VISTA DO INCRA','RS','boa-vista-do-incra'),(4654,4302253,'BOA VISTA DO SUL','RS','boa-vista-do-sul'),(4655,4302303,'BOM JESUS','RS','bom-jesus'),(4656,4302352,'BOM PRINCÍPIO','RS','bom-principio'),(4657,4302378,'BOM PROGRESSO','RS','bom-progresso'),(4658,4302402,'BOM RETIRO DO SUL','RS','bom-retiro-do-sul'),(4659,4302451,'BOQUEIRÃO DO LEÃO','RS','boqueirao-do-leao'),(4660,4302501,'BOSSOROCA','RS','bossoroca'),(4661,4302584,'BOZANO','RS','bozano'),(4662,4302600,'BRAGA','RS','braga'),(4663,4302659,'BROCHIER','RS','brochier'),(4664,4302709,'BUTIÁ','RS','butia'),(4665,4302808,'CAÇAPAVA DO SUL','RS','cacapava-do-sul'),(4666,4302907,'CACEQUI','RS','cacequi'),(4667,4303004,'CACHOEIRA DO SUL','RS','cachoeira-do-sul'),(4668,4303103,'CACHOEIRINHA','RS','cachoeirinha'),(4669,4303202,'CACIQUE DOBLE','RS','cacique-doble'),(4670,4303301,'CAIBATÉ','RS','caibate'),(4671,4303400,'CAIÇARA','RS','caicara'),(4672,4303509,'CAMAQUÃ','RS','camaqua'),(4673,4303558,'CAMARGO','RS','camargo'),(4674,4303608,'CAMBARÁ DO SUL','RS','cambara-do-sul'),(4675,4303673,'CAMPESTRE DA SERRA','RS','campestre-da-serra'),(4676,4303707,'CAMPINA DAS MISSÕES','RS','campina-das-missoes'),(4677,4303806,'CAMPINAS DO SUL','RS','campinas-do-sul'),(4678,4303905,'CAMPO BOM','RS','campo-bom'),(4679,4304002,'CAMPO NOVO','RS','campo-novo'),(4680,4304101,'CAMPOS BORGES','RS','campos-borges'),(4681,4304200,'CANDELÁRIA','RS','candelaria'),(4682,4304309,'CÂNDIDO GODÓI','RS','candido-godoi'),(4683,4304358,'CANDIOTA','RS','candiota'),(4684,4304408,'CANELA','RS','canela'),(4685,4304507,'CANGUÇU','RS','cangucu'),(4686,4304606,'CANOAS','RS','canoas'),(4687,4304614,'CANUDOS DO VALE','RS','canudos-do-vale'),(4688,4304622,'CAPÃO BONITO DO SUL','RS','capao-bonito-do-sul'),(4689,4304630,'CAPÃO DA CANOA','RS','capao-da-canoa'),(4690,4304655,'CAPÃO DO CIPÓ','RS','capao-do-cipo'),(4691,4304663,'CAPÃO DO LEÃO','RS','capao-do-leao'),(4692,4304671,'CAPIVARI DO SUL','RS','capivari-do-sul'),(4693,4304689,'CAPELA DE SANTANA','RS','capela-de-santana'),(4694,4304697,'CAPITÃO','RS','capitao'),(4695,4304705,'CARAZINHO','RS','carazinho'),(4696,4304713,'CARAÁ','RS','caraa'),(4697,4304804,'CARLOS BARBOSA','RS','carlos-barbosa'),(4698,4304853,'CARLOS GOMES','RS','carlos-gomes'),(4699,4304903,'CASCA','RS','casca'),(4700,4304952,'CASEIROS','RS','caseiros'),(4701,4305009,'CATUÍPE','RS','catuipe'),(4702,4305108,'CAXIAS DO SUL','RS','caxias-do-sul'),(4703,4305116,'CENTENÁRIO','RS','centenario'),(4704,4305124,'CERRITO','RS','cerrito'),(4705,4305132,'CERRO BRANCO','RS','cerro-branco'),(4706,4305157,'CERRO GRANDE','RS','cerro-grande'),(4707,4305173,'CERRO GRANDE DO SUL','RS','cerro-grande-do-sul'),(4708,4305207,'CERRO LARGO','RS','cerro-largo'),(4709,4305306,'CHAPADA','RS','chapada'),(4710,4305355,'CHARQUEADAS','RS','charqueadas'),(4711,4305371,'CHARRUA','RS','charrua'),(4712,4305405,'CHIAPETTA','RS','chiapetta'),(4713,4305439,'CHUÍ','RS','chui'),(4714,4305447,'CHUVISCA','RS','chuvisca'),(4715,4305454,'CIDREIRA','RS','cidreira'),(4716,4305504,'CIRÍACO','RS','ciriaco'),(4717,4305587,'COLINAS','RS','colinas'),(4718,4305603,'COLORADO','RS','colorado'),(4719,4305702,'CONDOR','RS','condor'),(4720,4305801,'CONSTANTINA','RS','constantina'),(4721,4305835,'COQUEIRO BAIXO','RS','coqueiro-baixo'),(4722,4305850,'COQUEIROS DO SUL','RS','coqueiros-do-sul'),(4723,4305871,'CORONEL BARROS','RS','coronel-barros'),(4724,4305900,'CORONEL BICACO','RS','coronel-bicaco'),(4725,4305934,'CORONEL PILAR','RS','coronel-pilar'),(4726,4305959,'COTIPORÃ','RS','cotipora'),(4727,4305975,'COXILHA','RS','coxilha'),(4728,4306007,'CRISSIUMAL','RS','crissiumal'),(4729,4306056,'CRISTAL','RS','cristal'),(4730,4306072,'CRISTAL DO SUL','RS','cristal-do-sul'),(4731,4306106,'CRUZ ALTA','RS','cruz-alta'),(4732,4306130,'CRUZALTENSE','RS','cruzaltense'),(4733,4306205,'CRUZEIRO DO SUL','RS','cruzeiro-do-sul'),(4734,4306304,'DAVID CANABARRO','RS','david-canabarro'),(4735,4306320,'DERRUBADAS','RS','derrubadas'),(4736,4306353,'DEZESSEIS DE NOVEMBRO','RS','dezesseis-de-novembro'),(4737,4306379,'DILERMANDO DE AGUIAR','RS','dilermando-de-aguiar'),(4738,4306403,'DOIS IRMÃOS','RS','dois-irmaos'),(4739,4306429,'DOIS IRMÃOS DAS MISSÕES','RS','dois-irmaos-das-missoes'),(4740,4306452,'DOIS LAJEADOS','RS','dois-lajeados'),(4741,4306502,'DOM FELICIANO','RS','dom-feliciano'),(4742,4306551,'DOM PEDRO DE ALCÂNTARA','RS','dom-pedro-de-alcantara'),(4743,4306601,'DOM PEDRITO','RS','dom-pedrito'),(4744,4306700,'DONA FRANCISCA','RS','dona-francisca'),(4745,4306734,'DOUTOR MAURÍCIO CARDOSO','RS','doutor-mauricio-cardoso'),(4746,4306759,'DOUTOR RICARDO','RS','doutor-ricardo'),(4747,4306767,'ELDORADO DO SUL','RS','eldorado-do-sul'),(4748,4306809,'ENCANTADO','RS','encantado'),(4749,4306908,'ENCRUZILHADA DO SUL','RS','encruzilhada-do-sul'),(4750,4306924,'ENGENHO VELHO','RS','engenho-velho'),(4751,4306932,'ENTRE-IJUÍS','RS','entre-ijuis'),(4752,4306957,'ENTRE RIOS DO SUL','RS','entre-rios-do-sul'),(4753,4306973,'EREBANGO','RS','erebango'),(4754,4307005,'ERECHIM','RS','erechim'),(4755,4307054,'ERNESTINA','RS','ernestina'),(4756,4307104,'HERVAL','RS','herval'),(4757,4307203,'ERVAL GRANDE','RS','erval-grande'),(4758,4307302,'ERVAL SECO','RS','erval-seco'),(4759,4307401,'ESMERALDA','RS','esmeralda'),(4760,4307450,'ESPERANÇA DO SUL','RS','esperanca-do-sul'),(4761,4307500,'ESPUMOSO','RS','espumoso'),(4762,4307559,'ESTAÇÃO','RS','estacao'),(4763,4307609,'ESTÂNCIA VELHA','RS','estancia-velha'),(4764,4307708,'ESTEIO','RS','esteio'),(4765,4307807,'ESTRELA','RS','estrela'),(4766,4307815,'ESTRELA VELHA','RS','estrela-velha'),(4767,4307831,'EUGÊNIO DE CASTRO','RS','eugenio-de-castro'),(4768,4307864,'FAGUNDES VARELA','RS','fagundes-varela'),(4769,4307906,'FARROUPILHA','RS','farroupilha'),(4770,4308003,'FAXINAL DO SOTURNO','RS','faxinal-do-soturno'),(4771,4308052,'FAXINALZINHO','RS','faxinalzinho'),(4772,4308078,'FAZENDA VILANOVA','RS','fazenda-vilanova'),(4773,4308102,'FELIZ','RS','feliz'),(4774,4308201,'FLORES DA CUNHA','RS','flores-da-cunha'),(4775,4308250,'FLORIANO PEIXOTO','RS','floriano-peixoto'),(4776,4308300,'FONTOURA XAVIER','RS','fontoura-xavier'),(4777,4308409,'FORMIGUEIRO','RS','formigueiro'),(4778,4308433,'FORQUETINHA','RS','forquetinha'),(4779,4308458,'FORTALEZA DOS VALOS','RS','fortaleza-dos-valos'),(4780,4308508,'FREDERICO WESTPHALEN','RS','frederico-westphalen'),(4781,4308607,'GARIBALDI','RS','garibaldi'),(4782,4308656,'GARRUCHOS','RS','garruchos'),(4783,4308706,'GAURAMA','RS','gaurama'),(4784,4308805,'GENERAL CÂMARA','RS','general-camara'),(4785,4308854,'GENTIL','RS','gentil'),(4786,4308904,'GETÚLIO VARGAS','RS','getulio-vargas'),(4787,4309001,'GIRUÁ','RS','girua'),(4788,4309050,'GLORINHA','RS','glorinha'),(4789,4309100,'GRAMADO','RS','gramado'),(4790,4309126,'GRAMADO DOS LOUREIROS','RS','gramado-dos-loureiros'),(4791,4309159,'GRAMADO XAVIER','RS','gramado-xavier'),(4792,4309209,'GRAVATAÍ','RS','gravatai'),(4793,4309258,'GUABIJU','RS','guabiju'),(4794,4309308,'GUAÍBA','RS','guaiba'),(4795,4309407,'GUAPORÉ','RS','guapore'),(4796,4309506,'GUARANI DAS MISSÕES','RS','guarani-das-missoes'),(4797,4309555,'HARMONIA','RS','harmonia'),(4798,4309571,'HERVEIRAS','RS','herveiras'),(4799,4309605,'HORIZONTINA','RS','horizontina'),(4800,4309654,'HULHA NEGRA','RS','hulha-negra'),(4801,4309704,'HUMAITÁ','RS','humaita'),(4802,4309753,'IBARAMA','RS','ibarama'),(4803,4309803,'IBIAÇÁ','RS','ibiaca'),(4804,4309902,'IBIRAIARAS','RS','ibiraiaras'),(4805,4309951,'IBIRAPUITÃ','RS','ibirapuita'),(4806,4310009,'IBIRUBÁ','RS','ibiruba'),(4807,4310108,'IGREJINHA','RS','igrejinha'),(4808,4310207,'IJUÍ','RS','ijui'),(4809,4310306,'ILÓPOLIS','RS','ilopolis'),(4810,4310330,'IMBÉ','RS','imbe'),(4811,4310363,'IMIGRANTE','RS','imigrante'),(4812,4310405,'INDEPENDÊNCIA','RS','independencia'),(4813,4310413,'INHACORÁ','RS','inhacora'),(4814,4310439,'IPÊ','RS','ipe'),(4815,4310462,'IPIRANGA DO SUL','RS','ipiranga-do-sul'),(4816,4310504,'IRAÍ','RS','irai'),(4817,4310538,'ITAARA','RS','itaara'),(4818,4310553,'ITACURUBI','RS','itacurubi'),(4819,4310579,'ITAPUCA','RS','itapuca'),(4820,4310603,'ITAQUI','RS','itaqui'),(4821,4310652,'ITATI','RS','itati'),(4822,4310702,'ITATIBA DO SUL','RS','itatiba-do-sul'),(4823,4310751,'IVORÁ','RS','ivora'),(4824,4310801,'IVOTI','RS','ivoti'),(4825,4310850,'JABOTICABA','RS','jaboticaba'),(4826,4310876,'JACUIZINHO','RS','jacuizinho'),(4827,4310900,'JACUTINGA','RS','jacutinga'),(4828,4311007,'JAGUARÃO','RS','jaguarao'),(4829,4311106,'JAGUARI','RS','jaguari'),(4830,4311122,'JAQUIRANA','RS','jaquirana'),(4831,4311130,'JARI','RS','jari'),(4832,4311155,'JÓIA','RS','joia'),(4833,4311205,'JÚLIO DE CASTILHOS','RS','julio-de-castilhos'),(4834,4311239,'LAGOA BONITA DO SUL','RS','lagoa-bonita-do-sul'),(4835,4311254,'LAGOÃO','RS','lagoao'),(4836,4311270,'LAGOA DOS TRÊS CANTOS','RS','lagoa-dos-tres-cantos'),(4837,4311304,'LAGOA VERMELHA','RS','lagoa-vermelha'),(4838,4311403,'LAJEADO','RS','lajeado'),(4839,4311429,'LAJEADO DO BUGRE','RS','lajeado-do-bugre'),(4840,4311502,'LAVRAS DO SUL','RS','lavras-do-sul'),(4841,4311601,'LIBERATO SALZANO','RS','liberato-salzano'),(4842,4311627,'LINDOLFO COLLOR','RS','lindolfo-collor'),(4843,4311643,'LINHA NOVA','RS','linha-nova'),(4844,4311700,'MACHADINHO','RS','machadinho'),(4845,4311718,'MAÇAMBARÁ','RS','macambara'),(4846,4311734,'MAMPITUBA','RS','mampituba'),(4847,4311759,'MANOEL VIANA','RS','manoel-viana'),(4848,4311775,'MAQUINÉ','RS','maquine'),(4849,4311791,'MARATÁ','RS','marata'),(4850,4311809,'MARAU','RS','marau'),(4851,4311908,'MARCELINO RAMOS','RS','marcelino-ramos'),(4852,4311981,'MARIANA PIMENTEL','RS','mariana-pimentel'),(4853,4312005,'MARIANO MORO','RS','mariano-moro'),(4854,4312054,'MARQUES DE SOUZA','RS','marques-de-souza'),(4855,4312104,'MATA','RS','mata'),(4856,4312138,'MATO CASTELHANO','RS','mato-castelhano'),(4857,4312153,'MATO LEITÃO','RS','mato-leitao'),(4858,4312179,'MATO QUEIMADO','RS','mato-queimado'),(4859,4312203,'MAXIMILIANO DE ALMEIDA','RS','maximiliano-de-almeida'),(4860,4312252,'MINAS DO LEÃO','RS','minas-do-leao'),(4861,4312302,'MIRAGUAÍ','RS','miraguai'),(4862,4312351,'MONTAURI','RS','montauri'),(4863,4312377,'MONTE ALEGRE DOS CAMPOS','RS','monte-alegre-dos-campos'),(4864,4312385,'MONTE BELO DO SUL','RS','monte-belo-do-sul'),(4865,4312401,'MONTENEGRO','RS','montenegro'),(4866,4312427,'MORMAÇO','RS','mormaco'),(4867,4312443,'MORRINHOS DO SUL','RS','morrinhos-do-sul'),(4868,4312450,'MORRO REDONDO','RS','morro-redondo'),(4869,4312476,'MORRO REUTER','RS','morro-reuter'),(4870,4312500,'MOSTARDAS','RS','mostardas'),(4871,4312609,'MUÇUM','RS','mucum'),(4872,4312617,'MUITOS CAPÕES','RS','muitos-capoes'),(4873,4312625,'MULITERNO','RS','muliterno'),(4874,4312658,'NÃO-ME-TOQUE','RS','nao-me-toque'),(4875,4312674,'NICOLAU VERGUEIRO','RS','nicolau-vergueiro'),(4876,4312708,'NONOAI','RS','nonoai'),(4877,4312757,'NOVA ALVORADA','RS','nova-alvorada'),(4878,4312807,'NOVA ARAÇÁ','RS','nova-araca'),(4879,4312906,'NOVA BASSANO','RS','nova-bassano'),(4880,4312955,'NOVA BOA VISTA','RS','nova-boa-vista'),(4881,4313003,'NOVA BRÉSCIA','RS','nova-brescia'),(4882,4313011,'NOVA CANDELÁRIA','RS','nova-candelaria'),(4883,4313037,'NOVA ESPERANÇA DO SUL','RS','nova-esperanca-do-sul'),(4884,4313060,'NOVA HARTZ','RS','nova-hartz'),(4885,4313086,'NOVA PÁDUA','RS','nova-padua'),(4886,4313102,'NOVA PALMA','RS','nova-palma'),(4887,4313201,'NOVA PETRÓPOLIS','RS','nova-petropolis'),(4888,4313300,'NOVA PRATA','RS','nova-prata'),(4889,4313334,'NOVA RAMADA','RS','nova-ramada'),(4890,4313359,'NOVA ROMA DO SUL','RS','nova-roma-do-sul'),(4891,4313375,'NOVA SANTA RITA','RS','nova-santa-rita'),(4892,4313391,'NOVO CABRAIS','RS','novo-cabrais'),(4893,4313409,'NOVO HAMBURGO','RS','novo-hamburgo'),(4894,4313425,'NOVO MACHADO','RS','novo-machado'),(4895,4313441,'NOVO TIRADENTES','RS','novo-tiradentes'),(4896,4313466,'NOVO XINGU','RS','novo-xingu'),(4897,4313490,'NOVO BARREIRO','RS','novo-barreiro'),(4898,4313508,'OSÓRIO','RS','osorio'),(4899,4313607,'PAIM FILHO','RS','paim-filho'),(4900,4313656,'PALMARES DO SUL','RS','palmares-do-sul'),(4901,4313706,'PALMEIRA DAS MISSÕES','RS','palmeira-das-missoes'),(4902,4313805,'PALMITINHO','RS','palmitinho'),(4903,4313904,'PANAMBI','RS','panambi'),(4904,4313953,'PANTANO GRANDE','RS','pantano-grande'),(4905,4314001,'PARAÍ','RS','parai'),(4906,4314027,'PARAÍSO DO SUL','RS','paraiso-do-sul'),(4907,4314035,'PARECI NOVO','RS','pareci-novo'),(4908,4314050,'PAROBÉ','RS','parobe'),(4909,4314068,'PASSA SETE','RS','passa-sete'),(4910,4314076,'PASSO DO SOBRADO','RS','passo-do-sobrado'),(4911,4314100,'PASSO FUNDO','RS','passo-fundo'),(4912,4314134,'PAULO BENTO','RS','paulo-bento'),(4913,4314159,'PAVERAMA','RS','paverama'),(4914,4314175,'PEDRAS ALTAS','RS','pedras-altas'),(4915,4314209,'PEDRO OSÓRIO','RS','pedro-osorio'),(4916,4314308,'PEJUÇARA','RS','pejucara'),(4917,4314407,'PELOTAS','RS','pelotas'),(4918,4314423,'PICADA CAFÉ','RS','picada-cafe'),(4919,4314456,'PINHAL','RS','pinhal'),(4920,4314464,'PINHAL DA SERRA','RS','pinhal-da-serra'),(4921,4314472,'PINHAL GRANDE','RS','pinhal-grande'),(4922,4314498,'PINHEIRINHO DO VALE','RS','pinheirinho-do-vale'),(4923,4314506,'PINHEIRO MACHADO','RS','pinheiro-machado'),(4924,4314548,'PINTO BANDEIRA','RS','pinto-bandeira'),(4925,4314555,'PIRAPÓ','RS','pirapo'),(4926,4314605,'PIRATINI','RS','piratini'),(4927,4314704,'PLANALTO','RS','planalto'),(4928,4314753,'POÇO DAS ANTAS','RS','poco-das-antas'),(4929,4314779,'PONTÃO','RS','pontao'),(4930,4314787,'PONTE PRETA','RS','ponte-preta'),(4931,4314803,'PORTÃO','RS','portao'),(4932,4314902,'PORTO ALEGRE','RS','porto-alegre'),(4933,4315008,'PORTO LUCENA','RS','porto-lucena'),(4934,4315057,'PORTO MAUÁ','RS','porto-maua'),(4935,4315073,'PORTO VERA CRUZ','RS','porto-vera-cruz'),(4936,4315107,'PORTO XAVIER','RS','porto-xavier'),(4937,4315131,'POUSO NOVO','RS','pouso-novo'),(4938,4315149,'PRESIDENTE LUCENA','RS','presidente-lucena'),(4939,4315156,'PROGRESSO','RS','progresso'),(4940,4315172,'PROTÁSIO ALVES','RS','protasio-alves'),(4941,4315206,'PUTINGA','RS','putinga'),(4942,4315305,'QUARAÍ','RS','quarai'),(4943,4315313,'QUATRO IRMÃOS','RS','quatro-irmaos'),(4944,4315321,'QUEVEDOS','RS','quevedos'),(4945,4315354,'QUINZE DE NOVEMBRO','RS','quinze-de-novembro'),(4946,4315404,'REDENTORA','RS','redentora'),(4947,4315453,'RELVADO','RS','relvado'),(4948,4315503,'RESTINGA SECA','RS','restinga-seca'),(4949,4315552,'RIO DOS ÍNDIOS','RS','rio-dos-indios'),(4950,4315602,'RIO GRANDE','RS','rio-grande'),(4951,4315701,'RIO PARDO','RS','rio-pardo'),(4952,4315750,'RIOZINHO','RS','riozinho'),(4953,4315800,'ROCA SALES','RS','roca-sales'),(4954,4315909,'RODEIO BONITO','RS','rodeio-bonito'),(4955,4315958,'ROLADOR','RS','rolador'),(4956,4316006,'ROLANTE','RS','rolante'),(4957,4316105,'RONDA ALTA','RS','ronda-alta'),(4958,4316204,'RONDINHA','RS','rondinha'),(4959,4316303,'ROQUE GONZALES','RS','roque-gonzales'),(4960,4316402,'ROSÁRIO DO SUL','RS','rosario-do-sul'),(4961,4316428,'SAGRADA FAMÍLIA','RS','sagrada-familia'),(4962,4316436,'SALDANHA MARINHO','RS','saldanha-marinho'),(4963,4316451,'SALTO DO JACUÍ','RS','salto-do-jacui'),(4964,4316477,'SALVADOR DAS MISSÕES','RS','salvador-das-missoes'),(4965,4316501,'SALVADOR DO SUL','RS','salvador-do-sul'),(4966,4316600,'SANANDUVA','RS','sananduva'),(4967,4316709,'SANTA BÁRBARA DO SUL','RS','santa-barbara-do-sul'),(4968,4316733,'SANTA CECÍLIA DO SUL','RS','santa-cecilia-do-sul'),(4969,4316758,'SANTA CLARA DO SUL','RS','santa-clara-do-sul'),(4970,4316808,'SANTA CRUZ DO SUL','RS','santa-cruz-do-sul'),(4971,4316907,'SANTA MARIA','RS','santa-maria'),(4972,4316956,'SANTA MARIA DO HERVAL','RS','santa-maria-do-herval'),(4973,4316972,'SANTA MARGARIDA DO SUL','RS','santa-margarida-do-sul'),(4974,4317004,'SANTANA DA BOA VISTA','RS','santana-da-boa-vista'),(4975,4317103,'SANT\'ANA DO LIVRAMENTO','RS','santana-do-livramento'),(4976,4317202,'SANTA ROSA','RS','santa-rosa'),(4977,4317251,'SANTA TEREZA','RS','santa-tereza'),(4978,4317301,'SANTA VITÓRIA DO PALMAR','RS','santa-vitoria-do-palmar'),(4979,4317400,'SANTIAGO','RS','santiago'),(4980,4317509,'SANTO ÂNGELO','RS','santo-angelo'),(4981,4317558,'SANTO ANTÔNIO DO PALMA','RS','santo-antonio-do-palma'),(4982,4317608,'SANTO ANTÔNIO DA PATRULHA','RS','santo-antonio-da-patrulha'),(4983,4317707,'SANTO ANTÔNIO DAS MISSÕES','RS','santo-antonio-das-missoes'),(4984,4317756,'SANTO ANTÔNIO DO PLANALTO','RS','santo-antonio-do-planalto'),(4985,4317806,'SANTO AUGUSTO','RS','santo-augusto'),(4986,4317905,'SANTO CRISTO','RS','santo-cristo'),(4987,4317954,'SANTO EXPEDITO DO SUL','RS','santo-expedito-do-sul'),(4988,4318002,'SÃO BORJA','RS','sao-borja'),(4989,4318051,'SÃO DOMINGOS DO SUL','RS','sao-domingos-do-sul'),(4990,4318101,'SÃO FRANCISCO DE ASSIS','RS','sao-francisco-de-assis'),(4991,4318200,'SÃO FRANCISCO DE PAULA','RS','sao-francisco-de-paula'),(4992,4318309,'SÃO GABRIEL','RS','sao-gabriel'),(4993,4318408,'SÃO JERÔNIMO','RS','sao-jeronimo'),(4994,4318424,'SÃO JOÃO DA URTIGA','RS','sao-joao-da-urtiga'),(4995,4318432,'SÃO JOÃO DO POLÊSINE','RS','sao-joao-do-polesine'),(4996,4318440,'SÃO JORGE','RS','sao-jorge'),(4997,4318457,'SÃO JOSÉ DAS MISSÕES','RS','sao-jose-das-missoes'),(4998,4318465,'SÃO JOSÉ DO HERVAL','RS','sao-jose-do-herval'),(4999,4318481,'SÃO JOSÉ DO HORTÊNCIO','RS','sao-jose-do-hortencio'),(5000,4318499,'SÃO JOSÉ DO INHACORÁ','RS','sao-jose-do-inhacora'),(5001,4318507,'SÃO JOSÉ DO NORTE','RS','sao-jose-do-norte'),(5002,4318606,'SÃO JOSÉ DO OURO','RS','sao-jose-do-ouro'),(5003,4318614,'SÃO JOSÉ DO SUL','RS','sao-jose-do-sul'),(5004,4318622,'SÃO JOSÉ DOS AUSENTES','RS','sao-jose-dos-ausentes'),(5005,4318705,'SÃO LEOPOLDO','RS','sao-leopoldo'),(5006,4318804,'SÃO LOURENÇO DO SUL','RS','sao-lourenco-do-sul'),(5007,4318903,'SÃO LUIZ GONZAGA','RS','sao-luiz-gonzaga'),(5008,4319000,'SÃO MARCOS','RS','sao-marcos'),(5009,4319109,'SÃO MARTINHO','RS','sao-martinho'),(5010,4319125,'SÃO MARTINHO DA SERRA','RS','sao-martinho-da-serra'),(5011,4319158,'SÃO MIGUEL DAS MISSÕES','RS','sao-miguel-das-missoes'),(5012,4319208,'SÃO NICOLAU','RS','sao-nicolau'),(5013,4319307,'SÃO PAULO DAS MISSÕES','RS','sao-paulo-das-missoes'),(5014,4319356,'SÃO PEDRO DA SERRA','RS','sao-pedro-da-serra'),(5015,4319364,'SÃO PEDRO DAS MISSÕES','RS','sao-pedro-das-missoes'),(5016,4319372,'SÃO PEDRO DO BUTIÁ','RS','sao-pedro-do-butia'),(5017,4319406,'SÃO PEDRO DO SUL','RS','sao-pedro-do-sul'),(5018,4319505,'SÃO SEBASTIÃO DO CAÍ','RS','sao-sebastiao-do-cai'),(5019,4319604,'SÃO SEPÉ','RS','sao-sepe'),(5020,4319703,'SÃO VALENTIM','RS','sao-valentim'),(5021,4319711,'SÃO VALENTIM DO SUL','RS','sao-valentim-do-sul'),(5022,4319737,'SÃO VALÉRIO DO SUL','RS','sao-valerio-do-sul'),(5023,4319752,'SÃO VENDELINO','RS','sao-vendelino'),(5024,4319802,'SÃO VICENTE DO SUL','RS','sao-vicente-do-sul'),(5025,4319901,'SAPIRANGA','RS','sapiranga'),(5026,4320008,'SAPUCAIA DO SUL','RS','sapucaia-do-sul'),(5027,4320107,'SARANDI','RS','sarandi'),(5028,4320206,'SEBERI','RS','seberi'),(5029,4320230,'SEDE NOVA','RS','sede-nova'),(5030,4320263,'SEGREDO','RS','segredo'),(5031,4320305,'SELBACH','RS','selbach'),(5032,4320321,'SENADOR SALGADO FILHO','RS','senador-salgado-filho'),(5033,4320354,'SENTINELA DO SUL','RS','sentinela-do-sul'),(5034,4320404,'SERAFINA CORRÊA','RS','serafina-correa'),(5035,4320453,'SÉRIO','RS','serio'),(5036,4320503,'SERTÃO','RS','sertao'),(5037,4320552,'SERTÃO SANTANA','RS','sertao-santana'),(5038,4320578,'SETE DE SETEMBRO','RS','sete-de-setembro'),(5039,4320602,'SEVERIANO DE ALMEIDA','RS','severiano-de-almeida'),(5040,4320651,'SILVEIRA MARTINS','RS','silveira-martins'),(5041,4320677,'SINIMBU','RS','sinimbu'),(5042,4320701,'SOBRADINHO','RS','sobradinho'),(5043,4320800,'SOLEDADE','RS','soledade'),(5044,4320859,'TABAÍ','RS','tabai'),(5045,4320909,'TAPEJARA','RS','tapejara'),(5046,4321006,'TAPERA','RS','tapera'),(5047,4321105,'TAPES','RS','tapes'),(5048,4321204,'TAQUARA','RS','taquara'),(5049,4321303,'TAQUARI','RS','taquari'),(5050,4321329,'TAQUARUÇU DO SUL','RS','taquarucu-do-sul'),(5051,4321352,'TAVARES','RS','tavares'),(5052,4321402,'TENENTE PORTELA','RS','tenente-portela'),(5053,4321436,'TERRA DE AREIA','RS','terra-de-areia'),(5054,4321451,'TEUTÔNIA','RS','teutonia'),(5055,4321469,'TIO HUGO','RS','tio-hugo'),(5056,4321477,'TIRADENTES DO SUL','RS','tiradentes-do-sul'),(5057,4321493,'TOROPI','RS','toropi'),(5058,4321501,'TORRES','RS','torres'),(5059,4321600,'TRAMANDAÍ','RS','tramandai'),(5060,4321626,'TRAVESSEIRO','RS','travesseiro'),(5061,4321634,'TRÊS ARROIOS','RS','tres-arroios'),(5062,4321667,'TRÊS CACHOEIRAS','RS','tres-cachoeiras'),(5063,4321709,'TRÊS COROAS','RS','tres-coroas'),(5064,4321808,'TRÊS DE MAIO','RS','tres-de-maio'),(5065,4321832,'TRÊS FORQUILHAS','RS','tres-forquilhas'),(5066,4321857,'TRÊS PALMEIRAS','RS','tres-palmeiras'),(5067,4321907,'TRÊS PASSOS','RS','tres-passos'),(5068,4321956,'TRINDADE DO SUL','RS','trindade-do-sul'),(5069,4322004,'TRIUNFO','RS','triunfo'),(5070,4322103,'TUCUNDUVA','RS','tucunduva'),(5071,4322152,'TUNAS','RS','tunas'),(5072,4322186,'TUPANCI DO SUL','RS','tupanci-do-sul'),(5073,4322202,'TUPANCIRETÃ','RS','tupancireta'),(5074,4322251,'TUPANDI','RS','tupandi'),(5075,4322301,'TUPARENDI','RS','tuparendi'),(5076,4322327,'TURUÇU','RS','turucu'),(5077,4322343,'UBIRETAMA','RS','ubiretama'),(5078,4322350,'UNIÃO DA SERRA','RS','uniao-da-serra'),(5079,4322376,'UNISTALDA','RS','unistalda'),(5080,4322400,'URUGUAIANA','RS','uruguaiana'),(5081,4322509,'VACARIA','RS','vacaria'),(5082,4322525,'VALE VERDE','RS','vale-verde'),(5083,4322533,'VALE DO SOL','RS','vale-do-sol'),(5084,4322541,'VALE REAL','RS','vale-real'),(5085,4322558,'VANINI','RS','vanini'),(5086,4322608,'VENÂNCIO AIRES','RS','venancio-aires'),(5087,4322707,'VERA CRUZ','RS','vera-cruz'),(5088,4322806,'VERANÓPOLIS','RS','veranopolis'),(5089,4322855,'VESPASIANO CORREA','RS','vespasiano-correa'),(5090,4322905,'VIADUTOS','RS','viadutos'),(5091,4323002,'VIAMÃO','RS','viamao'),(5092,4323101,'VICENTE DUTRA','RS','vicente-dutra'),(5093,4323200,'VICTOR GRAEFF','RS','victor-graeff'),(5094,4323309,'VILA FLORES','RS','vila-flores'),(5095,4323358,'VILA LÂNGARO','RS','vila-langaro'),(5096,4323408,'VILA MARIA','RS','vila-maria'),(5097,4323457,'VILA NOVA DO SUL','RS','vila-nova-do-sul'),(5098,4323507,'VISTA ALEGRE','RS','vista-alegre'),(5099,4323606,'VISTA ALEGRE DO PRATA','RS','vista-alegre-do-prata'),(5100,4323705,'VISTA GAÚCHA','RS','vista-gaucha'),(5101,4323754,'VITÓRIA DAS MISSÕES','RS','vitoria-das-missoes'),(5102,4323770,'WESTFALIA','RS','westfalia'),(5103,4323804,'XANGRI-LÁ','RS','xangri-la'),(5104,5000203,'ÁGUA CLARA','MS','agua-clara'),(5105,5000252,'ALCINÓPOLIS','MS','alcinopolis'),(5106,5000609,'AMAMBAI','MS','amambai'),(5107,5000708,'ANASTÁCIO','MS','anastacio'),(5108,5000807,'ANAURILÂNDIA','MS','anaurilandia'),(5109,5000856,'ANGÉLICA','MS','angelica'),(5110,5000906,'ANTÔNIO JOÃO','MS','antonio-joao'),(5111,5001003,'APARECIDA DO TABOADO','MS','aparecida-do-taboado'),(5112,5001102,'AQUIDAUANA','MS','aquidauana'),(5113,5001243,'ARAL MOREIRA','MS','aral-moreira'),(5114,5001508,'BANDEIRANTES','MS','bandeirantes'),(5115,5001904,'BATAGUASSU','MS','bataguassu'),(5116,5002001,'BATAYPORÃ','MS','bataypora'),(5117,5002100,'BELA VISTA','MS','bela-vista'),(5118,5002159,'BODOQUENA','MS','bodoquena'),(5119,5002209,'BONITO','MS','bonito'),(5120,5002308,'BRASILÂNDIA','MS','brasilandia'),(5121,5002407,'CAARAPÓ','MS','caarapo'),(5122,5002605,'CAMAPUÃ','MS','camapua'),(5123,5002704,'CAMPO GRANDE','MS','campo-grande'),(5124,5002803,'CARACOL','MS','caracol'),(5125,5002902,'CASSILÂNDIA','MS','cassilandia'),(5126,5002951,'CHAPADÃO DO SUL','MS','chapadao-do-sul'),(5127,5003108,'CORGUINHO','MS','corguinho'),(5128,5003157,'CORONEL SAPUCAIA','MS','coronel-sapucaia'),(5129,5003207,'CORUMBÁ','MS','corumba'),(5130,5003256,'COSTA RICA','MS','costa-rica'),(5131,5003306,'COXIM','MS','coxim'),(5132,5003454,'DEODÁPOLIS','MS','deodapolis'),(5133,5003488,'DOIS IRMÃOS DO BURITI','MS','dois-irmaos-do-buriti'),(5134,5003504,'DOURADINA','MS','douradina'),(5135,5003702,'DOURADOS','MS','dourados'),(5136,5003751,'ELDORADO','MS','eldorado'),(5137,5003801,'FÁTIMA DO SUL','MS','fatima-do-sul'),(5138,5003900,'FIGUEIRÃO','MS','figueirao'),(5139,5004007,'GLÓRIA DE DOURADOS','MS','gloria-de-dourados'),(5140,5004106,'GUIA LOPES DA LAGUNA','MS','guia-lopes-da-laguna'),(5141,5004304,'IGUATEMI','MS','iguatemi'),(5142,5004403,'INOCÊNCIA','MS','inocencia'),(5143,5004502,'ITAPORÃ','MS','itapora'),(5144,5004601,'ITAQUIRAÍ','MS','itaquirai'),(5145,5004700,'IVINHEMA','MS','ivinhema'),(5146,5004809,'JAPORÃ','MS','japora'),(5147,5004908,'JARAGUARI','MS','jaraguari'),(5148,5005004,'JARDIM','MS','jardim'),(5149,5005103,'JATEÍ','MS','jatei'),(5150,5005152,'JUTI','MS','juti'),(5151,5005202,'LADÁRIO','MS','ladario'),(5152,5005251,'LAGUNA CARAPÃ','MS','laguna-carapa'),(5153,5005400,'MARACAJU','MS','maracaju'),(5154,5005608,'MIRANDA','MS','miranda'),(5155,5005681,'MUNDO NOVO','MS','mundo-novo'),(5156,5005707,'NAVIRAÍ','MS','navirai'),(5157,5005806,'NIOAQUE','MS','nioaque'),(5158,5006002,'NOVA ALVORADA DO SUL','MS','nova-alvorada-do-sul'),(5159,5006200,'NOVA ANDRADINA','MS','nova-andradina'),(5160,5006259,'NOVO HORIZONTE DO SUL','MS','novo-horizonte-do-sul'),(5161,5006275,'PARAÍSO DAS ÁGUAS','MS','paraiso-das-aguas'),(5162,5006309,'PARANAÍBA','MS','paranaiba'),(5163,5006358,'PARANHOS','MS','paranhos'),(5164,5006408,'PEDRO GOMES','MS','pedro-gomes'),(5165,5006606,'PONTA PORÃ','MS','ponta-pora'),(5166,5006903,'PORTO MURTINHO','MS','porto-murtinho'),(5167,5007109,'RIBAS DO RIO PARDO','MS','ribas-do-rio-pardo'),(5168,5007208,'RIO BRILHANTE','MS','rio-brilhante'),(5169,5007307,'RIO NEGRO','MS','rio-negro'),(5170,5007406,'RIO VERDE DE MATO GROSSO','MS','rio-verde-de-mato-grosso'),(5171,5007505,'ROCHEDO','MS','rochedo'),(5172,5007554,'SANTA RITA DO PARDO','MS','santa-rita-do-pardo'),(5173,5007695,'SÃO GABRIEL DO OESTE','MS','sao-gabriel-do-oeste'),(5174,5007703,'SETE QUEDAS','MS','sete-quedas'),(5175,5007802,'SELVÍRIA','MS','selviria'),(5176,5007901,'SIDROLÂNDIA','MS','sidrolandia'),(5177,5007935,'SONORA','MS','sonora'),(5178,5007950,'TACURU','MS','tacuru'),(5179,5007976,'TAQUARUSSU','MS','taquarussu'),(5180,5008008,'TERENOS','MS','terenos'),(5181,5008305,'TRÊS LAGOAS','MS','tres-lagoas'),(5182,5008404,'VICENTINA','MS','vicentina'),(5183,5100102,'ACORIZAL','MT','acorizal'),(5184,5100201,'ÁGUA BOA','MT','agua-boa'),(5185,5100250,'ALTA FLORESTA','MT','alta-floresta'),(5186,5100300,'ALTO ARAGUAIA','MT','alto-araguaia'),(5187,5100359,'ALTO BOA VISTA','MT','alto-boa-vista'),(5188,5100409,'ALTO GARÇAS','MT','alto-garcas'),(5189,5100508,'ALTO PARAGUAI','MT','alto-paraguai'),(5190,5100607,'ALTO TAQUARI','MT','alto-taquari'),(5191,5100805,'APIACÁS','MT','apiacas'),(5192,5101001,'ARAGUAIANA','MT','araguaiana'),(5193,5101209,'ARAGUAINHA','MT','araguainha'),(5194,5101258,'ARAPUTANGA','MT','araputanga'),(5195,5101308,'ARENÁPOLIS','MT','arenapolis'),(5196,5101407,'ARIPUANÃ','MT','aripuana'),(5197,5101605,'BARÃO DE MELGAÇO','MT','barao-de-melgaco'),(5198,5101704,'BARRA DO BUGRES','MT','barra-do-bugres'),(5199,5101803,'BARRA DO GARÇAS','MT','barra-do-garcas'),(5200,5101852,'BOM JESUS DO ARAGUAIA','MT','bom-jesus-do-araguaia'),(5201,5101902,'BRASNORTE','MT','brasnorte'),(5202,5102504,'CÁCERES','MT','caceres'),(5203,5102603,'CAMPINÁPOLIS','MT','campinapolis'),(5204,5102637,'CAMPO NOVO DO PARECIS','MT','campo-novo-do-parecis'),(5205,5102678,'CAMPO VERDE','MT','campo-verde'),(5206,5102686,'CAMPOS DE JÚLIO','MT','campos-de-julio'),(5207,5102694,'CANABRAVA DO NORTE','MT','canabrava-do-norte'),(5208,5102702,'CANARANA','MT','canarana'),(5209,5102793,'CARLINDA','MT','carlinda'),(5210,5102850,'CASTANHEIRA','MT','castanheira'),(5211,5103007,'CHAPADA DOS GUIMARÃES','MT','chapada-dos-guimaraes'),(5212,5103056,'CLÁUDIA','MT','claudia'),(5213,5103106,'COCALINHO','MT','cocalinho'),(5214,5103205,'COLÍDER','MT','colider'),(5215,5103254,'COLNIZA','MT','colniza'),(5216,5103304,'COMODORO','MT','comodoro'),(5217,5103353,'CONFRESA','MT','confresa'),(5218,5103361,'CONQUISTA D\'OESTE','MT','conquista-doeste'),(5219,5103379,'COTRIGUAÇU','MT','cotriguacu'),(5220,5103403,'CUIABÁ','MT','cuiaba'),(5221,5103437,'CURVELÂNDIA','MT','curvelandia'),(5222,5103452,'DENISE','MT','denise'),(5223,5103502,'DIAMANTINO','MT','diamantino'),(5224,5103601,'DOM AQUINO','MT','dom-aquino'),(5225,5103700,'FELIZ NATAL','MT','feliz-natal'),(5226,5103809,'FIGUEIRÓPOLIS D\'OESTE','MT','figueiropolis-doeste'),(5227,5103858,'GAÚCHA DO NORTE','MT','gaucha-do-norte'),(5228,5103908,'GENERAL CARNEIRO','MT','general-carneiro'),(5229,5103957,'GLÓRIA D\'OESTE','MT','gloria-doeste'),(5230,5104104,'GUARANTÃ DO NORTE','MT','guaranta-do-norte'),(5231,5104203,'GUIRATINGA','MT','guiratinga'),(5232,5104500,'INDIAVAÍ','MT','indiavai'),(5233,5104526,'IPIRANGA DO NORTE','MT','ipiranga-do-norte'),(5234,5104542,'ITANHANGÁ','MT','itanhanga'),(5235,5104559,'ITAÚBA','MT','itauba'),(5236,5104609,'ITIQUIRA','MT','itiquira'),(5237,5104807,'JACIARA','MT','jaciara'),(5238,5104906,'JANGADA','MT','jangada'),(5239,5105002,'JAURU','MT','jauru'),(5240,5105101,'JUARA','MT','juara'),(5241,5105150,'JUÍNA','MT','juina'),(5242,5105176,'JURUENA','MT','juruena'),(5243,5105200,'JUSCIMEIRA','MT','juscimeira'),(5244,5105234,'LAMBARI D\'OESTE','MT','lambari-doeste'),(5245,5105259,'LUCAS DO RIO VERDE','MT','lucas-do-rio-verde'),(5246,5105309,'LUCIARA','MT','luciara'),(5247,5105507,'VILA BELA DA SANTÍSSIMA TRINDADE','MT','vila-bela-da-santissima-trindade'),(5248,5105580,'MARCELÂNDIA','MT','marcelandia'),(5249,5105606,'MATUPÁ','MT','matupa'),(5250,5105622,'MIRASSOL D\'OESTE','MT','mirassol-doeste'),(5251,5105903,'NOBRES','MT','nobres'),(5252,5106000,'NORTELÂNDIA','MT','nortelandia'),(5253,5106109,'NOSSA SENHORA DO LIVRAMENTO','MT','nossa-senhora-do-livramento'),(5254,5106158,'NOVA BANDEIRANTES','MT','nova-bandeirantes'),(5255,5106174,'NOVA NAZARÉ','MT','nova-nazare'),(5256,5106182,'NOVA LACERDA','MT','nova-lacerda'),(5257,5106190,'NOVA SANTA HELENA','MT','nova-santa-helena'),(5258,5106208,'NOVA BRASILÂNDIA','MT','nova-brasilandia'),(5259,5106216,'NOVA CANAÃ DO NORTE','MT','nova-canaa-do-norte'),(5260,5106224,'NOVA MUTUM','MT','nova-mutum'),(5261,5106232,'NOVA OLÍMPIA','MT','nova-olimpia'),(5262,5106240,'NOVA UBIRATÃ','MT','nova-ubirata'),(5263,5106257,'NOVA XAVANTINA','MT','nova-xavantina'),(5264,5106265,'NOVO MUNDO','MT','novo-mundo'),(5265,5106273,'NOVO HORIZONTE DO NORTE','MT','novo-horizonte-do-norte'),(5266,5106281,'NOVO SÃO JOAQUIM','MT','novo-sao-joaquim'),(5267,5106299,'PARANAÍTA','MT','paranaita'),(5268,5106307,'PARANATINGA','MT','paranatinga'),(5269,5106315,'NOVO SANTO ANTÔNIO','MT','novo-santo-antonio'),(5270,5106372,'PEDRA PRETA','MT','pedra-preta'),(5271,5106422,'PEIXOTO DE AZEVEDO','MT','peixoto-de-azevedo'),(5272,5106455,'PLANALTO DA SERRA','MT','planalto-da-serra'),(5273,5106505,'POCONÉ','MT','pocone'),(5274,5106653,'PONTAL DO ARAGUAIA','MT','pontal-do-araguaia'),(5275,5106703,'PONTE BRANCA','MT','ponte-branca'),(5276,5106752,'PONTES E LACERDA','MT','pontes-e-lacerda'),(5277,5106778,'PORTO ALEGRE DO NORTE','MT','porto-alegre-do-norte'),(5278,5106802,'PORTO DOS GAÚCHOS','MT','porto-dos-gauchos'),(5279,5106828,'PORTO ESPERIDIÃO','MT','porto-esperidiao'),(5280,5106851,'PORTO ESTRELA','MT','porto-estrela'),(5281,5107008,'POXORÉO','MT','poxoreo'),(5282,5107040,'PRIMAVERA DO LESTE','MT','primavera-do-leste'),(5283,5107065,'QUERÊNCIA','MT','querencia'),(5284,5107107,'SÃO JOSÉ DOS QUATRO MARCOS','MT','sao-jose-dos-quatro-marcos'),(5285,5107156,'RESERVA DO CABAÇAL','MT','reserva-do-cabacal'),(5286,5107180,'RIBEIRÃO CASCALHEIRA','MT','ribeirao-cascalheira'),(5287,5107198,'RIBEIRÃOZINHO','MT','ribeiraozinho'),(5288,5107206,'RIO BRANCO','MT','rio-branco'),(5289,5107248,'SANTA CARMEM','MT','santa-carmem'),(5290,5107263,'SANTO AFONSO','MT','santo-afonso'),(5291,5107297,'SÃO JOSÉ DO POVO','MT','sao-jose-do-povo'),(5292,5107305,'SÃO JOSÉ DO RIO CLARO','MT','sao-jose-do-rio-claro'),(5293,5107354,'SÃO JOSÉ DO XINGU','MT','sao-jose-do-xingu'),(5294,5107404,'SÃO PEDRO DA CIPA','MT','sao-pedro-da-cipa'),(5295,5107578,'RONDOLÂNDIA','MT','rondolandia'),(5296,5107602,'RONDONÓPOLIS','MT','rondonopolis'),(5297,5107701,'ROSÁRIO OESTE','MT','rosario-oeste'),(5298,5107743,'SANTA CRUZ DO XINGU','MT','santa-cruz-do-xingu'),(5299,5107750,'SALTO DO CÉU','MT','salto-do-ceu'),(5300,5107768,'SANTA RITA DO TRIVELATO','MT','santa-rita-do-trivelato'),(5301,5107776,'SANTA TEREZINHA','MT','santa-terezinha'),(5302,5107792,'SANTO ANTÔNIO DO LESTE','MT','santo-antonio-do-leste'),(5303,5107800,'SANTO ANTÔNIO DO LEVERGER','MT','santo-antonio-do-leverger'),(5304,5107859,'SÃO FÉLIX DO ARAGUAIA','MT','sao-felix-do-araguaia'),(5305,5107875,'SAPEZAL','MT','sapezal'),(5306,5107883,'SERRA NOVA DOURADA','MT','serra-nova-dourada'),(5307,5107909,'SINOP','MT','sinop'),(5308,5107925,'SORRISO','MT','sorriso'),(5309,5107941,'TABAPORÃ','MT','tabapora'),(5310,5107958,'TANGARÁ DA SERRA','MT','tangara-da-serra'),(5311,5108006,'TAPURAH','MT','tapurah'),(5312,5108055,'TERRA NOVA DO NORTE','MT','terra-nova-do-norte'),(5313,5108105,'TESOURO','MT','tesouro'),(5314,5108204,'TORIXORÉU','MT','torixoreu'),(5315,5108303,'UNIÃO DO SUL','MT','uniao-do-sul'),(5316,5108352,'VALE DE SÃO DOMINGOS','MT','vale-de-sao-domingos'),(5317,5108402,'VÁRZEA GRANDE','MT','varzea-grande'),(5318,5108501,'VERA','MT','vera'),(5319,5108600,'VILA RICA','MT','vila-rica'),(5320,5108808,'NOVA GUARITA','MT','nova-guarita'),(5321,5108857,'NOVA MARILÂNDIA','MT','nova-marilandia'),(5322,5108907,'NOVA MARINGÁ','MT','nova-maringa'),(5323,5108956,'NOVA MONTE VERDE','MT','nova-monte-verde'),(5324,5200050,'ABADIA DE GOIÁS','GO','abadia-de-goias'),(5325,5200100,'ABADIÂNIA','GO','abadiania'),(5326,5200134,'ACREÚNA','GO','acreuna'),(5327,5200159,'ADELÂNDIA','GO','adelandia'),(5328,5200175,'ÁGUA FRIA DE GOIÁS','GO','agua-fria-de-goias'),(5329,5200209,'ÁGUA LIMPA','GO','agua-limpa'),(5330,5200258,'ÁGUAS LINDAS DE GOIÁS','GO','aguas-lindas-de-goias'),(5331,5200308,'ALEXÂNIA','GO','alexania'),(5332,5200506,'ALOÂNDIA','GO','aloandia'),(5333,5200555,'ALTO HORIZONTE','GO','alto-horizonte'),(5334,5200605,'ALTO PARAÍSO DE GOIÁS','GO','alto-paraiso-de-goias'),(5335,5200803,'ALVORADA DO NORTE','GO','alvorada-do-norte'),(5336,5200829,'AMARALINA','GO','amaralina'),(5337,5200852,'AMERICANO DO BRASIL','GO','americano-do-brasil'),(5338,5200902,'AMORINÓPOLIS','GO','amorinopolis'),(5339,5201108,'ANÁPOLIS','GO','anapolis'),(5340,5201207,'ANHANGUERA','GO','anhanguera'),(5341,5201306,'ANICUNS','GO','anicuns'),(5342,5201405,'APARECIDA DE GOIÂNIA','GO','aparecida-de-goiania'),(5343,5201454,'APARECIDA DO RIO DOCE','GO','aparecida-do-rio-doce'),(5344,5201504,'APORÉ','GO','apore'),(5345,5201603,'ARAÇU','GO','aracu'),(5346,5201702,'ARAGARÇAS','GO','aragarcas'),(5347,5201801,'ARAGOIÂNIA','GO','aragoiania'),(5348,5202155,'ARAGUAPAZ','GO','araguapaz'),(5349,5202353,'ARENÓPOLIS','GO','arenopolis'),(5350,5202502,'ARUANÃ','GO','aruana'),(5351,5202601,'AURILÂNDIA','GO','aurilandia'),(5352,5202809,'AVELINÓPOLIS','GO','avelinopolis'),(5353,5203104,'BALIZA','GO','baliza'),(5354,5203203,'BARRO ALTO','GO','barro-alto'),(5355,5203302,'BELA VISTA DE GOIÁS','GO','bela-vista-de-goias'),(5356,5203401,'BOM JARDIM DE GOIÁS','GO','bom-jardim-de-goias'),(5357,5203500,'BOM JESUS DE GOIÁS','GO','bom-jesus-de-goias'),(5358,5203559,'BONFINÓPOLIS','GO','bonfinopolis'),(5359,5203575,'BONÓPOLIS','GO','bonopolis'),(5360,5203609,'BRAZABRANTES','GO','brazabrantes'),(5361,5203807,'BRITÂNIA','GO','britania'),(5362,5203906,'BURITI ALEGRE','GO','buriti-alegre'),(5363,5203939,'BURITI DE GOIÁS','GO','buriti-de-goias'),(5364,5203962,'BURITINÓPOLIS','GO','buritinopolis'),(5365,5204003,'CABECEIRAS','GO','cabeceiras'),(5366,5204102,'CACHOEIRA ALTA','GO','cachoeira-alta'),(5367,5204201,'CACHOEIRA DE GOIÁS','GO','cachoeira-de-goias'),(5368,5204250,'CACHOEIRA DOURADA','GO','cachoeira-dourada'),(5369,5204300,'CAÇU','GO','cacu'),(5370,5204409,'CAIAPÔNIA','GO','caiaponia'),(5371,5204508,'CALDAS NOVAS','GO','caldas-novas'),(5372,5204557,'CALDAZINHA','GO','caldazinha'),(5373,5204607,'CAMPESTRE DE GOIÁS','GO','campestre-de-goias'),(5374,5204656,'CAMPINAÇU','GO','campinacu'),(5375,5204706,'CAMPINORTE','GO','campinorte'),(5376,5204805,'CAMPO ALEGRE DE GOIÁS','GO','campo-alegre-de-goias'),(5377,5204854,'CAMPO LIMPO DE GOIÁS','GO','campo-limpo-de-goias'),(5378,5204904,'CAMPOS BELOS','GO','campos-belos'),(5379,5204953,'CAMPOS VERDES','GO','campos-verdes'),(5380,5205000,'CARMO DO RIO VERDE','GO','carmo-do-rio-verde'),(5381,5205059,'CASTELÂNDIA','GO','castelandia'),(5382,5205109,'CATALÃO','GO','catalao'),(5383,5205208,'CATURAÍ','GO','caturai'),(5384,5205307,'CAVALCANTE','GO','cavalcante'),(5385,5205406,'CERES','GO','ceres'),(5386,5205455,'CEZARINA','GO','cezarina'),(5387,5205471,'CHAPADÃO DO CÉU','GO','chapadao-do-ceu'),(5388,5205497,'CIDADE OCIDENTAL','GO','cidade-ocidental'),(5389,5205513,'COCALZINHO DE GOIÁS','GO','cocalzinho-de-goias'),(5390,5205521,'COLINAS DO SUL','GO','colinas-do-sul'),(5391,5205703,'CÓRREGO DO OURO','GO','corrego-do-ouro'),(5392,5205802,'CORUMBÁ DE GOIÁS','GO','corumba-de-goias'),(5393,5205901,'CORUMBAÍBA','GO','corumbaiba'),(5394,5206206,'CRISTALINA','GO','cristalina'),(5395,5206305,'CRISTIANÓPOLIS','GO','cristianopolis'),(5396,5206404,'CRIXÁS','GO','crixas'),(5397,5206503,'CROMÍNIA','GO','crominia'),(5398,5206602,'CUMARI','GO','cumari'),(5399,5206701,'DAMIANÓPOLIS','GO','damianopolis'),(5400,5206800,'DAMOLÂNDIA','GO','damolandia'),(5401,5206909,'DAVINÓPOLIS','GO','davinopolis'),(5402,5207105,'DIORAMA','GO','diorama'),(5403,5207253,'DOVERLÂNDIA','GO','doverlandia'),(5404,5207352,'EDEALINA','GO','edealina'),(5405,5207402,'EDÉIA','GO','edeia'),(5406,5207501,'ESTRELA DO NORTE','GO','estrela-do-norte'),(5407,5207535,'FAINA','GO','faina'),(5408,5207600,'FAZENDA NOVA','GO','fazenda-nova'),(5409,5207808,'FIRMINÓPOLIS','GO','firminopolis'),(5410,5207907,'FLORES DE GOIÁS','GO','flores-de-goias'),(5411,5208004,'FORMOSA','GO','formosa'),(5412,5208103,'FORMOSO','GO','formoso'),(5413,5208152,'GAMELEIRA DE GOIÁS','GO','gameleira-de-goias'),(5414,5208301,'DIVINÓPOLIS DE GOIÁS','GO','divinopolis-de-goias'),(5415,5208400,'GOIANÁPOLIS','GO','goianapolis'),(5416,5208509,'GOIANDIRA','GO','goiandira'),(5417,5208608,'GOIANÉSIA','GO','goianesia'),(5418,5208707,'GOIÂNIA','GO','goiania'),(5419,5208806,'GOIANIRA','GO','goianira'),(5420,5208905,'GOIÁS','GO','goias'),(5421,5209101,'GOIATUBA','GO','goiatuba'),(5422,5209150,'GOUVELÂNDIA','GO','gouvelandia'),(5423,5209200,'GUAPÓ','GO','guapo'),(5424,5209291,'GUARAÍTA','GO','guaraita'),(5425,5209408,'GUARANI DE GOIÁS','GO','guarani-de-goias'),(5426,5209457,'GUARINOS','GO','guarinos'),(5427,5209606,'HEITORAÍ','GO','heitorai'),(5428,5209705,'HIDROLÂNDIA','GO','hidrolandia'),(5429,5209804,'HIDROLINA','GO','hidrolina'),(5430,5209903,'IACIARA','GO','iaciara'),(5431,5209937,'INACIOLÂNDIA','GO','inaciolandia'),(5432,5209952,'INDIARA','GO','indiara'),(5433,5210000,'INHUMAS','GO','inhumas'),(5434,5210109,'IPAMERI','GO','ipameri'),(5435,5210158,'IPIRANGA DE GOIÁS','GO','ipiranga-de-goias'),(5436,5210208,'IPORÁ','GO','ipora'),(5437,5210307,'ISRAELÂNDIA','GO','israelandia'),(5438,5210406,'ITABERAÍ','GO','itaberai'),(5439,5210562,'ITAGUARI','GO','itaguari'),(5440,5210604,'ITAGUARU','GO','itaguaru'),(5441,5210802,'ITAJÁ','GO','itaja'),(5442,5210901,'ITAPACI','GO','itapaci'),(5443,5211008,'ITAPIRAPUÃ','GO','itapirapua'),(5444,5211206,'ITAPURANGA','GO','itapuranga'),(5445,5211305,'ITARUMÃ','GO','itaruma'),(5446,5211404,'ITAUÇU','GO','itaucu'),(5447,5211503,'ITUMBIARA','GO','itumbiara'),(5448,5211602,'IVOLÂNDIA','GO','ivolandia'),(5449,5211701,'JANDAIA','GO','jandaia'),(5450,5211800,'JARAGUÁ','GO','jaragua'),(5451,5211909,'JATAÍ','GO','jatai'),(5452,5212006,'JAUPACI','GO','jaupaci'),(5453,5212055,'JESÚPOLIS','GO','jesupolis'),(5454,5212105,'JOVIÂNIA','GO','joviania'),(5455,5212204,'JUSSARA','GO','jussara'),(5456,5212253,'LAGOA SANTA','GO','lagoa-santa'),(5457,5212303,'LEOPOLDO DE BULHÕES','GO','leopoldo-de-bulhoes'),(5458,5212501,'LUZIÂNIA','GO','luziania'),(5459,5212600,'MAIRIPOTABA','GO','mairipotaba'),(5460,5212709,'MAMBAÍ','GO','mambai'),(5461,5212808,'MARA ROSA','GO','mara-rosa'),(5462,5212907,'MARZAGÃO','GO','marzagao'),(5463,5212956,'MATRINCHÃ','GO','matrincha'),(5464,5213004,'MAURILÂNDIA','GO','maurilandia'),(5465,5213053,'MIMOSO DE GOIÁS','GO','mimoso-de-goias'),(5466,5213087,'MINAÇU','GO','minacu'),(5467,5213103,'MINEIROS','GO','mineiros'),(5468,5213400,'MOIPORÁ','GO','moipora'),(5469,5213509,'MONTE ALEGRE DE GOIÁS','GO','monte-alegre-de-goias'),(5470,5213707,'MONTES CLAROS DE GOIÁS','GO','montes-claros-de-goias'),(5471,5213756,'MONTIVIDIU','GO','montividiu'),(5472,5213772,'MONTIVIDIU DO NORTE','GO','montividiu-do-norte'),(5473,5213806,'MORRINHOS','GO','morrinhos'),(5474,5213855,'MORRO AGUDO DE GOIÁS','GO','morro-agudo-de-goias'),(5475,5213905,'MOSSÂMEDES','GO','mossamedes'),(5476,5214002,'MOZARLÂNDIA','GO','mozarlandia'),(5477,5214051,'MUNDO NOVO','GO','mundo-novo'),(5478,5214101,'MUTUNÓPOLIS','GO','mutunopolis'),(5479,5214408,'NAZÁRIO','GO','nazario'),(5480,5214507,'NERÓPOLIS','GO','neropolis'),(5481,5214606,'NIQUELÂNDIA','GO','niquelandia'),(5482,5214705,'NOVA AMÉRICA','GO','nova-america'),(5483,5214804,'NOVA AURORA','GO','nova-aurora'),(5484,5214838,'NOVA CRIXÁS','GO','nova-crixas'),(5485,5214861,'NOVA GLÓRIA','GO','nova-gloria'),(5486,5214879,'NOVA IGUAÇU DE GOIÁS','GO','nova-iguacu-de-goias'),(5487,5214903,'NOVA ROMA','GO','nova-roma'),(5488,5215009,'NOVA VENEZA','GO','nova-veneza'),(5489,5215207,'NOVO BRASIL','GO','novo-brasil'),(5490,5215231,'NOVO GAMA','GO','novo-gama'),(5491,5215256,'NOVO PLANALTO','GO','novo-planalto'),(5492,5215306,'ORIZONA','GO','orizona'),(5493,5215405,'OURO VERDE DE GOIÁS','GO','ouro-verde-de-goias'),(5494,5215504,'OUVIDOR','GO','ouvidor'),(5495,5215603,'PADRE BERNARDO','GO','padre-bernardo'),(5496,5215652,'PALESTINA DE GOIÁS','GO','palestina-de-goias'),(5497,5215702,'PALMEIRAS DE GOIÁS','GO','palmeiras-de-goias'),(5498,5215801,'PALMELO','GO','palmelo'),(5499,5215900,'PALMINÓPOLIS','GO','palminopolis'),(5500,5216007,'PANAMÁ','GO','panama'),(5501,5216304,'PARANAIGUARA','GO','paranaiguara'),(5502,5216403,'PARAÚNA','GO','parauna'),(5503,5216452,'PEROLÂNDIA','GO','perolandia'),(5504,5216809,'PETROLINA DE GOIÁS','GO','petrolina-de-goias'),(5505,5216908,'PILAR DE GOIÁS','GO','pilar-de-goias'),(5506,5217104,'PIRACANJUBA','GO','piracanjuba'),(5507,5217203,'PIRANHAS','GO','piranhas'),(5508,5217302,'PIRENÓPOLIS','GO','pirenopolis'),(5509,5217401,'PIRES DO RIO','GO','pires-do-rio'),(5510,5217609,'PLANALTINA','GO','planaltina'),(5511,5217708,'PONTALINA','GO','pontalina'),(5512,5218003,'PORANGATU','GO','porangatu'),(5513,5218052,'PORTEIRÃO','GO','porteirao'),(5514,5218102,'PORTELÂNDIA','GO','portelandia'),(5515,5218300,'POSSE','GO','posse'),(5516,5218391,'PROFESSOR JAMIL','GO','professor-jamil'),(5517,5218508,'QUIRINÓPOLIS','GO','quirinopolis'),(5518,5218607,'RIALMA','GO','rialma'),(5519,5218706,'RIANÁPOLIS','GO','rianapolis'),(5520,5218789,'RIO QUENTE','GO','rio-quente'),(5521,5218805,'RIO VERDE','GO','rio-verde'),(5522,5218904,'RUBIATABA','GO','rubiataba'),(5523,5219001,'SANCLERLÂNDIA','GO','sanclerlandia'),(5524,5219100,'SANTA BÁRBARA DE GOIÁS','GO','santa-barbara-de-goias'),(5525,5219209,'SANTA CRUZ DE GOIÁS','GO','santa-cruz-de-goias'),(5526,5219258,'SANTA FÉ DE GOIÁS','GO','santa-fe-de-goias'),(5527,5219308,'SANTA HELENA DE GOIÁS','GO','santa-helena-de-goias'),(5528,5219357,'SANTA ISABEL','GO','santa-isabel'),(5529,5219407,'SANTA RITA DO ARAGUAIA','GO','santa-rita-do-araguaia'),(5530,5219456,'SANTA RITA DO NOVO DESTINO','GO','santa-rita-do-novo-destino'),(5531,5219506,'SANTA ROSA DE GOIÁS','GO','santa-rosa-de-goias'),(5532,5219605,'SANTA TEREZA DE GOIÁS','GO','santa-tereza-de-goias'),(5533,5219704,'SANTA TEREZINHA DE GOIÁS','GO','santa-terezinha-de-goias'),(5534,5219712,'SANTO ANTÔNIO DA BARRA','GO','santo-antonio-da-barra'),(5535,5219738,'SANTO ANTÔNIO DE GOIÁS','GO','santo-antonio-de-goias'),(5536,5219753,'SANTO ANTÔNIO DO DESCOBERTO','GO','santo-antonio-do-descoberto'),(5537,5219803,'SÃO DOMINGOS','GO','sao-domingos'),(5538,5219902,'SÃO FRANCISCO DE GOIÁS','GO','sao-francisco-de-goias'),(5539,5220009,'SÃO JOÃO D\'ALIANÇA','GO','sao-joao-dalianca'),(5540,5220058,'SÃO JOÃO DA PARAÚNA','GO','sao-joao-da-parauna'),(5541,5220108,'SÃO LUÍS DE MONTES BELOS','GO','sao-luis-de-montes-belos'),(5542,5220157,'SÃO LUÍZ DO NORTE','GO','sao-luiz-do-norte'),(5543,5220207,'SÃO MIGUEL DO ARAGUAIA','GO','sao-miguel-do-araguaia'),(5544,5220264,'SÃO MIGUEL DO PASSA QUATRO','GO','sao-miguel-do-passa-quatro'),(5545,5220280,'SÃO PATRÍCIO','GO','sao-patricio'),(5546,5220405,'SÃO SIMÃO','GO','sao-simao'),(5547,5220454,'SENADOR CANEDO','GO','senador-canedo'),(5548,5220504,'SERRANÓPOLIS','GO','serranopolis'),(5549,5220603,'SILVÂNIA','GO','silvania'),(5550,5220686,'SIMOLÂNDIA','GO','simolandia'),(5551,5220702,'SÍTIO D\'ABADIA','GO','sitio-dabadia'),(5552,5221007,'TAQUARAL DE GOIÁS','GO','taquaral-de-goias'),(5553,5221080,'TERESINA DE GOIÁS','GO','teresina-de-goias'),(5554,5221197,'TEREZÓPOLIS DE GOIÁS','GO','terezopolis-de-goias'),(5555,5221304,'TRÊS RANCHOS','GO','tres-ranchos'),(5556,5221403,'TRINDADE','GO','trindade'),(5557,5221452,'TROMBAS','GO','trombas'),(5558,5221502,'TURVÂNIA','GO','turvania'),(5559,5221551,'TURVELÂNDIA','GO','turvelandia'),(5560,5221577,'UIRAPURU','GO','uirapuru'),(5561,5221601,'URUAÇU','GO','uruacu'),(5562,5221700,'URUANA','GO','uruana'),(5563,5221809,'URUTAÍ','GO','urutai'),(5564,5221858,'VALPARAÍSO DE GOIÁS','GO','valparaiso-de-goias'),(5565,5221908,'VARJÃO','GO','varjao'),(5566,5222005,'VIANÓPOLIS','GO','vianopolis'),(5567,5222054,'VICENTINÓPOLIS','GO','vicentinopolis'),(5568,5222203,'VILA BOA','GO','vila-boa'),(5569,5222302,'VILA PROPÍCIO','GO','vila-propicio'),(5570,5300108,'BRASÍLIA','DF','brasilia');
/*!40000 ALTER TABLE `cities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `client_type_resource_types`
--

DROP TABLE IF EXISTS `client_type_resource_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_type_resource_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_customer_type` int(11) DEFAULT NULL,
  `id_customer` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client_type_resource_types`
--

LOCK TABLES `client_type_resource_types` WRITE;
/*!40000 ALTER TABLE `client_type_resource_types` DISABLE KEYS */;
INSERT INTO `client_type_resource_types` VALUES (1,10,1);
/*!40000 ALTER TABLE `client_type_resource_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `communication_channels`
--

DROP TABLE IF EXISTS `communication_channels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `communication_channels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `status` binary(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `communication_channels`
--

LOCK TABLES `communication_channels` WRITE;
/*!40000 ALTER TABLE `communication_channels` DISABLE KEYS */;
INSERT INTO `communication_channels` VALUES (1,'Cliente / Fluxo',_binary '1'),(2,'Cliente / Prospecção',_binary '1'),(3,'Facebook',_binary '1'),(4,'Indicação',_binary '1'),(5,'Instagram',_binary '1'),(6,'Jornal',_binary '1'),(7,'Ligação',_binary '1'),(8,'Site',_binary '1'),(9,'Rádio',_binary '1'),(10,'Outros',_binary '1');
/*!40000 ALTER TABLE `communication_channels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configuracao`
--

DROP TABLE IF EXISTS `configuracao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `configuracao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `url_global` text NOT NULL,
  `url_sistema` text DEFAULT NULL,
  `localizacao` text DEFAULT NULL,
  `localizacao_status` binary(1) NOT NULL DEFAULT '1',
  `rodape` text NOT NULL,
  `telefone` text DEFAULT NULL,
  `celular` varchar(50) DEFAULT NULL,
  `facebook_frame` text DEFAULT NULL,
  `instagram_frame` text DEFAULT NULL,
  `google_analytics` text DEFAULT NULL,
  `endereco` text DEFAULT NULL,
  `tawk` text DEFAULT NULL,
  `frase` text DEFAULT NULL,
  `cont_logo` int(11) DEFAULT 1,
  `cont_rodape` int(11) DEFAULT 1,
  `cont_favicon` int(11) DEFAULT 1,
  `capa_logo` binary(1) DEFAULT '0',
  `capa_rodape` binary(1) DEFAULT '0',
  `capa_favicon` binary(1) DEFAULT '0',
  `cont_share` int(11) DEFAULT 1,
  `capa_share` binary(1) DEFAULT '0',
  `localizacao2` text DEFAULT NULL,
  `popup_ativo` binary(1) DEFAULT '1',
  `popup_tipo` int(11) DEFAULT 1,
  `popup_texto` text DEFAULT NULL,
  `popup_imagem` text DEFAULT NULL,
  `popup_imagem_cont` int(11) DEFAULT 1,
  `popup_imagem_ext` varchar(20) DEFAULT NULL,
  `popup_video` text DEFAULT NULL,
  `popup_cor_fonte` varchar(50) DEFAULT NULL,
  `popup_background` varchar(50) DEFAULT NULL,
  `popup_imagem_capa` binary(1) DEFAULT '0',
  `whatsapp_link` varchar(50) DEFAULT NULL,
  `topo_cor_fundo` varchar(50) DEFAULT '#009eff',
  `topo_cor_fonte` varchar(50) DEFAULT '#ffffff',
  `topo_botao_cor` varchar(50) DEFAULT '#ffffff',
  `topo_botao_cor_efeito` varchar(50) DEFAULT '#003d63',
  `menu_cor_fundo` varchar(50) DEFAULT '#ffffff',
  `menu_cor_fonte` varchar(50) DEFAULT '#515151',
  `menu_cor_fonte_efeito` varchar(50) DEFAULT '#009eff',
  `banner_cor_fundo` varchar(50) DEFAULT '#009eff',
  `filtro_cor_fundo` varchar(50) DEFAULT '#f9f9f9',
  `filtro_campo_cor_fonte` varchar(50) DEFAULT '#ffffff',
  `filtro_campo_cor_fundo` varchar(50) DEFAULT '#515151',
  `base_cor_fonte` varchar(50) DEFAULT '#515151',
  `filtro_botao_cor_fonte` varchar(50) DEFAULT '#ffffff',
  `filtro_botao_cor_fonte_efeito` varchar(50) DEFAULT '#ffffff',
  `filtro_botao_cor_fundo` varchar(50) DEFAULT '#009eff',
  `filtro_botao_cor_fundo_efeito` varchar(50) DEFAULT '#003d63',
  `filtro_valor_cor_fundo` varchar(50) DEFAULT '#009eff',
  `filtro_botao_avancado_cor_fonte` varchar(50) DEFAULT '#515151',
  `filtro_botao_avancado_cor_fonte_efeito` varchar(50) DEFAULT '#009eff',
  `empresa_home_cor_fundo` varchar(50) DEFAULT '#f4f4f4',
  `empresa_home_apoio_titulo_cor_fonte` varchar(50) DEFAULT '#009eff',
  `empresa_home_titulo_cor_fonte` varchar(50) DEFAULT '#353535',
  `empresa_home_texto_cor_fonte` varchar(50) DEFAULT '#515151',
  `empresa_home_botao_cor_fundo` varchar(50) DEFAULT '#f4f4f4',
  `empresa_home_botao_cor_fundo_efeito` varchar(50) DEFAULT '#009eff',
  `empresa_home_botao_cor_fonte` varchar(50) DEFAULT '#009eff',
  `empresa_home_botao_cor_fonte_efeito` varchar(50) DEFAULT '#ffffff',
  `empresa_home_botao_cor_borda` varchar(50) DEFAULT '#009eff',
  `contato_home_cor_fundo` varchar(50) DEFAULT '#ffffff',
  `contato_home_apoio_titulo_cor_fonte` varchar(50) DEFAULT '#009eff',
  `contato_home_titulo_cor_fonte` varchar(50) DEFAULT '#353535',
  `contato_home_botao_cor_fonte` varchar(50) DEFAULT '#ffffff',
  `contato_home_botao_cor_fonte_efeito` varchar(50) DEFAULT '#ffffff',
  `contato_home_botao_cor_fundo` varchar(50) DEFAULT '#009eff',
  `contato_home_botao_cor_fundo_efeito` varchar(50) DEFAULT '#003d63',
  `contato_home_box_cor_fundo` varchar(50) DEFAULT '#ffffff',
  `contato_home_box_cor_sombra` varchar(50) DEFAULT '0 0 0',
  `contato_home_box_cor_fonte` varchar(50) DEFAULT '#515151',
  `mapa_botao_cor_fonte` varchar(50) DEFAULT '#ffffff',
  `mapa_botao_cor_fonte_efeito` varchar(50) DEFAULT '#ffffff',
  `mapa_botao_cor_fundo` varchar(50) DEFAULT '#009eff',
  `mapa_botao_cor_fundo_efeito` varchar(55) DEFAULT '#009eff',
  `rodape_cor_fundo` varchar(50) DEFAULT '#009eff',
  `rodape_cor_fonte` varchar(50) DEFAULT '#ffffff',
  `rodape_cor_fonte_efeito` varchar(50) DEFAULT '#003d63',
  `direito_cor_fundo` varchar(50) DEFAULT '#008be0',
  `direito_cor_fonte` varchar(50) DEFAULT '#ffffff',
  `direito_cor_fonte_efeito` varchar(50) DEFAULT '#003d63',
  `imovel_apoio_titulo_cor_fonte` varchar(50) DEFAULT '#009eff',
  `imovel_titulo_cor_fonte` varchar(50) DEFAULT '#353535',
  `imovel_box_cor_fundo` varchar(50) DEFAULT '#ffffff',
  `imovel_box_cor_fonte` varchar(50) DEFAULT '#515151',
  `imovel_box_titulo_cor_fonte` varchar(50) DEFAULT '#009eff',
  `imovel_box_tag_cor_fonte` varchar(55) DEFAULT '#ffffff',
  `imovel_box_tag_cor_fundo` varchar(50) DEFAULT '#009eff',
  `imovel_box_botao_cor_fonte` varchar(50) DEFAULT '#ffffff',
  `imovel_box_botao_cor_fonte_efeito` varchar(50) DEFAULT '#ffffff',
  `imovel_box_botao_cor_fundo` varchar(50) DEFAULT '#009eff',
  `imovel_box_botao_cor_fundo_efeito` varchar(50) DEFAULT '#003d63',
  `imovel_cor_fundo` varchar(50) DEFAULT '#ffffff',
  `imovel_mais_imoveis_cor_fonte` varchar(50) DEFAULT '#009eff',
  `imovel_mais_imoveis_cor_fonte_efeito` varchar(50) DEFAULT '#ffffff',
  `imovel_mais_imoveis_cor_fundo` varchar(50) DEFAULT '#ffffff',
  `imovel_mais_imoveis_cor_fundo_efeito` varchar(50) DEFAULT '#009eff',
  `pagina_cor_fundo` varchar(50) DEFAULT '#f4f4f4',
  `pagina_apoio_titulo_cor` varchar(50) DEFAULT '#009eff',
  `pagina_titulo_cor` varchar(50) DEFAULT '#353535',
  `pagina_cor_fonte` varchar(50) DEFAULT '#515151',
  `imovel_box_detalhe_cor_fundo` varchar(50) DEFAULT '#ffffff',
  `imovel_box_detalhe_cor_fonte` varchar(50) DEFAULT '#353535',
  `central_atendimento_cor_fundo` varchar(50) DEFAULT '#009eff',
  `central_atendimento_cor_fonte` varchar(50) DEFAULT '#ffffff',
  `central_atendimento_cor_fonte_efeito` varchar(50) DEFAULT '#003d63',
  `praia_sonho_cor_fundo` varchar(50) DEFAULT '#009eff',
  `praia_sonho_cor_fonte` varchar(50) DEFAULT '#ffffff',
  `praia_sonho_cor_fonte_efeito` varchar(50) DEFAULT '#003d63',
  `filtro_lateral_cor_fonte` varchar(50) DEFAULT '#999999',
  `filtro_lateral_cor_fonte_efeito` varchar(50) DEFAULT '#009eff',
  `central_atendimento` binary(1) DEFAULT '1',
  `praia_sonho` binary(1) DEFAULT '1',
  `praia_capa` binary(1) DEFAULT '0',
  `praia_cont` int(11) DEFAULT 0,
  `praia_ext` varchar(20) DEFAULT NULL,
  `modulo_menu_filtros_imovel` binary(1) DEFAULT '1',
  `codigo` binary(1) DEFAULT '0',
  `codigo_tipo` int(11) DEFAULT 1,
  `box_galeria_cor_fundo` varchar(50) DEFAULT '#cdcdcd',
  `cronograma` binary(1) DEFAULT '0',
  `script_header` text DEFAULT NULL,
  `script_body_top` text DEFAULT NULL,
  `script_body_bottom` text DEFAULT NULL,
  `filtro_and_cor_fonte` varchar(10) DEFAULT '#515151',
  `filtro_and_cor_fonte_efeito` varchar(7) DEFAULT '#009eff',
  `andamento` int(11) DEFAULT 0,
  `status_site` binary(1) DEFAULT '1',
  `layout_top_left` binary(1) NOT NULL DEFAULT '1',
  `layout_bottom_left` binary(1) NOT NULL DEFAULT '1',
  `layout_bottom_right` binary(1) NOT NULL DEFAULT '1',
  `layout_desc_value` binary(1) NOT NULL DEFAULT '1',
  `creci` varchar(11) DEFAULT NULL,
  `filtro_cor_barra_lateral_subtitulo` varchar(50) DEFAULT '#353535',
  `filtro_cor_barra_lateral_titulo` varchar(50) DEFAULT '#353535',
  `whatsapp_cor_botao` varchar(50) DEFAULT '#147118',
  `whatsapp_tamanho_botao` int(11) DEFAULT 50,
  `whatsapp_direcao` varchar(10) DEFAULT 'right',
  `cor_caixa_contato` varchar(50) DEFAULT '#fff',
  `cor_fonte_contato` varchar(50) DEFAULT '#515151',
  `cor_titulo_contato` varchar(50) DEFAULT '#353535',
  `advanced_filter` int(11) DEFAULT 1,
  `input_type` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuracao`
--

LOCK TABLES `configuracao` WRITE;
/*!40000 ALTER TABLE `configuracao` DISABLE KEYS */;
INSERT INTO `configuracao` VALUES (1,'Realize Repasse','','http://localhost/realze_repasse/',NULL,_binary '1','© Realize Repasse - Todos os direitos reservados - Desenvolvido por Ydeal Tecnologia',NULL,'','','',NULL,'Rua Manoel Luis dos Santos',NULL,'SEU FUTURO ESTÁ EM NOSSOS PLANOS!1',0,0,0,_binary '0',_binary '0',_binary '0',0,_binary '0',NULL,_binary '0',1,'<p><strong>Lorem Ipsum</strong>&nbsp;&eacute; simplesmente uma simula&ccedil;&atilde;o de texto da ind&uacute;stria tipogr&aacute;fica e de impressos, e vem sendo utilizado desde o s&eacute;culo XVI, quando um impressor desconhecido pegou uma bandeja de tipos e os embaralhou para fazer um livro de modelos de tipos. Lorem Ipsum sobreviveu n&atilde;o s&oacute; a cinco s&eacute;culos, como tamb&eacute;m ao salto para a editora&ccedil;&atilde;o eletr&ocirc;nica, permanecendo essencialmente inalterado.</p>\r\n\r\n<p>Se popularizou na d&eacute;cada de 60, quando a Letraset lan&ccedil;ou decalques contendo passagens de Lorem Ipsum, e mais recentemente quando passou a ser integrado a softwares de editora&ccedil;&atilde;o eletr&ocirc;nica como Aldus PageMaker.</p>\r\n','0',0,'',NULL,'#ffffff','#000000',_binary '\0','5532636688','#184264','#ffffff','#ffffff','#919192','#ffffff','#184264','#919192','#184264','#f9f9f9','#515151','#ffffff','#515151','#ffffff','#ffffff','#184264','#919192','#184264','#515151','#184264','#f4f4f4','#184264','#353535',NULL,'#f4f4f4','#009eff','#009eff','#ffffff','#009eff','#ffffff','#184264','#353535','#ffffff','#ffffff','#009eff','#003d63','#ffffff','0 0 0','#515151','#ffffff','#ffffff','#009eff','#009eff','#184264','#ffffff','#919192','#153450','#ffffff','#919192','#184264','#353535','#ffffff','#515151','#184264','#ffffff','#184264','#ffffff',NULL,'#184264','#919192','#ffffff','#184264','#ffffff','#ffffff','#184264','#f4f4f4','#184264','#353535','#515151','#f4f4f4','#353535','#184264','#ffffff','#919192','#184264','#ffffff','#919192','#999999','#184264',_binary '1',_binary '\0',_binary '0',0,NULL,_binary '0',_binary '1',2,'#cdcdcd',_binary '0','<meta name=\"facebook-domain-verification\" content=\"kh8mwb58cbykrc8vz62p99sjyois2y\" />\r\n\r\n<!-- Google Tag Manager -->\r\n<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({\'gtm.start\':\r\nnew Date().getTime(),event:\'gtm.js\'});var f=d.getElementsByTagName(s)[0],\r\nj=d.createElement(s),dl=l!=\'dataLayer\'?\'&l=\'+l:\'\';j.async=true;j.src=\r\n\'https://www.googletagmanager.com/gtm.js?id=\'+i+dl;f.parentNode.insertBefore(j,f);\r\n})(window,document,\'script\',\'dataLayer\',\'GTM-P4NPW4N\');</script>\r\n<!-- End Google Tag Manager -->','<!-- Google Tag Manager (noscript) -->\r\n<noscript><iframe src=\"https://www.googletagmanager.com/ns.html?id=GTM-P4NPW4N\"\r\nheight=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript>\r\n<!-- End Google Tag Manager (noscript) -->',NULL,'#515151','#000000',0,_binary '0',_binary '1',_binary '1',_binary '1',_binary '0',NULL,'#353535','#353535','#147118',50,'right','#ffffff','#515151','#353535',1,1);
/*!40000 ALTER TABLE `configuracao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configuracao_email`
--

DROP TABLE IF EXISTS `configuracao_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `configuracao_email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha2` text DEFAULT NULL,
  `smtp` varchar(255) NOT NULL,
  `porta` varchar(50) NOT NULL,
  `seguranca` binary(1) DEFAULT '0',
  `destinatario` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuracao_email`
--

LOCK TABLES `configuracao_email` WRITE;
/*!40000 ALTER TABLE `configuracao_email` DISABLE KEYS */;
INSERT INTO `configuracao_email` VALUES (1,'Website','','','mail.ydeal.net.br','587',_binary '0','');
/*!40000 ALTER TABLE `configuracao_email` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configuracao_metatags`
--

DROP TABLE IF EXISTS `configuracao_metatags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `configuracao_metatags` (
  `id` int(11) NOT NULL,
  `palavra_chave` text NOT NULL,
  `descricao` text NOT NULL,
  `frase_curta` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuracao_metatags`
--

LOCK TABLES `configuracao_metatags` WRITE;
/*!40000 ALTER TABLE `configuracao_metatags` DISABLE KEYS */;
/*!40000 ALTER TABLE `configuracao_metatags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `construction_cost_centers`
--

DROP TABLE IF EXISTS `construction_cost_centers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `construction_cost_centers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_construction` int(11) DEFAULT NULL,
  `id_cost_center` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `construction_cost_centers`
--

LOCK TABLES `construction_cost_centers` WRITE;
/*!40000 ALTER TABLE `construction_cost_centers` DISABLE KEYS */;
/*!40000 ALTER TABLE `construction_cost_centers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `construction_properties`
--

DROP TABLE IF EXISTS `construction_properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `construction_properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_construction` int(11) DEFAULT NULL,
  `id_property` int(11) DEFAULT NULL,
  `frt_value` decimal(12,2) DEFAULT NULL,
  `exchange` binary(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `construction_properties`
--

LOCK TABLES `construction_properties` WRITE;
/*!40000 ALTER TABLE `construction_properties` DISABLE KEYS */;
/*!40000 ALTER TABLE `construction_properties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `constructions`
--

DROP TABLE IF EXISTS `constructions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `constructions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `id_cost_center` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` int(11) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `constructions`
--

LOCK TABLES `constructions` WRITE;
/*!40000 ALTER TABLE `constructions` DISABLE KEYS */;
/*!40000 ALTER TABLE `constructions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contracts`
--

DROP TABLE IF EXISTS `contracts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contracts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_sale` int(11) NOT NULL,
  `id_standard_contract` int(11) NOT NULL,
  `contract_text` mediumtext NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `id_type_branch` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contracts`
--

LOCK TABLES `contracts` WRITE;
/*!40000 ALTER TABLE `contracts` DISABLE KEYS */;
/*!40000 ALTER TABLE `contracts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contracts_variables`
--

DROP TABLE IF EXISTS `contracts_variables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contracts_variables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_contract` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `value` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contracts_variables`
--

LOCK TABLES `contracts_variables` WRITE;
/*!40000 ALTER TABLE `contracts_variables` DISABLE KEYS */;
/*!40000 ALTER TABLE `contracts_variables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cost_center`
--

DROP TABLE IF EXISTS `cost_center`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cost_center` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `id_father` int(11) DEFAULT NULL,
  `status` binary(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `id_type` int(11) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cost_center`
--

LOCK TABLES `cost_center` WRITE;
/*!40000 ALTER TABLE `cost_center` DISABLE KEYS */;
/*!40000 ALTER TABLE `cost_center` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `countries` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_code` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_symbol` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `updated_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=250 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `countries`
--

LOCK TABLES `countries` WRITE;
/*!40000 ALTER TABLE `countries` DISABLE KEYS */;
INSERT INTO `countries` VALUES (1,'Reino Unido','826','GBP','British pound','£','2018-07-21 07:11:03','2022-08-18 17:39:10',1,NULL,NULL),(2,'Estados Unidos','840','USD','United States dollar','$','2018-07-21 07:11:03','2022-08-18 17:39:10',1,NULL,NULL),(3,'Ilhas Menores Distantes dos Estados Unidos','581','USD','United States dollar','$','2018-07-21 07:11:03','2022-08-18 17:39:10',1,NULL,NULL),(4,'Uruguai','858','UYU','Uruguayan peso','$','2018-07-21 07:11:03','2022-08-18 17:39:11',1,NULL,NULL),(5,'Uzbequistão','860','UZS','Uzbekistani so?m','??','2018-07-21 07:11:03','2022-08-18 17:39:11',1,NULL,NULL),(6,'Vanuatu','548','VUV','Vanuatu vatu','VT','2018-07-21 07:11:03','2022-05-22 03:39:27',1,NULL,NULL),(7,'Estado da Cidade do Vaticano (Santa Sé)','336','EUR','Euro','€','2018-07-21 07:11:03','2022-08-18 17:39:16',1,NULL,NULL),(8,'Venezuela','862','VEF','Bolívar','Bs','2018-07-21 07:11:03','2022-05-22 03:39:27',1,NULL,NULL),(9,'Vietnã','704','VND','Vietnamese d?ng','?','2018-07-21 07:11:03','2022-08-18 17:39:17',1,NULL,NULL),(10,'Ilhas Virgens (britânicas)','092','USD','United States dollar','$','2018-07-21 07:11:03','2022-08-18 17:39:17',1,NULL,NULL),(11,'Afeganistão','02','AF','Afghan','? a','2018-07-21 07:11:03','2022-08-23 20:52:45',0,43,NULL),(12,'Ilhas Aland','248','EUR','Euro','€','2018-07-21 07:11:03','2022-08-18 17:25:37',1,NULL,NULL),(13,'Albânia','008','ALL','Albanian lek','Lek','2018-07-21 07:11:03','2022-08-19 19:39:18',1,43,NULL),(14,'Argélia','012','DZD','Algerian dinar','??','2018-07-21 07:11:03','2022-08-18 17:25:37',1,NULL,NULL),(15,'Samoa Americana','016','USD','US Dollar','$','2018-07-21 07:11:03','2022-08-18 17:25:37',1,NULL,NULL),(16,'Andorra','020','EUR','Euro','€','2018-07-21 07:11:03','2022-08-18 22:18:21',1,43,NULL),(17,'Angola','024','AOA','Angolan kwanza','Kz','2018-07-21 07:11:03','2022-05-22 03:06:00',1,NULL,NULL),(18,'Anguila','660','XCD','East Caribbean dollar','$','2018-07-21 07:11:03','2022-08-18 22:16:14',1,43,NULL),(19,'Antártica','010','AAD','Antarctican dollar','$','2018-07-21 07:11:03','2022-08-18 17:25:38',1,NULL,NULL),(20,'Antígua e Barbuda','028','XCD','Eastern Caribbean dollar','$','2018-07-21 07:11:03','2022-08-18 17:25:38',1,NULL,NULL),(21,'Argentina','032','ARS','Argentine peso','$','2018-07-21 07:11:03','2022-05-22 03:06:00',1,NULL,NULL),(22,'Armênia','051','AMD','Armenian dram','?','2018-07-21 07:11:03','2022-08-18 17:25:39',1,NULL,NULL),(23,'Aruba','533','AWG','Aruban florin','ƒ','2018-07-21 07:11:03','2022-05-22 03:06:00',1,NULL,NULL),(24,'Austrália','036','AUD','Australian dollar','$','2018-07-21 07:11:03','2022-08-18 17:25:39',1,NULL,NULL),(25,'Áustria','040','EUR','Euro','€','2018-07-21 07:11:03','2022-08-18 17:25:40',1,NULL,NULL),(26,'Azerbaijão','031','AZN','Azerbaijani manat','m','2018-07-21 07:11:03','2022-08-18 17:25:40',1,NULL,NULL),(27,'As Bahamas','044','BSD','Bahamian dollar','B$','2018-07-21 07:11:03','2022-08-18 17:39:06',1,NULL,NULL),(28,'Bahrein','048','BHD','Bahraini dinar','.?.?','2018-07-21 07:11:03','2022-08-18 17:25:40',1,NULL,NULL),(29,'Bangladesh','050','BDT','Bangladeshi taka','?','2018-07-21 07:11:03','2022-05-22 03:11:20',1,NULL,NULL),(30,'Barbados','052','BBD','Barbadian dollar','Bds$','2018-07-21 07:11:03','2022-05-22 03:11:20',1,NULL,NULL),(31,'Bielorrússia','112','BYN','Belarusian ruble','Br','2018-07-21 07:11:03','2022-08-18 17:25:41',1,NULL,NULL),(32,'Bélgica','056','EUR','Euro','€','2018-07-21 07:11:03','2022-08-18 17:25:52',1,NULL,NULL),(33,'Belize','084','BZD','Belize dollar','$','2018-07-21 07:11:03','2022-05-22 03:11:20',1,NULL,NULL),(34,'Benim','204','XOF','West African CFA franc','CFA','2018-07-21 07:11:03','2022-08-18 17:25:53',1,NULL,NULL),(35,'Bermudas','060','BMD','Bermudian dollar','$','2018-07-21 07:11:03','2022-08-18 17:31:33',1,NULL,NULL),(36,'Butão','064','BTN','Bhutanese ngultrum','Nu.','2018-07-21 07:11:03','2022-08-18 17:31:33',1,NULL,NULL),(37,'Bolívia','068','BOB','Bolivian boliviano','Bs.','2018-07-21 07:11:03','2022-08-18 17:31:34',1,NULL,NULL),(38,'Bósnia e Herzegovina','070','BAM','Bosnia and Herzegovina convertible mark','KM','2018-07-21 07:11:03','2022-08-18 17:31:34',1,NULL,NULL),(39,'Botsuana','072','BWP','Botswana pula','P','2018-07-21 07:11:03','2022-08-18 17:31:35',1,NULL,NULL),(40,'Ilha Bouvet','074','NOK','Norwegian Krone','kr','2018-07-21 07:11:03','2022-08-18 17:31:35',1,NULL,NULL),(41,'Brasil','076','BRL','Brazilian real','R$','2018-07-21 07:11:03','2023-03-30 17:23:38',1,41,NULL),(42,'Território Britânico do Oceano Índico','086','USD','United States dollar','$','2018-07-21 07:11:03','2022-08-18 17:31:36',1,NULL,NULL),(43,'Brunei','096','BND','Brunei dollar','B$','2018-07-21 07:11:03','2022-05-22 03:11:20',1,NULL,NULL),(44,'Bulgária','100','BGN','Bulgarian lev','??.','2018-07-21 07:11:03','2022-08-18 17:31:36',1,NULL,NULL),(45,'Burkina Faso','854','XOF','West African CFA franc','CFA','2018-07-21 07:11:03','2022-05-22 03:11:20',1,NULL,NULL),(46,'Burundi','108','BIF','Burundian franc','FBu','2018-07-21 07:11:03','2022-05-22 03:11:20',1,NULL,NULL),(47,'Camboja','116','KHR','Cambodian riel','KHR','2018-07-21 07:11:03','2022-08-18 17:31:37',1,NULL,NULL),(48,'Camarões','120','XAF','Central African CFA franc','FCFA','2018-07-21 07:11:03','2022-08-18 17:31:37',1,NULL,NULL),(49,'Canadá','124','CAD','Canadian dollar','$','2018-07-21 07:11:03','2022-08-18 17:31:37',1,NULL,NULL),(50,'cabo Verde','132','CVE','Cape Verdean escudo','$','2018-07-21 07:11:03','2022-08-18 17:31:38',1,NULL,NULL),(51,'Ilhas Cayman','136','KYD','Cayman Islands dollar','$','2018-07-21 07:11:03','2022-08-18 17:31:38',1,NULL,NULL),(52,'República Centro-Africana','140','XAF','Central African CFA franc','FCFA','2018-07-21 07:11:03','2022-08-18 17:31:38',1,NULL,NULL),(53,'Chade','148','XAF','Central African CFA franc','FCFA','2018-07-21 07:11:03','2022-08-18 17:32:30',1,NULL,NULL),(54,'Chile','152','CLP','Chilean peso','$','2018-07-21 07:11:03','2022-05-22 03:11:20',1,NULL,NULL),(55,'China','156','CNY','Chinese yuan','¥','2018-07-21 07:11:03','2022-05-22 03:11:20',1,NULL,NULL),(56,'Ilha do Natal','162','AUD','Australian dollar','$','2018-07-21 07:11:03','2022-08-18 17:32:31',1,NULL,NULL),(57,'Cocos (Keeling) Islands','166','AUD','Australian dollar','$','2018-07-21 07:11:03','2022-05-22 03:11:20',1,NULL,NULL),(58,'Colômbia','170','COP','Colombian peso','$','2018-07-21 07:11:03','2022-08-18 17:32:35',1,NULL,NULL),(59,'Comores','174','KMF','Comorian franc','CF','2018-07-21 07:11:03','2022-08-18 17:32:35',1,NULL,NULL),(60,'Congo','178','XAF','Central African CFA franc','FC','2018-07-21 07:11:03','2022-05-22 03:11:20',1,NULL,NULL),(61,'República Democrática do Congo','180','CDF','Congolese Franc','FC','2018-07-21 07:11:03','2022-08-18 17:32:39',1,NULL,NULL),(62,'Cozinheiro das Ilhas','184','NZD','Cook Islands dollar','$','2018-07-21 07:11:03','2022-08-18 17:33:21',1,NULL,NULL),(63,'Costa Rica','188','CRC','Costa Rican colón','¢','2018-07-21 07:11:03','2022-05-22 03:13:35',1,NULL,NULL),(64,'Costa do Marfim (Costa do Marfim)','384','XOF','West African CFA franc','CFA','2018-07-21 07:11:03','2022-08-18 17:32:36',1,NULL,NULL),(65,'Croácia','191','HRK','Croatian kuna','kn','2018-07-21 07:11:03','2022-08-18 17:32:36',1,NULL,NULL),(66,'Cuba','192','CUP','Cuban peso','$','2018-07-21 07:11:03','2022-05-22 03:13:35',1,NULL,NULL),(67,'Chipre','196','EUR','Euro','€','2018-07-21 07:11:03','2022-08-18 17:32:38',1,NULL,NULL),(68,'República Checa','203','CZK','Czech koruna','Kc','2018-07-21 07:11:03','2022-08-18 17:32:38',1,NULL,NULL),(69,'Dinamarca','208','DKK','Danish krone','Kr.','2018-07-21 07:11:03','2022-08-18 17:32:39',1,NULL,NULL),(70,'Djibuti','262','DJF','Djiboutian franc','Fdj','2018-07-21 07:11:03','2022-08-18 17:32:39',1,NULL,NULL),(71,'Dominica','212','XCD','Eastern Caribbean dollar','$','2018-07-21 07:11:03','2022-05-22 03:17:53',1,NULL,NULL),(72,'República Dominicana','214','DOP','Dominican peso','$','2018-07-21 07:11:03','2022-08-18 17:32:40',1,NULL,NULL),(73,'Timor Leste','626','USD','United States dollar','$','2018-07-21 07:11:03','2022-08-18 17:32:40',1,NULL,NULL),(74,'Equador','218','USD','United States dollar','$','2018-07-21 07:11:03','2022-08-18 17:32:40',1,NULL,NULL),(75,'Egito','818','EGP','Egyptian pound','?.?','2018-07-21 07:11:03','2022-08-18 17:32:41',1,NULL,NULL),(76,'El Salvador','222','USD','United States dollar','$','2018-07-21 07:11:03','2022-05-22 03:17:53',1,NULL,NULL),(77,'Equatorial Guinea','226','XAF','Central African CFA franc','FCFA','2018-07-21 07:11:03','2022-05-22 03:17:53',1,NULL,NULL),(78,'Eritrea','232','ERN','Eritrean nakfa','Nfk','2018-07-21 07:11:03','2022-05-22 03:17:53',1,NULL,NULL),(79,'Estonia','233','EUR','Euro','€','2018-07-21 07:11:03','2022-05-22 03:17:53',1,NULL,NULL),(80,'Ethiopia','231','ETB','Ethiopian birr','Nkf','2018-07-21 07:11:03','2022-05-22 03:20:25',1,NULL,NULL),(81,'Falkland Islands','238','FKP','Falkland Islands pound','£','2018-07-21 07:11:03','2022-05-22 03:20:25',1,NULL,NULL),(82,'Ilhas Faroé','234','DKK','Danish krone','Kr.','2018-07-21 07:11:03','2022-08-18 17:33:22',1,NULL,NULL),(83,'Ilhas Fiji','242','FJD','Fijian dollar','FJ$','2018-07-21 07:11:03','2022-08-18 17:33:01',1,NULL,NULL),(84,'Finlândia','246','EUR','Euro','€','2018-07-21 07:11:03','2022-08-18 17:33:02',1,NULL,NULL),(85,'França','250','EUR','Euro','€','2018-07-21 07:11:03','2022-08-18 17:33:03',1,NULL,NULL),(86,'Guiana Francesa','254','EUR','Euro','€','2018-07-21 07:11:03','2022-08-18 17:33:04',1,NULL,NULL),(87,'Polinésia Francesa','258','XPF','CFP franc','?','2018-07-21 07:11:03','2022-08-18 17:33:04',1,NULL,NULL),(88,'Territórios do Sul da França','260','EUR','Euro','€','2018-07-21 07:11:03','2022-08-18 17:33:05',1,NULL,NULL),(89,'Gabão','266','XAF','Central African CFA franc','FCFA','2018-07-21 07:11:03','2022-08-18 17:33:05',1,NULL,NULL),(90,'Gâmbia A','270','GMD','Gambian dalasi','D','2018-07-21 07:11:03','2022-08-18 17:33:06',1,NULL,NULL),(91,'Geórgia','268','GEL','Georgian lari','?','2018-07-21 07:11:03','2022-08-18 17:33:06',1,NULL,NULL),(92,'Alemanha','276','EUR','Euro','€','2018-07-21 07:11:03','2022-08-18 17:33:08',1,NULL,NULL),(93,'Gana','288','GHS','Ghanaian cedi','GH?','2018-07-21 07:11:03','2022-08-18 17:33:09',1,NULL,NULL),(94,'Gibraltar','292','GIP','Gibraltar pound','£','2018-07-21 07:11:03','2022-05-22 03:20:25',1,NULL,NULL),(95,'Grécia','300','EUR','Euro','€','2018-07-21 07:11:03','2022-08-18 17:33:10',1,NULL,NULL),(96,'Groenlândia','304','DKK','Danish krone','Kr.','2018-07-21 07:11:03','2022-08-18 17:33:12',1,NULL,NULL),(97,'Granada','308','XCD','Eastern Caribbean dollar','$','2018-07-21 07:11:03','2022-08-18 17:33:12',1,NULL,NULL),(98,'Guadalupe','312','EUR','Euro','€','2018-07-21 07:11:03','2022-08-18 17:33:12',1,NULL,NULL),(99,'Guam','316','USD','US Dollar','$','2018-07-21 07:11:03','2022-05-22 03:20:25',1,NULL,NULL),(100,'Guatemala','320','GTQ','Guatemalan quetzal','Q','2018-07-21 07:11:03','2022-05-22 03:20:25',1,NULL,NULL),(101,'Guernsey e Alderney','831','GBP','British pound','£','2018-07-21 07:11:03','2022-08-18 17:33:14',1,NULL,NULL),(102,'Guinea','324','GNF','Guinean franc','FG','2018-07-21 07:11:03','2022-05-22 03:32:07',1,NULL,NULL),(103,'Guinea-Bissau','624','XOF','West African CFA franc','CFA','2018-07-21 07:11:03','2022-05-22 03:32:07',1,NULL,NULL),(104,'Guyana','328','GYD','Guyanese dollar','$','2018-07-21 07:11:03','2022-05-22 03:32:07',1,NULL,NULL),(105,'Haiti','332','HTG','Haitian gourde','G','2018-07-21 07:11:03','2022-05-22 03:32:07',1,NULL,NULL),(106,'Ilha Heard e Ilhas McDonald','334','AUD','Australian dollar','$','2018-07-21 07:11:03','2022-08-18 17:33:18',1,NULL,NULL),(107,'Honduras','340','HNL','Honduran lempira','L','2018-07-21 07:11:03','2022-05-22 03:32:07',1,NULL,NULL),(108,'Hong Kong S.A.R.','344','HKD','Hong Kong dollar','$','2018-07-21 07:11:03','2022-05-22 03:32:07',1,NULL,NULL),(109,'Hungria','348','HUF','Hungarian forint','Ft','2018-07-21 07:11:03','2022-08-18 17:33:20',1,NULL,NULL),(110,'Islândia','352','ISK','Icelandic króna','kr','2018-07-21 07:11:03','2022-08-18 17:33:20',1,NULL,NULL),(111,'Índia','356','INR','Indian rupee','?','2018-07-21 07:11:03','2022-08-18 17:33:22',1,NULL,NULL),(112,'Indonésia','360','IDR','Indonesian rupiah','Rp','2018-07-21 07:11:03','2022-08-18 17:33:22',1,NULL,NULL),(113,'Irã','364','IRR','Iranian rial','?','2018-07-21 07:11:03','2022-08-18 17:33:23',1,NULL,NULL),(114,'Iraque','368','IQD','Iraqi dinar','?.?','2018-07-21 07:11:03','2022-08-18 17:33:23',1,NULL,NULL),(115,'Irlanda','372','EUR','Euro','€','2018-07-21 07:11:03','2022-08-18 17:33:23',1,NULL,NULL),(116,'Israel','376','ILS','Israeli new shekel','?','2018-07-21 07:11:03','2022-05-22 03:32:07',1,NULL,NULL),(117,'Itália','380','EUR','Euro','€','2018-07-21 07:11:03','2022-08-18 17:33:23',1,NULL,NULL),(118,'Jamaica','388','JMD','Jamaican dollar','J$','2018-07-21 07:11:03','2022-05-22 03:32:07',1,NULL,NULL),(119,'Japão','392','JPY','Japanese yen','¥','2018-07-21 07:11:03','2022-08-18 17:33:28',1,NULL,NULL),(120,'Jersey','832','GBP','British pound','£','2018-07-21 07:11:03','2022-05-22 03:32:07',1,NULL,NULL),(121,'Jordânia','400','JOD','Jordanian dinar','?.?','2018-07-21 07:11:03','2022-08-18 17:33:29',1,NULL,NULL),(122,'Cazaquistão','398','KZT','Kazakhstani tenge','??','2018-07-21 07:11:03','2022-08-18 17:33:29',1,NULL,NULL),(123,'Quênia','404','KES','Kenyan shilling','KSh','2018-07-21 07:11:03','2022-08-18 17:33:29',1,NULL,NULL),(124,'Kiribati','296','AUD','Australian dollar','$','2018-07-21 07:11:03','2022-05-22 03:32:07',1,NULL,NULL),(125,'Coréia do Norte','408','KPW','North Korean Won','?','2018-07-21 07:11:03','2022-08-18 17:33:56',1,NULL,NULL),(126,'Coreia do Sul','410','KRW','Won','?','2018-07-21 07:11:03','2022-08-18 17:34:56',1,NULL,NULL),(127,'Kuwait','414','KWD','Kuwaiti dinar','?.?','2018-07-21 07:11:03','2022-05-22 03:32:07',1,NULL,NULL),(128,'Quirguistão','417','KGS','Kyrgyzstani som','??','2018-07-21 07:11:03','2022-08-18 17:33:30',1,NULL,NULL),(129,'Laos','418','LAK','Lao kip','?','2018-07-21 07:11:03','2022-05-22 03:32:07',1,NULL,NULL),(130,'Letônia','428','EUR','Euro','€','2018-07-21 07:11:03','2022-08-18 17:33:31',1,NULL,NULL),(131,'Líbano','422','LBP','Lebanese pound','£','2018-07-21 07:11:03','2022-08-18 17:33:31',1,NULL,NULL),(132,'Lesoto','426','LSL','Lesotho loti','L','2018-07-21 07:11:03','2022-08-18 17:33:31',1,NULL,NULL),(133,'Libéria','430','LRD','Liberian dollar','$','2018-07-21 07:11:03','2022-08-18 17:33:32',1,NULL,NULL),(134,'Líbia','434','LYD','Libyan dinar','?.?','2018-07-21 07:11:03','2022-08-18 17:33:34',1,NULL,NULL),(135,'Listenstaine','438','CHF','Swiss franc','CHf','2018-07-21 07:11:03','2022-08-18 17:33:34',1,NULL,NULL),(136,'Lituânia','440','EUR','Euro','€','2018-07-21 07:11:03','2022-08-18 17:33:34',1,NULL,NULL),(137,'Luxemburgo','442','EUR','Euro','€','2018-07-21 07:11:03','2022-08-18 17:33:34',1,NULL,NULL),(138,'Macau S.A.R.','446','MOP','Macanese pataca','$','2018-07-21 07:11:03','2022-05-22 03:32:07',1,NULL,NULL),(139,'Macedônia','807','MKD','Denar','???','2018-07-21 07:11:03','2022-08-18 17:33:35',1,NULL,NULL),(140,'Madagascar','450','MGA','Malagasy ariary','Ar','2018-07-21 07:11:03','2022-05-22 03:32:07',1,NULL,NULL),(141,'Malawi','454','MWK','Malawian kwacha','MK','2018-07-21 07:11:03','2022-05-22 03:32:07',1,NULL,NULL),(142,'Malásia','458','MYR','Malaysian ringgit','RM','2018-07-21 07:11:03','2022-08-18 17:33:39',1,NULL,NULL),(143,'Maldivas','462','MVR','Maldivian rufiyaa','Rf','2018-07-21 07:11:03','2022-08-18 17:33:39',1,NULL,NULL),(144,'Mali','466','XOF','West African CFA franc','CFA','2018-07-21 07:11:03','2022-05-22 03:32:07',1,NULL,NULL),(145,'Malta','470','EUR','Euro','€','2018-07-21 07:11:03','2022-05-22 03:32:07',1,NULL,NULL),(146,'Homem (Ilha de)','833','GBP','British pound','£','2018-07-21 07:11:03','2022-08-18 17:33:40',1,NULL,NULL),(147,'Ilhas Marshall','584','USD','United States dollar','$','2018-07-21 07:11:03','2022-08-18 17:33:41',1,NULL,NULL),(148,'Martinica','474','EUR','Euro','€','2018-07-21 07:11:03','2022-08-18 17:33:41',1,NULL,NULL),(149,'Mauritânia','478','MRO','Mauritanian ouguiya','MRU','2018-07-21 07:11:03','2022-08-18 17:33:41',1,NULL,NULL),(150,'Maurício','480','MUR','Mauritian rupee','?','2018-07-21 07:11:03','2022-08-18 17:33:41',1,NULL,NULL),(151,'Maiote','175','EUR','Euro','€','2018-07-21 07:11:03','2022-08-18 17:33:42',1,NULL,NULL),(152,'México','484','MXN','Mexican peso','$','2018-07-21 07:11:03','2022-08-18 17:33:42',1,NULL,NULL),(153,'Micronésia','583','USD','United States dollar','$','2018-07-21 07:11:03','2022-08-18 17:33:42',1,NULL,NULL),(154,'Moldávia','498','MDL','Moldovan leu','L','2018-07-21 07:11:03','2022-08-18 17:33:43',1,NULL,NULL),(155,'Mônaco','492','EUR','Euro','€','2018-07-21 07:11:03','2022-08-18 17:33:43',1,NULL,NULL),(156,'Mongólia','496','MNT','Mongolian tögrög','?','2018-07-21 07:11:03','2022-08-18 17:33:43',1,NULL,NULL),(157,'Montenegro','499','EUR','Euro','€','2018-07-21 07:11:03','2022-05-22 03:32:07',1,NULL,NULL),(158,'Montserrat','500','XCD','Eastern Caribbean dollar','$','2018-07-21 07:11:03','2022-05-22 03:32:07',1,NULL,NULL),(159,'Marrocos','504','MAD','Moroccan dirham','DH','2018-07-21 07:11:03','2022-08-18 17:33:45',1,NULL,NULL),(160,'Moçambique','508','MZN','Mozambican metical','MT','2018-07-21 07:11:03','2022-08-18 17:33:45',1,NULL,NULL),(161,'Mianmar','104','MMK','Burmese kyat','K','2018-07-21 07:11:03','2022-08-18 17:33:45',1,NULL,NULL),(162,'Namíbia','516','NAD','Namibian dollar','$','2018-07-21 07:11:03','2022-08-18 17:33:49',1,NULL,NULL),(163,'Nauru','520','AUD','Australian dollar','$','2018-07-21 07:11:03','2022-05-22 03:32:07',1,NULL,NULL),(164,'Nepal','524','NPR','Nepalese rupee','?','2018-07-21 07:11:03','2022-05-22 03:32:07',1,NULL,NULL),(165,'Bonaire, Santo Eustáquio e Saba','535','USD','United States dollar','$','2018-07-21 07:11:03','2022-08-18 17:31:34',1,NULL,NULL),(166,'Holanda','528','EUR','Euro','€','2018-07-21 07:11:03','2022-08-18 17:33:50',1,NULL,NULL),(167,'Nova Caledônia','540','XPF','CFP franc','?','2018-07-21 07:11:03','2022-08-18 17:33:50',1,NULL,NULL),(168,'Nova Zelândia','554','NZD','New Zealand dollar','$','2018-07-21 07:11:03','2022-08-18 17:33:52',1,NULL,NULL),(169,'Nicarágua','558','NIO','Nicaraguan córdoba','C$','2018-07-21 07:11:03','2022-08-18 17:33:53',1,NULL,NULL),(170,'Níger','562','XOF','West African CFA franc','CFA','2018-07-21 07:11:03','2022-08-18 17:33:53',1,NULL,NULL),(171,'Nigéria','566','NGN','Nigerian naira','?','2018-07-21 07:11:03','2022-08-18 17:33:53',1,NULL,NULL),(172,'Niue','570','NZD','New Zealand dollar','$','2018-07-21 07:11:03','2022-05-22 03:32:07',1,NULL,NULL),(173,'Ilha Norfolk','574','AUD','Australian dollar','$','2018-07-21 07:11:03','2022-08-18 17:33:56',1,NULL,NULL),(174,'Ilhas Marianas do Norte','580','USD','United States dollar','$','2018-07-21 07:11:03','2022-08-18 17:33:56',1,NULL,NULL),(175,'Noruega','578','NOK','Norwegian krone','kr','2018-07-21 07:11:03','2022-08-18 17:33:57',1,NULL,NULL),(176,'Omã','512','OMR','Omani rial','.?.?','2018-07-21 07:11:03','2022-08-18 17:33:57',1,NULL,NULL),(177,'Paquistão','586','PKR','Pakistani rupee','?','2018-07-21 07:11:03','2022-08-18 17:33:57',1,NULL,NULL),(178,'Palau','585','USD','United States dollar','$','2018-07-21 07:11:03','2022-05-22 03:32:07',1,NULL,NULL),(179,'Território Palestino, Ocupado','275','ILS','Israeli new shekel','?','2018-07-21 07:11:03','2022-08-18 17:33:58',1,NULL,NULL),(180,'Panamá','591','PAB','Panamanian balboa','B/.','2018-07-21 07:11:03','2022-08-18 17:33:58',1,NULL,NULL),(181,'Papua Nova Guiné','598','PGK','Papua New Guinean kina','K','2018-07-21 07:11:03','2022-08-18 17:33:58',1,NULL,NULL),(182,'Paraguay','600','PYG','Paraguayan guarani','?','2018-07-21 07:11:03','2022-05-22 03:32:07',1,NULL,NULL),(183,'Peru','604','PEN','Peruvian sol','S/.','2018-07-21 07:11:03','2022-05-22 03:32:07',1,NULL,NULL),(184,'Filipinas','608','PHP','Philippine peso','?','2018-07-21 07:11:03','2022-08-18 17:34:08',1,NULL,NULL),(185,'Ilha Pitcairn','612','NZD','New Zealand dollar','$','2018-07-21 07:11:03','2022-08-18 17:34:08',1,NULL,NULL),(186,'Polônia','616','PLN','Polish zloty','zl','2018-07-21 07:11:03','2022-08-18 17:34:08',1,NULL,NULL),(187,'Portugal','620','EUR','Euro','€','2018-07-21 07:11:03','2022-05-22 03:32:07',1,NULL,NULL),(188,'Porto Rico','630','USD','United States dollar','$','2018-07-21 07:11:03','2022-08-18 17:34:09',1,NULL,NULL),(189,'Catar','634','QAR','Qatari riyal','?.?','2018-07-21 07:11:03','2022-08-18 17:34:09',1,NULL,NULL),(190,'Reunião','638','EUR','Euro','€','2018-07-21 07:11:03','2022-08-18 17:34:14',1,NULL,NULL),(191,'Romênia','642','RON','Romanian leu','lei','2018-07-21 07:11:03','2022-08-18 17:34:15',1,NULL,NULL),(192,'Rússia','643','RUB','Russian ruble','?','2018-07-21 07:11:03','2022-08-18 17:34:15',1,NULL,NULL),(193,'Ruanda','646','RWF','Rwandan franc','FRw','2018-07-21 07:11:03','2022-08-18 17:34:15',1,NULL,NULL),(194,'Santa Helena','654','SHP','Saint Helena pound','£','2018-07-21 07:11:03','2022-08-18 17:34:16',1,NULL,NULL),(195,'São Cristóvão e Nevis','659','XCD','Eastern Caribbean dollar','$','2018-07-21 07:11:03','2022-08-18 17:34:17',1,NULL,NULL),(196,'Saint Lucia','662','XCD','Eastern Caribbean dollar','$','2018-07-21 07:11:03','2022-05-22 03:32:07',1,NULL,NULL),(197,'Saint Pierre and Miquelon','666','EUR','Euro','€','2018-07-21 07:11:03','2022-05-22 03:32:07',1,NULL,NULL),(198,'São Vicente e Granadinas','670','XCD','Eastern Caribbean dollar','$','2018-07-21 07:11:03','2022-08-18 17:34:42',1,NULL,NULL),(199,'São Bartolomeu','652','EUR','Euro','€','2018-07-21 07:11:03','2022-08-18 17:34:43',1,NULL,NULL),(200,'Saint-Martin (parte francesa)','663','EUR','Euro','€','2018-07-21 07:11:03','2022-08-18 17:34:44',1,NULL,NULL),(201,'Samoa','882','WST','Samoan tala','SAT','2018-07-21 07:11:03','2022-05-22 03:39:27',1,NULL,NULL),(202,'São Marinho','674','EUR','Euro','€','2018-07-21 07:11:03','2022-08-18 17:34:45',1,NULL,NULL),(203,'São Tomé e Príncipe','678','STD','Dobra','Db','2018-07-21 07:11:03','2022-08-18 17:34:46',1,NULL,NULL),(204,'Arábia Saudita','682','SAR','Saudi riyal','?','2018-07-21 07:11:03','2022-08-18 17:34:46',1,NULL,NULL),(205,'Senegal','686','XOF','West African CFA franc','CFA','2018-07-21 07:11:03','2022-05-22 03:39:27',1,NULL,NULL),(206,'Sérvia','688','RSD','Serbian dinar','din','2018-07-21 07:11:03','2022-08-18 17:34:47',1,NULL,NULL),(207,'Seicheles','690','SCR','Seychellois rupee','SRe','2018-07-21 07:11:03','2022-08-18 17:34:47',1,NULL,NULL),(208,'Serra Leoa','694','SLL','Sierra Leonean leone','Le','2018-07-21 07:11:03','2022-08-18 17:34:48',1,NULL,NULL),(209,'Cingapura','702','SGD','Singapore dollar','$','2018-07-21 07:11:03','2022-08-18 17:34:48',1,NULL,NULL),(210,'Eslováquia','703','EUR','Euro','€','2018-07-21 07:11:03','2022-08-18 17:34:48',1,NULL,NULL),(211,'Eslovênia','705','EUR','Euro','€','2018-07-21 07:11:03','2022-08-18 17:34:49',1,NULL,NULL),(212,'Ilhas Salomão','090','SBD','Solomon Islands dollar','Si$','2018-07-21 07:11:03','2022-08-18 17:34:49',1,NULL,NULL),(213,'Somália','706','SOS','Somali shilling','Sh.so.','2018-07-21 07:11:03','2022-08-18 17:34:50',1,NULL,NULL),(214,'Geórgia do Sul','239','GBP','British pound','£','2018-07-21 07:11:03','2022-08-18 17:34:56',1,NULL,NULL),(215,'Sudão do Sul','728','SSP','South Sudanese pound','£','2018-07-21 07:11:03','2022-08-18 17:34:56',1,NULL,NULL),(216,'Espanha','724','EUR','Euro','€','2018-07-21 07:11:03','2022-08-18 17:34:57',1,NULL,NULL),(217,'Sri Lanka','144','LKR','Sri Lankan rupee','Rs','2018-07-21 07:11:03','2022-05-22 03:39:27',1,NULL,NULL),(218,'Sudão','729','SDG','Sudanese pound','.?.?','2018-07-21 07:11:03','2022-08-18 17:34:57',1,NULL,NULL),(219,'Suriname','740','SRD','Surinamese dollar','$','2018-07-21 07:11:03','2022-05-22 03:39:27',1,NULL,NULL),(220,'Ilhas Svalbard e Jan Mayen','744','NOK','Norwegian Krone','kr','2018-07-21 07:11:03','2022-08-18 17:34:58',1,NULL,NULL),(221,'Suazilândia','748','SZL','Lilangeni','E','2018-07-21 07:11:03','2022-08-18 17:34:58',1,NULL,NULL),(222,'Suécia','752','SEK','Swedish krona','kr','2018-07-21 07:11:03','2022-08-18 17:34:58',1,NULL,NULL),(223,'Suíça','756','CHF','Swiss franc','CHf','2018-07-21 07:11:03','2022-08-18 17:34:59',1,NULL,NULL),(224,'Síria','760','SYP','Syrian pound','LS','2018-07-21 07:11:03','2022-08-18 17:35:00',1,NULL,NULL),(225,'Taiwan','158','TWD','New Taiwan dollar','$','2018-07-21 07:11:03','2022-05-22 03:39:27',1,NULL,NULL),(226,'Tajiquistão','762','TJS','Tajikistani somoni','SM','2018-07-21 07:11:03','2022-08-18 17:35:00',1,NULL,NULL),(227,'Tanzânia','834','TZS','Tanzanian shilling','TSh','2018-07-21 07:11:03','2022-08-18 17:35:01',1,NULL,NULL),(228,'Tailândia','764','THB','Thai baht','?','2018-07-21 07:11:03','2022-08-18 17:39:06',1,NULL,NULL),(229,'Ir','768','XOF','West African CFA franc','CFA','2018-07-21 07:11:03','2022-08-18 17:39:07',1,NULL,NULL),(230,'Toquelau','772','NZD','New Zealand dollar','$','2018-07-21 07:11:03','2022-08-18 17:39:07',1,NULL,NULL),(231,'Tonga','776','TOP','Tongan pa?anga','$','2018-07-21 07:11:03','2022-05-22 03:39:27',1,NULL,NULL),(232,'Trindade e Tobago','780','TTD','Trinidad and Tobago dollar','$','2018-07-21 07:11:03','2022-08-18 17:39:07',1,NULL,NULL),(233,'Tunísia','788','TND','Tunisian dinar','?.?','2018-07-21 07:11:03','2022-08-18 17:39:08',1,NULL,NULL),(234,'Peru','792','TRY','Turkish lira','?','2018-07-21 07:11:03','2022-08-18 17:39:08',1,NULL,NULL),(235,'Turcomenistão','795','TMT','Turkmenistan manat','T','2018-07-21 07:11:03','2022-08-18 17:39:08',1,NULL,NULL),(236,'Ilhas Turcas e Caicos','796','USD','United States dollar','$','2018-07-21 07:11:03','2022-08-18 17:39:08',1,NULL,NULL),(237,'Tuvalu','798','AUD','Australian dollar','$','2018-07-21 07:11:03','2022-05-22 03:39:27',1,NULL,NULL),(238,'Uganda','800','UGX','Ugandan shilling','USh','2018-07-21 07:11:03','2022-05-22 03:39:27',1,NULL,NULL),(239,'Ucrânia','804','UAH','Ukrainian hryvnia','?','2018-07-21 07:11:03','2022-08-18 17:39:09',1,NULL,NULL),(240,'Emirados Árabes Unidos','784','AED','United Arab Emirates dirham','?.?','2018-07-21 07:11:03','2022-08-18 17:39:09',1,NULL,NULL),(241,'Ilhas Virgens (EUA)','850','USD','United States dollar','$','2018-07-21 07:11:03','2022-08-18 17:39:17',1,NULL,NULL),(242,'Ilhas Wallis e Futuna','876','XPF','CFP franc','?','2018-07-21 07:11:03','2022-08-18 17:39:18',1,NULL,NULL),(243,'Saara Ocidental','732','MAD','Moroccan Dirham','MAD','2018-07-21 07:11:03','2022-08-18 17:39:18',1,NULL,NULL),(244,'Iémen','887','YER','Yemeni rial','?','2018-07-21 07:11:03','2022-08-18 17:39:18',1,NULL,NULL),(245,'Zâmbia','894','ZMW','Zambian kwacha','ZK','2018-07-21 07:11:03','2022-08-18 17:39:19',1,NULL,NULL),(246,'Zimbábue','716','ZWL','Zimbabwe Dollar','$','2018-07-21 07:11:03','2022-08-18 17:39:19',1,NULL,NULL),(247,'Kosovo','926','EUR','Euro','€','2020-08-16 02:33:50','2022-05-22 03:39:27',1,NULL,NULL),(248,'Curaçao','531','ANG','Netherlands Antillean guilder','ƒ','2020-10-26 01:54:20','2022-05-22 03:39:27',1,NULL,NULL),(249,'Sint Maarten (parte holandesa)','534','ANG','Netherlands Antillean guilder','ƒ','2020-12-06 00:03:39','2022-08-18 17:34:48',1,NULL,NULL);
/*!40000 ALTER TABLE `countries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `currencies`
--

DROP TABLE IF EXISTS `currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `currencies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency` varchar(255) NOT NULL,
  `currency_name` varchar(255) NOT NULL,
  `currency_symbol` varchar(255) DEFAULT NULL,
  `value` decimal(12,2) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `currencies`
--

LOCK TABLES `currencies` WRITE;
/*!40000 ALTER TABLE `currencies` DISABLE KEYS */;
INSERT INTO `currencies` VALUES (1,'BRL','Real Brasileiro','R$',1.00,1,'2023-07-21 19:12:30',NULL,NULL,NULL),(2,'CUB','Custo Unitário Básico','CUB',2747.90,1,'2023-07-21 19:12:30',NULL,NULL,NULL);
/*!40000 ALTER TABLE `currencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer`
--

DROP TABLE IF EXISTS `customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_person_type` int(11) DEFAULT NULL,
  `name` varchar(250) NOT NULL,
  `birth_date` date DEFAULT NULL,
  `person_registration` varchar(20) DEFAULT NULL,
  `rg` varchar(255) DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `number_address` varchar(255) DEFAULT NULL,
  `complement` text DEFAULT NULL,
  `id_country` int(11) NOT NULL,
  `uf_state` varchar(2) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `cellphone` varchar(255) DEFAULT NULL,
  `email` varchar(350) DEFAULT NULL,
  `id_profession` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `cep` varchar(11) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `neighborhood` text DEFAULT NULL,
  `id_city` int(11) DEFAULT NULL,
  `id_marital_status` int(11) DEFAULT NULL,
  `id_branch` int(11) NOT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `fancy_name_company` varchar(100) DEFAULT NULL,
  `cnpj` varchar(100) DEFAULT NULL,
  `state_registration` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `blocked` tinyint(4) NOT NULL DEFAULT 0,
  `logo` binary(1) DEFAULT '0',
  `logo_cont` int(11) DEFAULT 0,
  `logo_ext` varchar(20) DEFAULT NULL,
  `observation` text DEFAULT NULL,
  `balance` decimal(12,2) DEFAULT NULL,
  `admission_date` date DEFAULT NULL,
  `demission_date` date DEFAULT NULL,
  `experience_date` date DEFAULT NULL,
  `last_vacation_date` date DEFAULT NULL,
  `creci` varchar(100) DEFAULT NULL,
  `salary` decimal(12,2) DEFAULT NULL,
  `percentage_commission` decimal(10,2) DEFAULT NULL,
  `user_observation` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_id_person_type_IDX` (`id_person_type`) USING BTREE,
  KEY `customer_id_city_IDX` (`id_city`) USING BTREE,
  KEY `customer_id_profession_IDX` (`id_profession`) USING BTREE,
  KEY `customer_id_marital_status_IDX` (`id_marital_status`) USING BTREE,
  KEY `customer_id_branch_IDX` (`id_branch`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer`
--

LOCK TABLES `customer` WRITE;
/*!40000 ALTER TABLE `customer` DISABLE KEYS */;
INSERT INTO `customer` VALUES (1,2,'Wyllyam Neves Rozenq','2014-01-06',NULL,NULL,'Brasileiro(a)',NULL,NULL,NULL,41,'SC','4832636688','48999878706',NULL,656,1,'88200000',NULL,NULL,4579,1,1,'Ydeal Tecnologia Ltda','Ydeal Tecnologia','19471199000164',NULL,'2023-10-16 16:22:15',NULL,1,NULL,0,_binary '0',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `customer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_attachments`
--

DROP TABLE IF EXISTS `customer_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_customer` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `filename` varchar(350) DEFAULT NULL,
  `extension` varchar(20) DEFAULT NULL,
  `status` binary(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_attachments`
--

LOCK TABLES `customer_attachments` WRITE;
/*!40000 ALTER TABLE `customer_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `customer_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_balance_log`
--

DROP TABLE IF EXISTS `customer_balance_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_balance_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_customer` int(11) DEFAULT NULL,
  `amount_paid` decimal(12,2) DEFAULT NULL,
  `amount_received` decimal(12,2) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_sale` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `text` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_balance_log`
--

LOCK TABLES `customer_balance_log` WRITE;
/*!40000 ALTER TABLE `customer_balance_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `customer_balance_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_branches`
--

DROP TABLE IF EXISTS `customer_branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_branches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_customer` int(11) NOT NULL,
  `id_branch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_branches`
--

LOCK TABLES `customer_branches` WRITE;
/*!40000 ALTER TABLE `customer_branches` DISABLE KEYS */;
INSERT INTO `customer_branches` VALUES (1,1,1);
/*!40000 ALTER TABLE `customer_branches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_integration`
--

DROP TABLE IF EXISTS `customer_integration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_integration` (
  `id_customer` int(11) NOT NULL,
  `id_customer_integration` int(11) DEFAULT NULL,
  `id_integration` int(11) NOT NULL,
  `phone_customer_integration` varchar(255) DEFAULT NULL,
  UNIQUE KEY `customer_integration_un` (`id_customer`,`id_customer_integration`,`id_integration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_integration`
--

LOCK TABLES `customer_integration` WRITE;
/*!40000 ALTER TABLE `customer_integration` DISABLE KEYS */;
/*!40000 ALTER TABLE `customer_integration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_required_field`
--

DROP TABLE IF EXISTS `customer_required_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_required_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `nationality` binary(1) DEFAULT '0',
  `rg` binary(1) DEFAULT '0',
  `cpf` binary(1) DEFAULT '0',
  `cellphone` binary(1) DEFAULT '0',
  `email` binary(1) DEFAULT '0',
  `phone` binary(1) DEFAULT '0',
  `cep` binary(1) DEFAULT '0',
  `zip` binary(1) DEFAULT '0',
  `neighborhood` binary(1) DEFAULT '0',
  `address` binary(1) DEFAULT '0',
  `number_address` binary(1) DEFAULT '0',
  `complement` binary(1) DEFAULT '0',
  `birth_date` binary(1) DEFAULT '0',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `branches` binary(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_required_field`
--

LOCK TABLES `customer_required_field` WRITE;
/*!40000 ALTER TABLE `customer_required_field` DISABLE KEYS */;
INSERT INTO `customer_required_field` VALUES (1,'customer',_binary '0',_binary '0',_binary '0',_binary '1',_binary '0',_binary '0',_binary '0',_binary '0',_binary '0',_binary '0',_binary '0',_binary '0',_binary '1','2023-06-23 14:17:48',NULL,_binary '1'),(2,'spouse',_binary '0',_binary '0',_binary '0',_binary '0',_binary '0',_binary '0',_binary '0',_binary '0',_binary '0',_binary '0',_binary '0',_binary '0',_binary '0','2023-06-23 14:17:48',NULL,_binary '1');
/*!40000 ALTER TABLE `customer_required_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_type`
--

DROP TABLE IF EXISTS `customer_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` binary(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `disableable` binary(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_type`
--

LOCK TABLES `customer_type` WRITE;
/*!40000 ALTER TABLE `customer_type` DISABLE KEYS */;
INSERT INTO `customer_type` VALUES (8,'Comprador',_binary '1',NULL,NULL,NULL,NULL,_binary '0'),(9,'Proprietário Final',_binary '1',NULL,NULL,NULL,NULL,_binary '0'),(10,'Fornecedor',_binary '1',NULL,NULL,NULL,NULL,_binary '0'),(11,'Construtora',_binary '1',NULL,NULL,NULL,NULL,_binary '0'),(12,'Colaborador',_binary '1',NULL,NULL,NULL,NULL,_binary '0');
/*!40000 ALTER TABLE `customer_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dashboard`
--

DROP TABLE IF EXISTS `dashboard`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dashboard` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dashboard`
--

LOCK TABLES `dashboard` WRITE;
/*!40000 ALTER TABLE `dashboard` DISABLE KEYS */;
INSERT INTO `dashboard` VALUES (1,'Atendimentos'),(2,'Recadastro Imóveis'),(3,'Últimas Vendas'),(4,'Custo Operacional'),(5,'Últimos Pagamentos'),(6,'Atividade Leads');
/*!40000 ALTER TABLE `dashboard` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dashboard_order`
--

DROP TABLE IF EXISTS `dashboard_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dashboard_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_dashboard` int(11) NOT NULL,
  `order_by` int(11) NOT NULL,
  `status` binary(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dashboard_order`
--

LOCK TABLES `dashboard_order` WRITE;
/*!40000 ALTER TABLE `dashboard_order` DISABLE KEYS */;
INSERT INTO `dashboard_order` VALUES (1,1,1,1,_binary '1'),(2,1,2,2,_binary '1'),(3,1,3,3,_binary '1'),(4,1,4,4,_binary '1'),(5,1,5,5,_binary '1'),(6,1,6,6,_binary '1');
/*!40000 ALTER TABLE `dashboard_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `depoimento`
--

DROP TABLE IF EXISTS `depoimento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `depoimento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slugify` text DEFAULT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `ativo` binary(1) DEFAULT '1',
  `cont` int(11) DEFAULT 1,
  `capa` binary(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `depoimento`
--

LOCK TABLES `depoimento` WRITE;
/*!40000 ALTER TABLE `depoimento` DISABLE KEYS */;
/*!40000 ALTER TABLE `depoimento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `digital_card`
--

DROP TABLE IF EXISTS `digital_card`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `digital_card` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `cor_fundo` varchar(20) DEFAULT '#62aeec',
  `cor_fonte_usuario` varchar(20) DEFAULT '#ffffff',
  `cor_fonte_ocupacao` varchar(20) DEFAULT '#ffffff',
  `cor_fonte_creci` varchar(20) DEFAULT '#ffffff',
  `cor_fonte_imobiliaria` varchar(20) DEFAULT '#ffffff',
  `cor_fonte_legenda` varchar(20) DEFAULT '#ffffff',
  `cor_borda_usuario` varchar(20) DEFAULT '#000000',
  `cor_fundo_logo` varchar(20) DEFAULT '#ffffff',
  `cor_borda_icone` varchar(20) DEFAULT '#ffffff',
  `fonte_usuario` varchar(20) DEFAULT 'gotham',
  `tamanho_fonte_usuario` int(11) DEFAULT 100,
  `fonte_ocupacao` varchar(20) DEFAULT 'gotham',
  `tamanho_fonte_ocupacao` int(11) DEFAULT 63,
  `fonte_creci` varchar(20) DEFAULT 'gotham',
  `tamanho_fonte_creci` int(11) DEFAULT 37,
  `fonte_imobiliaria` varchar(20) DEFAULT 'gotham',
  `tamanho_fonte_imobiliaria` int(11) DEFAULT 100,
  `fonte_legenda` varchar(20) DEFAULT 'gotham',
  `tamanho_fonte_legenda` int(11) DEFAULT 50,
  `capa_fundo` binary(1) DEFAULT '0',
  `cont_fundo` int(11) DEFAULT 1,
  `ext_fundo` varchar(20) DEFAULT NULL,
  `capa_logo` binary(1) DEFAULT '0',
  `cont_logo` int(11) DEFAULT 1,
  `ext_logo` varchar(20) DEFAULT NULL,
  `status` bigint(20) unsigned DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `digital_card`
--

LOCK TABLES `digital_card` WRITE;
/*!40000 ALTER TABLE `digital_card` DISABLE KEYS */;
INSERT INTO `digital_card` VALUES (1,'Cartão Padrão','#3c8dbc','#ffffff','#ffffff','#ffffff','#ffffff','#ffffff','#000000','#ffffff','#ffffff','gotham',100,'gotham',63,'gotham',37,'gotham',100,'gotham',50,_binary '0',1,NULL,_binary '0',1,'',1,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `digital_card` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `displayed_properties`
--

DROP TABLE IF EXISTS `displayed_properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `displayed_properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_attendance` int(11) DEFAULT NULL,
  `id_product` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `code` text DEFAULT NULL,
  `status_code` binary(1) DEFAULT '0',
  `status` binary(1) DEFAULT '1',
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `ip_user` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `displayed_properties`
--

LOCK TABLES `displayed_properties` WRITE;
/*!40000 ALTER TABLE `displayed_properties` DISABLE KEYS */;
/*!40000 ALTER TABLE `displayed_properties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `form_of_payment`
--

DROP TABLE IF EXISTS `form_of_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `form_of_payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `form_payment_sale` binary(1) DEFAULT '0',
  `restricted` binary(1) DEFAULT '0',
  `form_payment_accounts_payable` binary(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `form_of_payment`
--

LOCK TABLES `form_of_payment` WRITE;
/*!40000 ALTER TABLE `form_of_payment` DISABLE KEYS */;
INSERT INTO `form_of_payment` VALUES (1,'Boleto',1,'2023-06-22 14:53:02',NULL,NULL,NULL,_binary '1',_binary '1',_binary '1'),(2,'Transferência Bancária',1,'2023-06-22 14:53:02',NULL,NULL,NULL,_binary '1',_binary '1',_binary '1'),(3,'Cheque',1,'2023-06-22 14:53:02',NULL,NULL,NULL,_binary '1',_binary '1',_binary '1'),(4,'Financiamento',1,'2023-06-22 14:53:02',NULL,NULL,NULL,_binary '1',_binary '1',_binary '0'),(5,'Pix',1,'2023-06-22 14:53:02',NULL,NULL,NULL,NULL,NULL,_binary '1'),(6,'Dinheiro',1,'2023-06-22 14:53:02',NULL,NULL,NULL,NULL,NULL,_binary '1'),(7,'Cartão de Crédito',1,'2023-06-22 14:53:02',NULL,NULL,NULL,NULL,NULL,_binary '1'),(8,'Cartão de Débito',1,'2023-06-22 14:53:02',NULL,NULL,NULL,NULL,NULL,_binary '1');
/*!40000 ALTER TABLE `form_of_payment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `home_category_link`
--

DROP TABLE IF EXISTS `home_category_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `home_category_link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_home` int(11) DEFAULT 1,
  `id_property_category` int(11) DEFAULT NULL,
  `status` binary(1) DEFAULT '1',
  `item_order` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `home_category_link`
--

LOCK TABLES `home_category_link` WRITE;
/*!40000 ALTER TABLE `home_category_link` DISABLE KEYS */;
INSERT INTO `home_category_link` VALUES (1,1,1,_binary '1',1,NULL,NULL),(2,1,2,_binary '1',2,NULL,NULL),(3,1,3,_binary '1',3,NULL,NULL),(4,1,4,_binary '1',4,NULL,NULL),(5,1,5,_binary '1',5,NULL,NULL);
/*!40000 ALTER TABLE `home_category_link` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `imagem`
--

DROP TABLE IF EXISTS `imagem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `imagem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) DEFAULT NULL,
  `link` text DEFAULT NULL,
  `ativo` binary(1) DEFAULT '1',
  `ordem` int(11) DEFAULT NULL,
  `cont` int(11) DEFAULT 1,
  `capa` binary(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `imagem`
--

LOCK TABLES `imagem` WRITE;
/*!40000 ALTER TABLE `imagem` DISABLE KEYS */;
/*!40000 ALTER TABLE `imagem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `immovable_resource`
--

DROP TABLE IF EXISTS `immovable_resource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `immovable_resource` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `filter` binary(1) DEFAULT '0',
  `status` binary(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `slugify` varchar(255) DEFAULT NULL,
  `cont` int(11) DEFAULT 1,
  `capa` binary(1) DEFAULT '0',
  `ext` varchar(20) DEFAULT NULL,
  `site_filter` binary(1) DEFAULT '0',
  `data_type` int(11) DEFAULT 1,
  `alias` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `immovable_resource`
--

LOCK TABLES `immovable_resource` WRITE;
/*!40000 ALTER TABLE `immovable_resource` DISABLE KEYS */;
INSERT INTO `immovable_resource` VALUES (7,'Alarme',_binary '0',_binary '1','2023-06-22 14:54:42',NULL,0,NULL,'alarme',NULL,_binary '\0',NULL,_binary '0',1,NULL),(9,'Área de Serviço',_binary '0',_binary '1','2023-06-22 14:54:42',NULL,0,NULL,'rea-de-servio',NULL,_binary '\0',NULL,_binary '0',1,NULL),(11,'Banheiros',_binary '1',_binary '1','2023-06-22 14:54:42',NULL,0,NULL,'banheiros',1,_binary '1','png',_binary '1',1,NULL),(12,'Cachoeira',_binary '0',_binary '1','2023-06-22 14:54:42',NULL,0,NULL,'cachoeira',NULL,_binary '\0',NULL,_binary '0',1,NULL),(13,'Canil',_binary '0',_binary '1','2023-06-22 14:54:42',NULL,0,NULL,'canil',NULL,_binary '\0',NULL,_binary '0',1,NULL),(14,'Cerca Elétrica',_binary '0',_binary '1','2023-06-22 14:54:42',NULL,0,NULL,'cerca-eltrica',NULL,_binary '\0',NULL,_binary '0',1,NULL),(15,'Churrasqueira',_binary '0',_binary '1','2023-06-22 14:54:42',NULL,0,NULL,'churrasqueira',NULL,_binary '\0',NULL,_binary '0',1,NULL),(16,'Cozinha',_binary '0',_binary '1','2023-06-22 14:54:42',NULL,0,NULL,'cozinha',NULL,_binary '\0',NULL,_binary '0',1,NULL),(17,'Despensa',_binary '0',_binary '1','2023-06-22 14:54:42',NULL,0,NULL,'despensa',NULL,_binary '\0',NULL,_binary '0',1,NULL),(18,'Elevador',_binary '0',_binary '1','2023-06-22 14:54:42',NULL,0,NULL,'elevador',NULL,_binary '\0',NULL,_binary '0',1,NULL),(20,'Garagem Coberta',_binary '0',_binary '1','2023-06-22 14:54:42',NULL,0,NULL,'garagem-coberta',1,_binary '1','png',_binary '0',1,NULL),(21,'Guarita',_binary '0',_binary '1','2023-06-22 14:54:42',NULL,0,NULL,'guarita',NULL,_binary '\0',NULL,_binary '0',1,NULL),(22,'Hidromassagem',_binary '0',_binary '1','2023-06-22 14:54:42',NULL,0,NULL,'hidromassagem',NULL,_binary '\0',NULL,_binary '0',1,NULL),(23,'Interfone',_binary '0',_binary '1','2023-06-22 14:54:42',NULL,0,NULL,'interfone',NULL,_binary '\0',NULL,_binary '0',1,NULL),(24,'Jardim',_binary '0',_binary '1','2023-06-22 14:54:42',NULL,0,NULL,'jardim',NULL,_binary '\0',NULL,_binary '0',1,NULL),(26,'Lagoa',_binary '0',_binary '1','2023-06-22 14:54:42',NULL,0,NULL,'lagoa',NULL,_binary '\0',NULL,_binary '0',1,NULL),(27,'Lavabo',_binary '0',_binary '1','2023-06-22 14:54:42',NULL,0,NULL,'lavabo',NULL,_binary '\0',NULL,_binary '0',1,NULL),(28,'Mezanino',_binary '0',_binary '1','2023-06-22 14:54:42',NULL,0,NULL,'mezanino',NULL,_binary '\0',NULL,_binary '0',1,NULL),(29,'Mobília',_binary '0',_binary '1','2023-06-22 14:54:42',NULL,0,NULL,'moblia',NULL,_binary '\0',NULL,_binary '0',1,NULL),(30,'Pavimentação',_binary '0',_binary '1','2023-06-22 14:54:42',NULL,0,NULL,'pavimentao',NULL,_binary '\0',NULL,_binary '0',1,NULL),(31,'Piscina',_binary '0',_binary '1','2023-06-22 14:54:42',NULL,0,NULL,'piscina',NULL,_binary '\0',NULL,_binary '0',1,NULL),(32,'Portão Eletrônico',_binary '0',_binary '1','2023-06-22 14:54:42',NULL,0,NULL,'porto-eletrnico',NULL,_binary '\0',NULL,_binary '0',1,NULL),(33,'Quarto',_binary '1',_binary '1','2023-06-22 14:54:42',NULL,0,NULL,'quarto',1,_binary '1','png',_binary '1',1,NULL),(34,'Sacada',_binary '0',_binary '1','2023-06-22 14:54:42',NULL,0,NULL,'sacada',NULL,_binary '\0',NULL,_binary '0',1,NULL),(35,'Sala',_binary '0',_binary '1','2023-06-22 14:54:42',NULL,0,NULL,'sala',NULL,_binary '\0',NULL,_binary '0',1,NULL),(36,'Salão De Festas',_binary '0',_binary '1','2023-06-22 14:54:42',NULL,0,NULL,'salo-de-festas',NULL,_binary '\0',NULL,_binary '0',1,NULL),(38,'Suite',_binary '1',_binary '1','2023-06-22 14:54:42',NULL,0,NULL,'suite',1,_binary '1','png',_binary '1',1,NULL),(39,'Garagens',_binary '1',_binary '1','2023-06-22 14:54:42',NULL,0,NULL,'garagens',1,_binary '1','png',_binary '1',1,NULL);
/*!40000 ALTER TABLE `immovable_resource` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `integrations`
--

DROP TABLE IF EXISTS `integrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `integrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `status` binary(1) NOT NULL DEFAULT '',
  `token` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `integrations`
--

LOCK TABLES `integrations` WRITE;
/*!40000 ALTER TABLE `integrations` DISABLE KEYS */;
INSERT INTO `integrations` VALUES (1,'Facebook',_binary '1',''),(2,'DWV',_binary '0','');
/*!40000 ALTER TABLE `integrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead`
--

DROP TABLE IF EXISTS `lead`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_communication_channel` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` binary(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `id_product` int(11) DEFAULT NULL,
  `id_attendance` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead`
--

LOCK TABLES `lead` WRITE;
/*!40000 ALTER TABLE `lead` DISABLE KEYS */;
/*!40000 ALTER TABLE `lead` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead_config`
--

DROP TABLE IF EXISTS `lead_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `distribution_type` int(11) DEFAULT NULL,
  `status` binary(1) DEFAULT '1',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead_config`
--

LOCK TABLES `lead_config` WRITE;
/*!40000 ALTER TABLE `lead_config` DISABLE KEYS */;
INSERT INTO `lead_config` VALUES (1,3,_binary '1','2023-06-23 17:32:11',NULL);
/*!40000 ALTER TABLE `lead_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead_random`
--

DROP TABLE IF EXISTS `lead_random`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_random` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `attendance` binary(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead_random`
--

LOCK TABLES `lead_random` WRITE;
/*!40000 ALTER TABLE `lead_random` DISABLE KEYS */;
/*!40000 ALTER TABLE `lead_random` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lead_working_date`
--

DROP TABLE IF EXISTS `lead_working_date`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_working_date` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date_start` datetime DEFAULT NULL,
  `date_end` datetime DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `all_day` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lead_working_date`
--

LOCK TABLES `lead_working_date` WRITE;
/*!40000 ALTER TABLE `lead_working_date` DISABLE KEYS */;
/*!40000 ALTER TABLE `lead_working_date` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manager_team`
--

DROP TABLE IF EXISTS `manager_team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manager_team` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_manager` int(11) DEFAULT NULL,
  `id_seller` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manager_team`
--

LOCK TABLES `manager_team` WRITE;
/*!40000 ALTER TABLE `manager_team` DISABLE KEYS */;
/*!40000 ALTER TABLE `manager_team` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marital_status`
--

DROP TABLE IF EXISTS `marital_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marital_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `spouse` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marital_status`
--

LOCK TABLES `marital_status` WRITE;
/*!40000 ALTER TABLE `marital_status` DISABLE KEYS */;
INSERT INTO `marital_status` VALUES (1,'SOLTEIRO',1,0),(2,'CASADO',1,1),(3,'DIVORCIADO',1,0);
/*!40000 ALTER TABLE `marital_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL DEFAULT '',
  `item_order` int(11) NOT NULL DEFAULT 1,
  `route` varchar(256) NOT NULL DEFAULT '#',
  `icon` varchar(256) NOT NULL DEFAULT 'fa',
  `access` int(11) NOT NULL DEFAULT 30,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `id_menu_parent` int(11) DEFAULT NULL,
  `get` text DEFAULT NULL,
  `type_branch` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu`
--

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES (3,'Configurações',6,'#','fa fa-cogs',5,1,NULL,NULL,NULL),(4,'Home',1,'home','fa fa-home',30,1,NULL,NULL,NULL),(5,'Cadastros',3,'#','fa fa-pencil-alt',10,1,NULL,NULL,NULL),(6,'Usuários',6,'users','fa fa-users',10,1,5,NULL,NULL),(7,'Filiais',4,'branch','fa fa-home',5,1,5,NULL,NULL),(9,'Profissões',1,'professions','fa fa-user-tie',10,1,76,NULL,NULL),(10,'Estado Civil',9,'marital-status','fa fa-ring',10,0,5,NULL,NULL),(11,'Status Venda',1,'status','fa fa-stream',5,1,80,NULL,NULL),(12,'Formas Pagamento',4,'form-of-payment','fa fa-money-bill-wave',5,1,77,NULL,NULL),(14,'Contas',3,'bank-accounts','fa fa-landmark',10,1,77,NULL,NULL),(15,'Bancos',1,'banks','fa fa-university',5,1,77,NULL,NULL),(17,'Contrato Padrão',1,'standard-contract','fa fa-file-contract',5,1,5,NULL,NULL),(18,'Vendas',2,'sales','fa fa-tags',30,1,79,NULL,NULL),(19,'Cliente',2,'customer','fa fa-users',30,1,24,'?customer_type_in%5B%5D=8',NULL),(20,'Todos Imóveis',3,'property','fa fa-building',30,1,23,NULL,NULL),(21,'Tipo Imóvel',5,'property-type','fa fa-cubes',10,1,78,NULL,NULL),(22,'Características Imóvel',1,'immovable-resource','fa fa-paint-roller',10,1,78,NULL,NULL),(23,'Imóveis',8,'#','fa fa-laptop-house',30,1,NULL,NULL,NULL),(24,'Clientes',4,'#','fa fa-users',30,1,NULL,NULL,NULL),(25,'Construtora',4,'customer','fa fa-hard-hat',30,1,24,'?customer_type_in%5B%5D=11',NULL),(26,'Proprietário',6,'customer','fa fa-users',30,1,24,'?customer_type_in%5B%5D=9',NULL),(27,'Categoria Imóvel',2,'property-category','fa fa-list',10,1,78,NULL,NULL),(28,'Meus Imóveis',2,'property','fa fa-house-user',30,1,23,'?id_user_owner=me',NULL),(29,'Atendimentos',2,'#','fa fa-comments',30,1,NULL,NULL,NULL),(30,'Calendário',4,'calendar','fa fa-calendar-alt',30,1,29,NULL,NULL),(32,'Atendimentos',3,'attendance','fa fa-comments',30,1,29,NULL,NULL),(33,'Status Atendimento',2,'attendance-status','fa fa-headphones',10,1,75,NULL,NULL),(34,'Canais Comunicação',1,'communication-channels','fa fa-assistive-listening-systems',10,1,75,NULL,NULL),(35,'Transferir Usuário',4,'change-link','fa fa-exchange-alt',10,1,3,NULL,NULL),(36,'Site',11,'#','fa fa-globe',10,1,NULL,NULL,NULL),(37,'Banners',1,'banners-site','fa fa-image',10,1,36,NULL,NULL),(38,'Configurações',2,'settings-site','fa fa-cogs',10,1,36,NULL,NULL),(39,'Depoimentos',3,'depositions-site','fa fa-digital-tachograph',10,0,36,NULL,NULL),(40,'Imagens',4,'images-site','fa fa-images',10,0,36,NULL,NULL),(41,'Textos',7,'texts-site','fa fa-pen-alt',10,1,36,NULL,NULL),(42,'Rede Sociais',6,'networks-site','fa fa-satellite',10,1,36,NULL,NULL),(43,'Novos Leads',2,'lead','fa fa-magnet',25,1,45,NULL,NULL),(44,'Configurações',1,'lead-config','fa fa-cog',25,1,45,NULL,NULL),(45,'Leads',9,'#','fa fa-magnet',25,1,NULL,NULL,NULL),(46,'Praia dos Sonhos',5,'praia-sonhos-site','fa fa-umbrella-beach',10,1,36,NULL,NULL),(47,'Cartão Digital',3,'digital-card','fa fa-id-badge',10,1,5,NULL,NULL),(48,'Landing-page',4,'presentation-card','fa fa-pager',5,1,5,NULL,NULL),(49,'Marca d\' água',4,'water-mark','fa fa-mask',10,1,78,NULL,NULL),(50,'Campos Obrigatórios',2,'required-field-settings','fa fa-star-of-life',5,1,3,NULL,NULL),(51,'Configuração',3,'settings','fa fa-cog',5,1,3,NULL,NULL),(52,'Classificação',3,'property-classification','fa fa-star',10,1,78,NULL,NULL),(53,'Financeiro',7,'#','fa fa-dollar-sign',30,1,NULL,NULL,NULL),(54,'Contas Receber',4,'bill-receive-installment','fa fa-money-check-alt',10,1,53,NULL,NULL),(55,'Contas Pagar',3,'bills-to-pay-installment','fa fa-money-check',10,1,53,NULL,NULL),(56,'Centro Custo',2,'cost-center','fa fa-sitemap',10,1,77,NULL,NULL),(57,'Avisos',1,'notice','fa fa-exclamation-triangle',1,0,3,NULL,NULL),(59,'Relatório DRE',10,'report-dre','fa fa-receipt',5,1,NULL,NULL,NULL),(60,'Fornecedor',5,'customer','fa fa-dolly',30,1,24,'?customer_type_in%5B%5D=10',NULL),(61,'Lançamentos Pagar',5,'bills-to-pay','fa  fa-clipboard',10,1,53,NULL,NULL),(62,'Lançamentos Receber',6,'bill-receive','fa  fa-clipboard-list',10,1,53,NULL,NULL),(63,'Contas Pagar',1,'record-bills-to-pay-installment','fa fa-clipboard',10,1,82,NULL,NULL),(64,'Todos',7,'customer','fa fa-users',30,1,24,NULL,NULL),(65,'Cargos',2,'user-position','fa fa-user-astronaut',10,1,5,NULL,NULL),(66,'Atendimentos',1,'report-attendance','fa fa-comments',10,1,81,NULL,NULL),(67,'Tipo de cliente',2,'customer-type','fas fa-people-arrows',10,1,76,NULL,NULL),(69,'Colaborador',3,'customer','fa fa-dolly',30,1,24,'?customer_type_in%5B%5D=12',NULL),(70,'Obras',13,'construction','fa fa-building',10,1,NULL,NULL,2),(72,'Países',5,'countries','fa fa-globe',5,1,5,NULL,NULL),(73,'Contas Receber',1,'record-bill-receive-installment','fa fa-clipboard',10,1,82,NULL,NULL),(74,'Moedas',5,'currencies','fa fa-coins',5,1,77,NULL,NULL),(75,'# Cadastros',1,'#','fa fa-pencil-alt',30,1,29,NULL,NULL),(76,'# Cadastros',1,'#','fa fa-pencil-alt',30,1,24,NULL,NULL),(77,'# Cadastros',1,'#','fa fa-pencil-alt',30,1,53,NULL,NULL),(78,'# Cadastros',1,'#','fa fa-pencil-alt',30,1,23,NULL,NULL),(79,'Vendas',12,'#','fa fa-tags',30,1,NULL,NULL,NULL),(80,'# Cadastros',1,'#','fa fa-pencil-alt',30,1,79,NULL,NULL),(81,'# Relatórios',2,'#','fa fa-clipboard',30,1,29,NULL,NULL),(82,'# Relatórios',2,'#','fa fa-clipboard',30,1,53,NULL,NULL),(83,'Integrações',10,'#','fas fa-sync',30,1,NULL,NULL,NULL),(84,'Configurações',1,'integration-config','fa fa-cogs',30,1,83,NULL,NULL),(85,'Imóveis',2,'integration-dwv','fa fa-building',30,1,83,NULL,NULL),(89,'# Relatórios',2,'#','fa fa-clipboard',30,1,79,NULL,NULL),(90,'Vendas',1,'report-sales','fa fa-tags',30,1,83,NULL,NULL);
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_access`
--

DROP TABLE IF EXISTS `menu_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_menu` int(11) DEFAULT NULL,
  `id_profile` int(11) DEFAULT NULL,
  `status` binary(1) DEFAULT NULL,
  `updated_by` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `menu_access_un` (`id_menu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_access`
--

LOCK TABLES `menu_access` WRITE;
/*!40000 ALTER TABLE `menu_access` DISABLE KEYS */;
/*!40000 ALTER TABLE `menu_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `network_card_digital`
--

DROP TABLE IF EXISTS `network_card_digital`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `network_card_digital` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `status` binary(1) DEFAULT '1',
  `filename` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `network_card_digital`
--

LOCK TABLES `network_card_digital` WRITE;
/*!40000 ALTER TABLE `network_card_digital` DISABLE KEYS */;
INSERT INTO `network_card_digital` VALUES (1,'Facebook',_binary '1','facebook'),(2,'Instagram',_binary '1','instagram'),(3,'Email',_binary '1','email'),(4,'WhatsApp',_binary '1','whatsapp'),(5,'Linkedln',_binary '1','linkedln'),(6,'Twitter',_binary '1','twitter');
/*!40000 ALTER TABLE `network_card_digital` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notice`
--

DROP TABLE IF EXISTS `notice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` text DEFAULT NULL,
  `status` binary(1) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `box` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notice`
--

LOCK TABLES `notice` WRITE;
/*!40000 ALTER TABLE `notice` DISABLE KEYS */;
INSERT INTO `notice` VALUES (1,'<p>Estamos em manuten&ccedil;&atilde;o aguarde!</p>\r\n',NULL,'Aviso','box-danger',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `notice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification`
--

DROP TABLE IF EXISTS `notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `icon` varchar(255) NOT NULL DEFAULT '''fa''',
  `route` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `id_branch` int(11) DEFAULT NULL,
  `status` binary(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification`
--

LOCK TABLES `notification` WRITE;
/*!40000 ALTER TABLE `notification` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_group`
--

DROP TABLE IF EXISTS `notification_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `icon_text_color` varchar(100) DEFAULT NULL,
  `status` binary(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_group`
--

LOCK TABLES `notification_group` WRITE;
/*!40000 ALTER TABLE `notification_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_read`
--

DROP TABLE IF EXISTS `notification_read`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_read` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_notification` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `message_read` binary(1) DEFAULT '0',
  `seen_at` timestamp NULL DEFAULT NULL,
  `intended_user` binary(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_read`
--

LOCK TABLES `notification_read` WRITE;
/*!40000 ALTER TABLE `notification_read` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification_read` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_user`
--

DROP TABLE IF EXISTS `notification_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_notification` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `read` binary(1) DEFAULT NULL,
  `read_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_user`
--

LOCK TABLES `notification_user` WRITE;
/*!40000 ALTER TABLE `notification_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_status`
--

DROP TABLE IF EXISTS `payment_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `status` binary(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_status`
--

LOCK TABLES `payment_status` WRITE;
/*!40000 ALTER TABLE `payment_status` DISABLE KEYS */;
INSERT INTO `payment_status` VALUES (1,'Aguardando Pagamento',_binary '1','2021-10-20 16:03:16',NULL,NULL,NULL),(2,'Pago',_binary '1','2021-10-20 16:03:16',NULL,NULL,NULL),(3,'Cancelado',_binary '1','2021-10-20 16:03:16',NULL,NULL,NULL);
/*!40000 ALTER TABLE `payment_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments_of_sales`
--

DROP TABLE IF EXISTS `payments_of_sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments_of_sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_form_of_payment` int(11) DEFAULT NULL,
  `id_sale` int(11) DEFAULT NULL,
  `id_bank_finance` int(11) DEFAULT NULL,
  `id_account` int(11) DEFAULT NULL,
  `value` decimal(12,2) DEFAULT NULL,
  `status_portion` int(11) DEFAULT NULL,
  `portion_number` int(11) NOT NULL,
  `due_date` date DEFAULT NULL,
  `number_paymentOrder` varchar(100) DEFAULT NULL,
  `own_paymentOrder` binary(1) DEFAULT NULL,
  `owner_paymentOrder` varchar(255) DEFAULT NULL,
  `cpfcnpj_paymentOrder` varchar(50) DEFAULT NULL,
  `bank_paymentOrder` int(11) DEFAULT NULL,
  `agency_paymentOrder` varchar(100) DEFAULT NULL,
  `number_account_paymentOrder` varchar(100) DEFAULT NULL,
  `observation` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` date DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `status` binary(1) DEFAULT '1',
  `id_bill_receive_installment` int(11) DEFAULT NULL,
  `amount_paid` decimal(12,2) DEFAULT NULL,
  `pay_day` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `type_of_payment` int(11) DEFAULT 1,
  `id_property` int(11) DEFAULT NULL,
  `vehicle` varchar(255) DEFAULT NULL,
  `license_plate` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments_of_sales`
--

LOCK TABLES `payments_of_sales` WRITE;
/*!40000 ALTER TABLE `payments_of_sales` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments_of_sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_type`
--

DROP TABLE IF EXISTS `person_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `person_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` binary(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person_type`
--

LOCK TABLES `person_type` WRITE;
/*!40000 ALTER TABLE `person_type` DISABLE KEYS */;
INSERT INTO `person_type` VALUES (1,'FÍSICA',_binary '1'),(2,'JURÍDICA',_binary '1');
/*!40000 ALTER TABLE `person_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `praia_sonho`
--

DROP TABLE IF EXISTS `praia_sonho`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `praia_sonho` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cidade` int(11) NOT NULL,
  `slugify` blob DEFAULT NULL,
  `nome` blob DEFAULT NULL,
  `ativo` binary(1) DEFAULT '1',
  `capa` binary(1) DEFAULT '0',
  `cont` int(11) DEFAULT 0,
  `ext` varchar(20) DEFAULT NULL,
  `capa2` binary(1) DEFAULT '0',
  `cont2` int(11) DEFAULT 0,
  `ext2` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `praia_sonho`
--

LOCK TABLES `praia_sonho` WRITE;
/*!40000 ALTER TABLE `praia_sonho` DISABLE KEYS */;
/*!40000 ALTER TABLE `praia_sonho` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presentation_card`
--

DROP TABLE IF EXISTS `presentation_card`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presentation_card` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `status` binary(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `cor_contato_fundo` varchar(20) NOT NULL DEFAULT '#5babea',
  `cor_contato_fonte` varchar(20) NOT NULL DEFAULT '#ffffff',
  `cor_contato_btn_fundo` varchar(20) NOT NULL DEFAULT '#3c8dbc',
  `cor_contato_btn_fonte` varchar(20) NOT NULL DEFAULT '#ffffff',
  `cor_rodape_copyright_fundo` varchar(20) NOT NULL DEFAULT '#3c8dbc',
  `cor_rodape_copyright_fonte` varchar(20) NOT NULL DEFAULT '#cecece',
  `cor_contato_btn_border` varchar(20) NOT NULL DEFAULT '#3c8dbc',
  `cor_contato_btn_fonte_efeito` varchar(20) NOT NULL DEFAULT '#ffffff',
  `cor_rodape_copyright_link_fonte` varchar(20) NOT NULL DEFAULT '#ffffff',
  `message` text DEFAULT NULL,
  `show_payment` binary(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presentation_card`
--

LOCK TABLES `presentation_card` WRITE;
/*!40000 ALTER TABLE `presentation_card` DISABLE KEYS */;
INSERT INTO `presentation_card` VALUES (1,'Cartão Padrão',_binary '1',NULL,NULL,NULL,NULL,'#3c8dbc','#ffffff','#3c8dbc','#ffffff','#3c8dbc','#cecece','#3c8dbc','#ffffff','#ffffff',NULL,_binary '1');
/*!40000 ALTER TABLE `presentation_card` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presentation_config`
--

DROP TABLE IF EXISTS `presentation_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presentation_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template` int(11) DEFAULT 1,
  `show_characteristics` tinyint(4) DEFAULT 1,
  `show_pictures` tinyint(4) DEFAULT 1,
  `show_payments` tinyint(4) DEFAULT 1,
  `show_value` tinyint(4) DEFAULT NULL,
  `show_description` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presentation_config`
--

LOCK TABLES `presentation_config` WRITE;
/*!40000 ALTER TABLE `presentation_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `presentation_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presentations`
--

DROP TABLE IF EXISTS `presentations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presentations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `IP` varchar(100) DEFAULT NULL,
  `id_displayed_properties` int(11) DEFAULT NULL,
  `status` binary(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presentations`
--

LOCK TABLES `presentations` WRITE;
/*!40000 ALTER TABLE `presentations` DISABLE KEYS */;
/*!40000 ALTER TABLE `presentations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_ownership_feature`
--

DROP TABLE IF EXISTS `product_ownership_feature`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_ownership_feature` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_product` int(11) DEFAULT NULL,
  `id_property_type_resources` int(11) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `slugify` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_ownership_feature_id_product_IDX` (`id_product`) USING BTREE,
  KEY `product_ownership_feature_id_property_type_resources_IDX` (`id_property_type_resources`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_ownership_feature`
--

LOCK TABLES `product_ownership_feature` WRITE;
/*!40000 ALTER TABLE `product_ownership_feature` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_ownership_feature` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_branch` int(11) DEFAULT NULL,
  `id_residential_type` int(11) DEFAULT NULL,
  `id_property_category` int(11) DEFAULT NULL,
  `id_city` int(11) DEFAULT NULL,
  `id_owner` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `uf_state` varchar(2) DEFAULT NULL,
  `neighborhood` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `number` varchar(255) DEFAULT NULL,
  `complement` varchar(255) DEFAULT NULL,
  `total_area` decimal(10,2) DEFAULT NULL,
  `value` decimal(12,2) DEFAULT NULL,
  `lat` decimal(25,15) DEFAULT NULL,
  `lng` decimal(25,15) DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` date NOT NULL,
  `updated_at` date DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `cod` varchar(100) DEFAULT NULL,
  `installment_value` decimal(12,2) DEFAULT NULL,
  `condition_product` varchar(255) DEFAULT NULL,
  `re_registered_at` date DEFAULT NULL,
  `site_name` varchar(255) DEFAULT NULL,
  `site_complement` varchar(500) DEFAULT NULL,
  `site_value` decimal(12,2) DEFAULT NULL,
  `site_status` binary(1) DEFAULT '0',
  `site_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `site_contrast` binary(1) DEFAULT '0',
  `cont_thumb` int(11) DEFAULT 1,
  `ext_thumb` varchar(15) DEFAULT NULL,
  `capa_thumb` binary(1) DEFAULT '0',
  `site_schedule` text DEFAULT NULL,
  `slugify` varchar(255) DEFAULT NULL,
  `url` varchar(350) DEFAULT NULL,
  `id_property_classification` int(11) DEFAULT 0,
  `allotment` varchar(255) DEFAULT NULL,
  `site_name_complement` varchar(255) DEFAULT NULL,
  `id_user_owner` int(11) DEFAULT NULL,
  `synced_integration` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_cod_IDX` (`cod`) USING BTREE,
  KEY `products_id_branch_IDX` (`id_branch`) USING BTREE,
  KEY `products_id_residential_type_IDX` (`id_residential_type`) USING BTREE,
  KEY `products_id_property_category_IDX` (`id_property_category`) USING BTREE,
  KEY `products_id_city_IDX` (`id_city`) USING BTREE,
  KEY `products_id_owner_IDX` (`id_owner`) USING BTREE,
  KEY `products_created_by_IDX` (`created_by`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products_attachments`
--

DROP TABLE IF EXISTS `products_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_product` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) DEFAULT NULL,
  `filename` varchar(255) NOT NULL,
  `extension` varchar(8) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `item_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_attachments_id_product_IDX` (`id_product`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products_attachments`
--

LOCK TABLES `products_attachments` WRITE;
/*!40000 ALTER TABLE `products_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `products_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products_imgs`
--

DROP TABLE IF EXISTS `products_imgs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products_imgs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_product` int(11) NOT NULL DEFAULT 0,
  `filename` varchar(255) DEFAULT NULL,
  `extension` varchar(15) NOT NULL DEFAULT 'png',
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `item_order` int(11) DEFAULT NULL,
  `status_site` binary(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `products_imgs_id_product_IDX` (`id_product`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products_imgs`
--

LOCK TABLES `products_imgs` WRITE;
/*!40000 ALTER TABLE `products_imgs` DISABLE KEYS */;
/*!40000 ALTER TABLE `products_imgs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products_imgs_progress`
--

DROP TABLE IF EXISTS `products_imgs_progress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products_imgs_progress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_product` int(11) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `extension` varchar(15) NOT NULL,
  `item_order` int(11) DEFAULT NULL,
  `status_site` binary(1) DEFAULT '0',
  `date_progress` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_imgs_progress_id_product_IDX` (`id_product`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products_imgs_progress`
--

LOCK TABLES `products_imgs_progress` WRITE;
/*!40000 ALTER TABLE `products_imgs_progress` DISABLE KEYS */;
/*!40000 ALTER TABLE `products_imgs_progress` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products_pricelist`
--

DROP TABLE IF EXISTS `products_pricelist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products_pricelist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_product` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `extension` varchar(8) NOT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `item_order` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products_pricelist`
--

LOCK TABLES `products_pricelist` WRITE;
/*!40000 ALTER TABLE `products_pricelist` DISABLE KEYS */;
/*!40000 ALTER TABLE `products_pricelist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products_site_view`
--

DROP TABLE IF EXISTS `products_site_view`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products_site_view` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_product` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_site_view_id_product_IDX` (`id_product`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products_site_view`
--

LOCK TABLES `products_site_view` WRITE;
/*!40000 ALTER TABLE `products_site_view` DISABLE KEYS */;
/*!40000 ALTER TABLE `products_site_view` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products_videos`
--

DROP TABLE IF EXISTS `products_videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products_videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `link_videos` varchar(500) DEFAULT NULL,
  `id_product` int(11) DEFAULT NULL,
  `item_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_videos_id_product_IDX` (`id_product`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products_videos`
--

LOCK TABLES `products_videos` WRITE;
/*!40000 ALTER TABLE `products_videos` DISABLE KEYS */;
/*!40000 ALTER TABLE `products_videos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `professions`
--

DROP TABLE IF EXISTS `professions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `professions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=773 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `professions`
--

LOCK TABLES `professions` WRITE;
/*!40000 ALTER TABLE `professions` DISABLE KEYS */;
INSERT INTO `professions` VALUES (1,'INVESTIGADOR PARTICULAR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(2,'GESTOR PÚBLICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(3,'GINASTA ARTÍSTICA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(4,'GINASTA RÍTMICA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(5,'GINECOLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(6,'GOURMET',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(7,'GOVERNADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(8,'GOVERNANTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(9,'GRAFOLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(10,'GRAVURISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(11,'GUARDA OU POLICIAL RODOVIÁRIO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(12,'GUARDA ROUPEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(13,'GUARDADOR DE VEÍCULOS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(14,'GUIA TURISTICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(15,'GUINCHEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(16,'GUITARRISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(17,'HARPISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(18,'HEADHUNTER',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(19,'HEMATOLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(20,'HISTORIADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(21,'HOMEOPATA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(22,'HOSTESS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(23,'ILUSTRADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(24,'IMPLANTODONTISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(25,'IMPRESSOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(26,'IMUNOLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(27,'INFECTOLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(28,'INSPETOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(29,'INSTALADOR DE LINHA TELEFÔNICA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(30,'INSTALADOR DE PAINÉIS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(31,'INSTRUMENTADOR CIRÚRGICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(32,'INSTRUMENTISTA MUSICAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(33,'INSTRUTOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(34,'INTÉRPRETE',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(35,'INTÉRPRETE DE BÍBLIAS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(36,'INTÉRPRETE E TRADUTOR DE LÍNGUA DE SINAIS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(37,'INVESTIGADOR DE POLÍCIA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(38,'JANGADEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(39,'JARDINEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(40,'JOGADOR DE BADMINTON',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(41,'JOGADOR DE BASQUETE',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(42,'JOGADOR DE BOCHA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(43,'JOGADOR DE BOLICHE',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(44,'JOGADOR DE FUTEBOL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(45,'JOGADOR DE GOLFE',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(46,'JOGADOR DE HANDEBOL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(47,'JOGADOR DE HÓQUEI',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(48,'JOGADOR DE TÊNIS DE MESA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(49,'JOGADOR DE VÔLEI',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(50,'JÓQUEI',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(51,'JORNALEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(52,'JORNALISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(53,'JUDOCA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(54,'JUIZ DE DIREITO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(55,'JUIZ DE FUTEBOL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(56,'JUIZ OU ÁRBITRO DE FUTEBOL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(57,'KARATECA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(58,'KITE-SURFER',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(59,'LABORATORISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(60,'LACTARISTA HOSPITALAR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(61,'LAMBOTEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(62,'LANCHEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(63,'LANTERNEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(64,'LAPIDÁRIO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(65,'LAVADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(66,'LAVADOR DE VEÍCULOS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(67,'LEÃO DE CHÁCARA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(68,'LEILOEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(69,'LEITEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(70,'LENHADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(71,'LETRISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(72,'LEVANTADOR DE PESO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(73,'LÍDER COMUNITÁRIO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(74,'LIMPADOR DE VIDROS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(75,'LIXEIRO/COLETOR DE LIXO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(76,'LOCUTOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(77,'LUBRIFICADOR DE MÁQUINAS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(78,'LUTADOR DE JIU-JITSU',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(79,'LUTADOR DE KARATÊ',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(80,'LUTADOR DE KUNG FU',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(81,'LUTADOR DE LUTA LIVRE',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(82,'LUTADOR DE TAEKWONDO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(83,'LUTHIER',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(84,'MÃE SOCIAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(85,'MAESTRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(86,'MÁGICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(87,'MAITRE',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(88,'MAJOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(89,'MANICURE',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(90,'MANOBRISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(91,'MAQUIADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(92,'MAQUINISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(93,'MARCADOR DE LUZ',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(94,'MARCENEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(95,'MARECHAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(96,'MARINHEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(97,'MARKETEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(98,'MASSAGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(99,'MASSOTERAPEUTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(100,'MATEMÁTICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(101,'MECÂNICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(102,'MECÂNICO DE VÔO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(103,'MECANÓGRAFO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(104,'MÉDICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(105,'MÉDICO CIRURGIÃO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(106,'MÉDICO GENETICISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(107,'MÉDICO LEGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(108,'MÉDICO NUCLEAR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(109,'MÉDICO CARDIOLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(110,'MÉDICO PEDIATRA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(111,'MÉDICO CLINICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(112,'MÉDICO OFTALMOLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(113,'MÉDICO DO TRABALHO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(114,'MEEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(115,'MENSAGEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(116,'MEREDEIRA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(117,'MERGULHADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(118,'MESTRE CERVEJEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(119,'MESTRE-DE-OBRAS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(120,'METALÚRGICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(121,'METEOROLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(122,'MICROFONISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(123,'MILITAR DA AERONÁUTICA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(124,'MILITAR DA MARINHA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(125,'MILITAR DO EXÉRCITO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(126,'MINISTRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(127,'MODELISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(128,'MODELO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(129,'MOLDADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(130,'MOLDUREIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(131,'MOLEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(132,'MONTADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(133,'MONTADOR DE NEGATIVOS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(134,'MOTOFRETE',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(135,'MOTORISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(136,'MOTOTAXISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(137,'MUSEÓLOGO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(138,'MÚSICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(139,'MUSICOTERAPEUTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(140,'NADADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(141,'NATURÓLOGO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(142,'NAVEGADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(143,'NECROMAQUIADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(144,'NEFROLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(145,'NEONATOLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(146,'NEUROCIRURGIÃO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(147,'NEUROLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(148,'NOTÁRIO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(149,'NUMERÓLOGO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(150,'NUTRICIONISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(151,'NUTROLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(152,'OBSTETRA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(153,'OCEANÓGRAFO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(154,'OCULISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(155,'ODONTOLOGISTA ESTÉTICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(156,'ODONTOLOGISTA LEGAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(157,'ODONTOLOGISTA PREVENTIVO E SOCIAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(158,'ODONTOPEDIATRA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(159,'OFFICE-BOY',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(160,'OFICIAL DE JUSTIÇA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(161,'OFTALMOLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(162,'OMBUDSMAN',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(163,'OPERADOR DE BOMBAS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(164,'OPERADOR DE TELEMARKETING',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(165,'OPERADOR DE VÍDEO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(166,'OPTOMETRISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(167,'ORÇAMENTISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(168,'ORIENTADOR EDUCACIONAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(169,'ORTESISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(170,'ORTODONTISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(171,'ORTOPEDISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(172,'ORTOPTISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(173,'OTORRINOLARINGOLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(174,'OURIVES',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(175,'PAGINADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(176,'PAISAGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(177,'PANFLETEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(178,'PANIFICADOR/PADEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(179,'PAPARAZZO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(180,'PAPILOSCOPISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(181,'PÁRA-QUEDISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(182,'PARAMÉDICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(183,'PARTEIRA TRADICIONAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(184,'PASSADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(185,'PASTILHEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(186,'PATINADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(187,'PATOLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(188,'PATOLOGISTA ORAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(189,'PEÃO DE RODEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(190,'PECUARISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(191,'PEDAGOGO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(192,'PEDIATRA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(193,'PEDICURE',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(194,'PEDREIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(195,'PEIXEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(196,'PENHORISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(197,'PERCURSIONISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(198,'PERFUMISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(199,'PERFUSIONISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(200,'PERITO CRIMINAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(201,'PERITO JUDICIAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(202,'PERSONAL STYLIST',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(203,'PERSONAL TRAINER',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(204,'PESCADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(205,'PESQUISADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(206,'PETROLEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(207,'PIANISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(208,'PILOTO AUTOMOBILÍSTICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(209,'PILOTO DE AVIÃO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(210,'PINTOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(211,'PIZZAIOLO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(212,'PLASTIMODELISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(213,'PNEUMOLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(214,'PODÓLOGO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(215,'POLICIAL CIVIL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(216,'POLICIAL FEDERAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(217,'POLICIAL MILITAR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(218,'POLIDOR DE PRODUÇÃO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(219,'POLÍTICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(220,'PORTEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(221,'PORTUÁRIO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(222,'PRÁTICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(223,'PREFEITO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(224,'PRENSISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(225,'PREPARADOR DE MÁQUINAS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(226,'PRESIDENTE DA REPÚBLICA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(227,'PROCURADOR DE JUSTIÇA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(228,'PRODUTOR DE AUDIO VISUAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(229,'PRODUTOR DE EVENTOS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(230,'PRODUTOR DE MULTIMÍDIA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(231,'PRODUTOR EDITORIAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(232,'PRODUTOR FONOGRÁFICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(233,'PRODUTOR MUSICAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(234,'PROFESSOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(235,'PROFISSIONAL DE ÁUDIO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(236,'PROFISSIONAL DE CINEMA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(237,'PROFISSIONAL DE COMÉRCIO EXTERIOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(238,'PROFISSIONAL DE EDUCAÇÃO FÍSICA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(239,'PROFISSIONAL DE EFEITOS ESPECIAIS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(240,'PROFISSIONAL DE HOTELARIA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(241,'PROFISSIONAL DE INFORMÁTICA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(242,'PROFISSIONAL DE LINGUÍSTICA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(243,'PROFISSIONAL DE LOGÍSTICA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(244,'PROFISSIONAL DE MANUTENÇÃO INDUSTRIAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(245,'PROFISSIONAL DE MARKETING',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(246,'PROFISSIONAL DE RÁDIO E TV',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(247,'PROFISSIONAL DE RECICLAGEM',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(248,'PROFISSIONAL DE RECURSOS HUMANOS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(249,'PROFISSIONAL DE RELACIONAMENTO COM INVESTIDORES RI',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(250,'PROFISSIONAL DE RELAÇÕES INTERNACIONAIS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(251,'PROFISSIONAL DE RELAÇÕES PÚBLICAS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(252,'PROFISSIONAL DE TECNOLOGIA DE LATICÍNIOS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(253,'PROGRAMADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(254,'PROGRAMADOR DE COMPUTADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(255,'PROGRAMADORPHP',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(256,'PROGRAMADOR JAVA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(257,'PROGRAMADOR C#',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(258,'PROGRAMADOR .NET',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(259,'PROGRAMADOR ASP',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(260,'PROGRAMADOR VB',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(261,'PROGRAMADOR PASCAL - DELPHI',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(262,'PROGRAMADORJAVASCRIPT',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(263,'PROGRAMADOR HTML 5',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(264,'PROGRAMADOR COBOL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(265,'PROGRAMADOR C++',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(266,'PROJETISTA MECÂNICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(267,'PROMOTOR DE EVENTOS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(268,'PROMOTOR DE VENDAS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(269,'PROMOTOR PÚBLICO/DE JUSTIÇA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(270,'PROTESISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(271,'PROTÉTICO DENTÁRIO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(272,'PSICÓLOGO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(273,'PSICOMOTRICISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(274,'PSICOPEDAGOGO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(275,'PSIQUIATRA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(276,'PUBLICITÁRIO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(277,'QUADRINISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(278,'QUÍMICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(279,'QUÍMICO FARMACÊUTICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(280,'QUIROPRAXISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(281,'QUITANDEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(282,'RADIALISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(283,'RADIALISTA PROGRAMADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(284,'RADIOLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(285,'RADIOOPERADOR DE VÔO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(286,'RADIOTERAPÊUTICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(287,'REBARBADOR DE METAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(288,'RECEPCIONISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(289,'RECREADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(290,'REDATOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(291,'REGENTE',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(292,'RELAÇÕES PÚBLICAS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(293,'REMADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(294,'REPÓRTER',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(295,'REPOSITOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(296,'REPRESENTANTE COMERCIAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(297,'RESTAURADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(298,'RETIFICADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(299,'REUMATOLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(300,'REVENDEDOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(301,'REVISOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(302,'ROTEIRISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(303,'SACOLEIRA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(304,'SALGADEIRA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(305,'SALVA-VIDAS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(306,'SAPATEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(307,'SARGENTO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(308,'SAXOFONISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(309,'SECRETÁRIA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(310,'SEGUIDOR DE COMPRAS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(311,'SEGURANÇA PARTICULAR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(312,'SELECIONADOR DE PESSOAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(313,'SENADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(314,'SEPARADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(315,'SERINGUEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(316,'SERRALHEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(317,'SERVENTE-DE-OBRAS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(318,'SERVENTUÁRIO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(319,'SEXÓLOGO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(320,'SÍNDICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(321,'SKATISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(322,'SOCIÓLOGO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(323,'SOLDADO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(324,'SOLDADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(325,'SOMELIER',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(326,'SONOPLASTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(327,'SUBPREFEITO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(328,'SUPERVISOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(329,'SURFISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(330,'SUSHIMAN',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(331,'TABELIÃO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(332,'TAIFEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(333,'TAPECEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(334,'TATUADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(335,'TAXIDERMISTA/EMBALSAMADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(336,'TAXISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(337,'TECELÃO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(338,'TÉCNICO DE GESSO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(339,'TÉCNICO DE SOM',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(340,'TÉCNICO EM AGROPECUÁRIA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(341,'TÉCNICO EM ARQUIVO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(342,'TÉCNICO EM AVIAÇÃO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(343,'TÉCNICO EM DESPORTO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(344,'TÉCNICO EM DOCUMENTAÇÃO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(345,'TÉCNICO EM EDIFICAÇÕES',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(346,'TÉCNICO EM HARDWARE',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(347,'TÉCNICO EM HIGIENE DENTÁRIA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(348,'TÉCNICO EM ÓPTICA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(349,'TÉCNICO EM RADIOLOGIA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(350,'TÉCNICO EM REDE',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(351,'TÉCNICO EM SEGURANÇA DO TRABALHO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(352,'TÉCNICO EM TAQUIGRAFIA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(353,'TÉCNICO EM TRATAMENTO DE ÁGUA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(354,'TÉCNICO TRIBUTARISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(355,'TECNÓLOGO EM AUTOMAÇÃO INDUSTRIAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(356,'TECNÓLOGO EM CIÊNCIAS DAS PLANTAS MEDICINAIS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(357,'TECNÓLOGO EM DESENVOLVIMENTO SOCIAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(358,'TECNÓLOGO EM ESPORTE E LAZER',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(359,'TECNÓLOGO EM GEOPROCESSAMENTO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(360,'TECNÓLOGO EM IRRIGAÇÃO E DRENAGEM',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(361,'TECNÓLOGO EM JOGOS DIGITAIS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(362,'TECNÓLOGO EM NAVEGAÇÃO FLUVIAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(363,'TECNÓLOGO EM NEGÓCIOS IMOBILIÁRIOS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(364,'TECNÓLOGO EM PAPEL E CELULOSE',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(365,'TECNÓLOGO EM PROCESSOS QUÍMICOS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(366,'TECNÓLOGO EM PRODUÇÃO DE BEBIDAS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(367,'TECNÓLOGO EM PRODUÇÃO MOVELEIRA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(368,'TECNÓLOGO EM PRODUÇÃO SUCROALCOOLEIRA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(369,'TECNÓLOGO EM RECURSOS PESQUEIROS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(370,'TECNÓLOGO EM ROCHAS ORNAMENTAIS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(371,'TECNÓLOGO EM SISTEMAS DA INFORMAÇÃO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(372,'TECNÓLOGO EM SISTEMAS PARA INTERNET',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(373,'TECNÓLOGO EM SILVICULTURA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(374,'TECNÓLOGO EM TECNOLOGIA DA MADEIRA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(375,'TELEFONISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(376,'TELEGRAFISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(377,'TENENTE',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(378,'TENISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(379,'TEÓLOGO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(380,'TERAPEUTA FLORAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(381,'TERAPEUTA HOLÍSTICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(382,'TERAPEUTA OCUPACIONAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(383,'TESOUREIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(384,'TIMONEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(385,'TINTUREIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(386,'TOPÓGRAFO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(387,'TORNEIRO MECÂNICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(388,'TORREIRO/TORRISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(389,'TOSADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(390,'TOXICOLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(391,'TRADUTOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(392,'TRANSCRITOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(393,'TRANSPORTADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(394,'TRAUMATOLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(395,'TREINADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(396,'TRIATLETA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(397,'TRILHEIRO OU MÚSICO DE CINEMA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(398,'TROMPETISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(399,'TURISMÓLOGO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(400,'UFÓLOGO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(401,'URBANISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(402,'UROLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(403,'VELEJADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(404,'VENDEDOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(405,'VENTRÍLOQUO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(406,'VEREADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(407,'VETERINÁRIO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(408,'VIGIA PARLAMENTAR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(409,'VIGILANTE NOTURNO/DIURNO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(410,'VIOLONISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(411,'VISTORIADOR DE SINISTROS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(412,'VIVEIRISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(413,'WEBDESIGNER',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(414,'WEBMASTER',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(415,'WINDSURFER',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(416,'XILÓGRAFO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(417,'ZELADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(418,'ZOOTECNISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(419,'ACOMPANHANTE',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(420,'AÇOUGUEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(421,'ACUPUNTURISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(422,'ADESTRADOR DE ANIMAIS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(423,'ADMINISTRADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(424,'ADMINISTRADOR DE BANCO DE DADOS DBA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(425,'ADMINISTRADOR DE REDES',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(426,'ADMINISTRADOR PÚBLICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(427,'ADVOGADO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(428,'AEROMOÇA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(429,'AERONAUTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(430,'AEROVIÁRIO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(431,'AFIADOR DE FERRAMENTAS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(432,'AGENTE COMUNITÁRIO DE SAÚDE',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(433,'AGENTE DE COMBATE À ENDEMIAS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(434,'AGENTE DE DEFESA SANITÁRIA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(435,'AGENTE DE DEFESA SANITÁRIA ANIMAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(436,'AGENTE DE VIAGENS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(437,'AGENTE FUNERÁRIO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(438,'AGENTE PENITENCIÁRIO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(439,'AGRICULTOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(440,'AGRIMENSOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(441,'AGRÔNOMO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(442,'AJUDANTE DE PRODUÇÃO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(443,'ALERGOLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(444,'ALFAIATE',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(445,'ALMIRANTE',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(446,'ALMOXARIFE',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(447,'ALPINISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(448,'AMBIENTALISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(449,'AMBULANTE',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(450,'AMOLADOR DE FERRAMENTAS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(451,'ANALISTA DE SISTEMAS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(452,'ANESTESIOLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(453,'ANGIOLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(454,'ANTROPÓLOGO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(455,'APICULTOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(456,'APONTADOR DE MÃO-DE-OBRA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(457,'APRESENTADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(458,'ÁRBITRO E MEDIADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(459,'ARGUMENTISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(460,'ARMADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(461,'ARMEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(462,'ARQUEÓLOGO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(463,'ARQUITETO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(464,'ARQUIVISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(465,'ARRANJADOR MUSICAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(466,'ARRUMADEIRA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(467,'ARTESÃO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(468,'ARTISTA DE CIRCO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(469,'ARTISTA PLÁSTICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(470,'ARTISTA/TÉCNICO EM ESPETÁCULOS DE DIVERSÕES',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(471,'ASCENSORISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(472,'ASSESSOR DE IMPRENSA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(473,'ASSESSOR PARLAMENTAR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(474,'ASSISTENTE ADMINISTRATIVO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(475,'ASSISTENTE DE CÂMERA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(476,'ASSISTENTE DE DIREÇÃO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(477,'ASSISTENTE DE PRODUÇÃO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(478,'ASSISTENTE SOCIAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(479,'ASTROFÍSICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(480,'ASTRÓLOGO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(481,'ASTRONAUTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(482,'ASTRÔNOMO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(483,'ATENDENTE',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(484,'ATLETA DE ARREMESSO DE PESO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(485,'ATLETA DE CANOAGEM',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(486,'ATLETA DE NADO SINCRONIZADO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(487,'ATLETA DE TIRO COM ARCO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(488,'ATOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(489,'ATUÁRIO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(490,'AUDITOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(491,'AUXILIAR ADMINISTRATIVO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(492,'AUXILIAR DE REPROGRAFIA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(493,'AUXILIAR DE SERVIÇOS GERAIS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(494,'AVALISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(495,'AVIADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(496,'BABÁ',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(497,'BABYSITTER',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(498,'BAILARINA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(499,'BAIXISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(500,'BALCONISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(501,'BANCÁRIO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(502,'BARBEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(503,'BARMAN',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(504,'BARTENDER',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(505,'BATERISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(506,'BEDEL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(507,'BERÇARISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(508,'BIBLIOTECÁRIO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(509,'BIBLIOTECONOMISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(510,'BIÓLOGO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(511,'BIOMÉDICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(512,'BIOQUÍMICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(513,'BIOTECNÓLOGO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(514,'BÓIA-FRIA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(515,'BOMBEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(516,'BORRACHEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(517,'BOTICÁRIO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(518,'BOXEADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(519,'BRIGADEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(520,'BROKER/CORRETOR DA BOLSA DE VALORES',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(521,'CABELEIREIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(522,'CABO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(523,'CAÇA-TALENTOS/OLHEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(524,'CADEIRINHA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(525,'CADISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(526,'CAIXA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(527,'CALDEIREIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(528,'CAMBISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(529,'CAMELÔ',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(530,'CAMERAMAN',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(531,'CAMINHONEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(532,'CANCEROLOGISTA OU ONCOLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(533,'CANTOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(534,'CAPATAZ',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(535,'CAPELÃO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(536,'CAPITÃO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(537,'CAPOEIRISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(538,'CARDIOLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(539,'CARNAVALESCO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(540,'CARPINTEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(541,'CARTAZEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(542,'CARTEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(543,'CARTÓGRAFO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(544,'CARTUNISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(545,'CATADOR DE CARANGUEIJOS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(546,'CATADOR DE MATERIAL RECICLÁVEL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(547,'CENÓGRAFO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(548,'CENOTÉCNICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(549,'CERAMISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(550,'CERIMONIALISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(551,'CHAPEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(552,'CHARGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(553,'CHAVEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(554,'CHEFE DE COZINHA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(555,'CICLISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(556,'CIENTISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(557,'CIENTISTA DA INFORMAÇÃO E DOCUMENTAÇÃO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(558,'CIENTISTA DE ALIMENTOS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(559,'CIENTISTA POLÍTICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(560,'CIENTISTA SOCIAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(561,'CINEASTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(562,'CINEGRAFISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(563,'CINEMATOGRÁFO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(564,'CIRURGIÃO BUCAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(565,'CIRURGIÃO DENTISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(566,'CLAP LOADER',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(567,'CLARINETISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(568,'CLASSIFICADOR CONTÁBIL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(569,'CLÍNICO GERAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(570,'CO-PILOTO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(571,'COACH',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(572,'COBAIA MÉDICA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(573,'COBRADOR DE ÔNIBUS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(574,'COBRADOR DE PEDÁGIO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(575,'COLOPROCTOLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(576,'COMANDANTE',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(577,'COMERCIANTE',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(578,'COMISSÁRIO DE BORDO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(579,'COMPOSITOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(580,'COMPRADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(581,'CONFEITEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(582,'CONFERENTE DE CARGA E DESCARGA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(583,'CONFERENTE DE EXPEDIÇÃO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(584,'CONFERENTE DE RECEBIMENTO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(585,'CONSTRUTOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(586,'CONSULTOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(587,'CONSULTOR DE MODA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(588,'CONSULTOR DE RADIESTESIA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(589,'CONTÁBIL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(590,'CONTABILISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(591,'CONTADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(592,'CONTATÓLOGO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(593,'CONTINUISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(594,'CONTRA REGRA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(595,'CONTRAMESTRE EM TRANSPORTE MARÍTIMO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(596,'CONTROLADOR DE VÔO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(597,'CONTROLLER',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(598,'COORDENADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(599,'COPEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(600,'COREÓGRAFO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(601,'CORONEL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(602,'CORREDOR DE ATLETISMO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(603,'CORREGEDOR DE JUSTIÇA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(604,'CORRETOR DA BOLSA DE VALORES',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(605,'CORRETOR DE IMÓVEIS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(606,'CORRETOR DE SEGUROS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(607,'CORTADOR DE CANA-DE-AÇUCAR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(608,'COSTUREIRA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(609,'COVEIRO/SEPULTADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(610,'COZINHEIRA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(611,'CRÍTICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(612,'CUMIM',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(613,'DANÇARINO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(614,'DATILÓGRAFO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(615,'DEDETIZADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(616,'DEFENSOR PÚBLICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(617,'DEGUSTADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(618,'DELEGADO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(619,'DENTISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(620,'DEPUTADO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(621,'DERMATOLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(622,'DESEMBARGADOR DE JUSTIÇA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(623,'DESENHISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(624,'DESIGNER DE INTERIORES',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(625,'DESIGNER DE JÓIA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(626,'DESIGNER DE MODA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(627,'DESIGNER DE PRODUTO OU DESENHISTA INDUSTRIAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(628,'DESIGNER GRÁFICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(629,'DESPACHANTE',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(630,'DIAGRAMADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(631,'DIALOGUISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(632,'DIARISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(633,'DIGITADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(634,'DIPLOMATA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(635,'DIRETOR DE CINEMA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(636,'DIRETOR DE FOTOGRAFIA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(637,'DIRETOR DE PRODUÇÃO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(638,'DJ',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(639,'DOGUEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(640,'DUBLADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(641,'DUBLÊ',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(642,'ECÓLOGO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(643,'ECONOMISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(644,'ECONOMISTA DOMÉSTICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(645,'EDITOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(646,'EDITOR DE MESA DE CORTE',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(647,'EDUCADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(648,'EDUCADOR INTEGRADO À SAÚDE PÚBLICA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(649,'ELETRICISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(650,'ELETRICISTA DE AUTOMÓVEIS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(651,'EMBAIXADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(652,'EMBALADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(653,'EMBALSAMADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(654,'EMPACOTADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(655,'EMPREGADO DOMÉSTICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(656,'EMPRESÁRIO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(657,'ENCANADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(658,'ENCARREGADO DE MANUTENÇÃO PREDIAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(659,'ENDOCRINOLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(660,'ENDODONTISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(661,'ENFERMEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(662,'ENGENHEIRO ACÚSTICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(663,'ENGENHEIRO AERONÁUTICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(664,'ENGENHEIRO AGRÍCOLA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(665,'ENGENHEIRO AGRIMENSOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(666,'ENGENHEIRO AGRÔNOMO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(667,'ENGENHEIRO AMBIENTAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(668,'ENGENHEIRO CARTOGRÁFICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(669,'ENGENHEIRO CIVIL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(670,'ENGENHEIRO DE ALIMENTOS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(671,'ENGENHEIRO DE AQUICULTURA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(672,'ENGENHEIRO DE COMPUTAÇÃO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(673,'ENGENHEIRO DE CONTROLE E AUTOMAÇÃO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(674,'ENGENHEIRO DE ENERGIA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(675,'ENGENHEIRO DE ERGONOMIA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(676,'ENGENHEIRO DE HORTICULTURA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(677,'ENGENHEIRO DE ILUMINAÇÃO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(678,'ENGENHEIRO DE MANUFATURA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(679,'ENGENHEIRO DE MATERIAIS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(680,'ENGENHEIRO DE MINAS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(681,'ENGENHEIRO DE PETRÓLEO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(682,'ENGENHEIRO DE PROCESSOS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(683,'ENGENHEIRO DE PRODUÇÃO AGROINDUSTRIAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(684,'ENGENHEIRO DE PRODUTO OU PRODUÇÃO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(685,'ENGENHEIRO DE PROJETOS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(686,'ENGENHEIRO DE SEGURANÇA DO TRABALHO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(687,'ENGENHEIRO DE SOM',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(688,'ENGENHEIRO DE SUPPLY CHAIN OU LOGÍSTICA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(689,'ENGENHEIRO DE TELECOMUNICAÇÕES',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(690,'ENGENHEIRO DE TRANSPORTES',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(691,'ENGENHEIRO ELÉTRICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(692,'ENGENHEIRO FÍSICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(693,'ENGENHEIRO FLORESTAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(694,'ENGENHEIRO INDUSTRIAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(695,'ENGENHEIRO MECÂNICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(696,'ENGENHEIRO MECATRÔNICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(697,'ENGENHEIRO METALÚRGICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(698,'ENGENHEIRO NAVAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(699,'ENGENHEIRO PETROQUÍMICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(700,'ENGENHEIRO QUÍMICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(701,'ENGENHEIRO SANITARISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(702,'ENGENHEIRO TÊXTIL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(703,'ENGRAXATE',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(704,'ENÓLOGO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(705,'ENTALHADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(706,'EPIDEMIÓLOGO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(707,'ESCOTEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(708,'ESCRITOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(709,'ESCRITURÁRIO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(710,'ESCRIVÃO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(711,'ESCULTOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(712,'ESGRIMISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(713,'ESPECIALISTA EM AGRONEGÓCIOS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(714,'ESPELEOLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(715,'ESTAMPADOR DE TECIDOS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(716,'ESTATÍSTICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(717,'ESTETICISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(718,'ESTILISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(719,'ESTIVADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(720,'ESTOFADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(721,'ESTOQUISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(722,'FARMACÊUTICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(723,'FATURISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(724,'FAXINEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(725,'FEIRANTE',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(726,'FERRAMENTEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(727,'FERREIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(728,'FERROVIÁRIO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(729,'FIGURANTE',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(730,'FIGURINISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(731,'FILÓSOFO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(732,'FISCAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(733,'FÍSICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(734,'FÍSICO NUCLEAR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(735,'FISICULTURISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(736,'FISIOTERAPEUTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(737,'FLANELINHA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(738,'FLAUTISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(739,'FLORISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(740,'FONOAUDIÓLOGO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(741,'FORNEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(742,'FOTÓGRAFO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(743,'FRENTISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(744,'FRESADOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(745,'FUNDIDOR',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(746,'FUNDIDOR DE PLACA DE GESSO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(747,'FUNILEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(748,'GAGSMAN',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(749,'GANDULA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(750,'GARÇOM',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(751,'GARI',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(752,'GARIMPEIRO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(753,'GASTROENTEROLOGISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(754,'GASTRÔNOMO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(755,'GENERAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(756,'GEOFÍSICO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(757,'GEÓGRAFO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(758,'GEÓLOGO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(759,'GERADORISTA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(760,'GERENTE DE BANCO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(761,'GERENTE DE INOVAÇÕES OU NOVOS NEGÓCIOS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(762,'GERENTE DE RISCOS EM SEGUROS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(763,'GERENTE DE VENDAS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(764,'GERIATRA',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(765,'GESTOR AMBIENTAL',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(766,'GESTOR DE QUALIDADE',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(767,'GESTOR DE RECURSOS HUMANOS',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(768,'GESTOR DE TECNOLOGIA DA INFORMAÇÃO',1,'2021-03-30 16:37:31',NULL,NULL,NULL),(769,'SÓCIO PROPRIETÁRIO ',1,'2021-06-22 14:20:44','2021-06-22 14:20:47',9,9),(770,'AUTÔNOMO',1,'2021-07-19 14:28:03',NULL,NULL,NULL),(771,'TEC. EM ENFERMAGEM ',1,'2021-12-15 12:47:22',NULL,9,NULL),(772,'APOSENTADO',1,'2023-02-02 18:05:27',NULL,9,NULL);
/*!40000 ALTER TABLE `professions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `property_authorization_contract`
--

DROP TABLE IF EXISTS `property_authorization_contract`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `property_authorization_contract` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_customer` int(11) DEFAULT NULL,
  `id_standard_contract` int(11) DEFAULT NULL,
  `contract_text` mediumtext DEFAULT NULL,
  `signed` binary(1) DEFAULT '0',
  `status` binary(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `signed_at` datetime DEFAULT NULL,
  `code` text DEFAULT NULL,
  `ip` varchar(100) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `cpf` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `property_authorization_contract`
--

LOCK TABLES `property_authorization_contract` WRITE;
/*!40000 ALTER TABLE `property_authorization_contract` DISABLE KEYS */;
/*!40000 ALTER TABLE `property_authorization_contract` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `property_branch`
--

DROP TABLE IF EXISTS `property_branch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `property_branch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_property` int(11) NOT NULL,
  `id_branch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `property_branch`
--

LOCK TABLES `property_branch` WRITE;
/*!40000 ALTER TABLE `property_branch` DISABLE KEYS */;
/*!40000 ALTER TABLE `property_branch` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `property_category`
--

DROP TABLE IF EXISTS `property_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `property_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `slugify` varchar(255) DEFAULT NULL,
  `site_filter` binary(1) DEFAULT '0',
  `alias` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `property_category`
--

LOCK TABLES `property_category` WRITE;
/*!40000 ALTER TABLE `property_category` DISABLE KEYS */;
INSERT INTO `property_category` VALUES (1,'Lançamento',1,'2021-04-07 14:58:55','2023-05-09 11:23:38',14,7,'lanamento',_binary '1',NULL),(2,'Pré-lançamento',1,'2021-04-07 14:59:15','2023-05-09 11:23:43',14,7,'pr-lanamento',_binary '1',NULL),(3,'Construção',1,'2021-04-09 13:58:43','2021-07-05 12:20:56',14,7,'construcao',_binary '1',NULL),(4,'Pronta Entrega',1,'2021-04-09 14:13:05','2021-07-05 12:21:03',8,7,'pronta-entrega',_binary '1',NULL),(5,'Destaque',1,'2022-04-26 17:45:43','2023-05-09 11:23:49',1,7,'destaque',_binary '1',NULL);
/*!40000 ALTER TABLE `property_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `property_classification`
--

DROP TABLE IF EXISTS `property_classification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `property_classification` (
  `id` float NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `status` binary(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `property_classification`
--

LOCK TABLES `property_classification` WRITE;
/*!40000 ALTER TABLE `property_classification` DISABLE KEYS */;
/*!40000 ALTER TABLE `property_classification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `property_filter`
--

DROP TABLE IF EXISTS `property_filter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `property_filter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `name_column` varchar(255) DEFAULT NULL,
  `item_order` int(11) DEFAULT NULL,
  `status` binary(1) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `property_filter`
--

LOCK TABLES `property_filter` WRITE;
/*!40000 ALTER TABLE `property_filter` DISABLE KEYS */;
INSERT INTO `property_filter` VALUES (1,'Código','cod',1,_binary '1',NULL,NULL),(2,'Nome Imóvel','name',2,_binary '1',NULL,NULL),(3,'Tipo imóvel','id_residential_type',3,_binary '1',NULL,NULL),(4,'Categoria','id_property_category',4,_binary '1',NULL,NULL),(5,'Preço de','price_start',5,_binary '1',NULL,NULL),(6,'Preço até','price_end',6,_binary '1',NULL,NULL),(7,'Site','site_status',7,_binary '1',NULL,NULL),(8,'Status','status',8,_binary '1',NULL,NULL),(9,'UF - Cidade','id_city',9,_binary '1',NULL,NULL),(10,'Bairro','neighborhood',10,_binary '1',NULL,NULL),(11,'Loteamento','allotment',11,_binary '1',NULL,NULL),(12,'Rua','address',12,_binary '1',NULL,NULL),(13,'Disponibilidade','availability',13,_binary '1',NULL,NULL),(14,'Proprietário','id_owner',14,_binary '1',NULL,NULL),(15,'Criado por','created_by',15,_binary '1',NULL,NULL),(16,'Ordenar por','order',17,_binary '1',NULL,NULL),(17,'Filiais','property_branch',18,_binary '1',NULL,NULL),(18,'Proprietário do Cadastro','id_user_owner',16,_binary '1',NULL,NULL);
/*!40000 ALTER TABLE `property_filter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `property_integration`
--

DROP TABLE IF EXISTS `property_integration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `property_integration` (
  `id_property` int(11) NOT NULL,
  `id_property_integration` int(11) NOT NULL,
  `id_integration` int(11) NOT NULL,
  UNIQUE KEY `property_integration_un` (`id_property`,`id_property_integration`,`id_integration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `property_integration`
--

LOCK TABLES `property_integration` WRITE;
/*!40000 ALTER TABLE `property_integration` DISABLE KEYS */;
/*!40000 ALTER TABLE `property_integration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `property_involved_authorization_contract`
--

DROP TABLE IF EXISTS `property_involved_authorization_contract`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `property_involved_authorization_contract` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_product` int(11) DEFAULT NULL,
  `id_property_authorization_contract` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `property_involved_authorization_contract_id_product_IDX` (`id_product`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `property_involved_authorization_contract`
--

LOCK TABLES `property_involved_authorization_contract` WRITE;
/*!40000 ALTER TABLE `property_involved_authorization_contract` DISABLE KEYS */;
/*!40000 ALTER TABLE `property_involved_authorization_contract` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `property_type`
--

DROP TABLE IF EXISTS `property_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `property_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` binary(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `slugify` varchar(255) DEFAULT NULL,
  `site_filter` binary(1) DEFAULT '0',
  `prefix` varchar(5) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `property_type_UN` (`prefix`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `property_type`
--

LOCK TABLES `property_type` WRITE;
/*!40000 ALTER TABLE `property_type` DISABLE KEYS */;
INSERT INTO `property_type` VALUES (5,'Apartamento',_binary '1','2021-03-30 14:56:27','2023-10-16 13:43:17',NULL,1,'apartamento',_binary '1',NULL,NULL),(6,'Casa',_binary '1','2021-03-30 14:56:37','2023-10-16 13:45:18',NULL,1,'casa',_binary '1',NULL,NULL),(7,'Galpão',_binary '1','2021-03-30 14:56:45','2023-10-16 13:48:08',NULL,1,'galpao',_binary '1',NULL,NULL),(8,'Geminado',_binary '1','2021-03-30 14:56:54','2023-10-16 13:49:15',NULL,1,'geminado',_binary '1',NULL,NULL),(9,'Kitnet',_binary '1','2021-03-30 14:57:03','2023-10-16 13:50:13',NULL,1,'kitnet',_binary '1',NULL,NULL),(10,'Lote / Terreno',_binary '1','2021-03-30 14:57:15','2023-10-16 13:51:07',NULL,1,'lote-terreno',_binary '1',NULL,NULL),(12,'Sala Comercial',_binary '1','2021-03-30 14:57:36','2023-10-16 13:52:10',NULL,1,'sala-comercial',_binary '1',NULL,NULL),(13,'Sítio / Chácara',_binary '1','2021-03-30 14:57:48','2023-10-16 13:53:03',NULL,1,'sitio-chacara',_binary '1',NULL,NULL);
/*!40000 ALTER TABLE `property_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `property_type_resources`
--

DROP TABLE IF EXISTS `property_type_resources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `property_type_resources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_property_type` int(11) NOT NULL,
  `id_immovable_resource` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `property_type_resources_id_property_type_IDX` (`id_property_type`) USING BTREE,
  KEY `property_type_resources_id_immovable_resource_IDX` (`id_immovable_resource`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=172 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `property_type_resources`
--

LOCK TABLES `property_type_resources` WRITE;
/*!40000 ALTER TABLE `property_type_resources` DISABLE KEYS */;
INSERT INTO `property_type_resources` VALUES (1,5,7),(2,5,9),(3,5,11),(4,5,14),(5,5,15),(6,5,16),(7,5,17),(8,5,18),(9,5,20),(10,5,39),(11,5,21),(12,5,22),(13,5,23),(14,5,24),(15,5,27),(16,5,28),(17,5,29),(18,5,30),(19,5,31),(20,5,32),(21,5,33),(22,5,34),(23,5,35),(24,5,36),(25,5,38),(26,6,7),(27,6,9),(28,6,11),(29,6,12),(30,6,13),(31,6,14),(32,6,15),(33,6,16),(34,6,17),(35,6,18),(36,6,20),(37,6,39),(38,6,21),(39,6,22),(40,6,23),(41,6,24),(42,6,26),(43,6,27),(44,6,28),(45,6,29),(46,6,30),(47,6,31),(48,6,32),(49,6,33),(50,6,34),(51,6,35),(52,6,36),(53,6,38),(54,7,7),(55,7,11),(56,7,13),(57,7,14),(58,7,16),(59,7,17),(60,7,18),(61,7,20),(62,7,39),(63,7,21),(64,7,23),(65,7,24),(66,7,27),(67,7,28),(68,7,29),(69,7,30),(70,7,32),(71,8,7),(72,8,9),(73,8,11),(74,8,14),(75,8,15),(76,8,16),(77,8,17),(78,8,18),(79,8,20),(80,8,39),(81,8,21),(82,8,22),(83,8,23),(84,8,24),(85,8,27),(86,8,28),(87,8,29),(88,8,30),(89,8,31),(90,8,32),(91,8,33),(92,8,34),(93,8,35),(94,8,38),(95,9,7),(96,9,9),(97,9,11),(98,9,14),(99,9,15),(100,9,16),(101,9,17),(102,9,20),(103,9,39),(104,9,21),(105,9,22),(106,9,23),(107,9,24),(108,9,27),(109,9,29),(110,9,30),(111,9,31),(112,9,32),(113,9,33),(114,9,34),(115,9,35),(116,9,38),(117,10,7),(118,10,12),(119,10,13),(120,10,14),(121,10,20),(122,10,39),(123,10,21),(124,10,24),(125,10,26),(126,10,30),(127,10,32),(128,12,7),(129,12,9),(130,12,11),(131,12,14),(132,12,16),(133,12,17),(134,12,18),(135,12,20),(136,12,39),(137,12,21),(138,12,23),(139,12,24),(140,12,27),(141,12,28),(142,12,30),(143,12,32),(144,13,7),(145,13,9),(146,13,11),(147,13,12),(148,13,13),(149,13,14),(150,13,15),(151,13,16),(152,13,17),(153,13,18),(154,13,20),(155,13,39),(156,13,21),(157,13,22),(158,13,23),(159,13,24),(160,13,26),(161,13,27),(162,13,28),(163,13,29),(164,13,30),(165,13,31),(166,13,32),(167,13,33),(168,13,34),(169,13,35),(170,13,36),(171,13,38);
/*!40000 ALTER TABLE `property_type_resources` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rede_social`
--

DROP TABLE IF EXISTS `rede_social`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rede_social` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `icone` varchar(100) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `ordem` int(11) NOT NULL,
  `cor` varchar(45) DEFAULT NULL,
  `cor_hover` varchar(45) DEFAULT NULL,
  `cont` int(11) DEFAULT 0,
  `ativo` binary(1) DEFAULT '1',
  `cor_rodape` varchar(45) DEFAULT NULL,
  `cor_rodape_hover` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rede_social`
--

LOCK TABLES `rede_social` WRITE;
/*!40000 ALTER TABLE `rede_social` DISABLE KEYS */;
INSERT INTO `rede_social` VALUES (1,'Facebook','fa fa-facebook-square',NULL,1,'#184264','#000000',0,_binary '1','#184264','#153450'),(2,'Instagram','fa fa-instagram',NULL,2,'#184264','#000000',0,_binary '1','#184264','#153450'),(4,'Whatsapp','fa fa-whatsapp',NULL,3,'#014a6e','#000000',0,_binary '1','#014a6e','#153450');
/*!40000 ALTER TABLE `rede_social` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_attendance` int(11) DEFAULT NULL,
  `id_customer` int(11) DEFAULT NULL,
  `id_product` varchar(100) DEFAULT NULL,
  `id_standard_contract` int(11) DEFAULT NULL,
  `id_standard_proposal` int(11) DEFAULT NULL,
  `id_sale_status` int(11) DEFAULT NULL,
  `id_branch` int(11) DEFAULT NULL,
  `sale_value` decimal(12,2) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `sale_date` date DEFAULT NULL,
  `text` text DEFAULT NULL,
  `sales_manager` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `fgts` decimal(12,2) DEFAULT NULL,
  `fgts_select` binary(1) DEFAULT '0',
  `blocked` binary(1) DEFAULT '0',
  `id_bill_receive` int(11) DEFAULT NULL,
  `number_installments` int(11) DEFAULT NULL,
  `id_form_payment` int(11) DEFAULT NULL,
  `percentage_commission` decimal(10,2) DEFAULT 0.00,
  `percentage_commission_virtual_rate` decimal(10,2) DEFAULT 0.00,
  `percentage_commission_real_rate` decimal(10,2) DEFAULT 0.00,
  `percentage_commission_seller` decimal(10,2) DEFAULT 0.00,
  `origin_commission_seller` int(11) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `commission_value` decimal(12,2) DEFAULT NULL,
  `expense_commission` decimal(12,2) DEFAULT NULL,
  `expense_operational_value` decimal(12,2) DEFAULT NULL,
  `after_sale_retained` decimal(12,2) DEFAULT NULL,
  `expense_tax` decimal(12,2) DEFAULT NULL,
  `expense_management_commission` decimal(12,2) DEFAULT NULL,
  `seller_name` varchar(255) DEFAULT NULL,
  `seller_payment_date` date DEFAULT NULL,
  `currency_id` int(11) NOT NULL DEFAULT 2,
  `currency_value` decimal(12,2) DEFAULT `currency_id`,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_attachment`
--

DROP TABLE IF EXISTS `sales_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_attachment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `extension` varchar(15) DEFAULT NULL,
  `id_sale` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `update_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_attachment`
--

LOCK TABLES `sales_attachment` WRITE;
/*!40000 ALTER TABLE `sales_attachment` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_attachment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_charge_payment_agreement`
--

DROP TABLE IF EXISTS `sales_charge_payment_agreement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_charge_payment_agreement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_sale` int(11) NOT NULL,
  `id_user_position` int(11) NOT NULL,
  `id_customer` int(11) DEFAULT NULL,
  `percentage_commission` decimal(10,3) DEFAULT 0.000,
  `origin_commission` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `id_cost_center` int(11) DEFAULT NULL,
  `id_form_payment` int(11) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `currency_id` int(11) NOT NULL DEFAULT 2,
  `currency_value` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_charge_payment_agreement`
--

LOCK TABLES `sales_charge_payment_agreement` WRITE;
/*!40000 ALTER TABLE `sales_charge_payment_agreement` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_charge_payment_agreement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_expenses`
--

DROP TABLE IF EXISTS `sales_expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_sale` int(11) DEFAULT NULL,
  `expense_key` varchar(255) DEFAULT NULL,
  `expense_value` decimal(12,2) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_expenses`
--

LOCK TABLES `sales_expenses` WRITE;
/*!40000 ALTER TABLE `sales_expenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spouse_customer`
--

DROP TABLE IF EXISTS `spouse_customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `spouse_customer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_spouse` int(11) NOT NULL,
  `id_profession` int(11) DEFAULT NULL,
  `name` varchar(250) DEFAULT NULL,
  `person_registration` varchar(20) DEFAULT NULL,
  `address_spouse` tinyint(4) DEFAULT 0,
  `neighborhood` text DEFAULT NULL,
  `number_address` varchar(11) DEFAULT NULL,
  `cep` varchar(11) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `cellphone` varchar(11) DEFAULT NULL,
  `uf_state` varchar(2) DEFAULT NULL,
  `id_city` int(11) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `complement` text DEFAULT NULL,
  `rg` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `nationality` varchar(100) DEFAULT NULL,
  `id_country` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spouse_customer`
--

LOCK TABLES `spouse_customer` WRITE;
/*!40000 ALTER TABLE `spouse_customer` DISABLE KEYS */;
/*!40000 ALTER TABLE `spouse_customer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `standard_contract`
--

DROP TABLE IF EXISTS `standard_contract`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `standard_contract` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `text` mediumtext DEFAULT NULL,
  `type_contract` int(11) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `standard_contract`
--

LOCK TABLES `standard_contract` WRITE;
/*!40000 ALTER TABLE `standard_contract` DISABLE KEYS */;
INSERT INTO `standard_contract` VALUES (1,'PROPOSTA COMPRA E VENDA PADRÃO','<p style=\"text-align:center\"><strong>PROPOSTA DE NEGOCIA&Ccedil;&Atilde;O</strong></p>\r\n\r\n<p style=\"text-align:justify\"><strong>{%nome_filial%}</strong>, empresa com sede na cidade de {%cidade_filial%}, {%estado_filial%}, localizada no bairro&nbsp;{%bairroEndereco_filial%}, {%endereco_filial%} n&ordm; {%numeroEndereco_filial%}, {%complementoEndereco_filial%},&nbsp;devidamente inscrita no CNPJ/MF sob o n. {%cnpj_filial%}, vem &agrave; presen&ccedil;a de Vossa Senhoria apresentar a presente proposta de aquisi&ccedil;&atilde;o de um im&oacute;vel de sua propriedade, situado &agrave; cidade de {%cidade_produto%}/{%uf_produto%}, no loteamento&nbsp;{%loteamento_produto%}, bairro {%bairroEndereco_produto%}, {%endereco_produto%}, n&ordm;&nbsp;{%numeroEndereco_produto%},&nbsp; pelo valor total de R$ {%valor_venda%} ({%valorExtenso_venda%}), a ser pago por meio das seguintes parcelas:</p>\r\n\r\n<p style=\"text-align:justify\">{%parcelas_venda%}</p>\r\n\r\n<p style=\"text-align:justify\">Esta proposta tem validade de 30 (trinta) dias.</p>\r\n\r\n<p style=\"text-align:justify\">Sendo o que se apresenta, aguardo resposta na forma de aceita&ccedil;&atilde;o ou contraproposta.</p>\r\n\r\n<p style=\"text-align:justify\">{%cidade_filial%}, {%estado_filial%}, {%dia_venda%} de {%mes_venda%} de {%ano_venda%}.</p>\r\n\r\n<p style=\"text-align:center\">_____________________________________________________________________________________</p>\r\n\r\n<p style=\"text-align:center\"><strong>{%nomeGerente_venda%}</strong></p>\r\n\r\n<p style=\"text-align:center\">Creci: {%creciGerente_venda%}</p>\r\n',2,1,'2023-06-23 14:32:21',NULL,0,NULL),(2,'AUTORIZAÇÃO COM EXCLUSIVIDADE','<p style=\"text-align:center\"><strong>AUTORIZA&Ccedil;&Atilde;O PARA VENDA DE IM&Oacute;VEL COM EXCLUSIVIDADE</strong></p>\r\n\r\n<p>&nbsp; &nbsp; &nbsp;{%nome_cliente%}, {%nacionalidade_cliente%}, {%estadoCivil_cliente%}, {%profissao_cliente%}, inscrito(a) no CPF sob o n&ordm; {%cpf_cliente%}e no RG n&ordm; {%rg_cliente%}, residente e domiciliado(a) &agrave; {%endereco_cliente%} n&ordm;{%numeroEndereco_cliente%}, {%cidade_cliente%} - {%uf_cliente%}&nbsp;na qualidade de propriet&aacute;rio(a) dos imoveis:</p>\r\n\r\n<p>{%autorizacaoImovel_produtos%}</p>\r\n\r\n<p>&nbsp; &nbsp; &nbsp;A autorizada poder&aacute; realizar a divulga&ccedil;&atilde;o do im&oacute;vel atrav&eacute;s dos meios de comunica&ccedil;&atilde;o que julgar necess&aacute;rios, por suas pr&oacute;prias expensas, inclusive com a coloca&ccedil;&atilde;o de placa, veicula&ccedil;&atilde;o de an&uacute;ncios e visitas ao im&oacute;vel para mostr&aacute;-lo aos interessados.</p>\r\n\r\n<p>&nbsp; &nbsp; &nbsp;Ocorrendo a venda do im&oacute;vel pela autorizada, esta receber&aacute; honor&aacute;rios pelos servi&ccedil;os prestados a t&iacute;tulo de corretagem em quantia equivalente a {%comissao_filial%}&nbsp;({%comissaoExtenso_filial%}) do valor da venda do im&oacute;vel.</p>\r\n\r\n<p>&nbsp; &nbsp; &nbsp;O(a) propriet&aacute;rio(a) se obriga a n&atilde;o realizar a venda do im&oacute;vel diretamente ou por interm&eacute;dio de outrem durante o prazo de vig&ecirc;ncia desta autoriza&ccedil;&atilde;o, sob pena de ter de pagar a corretagem &agrave; autorizada como se a transa&ccedil;&atilde;o tivesse sido por ela conclu&iacute;da.</p>\r\n\r\n<p>&nbsp; &nbsp; &nbsp;Esta autoriza&ccedil;&atilde;o tem prazo de validade de {%diasRecadastro_filial%} ({%diasRecadastroExtenso_filial%}) dias, contados a partir da data de sua assinatura.</p>\r\n\r\n<p>{%cidade_filial%} - {%uf_filial%}, {%dia%} de {%mesExtenso%} de {%ano%}.</p>\r\n',3,1,'2023-06-23 14:32:21',NULL,0,NULL),(3,'AUTORIZAÇÃO SEM EXCLUSIVIDADE','<p style=\"text-align:center\"><strong>AUTORIZA&Ccedil;&Atilde;O PARA VENDA DE IM&Oacute;VEL SEM EXCLUSIVIDADE</strong></p>\r\n\r\n<p>&nbsp; &nbsp; &nbsp;{%nome_cliente%}, {%nacionalidade_cliente%}, {%estadoCivil_cliente%}, {%profissao_cliente%}, inscrito(a) no CPF sob o n&ordm; {%cpf_cliente%}e no RG n&ordm; {%rg_cliente%}, residente e domiciliado(a) &agrave; {%endereco_cliente%} n&ordm;{%numeroEndereco_cliente%}, {%cidade_cliente%} - {%uf_cliente%}&nbsp;na qualidade de propriet&aacute;rio(a) dos imoveis:</p>\r\n\r\n<p>{%autorizacaoImovel_produtos%}</p>\r\n\r\n<p>&nbsp; &nbsp; &nbsp;AUTORIZO a {%nomeJuridico_filial%}, inscrita no CRECI n&ordm; {%creci_filial%}, com sede &agrave; {%endereco_filial%} n&ordm;{%numeroEndereco_filial%}, {%cidade_filial%} - {%uf_filial%}, a intermediar a venda do referido aos im&oacute;veis.</p>\r\n\r\n<p>&nbsp; &nbsp; &nbsp;A autorizada poder&aacute; proceder a divulga&ccedil;&atilde;o do im&oacute;vel pelos meios de comunica&ccedil;&atilde;o que habitualmente utiliza, por suas pr&oacute;prias expensas, incluindo a coloca&ccedil;&atilde;o de placa, veicula&ccedil;&atilde;o de an&uacute;ncios e visitas ao im&oacute;vel para mostr&aacute;-lo &agrave;s pessoas interessadas.</p>\r\n\r\n<p>&nbsp; &nbsp; &nbsp;Em caso de efetiva&ccedil;&atilde;o da venda do im&oacute;vel pela autorizada, fica a ela assegurado o recebimento de honor&aacute;rios pelos servi&ccedil;os prestados, na forma de corretagem, equivalente a {%comissao_filial%}&nbsp;({%comissaoExtenso_filial%}) do valor da venda do im&oacute;vel.</p>\r\n\r\n<p>&nbsp; &nbsp; &nbsp;Esta autoriza&ccedil;&atilde;o tem prazo de validade de {%diasRecadastro_filial%} ({%diasRecadastroExtenso_filial%}) dias, contados a partir da data de sua assinatura.</p>\r\n\r\n<p>{%cidade_filial%} - {%uf_filial%}, {%dia%} de {%mesExtenso%} de {%ano%}.</p>\r\n',3,1,'2023-06-23 14:32:22',NULL,0,NULL),(4,'RECIBO SIMPLES','<p>Recibo refente a parcela nº {%numero_parcela%}.</p>\r\n\r\n<p>Favorecido: <strong>{%nome_filial%}</strong>.</p>\r\n\r\n<p>Eu, {%nome_filial%}, inscrito sob o CPF/CNPJ nº {%cnpj_filial%}, com endereço na {%endereco_filial%}, nº {%numeroEndereco_filial%}, {%bairroEndereco_filial%} - {%cidade_filial%}/{%uf_filial%}, declaro que recebi no formato de {%forma_pagamento%}, a soma de <strong> </strong>{%valor_parcela%} <strong>({%valor_extenso%})</strong>, da pessoa de nome {%nome_cliente%}.</p>\r\n\r\n<p>Assim, dou a plena, real e sincera  quitação do valor, estando livre de qualquer vício ou coação, nada tendo a reclamar sob o presente feito.</p>\r\n\r\n<p>____________________________________________________ </p>\r\n\r\n<p>Nome e assinatura do credor.</p>\r\n\r\n<p>CPF/CNPJ nº {%cpf_cliente%}{%cnpj_empresa%}</p>',4,1,'2023-06-23 14:32:22',NULL,0,NULL);
/*!40000 ALTER TABLE `standard_contract` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `standard_contract_variables`
--

DROP TABLE IF EXISTS `standard_contract_variables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `standard_contract_variables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_standard_contract` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `standard_contract_variables`
--

LOCK TABLES `standard_contract_variables` WRITE;
/*!40000 ALTER TABLE `standard_contract_variables` DISABLE KEYS */;
/*!40000 ALTER TABLE `standard_contract_variables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `states`
--

DROP TABLE IF EXISTS `states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `states` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uf_code` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `uf` char(2) NOT NULL,
  `id_country` int(11) NOT NULL,
  `region` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `states`
--

LOCK TABLES `states` WRITE;
/*!40000 ALTER TABLE `states` DISABLE KEYS */;
INSERT INTO `states` VALUES (1,12,'ACRE','AC',41,1,1),(2,27,'ALAGOAS','AL',41,2,1),(3,16,'AMAPÁ','AP',41,1,1),(4,13,'AMAZONAS','AM',41,1,1),(5,29,'BAHIA','BA',41,2,1),(6,23,'CEARÁ','CE',41,2,1),(7,53,'DISTRITO FEDERAL','DF',41,5,1),(8,32,'ESPÍRITO SANTO','ES',41,3,1),(9,52,'GOIÁS','GO',41,5,1),(10,21,'MARANHÃO','MA',41,2,1),(11,51,'MATO GROSSO','MT',41,5,1),(12,50,'MATO GROSSO DO SUL','MS',41,5,1),(13,31,'MINAS GERAIS','MG',41,3,1),(14,15,'PARÁ','PA',41,2,1),(15,25,'PARAÍBA','PB',41,2,1),(16,41,'PARANÁ','PR',41,4,1),(17,26,'PERNAMBUCO','PE',41,2,1),(18,22,'PIAUÍ','PI',41,2,1),(19,33,'RIO DE JANEIRO','RJ',41,3,1),(20,24,'RIO GRANDE DO NORTE','RN',41,2,1),(21,43,'RIO GRANDE DO SUL','RS',41,4,1),(22,11,'RONDÔNIA','RO',41,1,1),(23,14,'RORAIMA','RR',41,1,1),(24,42,'SANTA CATARINA','SC',41,4,1),(25,35,'SÃO PAULO','SP',41,3,1),(26,28,'SERGIPE','SE',41,2,1),(27,17,'TOCANTINS','TO',41,1,1);
/*!40000 ALTER TABLE `states` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `status`
--

DROP TABLE IF EXISTS `status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `status`
--

LOCK TABLES `status` WRITE;
/*!40000 ALTER TABLE `status` DISABLE KEYS */;
INSERT INTO `status` VALUES (1,'Aguardando Fechamento',1,'2021-03-29 15:59:50',NULL,0,NULL),(2,'Aguardando Aprovação',1,'2021-03-29 15:59:50',NULL,0,NULL),(3,'Aprovado',1,'2021-03-29 15:59:50',NULL,0,NULL),(9,'Cancelado',1,'2021-03-29 15:59:50',NULL,0,NULL);
/*!40000 ALTER TABLE `status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `status_icon`
--

DROP TABLE IF EXISTS `status_icon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `status_icon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `status` binary(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `status_icon`
--

LOCK TABLES `status_icon` WRITE;
/*!40000 ALTER TABLE `status_icon` DISABLE KEYS */;
INSERT INTO `status_icon` VALUES (1,'fa fa-plus bg-green',_binary '1'),(2,'fa fas fa-pen bg-aqua',_binary '1'),(3,'fa fa-comment bg-blue',_binary '1'),(4,'fa fa-clock bg-red',_binary '1'),(5,'fa fa-paperclip bg-yellow',_binary '1'),(6,'fa fas fa-phone-alt bg-green',_binary '1'),(7,'fa fas fa-trash-alt bg-red',_binary '1'),(8,'fa fas fa-home bg-blue',_binary '1'),(9,'fa fas fa-home bg-red',_binary '1');
/*!40000 ALTER TABLE `status_icon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `summary_involved`
--

DROP TABLE IF EXISTS `summary_involved`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `summary_involved` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL,
  `installment_number` int(11) NOT NULL,
  `user_position_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `id_cost_center` int(11) DEFAULT NULL,
  `id_form_payment` int(11) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_date` date NOT NULL,
  `currency_id` int(11) NOT NULL,
  `currency_value` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `summary_involved`
--

LOCK TABLES `summary_involved` WRITE;
/*!40000 ALTER TABLE `summary_involved` DISABLE KEYS */;
/*!40000 ALTER TABLE `summary_involved` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `summary_sale`
--

DROP TABLE IF EXISTS `summary_sale`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `summary_sale` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sale_id` int(11) NOT NULL,
  `installment_number` int(11) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `received_date` date NOT NULL,
  `currency_id` int(11) NOT NULL,
  `currency_value` decimal(12,2) DEFAULT NULL,
  `seller_amount` decimal(12,2) NOT NULL,
  `seller_payment_date` date NOT NULL,
  `seller_currency_id` int(11) NOT NULL,
  `seller_currency_value` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `summary_sale`
--

LOCK TABLES `summary_sale` WRITE;
/*!40000 ALTER TABLE `summary_sale` DISABLE KEYS */;
/*!40000 ALTER TABLE `summary_sale` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_config`
--

DROP TABLE IF EXISTS `system_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL DEFAULT '',
  `path_images` varchar(256) NOT NULL DEFAULT '',
  `footer` varchar(256) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `water_mark_cont` int(11) DEFAULT 0,
  `water_mark_ext` varchar(20) DEFAULT NULL,
  `water_mark_capa` binary(1) DEFAULT '0',
  `water_mark_horizontal` int(11) DEFAULT NULL,
  `water_mark_vertical` int(11) DEFAULT NULL,
  `water_mark_required` binary(1) DEFAULT '0',
  `water_mark_opacity` varchar(3) DEFAULT '100',
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `maintenance` binary(1) DEFAULT '0',
  `logo_menu_capa` binary(1) DEFAULT '0',
  `logo_menu_cont` int(11) DEFAULT 0,
  `logo_menu_ext` varchar(20) DEFAULT NULL,
  `logo_mini_capa` binary(1) DEFAULT '0',
  `logo_mini_cont` int(11) DEFAULT 0,
  `logo_mini_ext` varchar(20) DEFAULT NULL,
  `logo_login_capa` binary(1) DEFAULT '0',
  `logo_login_cont` int(11) DEFAULT 0,
  `logo_login_ext` varchar(20) DEFAULT NULL,
  `logo_favicon_capa` binary(1) DEFAULT '0',
  `logo_favicon_cont` int(11) DEFAULT 0,
  `logo_favicon_ext` varchar(20) DEFAULT NULL,
  `logo_rodape_capa` binary(1) DEFAULT '0',
  `logo_rodape_cont` int(11) DEFAULT 0,
  `logo_rodape_ext` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_config`
--

LOCK TABLES `system_config` WRITE;
/*!40000 ALTER TABLE `system_config` DISABLE KEYS */;
INSERT INTO `system_config` VALUES (1,'Realize Repasse','realize_repasse','Realize Repasse',1,0,'',_binary '0',3,3,_binary '0','',NULL,NULL,NULL,_binary '0',0,'',_binary '0',0,'',_binary '0',0,'',_binary '0',0,'',_binary '0',0,'');
/*!40000 ALTER TABLE `system_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `texto`
--

DROP TABLE IF EXISTS `texto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `texto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categoria` varchar(255) NOT NULL,
  `slugify` text NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `galeria` binary(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `texto`
--

LOCK TABLES `texto` WRITE;
/*!40000 ALTER TABLE `texto` DISABLE KEYS */;
INSERT INTO `texto` VALUES (1,'empresa','sobre-ns','Sobre Nós','<p style=\"text-align:center\">A Ydeal Construtora E Incorporadora&nbsp;come&ccedil;ou h&aacute; 10 anos, de um sonho, trabalho e de conhecimento no setor da constru&ccedil;&atilde;o civil. Hoje a construtora, que j&aacute; &eacute; refer&ecirc;ncia na cidade est&aacute; entrando em uma nova fase e passa a oferecer servi&ccedil;os Imobili&aacute;rios a seus clientes.<br />\r\n<br />\r\nAssim como vinha sendo feito at&eacute; ent&atilde;o, o principal objetivo deste servi&ccedil;o &eacute; oferecer o melhor para o cliente, ajudando ele a tirar do papel o sonho de encontrar o im&oacute;vel ideal.<br />\r\n<br />\r\nFalando nos im&oacute;veis, eles com absoluta certeza, contar&atilde;o com todos os diferenciais que somente projetos de alta qualidade tem a oferecer.</p>',_binary '0'),(2,'horario','horarios-de-atendimento','Horários de Atendimento','<p>Segunda a Sexta das 08:00h at&eacute; 17:00h</p>\r\n\r\n<p>S&aacute;bado e Domingo fechado</p>',_binary '0'),(3,'depoimento','depoimento','Depoimentos','<p>Veja o que nossos clientes dizem sobre n&oacute;s!</p>\r\n',_binary '0'),(4,'contato','fale-conosco','Fale Conosco','<p>Ainda n&atilde;o encontrou o im&oacute;vel perfeito?<br />\r\n<strong>N&oacute;s te ajudaremos!</strong></p>\r\n\r\n<p>Preencha os campos&nbsp;ou entre em contato pelo Whatsapp.</p>\r\n',_binary '0'),(5,'termos','politicas-de-privacidades-e-termos-de-uso','Políticas de Privacidades e Termos de Uso','<h2>Pol&iacute;tica Privacidade</h2>\r\n\r\n<p>A sua privacidade &eacute; importante para n&oacute;s. &Eacute; pol&iacute;tica do Ydeal Tec Im&oacute;veis respeitar a sua privacidade em rela&ccedil;&atilde;o a qualquer informa&ccedil;&atilde;o sua que possamos coletar no site <a href=\"https://www.ydealtecnologia.com.br\">Ydeal Tec Im&oacute;veis</a>, e outros sites que possu&iacute;mos e operamos.</p>\r\n\r\n<p>Solicitamos informa&ccedil;&otilde;es pessoais apenas quando realmente precisamos delas para lhe fornecer um servi&ccedil;o. Fazemo-lo por meios justos e legais, com o seu conhecimento e consentimento. Tamb&eacute;m informamos por que estamos coletando e como ser&aacute; usado.</p>\r\n\r\n<p>Apenas retemos as informa&ccedil;&otilde;es coletadas pelo tempo necess&aacute;rio para fornecer o servi&ccedil;o solicitado. Quando armazenamos dados, protegemos dentro de meios comercialmente aceit&aacute;veis ??para evitar perdas e roubos, bem como acesso, divulga&ccedil;&atilde;o, c&oacute;pia, uso ou modifica&ccedil;&atilde;o n&atilde;o autorizados.</p>\r\n\r\n<p>N&atilde;o compartilhamos informa&ccedil;&otilde;es de identifica&ccedil;&atilde;o pessoal publicamente ou com terceiros, exceto quando exigido por lei.</p>\r\n\r\n<p>O nosso site pode ter links para sites externos que n&atilde;o s&atilde;o operados por n&oacute;s. Esteja ciente de que n&atilde;o temos controle sobre o conte&uacute;do e pr&aacute;ticas desses sites e n&atilde;o podemos aceitar responsabilidade por suas respectivas <a href=\"https://politicaprivacidade.com\" target=\"_BLANK\">pol&iacute;ticas de privacidade</a>.</p>\r\n\r\n<p>Voc&ecirc; &eacute; livre para recusar a nossa solicita&ccedil;&atilde;o de informa&ccedil;&otilde;es pessoais, entendendo que talvez n&atilde;o possamos fornecer alguns dos servi&ccedil;os desejados.</p>\r\n\r\n<p>O uso continuado de nosso site ser&aacute; considerado como aceita&ccedil;&atilde;o de nossas pr&aacute;ticas em torno de privacidade e informa&ccedil;&otilde;es pessoais. Se voc&ecirc; tiver alguma d&uacute;vida sobre como lidamos com dados do usu&aacute;rio e informa&ccedil;&otilde;es pessoais, entre em contacto connosco.</p>\r\n\r\n<h2>Pol&iacute;tica de Cookies Ydeal Tec Im&oacute;veis</h2>\r\n\r\n<h3>O que s&atilde;o cookies?</h3>\r\n\r\n<p>Como &eacute; pr&aacute;tica comum em quase todos os sites profissionais, este site usa cookies, que s&atilde;o pequenos arquivos baixados no seu computador, para melhorar sua experi&ecirc;ncia. Esta p&aacute;gina descreve quais informa&ccedil;&otilde;es eles coletam, como as usamos e por que &agrave;s vezes precisamos armazenar esses cookies. Tamb&eacute;m compartilharemos como voc&ecirc; pode impedir que esses cookies sejam armazenados, no entanto, isso pode fazer o downgrade ou &#39;quebrar&#39; certos elementos da funcionalidade do site.</p>\r\n\r\n<h3>Como usamos os cookies?</h3>\r\n\r\n<p>Utilizamos cookies por v&aacute;rios motivos, detalhados abaixo. Infelizmente, na maioria dos casos, n&atilde;o existem op&ccedil;&otilde;es padr&atilde;o do setor para desativar os cookies sem desativar completamente a funcionalidade e os recursos que eles adicionam a este site. &Eacute; recomend&aacute;vel que voc&ecirc; deixe todos os cookies se n&atilde;o tiver certeza se precisa ou n&atilde;o deles, caso sejam usados ??para fornecer um servi&ccedil;o que voc&ecirc; usa.</p>\r\n\r\n<h3>Desativar cookies</h3>\r\n\r\n<p>Voc&ecirc; pode impedir a configura&ccedil;&atilde;o de cookies ajustando as configura&ccedil;&otilde;es do seu navegador (consulte a Ajuda do navegador para saber como fazer isso). Esteja ciente de que a desativa&ccedil;&atilde;o de cookies afetar&aacute; a funcionalidade deste e de muitos outros sites que voc&ecirc; visita. A desativa&ccedil;&atilde;o de cookies geralmente resultar&aacute; na desativa&ccedil;&atilde;o de determinadas funcionalidades e recursos deste site. Portanto, &eacute; recomend&aacute;vel que voc&ecirc; n&atilde;o desative os cookies.</p>\r\n\r\n<h3>Cookies que definimos</h3>\r\n\r\n<ul>\r\n	<li>Cookies relacionados &agrave; conta<br />\r\n	<br />\r\n	Se voc&ecirc; criar uma conta connosco, usaremos cookies para o gerenciamento do processo de inscri&ccedil;&atilde;o e administra&ccedil;&atilde;o geral. Esses cookies geralmente ser&atilde;o exclu&iacute;dos quando voc&ecirc; sair do sistema, por&eacute;m, em alguns casos, eles poder&atilde;o permanecer posteriormente para lembrar as prefer&ecirc;ncias do seu site ao sair.<br />\r\n	&nbsp;</li>\r\n	<li>Cookies relacionados ao login<br />\r\n	<br />\r\n	Utilizamos cookies quando voc&ecirc; est&aacute; logado, para que possamos lembrar dessa a&ccedil;&atilde;o. Isso evita que voc&ecirc; precise fazer login sempre que visitar uma nova p&aacute;gina. Esses cookies s&atilde;o normalmente removidos ou limpos quando voc&ecirc; efetua logout para garantir que voc&ecirc; possa acessar apenas a recursos e &aacute;reas restritas ao efetuar login.<br />\r\n	&nbsp;</li>\r\n	<li>Cookies relacionados a boletins por e-mail<br />\r\n	<br />\r\n	Este site oferece servi&ccedil;os de assinatura de boletim informativo ou e-mail e os cookies podem ser usados ??para lembrar se voc&ecirc; j&aacute; est&aacute; registrado e se deve mostrar determinadas notifica&ccedil;&otilde;es v&aacute;lidas apenas para usu&aacute;rios inscritos / n&atilde;o inscritos.<br />\r\n	&nbsp;</li>\r\n	<li>Pedidos processando cookies relacionados<br />\r\n	<br />\r\n	Este site oferece facilidades de com&eacute;rcio eletr&ocirc;nico ou pagamento e alguns cookies s&atilde;o essenciais para garantir que seu pedido seja lembrado entre as p&aacute;ginas, para que possamos process&aacute;-lo adequadamente.<br />\r\n	&nbsp;</li>\r\n	<li>Cookies relacionados a pesquisas<br />\r\n	<br />\r\n	Periodicamente, oferecemos pesquisas e question&aacute;rios para fornecer informa&ccedil;&otilde;es interessantes, ferramentas &uacute;teis ou para entender nossa base de usu&aacute;rios com mais precis&atilde;o. Essas pesquisas podem usar cookies para lembrar quem j&aacute; participou numa pesquisa ou para fornecer resultados precisos ap&oacute;s a altera&ccedil;&atilde;o das p&aacute;ginas.<br />\r\n	&nbsp;</li>\r\n	<li>Cookies relacionados a formul&aacute;rios<br />\r\n	<br />\r\n	Quando voc&ecirc; envia dados por meio de um formul&aacute;rio como os encontrados nas p&aacute;ginas de contacto ou nos formul&aacute;rios de coment&aacute;rios, os cookies podem ser configurados para lembrar os detalhes do usu&aacute;rio para correspond&ecirc;ncia futura.<br />\r\n	&nbsp;</li>\r\n	<li>Cookies de prefer&ecirc;ncias do site<br />\r\n	<br />\r\n	Para proporcionar uma &oacute;tima experi&ecirc;ncia neste site, fornecemos a funcionalidade para definir suas prefer&ecirc;ncias de como esse site &eacute; executado quando voc&ecirc; o usa. Para lembrar suas prefer&ecirc;ncias, precisamos definir cookies para que essas informa&ccedil;&otilde;es possam ser chamadas sempre que voc&ecirc; interagir com uma p&aacute;gina for afetada por suas prefer&ecirc;ncias.</li>\r\n</ul>\r\n\r\n<h3>Cookies de Terceiros</h3>\r\n\r\n<p>Em alguns casos especiais, tamb&eacute;m usamos cookies fornecidos por terceiros confi&aacute;veis. A se&ccedil;&atilde;o a seguir detalha quais cookies de terceiros voc&ecirc; pode encontrar atrav&eacute;s deste site.</p>\r\n\r\n<ul>\r\n	<li>Este site usa o Google Analytics, que &eacute; uma das solu&ccedil;&otilde;es de an&aacute;lise mais difundidas e confi&aacute;veis ??da Web, para nos ajudar a entender como voc&ecirc; usa o site e como podemos melhorar sua experi&ecirc;ncia. Esses cookies podem rastrear itens como quanto tempo voc&ecirc; gasta no site e as p&aacute;ginas visitadas, para que possamos continuar produzindo conte&uacute;do atraente.</li>\r\n</ul>\r\n\r\n<p>Para mais informa&ccedil;&otilde;es sobre cookies do Google Analytics, consulte a p&aacute;gina oficial do Google Analytics.</p>\r\n\r\n<ul>\r\n	<li>As an&aacute;lises de terceiros s&atilde;o usadas para rastrear e medir o uso deste site, para que possamos continuar produzindo conte&uacute;do atrativo. Esses cookies podem rastrear itens como o tempo que voc&ecirc; passa no site ou as p&aacute;ginas visitadas, o que nos ajuda a entender como podemos melhorar o site para voc&ecirc;.</li>\r\n	<li>Periodicamente, testamos novos recursos e fazemos altera&ccedil;&otilde;es subtis na maneira como o site se apresenta. Quando ainda estamos testando novos recursos, esses cookies podem ser usados ??para garantir que voc&ecirc; receba uma experi&ecirc;ncia consistente enquanto estiver no site, enquanto entendemos quais otimiza&ccedil;&otilde;es os nossos usu&aacute;rios mais apreciam.</li>\r\n	<li>&Agrave; medida que vendemos produtos, &eacute; importante entendermos as estat&iacute;sticas sobre quantos visitantes de nosso site realmente compram e, portanto, esse &eacute; o tipo de dados que esses cookies rastrear&atilde;o. Isso &eacute; importante para voc&ecirc;, pois significa que podemos fazer previs&otilde;es de neg&oacute;cios com precis&atilde;o que nos permitem analizar nossos custos de publicidade e produtos para garantir o melhor pre&ccedil;o poss&iacute;vel.</li>\r\n	<li>O servi&ccedil;o Google AdSense que usamos para veicular publicidade usa um cookie DoubleClick para veicular an&uacute;ncios mais relevantes em toda a Web e limitar o n&uacute;mero de vezes que um determinado an&uacute;ncio &eacute; exibido para voc&ecirc;.<br />\r\n	Para mais informa&ccedil;&otilde;es sobre o Google AdSense, consulte as FAQs oficiais sobre privacidade do Google AdSense.</li>\r\n	<li>Utilizamos an&uacute;ncios para compensar os custos de funcionamento deste site e fornecer financiamento para futuros desenvolvimentos. Os cookies de publicidade comportamental usados ??por este site foram projetados para garantir que voc&ecirc; forne&ccedil;a os an&uacute;ncios mais relevantes sempre que poss&iacute;vel, rastreando anonimamente seus interesses e apresentando coisas semelhantes que possam ser do seu interesse.</li>\r\n	<li>V&aacute;rios parceiros anunciam em nosso nome e os cookies de rastreamento de afiliados simplesmente nos permitem ver se nossos clientes acessaram o site atrav&eacute;s de um dos sites de nossos parceiros, para que possamos credit&aacute;-los adequadamente e, quando aplic&aacute;vel, permitir que nossos parceiros afiliados ofere&ccedil;am qualquer promo&ccedil;&atilde;o que pode fornec&ecirc;-lo para fazer uma compra.</li>\r\n</ul>\r\n\r\n<h3>Compromisso do Usu&aacute;rio</h3>\r\n\r\n<p>O usu&aacute;rio se compromete a fazer uso adequado dos conte&uacute;dos e da informa&ccedil;&atilde;o que o Ydeal Tec Im&oacute;veis oferece no site e com car&aacute;ter enunciativo, mas n&atilde;o limitativo:</p>\r\n\r\n<ul>\r\n	<li>A) N&atilde;o se envolver em atividades que sejam ilegais ou contr&aacute;rias &agrave; boa f&eacute; a &agrave; ordem p&uacute;blica;</li>\r\n	<li>B) N&atilde;o difundir propaganda ou conte&uacute;do de natureza racista, xenof&oacute;bica, ou apostas desportivas (ex.:<a href=\"https://ondeapostar.pt/review/betano/\"> Betano</a>), jogos de sorte e azar, qualquer tipo de pornografia ilegal, de apologia ao terrorismo ou contra os direitos humanos;</li>\r\n	<li>C) N&atilde;o causar danos aos sistemas f&iacute;sicos (hardwares) e l&oacute;gicos (softwares) do Ydeal Tec Im&oacute;veis, de seus fornecedores ou terceiros, para introduzir ou disseminar v&iacute;rus inform&aacute;ticos ou quaisquer outros sistemas de hardware ou software que sejam capazes de causar danos anteriormente mencionados.</li>\r\n</ul>\r\n\r\n<h3>Mais informa&ccedil;&otilde;es</h3>\r\n\r\n<p>Esperemos que esteja esclarecido e, como mencionado anteriormente, se houver algo que voc&ecirc; n&atilde;o tem certeza se precisa ou n&atilde;o, geralmente &eacute; mais seguro deixar os cookies ativados, caso interaja com um dos recursos que voc&ecirc; usa em nosso site.</p>\r\n\r\n<p>Esta pol&iacute;tica &eacute; efetiva a partir de <strong>June</strong>/<strong>2021</strong>.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h2>1. Termos de Uso</h2>\r\n\r\n<p>Ao acessar ao site <a href=\"https://www.ydealtecnologia.com.br\">Ydeal Tec Im&oacute;veis</a>, concorda em cumprir estes termos de servi&ccedil;o, todas as leis e regulamentos aplic&aacute;veis ??e concorda que &eacute; respons&aacute;vel pelo cumprimento de todas as leis locais aplic&aacute;veis. Se voc&ecirc; n&atilde;o concordar com algum desses termos, est&aacute; proibido de usar ou acessar este site. Os materiais contidos neste site s&atilde;o protegidos pelas leis de direitos autorais e marcas comerciais aplic&aacute;veis.</p>\r\n\r\n<h2>2. Uso de Licen&ccedil;a</h2>\r\n\r\n<p>&Eacute; concedida permiss&atilde;o para baixar temporariamente uma c&oacute;pia dos materiais (informa&ccedil;&otilde;es ou software) no site Ydeal Tec Im&oacute;veis , apenas para visualiza&ccedil;&atilde;o transit&oacute;ria pessoal e n&atilde;o comercial. Esta &eacute; a concess&atilde;o de uma licen&ccedil;a, n&atilde;o uma transfer&ecirc;ncia de t&iacute;tulo e, sob esta licen&ccedil;a, voc&ecirc; n&atilde;o pode:&nbsp;</p>\r\n\r\n<ol>\r\n	<li>modificar ou copiar os materiais;&nbsp;</li>\r\n	<li>usar os materiais para qualquer finalidade comercial ou para exibi&ccedil;&atilde;o p&uacute;blica (comercial ou n&atilde;o comercial);&nbsp;</li>\r\n	<li>tentar descompilar ou fazer engenharia reversa de qualquer software contido no site Ydeal Tec Im&oacute;veis;&nbsp;</li>\r\n	<li>remover quaisquer direitos autorais ou outras nota&ccedil;&otilde;es de propriedade dos materiais; ou&nbsp;</li>\r\n	<li>transferir os materiais para outra pessoa ou &#39;espelhe&#39; os materiais em qualquer outro servidor.</li>\r\n</ol>\r\n\r\n<p>Esta licen&ccedil;a ser&aacute; automaticamente rescindida se voc&ecirc; violar alguma dessas restri&ccedil;&otilde;es e poder&aacute; ser rescindida por Ydeal Tec Im&oacute;veis a qualquer momento. Ao encerrar a visualiza&ccedil;&atilde;o desses materiais ou ap&oacute;s o t&eacute;rmino desta licen&ccedil;a, voc&ecirc; deve apagar todos os materiais baixados em sua posse, seja em formato eletr&oacute;nico ou impresso.</p>\r\n\r\n<h2>3. Isen&ccedil;&atilde;o de responsabilidade</h2>\r\n\r\n<ol>\r\n	<li>Os materiais no site da Ydeal Tec Im&oacute;veis s&atilde;o fornecidos &#39;como est&atilde;o&#39;. Ydeal Tec Im&oacute;veis n&atilde;o oferece garantias, expressas ou impl&iacute;citas, e, por este meio, isenta e nega todas as outras garantias, incluindo, sem limita&ccedil;&atilde;o, garantias impl&iacute;citas ou condi&ccedil;&otilde;es de comercializa&ccedil;&atilde;o, adequa&ccedil;&atilde;o a um fim espec&iacute;fico ou n&atilde;o viola&ccedil;&atilde;o de propriedade intelectual ou outra viola&ccedil;&atilde;o de direitos.</li>\r\n	<li>Al&eacute;m disso, o Ydeal Tec Im&oacute;veis n&atilde;o garante ou faz qualquer representa&ccedil;&atilde;o relativa &agrave; precis&atilde;o, aos resultados prov&aacute;veis ??ou &agrave; confiabilidade do uso dos materiais em seu site ou de outra forma relacionado a esses materiais ou em sites vinculados a este site.</li>\r\n</ol>\r\n\r\n<h2>4. Limita&ccedil;&otilde;es</h2>\r\n\r\n<p>Em nenhum caso o Ydeal Tec Im&oacute;veis ou seus fornecedores ser&atilde;o respons&aacute;veis ??por quaisquer danos (incluindo, sem limita&ccedil;&atilde;o, danos por perda de dados ou lucro ou devido a interrup&ccedil;&atilde;o dos neg&oacute;cios) decorrentes do uso ou da incapacidade de usar os materiais em Ydeal Tec Im&oacute;veis, mesmo que Ydeal Tec Im&oacute;veis ou um representante autorizado da Ydeal Tec Im&oacute;veis tenha sido notificado oralmente ou por escrito da possibilidade de tais danos. Como algumas jurisdi&ccedil;&otilde;es n&atilde;o permitem limita&ccedil;&otilde;es em garantias impl&iacute;citas, ou limita&ccedil;&otilde;es de responsabilidade por danos conseq&uuml;entes ou incidentais, essas limita&ccedil;&otilde;es podem n&atilde;o se aplicar a voc&ecirc;.</p>\r\n\r\n<h2>5. Precis&atilde;o dos materiais</h2>\r\n\r\n<p>Os materiais exibidos no site da Ydeal Tec Im&oacute;veis podem incluir erros t&eacute;cnicos, tipogr&aacute;ficos ou fotogr&aacute;ficos. Ydeal Tec Im&oacute;veis n&atilde;o garante que qualquer material em seu site seja preciso, completo ou atual. Ydeal Tec Im&oacute;veis pode fazer altera&ccedil;&otilde;es nos materiais contidos em seu site a qualquer momento, sem aviso pr&eacute;vio. No entanto, Ydeal Tec Im&oacute;veis n&atilde;o se compromete a atualizar os materiais.</p>\r\n\r\n<h2>6. Links</h2>\r\n\r\n<p>O Ydeal Tec Im&oacute;veis n&atilde;o analisou todos os sites vinculados ao seu site e n&atilde;o &eacute; respons&aacute;vel pelo conte&uacute;do de nenhum site vinculado. A inclus&atilde;o de qualquer link n&atilde;o implica endosso por Ydeal Tec Im&oacute;veis do site. O uso de qualquer site vinculado &eacute; por conta e risco do usu&aacute;rio.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h3>Modifica&ccedil;&otilde;es</h3>\r\n\r\n<p>O Ydeal Tec Im&oacute;veis pode revisar estes termos de servi&ccedil;o do site a qualquer momento, sem aviso pr&eacute;vio. Ao usar este site, voc&ecirc; concorda em ficar vinculado &agrave; vers&atilde;o atual desses termos de servi&ccedil;o.</p>\r\n\r\n<h3>Lei aplic&aacute;vel</h3>\r\n\r\n<p>Estes termos e condi&ccedil;&otilde;es s&atilde;o regidos e interpretados de acordo com as leis do Ydeal Tec Im&oacute;veis e voc&ecirc; se submete irrevogavelmente &agrave; jurisdi&ccedil;&atilde;o exclusiva dos tribunais naquele estado ou localidade.</p>\r\n',_binary '0'),(6,'equipe','nossa-equipe','Nossa Equipe','<p>Conhe&ccedil;a nossos corretores, entre em contato!</p>\r\n',_binary '0');
/*!40000 ALTER TABLE `texto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `texto_foto`
--

DROP TABLE IF EXISTS `texto_foto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `texto_foto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_pasta` int(11) NOT NULL,
  `ordem` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `texto_foto`
--

LOCK TABLES `texto_foto` WRITE;
/*!40000 ALTER TABLE `texto_foto` DISABLE KEYS */;
/*!40000 ALTER TABLE `texto_foto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `timeline_re_registered_at_products`
--

DROP TABLE IF EXISTS `timeline_re_registered_at_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `timeline_re_registered_at_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_product` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `new_re_registered_at` date DEFAULT NULL,
  `last_re_registered_at` date DEFAULT NULL,
  `id_authorization_contract` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timeline_re_registered_at_products`
--

LOCK TABLES `timeline_re_registered_at_products` WRITE;
/*!40000 ALTER TABLE `timeline_re_registered_at_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `timeline_re_registered_at_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tokens`
--

DROP TABLE IF EXISTS `tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_unique` varchar(64) NOT NULL,
  `email` varchar(256) NOT NULL,
  `jwt` text NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tokens`
--

LOCK TABLES `tokens` WRITE;
/*!40000 ALTER TABLE `tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `type_contract`
--

DROP TABLE IF EXISTS `type_contract`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `type_contract` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `status` binary(1) NOT NULL DEFAULT '1',
  `update_at` timestamp NULL DEFAULT NULL,
  `update_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `type_contract`
--

LOCK TABLES `type_contract` WRITE;
/*!40000 ALTER TABLE `type_contract` DISABLE KEYS */;
INSERT INTO `type_contract` VALUES (1,'Contrato',_binary '1',NULL,NULL),(2,'Proposta',_binary '1',NULL,NULL),(3,'Autorização',_binary '1',NULL,NULL),(4,'Recibo',_binary '1',NULL,NULL);
/*!40000 ALTER TABLE `type_contract` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_branches`
--

DROP TABLE IF EXISTS `user_branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_branches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `id_branch` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_branches`
--

LOCK TABLES `user_branches` WRITE;
/*!40000 ALTER TABLE `user_branches` DISABLE KEYS */;
INSERT INTO `user_branches` VALUES (1,1,1);
/*!40000 ALTER TABLE `user_branches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_networks`
--

DROP TABLE IF EXISTS `user_networks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_networks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `link` text DEFAULT NULL,
  `item_order` int(11) DEFAULT NULL,
  `id_networks_card_digital` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_networks`
--

LOCK TABLES `user_networks` WRITE;
/*!40000 ALTER TABLE `user_networks` DISABLE KEYS */;
INSERT INTO `user_networks` VALUES (1,1,'https://pt-br.facebook.com/ydealtecnologia/',NULL,1),(2,1,'https://www.instagram.com/ydealtecnologia/',NULL,2),(3,1,'suporte@ydeal.net.br',NULL,3),(4,1,'48932636688',NULL,4),(5,1,'https://www.linkedin.com/company/ydeal-tecnologia/about/',NULL,5),(6,1,NULL,NULL,6);
/*!40000 ALTER TABLE `user_networks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_position`
--

DROP TABLE IF EXISTS `user_position`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `origin_commission` int(11) NOT NULL DEFAULT 1,
  `status` binary(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_position`
--

LOCK TABLES `user_position` WRITE;
/*!40000 ALTER TABLE `user_position` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_position` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_preferences`
--

DROP TABLE IF EXISTS `user_preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_preferences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_cost_center` int(11) NOT NULL,
  `by_date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_preferences`
--

LOCK TABLES `user_preferences` WRITE;
/*!40000 ALTER TABLE `user_preferences` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_preferences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_presentation_config`
--

DROP TABLE IF EXISTS `user_presentation_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_presentation_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_presentation_config` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_presentation_config`
--

LOCK TABLES `user_presentation_config` WRITE;
/*!40000 ALTER TABLE `user_presentation_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_presentation_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_profile` int(11) DEFAULT NULL,
  `id_branch` int(11) DEFAULT NULL,
  `name` varchar(256) NOT NULL DEFAULT '',
  `cpf` varchar(11) DEFAULT NULL,
  `creci` varchar(50) DEFAULT NULL,
  `email` varchar(256) NOT NULL DEFAULT '',
  `password` varchar(60) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `permission_publish` binary(1) DEFAULT '0',
  `phone` varchar(50) DEFAULT NULL,
  `profile_capa` binary(1) DEFAULT '0',
  `profile_cont` int(11) DEFAULT 0,
  `profile_ext` varchar(20) DEFAULT NULL,
  `card_digital_capa` binary(1) DEFAULT '0',
  `card_digital_cont` int(11) DEFAULT 0,
  `card_digital_ext` varchar(20) DEFAULT NULL,
  `card_digital_name` varchar(255) DEFAULT NULL,
  `card_digital_occupation` varchar(255) DEFAULT NULL,
  `card_digital` int(11) DEFAULT 1,
  `id_customer` int(11) DEFAULT NULL,
  `show_team` binary(1) DEFAULT '0',
  `color` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,5,1,'Suporte Ydeal',NULL,NULL,'suporte@ydeal.net.br','$2y$12$QNcmCCHmVNy/SHZ5oRJQl.ME5AzTiRLlALPTHh.WGav9rjtvwmktO',1,NULL,NULL,NULL,NULL,_binary '1',NULL,_binary '0',0,'',_binary '0',0,'','Ydeal Tecnologia','Desenvolvedor Web',1,NULL,_binary '0',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_profiles`
--

DROP TABLE IF EXISTS `users_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL DEFAULT '',
  `access` int(11) NOT NULL DEFAULT 30,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_profiles`
--

LOCK TABLES `users_profiles` WRITE;
/*!40000 ALTER TABLE `users_profiles` DISABLE KEYS */;
INSERT INTO `users_profiles` VALUES (1,'Superadm',5,1,NULL,NULL),(2,'Administrador',10,1,NULL,NULL),(3,'Gerente Geral',18,1,NULL,NULL),(4,'Vendedor',30,1,NULL,NULL),(5,'Desenvolvedor',1,1,NULL,NULL),(6,'Gerente de Filial',19,1,NULL,NULL),(7,'Gerente de Vendas',20,1,NULL,NULL),(8,'Secretária',25,1,NULL,NULL);
/*!40000 ALTER TABLE `users_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `variable_property_authorization_contract`
--

DROP TABLE IF EXISTS `variable_property_authorization_contract`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `variable_property_authorization_contract` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_property_authorization_contract` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `value` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `variable_property_authorization_contract`
--

LOCK TABLES `variable_property_authorization_contract` WRITE;
/*!40000 ALTER TABLE `variable_property_authorization_contract` DISABLE KEYS */;
/*!40000 ALTER TABLE `variable_property_authorization_contract` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `website_filters`
--

DROP TABLE IF EXISTS `website_filters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `website_filters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `slugify` varchar(255) DEFAULT NULL,
  `placeholder` varchar(255) DEFAULT NULL,
  `id_item` int(11) DEFAULT NULL,
  `item_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `website_filters`
--

LOCK TABLES `website_filters` WRITE;
/*!40000 ALTER TABLE `website_filters` DISABLE KEYS */;
INSERT INTO `website_filters` VALUES (8,'Tipo','tipo','Tipo',1,1),(9,'Categoria','categoria','Categoria',2,2),(10,'Cidade','cidade','Cidade',3,3),(11,'Bairro','bairro','Bairro',4,4),(12,'Nome','nome','Código, Desc, Nome, Rua...',5,5),(13,'Valor de','valor_minimo','de',6,6),(14,'Valor até','valor_maximo','ate',7,7);
/*!40000 ALTER TABLE `website_filters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'realize_repasse'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-02-22 14:18:08
