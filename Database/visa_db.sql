-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 05, 2026 at 10:07 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `visa_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(2, 'Admin AkuVisa', 'admin@gmail.com', '$2y$10$SRBQUEgqK5MXQJZAXuknqOzDRiMFlch3BGRB7qwQ3jjhCG5TvpRee', '2026-04-25 06:27:16'),
(3, 'Admin AkuVisa', 'admin123@gmail.com', '$2y$10$MVum3DRyPTD79WxSnkBrquMj1P6TnXBe2Jlxz95Anx.ZBwDS4gRcm', '2026-04-28 13:50:10');

-- --------------------------------------------------------

--
-- Table structure for table `applicants`
--

CREATE TABLE `applicants` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `passport_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applicants`
--

INSERT INTO `applicants` (`id`, `name`, `phone`, `passport_number`, `email`, `created_at`, `updated_at`) VALUES
(154, 'Andi Saputra', '081234567801', 'P10001', 'andi.saputra01@gmail.com', '2026-05-04 11:51:19', NULL),
(155, 'Budi Santoso', '081234567802', 'P10002', 'budi.santoso02@gmail.com', '2026-05-04 11:51:19', NULL),
(156, 'Citra Lestari', '081234567803', 'P10003', 'citra.lestari03@gmail.com', '2026-05-04 11:51:19', NULL),
(157, 'Dewi Anggraini', '081234567804', 'P10004', 'dewi.anggraini04@gmail.com', '2026-05-04 11:51:19', NULL),
(158, 'Eko Prasetyo', '081234567805', 'P10005', 'eko.prasetyo05@gmail.com', '2026-05-04 11:51:19', NULL),
(159, 'Fajar Nugroho', '081234567806', 'P10006', 'fajar.nugroho06@gmail.com', '2026-05-04 11:51:19', NULL),
(160, 'Gina Maharani', '081234567807', 'P10007', 'gina.maharani07@gmail.com', '2026-05-04 11:51:19', NULL),
(161, 'Hendra Wijaya', '081234567808', 'P10008', 'hendra.wijaya08@gmail.com', '2026-05-04 11:51:19', NULL),
(162, 'Intan Permata', '081234567809', 'P10009', 'intan.permata09@gmail.com', '2026-05-04 11:51:19', NULL),
(163, 'Joko Susilo', '081234567810', 'P10010', 'joko.susilo10@gmail.com', '2026-05-04 11:51:19', NULL),
(164, 'Kartika Sari', '081234567811', 'P10011', 'kartika.sari11@gmail.com', '2026-05-04 11:51:19', NULL),
(165, 'Lukman Hakim', '081234567812', 'P10012', 'lukman.hakim12@gmail.com', '2026-05-04 11:51:19', NULL),
(166, 'Maya Putri', '081234567813', 'P10013', 'maya.putri13@gmail.com', '2026-05-04 11:51:19', NULL),
(167, 'Nanda Saputri', '081234567814', 'P10014', 'nanda.saputri14@gmail.com', '2026-05-04 11:51:19', NULL),
(168, 'Oki Pratama', '081234567815', 'P10015', 'oki.pratama15@gmail.com', '2026-05-04 11:51:19', NULL),
(169, 'Putri Ayu', '081234567816', 'P10016', 'putri.ayu16@gmail.com', '2026-05-04 11:51:19', NULL),
(170, 'Qori Anisa', '081234567817', 'P10017', 'qori.anisa17@gmail.com', '2026-05-04 11:51:19', NULL),
(171, 'Rudi Hartono', '081234567818', 'P10018', 'rudi.hartono18@gmail.com', '2026-05-04 11:51:19', NULL),
(172, 'Siti Aisyah', '081234567819', 'P10019', 'siti.aisyah19@gmail.com', '2026-05-04 11:51:19', NULL),
(173, 'Tono Gunawan', '081234567820', 'P10020', 'tono.gunawan20@gmail.com', '2026-05-04 11:51:19', NULL),
(174, 'Umar Faruq', '081234567821', 'P10021', 'umar.faruq21@gmail.com', '2026-05-04 11:51:19', NULL),
(175, 'Vina Oktaviani', '081234567822', 'P10022', 'vina.oktaviani22@gmail.com', '2026-05-04 11:51:19', NULL),
(176, 'Wahyu Setiawan', '081234567823', 'P10023', 'wahyu.setiawan23@gmail.com', '2026-05-04 11:51:19', NULL),
(177, 'Xena Putri', '081234567824', 'P10024', 'xena.putri24@gmail.com', '2026-05-04 11:51:19', NULL),
(178, 'Yudi Kurniawan', '081234567825', 'P10025', 'yudi.kurniawan25@gmail.com', '2026-05-04 11:51:19', NULL),
(179, 'Zahra Amalia', '081234567826', 'P10026', 'zahra.amalia26@gmail.com', '2026-05-04 11:51:19', NULL),
(180, 'Agus Salim', '081234567827', 'P10027', 'agus.salim27@gmail.com', '2026-05-04 11:51:19', NULL),
(181, 'Bella Safitri', '081234567828', 'P10028', 'bella.safitri28@gmail.com', '2026-05-04 11:51:19', NULL),
(182, 'Chandra Wijaksana', '081234567829', 'P10029', 'chandra.wijaksana29@gmail.com', '2026-05-04 11:51:19', NULL),
(183, 'Dimas Saputra', '081234567830', 'P10030', 'dimas.saputra30@gmail.com', '2026-05-04 11:51:19', NULL),
(184, 'Erika Putri', '081234567831', 'P10031', 'erika.putri31@gmail.com', '2026-05-04 11:51:19', NULL),
(185, 'Farhan Akbar', '081234567832', 'P10032', 'farhan.akbar32@gmail.com', '2026-05-04 11:51:19', NULL),
(186, 'Galih Prakoso', '081234567833', 'P10033', 'galih.prakoso33@gmail.com', '2026-05-04 11:51:19', NULL),
(187, 'Hafiz Ramadhan', '081234567834', 'P10034', 'hafiz.ramadhan34@gmail.com', '2026-05-04 11:51:19', NULL),
(188, 'Indra Gunawan', '081234567835', 'P10035', 'indra.gunawan35@gmail.com', '2026-05-04 11:51:19', NULL),
(189, 'Jihan Permata', '081234567836', 'P10036', 'jihan.permata36@gmail.com', '2026-05-04 11:51:19', NULL),
(190, 'Kiki Amelia', '081234567837', 'P10037', 'kiki.amelia37@gmail.com', '2026-05-04 11:51:19', NULL),
(191, 'Lina Marlina', '081234567838', 'P10038', 'lina.marlina38@gmail.com', '2026-05-04 11:51:19', NULL),
(192, 'Mochammad Rizky', '081234567839', 'P10039', 'rizky39@gmail.com', '2026-05-04 11:51:19', NULL),
(193, 'Nina Salsabila', '081234567840', 'P10040', 'nina.salsabila40@gmail.com', '2026-05-04 11:51:19', NULL),
(194, 'Omar Dani', '081234567841', 'P10041', 'omar.dani41@gmail.com', '2026-05-04 11:51:19', NULL),
(195, 'Putra Mahendra', '081234567842', 'P10042', 'putra.mahendra42@gmail.com', '2026-05-04 11:51:19', NULL),
(196, 'Qila Azahra', '081234567843', 'P10043', 'qila.azahra43@gmail.com', '2026-05-04 11:51:19', NULL),
(197, 'Rina Kartika', '081234567844', 'P10044', 'rina.kartika44@gmail.com', '2026-05-04 11:51:19', NULL),
(198, 'Sandy Pratama', '081234567845', 'P10045', 'sandy.pratama45@gmail.com', '2026-05-04 11:51:19', NULL),
(199, 'Tari Wulandari', '081234567846', 'P10046', 'tari.wulandari46@gmail.com', '2026-05-04 11:51:19', NULL),
(200, 'Uci Rahmawati', '081234567847', 'P10047', 'uci.rahmawati47@gmail.com', '2026-05-04 11:51:19', NULL),
(201, 'Vicky Saputra', '081234567848', 'P10048', 'vicky.saputra48@gmail.com', '2026-05-04 11:51:19', NULL),
(202, 'Wulan Sari', '081234567849', 'P10049', 'wulan.sari49@gmail.com', '2026-05-04 11:51:19', NULL),
(203, 'Yoga Pratama', '081234567850', 'P10050', 'yoga.pratama50@gmail.com', '2026-05-04 11:51:19', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `applicant_id` int(11) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `visa_type` varchar(50) DEFAULT NULL,
  `status` enum('in_process','completed','rejected') DEFAULT 'in_process',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `applicant_id`, `country`, `visa_type`, `status`, `created_at`, `updated_at`) VALUES
(151, 203, 'Arab Saudi', 'Student', 'completed', '2026-05-04 11:51:47', NULL),
(152, 202, 'USA', 'Tourist', 'rejected', '2026-05-04 11:51:47', NULL),
(153, 201, 'USA', 'Work', 'in_process', '2026-05-04 11:51:47', NULL),
(154, 200, 'Jepang', 'Tourist', 'rejected', '2026-05-04 11:51:47', NULL),
(155, 199, 'USA', 'Work', 'rejected', '2026-05-04 11:51:47', NULL),
(156, 198, 'Jepang', 'Tourist', 'rejected', '2026-05-04 11:51:47', NULL),
(157, 197, 'Korea Selatan', 'Work', 'rejected', '2026-05-04 11:51:47', NULL),
(158, 196, 'Australia', 'Work', 'completed', '2026-05-04 11:51:47', NULL),
(159, 195, 'Korea Selatan', 'Student', 'in_process', '2026-05-04 11:51:47', NULL),
(160, 194, 'Australia', 'Work', 'completed', '2026-05-04 11:51:47', NULL),
(161, 193, 'Arab Saudi', 'Work', 'rejected', '2026-05-04 11:51:47', NULL),
(162, 192, 'Jepang', 'Work', 'completed', '2026-05-04 11:51:47', NULL),
(163, 191, 'USA', 'Work', 'completed', '2026-05-04 11:51:47', NULL),
(164, 190, 'Arab Saudi', 'Work', 'completed', '2026-05-04 11:51:47', NULL),
(165, 189, 'Arab Saudi', 'Work', 'completed', '2026-05-04 11:51:47', NULL),
(166, 188, 'Jepang', 'Student', 'rejected', '2026-05-04 11:51:47', NULL),
(167, 187, 'USA', 'Tourist', 'completed', '2026-05-04 11:51:47', NULL),
(168, 186, 'USA', 'Tourist', 'in_process', '2026-05-04 11:51:47', NULL),
(169, 185, 'Jepang', 'Student', 'in_process', '2026-05-04 11:51:47', NULL),
(170, 184, 'USA', 'Work', 'completed', '2026-05-04 11:51:47', NULL),
(171, 183, 'USA', 'Student', 'completed', '2026-05-04 11:51:47', NULL),
(172, 182, 'Australia', 'Student', 'in_process', '2026-05-04 11:51:47', NULL),
(173, 181, 'USA', 'Student', 'in_process', '2026-05-04 11:51:47', NULL),
(174, 180, 'USA', 'Student', 'in_process', '2026-05-04 11:51:47', NULL),
(175, 179, 'Australia', 'Work', 'rejected', '2026-05-04 11:51:47', NULL),
(176, 178, 'USA', 'Student', 'in_process', '2026-05-04 11:51:47', NULL),
(177, 177, 'Korea Selatan', 'Work', 'rejected', '2026-05-04 11:51:47', NULL),
(178, 176, 'Arab Saudi', 'Student', 'in_process', '2026-05-04 11:51:47', NULL),
(179, 175, 'Australia', 'Tourist', 'completed', '2026-05-04 11:51:47', NULL),
(180, 174, 'Arab Saudi', 'Tourist', 'completed', '2026-05-04 11:51:47', NULL),
(181, 173, 'Arab Saudi', 'Tourist', 'rejected', '2026-05-04 11:51:47', NULL),
(182, 172, 'Korea Selatan', 'Student', 'rejected', '2026-05-04 11:51:47', NULL),
(183, 171, 'USA', 'Tourist', 'rejected', '2026-05-04 11:51:47', NULL),
(184, 170, 'Australia', 'Work', 'in_process', '2026-05-04 11:51:47', NULL),
(185, 169, 'Korea Selatan', 'Work', 'completed', '2026-05-04 11:51:47', NULL),
(186, 168, 'Arab Saudi', 'Student', 'completed', '2026-05-04 11:51:47', NULL),
(187, 167, 'USA', 'Student', 'in_process', '2026-05-04 11:51:47', NULL),
(188, 166, 'Korea Selatan', 'Work', 'completed', '2026-05-04 11:51:47', NULL),
(189, 165, 'Australia', 'Work', 'in_process', '2026-05-04 11:51:47', NULL),
(190, 164, 'Korea Selatan', 'Tourist', 'in_process', '2026-05-04 11:51:47', NULL),
(191, 163, 'USA', 'Tourist', 'completed', '2026-05-04 11:51:47', NULL),
(192, 162, 'USA', 'Work', 'completed', '2026-05-04 11:51:47', NULL),
(193, 161, 'Korea Selatan', 'Work', 'completed', '2026-05-04 11:51:47', NULL),
(194, 160, 'Jepang', 'Tourist', 'completed', '2026-05-04 11:51:47', NULL),
(195, 159, 'Korea Selatan', 'Tourist', 'rejected', '2026-05-04 11:51:47', NULL),
(196, 158, 'Korea Selatan', 'Tourist', 'completed', '2026-05-04 11:51:47', NULL),
(197, 157, 'Australia', 'Student', 'completed', '2026-05-04 11:51:47', NULL),
(198, 156, 'Jepang', 'Work', 'completed', '2026-05-04 11:51:47', NULL),
(199, 155, 'Arab Saudi', 'Tourist', 'rejected', '2026-05-04 11:51:47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `application_id` int(11) DEFAULT NULL,
  `doc_type` varchar(50) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `application_id`, `doc_type`, `file_path`, `original_name`, `created_at`, `updated_at`) VALUES
(17, 179, NULL, 'uploads/documents/doc_179_1777895616_69f888c0bf819.png', NULL, '2026-05-04 11:53:36', NULL),
(18, 179, NULL, 'uploads/documents/doc_179_1777895616_69f888c0c1983.png', NULL, '2026-05-04 11:53:36', NULL),
(19, 179, NULL, 'uploads/documents/doc_179_1777895616_69f888c0c2e24.png', NULL, '2026-05-04 11:53:36', NULL),
(20, 174, NULL, 'uploads/documents/doc_174_1777959236_69f981440e297.png', NULL, '2026-05-05 05:33:56', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `application_id` int(11) DEFAULT NULL,
  `payment_type` enum('dp','full') DEFAULT 'dp',
  `amount_total` decimal(10,2) DEFAULT NULL,
  `dp_amount` decimal(10,2) DEFAULT NULL,
  `amount_paid` decimal(10,2) DEFAULT NULL,
  `status` enum('unpaid','partial','paid') DEFAULT 'unpaid',
  `proof` varchar(255) DEFAULT NULL,
  `paid_at` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `application_id`, `payment_type`, `amount_total`, `dp_amount`, `amount_paid`, `status`, `proof`, `paid_at`, `created_at`, `updated_at`) VALUES
(16, 199, 'dp', 17721523.00, 687185.00, 31493061.00, 'paid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(17, 198, 'dp', 36936920.00, 2198739.00, 6386046.00, 'partial', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(18, 197, 'full', 10628757.00, 654951.00, 1106433.00, 'paid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(19, 196, 'dp', 6746914.00, 2260041.00, 14682148.00, 'paid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(20, 195, 'dp', 43912011.00, 908111.00, 21807177.00, 'partial', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(21, 194, 'dp', 5718834.00, 773787.00, 32327353.00, 'paid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(22, 193, 'full', 24663978.00, 1166006.00, 18204242.00, 'paid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(23, 192, 'full', 34692815.00, 2329503.00, 30211938.00, 'partial', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(24, 191, 'dp', 8557518.00, 1178715.00, 8557518.00, 'paid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(25, 190, 'dp', 16986650.00, 1035944.00, 27537563.00, 'paid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(26, 189, 'full', 35834108.00, 2090358.00, 46514493.00, 'unpaid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(27, 188, 'dp', 47556318.00, 2044844.00, 1751200.00, 'paid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(28, 187, 'full', 22883192.00, 688887.00, 14500272.00, 'unpaid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(29, 186, 'full', 21525668.00, 1711579.00, 46861901.00, 'paid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(30, 185, 'dp', 9746803.00, 1680509.00, 32241006.00, 'partial', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(31, 184, 'dp', 20799545.00, 1337677.00, 2544549.00, 'paid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(32, 183, 'full', 15857815.00, 1210727.00, 1210726.00, 'partial', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(33, 182, 'full', 30990226.00, 1267112.00, 9754955.00, 'paid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(34, 181, 'dp', 17136927.00, 1284066.00, 8051840.00, 'partial', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(35, 180, 'dp', 26344224.00, 589276.00, 40512106.00, 'paid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(36, 179, 'full', 8460032.00, 1654361.00, 8460032.00, 'paid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(37, 178, 'full', 11902434.00, 1489133.00, 1133548.00, 'partial', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(38, 177, 'full', 30305448.00, 961863.00, 23881440.00, 'partial', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(39, 176, 'full', 13245985.00, 1389292.00, 34174912.00, 'unpaid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(40, 175, 'dp', 33606122.00, 1973295.00, 39308206.00, 'paid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(41, 174, 'dp', 10257753.00, 1513024.00, 9602254.00, 'partial', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(42, 173, 'full', 43900995.00, 700828.00, 43900995.00, 'paid', NULL, NULL, '2026-05-04 11:52:00', NULL),
(43, 172, 'dp', 36336591.00, 556447.00, 3100673.00, 'unpaid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(44, 171, 'full', 8532607.00, 959396.00, 46149234.00, 'paid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(45, 170, 'full', 30805449.00, 2393416.00, 1158942.00, 'unpaid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(46, 169, 'dp', 39615640.00, 1656512.00, 29678574.00, 'unpaid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(47, 168, 'dp', 17347354.00, 2255473.00, 28776347.00, 'unpaid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(48, 167, 'dp', 40749495.00, 791084.00, 17720599.00, 'unpaid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(49, 166, 'dp', 10100564.00, 1269885.00, 29733748.00, 'paid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(50, 165, 'dp', 11206249.00, 1346896.00, 35674553.00, 'unpaid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(51, 164, 'dp', 44783194.00, 590226.00, 29167604.00, 'paid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(52, 163, 'full', 27094607.00, 1787606.00, 37802062.00, 'paid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(53, 162, 'full', 22231441.00, 1794034.00, 4816122.00, 'partial', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(54, 161, 'dp', 27387933.00, 2312064.00, 2381618.00, 'partial', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(55, 160, 'dp', 39962672.00, 844910.00, 27071542.00, 'unpaid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(56, 159, 'dp', 9675228.00, 911026.00, 36294259.00, 'paid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(57, 158, 'full', 25034363.00, 951442.00, 40149127.00, 'unpaid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(58, 157, 'dp', 24608028.00, 2498563.00, 34960351.00, 'partial', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(59, 156, 'dp', 28493192.00, 680740.00, 44781982.00, 'unpaid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(60, 155, 'dp', 12709198.00, 1442064.00, 42560697.00, 'paid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(61, 154, 'dp', 42224348.00, 2124723.00, 29509289.00, 'partial', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(62, 153, 'full', 27579763.00, 2045761.00, 18454300.00, 'partial', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(63, 152, 'dp', 9636982.00, 1690351.00, 33837309.00, 'partial', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL),
(64, 151, 'full', 7668302.00, 648267.00, 10139043.00, 'paid', NULL, '2026-05-04', '2026-05-04 11:52:00', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `applicants`
--
ALTER TABLE `applicants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_applications_applicant` (`applicant_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_documents_application` (`application_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_payments_application` (`application_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `applicants`
--
ALTER TABLE `applicants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=204;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=201;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `fk_applications_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `fk_documents_application` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_application` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
