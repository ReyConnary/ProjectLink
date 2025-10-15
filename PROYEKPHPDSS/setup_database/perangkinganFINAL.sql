-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 06, 2025 at 07:09 PM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `perangkingan`
--

-- --------------------------------------------------------

--
-- Table structure for table `ahp_bobot_kriteria`
--

CREATE TABLE `ahp_bobot_kriteria` (
  `id` int(11) NOT NULL,
  `id_judul` varchar(100) NOT NULL,
  `id_kriteria` varchar(100) NOT NULL,
  `bobot` decimal(10,6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `ahp_bobot_sub`
--

CREATE TABLE `ahp_bobot_sub` (
  `id` int(11) NOT NULL,
  `id_judul` varchar(80) NOT NULL,
  `id_kriteria` varchar(60) NOT NULL,
  `id_sub` varchar(60) NOT NULL,
  `bobot` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `ahp_pairwise_comparison`
--

CREATE TABLE `ahp_pairwise_comparison` (
  `id` int(11) NOT NULL,
  `id_judul` varchar(100) NOT NULL,
  `id_kriteria1` varchar(100) NOT NULL,
  `id_kriteria2` varchar(100) NOT NULL,
  `nilai` decimal(10,6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `ahp_pairwise_sub`
--

CREATE TABLE `ahp_pairwise_sub` (
  `id` int(11) NOT NULL,
  `id_judul` varchar(80) NOT NULL,
  `id_kriteria` varchar(60) NOT NULL,
  `id_sub1` varchar(60) NOT NULL,
  `id_sub2` varchar(60) NOT NULL,
  `nilai` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ahp_pairwise_sub`
--

INSERT INTO `ahp_pairwise_sub` (`id`, `id_judul`, `id_kriteria`, `id_sub1`, `id_sub2`, `nilai`) VALUES
(77, 'JDL-20251006185753-68e3f511b6dee', 'KRT-68e3f52ff1d0c', 'SUB-68e3f570f3de1', 'SUB-68e3f57118f51', 2),
(78, 'JDL-20251006185753-68e3f511b6dee', 'KRT-68e3f5300f2cc', 'SUB-68e3f57128eb7', 'SUB-68e3f57138fcc', 3);

-- --------------------------------------------------------

--
-- Table structure for table `alternatif`
--

CREATE TABLE `alternatif` (
  `ID_Alternatif` varchar(100) NOT NULL,
  `ID_Judul` varchar(100) DEFAULT NULL,
  `nama_alternatif` varchar(255) NOT NULL,
  `nilai_akhir` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `judul`
--

CREATE TABLE `judul` (
  `ID_Judul` varchar(100) NOT NULL,
  `NamaJudul` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `kriteria`
--

CREATE TABLE `kriteria` (
  `ID_Kriteria` varchar(100) NOT NULL,
  `ID_Judul` varchar(100) DEFAULT NULL,
  `nama_kriteria` varchar(255) NOT NULL,
  `bobot` decimal(10,4) NOT NULL,
  `status` enum('B','C') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `nilai_alternatif`
--

CREATE TABLE `nilai_alternatif` (
  `ID_Nilai` varchar(100) NOT NULL,
  `ID_Judul` varchar(100) DEFAULT NULL,
  `ID_Alternatif` varchar(100) DEFAULT NULL,
  `ID_Kriteria` varchar(100) DEFAULT NULL,
  `nilai` decimal(10,4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `nilai_subkriteria`
--

CREATE TABLE `nilai_subkriteria` (
  `id` int(11) NOT NULL,
  `id_judul` varchar(80) NOT NULL,
  `id_alternatif` varchar(60) NOT NULL,
  `id_sub` varchar(60) NOT NULL,
  `nilai` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `nilai_subkriteria`
--

INSERT INTO `nilai_subkriteria` (`id`, `id_judul`, `id_alternatif`, `id_sub`, `nilai`) VALUES
(113, 'JDL-20251006185753-68e3f511b6dee', 'ALT-68e3f530152d9', 'SUB-68e3f570f3de1', 0),
(114, 'JDL-20251006185753-68e3f511b6dee', 'ALT-68e3f530152d9', 'SUB-68e3f57128eb7', 0),
(115, 'JDL-20251006185753-68e3f511b6dee', 'ALT-68e3f53033624', 'SUB-68e3f57118f51', 0),
(116, 'JDL-20251006185753-68e3f511b6dee', 'ALT-68e3f53033624', 'SUB-68e3f57128eb7', 0);

-- --------------------------------------------------------

--
-- Table structure for table `subkriteria`
--

CREATE TABLE `subkriteria` (
  `ID_Sub` varchar(60) NOT NULL,
  `ID_Judul` varchar(80) NOT NULL,
  `ID_Kriteria` varchar(60) NOT NULL,
  `nama_sub` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `subkriteria`
--

INSERT INTO `subkriteria` (`ID_Sub`, `ID_Judul`, `ID_Kriteria`, `nama_sub`) VALUES
('SUB-68e3f570f3de1', 'JDL-20251006185753-68e3f511b6dee', 'KRT-68e3f52ff1d0c', 'SBK1'),
('SUB-68e3f57118f51', 'JDL-20251006185753-68e3f511b6dee', 'KRT-68e3f52ff1d0c', 'SBK2'),
('SUB-68e3f57128eb7', 'JDL-20251006185753-68e3f511b6dee', 'KRT-68e3f5300f2cc', 'SKB1'),
('SUB-68e3f57138fcc', 'JDL-20251006185753-68e3f511b6dee', 'KRT-68e3f5300f2cc', 'SKB2');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ahp_bobot_kriteria`
--
ALTER TABLE `ahp_bobot_kriteria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq` (`id_judul`,`id_kriteria`);

--
-- Indexes for table `ahp_bobot_sub`
--
ALTER TABLE `ahp_bobot_sub`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_bs` (`id_judul`,`id_kriteria`,`id_sub`);

--
-- Indexes for table `ahp_pairwise_comparison`
--
ALTER TABLE `ahp_pairwise_comparison`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq` (`id_judul`,`id_kriteria1`,`id_kriteria2`),
  ADD KEY `fk_kriteria1` (`id_kriteria1`),
  ADD KEY `fk_kriteria2` (`id_kriteria2`);

--
-- Indexes for table `ahp_pairwise_sub`
--
ALTER TABLE `ahp_pairwise_sub`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_cmp` (`id_judul`,`id_kriteria`,`id_sub1`,`id_sub2`);

--
-- Indexes for table `alternatif`
--
ALTER TABLE `alternatif`
  ADD PRIMARY KEY (`ID_Alternatif`),
  ADD KEY `ID_Judul` (`ID_Judul`);

--
-- Indexes for table `judul`
--
ALTER TABLE `judul`
  ADD PRIMARY KEY (`ID_Judul`);

--
-- Indexes for table `kriteria`
--
ALTER TABLE `kriteria`
  ADD PRIMARY KEY (`ID_Kriteria`),
  ADD KEY `ID_Judul` (`ID_Judul`);

--
-- Indexes for table `nilai_alternatif`
--
ALTER TABLE `nilai_alternatif`
  ADD PRIMARY KEY (`ID_Nilai`),
  ADD KEY `ID_Judul` (`ID_Judul`),
  ADD KEY `ID_Alternatif` (`ID_Alternatif`),
  ADD KEY `ID_Kriteria` (`ID_Kriteria`);

--
-- Indexes for table `nilai_subkriteria`
--
ALTER TABLE `nilai_subkriteria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_nilai` (`id_judul`,`id_alternatif`,`id_sub`);

--
-- Indexes for table `subkriteria`
--
ALTER TABLE `subkriteria`
  ADD PRIMARY KEY (`ID_Sub`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ahp_bobot_kriteria`
--
ALTER TABLE `ahp_bobot_kriteria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `ahp_bobot_sub`
--
ALTER TABLE `ahp_bobot_sub`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `ahp_pairwise_comparison`
--
ALTER TABLE `ahp_pairwise_comparison`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `ahp_pairwise_sub`
--
ALTER TABLE `ahp_pairwise_sub`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `nilai_subkriteria`
--
ALTER TABLE `nilai_subkriteria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ahp_pairwise_comparison`
--
ALTER TABLE `ahp_pairwise_comparison`
  ADD CONSTRAINT `fk_judul_comp` FOREIGN KEY (`id_judul`) REFERENCES `judul` (`ID_Judul`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kriteria1` FOREIGN KEY (`id_kriteria1`) REFERENCES `kriteria` (`ID_Kriteria`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kriteria2` FOREIGN KEY (`id_kriteria2`) REFERENCES `kriteria` (`ID_Kriteria`) ON DELETE CASCADE;

--
-- Constraints for table `alternatif`
--
ALTER TABLE `alternatif`
  ADD CONSTRAINT `alternatif_ibfk_1` FOREIGN KEY (`ID_Judul`) REFERENCES `judul` (`ID_Judul`) ON DELETE CASCADE;

--
-- Constraints for table `kriteria`
--
ALTER TABLE `kriteria`
  ADD CONSTRAINT `kriteria_ibfk_1` FOREIGN KEY (`ID_Judul`) REFERENCES `judul` (`ID_Judul`) ON DELETE CASCADE;

--
-- Constraints for table `nilai_alternatif`
--
ALTER TABLE `nilai_alternatif`
  ADD CONSTRAINT `nilai_alternatif_ibfk_1` FOREIGN KEY (`ID_Judul`) REFERENCES `judul` (`ID_Judul`) ON DELETE CASCADE,
  ADD CONSTRAINT `nilai_alternatif_ibfk_2` FOREIGN KEY (`ID_Alternatif`) REFERENCES `alternatif` (`ID_Alternatif`) ON DELETE CASCADE,
  ADD CONSTRAINT `nilai_alternatif_ibfk_3` FOREIGN KEY (`ID_Kriteria`) REFERENCES `kriteria` (`ID_Kriteria`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
