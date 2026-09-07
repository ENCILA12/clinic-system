-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 07, 2026 at 06:10 PM
-- Server version: 11.8.8-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u300133080_Dental_Saas`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
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
  `clinic_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `appointment_id`, `patient_id`, `dentist_name`, `appointment_date`, `appointment_time`, `procedure_name`, `estimated_duration`, `status`, `notes`, `created_at`, `clinic_id`) VALUES
(1, 'APT-2607-001', 'PT-2607-001', 'Dr. John Smith', '2026-07-26', '00:12:00', 'Consultation', '1 hour', 'Pending', 'na', '2026-07-25 08:40:25', 1),
(2, 'APT-2607-002', 'PT-2607-002', 'Dr. John Smith', '2002-06-25', '11:11:00', 'Consultation', '30 mins', 'No Show', '', '2026-07-25 09:47:34', 1),
(3, 'APT-2607-003', 'PT-2607-002', 'Dr. John Smith', '2026-06-25', '11:00:00', 'Braces Adjustment', '1 hour', 'Pending', '', '2026-07-25 10:18:32', 1),
(4, 'APT-2607-004', 'PT-2607-0003', 'dentist', '2026-07-26', '13:00:00', 'Braces Adjustment', 'TBD', 'Pending', 'PUBLIC BOOKING: SAKIT PO', '2026-07-25 10:27:20', 1),
(5, 'APT-2607-005', 'PT-2607-0005', 'iganpaul', '2026-07-26', '18:28:00', 'Braces Adjustment', 'TBD', 'Pending', 'PUBLIC BOOKING: ANG SAKIT\r\n', '2026-07-25 10:28:41', 1),
(7, 'APT-2609-006', 'PT-2609-006', 'dentist', '2026-09-07', '22:45:00', 'Root Canal Treatment', '2 hours', 'Pending', 'asd', '2026-09-06 14:46:01', 1);

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `id` int(11) NOT NULL,
  `patient_id` varchar(50) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `clinic_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `attachments`
--

INSERT INTO `attachments` (`id`, `patient_id`, `file_type`, `file_name`, `file_path`, `uploaded_at`, `clinic_id`) VALUES
(1, 'PT-2607-002', 'X-Ray', '747674264_4699567030271250_5970220390835577646_n.jpg', 'uploads/attachments/ATT_6a647cb0e478f_1784970416.jpg', '2026-07-25 09:06:56', 1),
(2, 'PT-2607-002', 'Consent Form', '747674264_4699567030271250_5970220390835577646_n.jpg', 'uploads/attachments/ATT_6a647cbd2b4d4_1784970429.jpg', '2026-07-25 09:07:09', 1);

-- --------------------------------------------------------

--
-- Table structure for table `billing`
--

CREATE TABLE `billing` (
  `id` int(11) NOT NULL,
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
  `clinic_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `billing`
--

INSERT INTO `billing` (`id`, `invoice_id`, `patient_id`, `subtotal`, `discount`, `total_amount`, `amount_paid`, `balance`, `payment_method`, `payment_status`, `hmo_provider`, `hmo_approval_code`, `created_at`, `clinic_id`) VALUES
(1, 'INV-2607-0001', 'PT-2607-002', 1000.00, 10.00, 990.00, 1000.00, 0.00, 'Cash', 'Paid', NULL, NULL, '2026-07-25 08:55:54', 1),
(2, 'INV-2607-0002', 'PT-2607-001', 1000.00, 0.00, 1000.00, 0.00, 1000.00, 'GCash', 'Unpaid', NULL, NULL, '2026-07-25 08:56:37', 1),
(3, 'INV-2607-0003', 'PT-2607-002', 1000.00, 0.00, 1000.00, 0.00, 1000.00, 'GCash', 'Unpaid', NULL, NULL, '2026-07-25 08:57:11', 1),
(4, 'INV-2607-0004', 'PT-2607-001', 2800.00, 0.00, 2800.00, 3000.00, 0.00, 'Cash', 'Paid', NULL, NULL, '2026-07-25 08:57:51', 1),
(5, 'INV-2607-0005', 'PT-2607-0005', 1500.00, 0.00, 1500.00, 0.00, 1500.00, 'Cash', 'Unpaid', 'Maxicare', '', '2026-07-25 10:34:21', 1);

-- --------------------------------------------------------

--
-- Table structure for table `billing_items`
--

CREATE TABLE `billing_items` (
  `id` int(11) NOT NULL,
  `invoice_id` varchar(50) NOT NULL,
  `service_name` varchar(150) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `clinic_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `billing_items`
--

INSERT INTO `billing_items` (`id`, `invoice_id`, `service_name`, `quantity`, `price`, `clinic_id`) VALUES
(1, 'INV-2607-0001', 'Oral Prophylaxis', 1, 1000.00, 1),
(2, 'INV-2607-0002', 'Oral Prophylaxis', 1, 1000.00, 1),
(3, 'INV-2607-0003', 'Oral Prophylaxis', 1, 1000.00, 1),
(4, 'INV-2607-0004', 'Oral Prophylaxis', 1, 1000.00, 1),
(5, 'INV-2607-0004', 'Oral Prophylaxis', 1, 1000.00, 1),
(6, 'INV-2607-0004', 'Tooth Extraction', 1, 800.00, 1),
(7, 'INV-2607-0005', 'Braces Adjustment', 1, 1500.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `clinics`
--

CREATE TABLE `clinics` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `subscription_status` varchar(50) DEFAULT 'ACTIVE',
  `subscription_expiry` date DEFAULT NULL,
  `subscription_price` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `logo_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clinics`
--

INSERT INTO `clinics` (`id`, `name`, `subscription_status`, `subscription_expiry`, `subscription_price`, `created_at`, `logo_url`) VALUES
(1, 'Main Clinic', 'Active', NULL, 0.00, '2026-09-06 12:43:07', NULL),
(3, 'Bulihan Clinic', 'Suspended', '2026-10-06', 599.00, '2026-09-06 12:59:35', NULL),
(4, 'NB Magbanua dental clinic', 'Active', '2026-09-30', 0.00, '2026-09-06 13:31:27', 'uploads/logos/clinic_4_1788736555.png');

-- --------------------------------------------------------

--
-- Table structure for table `dental_records`
--

CREATE TABLE `dental_records` (
  `id` int(11) NOT NULL,
  `patient_id` varchar(50) NOT NULL,
  `tooth_number` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `diagnosis` text DEFAULT NULL,
  `treatment` text DEFAULT NULL,
  `dentist_name` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `clinic_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `dental_records`
--

INSERT INTO `dental_records` (`id`, `patient_id`, `tooth_number`, `status`, `diagnosis`, `treatment`, `dentist_name`, `notes`, `created_at`, `clinic_id`) VALUES
(1, 'PT-2607-002', 26, 'Cavity', '', '', '', '', '2026-07-25 08:47:39', 1),
(2, 'PT-2607-002', 26, 'Healthy', '', '', '', '', '2026-07-25 08:47:54', 1),
(3, 'PT-2607-002', 9, 'Cavity', '', '', '', '', '2026-07-25 09:35:00', 1),
(4, 'PT-2607-002', 17, 'Cavity', '', '', 'dentist', '', '2026-09-06 12:15:49', 1);

-- --------------------------------------------------------

--
-- Table structure for table `dentists`
--

CREATE TABLE `dentists` (
  `id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `specialization` varchar(100) NOT NULL,
  `prc_license` varchar(50) NOT NULL,
  `schedule` varchar(150) NOT NULL,
  `consultation_fee` decimal(10,2) DEFAULT 0.00,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `clinic_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `dentists`
--

INSERT INTO `dentists` (`id`, `full_name`, `specialization`, `prc_license`, `schedule`, `consultation_fee`, `is_available`, `created_at`, `clinic_id`) VALUES
(1, 'Dr. John Smith', 'General Dentistry', '1234567', 'Mon-Wed-Fri, 9AM-5PM', 500.00, 1, '2026-07-25 09:01:49', 1),
(2, 'Dr. Sarah Lee', 'Orthodontist', '7654321', 'Tue-Thu, 10AM-4PM', 800.00, 0, '2026-07-25 09:01:49', 1),
(3, 'igan paul', 'orthodox', '1235', '', 500.00, 1, '2026-07-25 11:32:55', 1);

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `clinic_id` int(11) NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `expense_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `item_name` varchar(150) NOT NULL,
  `current_stock` int(11) DEFAULT 0,
  `minimum_stock` int(11) DEFAULT 10,
  `expiration_date` date DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `clinic_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `item_name`, `current_stock`, `minimum_stock`, `expiration_date`, `last_updated`, `clinic_id`) VALUES
(1, 'Gloves', 50, 100, '2027-07-25', '2026-07-25 08:58:52', 1),
(2, 'Syringes', 20, 50, '2027-01-25', '2026-07-25 08:58:52', 1),
(3, 'Composite Resin', 5, 10, '2028-07-25', '2026-07-25 08:58:52', 1),
(4, 'Cotton Rolls', 200, 50, NULL, '2026-07-25 08:58:52', 1),
(5, 'Anesthetic', 100, 20, '2026-07-14', '2026-07-25 09:18:37', 1),
(6, 'Dental Cement', 2, 5, '2026-07-20', '2026-07-25 08:58:52', 1),
(7, 'Impression Material', 15, 10, '2026-10-25', '2026-07-25 08:58:52', 1),
(12, 'Face Masks (Surgical)', 500, 100, NULL, '2026-07-25 10:03:35', 1),
(13, 'Saliva Ejectors', 1000, 200, NULL, '2026-07-25 10:03:35', 1),
(14, 'Dental Bibs', 800, 150, NULL, '2026-07-25 10:03:35', 1),
(15, 'Local Anesthetic (Lidocaine)', 100, 20, '2027-12-31', '2026-07-25 10:03:35', 1),
(16, 'Dental Needles (Short/Long)', 300, 50, '2028-06-30', '2026-07-25 10:03:35', 1),
(17, 'Prophy Paste', 40, 10, '2026-10-15', '2026-07-25 10:03:35', 1),
(18, 'Fluoride Varnish', 30, 5, '2026-08-20', '2026-07-25 10:03:35', 1),
(19, 'Etching Gel (Phosphoric Acid)', 25, 5, '2027-02-14', '2026-07-25 10:03:35', 1),
(20, 'Bonding Agent', 15, 3, '2027-05-10', '2026-07-25 10:03:35', 1),
(21, 'Alginate Impression Material', 20, 5, '2026-11-30', '2026-07-25 10:03:35', 1),
(22, 'Temporary Crown Material', 10, 2, '2026-09-01', '2026-07-25 10:03:35', 1),
(23, 'Root Canal K-Files', 50, 10, NULL, '2026-07-25 10:03:35', 1),
(24, 'Gutta Percha Points', 150, 30, '2029-01-01', '2026-07-25 10:03:35', 1),
(25, 'Paper Points', 200, 50, '2029-01-01', '2026-07-25 10:03:35', 1),
(26, 'Handpiece Lubricant Oil', 10, 2, '2028-03-15', '2026-07-25 10:03:35', 1),
(27, 'Sterilization Pouches', 1000, 200, NULL, '2026-07-25 10:03:35', 1),
(28, 'Disinfectant Wipes (CaviWipes)', 20, 5, '2026-12-31', '2026-07-25 10:03:35', 1),
(29, 'Suture Materials (Silk)', 40, 10, '2028-07-20', '2026-07-25 10:03:35', 1),
(30, 'Gauze Pads 2x2', 2000, 300, NULL, '2026-07-25 10:03:35', 1),
(31, 'Topical Anesthetic Gel', 15, 3, '2027-01-10', '2026-07-25 10:03:35', 1),
(32, 'Betadine', 5, 10, '2026-10-30', '2026-09-06 17:24:58', 3),
(33, 'try2', 66, 10, '2026-10-30', '2026-09-06 17:30:04', 3);

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
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
  `clinic_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `patient_id`, `full_name`, `birthday`, `age`, `gender`, `contact_number`, `email`, `address`, `emergency_contact`, `occupation`, `blood_type`, `smoking_status`, `allergies`, `medical_conditions`, `current_medications`, `pregnancy`, `created_at`, `clinic_id`) VALUES
(1, 'PT-2607-001', 'Test User', '1990-01-01', 36, '', '', '', '', '', '', '', '', '', '', '', '', '2026-07-25 08:30:35', 1),
(2, 'PT-2607-002', 'igan encila a', '2552-02-25', 0, 'Male', '09469260165', 'iganpulencila01@gmail.com', 'blk 31 lot 22', '', '', 'Unknown', 'Non-Smoker', '1', '1', '1', 'Not Applicable / No', '2026-07-25 08:32:03', 1),
(4, 'PT-2607-0003', 'IGANP PAUL ENCILA', '2002-01-01', 24, NULL, '912239219371', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-25 10:27:20', 1),
(5, 'PT-2607-0005', 'AIZEL MAY CARUYAN', '2002-01-01', 24, 'Male', '091203812', '', '', '', '', 'Unknown', 'Non-Smoker', '', 'dasds', '', 'Not Applicable / No', '2026-07-25 10:28:41', 1),
(6, 'PT-2609-006', 'igan encila', '2026-09-15', 0, 'Female', '09469260165', 'iganpulencila01@gmail.com', 'blk 31 lot 22', '', '', 'A+', 'Occasional', 'sad', 'sad', 'asd', 'Not Applicable / No', '2026-09-06 13:07:28', 1),
(7, 'PT-2609-007', 'igan encila', '2026-09-06', 0, 'Male', '09469260165', 'iganpulencila01@gmail.com', 'blk 31 lot 22', '', '', 'Unknown', 'Non-Smoker', '1', 'asd', 'asd', 'Not Applicable / No', '2026-09-06 14:35:26', 1);

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` int(11) NOT NULL,
  `clinic_id` int(11) NOT NULL,
  `patient_id` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `dentist_name` varchar(100) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prescription_items`
--

CREATE TABLE `prescription_items` (
  `id` int(11) NOT NULL,
  `prescription_id` int(11) NOT NULL,
  `medicine_name` varchar(150) NOT NULL,
  `dosage` varchar(100) NOT NULL,
  `frequency` varchar(100) NOT NULL,
  `duration` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `treatments`
--

CREATE TABLE `treatments` (
  `id` int(11) NOT NULL,
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
  `clinic_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `treatments`
--

INSERT INTO `treatments` (`id`, `treatment_id`, `patient_id`, `dentist_name`, `chief_complaint`, `diagnosis`, `treatment_plan`, `procedure_done`, `notes`, `follow_up_date`, `created_at`, `materials_used`, `clinic_id`) VALUES
(1, 'TX-2607-001', 'PT-2607-002', 'Dr. Sarah Lee', 'a', 'a', 'a', 'Consultation Only', 'aa', '2026-07-25', '2026-07-25 08:51:43', NULL, 1),
(2, 'TX-2607-002', 'PT-2607-002', 'iganpaul', 'a', 'a', 'na', 'Dental Fillings (Pasta)', '', '2026-06-20', '2026-07-25 10:10:16', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `clinic_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `full_name`, `password`, `role`, `created_at`, `clinic_id`) VALUES
(1, 'admin', NULL, NULL, '$2y$10$ERnXPXsjuVmlAsEKJXL5NeYlg2elWQDEBFuhz2OAmMe.nsp8Lazxm', 'Admin', '2026-07-25 09:11:23', 1),
(2, 'reception', NULL, NULL, '$2y$10$eUyOszLb0jhMYZTKuqoVBeKj1a.G.YmSwv9TQ9x8n1lcFuPyKxCZu', 'Receptionist', '2026-07-25 09:11:23', 1),
(3, 'dentist', NULL, NULL, '$2y$10$DXPPSUa8.vj4KtFVVN1YIedHvfDDSqIed.O0GJ9wrAsTL2.k.k7.q', 'Dentist', '2026-07-25 09:11:23', 1),
(4, 'assistant', NULL, NULL, '$2y$10$O0rMM8SiFsSZThNbBWeW1OFh4kp73LYoqW/lm6Bv.XubwY1A8nWvq', 'Assistant', '2026-07-25 09:11:23', 1),
(7, 'aa', NULL, 'aa', '$2y$10$HYmU5mE8YsNf6D3NI3TRo..KVWV6/ya7q5P5gHW3fZw3kDvShnxd.', 'Assistant', '2026-09-06 12:20:40', 1),
(8, 'superadmin', NULL, 'System Owner', '$2y$10$i5jinntfYLCm.vITF.4EE.iRy2m/ra14PiI19Ho/EW5DMX6gHeF8O', 'Superadmin', '2026-09-06 12:54:02', NULL),
(10, 'uriel', 'uriel@gmail.com', 'Clinic Administrator', '$2y$10$jxuJvs0Yt1oWJ9q/aLkgq.N7a4PB4aSo1PEcXla/PG/28DQJu6ZDS', 'Admin', '2026-09-06 12:59:35', 3),
(11, 'mariano', 'marianosonny27@gmail.com', 'Clinic Administrator', '$2y$10$B0Vt6.v4Bm860W43lOcZvutiQdKEq2myNwCT9UAOWhH1zwEo3N15i', 'Admin', '2026-09-06 13:31:27', 4),
(12, 'Try1', NULL, 'Mr. Dante', '$2y$10$na27pCkHZEtWPYFlJnfqXO3EMEWPMwZhGzD/nQN5GwBRLelR7/dnG', 'Assistant', '2026-09-06 17:26:28', 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `fk_appointments_clinic` (`clinic_id`);

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `fk_attachments_clinic` (`clinic_id`);

--
-- Indexes for table `billing`
--
ALTER TABLE `billing`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_id` (`invoice_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `fk_billing_clinic` (`clinic_id`);

--
-- Indexes for table `billing_items`
--
ALTER TABLE `billing_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `fk_billing_items_clinic` (`clinic_id`);

--
-- Indexes for table `clinics`
--
ALTER TABLE `clinics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dental_records`
--
ALTER TABLE `dental_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `fk_dental_records_clinic` (`clinic_id`);

--
-- Indexes for table `dentists`
--
ALTER TABLE `dentists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_dentists_clinic` (`clinic_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clinic_id` (`clinic_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_inventory_clinic` (`clinic_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `patient_id` (`patient_id`),
  ADD KEY `fk_patients_clinic` (`clinic_id`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clinic_id` (`clinic_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `prescription_items`
--
ALTER TABLE `prescription_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prescription_id` (`prescription_id`);

--
-- Indexes for table `treatments`
--
ALTER TABLE `treatments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `fk_treatments_clinic` (`clinic_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_users_clinic` (`clinic_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `billing`
--
ALTER TABLE `billing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `billing_items`
--
ALTER TABLE `billing_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `clinics`
--
ALTER TABLE `clinics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `dental_records`
--
ALTER TABLE `dental_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `dentists`
--
ALTER TABLE `dentists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prescription_items`
--
ALTER TABLE `prescription_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `treatments`
--
ALTER TABLE `treatments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_appointments_clinic` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attachments`
--
ALTER TABLE `attachments`
  ADD CONSTRAINT `attachments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_attachments_clinic` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `billing`
--
ALTER TABLE `billing`
  ADD CONSTRAINT `billing_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_billing_clinic` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `billing_items`
--
ALTER TABLE `billing_items`
  ADD CONSTRAINT `billing_items_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `billing` (`invoice_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_billing_items_clinic` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dental_records`
--
ALTER TABLE `dental_records`
  ADD CONSTRAINT `dental_records_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_dental_records_clinic` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dentists`
--
ALTER TABLE `dentists`
  ADD CONSTRAINT `fk_dentists_clinic` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `fk_inventory_clinic` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `fk_patients_clinic` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prescriptions_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE;

--
-- Constraints for table `prescription_items`
--
ALTER TABLE `prescription_items`
  ADD CONSTRAINT `prescription_items_ibfk_1` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `treatments`
--
ALTER TABLE `treatments`
  ADD CONSTRAINT `fk_treatments_clinic` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `treatments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_clinic` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
