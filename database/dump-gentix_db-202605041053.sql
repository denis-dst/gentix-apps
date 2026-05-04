-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: gentix_db
-- ------------------------------------------------------
-- Server version	8.0.30

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
INSERT INTO `cache` VALUES ('gentix-cache-admin@gentix.test|127.0.0.1','i:1;',1777863477),('gentix-cache-admin@gentix.test|127.0.0.1:timer','i:1777863477;',1777863477),('gentix-cache-eo@example.com|127.0.0.1','i:1;',1777863526),('gentix-cache-eo@example.com|127.0.0.1:timer','i:1777863526;',1777863526),('gentix-cache-superadmin@gentix.id|127.0.0.1','i:1;',1777863500),('gentix-cache-superadmin@gentix.id|127.0.0.1:timer','i:1777863500;',1777863500);
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
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `terms_conditions` text COLLATE utf8mb4_unicode_ci COMMENT 'General terms and conditions for the event',
  `venue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `background_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `banner_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_start_date` datetime NOT NULL,
  `event_end_date` datetime NOT NULL,
  `gate_open_at` datetime DEFAULT NULL,
  `gate_close_at` datetime DEFAULT NULL,
  `status` enum('draft','published','ongoing','ended','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `security_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `events_slug_unique` (`slug`),
  KEY `events_tenant_id_status_index` (`tenant_id`,`status`),
  KEY `events_slug_index` (`slug`),
  CONSTRAINT `events_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
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
-- Table structure for table `gate_logs`
--

DROP TABLE IF EXISTS `gate_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gate_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `event_id` bigint unsigned NOT NULL,
  `ticket_id` bigint unsigned NOT NULL,
  `gate_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('IN','OUT') COLLATE utf8mb4_unicode_ci NOT NULL,
  `scanned_at` timestamp NOT NULL,
  `device_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scanned_by` bigint unsigned DEFAULT NULL,
  `meta` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gate_logs_tenant_id_foreign` (`tenant_id`),
  KEY `gate_logs_scanned_by_foreign` (`scanned_by`),
  KEY `gate_logs_ticket_id_type_scanned_at_index` (`ticket_id`,`type`,`scanned_at`),
  KEY `gate_logs_event_id_type_index` (`event_id`,`type`),
  CONSTRAINT `gate_logs_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gate_logs_scanned_by_foreign` FOREIGN KEY (`scanned_by`) REFERENCES `users` (`id`),
  CONSTRAINT `gate_logs_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gate_logs_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gate_logs`
--

LOCK TABLES `gate_logs` WRITE;
/*!40000 ALTER TABLE `gate_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `gate_logs` ENABLE KEYS */;
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
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_04_22_133611_create_permission_tables',1),(5,'2026_04_22_133611_create_personal_access_tokens_table',1),(6,'2026_04_22_144225_create_settings_table',1),(7,'2026_04_22_153308_add_avatar_to_users_table',1),(8,'2026_04_22_200000_create_tenants_table',1),(9,'2026_04_22_200001_add_tenant_id_to_users_table',1),(10,'2026_04_22_200002_create_events_table',1),(11,'2026_04_22_200003_create_ticket_categories_table',1),(12,'2026_04_22_200004_create_transactions_table',1),(13,'2026_04_22_200005_create_tickets_table',1),(14,'2026_04_22_200006_create_gate_logs_table',1),(15,'2026_04_23_153709_add_layout_config_to_ticket_categories_table',1),(16,'2026_04_23_155411_add_sale_times_to_ticket_categories_table',1),(17,'2026_04_26_063755_add_user_id_to_tenants_table',1),(18,'2026_04_26_070541_add_nik_restriction_to_ticket_categories_table',1),(19,'2026_04_26_073132_add_nik_restriction_message_to_ticket_categories_table',1),(20,'2026_04_26_073612_add_extra_fields_to_events_and_tickets',1),(21,'2026_04_26_080318_create_promo_codes_table',1),(22,'2026_04_26_081353_add_promo_fields_to_transactions_table',1),(23,'2026_04_26_084707_add_booking_fields_to_transactions',1),(24,'2026_04_26_122207_add_terms_conditions_to_tenants_table',1),(25,'2026_04_26_124325_add_security_code_to_events_table',1),(26,'2026_04_26_124326_add_redeem_photo_to_tickets_table',1),(27,'2026_05_04_031938_create_pages_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(2,'App\\Models\\User',2),(3,'App\\Models\\User',3),(4,'App\\Models\\User',4);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pages_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES (1,'help-center','Help Center','\n                    <div class=\"prose prose-invert max-w-none prose-orange\">\n                        <p class=\"text-stone-400 leading-relaxed mb-6\">Welcome to the Help Center page. This content is dynamically loaded from the database.</p>\n                        <h3 class=\"text-2xl font-bold text-white mt-8 mb-4\">1. Introduction</h3>\n                        <p class=\"text-stone-400 leading-relaxed mb-6\">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>\n                        <h3 class=\"text-2xl font-bold text-white mt-8 mb-4\">2. General Information</h3>\n                        <p class=\"text-stone-400 leading-relaxed mb-6\">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>\n                        <ul class=\"list-disc list-inside text-stone-400 mb-6 space-y-2\">\n                            <li>Dynamic content management</li>\n                            <li>Secure data storage</li>\n                            <li>Easy updates through admin panel</li>\n                        </ul>\n                        <p class=\"text-stone-400 leading-relaxed mb-6\">You can easily update this content directly in your database\'s <code class=\"text-orange-400 bg-white/5 px-1 py-0.5 rounded\">pages</code> table.</p>\n                    </div>\n                ','2026-05-03 20:22:34','2026-05-03 20:22:34'),(2,'privacy-policy','Privacy Policy','\n                    <div class=\"prose prose-invert max-w-none prose-orange\">\n                        <p class=\"text-stone-400 leading-relaxed mb-6\">Welcome to the Privacy Policy page. This content is dynamically loaded from the database.</p>\n                        <h3 class=\"text-2xl font-bold text-white mt-8 mb-4\">1. Introduction</h3>\n                        <p class=\"text-stone-400 leading-relaxed mb-6\">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>\n                        <h3 class=\"text-2xl font-bold text-white mt-8 mb-4\">2. General Information</h3>\n                        <p class=\"text-stone-400 leading-relaxed mb-6\">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>\n                        <ul class=\"list-disc list-inside text-stone-400 mb-6 space-y-2\">\n                            <li>Dynamic content management</li>\n                            <li>Secure data storage</li>\n                            <li>Easy updates through admin panel</li>\n                        </ul>\n                        <p class=\"text-stone-400 leading-relaxed mb-6\">You can easily update this content directly in your database\'s <code class=\"text-orange-400 bg-white/5 px-1 py-0.5 rounded\">pages</code> table.</p>\n                    </div>\n                ','2026-05-03 20:22:55','2026-05-03 20:22:55'),(3,'terms-of-service','Terms of Service','\n                    <div class=\"prose prose-invert max-w-none prose-orange\">\n                        <p class=\"text-stone-400 leading-relaxed mb-6\">Welcome to the Terms of Service page. This content is dynamically loaded from the database.</p>\n                        <h3 class=\"text-2xl font-bold text-white mt-8 mb-4\">1. Introduction</h3>\n                        <p class=\"text-stone-400 leading-relaxed mb-6\">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>\n                        <h3 class=\"text-2xl font-bold text-white mt-8 mb-4\">2. General Information</h3>\n                        <p class=\"text-stone-400 leading-relaxed mb-6\">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>\n                        <ul class=\"list-disc list-inside text-stone-400 mb-6 space-y-2\">\n                            <li>Dynamic content management</li>\n                            <li>Secure data storage</li>\n                            <li>Easy updates through admin panel</li>\n                        </ul>\n                        <p class=\"text-stone-400 leading-relaxed mb-6\">You can easily update this content directly in your database\'s <code class=\"text-orange-400 bg-white/5 px-1 py-0.5 rounded\">pages</code> table.</p>\n                    </div>\n                ','2026-05-03 20:23:00','2026-05-03 20:23:00'),(4,'contact-us','Contact Us','\n                    <div class=\"prose prose-invert max-w-none prose-orange\">\n                        <p class=\"text-stone-400 leading-relaxed mb-6\">Welcome to the Contact Us page. This content is dynamically loaded from the database.</p>\n                        <h3 class=\"text-2xl font-bold text-white mt-8 mb-4\">1. Introduction</h3>\n                        <p class=\"text-stone-400 leading-relaxed mb-6\">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>\n                        <h3 class=\"text-2xl font-bold text-white mt-8 mb-4\">2. General Information</h3>\n                        <p class=\"text-stone-400 leading-relaxed mb-6\">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>\n                        <ul class=\"list-disc list-inside text-stone-400 mb-6 space-y-2\">\n                            <li>Dynamic content management</li>\n                            <li>Secure data storage</li>\n                            <li>Easy updates through admin panel</li>\n                        </ul>\n                        <p class=\"text-stone-400 leading-relaxed mb-6\">You can easily update this content directly in your database\'s <code class=\"text-orange-400 bg-white/5 px-1 py-0.5 rounded\">pages</code> table.</p>\n                    </div>\n                ','2026-05-03 20:23:19','2026-05-03 20:23:19');
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
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
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promo_codes`
--

DROP TABLE IF EXISTS `promo_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promo_codes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `event_id` bigint unsigned DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('fixed','percentage') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `value` decimal(15,2) NOT NULL,
  `max_usage` int DEFAULT NULL,
  `used_count` int NOT NULL DEFAULT '0',
  `start_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `promo_codes_code_unique` (`code`),
  KEY `promo_codes_tenant_id_foreign` (`tenant_id`),
  KEY `promo_codes_event_id_foreign` (`event_id`),
  CONSTRAINT `promo_codes_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `promo_codes_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promo_codes`
--

LOCK TABLES `promo_codes` WRITE;
/*!40000 ALTER TABLE `promo_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `promo_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Superadmin','web','2026-05-03 19:46:57','2026-05-03 19:46:57'),(2,'Penyedia Event','web','2026-05-03 19:46:57','2026-05-03 19:46:57'),(3,'Petugas Loket','web','2026-05-03 19:46:57','2026-05-03 19:46:57'),(4,'Petugas Gate','web','2026-05-03 19:46:57','2026-05-03 19:46:57');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
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
INSERT INTO `sessions` VALUES ('CYT5kKYYh0Y1hGMBfhOqyKR2ytPQMLc3hJ7fS3CC',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','eyJfdG9rZW4iOiIzNUVNSWJiRGtXUk1OTDJpWHhGaTZmUXRLNWdjM0dFcXU2UTlVc1ZPIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2dlbnRpeC1hcHBzLnRlc3QiLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1777866746),('ZFPDFcY6i2Y5ffEWfD782Uaxe1ocXr2ip4cPr8vC',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJmeEoxVUpFSlpDUFMyWGU1UXhoOTBRZUlySHJ2ZnpUeFVZY2pMOEJyIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2dlbnRpeC1hcHBzLnRlc3RcL3BcL2hlbHAtY2VudGVyIiwicm91dGUiOiJwYWdlcy5zaG93In0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1777865044);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenants`
--

DROP TABLE IF EXISTS `tenants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','suspended','deleted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `payment_server_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_client_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta` json DEFAULT NULL,
  `synced_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `terms_conditions` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenants_slug_unique` (`slug`),
  UNIQUE KEY `tenants_email_unique` (`email`),
  KEY `tenants_user_id_foreign` (`user_id`),
  CONSTRAINT `tenants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenants`
--

LOCK TABLES `tenants` WRITE;
/*!40000 ALTER TABLE `tenants` DISABLE KEYS */;
INSERT INTO `tenants` VALUES (1,NULL,'Gentix Central','gentix-central','admin@gentix.id',NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,'2026-05-03 19:46:57','2026-05-03 19:46:57',NULL,NULL),(2,NULL,'Example EO','example-eo','contact@example-eo.com',NULL,NULL,NULL,'active',NULL,NULL,NULL,NULL,'2026-05-03 19:46:58','2026-05-03 19:46:58',NULL,NULL);
/*!40000 ALTER TABLE `tenants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_categories`
--

DROP TABLE IF EXISTS `ticket_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint unsigned NOT NULL,
  `tenant_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `nik_restriction` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'First few digits of NIK to restrict purchase area',
  `nik_restriction_message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Custom message shown when NIK restriction fails',
  `badge_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Dynamic badge text shown on ticket card',
  `quota` int NOT NULL DEFAULT '0',
  `sold_count` int NOT NULL DEFAULT '0',
  `hex_color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#6366F1',
  `category_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `background_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `layout_config` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sale_start_at` timestamp NULL DEFAULT NULL,
  `sale_end_at` timestamp NULL DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_categories_event_id_is_active_index` (`event_id`,`is_active`),
  KEY `ticket_categories_tenant_id_index` (`tenant_id`),
  CONSTRAINT `ticket_categories_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ticket_categories_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_categories`
--

LOCK TABLES `ticket_categories` WRITE;
/*!40000 ALTER TABLE `ticket_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticket_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tickets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `event_id` bigint unsigned NOT NULL,
  `transaction_id` bigint unsigned NOT NULL,
  `ticket_category_id` bigint unsigned NOT NULL,
  `ticket_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `wristband_qr` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('available','reserved','sold','redeemed','void') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `redeemed_at` timestamp NULL DEFAULT NULL,
  `redeemed_by` bigint unsigned DEFAULT NULL,
  `visitor_data` json DEFAULT NULL,
  `redeem_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tickets_ticket_code_unique` (`ticket_code`),
  UNIQUE KEY `tickets_wristband_qr_unique` (`wristband_qr`),
  KEY `tickets_event_id_foreign` (`event_id`),
  KEY `tickets_transaction_id_foreign` (`transaction_id`),
  KEY `tickets_ticket_category_id_foreign` (`ticket_category_id`),
  KEY `tickets_redeemed_by_foreign` (`redeemed_by`),
  KEY `tickets_tenant_id_status_index` (`tenant_id`,`status`),
  KEY `tickets_ticket_code_index` (`ticket_code`),
  KEY `tickets_wristband_qr_index` (`wristband_qr`),
  CONSTRAINT `tickets_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tickets_redeemed_by_foreign` FOREIGN KEY (`redeemed_by`) REFERENCES `users` (`id`),
  CONSTRAINT `tickets_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tickets_ticket_category_id_foreign` FOREIGN KEY (`ticket_category_id`) REFERENCES `ticket_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tickets_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `event_id` bigint unsigned NOT NULL,
  `ticket_category_id` bigint unsigned DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `promo_code_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `reference_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_nik` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `payment_status` enum('pending','paid','expired','failed','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `channel` enum('online','pos') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'online',
  `processed_by` bigint unsigned DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transactions_reference_no_unique` (`reference_no`),
  KEY `transactions_event_id_foreign` (`event_id`),
  KEY `transactions_user_id_foreign` (`user_id`),
  KEY `transactions_processed_by_foreign` (`processed_by`),
  KEY `transactions_tenant_id_payment_status_index` (`tenant_id`,`payment_status`),
  KEY `transactions_reference_no_index` (`reference_no`),
  KEY `transactions_promo_code_id_foreign` (`promo_code_id`),
  KEY `transactions_ticket_category_id_foreign` (`ticket_category_id`),
  CONSTRAINT `transactions_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transactions_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`),
  CONSTRAINT `transactions_promo_code_id_foreign` FOREIGN KEY (`promo_code_id`) REFERENCES `promo_codes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transactions_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transactions_ticket_category_id_foreign` FOREIGN KEY (`ticket_category_id`) REFERENCES `ticket_categories` (`id`),
  CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
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
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_tenant_id_foreign` (`tenant_id`),
  CONSTRAINT `users_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Super Admin Gentix','superadmin@gentix.id',NULL,NULL,'$2y$12$SJOKx0XtIohEsZ/lWAu8auVY5KJg8PKUBRpxkgM667SrEKmocQ9AO',NULL,'2026-05-03 19:46:58','2026-05-03 19:46:58',1,NULL,1),(2,'Provider Manager','eo@example.com',NULL,NULL,'$2y$12$krgsUVmrjEpriXpAC4/AIOhG0oeIHTAOasiLHmkcQ/9be/Hxm802O',NULL,'2026-05-03 19:46:58','2026-05-03 19:46:58',2,NULL,1),(3,'Cashier Staff','cashier@example.com',NULL,NULL,'$2y$12$isWzq7lyBckFTwMO8T7hQeZpTw8/PRkmUMJo3BB24aGiFaW/ZLvXK',NULL,'2026-05-03 19:46:59','2026-05-03 19:46:59',2,NULL,1),(4,'Gate Staff','gate@example.com',NULL,NULL,'$2y$12$zym0HUDgHsaEbc3ZOvo9xOfmvwBKThFA9KnX5el93.n4Gse2wkDfO',NULL,'2026-05-03 19:46:59','2026-05-03 19:46:59',2,NULL,1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'gentix_db'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-04 10:53:11
