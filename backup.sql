mysqldump: [Warning] Using a password on the command line interface can be insecure.
-- MySQL dump 10.13  Distrib 8.0.45, for Linux (x86_64)
--
-- Host: localhost    Database: pharmacy_inventory
-- ------------------------------------------------------
-- Server version	8.0.45

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
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
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
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Analgesics','2026-04-09 14:23:33','2026-04-09 14:23:33'),(2,'Antibiotics','2026-04-09 14:23:33','2026-04-09 14:23:33'),(3,'Antipyretics','2026-04-09 14:23:33','2026-04-09 14:23:33'),(4,'Antihistamines','2026-04-09 14:23:34','2026-04-09 14:23:34'),(5,'Vitamins','2026-04-09 14:23:34','2026-04-09 14:23:34'),(6,'Cardiovascular','2026-04-09 14:23:34','2026-04-09 14:23:34'),(7,'Gastrointestinal','2026-04-09 14:23:34','2026-04-09 14:23:34'),(8,'Respiratory','2026-04-09 14:23:34','2026-04-09 14:23:34');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_history`
--

DROP TABLE IF EXISTS `chat_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chat_history_user_id_foreign` (`user_id`),
  CONSTRAINT `chat_history_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_history`
--

LOCK TABLES `chat_history` WRITE;
/*!40000 ALTER TABLE `chat_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `chat_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chatbot_logs`
--

DROP TABLE IF EXISTS `chatbot_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chatbot_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `response` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `intent` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entities` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chatbot_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `chatbot_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chatbot_logs`
--

LOCK TABLES `chatbot_logs` WRITE;
/*!40000 ALTER TABLE `chatbot_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `chatbot_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
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
-- Table structure for table `medicines`
--

DROP TABLE IF EXISTS `medicines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `medicines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `generic_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `category_id` bigint unsigned DEFAULT NULL,
  `category_type` enum('adults','children','both') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'both',
  `supplier_id` bigint unsigned DEFAULT NULL,
  `purchase_price` decimal(10,2) NOT NULL,
  `selling_price` decimal(10,2) NOT NULL,
  `quantity` int NOT NULL,
  `unit` enum('tablet','capsule','ml','mg','g','mcg','unit') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tablet',
  `strength` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reorder_level` int NOT NULL DEFAULT '10',
  `expiry_date` date NOT NULL,
  `batch_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shelf_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `requires_prescription` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'in_stock',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `medicines_category_id_foreign` (`category_id`),
  KEY `medicines_supplier_id_foreign` (`supplier_id`),
  CONSTRAINT `medicines_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `medicines_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `medicines`
--

LOCK TABLES `medicines` WRITE;
/*!40000 ALTER TABLE `medicines` DISABLE KEYS */;
INSERT INTO `medicines` VALUES (1,'Paracetamol','Acetaminophen','Biogesic','For fever and pain relief',1,'both',1,2.50,5.00,150,'tablet',NULL,20,'2028-04-09','PC2024001','A1',0,'in_stock','2026-04-09 14:23:34','2026-04-10 18:31:45'),(2,'Amoxicillin','Amoxicillin Trihydrate','Amoxil','Antibiotic for bacterial infections',2,'both',2,15.00,30.00,64,'tablet',NULL,10,'2027-04-09','AX2024001','B2',1,'in_stock','2026-04-09 14:23:34','2026-04-10 18:31:41'),(3,'Ibuprofen','Ibuprofen','Advil','NSAID for pain and inflammation',1,'both',1,3.00,6.00,110,'tablet',NULL,15,'2027-10-09','IB2024001','A3',0,'in_stock','2026-04-09 14:23:34','2026-04-10 18:31:45'),(4,'Cetirizine','Cetirizine HCl','Zyrtec','Antihistamine for allergies',4,'both',3,8.00,15.00,5,'tablet',NULL,10,'2026-10-09','CT2024001','C1',0,'low_stock','2026-04-09 14:23:34','2026-04-10 18:31:45'),(5,'Losartan','Losartan Potassium','Cozaar','For hypertension',6,'both',2,12.00,25.00,45,'tablet',NULL,10,'2028-04-09','LS2024001','D2',1,'in_stock','2026-04-09 14:23:34','2026-04-10 18:31:45'),(6,'Omeprazole','Omeprazole','Losec','For gastric acid reduction',7,'both',3,10.00,20.00,60,'tablet',NULL,10,'2027-04-09','OM2024001','C3',1,'in_stock','2026-04-09 14:23:34','2026-04-10 18:31:45'),(7,'Tiktiki','Tiks','Bearbrand','A',NULL,'both',NULL,50.00,60.00,50,'mg','200',10,'2028-08-06','Batch18','A9',1,'in_stock','2026-04-09 15:10:41','2026-04-10 18:31:45');
/*!40000 ALTER TABLE `medicines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_02_09_084913_create_categories_table',1),(5,'2026_02_09_085032_create_suppliers_table',1),(6,'2026_02_09_085051_create_medicines_table',1),(7,'2026_02_09_085111_create_sales_table',1),(8,'2026_02_09_085126_create_sale_items_table',1),(9,'2026_02_09_085220_create_chatbot_logs_table',1),(10,'2026_02_09_102843_create_sessions_table',1),(11,'2026_02_13_054503_create_password_histories_table',1),(12,'2026_03_09_052740_create_chat_history_table',1),(13,'2026_03_09_060701_add_category_type_and_unit_to_medicines_table',1),(14,'2026_03_20_042954_create_personal_access_tokens_table',1),(15,'2026_03_25_043818_modify_medicines_table_nullable_fields',1),(16,'2026_03_25_075506_add_is_active_to_users_table',1),(17,'2026_03_30_053612_create_stock_movements_table',1),(18,'2026_04_09_163057_add_license_number_to_suppliers_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_histories`
--

DROP TABLE IF EXISTS `password_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `password_histories_user_id_foreign` (`user_id`),
  CONSTRAINT `password_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_histories`
--

LOCK TABLES `password_histories` WRITE;
/*!40000 ALTER TABLE `password_histories` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_histories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (1,'App\\Models\\User',1,'mobile-app','e28251821658c5411648832bbe9dbb954534a3691f174ab1d5bfec6d0780194b','[\"*\"]',NULL,NULL,'2026-04-09 14:26:07','2026-04-09 14:26:07'),(3,'App\\Models\\User',1,'mobile','15dc886c019cb3975bcc7458d4f74d6e3bb38ba53332dd05b133a5a7b4c5b2fd','[\"*\"]','2026-04-09 14:41:16',NULL,'2026-04-09 14:39:46','2026-04-09 14:41:16'),(4,'App\\Models\\User',1,'mobile-app','20fcbb0c8503159345c5103e901a9781f28b1ec8c8b12ae835cbc1e47c3b5f0c','[\"*\"]',NULL,NULL,'2026-04-10 12:30:37','2026-04-10 12:30:37'),(6,'App\\Models\\User',5,'mobile-app','b47517ad90fec4323acfb9fe51779f9371c60b5029d5d037099a95a3925bd248','[\"*\"]','2026-04-10 18:25:26',NULL,'2026-04-10 18:25:17','2026-04-10 18:25:26');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sale_items`
--

DROP TABLE IF EXISTS `sale_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sale_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint unsigned NOT NULL,
  `medicine_id` bigint unsigned NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_items_sale_id_foreign` (`sale_id`),
  KEY `sale_items_medicine_id_foreign` (`medicine_id`),
  CONSTRAINT `sale_items_medicine_id_foreign` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`),
  CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sale_items`
--

LOCK TABLES `sale_items` WRITE;
/*!40000 ALTER TABLE `sale_items` DISABLE KEYS */;
INSERT INTO `sale_items` VALUES (1,1,2,1,30.00,30.00,'2026-04-10 12:32:03','2026-04-10 12:32:03'),(2,2,2,20,30.00,600.00,'2026-04-10 18:12:21','2026-04-10 18:12:21'),(3,3,3,10,6.00,60.00,'2026-04-10 18:22:00','2026-04-10 18:22:00');
/*!40000 ALTER TABLE `sale_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `grand_total` decimal(10,2) NOT NULL,
  `payment_method` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sales_invoice_number_unique` (`invoice_number`),
  KEY `sales_user_id_foreign` (`user_id`),
  CONSTRAINT `sales_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
INSERT INTO `sales` VALUES (1,'INV-20260410-69D87D431CF44',1,30.00,0.00,0.00,30.00,'cash',NULL,'2026-04-10 12:32:03','2026-04-10 12:32:03'),(2,'INV-20260410-69D8CD05AC634',1,600.00,2.00,29.90,627.90,'card',NULL,'2026-04-10 18:12:21','2026-04-10 18:12:21'),(3,'INV-20260410-69D8CF485934A',1,60.00,0.00,0.00,60.00,'cash',NULL,'2026-04-10 18:22:00','2026-04-10 18:22:00');
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
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
INSERT INTO `sessions` VALUES ('3cTppbYPcqoqsiK5FS75E4IPRFDZ3N6UkFhN9eKg',NULL,'172.18.0.1','okhttp/4.12.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiOFpZVnJRZkR3Z25IUzFtODhQZWpramJnT2dxeGZTazV1MHB4U3VvUSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xOTIuMTY4LjEwMC41NCI7czo1OiJyb3V0ZSI7czo3OiJsYW5kaW5nIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1775816501),('gv6dUnfD8T8fddwKCLl9NLnbcIuEXJ2Jkka9U6pP',NULL,'172.18.0.1','Dalvik/2.1.0 (Linux; U; Android 16; RMX3840 Build/BP2A.250605.015)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZ0lKaldMaVhRaTRrTjlYb0xQRnJqNVI2V0dhMWJuZTVCbE9yajk2aiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly8xOTIuMTY4LjEzNy4xIjtzOjU6InJvdXRlIjtzOjc6ImxhbmRpbmciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1775819796),('h6TUzRMO8l8cnD4LB3obOzZWh2xb0ystinzY9yfv',NULL,'172.18.0.1','Dalvik/2.1.0 (Linux; U; Android 16; RMX3840 Build/BP2A.250605.015)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUWtSWGUwTVFzWUtGUEdZb1p3RVhDdnZ5Vm9rRzZ0UUdzdzh4SHRpZSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly8xOTIuMTY4LjEzNy4xIjtzOjU6InJvdXRlIjtzOjc6ImxhbmRpbmciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1775817130),('jTZBesfv4SkKdBuo5gB1Lsb8SESIoQb7u2wIbTze',NULL,'172.18.0.1','Dalvik/2.1.0 (Linux; U; Android 16; RMX3840 Build/BP2A.250605.015)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiOVlLMlNsdm5VMnRVM1EzSVFVYWJiWnpVbVdHNkhFN2xsZlVXUzFLSCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly8xOTIuMTY4LjEzNy4xIjtzOjU6InJvdXRlIjtzOjc6ImxhbmRpbmciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1775817504),('LdGgvBmzRhAfdUZv6im4oC4UMZY3y9WSqrqKNyJy',NULL,'172.18.0.1','Dalvik/2.1.0 (Linux; U; Android 16; RMX3840 Build/BP2A.250605.015)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVWFWdXZhV3h2Y0JzZmo3OVFZcjlaUmprMnVwWDVra2lQenZ3ZThxRyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly8xOTIuMTY4LjEzNy4xIjtzOjU6InJvdXRlIjtzOjc6ImxhbmRpbmciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1775817935),('r6b375IT2kUvXPUdGQRxs7Kl7KeGijLLtOMHmykD',NULL,'172.18.0.1','Dalvik/2.1.0 (Linux; U; Android 16; RMX3840 Build/BP2A.250605.015)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWHpGVmI4aURIT282aWJqeHBUUEZMOVNHUVFIQU5qMXE0N1o2WHhVVCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly8xOTIuMTY4LjEzNy4xIjtzOjU6InJvdXRlIjtzOjc6ImxhbmRpbmciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1775818389),('rFzOLPRok8ukEXjfpDVeFH95b0gdU3iGWDzNqhPO',NULL,'172.18.0.1','Dalvik/2.1.0 (Linux; U; Android 16; RMX3840 Build/BP2A.250605.015)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiSm4yNHRnakhLdDJvWElFeTZWNk01dzR4SXVMVVhOZWJNMFQ0Z0lBRyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly8xOTIuMTY4LjEzNy4xIjtzOjU6InJvdXRlIjtzOjc6ImxhbmRpbmciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1775819906),('Sl2HAY2GxZUoPkb2VNOUdJFp4XgYCKAfjcyJA7Ff',5,'172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiWjVCcUZRY0dNblViUUpTRldYaEtKU1podEpuRVJWZHFwVG5Nd1FZcyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjY6Imh0dHA6Ly9sb2NhbGhvc3QvZGFzaGJvYXJkIjtzOjU6InJvdXRlIjtzOjk6ImRhc2hib2FyZCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjU7czo0OiJhdXRoIjthOjE6e3M6MjE6InBhc3N3b3JkX2NvbmZpcm1lZF9hdCI7aToxNzc1ODIwMzMzO319',1775820512),('UFhgsH3fbyUpk32gGk7iXViQPljAMgenBeEDEE7e',NULL,'172.18.0.1','Dalvik/2.1.0 (Linux; U; Android 16; RMX3840 Build/BP2A.250605.015)','YTozOntzOjY6Il90b2tlbiI7czo0MDoia2NwWENDYTd6czYyaEJJUVh6QWRPR3UxSjE0RVFIZjB4aG9ybmVxTSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly8xOTIuMTY4LjEzNy4xIjtzOjU6InJvdXRlIjtzOjc6ImxhbmRpbmciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1775818731),('UszFqkD6TyrQ0jq3FGpzGhUvDvYYmr9yzZZc1Wbv',NULL,'172.18.0.1','Dalvik/2.1.0 (Linux; U; Android 16; RMX3840 Build/BP2A.250605.015)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiU1Z2V2ttWGxwVWQyZnVoSUpWSDJDbXZvNjJyOHhkMHdBVURMeGJmSiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjA6Imh0dHA6Ly8xOTIuMTY4LjEzNy4xIjtzOjU6InJvdXRlIjtzOjc6ImxhbmRpbmciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1775817965);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_movements`
--

DROP TABLE IF EXISTS `stock_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_movements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `medicine_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `supplier_id` bigint unsigned DEFAULT NULL,
  `type` enum('stock_in','stock_out') COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` enum('purchase','adjustment','return','sale','expired','damaged','transfer') COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `quantity_before` int NOT NULL,
  `quantity_after` int NOT NULL,
  `batch_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `unit_cost` decimal(10,2) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_movements_medicine_id_foreign` (`medicine_id`),
  KEY `stock_movements_user_id_foreign` (`user_id`),
  KEY `stock_movements_supplier_id_foreign` (`supplier_id`),
  CONSTRAINT `stock_movements_medicine_id_foreign` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_movements_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_movements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_movements`
--

LOCK TABLES `stock_movements` WRITE;
/*!40000 ALTER TABLE `stock_movements` DISABLE KEYS */;
INSERT INTO `stock_movements` VALUES (1,2,1,NULL,'stock_in','purchase',5,80,85,'AX2024001','2028-07-08',15.00,NULL,'2026-04-09 15:08:15','2026-04-09 15:08:15');
/*!40000 ALTER TABLE `stock_movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `license_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `suppliers_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,'PharmaCorp Inc.','orders@pharmacorp.com','(02) 8888-9999','123 Pharma Street, Makati City','Juan Dela Cruz',NULL,'2026-04-09 14:23:34','2026-04-09 14:23:34'),(2,'MediSupply Philippines','sales@medisupply.ph','(02) 7777-8888','456 Medical Ave, Quezon City','Maria Santos',NULL,'2026-04-09 14:23:34','2026-04-09 14:23:34'),(3,'HealthPlus Distributors','info@healthplus.com.ph','(02) 6666-7777','789 Health Blvd, Taguig City','Pedro Reyes',NULL,'2026-04-09 14:23:34','2026-04-09 14:23:34'),(4,'Volvo','iyaha@gmail.com','(094) 335-5435','ilaha','Johnard','Lic203-5344','2026-04-09 16:50:53','2026-04-09 16:50:53');
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'staff',
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','admin@pharmacy.com',NULL,'$2y$12$/Rcun7qYSgLCBGAN45bIOeay9gPWplPN5M9SRu5MdJLqLeKYYqDeK','administrator','09171234567','Main Branch, Manila',NULL,1,'2026-04-09 14:23:33','2026-04-09 14:38:39'),(2,'Staff','staff@pharmacy.com',NULL,'$2y$12$ovypNbMrnEoTa0vf9b2.6.DSKk382dl1c3QKCRCioaYeiYi8jvk4u','staff','09171234568','Main Branch, Manila',NULL,1,'2026-04-09 14:23:33','2026-04-09 14:23:33'),(5,'Johnard Plati','estose@gmail.com',NULL,'$2y$12$zS5K/uST3kxCFY/vkUUByef2gkqmKB.p49/c31fDque9FmuEOWgH2','pharmacist','09457661153','Ilaha',NULL,1,'2026-04-10 18:25:03','2026-04-10 18:25:03');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-11 13:17:23
