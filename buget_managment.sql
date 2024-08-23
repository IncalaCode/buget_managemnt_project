-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 23, 2024 at 08:39 PM
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
-- Database: `buget_managment`
--

-- --------------------------------------------------------

--
-- Table structure for table `employ`
--

CREATE TABLE `employ` (
  `id` int(11) NOT NULL,
  `code` int(11) NOT NULL,
  `role` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `phonenum` int(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employ`
--

INSERT INTO `employ` (`id`, `code`, `role`, `fname`, `lname`, `phonenum`, `password`, `username`) VALUES
(16, 1, 'g_manager', 'general', 'manager', 923456789, '$2y$10$kOkNgucVR5qMR7jaYlnqDO2edYsvqPHrkXb4KBzOyhTR7kpet5.UK', 'general'),
(17, 2, 'b_manager', 'buget', 'manager', 923456789, '$2y$10$XJugOvYKz83ZwtJwP0KDZ.KTBOG8b4eS9UuBx4tcaCaNXll7biCiG', 'buget123'),
(18, 4, 'director', 'director', 'manager', 923456789, '$2y$10$A7OgUO.hMn8nSMyP97ipj.m/cxNgjfvvYkJ920PRWQ.B2AsuyrLyi', 'dir'),
(19, 3, 'finance', 'finance', 'manager', 923456789, '$2y$10$B8DcVvjSVtIsXaStmpxf5e8Pc1KLJw7X8iNRlkkE0YjpM3V/sl2Fq', 'fin');

-- --------------------------------------------------------

--
-- Table structure for table `finance_review`
--

CREATE TABLE `finance_review` (
  `id_code` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `review_status` enum('Pending','Approved','Rejected') NOT NULL,
  `review_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `finance_review`
--

INSERT INTO `finance_review` (`id_code`, `code`, `amount`, `review_status`, `review_time`) VALUES
('4', '6313', 1000.00, 'Approved', '2024-08-23 20:43:43'),
('4', '6111', 1000.00, '', '2024-08-23 20:49:13');

-- --------------------------------------------------------

--
-- Table structure for table `proposals`
--

CREATE TABLE `proposals` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `topic` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `dir_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `propsal`
--

CREATE TABLE `propsal` (
  `id` int(11) NOT NULL,
  `code` int(11) NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`data`)),
  `time` datetime NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `propsal`
--

INSERT INTO `propsal` (`id`, `code`, `data`, `time`, `status`) VALUES
(18, 4, '{\"head\":[\"Row-Number\",\"Item-name\",\"Item-code\",\"buget\",\"action\"],\"body\":[[\"1\",\"Item-name\",\"6111\",\"6912299\"],[\"2\",\"Item-name\",\"6212\",\"6912\"],[\"3\",\"Item-name\",\"6313\",\"9912\"]],\"footer\":null}', '2024-08-23 19:35:19', 1);

-- --------------------------------------------------------

--
-- Table structure for table `records`
--

CREATE TABLE `records` (
  `id` int(11) NOT NULL,
  `code` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `time` varchar(20) NOT NULL,
  `buget_limit` int(50) NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `records`
--

INSERT INTO `records` (`id`, `code`, `status`, `time`, `buget_limit`, `data`) VALUES
(16, 1, 1, '2025-12-23', 1200000000, '{\"head\":[\"Row-Number\",\"Item-name\",\"Item-code\",\"buget\"],\"body\":[[\"1 : 1\",\"Item-name : Item-name\",\"6111 : 6111\",\"6912299 : 6912299\"],[\"2 : 2\",\"Item-name : Item-name\",\"6212 : 6212\",\"6912 : 6912\"],[\"3 : 3\",\"Item-name : Item-name\",\"6313 : 6313\",\"9912: 10912\"]],\"footer\":null}');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id_code` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `employ`
--
ALTER TABLE `employ`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `proposals`
--
ALTER TABLE `proposals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `employ`
--
ALTER TABLE `employ`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `proposals`
--
ALTER TABLE `proposals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `proposals`
--
ALTER TABLE `proposals`
  ADD CONSTRAINT `proposals_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employ` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
