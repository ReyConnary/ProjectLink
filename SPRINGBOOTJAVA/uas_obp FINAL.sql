-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 26, 2024 at 06:55 AM
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
-- Database: `uas_obp`
--

-- --------------------------------------------------------

--
-- Table structure for table `interview`
--

CREATE TABLE `interview` (
  `no_interview` int(4) NOT NULL,
  `IDPelamar` int(4) NOT NULL,
  `IDPekerja` int(4) NOT NULL,
  `catatan_interview` varchar(500) DEFAULT NULL,
  `hasil_interview` varchar(255) DEFAULT NULL,
  `tanggal_interview` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pekerja`
--

CREATE TABLE `pekerja` (
  `IDPekerja` int(4) NOT NULL,
  `Posisi` varchar(255) DEFAULT NULL,
  `Nama` varchar(255) DEFAULT NULL,
  `Alamat` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `no_telp` int(11) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pelamar`
--

CREATE TABLE `pelamar` (
  `IDPelamar` int(4) NOT NULL,
  `posisi_lamar` varchar(255) DEFAULT NULL,
  `Nama` varchar(255) DEFAULT NULL,
  `Alamat` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `no_telp` int(11) DEFAULT NULL,
  `pengalaman_kerja` varchar(255) DEFAULT NULL,
  `Keputusan` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `seleksi`
--

CREATE TABLE `seleksi` (
  `kode_seleksi` int(4) NOT NULL,
  `keputusan` varchar(255) DEFAULT NULL,
  `tanggal_keputusan` varchar(255) DEFAULT NULL,
  `IDPelamar` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `undanganinterview`
--

CREATE TABLE `undanganinterview` (
  `kode_interview` int(4) NOT NULL,
  `IDPekerja` int(4) NOT NULL,
  `lokasi_interview` varchar(255) NOT NULL,
  `tanggal_interview` varchar(255) NOT NULL,
  `waktu_interview` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `interview`
--
ALTER TABLE `interview`
  ADD PRIMARY KEY (`no_interview`),
  ADD KEY `IDPekerja` (`IDPekerja`),
  ADD KEY `IDPelamar` (`IDPelamar`);

--
-- Indexes for table `pekerja`
--
ALTER TABLE `pekerja`
  ADD PRIMARY KEY (`IDPekerja`);

--
-- Indexes for table `pelamar`
--
ALTER TABLE `pelamar`
  ADD PRIMARY KEY (`IDPelamar`);

--
-- Indexes for table `seleksi`
--
ALTER TABLE `seleksi`
  ADD PRIMARY KEY (`kode_seleksi`),
  ADD KEY `IDPelamar` (`IDPelamar`);

--
-- Indexes for table `undanganinterview`
--
ALTER TABLE `undanganinterview`
  ADD PRIMARY KEY (`kode_interview`),
  ADD KEY `IDPekerja` (`IDPekerja`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `interview`
--
ALTER TABLE `interview`
  MODIFY `no_interview` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pekerja`
--
ALTER TABLE `pekerja`
  MODIFY `IDPekerja` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `pelamar`
--
ALTER TABLE `pelamar`
  MODIFY `IDPelamar` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `seleksi`
--
ALTER TABLE `seleksi`
  MODIFY `kode_seleksi` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `undanganinterview`
--
ALTER TABLE `undanganinterview`
  MODIFY `kode_interview` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `interview`
--
ALTER TABLE `interview`
  ADD CONSTRAINT `FKblog1fv4vo6ybsth6q8vmk2i9` FOREIGN KEY (`IDPelamar`) REFERENCES `pelamar` (`IDPelamar`),
  ADD CONSTRAINT `FKf89sr54ddac7b8f2rgl96t8yd` FOREIGN KEY (`IDPekerja`) REFERENCES `pekerja` (`IDPekerja`);

--
-- Constraints for table `seleksi`
--
ALTER TABLE `seleksi`
  ADD CONSTRAINT `seleksi_ibfk_1` FOREIGN KEY (`IDPelamar`) REFERENCES `pelamar` (`IDPelamar`);

--
-- Constraints for table `undanganinterview`
--
ALTER TABLE `undanganinterview`
  ADD CONSTRAINT `FK1ui7k86bvogiesbg8lork6ykq` FOREIGN KEY (`IDPekerja`) REFERENCES `pekerja` (`IDPekerja`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
