-- MySQL dump 10.13  Distrib 8.4.9, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: invocely
-- ------------------------------------------------------
-- Server version	8.4.9

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
-- Table structure for table `accounting_exports`
--

DROP TABLE IF EXISTS `accounting_exports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `accounting_exports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `format` enum('jurnal','mekari','quickbooks','xero','csv_generic') COLLATE utf8mb4_unicode_ci NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `record_count` int NOT NULL,
  `generated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `accounting_exports_generated_by_foreign` (`generated_by`),
  KEY `accounting_exports_workspace_id_created_at_index` (`workspace_id`,`created_at`),
  CONSTRAINT `accounting_exports_generated_by_foreign` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `accounting_exports_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounting_exports`
--

LOCK TABLES `accounting_exports` WRITE;
/*!40000 ALTER TABLE `accounting_exports` DISABLE KEYS */;
/*!40000 ALTER TABLE `accounting_exports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `affiliates`
--

DROP TABLE IF EXISTS `affiliates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `affiliates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `clicks` int unsigned NOT NULL DEFAULT '0',
  `signups` int unsigned NOT NULL DEFAULT '0',
  `paid_conversions` int unsigned NOT NULL DEFAULT '0',
  `commission_pct` decimal(5,2) NOT NULL DEFAULT '30.00',
  `balance_idr` decimal(12,2) NOT NULL DEFAULT '0.00',
  `balance_usd` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payout_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payout_details` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `affiliates_user_id_unique` (`user_id`),
  UNIQUE KEY `affiliates_code_unique` (`code`),
  CONSTRAINT `affiliates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `affiliates`
--

LOCK TABLES `affiliates` WRITE;
/*!40000 ALTER TABLE `affiliates` DISABLE KEYS */;
/*!40000 ALTER TABLE `affiliates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ai_feature_mapping`
--

DROP TABLE IF EXISTS `ai_feature_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_feature_mapping` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `feature` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider_id` bigint unsigned DEFAULT NULL,
  `model` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `config` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ai_feature_mapping_workspace_id_feature_unique` (`workspace_id`,`feature`),
  KEY `ai_feature_mapping_provider_id_foreign` (`provider_id`),
  KEY `ai_feature_mapping_workspace_id_index` (`workspace_id`),
  CONSTRAINT `ai_feature_mapping_provider_id_foreign` FOREIGN KEY (`provider_id`) REFERENCES `ai_providers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ai_feature_mapping_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ai_feature_mapping`
--

LOCK TABLES `ai_feature_mapping` WRITE;
/*!40000 ALTER TABLE `ai_feature_mapping` DISABLE KEYS */;
/*!40000 ALTER TABLE `ai_feature_mapping` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ai_providers`
--

DROP TABLE IF EXISTS `ai_providers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_providers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_format` enum('openai_compatible','anthropic','gemini','image_generic','ocr_generic') COLLATE utf8mb4_unicode_ci NOT NULL,
  `base_url` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_key_encrypted` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `extra_headers` json DEFAULT NULL,
  `default_model` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_pricing_input` decimal(10,6) DEFAULT NULL,
  `default_pricing_output` decimal(10,6) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ai_providers_workspace_id_is_active_index` (`workspace_id`,`is_active`),
  CONSTRAINT `ai_providers_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ai_providers`
--

LOCK TABLES `ai_providers` WRITE;
/*!40000 ALTER TABLE `ai_providers` DISABLE KEYS */;
INSERT INTO `ai_providers` VALUES (1,1,'Smoke2','openai_compatible','https://api.openai.com/v1','eyJpdiI6IjR5Zm5rYXBhUzN4NmxLaTdRdGdqSEE9PSIsInZhbHVlIjoieFJxdWJUeWNpR1lUVm5xRno3OWNTUT09IiwibWFjIjoiZjU5YTA5YzAzMWRiMjJjMjFlMzJkOWFmOTFmZDViZmVkYzNlOTQ1NDg0NTBlY2U5ZjVlMThjMGZlZTg4M2UyMiIsInRhZyI6IiJ9',NULL,'gpt-4o-mini',NULL,NULL,1,'2026-05-08 01:45:57','2026-05-08 01:45:57'),(2,1,'Smoke3','openai_compatible','https://api.openai.com/v1','eyJpdiI6IkVmWldNczIvQnlXQzVaVzNsbVB0T0E9PSIsInZhbHVlIjoiMzRQWDdMKzk5blRuT1RKRnBhOCtIQT09IiwibWFjIjoiMjM5MDc2NWU1N2VkZmYxZjlkMTQ5MGY5YTVkZTc5NTQzNGJlNjMzYWRhNWIzMTdmY2VjYjNiMmEyZmFjNmI4NCIsInRhZyI6IiJ9',NULL,NULL,NULL,NULL,1,'2026-05-08 01:46:45','2026-05-08 01:46:45');
/*!40000 ALTER TABLE `ai_providers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ai_usage_logs`
--

DROP TABLE IF EXISTS `ai_usage_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ai_usage_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `provider_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `feature` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `input_tokens` int NOT NULL DEFAULT '0',
  `output_tokens` int NOT NULL DEFAULT '0',
  `cost_estimate` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `duration_ms` int NOT NULL DEFAULT '0',
  `status` enum('success','error','timeout') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'success',
  `error_message` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ai_usage_logs_user_id_foreign` (`user_id`),
  KEY `ai_usage_logs_workspace_id_created_at_index` (`workspace_id`,`created_at`),
  KEY `ai_usage_logs_provider_id_index` (`provider_id`),
  CONSTRAINT `ai_usage_logs_provider_id_foreign` FOREIGN KEY (`provider_id`) REFERENCES `ai_providers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ai_usage_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ai_usage_logs_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ai_usage_logs`
--

LOCK TABLES `ai_usage_logs` WRITE;
/*!40000 ALTER TABLE `ai_usage_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `ai_usage_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `app_notifications`
--

DROP TABLE IF EXISTS `app_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `app_notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 0xF09F9494,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `app_notifications_user_id_foreign` (`user_id`),
  KEY `app_notifications_workspace_id_read_at_index` (`workspace_id`,`read_at`),
  CONSTRAINT `app_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `app_notifications_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_notifications`
--

LOCK TABLES `app_notifications` WRITE;
/*!40000 ALTER TABLE `app_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `app_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `app_settings`
--

DROP TABLE IF EXISTS `app_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `app_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` json DEFAULT NULL,
  `group` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `app_settings_key_unique` (`key`),
  KEY `app_settings_group_index` (`group`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_settings`
--

LOCK TABLES `app_settings` WRITE;
/*!40000 ALTER TABLE `app_settings` DISABLE KEYS */;
INSERT INTO `app_settings` VALUES (1,'branding.logo_path',NULL,'branding','2026-05-07 18:28:22','2026-05-07 18:38:32'),(2,'branding.app_name','\"Invocely\"','branding','2026-05-07 18:28:22','2026-05-07 18:28:22'),(3,'branding.tagline','\"Magical invoicing for dreamy businesses\"','branding','2026-05-07 18:28:22','2026-05-07 18:28:22'),(4,'branding.theme_preset','\"unicorn\"','branding','2026-05-07 18:28:22','2026-05-07 18:28:22'),(5,'branding.primary_color','\"#A78BFA\"','branding','2026-05-07 18:28:22','2026-05-07 18:28:22'),(6,'branding.accent_color','\"#FF7AC6\"','branding','2026-05-07 18:28:22','2026-05-07 18:28:22'),(7,'branding.sky_color','\"#7DD3FC\"','branding','2026-05-07 18:28:22','2026-05-07 18:28:22'),(8,'branding.font_display','\"Comfortaa\"','branding','2026-05-07 18:28:22','2026-05-07 18:28:22'),(9,'branding.font_body','\"Quicksand\"','branding','2026-05-07 18:28:22','2026-05-07 18:28:22'),(10,'branding.footer_text','\"✦ Magical invoicing 🦄\"','branding','2026-05-07 18:28:22','2026-05-07 18:28:22'),(11,'branding.support_email','\"support@invocely.test\"','branding','2026-05-07 18:28:22','2026-05-07 18:28:22'),(12,'branding.support_url',NULL,'branding','2026-05-07 18:28:22','2026-05-07 18:28:22');
/*!40000 ALTER TABLE `app_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `approval_requests`
--

DROP TABLE IF EXISTS `approval_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `approval_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `rule_id` bigint unsigned DEFAULT NULL,
  `subject_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `requested_by` bigint unsigned DEFAULT NULL,
  `status` enum('pending','approved','rejected','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approver_responses` json DEFAULT NULL,
  `final_decision_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approval_requests_rule_id_foreign` (`rule_id`),
  KEY `approval_requests_requested_by_foreign` (`requested_by`),
  KEY `approval_requests_workspace_id_status_index` (`workspace_id`,`status`),
  KEY `approval_requests_subject_type_subject_id_index` (`subject_type`,`subject_id`),
  CONSTRAINT `approval_requests_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `approval_requests_rule_id_foreign` FOREIGN KEY (`rule_id`) REFERENCES `approval_rules` (`id`) ON DELETE SET NULL,
  CONSTRAINT `approval_requests_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approval_requests`
--

LOCK TABLES `approval_requests` WRITE;
/*!40000 ALTER TABLE `approval_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `approval_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `approval_rules`
--

DROP TABLE IF EXISTS `approval_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `approval_rules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `applies_to` enum('invoice','quote','expense','credit_note') COLLATE utf8mb4_unicode_ci NOT NULL,
  `condition_type` enum('amount_gte','always','custom_field') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'amount_gte',
  `condition_value` decimal(12,2) DEFAULT NULL,
  `approver_user_ids` json NOT NULL,
  `require_all` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approval_rules_workspace_id_applies_to_index` (`workspace_id`,`applies_to`),
  CONSTRAINT `approval_rules_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approval_rules`
--

LOCK TABLES `approval_rules` WRITE;
/*!40000 ALTER TABLE `approval_rules` DISABLE KEYS */;
INSERT INTO `approval_rules` VALUES (1,1,'Big Invoice','invoice','amount_gte',10000000.00,'[\"1\"]',0,1,'2026-05-08 01:44:28','2026-05-08 01:44:28');
/*!40000 ALTER TABLE `approval_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `changes` json DEFAULT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  KEY `audit_logs_workspace_id_created_at_index` (`workspace_id`,`created_at`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `audit_logs_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,1,1,'projects.created','App\\Models\\Project',1,'Created Project #1',NULL,'127.0.0.1','2026-05-08 01:44:28','2026-05-08 01:44:28'),(2,1,1,'credit_notes.created','App\\Models\\CreditNote',1,'Created CreditNote #1',NULL,'127.0.0.1','2026-05-08 01:45:57','2026-05-08 01:45:57');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bank_reconcile_batches`
--

DROP TABLE IF EXISTS `bank_reconcile_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bank_reconcile_batches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `bank_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_format` enum('csv','ofx','mt940','json') COLLATE utf8mb4_unicode_ci NOT NULL,
  `imported_by` bigint unsigned DEFAULT NULL,
  `total_lines` int NOT NULL,
  `matched_count` int NOT NULL DEFAULT '0',
  `unmatched_count` int NOT NULL DEFAULT '0',
  `status` enum('processing','review','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'processing',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bank_reconcile_batches_workspace_id_foreign` (`workspace_id`),
  KEY `bank_reconcile_batches_imported_by_foreign` (`imported_by`),
  CONSTRAINT `bank_reconcile_batches_imported_by_foreign` FOREIGN KEY (`imported_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bank_reconcile_batches_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bank_reconcile_batches`
--

LOCK TABLES `bank_reconcile_batches` WRITE;
/*!40000 ALTER TABLE `bank_reconcile_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `bank_reconcile_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bank_reconcile_lines`
--

DROP TABLE IF EXISTS `bank_reconcile_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bank_reconcile_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `batch_id` bigint unsigned NOT NULL,
  `workspace_id` bigint unsigned NOT NULL,
  `trx_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `matched_invoice_id` bigint unsigned DEFAULT NULL,
  `matched_payment_id` bigint unsigned DEFAULT NULL,
  `match_confidence` enum('high','medium','low','manual','none') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `match_method` enum('auto_amount_date','auto_reference','manual','none') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `notes` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bank_reconcile_lines_matched_invoice_id_foreign` (`matched_invoice_id`),
  KEY `bank_reconcile_lines_matched_payment_id_foreign` (`matched_payment_id`),
  KEY `bank_reconcile_lines_batch_id_index` (`batch_id`),
  KEY `bank_reconcile_lines_workspace_id_trx_date_index` (`workspace_id`,`trx_date`),
  CONSTRAINT `bank_reconcile_lines_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `bank_reconcile_batches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bank_reconcile_lines_matched_invoice_id_foreign` FOREIGN KEY (`matched_invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bank_reconcile_lines_matched_payment_id_foreign` FOREIGN KEY (`matched_payment_id`) REFERENCES `payments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bank_reconcile_lines_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bank_reconcile_lines`
--

LOCK TABLES `bank_reconcile_lines` WRITE;
/*!40000 ALTER TABLE `bank_reconcile_lines` DISABLE KEYS */;
/*!40000 ALTER TABLE `bank_reconcile_lines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('invocely-cache-app_settings_all','a:12:{s:18:\"branding.logo_path\";N;s:17:\"branding.app_name\";s:8:\"Invocely\";s:16:\"branding.tagline\";s:39:\"Magical invoicing for dreamy businesses\";s:21:\"branding.theme_preset\";s:7:\"unicorn\";s:22:\"branding.primary_color\";s:7:\"#A78BFA\";s:21:\"branding.accent_color\";s:7:\"#FF7AC6\";s:18:\"branding.sky_color\";s:7:\"#7DD3FC\";s:21:\"branding.font_display\";s:9:\"Comfortaa\";s:18:\"branding.font_body\";s:9:\"Quicksand\";s:20:\"branding.footer_text\";s:26:\"✦ Magical invoicing 🦄\";s:22:\"branding.support_email\";s:21:\"support@invocely.test\";s:20:\"branding.support_url\";N;}',1778265659);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `client_portal_users`
--

DROP TABLE IF EXISTS `client_portal_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_portal_users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint unsigned NOT NULL,
  `workspace_id` bigint unsigned NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `client_portal_users_client_id_email_unique` (`client_id`,`email`),
  KEY `client_portal_users_workspace_id_index` (`workspace_id`),
  CONSTRAINT `client_portal_users_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `client_portal_users_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client_portal_users`
--

LOCK TABLES `client_portal_users` WRITE;
/*!40000 ALTER TABLE `client_portal_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `client_portal_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Indonesia',
  `postal_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_pph23_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `is_pkp` tinyint(1) NOT NULL DEFAULT '0',
  `npwp_verified` tinyint(1) NOT NULL DEFAULT '0',
  `npwp_verified_at` timestamp NULL DEFAULT NULL,
  `no_reminders` tinyint(1) NOT NULL DEFAULT '0',
  `avg_payment_days` int DEFAULT NULL,
  `payment_reliability` enum('excellent','good','fair','poor') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_invoices_count` int NOT NULL DEFAULT '0',
  `total_paid_count` int NOT NULL DEFAULT '0',
  `default_assignee_id` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `view_token` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clients_view_token_unique` (`view_token`),
  KEY `clients_workspace_id_index` (`workspace_id`),
  KEY `clients_view_token_index` (`view_token`),
  KEY `clients_default_assignee_id_foreign` (`default_assignee_id`),
  CONSTRAINT `clients_default_assignee_id_foreign` FOREIGN KEY (`default_assignee_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `clients_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
INSERT INTO `clients` VALUES (1,1,'Acme Corporation','Acme Corp','billing@acme.test','+1-555-1000','100 Market St','San Francisco','CA','United States','94110',NULL,0.00,0,0,NULL,0,NULL,NULL,0,0,NULL,NULL,'3c00eac2-8924-4217-a1d2-11abddb6785a',1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(2,1,'Jane Smith','Smith Consulting','jane@smithconsulting.test','+1-555-1001','200 Market St','San Francisco','CA','United States','94111',NULL,0.00,0,0,NULL,0,NULL,NULL,0,0,NULL,NULL,'53212f07-c6df-4c08-be82-065f6576a464',1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(3,1,'Bob Johnson','Johnson LLC','bob@johnson.test','+1-555-1002','300 Market St','San Francisco','CA','United States','94112',NULL,0.00,0,0,NULL,0,NULL,NULL,0,0,NULL,NULL,'4d4a78df-fe36-42c5-a8da-3a22599532f4',1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(4,1,'Global Tech Solutions','Global Tech Solutions','accounts@globaltech.test','+1-555-1003','400 Market St','San Francisco','CA','United States','94113',NULL,0.00,0,0,NULL,0,NULL,NULL,0,0,NULL,NULL,'f4d54621-7899-4bce-b4fd-7c70bf326a54',1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(5,1,'Creative Studios','Creative Studios Inc','hello@creativestudios.test','+1-555-1004','500 Market St','San Francisco','CA','United States','94114',NULL,0.00,0,0,NULL,0,NULL,NULL,0,0,NULL,NULL,'ae932768-7d33-4cc2-9a55-e79520b92d3b',1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(6,1,'DataFlow Analytics','DataFlow Analytics','finance@dataflow.test','+1-555-1005','600 Market St','San Francisco','CA','United States','94115',NULL,0.00,0,0,NULL,0,NULL,NULL,0,0,NULL,NULL,'7207cda1-8a0e-49f3-9245-0c7bc95d519f',1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(7,1,'GreenLeaf Organics','GreenLeaf Organics','orders@greenleaf.test','+1-555-1006','700 Market St','San Francisco','CA','United States','94116',NULL,0.00,0,0,NULL,0,NULL,NULL,0,0,NULL,NULL,'2fb6bc23-a869-49c6-a07d-9174502a2147',1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(8,1,'CloudNine Hosting','CloudNine Hosting LLC','billing@cloudnine.test','+1-555-1007','800 Market St','San Francisco','CA','United States','94117',NULL,0.00,0,0,NULL,0,NULL,NULL,0,0,NULL,NULL,'c765fc63-3be0-4d35-80c2-042468b99797',1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(9,1,'PixelPerfect Design','PixelPerfect Design','info@pixelperfect.test','+1-555-1008','900 Market St','San Francisco','CA','United States','94118',NULL,0.00,0,0,NULL,0,NULL,NULL,0,0,NULL,NULL,'01ac5e5e-bb23-45ee-b0be-a9e6e5d0d2de',1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(10,1,'BuildRight Construction','BuildRight Construction Co','accounts@buildright.test','+1-555-1009','1000 Market St','San Francisco','CA','United States','94119',NULL,0.00,0,0,NULL,0,NULL,NULL,0,0,NULL,NULL,'e9e332fc-c7b5-4b18-97e8-e93bce3dc9ed',1,'2026-05-06 01:05:55','2026-05-06 01:05:55');
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupon_redemptions`
--

DROP TABLE IF EXISTS `coupon_redemptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupon_redemptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `coupon_id` bigint unsigned NOT NULL,
  `workspace_id` bigint unsigned DEFAULT NULL,
  `subject_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `discount_applied` decimal(12,2) NOT NULL,
  `redeemed_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `coupon_redemptions_workspace_id_foreign` (`workspace_id`),
  KEY `coupon_redemptions_coupon_id_redeemed_at_index` (`coupon_id`,`redeemed_at`),
  KEY `coupon_redemptions_subject_type_subject_id_index` (`subject_type`,`subject_id`),
  CONSTRAINT `coupon_redemptions_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `coupon_redemptions_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupon_redemptions`
--

LOCK TABLES `coupon_redemptions` WRITE;
/*!40000 ALTER TABLE `coupon_redemptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `coupon_redemptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_type` enum('percent','fixed') COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `applies_to` enum('subscription','invoice','both') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'subscription',
  `min_amount` decimal(12,2) DEFAULT NULL,
  `workspace_id` bigint unsigned DEFAULT NULL,
  `allow_stack` tinyint(1) NOT NULL DEFAULT '0',
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plan_id` bigint unsigned DEFAULT NULL,
  `max_uses` int unsigned DEFAULT NULL,
  `uses_count` int unsigned NOT NULL DEFAULT '0',
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `coupons_code_unique` (`code`),
  KEY `coupons_plan_id_foreign` (`plan_id`),
  KEY `coupons_workspace_id_foreign` (`workspace_id`),
  CONSTRAINT `coupons_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE SET NULL,
  CONSTRAINT `coupons_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupons`
--

LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
INSERT INTO `coupons` VALUES (1,'SMOKE10','Smoke test','percent',10.00,'subscription',NULL,NULL,0,NULL,NULL,NULL,0,NULL,1,'2026-05-08 01:44:28','2026-05-08 01:44:28');
/*!40000 ALTER TABLE `coupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `credit_notes`
--

DROP TABLE IF EXISTS `credit_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `credit_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `invoice_id` bigint unsigned NOT NULL,
  `credit_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `issued_at` date NOT NULL,
  `status` enum('draft','issued','applied','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `credit_notes_workspace_id_credit_no_unique` (`workspace_id`,`credit_no`),
  KEY `credit_notes_invoice_id_foreign` (`invoice_id`),
  KEY `credit_notes_created_by_foreign` (`created_by`),
  KEY `credit_notes_workspace_id_issued_at_index` (`workspace_id`,`issued_at`),
  CONSTRAINT `credit_notes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `credit_notes_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `credit_notes_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `credit_notes`
--

LOCK TABLES `credit_notes` WRITE;
/*!40000 ALTER TABLE `credit_notes` DISABLE KEYS */;
INSERT INTO `credit_notes` VALUES (1,1,1,'CN-2026-001','Smoke test',1000.00,'IDR','2026-05-08','draft',NULL,1,'2026-05-08 01:45:57','2026-05-08 01:45:57');
/*!40000 ALTER TABLE `credit_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `custom_domains`
--

DROP TABLE IF EXISTS `custom_domains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `custom_domains` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `hostname` varchar(253) COLLATE utf8mb4_unicode_ci NOT NULL,
  `verification_token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `verification_method` enum('cname','txt') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','verified','failed','disabled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `ssl_issued_at` timestamp NULL DEFAULT NULL,
  `ssl_expires_at` timestamp NULL DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `last_check_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `custom_domains_hostname_unique` (`hostname`),
  KEY `custom_domains_workspace_id_index` (`workspace_id`),
  CONSTRAINT `custom_domains_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `custom_domains`
--

LOCK TABLES `custom_domains` WRITE;
/*!40000 ALTER TABLE `custom_domains` DISABLE KEYS */;
/*!40000 ALTER TABLE `custom_domains` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dashboard_widgets`
--

DROP TABLE IF EXISTS `dashboard_widgets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dashboard_widgets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `widget_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int NOT NULL DEFAULT '0',
  `size` enum('sm','md','lg','xl') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'md',
  `config` json DEFAULT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dashboard_widgets_user_id_foreign` (`user_id`),
  KEY `dashboard_widgets_workspace_id_user_id_position_index` (`workspace_id`,`user_id`,`position`),
  CONSTRAINT `dashboard_widgets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `dashboard_widgets_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dashboard_widgets`
--

LOCK TABLES `dashboard_widgets` WRITE;
/*!40000 ALTER TABLE `dashboard_widgets` DISABLE KEYS */;
INSERT INTO `dashboard_widgets` VALUES (1,1,1,'revenue_trend',0,'lg',NULL,1,'2026-05-08 01:42:59','2026-05-08 01:42:59'),(2,1,1,'aging_summary',1,'md',NULL,1,'2026-05-08 01:42:59','2026-05-08 01:42:59'),(3,1,1,'top_clients',2,'md',NULL,1,'2026-05-08 01:42:59','2026-05-08 01:42:59'),(4,1,1,'recent_invoices',3,'lg',NULL,1,'2026-05-08 01:42:59','2026-05-08 01:42:59'),(5,1,1,'recent_activity',4,'md',NULL,1,'2026-05-08 01:42:59','2026-05-08 01:42:59'),(6,1,1,'top_clients',5,'md',NULL,1,'2026-05-08 01:45:57','2026-05-08 01:45:57');
/*!40000 ALTER TABLE `dashboard_widgets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `data_export_requests`
--

DROP TABLE IF EXISTS `data_export_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `data_export_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `status` enum('pending','processing','ready','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `requested_at` timestamp NOT NULL,
  `ready_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `data_export_requests_user_id_status_index` (`user_id`,`status`),
  CONSTRAINT `data_export_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_export_requests`
--

LOCK TABLES `data_export_requests` WRITE;
/*!40000 ALTER TABLE `data_export_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_export_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_inbox_forwards`
--

DROP TABLE IF EXISTS `email_inbox_forwards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_inbox_forwards` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `inbound_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `forwarded_at` timestamp NOT NULL,
  `from_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachments_count` int NOT NULL DEFAULT '0',
  `status` enum('processing','converted','duplicate','failed','manual_review') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'processing',
  `expense_id` bigint unsigned DEFAULT NULL,
  `raw_email_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ai_extracted_data` json DEFAULT NULL,
  `error_message` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_inbox_forwards_expense_id_foreign` (`expense_id`),
  KEY `email_inbox_forwards_workspace_id_status_index` (`workspace_id`,`status`),
  CONSTRAINT `email_inbox_forwards_expense_id_foreign` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `email_inbox_forwards_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_inbox_forwards`
--

LOCK TABLES `email_inbox_forwards` WRITE;
/*!40000 ALTER TABLE `email_inbox_forwards` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_inbox_forwards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_logs`
--

DROP TABLE IF EXISTS `email_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `invoice_id` bigint unsigned NOT NULL,
  `to_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci,
  `status` enum('sent','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sent',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `sent_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_logs_workspace_id_foreign` (`workspace_id`),
  KEY `email_logs_invoice_id_index` (`invoice_id`),
  CONSTRAINT `email_logs_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `email_logs_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_logs`
--

LOCK TABLES `email_logs` WRITE;
/*!40000 ALTER TABLE `email_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expense_categories`
--

DROP TABLE IF EXISTS `expense_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expense_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `expense_categories_workspace_id_name_unique` (`workspace_id`,`name`),
  CONSTRAINT `expense_categories_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expense_categories`
--

LOCK TABLES `expense_categories` WRITE;
/*!40000 ALTER TABLE `expense_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `expense_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `client_id` bigint unsigned DEFAULT NULL,
  `project_id` bigint unsigned DEFAULT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IDR',
  `expense_date` date NOT NULL,
  `receipt_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billable` tinyint(1) NOT NULL DEFAULT '0',
  `markup_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `vendor_name` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ocr_status` enum('not_ocr','processing','done','failed') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ocr_extracted` json DEFAULT NULL,
  `ocr_provider_id` bigint unsigned DEFAULT NULL,
  `ai_category_suggested` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ai_category_confidence` decimal(4,2) DEFAULT NULL,
  `invoiced` tinyint(1) NOT NULL DEFAULT '0',
  `invoice_id` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expenses_client_id_foreign` (`client_id`),
  KEY `expenses_invoice_id_foreign` (`invoice_id`),
  KEY `expenses_workspace_id_expense_date_index` (`workspace_id`,`expense_date`),
  KEY `expenses_project_id_foreign` (`project_id`),
  KEY `expenses_ocr_provider_id_foreign` (`ocr_provider_id`),
  CONSTRAINT `expenses_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_ocr_provider_id_foreign` FOREIGN KEY (`ocr_provider_id`) REFERENCES `ai_providers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `external_oauth_identities`
--

DROP TABLE IF EXISTS `external_oauth_identities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `external_oauth_identities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `provider` enum('google','microsoft','apple','github') COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider_user_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token_encrypted` text COLLATE utf8mb4_unicode_ci,
  `linked_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `external_oauth_identities_provider_provider_user_id_unique` (`provider`,`provider_user_id`),
  KEY `external_oauth_identities_user_id_index` (`user_id`),
  CONSTRAINT `external_oauth_identities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `external_oauth_identities`
--

LOCK TABLES `external_oauth_identities` WRITE;
/*!40000 ALTER TABLE `external_oauth_identities` DISABLE KEYS */;
/*!40000 ALTER TABLE `external_oauth_identities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_attachments`
--

DROP TABLE IF EXISTS `invoice_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_attachments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint unsigned NOT NULL,
  `workspace_id` bigint unsigned NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` int unsigned NOT NULL DEFAULT '0',
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_attachments_workspace_id_foreign` (`workspace_id`),
  KEY `invoice_attachments_invoice_id_index` (`invoice_id`),
  CONSTRAINT `invoice_attachments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoice_attachments_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_attachments`
--

LOCK TABLES `invoice_attachments` WRITE;
/*!40000 ALTER TABLE `invoice_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_comments`
--

DROP TABLE IF EXISTS `invoice_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `invoice_id` bigint unsigned NOT NULL,
  `author_user_id` bigint unsigned DEFAULT NULL,
  `author_client_id` bigint unsigned DEFAULT NULL,
  `author_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_internal` tinyint(1) NOT NULL DEFAULT '0',
  `parent_id` bigint unsigned DEFAULT NULL,
  `attachments_json` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_comments_workspace_id_foreign` (`workspace_id`),
  KEY `invoice_comments_author_user_id_foreign` (`author_user_id`),
  KEY `invoice_comments_author_client_id_foreign` (`author_client_id`),
  KEY `invoice_comments_parent_id_foreign` (`parent_id`),
  KEY `invoice_comments_invoice_id_created_at_index` (`invoice_id`,`created_at`),
  CONSTRAINT `invoice_comments_author_client_id_foreign` FOREIGN KEY (`author_client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoice_comments_author_user_id_foreign` FOREIGN KEY (`author_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoice_comments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoice_comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `invoice_comments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoice_comments_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_comments`
--

LOCK TABLES `invoice_comments` WRITE;
/*!40000 ALTER TABLE `invoice_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_items`
--

DROP TABLE IF EXISTS `invoice_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT '1.00',
  `unit_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `discount_type` enum('fixed','percent','none') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `discount_value` decimal(15,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_items_product_id_foreign` (`product_id`),
  KEY `invoice_items_invoice_id_index` (`invoice_id`),
  CONSTRAINT `invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoice_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_items`
--

LOCK TABLES `invoice_items` WRITE;
/*!40000 ALTER TABLE `invoice_items` DISABLE KEYS */;
INSERT INTO `invoice_items` VALUES (5,2,3,'SEO Consultation',5.00,150.00,0.00,0.00,'none',0.00,750.00,750.00,1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(6,2,2,'UI/UX Design - Hourly',5.00,75.00,0.00,0.00,'none',0.00,375.00,375.00,2,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(7,2,8,'Mobile App Development - Hourly',1.00,95.00,0.00,0.00,'none',0.00,95.00,95.00,3,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(8,3,11,'Database Optimization',1.00,250.00,0.00,0.00,'none',0.00,250.00,250.00,1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(9,3,2,'UI/UX Design - Hourly',4.00,75.00,0.00,0.00,'none',0.00,300.00,300.00,2,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(10,3,1,'Web Development - Hourly',3.00,85.00,0.00,0.00,'none',0.00,255.00,255.00,3,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(11,3,5,'Server Maintenance - Monthly',5.00,200.00,0.00,0.00,'none',0.00,1000.00,1000.00,4,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(12,4,6,'Logo Design',2.00,350.00,0.00,0.00,'none',0.00,700.00,700.00,1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(13,4,14,'Technical Support - Hourly',4.00,55.00,0.00,0.00,'none',0.00,220.00,220.00,2,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(14,5,9,'Photo Editing - per image',3.00,15.00,0.00,0.00,'none',0.00,45.00,45.00,1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(15,5,4,'Content Writing - per 1000 words',4.00,50.00,0.00,0.00,'none',0.00,200.00,200.00,2,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(16,6,5,'Server Maintenance - Monthly',5.00,200.00,0.00,0.00,'none',0.00,1000.00,1000.00,1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(17,6,1,'Web Development - Hourly',4.00,85.00,0.00,0.00,'none',0.00,340.00,340.00,2,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(18,6,3,'SEO Consultation',4.00,150.00,0.00,0.00,'none',0.00,600.00,600.00,3,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(19,6,5,'Server Maintenance - Monthly',4.00,200.00,0.00,0.00,'none',0.00,800.00,800.00,4,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(20,7,14,'Technical Support - Hourly',4.00,55.00,0.00,0.00,'none',0.00,220.00,220.00,1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(21,7,6,'Logo Design',5.00,350.00,0.00,0.00,'none',0.00,1750.00,1750.00,2,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(22,7,3,'SEO Consultation',1.00,150.00,0.00,0.00,'none',0.00,150.00,150.00,3,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(23,8,13,'WordPress Plugin Development',4.00,1200.00,0.00,0.00,'none',0.00,4800.00,4800.00,1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(24,8,7,'Email Marketing Setup',1.00,500.00,0.00,0.00,'none',0.00,500.00,500.00,2,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(25,8,11,'Database Optimization',4.00,250.00,0.00,0.00,'none',0.00,1000.00,1000.00,3,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(26,9,1,'Web Development - Hourly',1.00,85.00,0.00,0.00,'none',0.00,85.00,85.00,1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(27,9,14,'Technical Support - Hourly',2.00,55.00,0.00,0.00,'none',0.00,110.00,110.00,2,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(28,10,12,'Video Editing - Hourly',2.00,65.00,0.00,0.00,'none',0.00,130.00,130.00,1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(29,10,10,'Social Media Management - Monthly',1.00,600.00,0.00,0.00,'none',0.00,600.00,600.00,2,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(30,10,8,'Mobile App Development - Hourly',1.00,95.00,0.00,0.00,'none',0.00,95.00,95.00,3,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(31,11,14,'Technical Support - Hourly',2.00,55.00,0.00,0.00,'none',0.00,110.00,110.00,1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(32,12,7,'Email Marketing Setup',5.00,500.00,0.00,0.00,'none',0.00,2500.00,2500.00,1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(33,13,3,'SEO Consultation',5.00,150.00,0.00,0.00,'none',0.00,750.00,750.00,1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(34,13,7,'Email Marketing Setup',4.00,500.00,0.00,0.00,'none',0.00,2000.00,2000.00,2,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(35,13,1,'Web Development - Hourly',1.00,85.00,0.00,0.00,'none',0.00,85.00,85.00,3,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(36,14,7,'Email Marketing Setup',3.00,500.00,0.00,0.00,'none',0.00,1500.00,1500.00,1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(37,14,8,'Mobile App Development - Hourly',2.00,95.00,0.00,0.00,'none',0.00,190.00,190.00,2,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(38,14,8,'Mobile App Development - Hourly',2.00,95.00,0.00,0.00,'none',0.00,190.00,190.00,3,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(39,15,6,'Logo Design',3.00,350.00,0.00,0.00,'none',0.00,1050.00,1050.00,1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(40,15,1,'Web Development - Hourly',2.00,85.00,0.00,0.00,'none',0.00,170.00,170.00,2,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(41,16,10,'Social Media Management - Monthly',4.00,600.00,0.00,0.00,'none',0.00,2400.00,2400.00,1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(42,16,5,'Server Maintenance - Monthly',5.00,200.00,0.00,0.00,'none',0.00,1000.00,1000.00,2,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(43,17,11,'Database Optimization',3.00,250.00,0.00,0.00,'none',0.00,750.00,750.00,1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(44,18,7,'Email Marketing Setup',3.00,500.00,0.00,0.00,'none',0.00,1500.00,1500.00,1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(45,18,12,'Video Editing - Hourly',4.00,65.00,0.00,0.00,'none',0.00,260.00,260.00,2,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(46,19,7,'Email Marketing Setup',2.00,500.00,0.00,0.00,'none',0.00,1000.00,1000.00,1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(47,19,15,'Cloud Migration Service',4.00,1500.00,0.00,0.00,'none',0.00,6000.00,6000.00,2,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(48,19,8,'Mobile App Development - Hourly',3.00,95.00,0.00,0.00,'none',0.00,285.00,285.00,3,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(49,20,15,'Cloud Migration Service',3.00,1500.00,0.00,0.00,'none',0.00,4500.00,4500.00,1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(50,20,14,'Technical Support - Hourly',2.00,55.00,0.00,0.00,'none',0.00,110.00,110.00,2,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(51,1,12,'Video Editing - Hourly',2.00,65.00,0.00,0.00,'none',0.00,130.00,130.00,0,'2026-05-06 21:37:28','2026-05-06 21:37:28'),(52,1,15,'Cloud Migration Service',2.00,1500.00,0.00,0.00,'none',0.00,3000.00,3000.00,1,'2026-05-06 21:37:28','2026-05-06 21:37:28'),(53,1,2,'UI/UX Design - Hourly',2.00,75.00,0.00,0.00,'none',0.00,150.00,150.00,2,'2026-05-06 21:37:28','2026-05-06 21:37:28'),(54,1,8,'Mobile App Development - Hourly',5.00,95.00,0.00,0.00,'none',0.00,475.00,475.00,3,'2026-05-06 21:37:28','2026-05-06 21:37:28'),(55,21,6,'Logo Design',2.00,350.00,0.00,0.00,'none',0.00,700.00,700.00,1,'2026-05-07 01:59:59','2026-05-07 01:59:59'),(56,21,14,'Technical Support - Hourly',4.00,55.00,0.00,0.00,'none',0.00,220.00,220.00,2,'2026-05-07 01:59:59','2026-05-07 01:59:59');
/*!40000 ALTER TABLE `invoice_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_late_fees`
--

DROP TABLE IF EXISTS `invoice_late_fees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_late_fees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `invoice_id` bigint unsigned NOT NULL,
  `applied_at` timestamp NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `type` enum('percent','fixed') COLLATE utf8mb4_unicode_ci NOT NULL,
  `percent_value` decimal(5,2) DEFAULT NULL,
  `fixed_amount` decimal(12,2) DEFAULT NULL,
  `description` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Late fee',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_late_fees_workspace_id_foreign` (`workspace_id`),
  KEY `invoice_late_fees_invoice_id_index` (`invoice_id`),
  CONSTRAINT `invoice_late_fees_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoice_late_fees_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_late_fees`
--

LOCK TABLES `invoice_late_fees` WRITE;
/*!40000 ALTER TABLE `invoice_late_fees` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice_late_fees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_layouts`
--

DROP TABLE IF EXISTS `invoice_layouts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_layouts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `blocks` json NOT NULL,
  `settings` json DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_layouts_workspace_id_is_default_index` (`workspace_id`,`is_default`),
  CONSTRAINT `invoice_layouts_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_layouts`
--

LOCK TABLES `invoice_layouts` WRITE;
/*!40000 ALTER TABLE `invoice_layouts` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice_layouts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_public_views`
--

DROP TABLE IF EXISTS `invoice_public_views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_public_views` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint unsigned NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `viewed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `invoice_public_views_invoice_id_index` (`invoice_id`),
  CONSTRAINT `invoice_public_views_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_public_views`
--

LOCK TABLES `invoice_public_views` WRITE;
/*!40000 ALTER TABLE `invoice_public_views` DISABLE KEYS */;
INSERT INTO `invoice_public_views` VALUES (1,1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-06 21:36:32'),(2,1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-06 21:37:41'),(3,1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-06 22:20:23');
/*!40000 ALTER TABLE `invoice_public_views` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_reminders`
--

DROP TABLE IF EXISTS `invoice_reminders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_reminders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `invoice_id` bigint unsigned NOT NULL,
  `schedule_offset` int NOT NULL,
  `scheduled_at` timestamp NOT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `template_subject` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','sent','failed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `fail_reason` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_reminders_workspace_id_status_scheduled_at_index` (`workspace_id`,`status`,`scheduled_at`),
  KEY `invoice_reminders_invoice_id_index` (`invoice_id`),
  CONSTRAINT `invoice_reminders_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoice_reminders_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_reminders`
--

LOCK TABLES `invoice_reminders` WRITE;
/*!40000 ALTER TABLE `invoice_reminders` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice_reminders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_tax_details`
--

DROP TABLE IF EXISTS `invoice_tax_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_tax_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `invoice_id` bigint unsigned NOT NULL,
  `ppn_rate` decimal(5,2) NOT NULL DEFAULT '11.00',
  `ppn_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `pph23_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `pph23_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `pph23_object_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `efaktur_status` enum('not_applicable','draft','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'not_applicable',
  `efaktur_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `efaktur_qr_code` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `efaktur_xml_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `coretax_response_json` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_tax_details_invoice_id_unique` (`invoice_id`),
  KEY `invoice_tax_details_workspace_id_efaktur_status_index` (`workspace_id`,`efaktur_status`),
  CONSTRAINT `invoice_tax_details_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoice_tax_details_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_tax_details`
--

LOCK TABLES `invoice_tax_details` WRITE;
/*!40000 ALTER TABLE `invoice_tax_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice_tax_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_templates`
--

DROP TABLE IF EXISTS `invoice_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `preview_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `config` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_templates_slug_workspace_id_unique` (`slug`,`workspace_id`),
  KEY `invoice_templates_workspace_id_foreign` (`workspace_id`),
  CONSTRAINT `invoice_templates_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_templates`
--

LOCK TABLES `invoice_templates` WRITE;
/*!40000 ALTER TABLE `invoice_templates` DISABLE KEYS */;
INSERT INTO `invoice_templates` VALUES (1,NULL,'Classic','classic',1,NULL,'{\"layout\": \"traditional\", \"font_family\": \"Helvetica Neue, Helvetica, Arial, sans-serif\", \"table_style\": \"striped-header\", \"header_style\": \"branded\"}','2026-05-06 01:05:54','2026-05-06 01:05:54'),(2,NULL,'Modern','modern',0,NULL,'{\"layout\": \"card-based\", \"font_family\": \"Figtree, sans-serif\", \"table_style\": \"borderless\", \"header_style\": \"minimal\"}','2026-05-06 01:05:54','2026-05-06 01:05:54'),(3,NULL,'Minimal','minimal',0,NULL,'{\"layout\": \"single-column\", \"font_family\": \"Inter, sans-serif\", \"table_style\": \"simple-lines\", \"header_style\": \"text-only\"}','2026-05-06 01:05:54','2026-05-06 01:05:54'),(4,NULL,'Bold','bold',0,NULL,'{\"layout\": \"full-width\", \"font_family\": \"Montserrat, sans-serif\", \"table_style\": \"accent-rows\", \"header_style\": \"dark-banner\"}','2026-05-06 01:05:54','2026-05-06 01:05:54'),(5,NULL,'Elegant','elegant',0,NULL,'{\"layout\": \"framed\", \"font_family\": \"Georgia, Times New Roman, serif\", \"table_style\": \"thin-borders\", \"header_style\": \"centered-brand\"}','2026-05-06 01:05:54','2026-05-06 01:05:54');
/*!40000 ALTER TABLE `invoice_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_translations`
--

DROP TABLE IF EXISTS `invoice_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_translations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint unsigned NOT NULL,
  `locale` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fields` json NOT NULL,
  `generated_by_provider_id` bigint unsigned DEFAULT NULL,
  `translated_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_translations_invoice_id_locale_unique` (`invoice_id`,`locale`),
  KEY `invoice_translations_generated_by_provider_id_foreign` (`generated_by_provider_id`),
  CONSTRAINT `invoice_translations_generated_by_provider_id_foreign` FOREIGN KEY (`generated_by_provider_id`) REFERENCES `ai_providers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoice_translations_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_translations`
--

LOCK TABLES `invoice_translations` WRITE;
/*!40000 ALTER TABLE `invoice_translations` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `client_id` bigint unsigned NOT NULL,
  `project_id` bigint unsigned DEFAULT NULL,
  `quote_id` bigint unsigned DEFAULT NULL,
  `invoice_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('draft','sent','paid','partial','overdue','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `is_locked` tinyint(1) NOT NULL DEFAULT '0',
  `recurring_invoice_id` bigint unsigned DEFAULT NULL,
  `template` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'classic',
  `invoice_layout_id` bigint unsigned DEFAULT NULL,
  `custom_layout` json DEFAULT NULL,
  `primary_color` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accent_color` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issue_date` date NOT NULL,
  `due_date` date NOT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `pph23_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `pph23_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount_type` enum('fixed','percent','none') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `discount_value` decimal(15,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `late_fee_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `amount_paid` decimal(15,2) NOT NULL DEFAULT '0.00',
  `balance_due` decimal(15,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IDR',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `terms` text COLLATE utf8mb4_unicode_ci,
  `public_token` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `signed_at` timestamp NULL DEFAULT NULL,
  `late_risk_score` tinyint DEFAULT NULL,
  `late_risk_factors` json DEFAULT NULL,
  `assigned_to` bigint unsigned DEFAULT NULL,
  `late_fee_policy` enum('none','one_time','monthly') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `late_fee_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `late_fee_type` enum('percent','fixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percent',
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoices_workspace_id_invoice_no_unique` (`workspace_id`,`invoice_no`),
  UNIQUE KEY `invoices_public_token_unique` (`public_token`),
  KEY `invoices_recurring_invoice_id_foreign` (`recurring_invoice_id`),
  KEY `invoices_workspace_id_status_index` (`workspace_id`,`status`),
  KEY `invoices_client_id_index` (`client_id`),
  KEY `invoices_public_token_index` (`public_token`),
  KEY `invoices_due_date_status_index` (`due_date`,`status`),
  KEY `invoices_is_locked_index` (`is_locked`),
  KEY `invoices_invoice_layout_id_foreign` (`invoice_layout_id`),
  KEY `invoices_project_id_foreign` (`project_id`),
  KEY `invoices_quote_id_foreign` (`quote_id`),
  KEY `invoices_assigned_to_foreign` (`assigned_to`),
  CONSTRAINT `invoices_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoices_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `invoices_invoice_layout_id_foreign` FOREIGN KEY (`invoice_layout_id`) REFERENCES `invoice_layouts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoices_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoices_quote_id_foreign` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoices_recurring_invoice_id_foreign` FOREIGN KEY (`recurring_invoice_id`) REFERENCES `recurring_invoices` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoices_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
INSERT INTO `invoices` VALUES (1,1,8,NULL,NULL,'INV-20260506-0001','draft',0,NULL,'classic',NULL,NULL,'#a78bfa','#ff7ac6','2026-03-23','2026-04-22',3755.00,10.00,375.50,0.00,0.00,'none',0.00,0.00,4130.50,0.00,0.00,4130.50,'IDR','Thank you for your business!','Payment is due within 30 days. Late payments may incur a 2% monthly fee.','5323db6c-209c-46b6-9a14-033e2686e837',NULL,'2026-05-06 01:05:55','2026-05-06 21:37:28',NULL,NULL,NULL,NULL,'none',0.00,'percent'),(2,1,1,NULL,NULL,'INV-20260506-0002','draft',0,NULL,'classic',NULL,NULL,NULL,NULL,'2026-04-25','2026-05-25',1220.00,10.00,122.00,0.00,0.00,'fixed',31.00,31.00,1311.00,0.00,0.00,1311.00,'USD','Thank you for your business!','Payment is due within 30 days. Late payments may incur a 2% monthly fee.','a7684fa4-8f04-43e7-8c26-ba208c9af97c',NULL,'2026-05-06 01:05:55','2026-05-06 01:05:55',NULL,NULL,NULL,NULL,'none',0.00,'percent'),(3,1,3,NULL,NULL,'INV-20260506-0003','draft',0,NULL,'classic',NULL,NULL,NULL,NULL,'2026-03-08','2026-04-07',1805.00,10.00,180.50,0.00,0.00,'none',0.00,0.00,1985.50,0.00,0.00,1985.50,'USD','Thank you for your business!','Payment is due within 30 days. Late payments may incur a 2% monthly fee.','10a43c5c-703e-4156-bccc-b626638a14db',NULL,'2026-05-06 01:05:55','2026-05-06 01:05:55',NULL,NULL,NULL,NULL,'none',0.00,'percent'),(4,1,4,NULL,NULL,'INV-20260506-0004','sent',0,NULL,'classic',NULL,NULL,NULL,NULL,'2026-05-04','2026-06-03',920.00,10.00,92.00,0.00,0.00,'none',0.00,0.00,1012.00,0.00,0.00,1012.00,'USD','Thank you for your business!','Payment is due within 30 days. Late payments may incur a 2% monthly fee.','7149826c-dd84-4982-997f-8749d3ef6ae7',NULL,'2026-05-06 01:05:55','2026-05-06 01:05:55',NULL,NULL,NULL,NULL,'none',0.00,'percent'),(5,1,2,NULL,NULL,'INV-20260506-0005','sent',0,NULL,'classic',NULL,NULL,NULL,NULL,'2026-03-19','2026-04-18',245.00,10.00,24.50,0.00,0.00,'percent',10.00,24.50,245.00,0.00,0.00,245.00,'USD','Thank you for your business!','Payment is due within 30 days. Late payments may incur a 2% monthly fee.','58ad4a15-d60d-44ef-8415-627525a01000',NULL,'2026-05-06 01:05:55','2026-05-06 01:05:55',NULL,NULL,NULL,NULL,'none',0.00,'percent'),(6,1,8,NULL,NULL,'INV-20260506-0006','sent',0,NULL,'classic',NULL,NULL,NULL,NULL,'2026-04-05','2026-05-05',2740.00,10.00,274.00,0.00,0.00,'none',0.00,0.00,3014.00,0.00,0.00,3014.00,'USD','Thank you for your business!','Payment is due within 30 days. Late payments may incur a 2% monthly fee.','be4b53be-5b21-499c-957c-9bf9aa98ba98',NULL,'2026-05-06 01:05:55','2026-05-06 01:05:55',NULL,NULL,NULL,NULL,'none',0.00,'percent'),(7,1,10,NULL,NULL,'INV-20260506-0007','sent',0,NULL,'classic',NULL,NULL,NULL,NULL,'2026-04-13','2026-05-13',2120.00,10.00,212.00,0.00,0.00,'percent',14.00,296.80,2035.20,0.00,0.00,2035.20,'USD','Thank you for your business!','Payment is due within 30 days. Late payments may incur a 2% monthly fee.','3fae02be-bfa0-46bf-86bc-86be2596b6b7',NULL,'2026-05-06 01:05:55','2026-05-06 01:05:55',NULL,NULL,NULL,NULL,'none',0.00,'percent'),(8,1,2,NULL,NULL,'INV-20260506-0008','sent',0,NULL,'classic',NULL,NULL,NULL,NULL,'2026-04-12','2026-05-12',6300.00,10.00,630.00,0.00,0.00,'none',0.00,0.00,6930.00,0.00,0.00,6930.00,'USD','Thank you for your business!','Payment is due within 30 days. Late payments may incur a 2% monthly fee.','7ad74b0d-6d1f-4201-bad8-6a52d21a129c',NULL,'2026-05-06 01:05:55','2026-05-06 01:05:55',NULL,NULL,NULL,NULL,'none',0.00,'percent'),(9,1,1,NULL,NULL,'INV-20260506-0009','paid',0,NULL,'classic',NULL,NULL,NULL,NULL,'2026-03-11','2026-04-10',195.00,10.00,19.50,0.00,0.00,'none',0.00,0.00,214.50,0.00,214.50,0.00,'USD','Thank you for your business!','Payment is due within 30 days. Late payments may incur a 2% monthly fee.','7e99aa97-2a47-422a-9a4d-41e8ad8765f7',NULL,'2026-05-06 01:05:55','2026-05-06 01:05:55',NULL,NULL,NULL,NULL,'none',0.00,'percent'),(10,1,1,NULL,NULL,'INV-20260506-0010','paid',0,NULL,'classic',NULL,NULL,NULL,NULL,'2026-04-29','2026-05-29',825.00,10.00,82.50,0.00,0.00,'fixed',44.00,44.00,863.50,0.00,863.50,0.00,'USD','Thank you for your business!','Payment is due within 30 days. Late payments may incur a 2% monthly fee.','b7f886ba-c852-4950-88d2-354668e5a225',NULL,'2026-05-06 01:05:55','2026-05-06 01:05:55',NULL,NULL,NULL,NULL,'none',0.00,'percent'),(11,1,3,NULL,NULL,'INV-20260506-0011','paid',0,NULL,'classic',NULL,NULL,NULL,NULL,'2026-03-07','2026-04-06',110.00,10.00,11.00,0.00,0.00,'none',0.00,0.00,121.00,0.00,121.00,0.00,'USD','Thank you for your business!','Payment is due within 30 days. Late payments may incur a 2% monthly fee.','341351ed-d097-4b26-abae-0561b3b4a541',NULL,'2026-05-06 01:05:55','2026-05-06 01:05:55',NULL,NULL,NULL,NULL,'none',0.00,'percent'),(12,1,6,NULL,NULL,'INV-20260506-0012','paid',0,NULL,'classic',NULL,NULL,NULL,NULL,'2026-04-01','2026-05-01',2500.00,10.00,250.00,0.00,0.00,'none',0.00,0.00,2750.00,0.00,2750.00,0.00,'USD','Thank you for your business!','Payment is due within 30 days. Late payments may incur a 2% monthly fee.','81832120-beba-4fa7-ad81-285675f73907',NULL,'2026-05-06 01:05:55','2026-05-06 01:05:55',NULL,NULL,NULL,NULL,'none',0.00,'percent'),(13,1,5,NULL,NULL,'INV-20260506-0013','paid',0,NULL,'classic',NULL,NULL,NULL,NULL,'2026-03-23','2026-04-22',2835.00,10.00,283.50,0.00,0.00,'none',0.00,0.00,3118.50,0.00,3118.50,0.00,'USD','Thank you for your business!','Payment is due within 30 days. Late payments may incur a 2% monthly fee.','17f2a2ee-3b23-4c1b-9282-961fce5637ba',NULL,'2026-05-06 01:05:55','2026-05-06 01:05:55',NULL,NULL,NULL,NULL,'none',0.00,'percent'),(14,1,10,NULL,NULL,'INV-20260506-0014','overdue',0,NULL,'classic',NULL,NULL,NULL,NULL,'2026-03-31','2026-04-30',1880.00,10.00,188.00,0.00,0.00,'none',0.00,0.00,2068.00,0.00,0.00,2068.00,'USD','Thank you for your business!','Payment is due within 30 days. Late payments may incur a 2% monthly fee.','3190d751-a0e4-49dd-b4e4-adbe366ebd81',NULL,'2026-05-06 01:05:55','2026-05-06 01:05:55',NULL,NULL,NULL,NULL,'none',0.00,'percent'),(15,1,5,NULL,NULL,'INV-20260506-0015','sent',0,NULL,'classic',NULL,NULL,NULL,NULL,'2026-04-02','2026-05-02',1220.00,10.00,122.00,0.00,0.00,'none',0.00,0.00,1342.00,0.00,0.00,1342.00,'USD','Thank you for your business!','Payment is due within 30 days. Late payments may incur a 2% monthly fee.','34c9d98c-182c-4c59-b00f-4a359f29da47',NULL,'2026-05-06 01:05:55','2026-05-06 01:05:55',NULL,NULL,NULL,NULL,'none',0.00,'percent'),(16,1,7,NULL,NULL,'INV-20260506-0016','sent',0,NULL,'classic',NULL,NULL,NULL,NULL,'2026-03-14','2026-04-13',3400.00,10.00,340.00,0.00,0.00,'percent',9.00,306.00,3434.00,0.00,0.00,3434.00,'USD','Thank you for your business!','Payment is due within 30 days. Late payments may incur a 2% monthly fee.','0210fdca-0536-493d-8374-1dd4b896c534',NULL,'2026-05-06 01:05:55','2026-05-06 01:05:55',NULL,NULL,NULL,NULL,'none',0.00,'percent'),(17,1,8,NULL,NULL,'INV-20260506-0017','overdue',0,NULL,'classic',NULL,NULL,NULL,NULL,'2026-04-02','2026-05-02',750.00,10.00,75.00,0.00,0.00,'none',0.00,0.00,825.00,0.00,0.00,825.00,'USD','Thank you for your business!','Payment is due within 30 days. Late payments may incur a 2% monthly fee.','8e1acc69-219f-4096-be8d-7ecfff31d7bf',NULL,'2026-05-06 01:05:55','2026-05-06 01:05:55',NULL,NULL,NULL,NULL,'none',0.00,'percent'),(18,1,10,NULL,NULL,'INV-20260506-0018','overdue',0,NULL,'classic',NULL,NULL,NULL,NULL,'2026-03-10','2026-04-09',1760.00,10.00,176.00,0.00,0.00,'none',0.00,0.00,1936.00,0.00,0.00,1936.00,'USD','Thank you for your business!','Payment is due within 30 days. Late payments may incur a 2% monthly fee.','4e7980c9-20ea-4c91-a90e-08f84d887ec1',NULL,'2026-05-06 01:05:55','2026-05-06 01:05:55',NULL,NULL,NULL,NULL,'none',0.00,'percent'),(19,1,1,NULL,NULL,'INV-20260506-0019','cancelled',0,NULL,'classic',NULL,NULL,NULL,NULL,'2026-04-13','2026-05-13',7285.00,10.00,728.50,0.00,0.00,'fixed',38.00,38.00,7975.50,0.00,0.00,7975.50,'USD','Thank you for your business!','Payment is due within 30 days. Late payments may incur a 2% monthly fee.','c41263df-a83d-4e07-9e93-7ba97f1f5961',NULL,'2026-05-06 01:05:55','2026-05-06 01:05:55',NULL,NULL,NULL,NULL,'none',0.00,'percent'),(20,1,5,NULL,NULL,'INV-20260506-0020','draft',0,NULL,'classic',NULL,NULL,NULL,NULL,'2026-04-28','2026-05-28',4610.00,10.00,461.00,0.00,0.00,'none',0.00,0.00,5071.00,0.00,0.00,5071.00,'USD','Thank you for your business!','Payment is due within 30 days. Late payments may incur a 2% monthly fee.','79501d80-63a4-4d59-afdc-ab8c5303f18b',NULL,'2026-05-06 01:05:55','2026-05-06 01:05:55',NULL,NULL,NULL,NULL,'none',0.00,'percent'),(21,1,4,NULL,NULL,'INV-20260507-0021','draft',0,NULL,'classic',NULL,NULL,NULL,NULL,'2026-05-04','2026-06-03',920.00,10.00,92.00,0.00,0.00,'none',0.00,0.00,1012.00,0.00,0.00,1012.00,'USD','Thank you for your business!','Payment is due within 30 days. Late payments may incur a 2% monthly fee.','978f2321-8a0f-4a2f-9347-17e37e2e323e',NULL,'2026-05-07 01:59:59','2026-05-07 01:59:59',NULL,NULL,NULL,NULL,'none',0.00,'percent');
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manual_payment_methods`
--

DROP TABLE IF EXISTS `manual_payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manual_payment_methods` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_or_provider` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instructions` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manual_payment_methods`
--

LOCK TABLES `manual_payment_methods` WRITE;
/*!40000 ALTER TABLE `manual_payment_methods` DISABLE KEYS */;
/*!40000 ALTER TABLE `manual_payment_methods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_05_06_072121_create_workspaces_table',1),(5,'2026_05_06_072122_create_clients_table',1),(6,'2026_05_06_072122_create_products_table',1),(7,'2026_05_06_072122_create_workspace_user_table',1),(8,'2026_05_06_072123_create_invoice_templates_table',1),(9,'2026_05_06_072123_create_recurring_invoices_table',1),(10,'2026_05_06_072124_create_invoices_table',1),(11,'2026_05_06_072125_create_invoice_items_table',1),(12,'2026_05_06_072126_create_payments_table',1),(13,'2026_05_06_072127_create_email_logs_table',1),(14,'2026_05_06_072128_create_invoice_public_views_table',1),(15,'2026_05_07_000001_create_payment_gateways_table',2),(16,'2026_05_07_000002_change_payment_method_to_string',2),(17,'2026_05_07_120000_create_plans_table',3),(18,'2026_05_07_120001_create_subscriptions_table',3),(19,'2026_05_07_120002_add_superadmin_to_users',3),(20,'2026_05_07_120003_add_recurring_to_payment_gateways',3),(21,'2026_05_07_120004_add_layout_to_invoices',3),(22,'2026_05_07_120005_create_invoice_layouts_table',4),(23,'2026_05_07_120006_create_app_settings_table',5),(24,'2026_05_07_130000_mega_features_batch',6),(25,'2026_05_07_140000_add_manual_payment_to_subscriptions',7),(26,'2026_05_08_100000_roadmap_v2_batch',8),(27,'2026_05_08_140000_add_whatsapp_config_to_workspaces',9);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nps_responses`
--

DROP TABLE IF EXISTS `nps_responses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nps_responses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `invoice_id` bigint unsigned DEFAULT NULL,
  `client_id` bigint unsigned DEFAULT NULL,
  `score` tinyint NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `submitted_at` timestamp NOT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ua` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `public_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nps_responses_public_token_unique` (`public_token`),
  KEY `nps_responses_invoice_id_foreign` (`invoice_id`),
  KEY `nps_responses_client_id_foreign` (`client_id`),
  KEY `nps_responses_workspace_id_score_index` (`workspace_id`,`score`),
  CONSTRAINT `nps_responses_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `nps_responses_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  CONSTRAINT `nps_responses_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nps_responses`
--

LOCK TABLES `nps_responses` WRITE;
/*!40000 ALTER TABLE `nps_responses` DISABLE KEYS */;
/*!40000 ALTER TABLE `nps_responses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `npwp_validations`
--

DROP TABLE IF EXISTS `npwp_validations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `npwp_validations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `npwp_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `canonical_npwp` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_valid` tinyint(1) NOT NULL,
  `name_djp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_djp` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_checked_at` timestamp NOT NULL,
  `provider_response` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `npwp_validations_canonical_npwp_unique` (`canonical_npwp`),
  KEY `npwp_validations_last_checked_at_index` (`last_checked_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `npwp_validations`
--

LOCK TABLES `npwp_validations` WRITE;
/*!40000 ALTER TABLE `npwp_validations` DISABLE KEYS */;
/*!40000 ALTER TABLE `npwp_validations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_gateways`
--

DROP TABLE IF EXISTS `payment_gateways`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_gateways` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gateway` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_format` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'rest',
  `base_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_key_encrypted` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_secret_encrypted` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `merchant_id_encrypted` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_key_encrypted` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `server_key_encrypted` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extra_headers` json DEFAULT NULL,
  `supported_channels` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `supports_recurring` tinyint(1) NOT NULL DEFAULT '0',
  `recurring_format` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_sandbox` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_gateways_workspace_id_index` (`workspace_id`),
  CONSTRAINT `payment_gateways_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_gateways`
--

LOCK TABLES `payment_gateways` WRITE;
/*!40000 ALTER TABLE `payment_gateways` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_gateways` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `bank_reconcile_line_id` bigint unsigned DEFAULT NULL,
  `invoice_id` bigint unsigned NOT NULL,
  `workspace_id` bigint unsigned NOT NULL,
  `payment_gateway_id` bigint unsigned DEFAULT NULL,
  `gateway_transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bank_transfer',
  `channel` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_raw` json DEFAULT NULL,
  `reference_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_invoice_id_index` (`invoice_id`),
  KEY `payments_workspace_id_index` (`workspace_id`),
  KEY `payments_payment_gateway_id_foreign` (`payment_gateway_id`),
  KEY `payments_bank_reconcile_line_id_foreign` (`bank_reconcile_line_id`),
  CONSTRAINT `payments_bank_reconcile_line_id_foreign` FOREIGN KEY (`bank_reconcile_line_id`) REFERENCES `bank_reconcile_lines` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_payment_gateway_id_foreign` FOREIGN KEY (`payment_gateway_id`) REFERENCES `payment_gateways` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,NULL,9,1,NULL,NULL,214.50,'2026-04-08','credit_card',NULL,NULL,NULL,'PAY-0001','Payment received','2026-05-06 01:05:55','2026-05-06 01:05:55'),(2,NULL,10,1,NULL,NULL,863.50,'2026-04-12','bank_transfer',NULL,NULL,NULL,'PAY-0002','Payment received','2026-05-06 01:05:55','2026-05-06 01:05:55'),(3,NULL,11,1,NULL,NULL,121.00,'2026-05-05','credit_card',NULL,NULL,NULL,'PAY-0003','Payment received','2026-05-06 01:05:55','2026-05-06 01:05:55'),(4,NULL,12,1,NULL,NULL,2750.00,'2026-04-27','bank_transfer',NULL,NULL,NULL,'PAY-0004','Payment received','2026-05-06 01:05:55','2026-05-06 01:05:55'),(5,NULL,13,1,NULL,NULL,3118.50,'2026-04-21','bank_transfer',NULL,NULL,NULL,'PAY-0005','Payment received','2026-05-06 01:05:55','2026-05-06 01:05:55');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plans`
--

DROP TABLE IF EXISTS `plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tagline` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price_idr_monthly` decimal(12,2) NOT NULL DEFAULT '0.00',
  `price_idr_yearly` decimal(12,2) NOT NULL DEFAULT '0.00',
  `price_usd_monthly` decimal(10,2) NOT NULL DEFAULT '0.00',
  `price_usd_yearly` decimal(10,2) NOT NULL DEFAULT '0.00',
  `features` json DEFAULT NULL,
  `trial_days` int unsigned NOT NULL DEFAULT '0',
  `is_public` tinyint(1) NOT NULL DEFAULT '1',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plans_slug_unique` (`slug`),
  KEY `plans_is_public_sort_order_index` (`is_public`,`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plans`
--

LOCK TABLES `plans` WRITE;
/*!40000 ALTER TABLE `plans` DISABLE KEYS */;
INSERT INTO `plans` VALUES (1,'free','Free','Coba dulu — gratis selamanya','Sempurna untuk mencoba. 1 invoice tanpa expire, dengan watermark Invocely.',0.00,0.00,0.00,0.00,'{\"2fa\": false, \"dunning\": false, \"audit_log\": false, \"max_users\": 1, \"api_access\": false, \"csv_import\": false, \"max_clients\": 5, \"bulk_actions\": false, \"invoice_lock\": true, \"custom_domain\": false, \"time_tracking\": false, \"custom_branding\": false, \"tenant_webhooks\": false, \"expense_tracking\": false, \"remove_watermark\": false, \"drag_drop_builder\": false, \"recurring_enabled\": false, \"custom_email_template\": false, \"max_invoices_lifetime\": 1, \"max_invoices_per_month\": null}',0,1,1,1,'2026-05-06 22:13:04','2026-05-06 22:13:04'),(2,'pro','Pro','Untuk freelancer dan UKM yang serius','Invoice unlimited, recurring, automation, dan branding sendiri.',99000.00,990000.00,9.00,90.00,'{\"2fa\": true, \"dunning\": true, \"audit_log\": true, \"max_users\": 3, \"api_access\": false, \"csv_import\": true, \"max_clients\": null, \"bulk_actions\": true, \"invoice_lock\": false, \"custom_domain\": false, \"time_tracking\": true, \"custom_branding\": true, \"tenant_webhooks\": false, \"expense_tracking\": true, \"remove_watermark\": true, \"drag_drop_builder\": false, \"recurring_enabled\": true, \"custom_email_template\": true, \"max_invoices_lifetime\": null, \"max_invoices_per_month\": null}',0,1,0,2,'2026-05-06 22:13:04','2026-05-06 22:13:04'),(3,'business','Business','Untuk agency dan tim yang scale','Drag-drop invoice builder, API, custom domain, unlimited team.',299000.00,2990000.00,29.00,290.00,'{\"2fa\": true, \"dunning\": true, \"audit_log\": true, \"max_users\": null, \"api_access\": true, \"csv_import\": true, \"max_clients\": null, \"bulk_actions\": true, \"invoice_lock\": false, \"custom_domain\": true, \"time_tracking\": true, \"custom_branding\": true, \"tenant_webhooks\": true, \"expense_tracking\": true, \"remove_watermark\": true, \"drag_drop_builder\": true, \"recurring_enabled\": true, \"custom_email_template\": true, \"max_invoices_lifetime\": null, \"max_invoices_per_month\": null}',0,1,0,3,'2026-05-06 22:13:04','2026-05-06 22:13:04');
/*!40000 ALTER TABLE `plans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `unit_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_workspace_id_index` (`workspace_id`),
  CONSTRAINT `products_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,1,'Web Development - Hourly','Professional development service.',85.00,0.00,'Development',1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(2,1,'UI/UX Design - Hourly','Professional design service.',75.00,0.00,'Design',1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(3,1,'SEO Consultation','Professional marketing service.',150.00,0.00,'Marketing',1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(4,1,'Content Writing - per 1000 words','Professional content service.',50.00,0.00,'Content',1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(5,1,'Server Maintenance - Monthly','Professional hosting service.',200.00,0.00,'Hosting',1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(6,1,'Logo Design','Professional design service.',350.00,0.00,'Design',1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(7,1,'Email Marketing Setup','Professional marketing service.',500.00,0.00,'Marketing',1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(8,1,'Mobile App Development - Hourly','Professional development service.',95.00,0.00,'Development',1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(9,1,'Photo Editing - per image','Professional design service.',15.00,0.00,'Design',1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(10,1,'Social Media Management - Monthly','Professional marketing service.',600.00,0.00,'Marketing',1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(11,1,'Database Optimization','Professional development service.',250.00,0.00,'Development',1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(12,1,'Video Editing - Hourly','Professional content service.',65.00,0.00,'Content',1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(13,1,'WordPress Plugin Development','Professional development service.',1200.00,0.00,'Development',1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(14,1,'Technical Support - Hourly','Professional support service.',55.00,0.00,'Support',1,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(15,1,'Cloud Migration Service','Professional hosting service.',1500.00,0.00,'Hosting',1,'2026-05-06 01:05:55','2026-05-06 01:05:55');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `projects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `client_id` bigint unsigned NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('active','on_hold','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `budget_amount` decimal(12,2) DEFAULT NULL,
  `budget_hours` decimal(8,2) DEFAULT NULL,
  `hourly_rate` decimal(12,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `color` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `projects_workspace_id_slug_unique` (`workspace_id`,`slug`),
  KEY `projects_client_id_foreign` (`client_id`),
  KEY `projects_workspace_id_status_index` (`workspace_id`,`status`),
  CONSTRAINT `projects_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `projects_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES (1,1,2,'Smoke Test Project','smoke-test-project-RdXd',NULL,'active',1000000.00,NULL,NULL,NULL,NULL,'#A78BFA','2026-05-08 01:44:28','2026-05-08 01:44:28');
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `push_subscriptions`
--

DROP TABLE IF EXISTS `push_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `push_subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `workspace_id` bigint unsigned DEFAULT NULL,
  `endpoint` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keys_p256dh` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keys_auth` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_label` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ua` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `push_subscriptions_workspace_id_foreign` (`workspace_id`),
  KEY `push_subscriptions_user_id_index` (`user_id`),
  CONSTRAINT `push_subscriptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `push_subscriptions_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `push_subscriptions`
--

LOCK TABLES `push_subscriptions` WRITE;
/*!40000 ALTER TABLE `push_subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `push_subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qris_codes`
--

DROP TABLE IF EXISTS `qris_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `qris_codes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `invoice_id` bigint unsigned NOT NULL,
  `provider_id` bigint unsigned DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `qr_payload` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `qr_image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL,
  `status` enum('active','paid','expired','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `paid_at` timestamp NULL DEFAULT NULL,
  `provider_ref` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `qris_codes_invoice_id_foreign` (`invoice_id`),
  KEY `qris_codes_provider_id_foreign` (`provider_id`),
  KEY `qris_codes_workspace_id_status_index` (`workspace_id`,`status`),
  CONSTRAINT `qris_codes_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `qris_codes_provider_id_foreign` FOREIGN KEY (`provider_id`) REFERENCES `payment_gateways` (`id`) ON DELETE SET NULL,
  CONSTRAINT `qris_codes_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qris_codes`
--

LOCK TABLES `qris_codes` WRITE;
/*!40000 ALTER TABLE `qris_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `qris_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quotes`
--

DROP TABLE IF EXISTS `quotes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quotes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `client_id` bigint unsigned NOT NULL,
  `project_id` bigint unsigned DEFAULT NULL,
  `quote_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('draft','sent','accepted','rejected','expired','converted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `issue_date` date NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount_value` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount_type` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `discount_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IDR',
  `items` json DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `terms` text COLLATE utf8mb4_unicode_ci,
  `converted_invoice_id` bigint unsigned DEFAULT NULL,
  `public_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accepted_at` timestamp NULL DEFAULT NULL,
  `signed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `quotes_workspace_id_quote_no_unique` (`workspace_id`,`quote_no`),
  UNIQUE KEY `quotes_public_token_unique` (`public_token`),
  KEY `quotes_client_id_foreign` (`client_id`),
  KEY `quotes_converted_invoice_id_foreign` (`converted_invoice_id`),
  KEY `quotes_workspace_id_status_index` (`workspace_id`,`status`),
  KEY `quotes_project_id_foreign` (`project_id`),
  CONSTRAINT `quotes_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `quotes_converted_invoice_id_foreign` FOREIGN KEY (`converted_invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quotes_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quotes_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quotes`
--

LOCK TABLES `quotes` WRITE;
/*!40000 ALTER TABLE `quotes` DISABLE KEYS */;
/*!40000 ALTER TABLE `quotes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recurring_invoices`
--

DROP TABLE IF EXISTS `recurring_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recurring_invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `client_id` bigint unsigned NOT NULL,
  `template` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'classic',
  `primary_color` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accent_color` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frequency` enum('daily','weekly','monthly','yearly') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'monthly',
  `interval_value` int unsigned NOT NULL DEFAULT '1',
  `next_run_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `max_occurrences` int unsigned DEFAULT NULL,
  `current_occurrence` int unsigned NOT NULL DEFAULT '0',
  `status` enum('active','paused','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `last_invoice_id` bigint unsigned DEFAULT NULL,
  `invoice_data` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recurring_invoices_client_id_foreign` (`client_id`),
  KEY `recurring_invoices_next_run_date_status_index` (`next_run_date`,`status`),
  KEY `recurring_invoices_workspace_id_index` (`workspace_id`),
  CONSTRAINT `recurring_invoices_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `recurring_invoices_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recurring_invoices`
--

LOCK TABLES `recurring_invoices` WRITE;
/*!40000 ALTER TABLE `recurring_invoices` DISABLE KEYS */;
INSERT INTO `recurring_invoices` VALUES (1,1,1,'classic',NULL,NULL,'monthly',1,'2026-05-13',NULL,12,0,'active',NULL,'{\"items\": [{\"quantity\": 1, \"tax_rate\": 0, \"unit_price\": 200, \"description\": \"Monthly Website Maintenance\"}, {\"quantity\": 1, \"tax_rate\": 0, \"unit_price\": 150, \"description\": \"Monthly SEO Report\"}], \"notes\": \"Monthly retainer services.\", \"terms\": \"Net 15.\", \"total\": 385, \"currency\": \"USD\", \"subtotal\": 350, \"tax_rate\": 10, \"tax_amount\": 35, \"discount_type\": \"none\", \"discount_value\": 0, \"discount_amount\": 0}','2026-05-06 01:05:55','2026-05-06 01:05:55'),(2,1,4,'modern',NULL,NULL,'monthly',1,'2026-06-05',NULL,NULL,0,'paused',NULL,'{\"items\": [{\"quantity\": 1, \"tax_rate\": 0, \"unit_price\": 500, \"description\": \"Cloud Infrastructure Support\"}], \"notes\": \"Ongoing cloud infrastructure support.\", \"terms\": \"Net 30.\", \"total\": 500, \"currency\": \"USD\", \"subtotal\": 500, \"tax_rate\": 0, \"tax_amount\": 0, \"discount_type\": \"none\", \"discount_value\": 0, \"discount_amount\": 0}','2026-05-06 01:05:55','2026-05-06 01:05:55');
/*!40000 ALTER TABLE `recurring_invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_subscriptions`
--

DROP TABLE IF EXISTS `report_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `report_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `frequency` enum('weekly','monthly','quarterly') COLLATE utf8mb4_unicode_ci NOT NULL,
  `day_of_week` tinyint DEFAULT NULL,
  `day_of_month` tinyint DEFAULT NULL,
  `hour` tinyint NOT NULL DEFAULT '8',
  `last_sent_at` timestamp NULL DEFAULT NULL,
  `next_send_at` timestamp NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `config` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `report_subscriptions_user_id_foreign` (`user_id`),
  KEY `report_subscriptions_workspace_id_is_active_next_send_at_index` (`workspace_id`,`is_active`,`next_send_at`),
  CONSTRAINT `report_subscriptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `report_subscriptions_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_subscriptions`
--

LOCK TABLES `report_subscriptions` WRITE;
/*!40000 ALTER TABLE `report_subscriptions` DISABLE KEYS */;
INSERT INTO `report_subscriptions` VALUES (1,1,1,'weekly_summary','weekly',1,NULL,8,NULL,'2026-05-11 01:00:00',1,NULL,'2026-05-08 01:46:45','2026-05-08 01:46:45');
/*!40000 ALTER TABLE `report_subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('5geQnS7NYptJt08CATH0RZOOTtfIhOwqOMa4XoLN',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoidXZwOVZOWDV1Ukk4Nk51UGRCbFBBb2tUT21lNlRCQXF6bmg5Ukh5byI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo1MToiaHR0cDovL211bHRpbWFhcnQudGVzdC9hZG1pbi9ub3RpZmljYXRpb24tdGVtcGxhdGVzIjt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly9tdWx0aW1hYXJ0LnRlc3QvYWRtaW4vbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1778256924),('cQ3ge65wtuesjcbBfeqAdS7BepZ3zKukpGbEvQ7i',2,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJ6TUhZSUxxeGltYlprMWRINWx0Sk5yd2szSEw0NTFCS2lkaFdlNWNBIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2ludm9jZWx5LnRlc3QiLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjIsImN1cnJlbnRfd29ya3NwYWNlX2lkIjoxfQ==',1778267340);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `signatures`
--

DROP TABLE IF EXISTS `signatures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `signatures` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `subject_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `signer_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `signer_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `signer_role` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signature_image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `signature_typed` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ua` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `signed_at` timestamp NOT NULL,
  `verification_hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `signatures_workspace_id_subject_type_subject_id_index` (`workspace_id`,`subject_type`,`subject_id`),
  CONSTRAINT `signatures_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `signatures`
--

LOCK TABLES `signatures` WRITE;
/*!40000 ALTER TABLE `signatures` DISABLE KEYS */;
/*!40000 ALTER TABLE `signatures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscription_invoices`
--

DROP TABLE IF EXISTS `subscription_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscription_invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `subscription_id` bigint unsigned NOT NULL,
  `workspace_id` bigint unsigned NOT NULL,
  `external_invoice_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','paid','failed','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `paid_at` timestamp NULL DEFAULT NULL,
  `period_starts_at` timestamp NULL DEFAULT NULL,
  `period_ends_at` timestamp NULL DEFAULT NULL,
  `raw_payload` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscription_invoices_subscription_id_foreign` (`subscription_id`),
  KEY `subscription_invoices_workspace_id_status_index` (`workspace_id`,`status`),
  KEY `subscription_invoices_external_invoice_id_index` (`external_invoice_id`),
  CONSTRAINT `subscription_invoices_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subscription_invoices_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscription_invoices`
--

LOCK TABLES `subscription_invoices` WRITE;
/*!40000 ALTER TABLE `subscription_invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `subscription_invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subscriptions`
--

DROP TABLE IF EXISTS `subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `plan_id` bigint unsigned NOT NULL,
  `coupon_id` bigint unsigned DEFAULT NULL,
  `payment_gateway_id` bigint unsigned DEFAULT NULL,
  `payment_method` enum('gateway','manual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'gateway',
  `manual_proof_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manual_payment_note` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `billing_period` enum('monthly','yearly','free') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'free',
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IDR',
  `external_subscription_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `external_customer_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `current_period_starts_at` timestamp NULL DEFAULT NULL,
  `current_period_ends_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancel_at_period_end` tinyint(1) NOT NULL DEFAULT '0',
  `last_payment_at` timestamp NULL DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscriptions_workspace_id_unique` (`workspace_id`),
  KEY `subscriptions_plan_id_foreign` (`plan_id`),
  KEY `subscriptions_payment_gateway_id_foreign` (`payment_gateway_id`),
  KEY `subscriptions_status_current_period_ends_at_index` (`status`,`current_period_ends_at`),
  KEY `subscriptions_external_subscription_id_index` (`external_subscription_id`),
  KEY `subscriptions_coupon_id_foreign` (`coupon_id`),
  CONSTRAINT `subscriptions_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE SET NULL,
  CONSTRAINT `subscriptions_payment_gateway_id_foreign` FOREIGN KEY (`payment_gateway_id`) REFERENCES `payment_gateways` (`id`) ON DELETE SET NULL,
  CONSTRAINT `subscriptions_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `subscriptions_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriptions`
--

LOCK TABLES `subscriptions` WRITE;
/*!40000 ALTER TABLE `subscriptions` DISABLE KEYS */;
INSERT INTO `subscriptions` VALUES (1,1,3,NULL,NULL,'gateway',NULL,NULL,'active','monthly','USD',NULL,NULL,NULL,'2026-05-07 01:59:22','2026-06-07 01:59:22',NULL,0,'2026-05-07 01:59:22',NULL,'2026-05-07 01:53:53','2026-05-07 01:59:22');
/*!40000 ALTER TABLE `subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_messages`
--

DROP TABLE IF EXISTS `support_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `is_admin_reply` tinyint(1) NOT NULL DEFAULT '0',
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_messages_ticket_id_foreign` (`ticket_id`),
  KEY `support_messages_user_id_foreign` (`user_id`),
  CONSTRAINT `support_messages_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `support_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_messages`
--

LOCK TABLES `support_messages` WRITE;
/*!40000 ALTER TABLE `support_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_tickets`
--

DROP TABLE IF EXISTS `support_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_tickets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` enum('low','normal','high','urgent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `status` enum('open','pending','resolved','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `support_tickets_workspace_id_foreign` (`workspace_id`),
  KEY `support_tickets_user_id_foreign` (`user_id`),
  KEY `support_tickets_status_created_at_index` (`status`,`created_at`),
  CONSTRAINT `support_tickets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `support_tickets_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_tickets`
--

LOCK TABLES `support_tickets` WRITE;
/*!40000 ALTER TABLE `support_tickets` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenant_webhooks`
--

DROP TABLE IF EXISTS `tenant_webhooks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenant_webhooks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `events` json NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_fired_at` timestamp NULL DEFAULT NULL,
  `failure_count` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tenant_webhooks_workspace_id_foreign` (`workspace_id`),
  CONSTRAINT `tenant_webhooks_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenant_webhooks`
--

LOCK TABLES `tenant_webhooks` WRITE;
/*!40000 ALTER TABLE `tenant_webhooks` DISABLE KEYS */;
/*!40000 ALTER TABLE `tenant_webhooks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `time_entries`
--

DROP TABLE IF EXISTS `time_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `time_entries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `client_id` bigint unsigned DEFAULT NULL,
  `project_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hours` decimal(8,2) NOT NULL DEFAULT '0.00',
  `hourly_rate` decimal(12,2) NOT NULL DEFAULT '0.00',
  `entry_date` date NOT NULL,
  `billable` tinyint(1) NOT NULL DEFAULT '1',
  `started_at` timestamp NULL DEFAULT NULL,
  `ended_at` timestamp NULL DEFAULT NULL,
  `is_running` tinyint(1) NOT NULL DEFAULT '0',
  `tags` json DEFAULT NULL,
  `invoiced` tinyint(1) NOT NULL DEFAULT '0',
  `invoice_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `time_entries_client_id_foreign` (`client_id`),
  KEY `time_entries_user_id_foreign` (`user_id`),
  KEY `time_entries_invoice_id_foreign` (`invoice_id`),
  KEY `time_entries_workspace_id_entry_date_index` (`workspace_id`,`entry_date`),
  KEY `time_entries_project_id_foreign` (`project_id`),
  CONSTRAINT `time_entries_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `time_entries_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  CONSTRAINT `time_entries_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `time_entries_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `time_entries_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `time_entries`
--

LOCK TABLES `time_entries` WRITE;
/*!40000 ALTER TABLE `time_entries` DISABLE KEYS */;
/*!40000 ALTER TABLE `time_entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_2fa_secrets`
--

DROP TABLE IF EXISTS `user_2fa_secrets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_2fa_secrets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `secret_encrypted` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `recovery_codes_encrypted` json NOT NULL,
  `enabled_at` timestamp NULL DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_2fa_secrets_user_id_unique` (`user_id`),
  CONSTRAINT `user_2fa_secrets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_2fa_secrets`
--

LOCK TABLES `user_2fa_secrets` WRITE;
/*!40000 ALTER TABLE `user_2fa_secrets` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_2fa_secrets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `is_superadmin` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_is_superadmin_index` (`is_superadmin`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Demo User','demo@invocely.test','2026-05-06 01:05:55','$2y$12$yaMZil7QygzIlj1b0IRf.O0Lx6Jl348Pv.cw1Kojo5QsNzKOMEnru',0,0,NULL,'2026-05-06 01:05:55','2026-05-06 01:05:55'),(2,'Super Admin','superadmin@invocely.test','2026-05-06 22:13:04','$2y$12$dUzInUtNeeUm1MDyNdcXgu3k6LmLo1wD3RL4A8QXzdHuWowufwMr6',0,1,NULL,'2026-05-06 22:13:04','2026-05-06 22:13:04');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webhook_deliveries`
--

DROP TABLE IF EXISTS `webhook_deliveries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `webhook_deliveries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `webhook_id` bigint unsigned NOT NULL,
  `event` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` json NOT NULL,
  `status_code` int DEFAULT NULL,
  `response_body` text COLLATE utf8mb4_unicode_ci,
  `attempt` int NOT NULL DEFAULT '1',
  `delivered_at` timestamp NULL DEFAULT NULL,
  `next_retry_at` timestamp NULL DEFAULT NULL,
  `final_status` enum('pending','success','failed','exhausted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `webhook_deliveries_webhook_id_final_status_index` (`webhook_id`,`final_status`),
  KEY `webhook_deliveries_next_retry_at_index` (`next_retry_at`),
  CONSTRAINT `webhook_deliveries_webhook_id_foreign` FOREIGN KEY (`webhook_id`) REFERENCES `tenant_webhooks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webhook_deliveries`
--

LOCK TABLES `webhook_deliveries` WRITE;
/*!40000 ALTER TABLE `webhook_deliveries` DISABLE KEYS */;
/*!40000 ALTER TABLE `webhook_deliveries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `whatsapp_logs`
--

DROP TABLE IF EXISTS `whatsapp_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `whatsapp_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `invoice_id` bigint unsigned DEFAULT NULL,
  `quote_id` bigint unsigned DEFAULT NULL,
  `to_phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message_body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('queued','sent','delivered','read','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'queued',
  `provider_msg_id` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fail_reason` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `whatsapp_logs_quote_id_foreign` (`quote_id`),
  KEY `whatsapp_logs_workspace_id_sent_at_index` (`workspace_id`,`sent_at`),
  KEY `whatsapp_logs_invoice_id_index` (`invoice_id`),
  CONSTRAINT `whatsapp_logs_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  CONSTRAINT `whatsapp_logs_quote_id_foreign` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `whatsapp_logs_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `whatsapp_logs`
--

LOCK TABLES `whatsapp_logs` WRITE;
/*!40000 ALTER TABLE `whatsapp_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `whatsapp_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workspace_api_tokens`
--

DROP TABLE IF EXISTS `workspace_api_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `workspace_api_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token_hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prefix` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` json DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `workspace_api_tokens_token_hash_unique` (`token_hash`),
  KEY `workspace_api_tokens_workspace_id_foreign` (`workspace_id`),
  KEY `workspace_api_tokens_user_id_foreign` (`user_id`),
  KEY `workspace_api_tokens_prefix_index` (`prefix`),
  CONSTRAINT `workspace_api_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `workspace_api_tokens_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workspace_api_tokens`
--

LOCK TABLES `workspace_api_tokens` WRITE;
/*!40000 ALTER TABLE `workspace_api_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `workspace_api_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workspace_invites`
--

DROP TABLE IF EXISTS `workspace_invites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `workspace_invites` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `invited_by` bigint unsigned NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','member') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'member',
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `accepted_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `workspace_invites_token_unique` (`token`),
  KEY `workspace_invites_invited_by_foreign` (`invited_by`),
  KEY `workspace_invites_workspace_id_email_index` (`workspace_id`,`email`),
  CONSTRAINT `workspace_invites_invited_by_foreign` FOREIGN KEY (`invited_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `workspace_invites_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workspace_invites`
--

LOCK TABLES `workspace_invites` WRITE;
/*!40000 ALTER TABLE `workspace_invites` DISABLE KEYS */;
/*!40000 ALTER TABLE `workspace_invites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workspace_user`
--

DROP TABLE IF EXISTS `workspace_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `workspace_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `role` enum('owner','admin','member') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'member',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `workspace_user_workspace_id_user_id_unique` (`workspace_id`,`user_id`),
  KEY `workspace_user_user_id_foreign` (`user_id`),
  CONSTRAINT `workspace_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `workspace_user_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workspace_user`
--

LOCK TABLES `workspace_user` WRITE;
/*!40000 ALTER TABLE `workspace_user` DISABLE KEYS */;
INSERT INTO `workspace_user` VALUES (1,1,1,'admin','2026-05-06 01:05:55','2026-05-06 01:05:55');
/*!40000 ALTER TABLE `workspace_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workspaces`
--

DROP TABLE IF EXISTS `workspaces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `workspaces` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Indonesia',
  `postal_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_pkp` tinyint(1) NOT NULL DEFAULT '0',
  `pkp_no` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pkp_certificate_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_prefix` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'INV-',
  `quote_prefix` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'QUO-',
  `next_quote_no` int NOT NULL DEFAULT '1',
  `invoice_number_format` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'INV-{seq:000}',
  `reset_period` enum('never','monthly','yearly') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'never',
  `default_late_fee_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `default_late_fee_type` enum('percent','fixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percent',
  `default_late_fee_policy` enum('none','one_time','monthly') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `enable_client_comments` tinyint(1) NOT NULL DEFAULT '1',
  `sso_required` enum('none','google','microsoft','any') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `widget_allowed_domains` json DEFAULT NULL,
  `ai_monthly_budget_idr` decimal(10,2) DEFAULT NULL,
  `whatsapp_config` json DEFAULT NULL,
  `industry` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `next_invoice_no` int unsigned NOT NULL DEFAULT '1',
  `default_template` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'classic',
  `primary_color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#2563EB',
  `accent_color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#1E40AF',
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IDR',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `workspaces_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workspaces`
--

LOCK TABLES `workspaces` WRITE;
/*!40000 ALTER TABLE `workspaces` DISABLE KEYS */;
INSERT INTO `workspaces` VALUES (1,'Demo Company','demo-company',NULL,'123 Business Ave, Suite 100','San Francisco','CA','United States','94105','+1-555-0199','hello@demo-company.test','TAX-12345-DEMO',0,NULL,NULL,'INV-','QUO-',1,'INV-{seq:000}','never',0.00,'percent','none',1,'none',NULL,NULL,NULL,NULL,22,'classic','#2563EB','#1E40AF','USD','2026-05-06 01:05:55','2026-05-07 01:59:59');
/*!40000 ALTER TABLE `workspaces` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'invocely'
--

--
-- Dumping routines for database 'invocely'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-09  2:16:34
