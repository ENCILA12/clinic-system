-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: clinic_db
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appointment_id` varchar(50) NOT NULL,
  `patient_id` varchar(50) NOT NULL,
  `dentist_name` varchar(100) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `procedure_name` varchar(100) NOT NULL,
  `estimated_duration` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `clinic_id` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `fk_appointments_clinic` (`clinic_id`),
  CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_appointments_clinic` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appointments`
--

LOCK TABLES `appointments` WRITE;
/*!40000 ALTER TABLE `appointments` DISABLE KEYS */;
INSERT INTO `appointments` VALUES (1,'APT-2607-001','PT-2607-001','Dr. John Smith','2026-07-26','00:12:00','Consultation','1 hour','Pending','na','2026-07-25 08:40:25',1),(2,'APT-2607-002','PT-2607-002','Dr. John Smith','2002-06-25','11:11:00','Consultation','30 mins','No Show','','2026-07-25 09:47:34',1),(3,'APT-2607-003','PT-2607-002','Dr. John Smith','2026-06-25','11:00:00','Braces Adjustment','1 hour','Pending','','2026-07-25 10:18:32',1),(4,'APT-2607-004','PT-2607-0003','dentist','2026-07-26','13:00:00','Braces Adjustment','TBD','Pending','PUBLIC BOOKING: SAKIT PO','2026-07-25 10:27:20',1),(5,'APT-2607-005','PT-2607-0005','iganpaul','2026-07-26','18:28:00','Braces Adjustment','TBD','Pending','PUBLIC BOOKING: ANG SAKIT\r\n','2026-07-25 10:28:41',1);
/*!40000 ALTER TABLE `appointments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attachments`
--

DROP TABLE IF EXISTS `attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` varchar(50) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `clinic_id` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `fk_attachments_clinic` (`clinic_id`),
  CONSTRAINT `attachments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_attachments_clinic` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attachments`
--

LOCK TABLES `attachments` WRITE;
/*!40000 ALTER TABLE `attachments` DISABLE KEYS */;
INSERT INTO `attachments` VALUES (1,'PT-2607-002','X-Ray','747674264_4699567030271250_5970220390835577646_n.jpg','uploads/attachments/ATT_6a647cb0e478f_1784970416.jpg','2026-07-25 09:06:56',1),(2,'PT-2607-002','Consent Form','747674264_4699567030271250_5970220390835577646_n.jpg','uploads/attachments/ATT_6a647cbd2b4d4_1784970429.jpg','2026-07-25 09:07:09',1);
/*!40000 ALTER TABLE `attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `billing`
--

DROP TABLE IF EXISTS `billing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` varchar(50) NOT NULL,
  `patient_id` varchar(50) NOT NULL,
  `subtotal` decimal(10,2) DEFAULT 0.00,
  `discount` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `amount_paid` decimal(10,2) DEFAULT 0.00,
  `balance` decimal(10,2) DEFAULT 0.00,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT 'Unpaid',
  `hmo_provider` varchar(100) DEFAULT NULL,
  `hmo_approval_code` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `clinic_id` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_id` (`invoice_id`),
  KEY `patient_id` (`patient_id`),
  KEY `fk_billing_clinic` (`clinic_id`),
  CONSTRAINT `billing_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_billing_clinic` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `billing`
--

LOCK TABLES `billing` WRITE;
/*!40000 ALTER TABLE `billing` DISABLE KEYS */;
INSERT INTO `billing` VALUES (1,'INV-2607-0001','PT-2607-002',1000.00,10.00,990.00,1000.00,0.00,'Cash','Paid',NULL,NULL,'2026-07-25 08:55:54',1),(2,'INV-2607-0002','PT-2607-001',1000.00,0.00,1000.00,0.00,1000.00,'GCash','Unpaid',NULL,NULL,'2026-07-25 08:56:37',1),(3,'INV-2607-0003','PT-2607-002',1000.00,0.00,1000.00,0.00,1000.00,'GCash','Unpaid',NULL,NULL,'2026-07-25 08:57:11',1),(4,'INV-2607-0004','PT-2607-001',2800.00,0.00,2800.00,3000.00,0.00,'Cash','Paid',NULL,NULL,'2026-07-25 08:57:51',1),(5,'INV-2607-0005','PT-2607-0005',1500.00,0.00,1500.00,0.00,1500.00,'Cash','Unpaid','Maxicare','','2026-07-25 10:34:21',1);
/*!40000 ALTER TABLE `billing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `billing_items`
--

DROP TABLE IF EXISTS `billing_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billing_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` varchar(50) NOT NULL,
  `service_name` varchar(150) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `clinic_id` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `fk_billing_items_clinic` (`clinic_id`),
  CONSTRAINT `billing_items_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `billing` (`invoice_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_billing_items_clinic` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `billing_items`
--

LOCK TABLES `billing_items` WRITE;
/*!40000 ALTER TABLE `billing_items` DISABLE KEYS */;
INSERT INTO `billing_items` VALUES (1,'INV-2607-0001','Oral Prophylaxis',1,1000.00,1),(2,'INV-2607-0002','Oral Prophylaxis',1,1000.00,1),(3,'INV-2607-0003','Oral Prophylaxis',1,1000.00,1),(4,'INV-2607-0004','Oral Prophylaxis',1,1000.00,1),(5,'INV-2607-0004','Oral Prophylaxis',1,1000.00,1),(6,'INV-2607-0004','Tooth Extraction',1,800.00,1),(7,'INV-2607-0005','Braces Adjustment',1,1500.00,1);
/*!40000 ALTER TABLE `billing_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clinics`
--

DROP TABLE IF EXISTS `clinics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clinics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `subscription_status` varchar(50) DEFAULT 'ACTIVE',
  `subscription_expiry` date DEFAULT NULL,
  `subscription_price` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `logo_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clinics`
--

LOCK TABLES `clinics` WRITE;
/*!40000 ALTER TABLE `clinics` DISABLE KEYS */;
INSERT INTO `clinics` VALUES (1,'Main Clinic','ACTIVE',NULL,0.00,'2026-09-06 12:43:07',NULL),(3,'Bulihan Clinic','Suspended','2026-10-06',599.00,'2026-09-06 12:59:35',NULL);
/*!40000 ALTER TABLE `clinics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dental_records`
--

DROP TABLE IF EXISTS `dental_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dental_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` varchar(50) NOT NULL,
  `tooth_number` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `diagnosis` text DEFAULT NULL,
  `treatment` text DEFAULT NULL,
  `dentist_name` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `clinic_id` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `fk_dental_records_clinic` (`clinic_id`),
  CONSTRAINT `dental_records_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_dental_records_clinic` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dental_records`
--

LOCK TABLES `dental_records` WRITE;
/*!40000 ALTER TABLE `dental_records` DISABLE KEYS */;
INSERT INTO `dental_records` VALUES (1,'PT-2607-002',26,'Cavity','','','','','2026-07-25 08:47:39',1),(2,'PT-2607-002',26,'Healthy','','','','','2026-07-25 08:47:54',1),(3,'PT-2607-002',9,'Cavity','','','','','2026-07-25 09:35:00',1),(4,'PT-2607-002',17,'Cavity','','','dentist','','2026-09-06 12:15:49',1);
/*!40000 ALTER TABLE `dental_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dentists`
--

DROP TABLE IF EXISTS `dentists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dentists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(150) NOT NULL,
  `specialization` varchar(100) NOT NULL,
  `prc_license` varchar(50) NOT NULL,
  `schedule` varchar(150) NOT NULL,
  `consultation_fee` decimal(10,2) DEFAULT 0.00,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `clinic_id` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `fk_dentists_clinic` (`clinic_id`),
  CONSTRAINT `fk_dentists_clinic` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dentists`
--

LOCK TABLES `dentists` WRITE;
/*!40000 ALTER TABLE `dentists` DISABLE KEYS */;
INSERT INTO `dentists` VALUES (1,'Dr. John Smith','General Dentistry','1234567','Mon-Wed-Fri, 9AM-5PM',500.00,1,'2026-07-25 09:01:49',1),(2,'Dr. Sarah Lee','Orthodontist','7654321','Tue-Thu, 10AM-4PM',800.00,0,'2026-07-25 09:01:49',1),(3,'igan paul','orthodox','1235','',500.00,1,'2026-07-25 11:32:55',1);
/*!40000 ALTER TABLE `dentists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_name` varchar(150) NOT NULL,
  `current_stock` int(11) DEFAULT 0,
  `minimum_stock` int(11) DEFAULT 10,
  `expiration_date` date DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `clinic_id` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `fk_inventory_clinic` (`clinic_id`),
  CONSTRAINT `fk_inventory_clinic` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory`
--

LOCK TABLES `inventory` WRITE;
/*!40000 ALTER TABLE `inventory` DISABLE KEYS */;
INSERT INTO `inventory` VALUES (1,'Gloves',50,100,'2027-07-25','2026-07-25 08:58:52',1),(2,'Syringes',20,50,'2027-01-25','2026-07-25 08:58:52',1),(3,'Composite Resin',5,10,'2028-07-25','2026-07-25 08:58:52',1),(4,'Cotton Rolls',200,50,NULL,'2026-07-25 08:58:52',1),(5,'Anesthetic',100,20,'2026-07-14','2026-07-25 09:18:37',1),(6,'Dental Cement',2,5,'2026-07-20','2026-07-25 08:58:52',1),(7,'Impression Material',15,10,'2026-10-25','2026-07-25 08:58:52',1),(12,'Face Masks (Surgical)',500,100,NULL,'2026-07-25 10:03:35',1),(13,'Saliva Ejectors',1000,200,NULL,'2026-07-25 10:03:35',1),(14,'Dental Bibs',800,150,NULL,'2026-07-25 10:03:35',1),(15,'Local Anesthetic (Lidocaine)',100,20,'2027-12-31','2026-07-25 10:03:35',1),(16,'Dental Needles (Short/Long)',300,50,'2028-06-30','2026-07-25 10:03:35',1),(17,'Prophy Paste',40,10,'2026-10-15','2026-07-25 10:03:35',1),(18,'Fluoride Varnish',30,5,'2026-08-20','2026-07-25 10:03:35',1),(19,'Etching Gel (Phosphoric Acid)',25,5,'2027-02-14','2026-07-25 10:03:35',1),(20,'Bonding Agent',15,3,'2027-05-10','2026-07-25 10:03:35',1),(21,'Alginate Impression Material',20,5,'2026-11-30','2026-07-25 10:03:35',1),(22,'Temporary Crown Material',10,2,'2026-09-01','2026-07-25 10:03:35',1),(23,'Root Canal K-Files',50,10,NULL,'2026-07-25 10:03:35',1),(24,'Gutta Percha Points',150,30,'2029-01-01','2026-07-25 10:03:35',1),(25,'Paper Points',200,50,'2029-01-01','2026-07-25 10:03:35',1),(26,'Handpiece Lubricant Oil',10,2,'2028-03-15','2026-07-25 10:03:35',1),(27,'Sterilization Pouches',1000,200,NULL,'2026-07-25 10:03:35',1),(28,'Disinfectant Wipes (CaviWipes)',20,5,'2026-12-31','2026-07-25 10:03:35',1),(29,'Suture Materials (Silk)',40,10,'2028-07-20','2026-07-25 10:03:35',1),(30,'Gauze Pads 2x2',2000,300,NULL,'2026-07-25 10:03:35',1),(31,'Topical Anesthetic Gel',15,3,'2027-01-10','2026-07-25 10:03:35',1);
/*!40000 ALTER TABLE `inventory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `patients`
--

DROP TABLE IF EXISTS `patients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `patients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` varchar(50) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `birthday` date NOT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `emergency_contact` varchar(100) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `blood_type` varchar(10) DEFAULT NULL,
  `smoking_status` varchar(50) DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `medical_conditions` text DEFAULT NULL,
  `current_medications` text DEFAULT NULL,
  `pregnancy` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `clinic_id` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `patient_id` (`patient_id`),
  KEY `fk_patients_clinic` (`clinic_id`),
  CONSTRAINT `fk_patients_clinic` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patients`
--

LOCK TABLES `patients` WRITE;
/*!40000 ALTER TABLE `patients` DISABLE KEYS */;
INSERT INTO `patients` VALUES (1,'PT-2607-001','Test User','1990-01-01',36,'','','','','','','','','','','','','2026-07-25 08:30:35',1),(2,'PT-2607-002','igan encila a','2552-02-25',0,'Male','09469260165','iganpulencila01@gmail.com','blk 31 lot 22','','','Unknown','Non-Smoker','1','1','1','Not Applicable / No','2026-07-25 08:32:03',1),(4,'PT-2607-0003','IGANP PAUL ENCILA','2002-01-01',24,NULL,'912239219371',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-07-25 10:27:20',1),(5,'PT-2607-0005','AIZEL MAY CARUYAN','2002-01-01',24,'Male','091203812','','','','','Unknown','Non-Smoker','','dasds','','Not Applicable / No','2026-07-25 10:28:41',1),(6,'PT-2609-006','igan encila','2026-09-15',0,'Female','09469260165','iganpulencila01@gmail.com','blk 31 lot 22','','','A+','Occasional','sad','sad','asd','Not Applicable / No','2026-09-06 13:07:28',1);
/*!40000 ALTER TABLE `patients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `treatments`
--

DROP TABLE IF EXISTS `treatments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `treatments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `treatment_id` varchar(50) NOT NULL,
  `patient_id` varchar(50) NOT NULL,
  `dentist_name` varchar(100) NOT NULL,
  `chief_complaint` text DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `treatment_plan` text DEFAULT NULL,
  `procedure_done` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `follow_up_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `materials_used` text DEFAULT NULL,
  `clinic_id` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `fk_treatments_clinic` (`clinic_id`),
  CONSTRAINT `fk_treatments_clinic` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  CONSTRAINT `treatments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `treatments`
--

LOCK TABLES `treatments` WRITE;
/*!40000 ALTER TABLE `treatments` DISABLE KEYS */;
INSERT INTO `treatments` VALUES (1,'TX-2607-001','PT-2607-002','Dr. Sarah Lee','a','a','a','Consultation Only','aa','2026-07-25','2026-07-25 08:51:43',NULL,1),(2,'TX-2607-002','PT-2607-002','iganpaul','a','a','na','Dental Fillings (Pasta)','','2026-06-20','2026-07-25 10:10:16',NULL,1);
/*!40000 ALTER TABLE `treatments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `clinic_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `fk_users_clinic` (`clinic_id`),
  CONSTRAINT `fk_users_clinic` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin',NULL,NULL,'$2y$10$ERnXPXsjuVmlAsEKJXL5NeYlg2elWQDEBFuhz2OAmMe.nsp8Lazxm','Admin','2026-07-25 09:11:23',1),(2,'reception',NULL,NULL,'$2y$10$eUyOszLb0jhMYZTKuqoVBeKj1a.G.YmSwv9TQ9x8n1lcFuPyKxCZu','Receptionist','2026-07-25 09:11:23',1),(3,'dentist',NULL,NULL,'$2y$10$DXPPSUa8.vj4KtFVVN1YIedHvfDDSqIed.O0GJ9wrAsTL2.k.k7.q','Dentist','2026-07-25 09:11:23',1),(4,'assistant',NULL,NULL,'$2y$10$O0rMM8SiFsSZThNbBWeW1OFh4kp73LYoqW/lm6Bv.XubwY1A8nWvq','Assistant','2026-07-25 09:11:23',1),(7,'aa',NULL,'aa','$2y$10$HYmU5mE8YsNf6D3NI3TRo..KVWV6/ya7q5P5gHW3fZw3kDvShnxd.','Assistant','2026-09-06 12:20:40',1),(8,'superadmin',NULL,'System Owner','$2y$10$i5jinntfYLCm.vITF.4EE.iRy2m/ra14PiI19Ho/EW5DMX6gHeF8O','Superadmin','2026-09-06 12:54:02',NULL),(10,'uriel','uriel@gmail.com','Clinic Administrator','$2y$10$zXGlVpKMJjwky75/kMPowu37dpNI6fXvlq0qiw5kX.PQp42erqrki','Admin','2026-09-06 12:59:35',3);
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

-- Dump completed on 2026-09-06 21:27:29
