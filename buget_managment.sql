-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 11, 2024 at 07:42 PM
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
(2, 1, 'b_manager', 'kaleb', 'adem', 2147483647, '$2y$10$xMKokubrvCvmEK5K4zqrlesobfNkDWi8Ee2O17ZsEJ4G36epfM3rK', 'qqqqqqqqqqqqqq1'),
(3, 2, 'finance', 'kaleb', 'adem', 2147483647, '$2y$10$OSfTUeAxJcQv/WcwuCE4weqs5cdUR2Ugnk0uq4w6pC3spqy2i9oYG', 'qqqqqqqqqqqqqq'),
(4, 3, 'b_manager', 'kaleb', 'adem', 2147483647, '$2y$10$mHnh8L6BzMk9HQZlOAK/gOQSjQjhXBG9.jOXiMkOhzHGBlLEXpKHK', 'qqqqqqqqqqqqqq'),
(5, 4, 'b_manager', 'kaleb', 'adem', 2147483647, '$2y$10$tiEyQRcwud7J7SC4MRMeJeXb1tN7y6/fEm0sOVY.oFOsZJy.hODn.', 'qqqqqqqqqqqqqq'),
(6, 5, 'b_manager', 'kaleb', 'adem', 2147483647, '$2y$10$JIrbdwZJPG.FetaxidFnGOUsC5sgk7t8B0MQNTxwXRzPv80YLay3a', 'wwwwwwwwwwwwwwwwww'),
(8, 6, 'director', 'kaleb', 'adem', 2147483647, '$2y$10$F9KSAc7Tvn51kp9V393KX.inXOS5pVIqkx17b7.Ta2Q2NDcA24kz2', 'wwwwwwwwwwwwwwwwww'),
(9, 7, 'director', 'kaleb', 'adem', 2147483647, '$2y$10$pZSbFHPK9qnQL41fNMvajem1JD4WXHz97lP7zomKm9XCwabf9T5ha', 'ademkisho'),
(10, 8, 'g_manager', 'kaleb', 'adem', 2147483647, '$2y$10$AflxkvVEbrT05GIbZcD3ced/aKMjyShNBtQGZIMBNzxCmOE0L7yvq', 'adem');

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

--
-- Dumping data for table `proposals`
--

INSERT INTO `proposals` (`id`, `employee_id`, `topic`, `description`, `dir_url`) VALUES
(1, 9, '', '', 'phpB840.tmp.docx'),
(2, 9, '', '', 'phpF856.tmp.docx'),
(3, 9, '', '', 'php7406.tmp.docx'),
(4, 9, '', '', 'php6A40.tmp.docx'),
(5, 9, '', '', 'phpC049.tmp.docx'),
(6, 9, '', '', 'php8254.tmp.docx'),
(7, 9, '', '', 'phpE7C1.tmp.docx'),
(9, 9, '', '', 'upload/propsal/AI.ASSIGNMENT.docx');

-- --------------------------------------------------------

--
-- Table structure for table `propsal`
--

CREATE TABLE `propsal` (
  `id` int(11) NOT NULL,
  `code` int(11) NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`data`)),
  `time` datetime NOT NULL,
  `status` tinyint(1) NOT NULL
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
