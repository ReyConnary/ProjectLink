-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2025 at 06:31 AM
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
-- Database: `goodbooks`
--

-- --------------------------------------------------------

--
-- Table structure for table `bank`
--

CREATE TABLE `bank` (
  `ID_Bank` varchar(30) NOT NULL,
  `NamaBank` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `bank`
--

INSERT INTO `bank` (`ID_Bank`, `NamaBank`) VALUES
('1', 'Mandiri'),
('2', 'BRI'),
('3', 'BankBahagia'),
('4', 'BankWay');

-- --------------------------------------------------------

--
-- Table structure for table `databuku`
--

CREATE TABLE `databuku` (
  `DataBuku` varchar(30) NOT NULL,
  `ID_Franchise` varchar(30) NOT NULL,
  `ID_Kategori` varchar(30) NOT NULL,
  `ID_Penerbit` varchar(30) NOT NULL,
  `ID_Penulis` varchar(30) NOT NULL,
  `Judul` varchar(1000) NOT NULL,
  `ISBN` varchar(30) NOT NULL,
  `TanggalPublikasi` date NOT NULL,
  `Bahasa` varchar(30) NOT NULL,
  `JumlahHalaman` varchar(10) NOT NULL,
  `Format` varchar(30) NOT NULL,
  `Ringkasan` text NOT NULL,
  `CoverImg` longblob NOT NULL,
  `Harga` mediumtext NOT NULL,
  `DriveLink` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `franchise`
--

CREATE TABLE `franchise` (
  `ID_Franchise` varchar(30) NOT NULL,
  `NamaFranchise` varchar(30) NOT NULL,
  `Deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `franchise`
--

INSERT INTO `franchise` (`ID_Franchise`, `NamaFranchise`, `Deskripsi`) VALUES
('2', 'Marvel', 'Superhero'),
('3', 'Star Wars', 'A long time ago in a galaxy far, far away...'),
('4', 'DC', '');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `ID_Kategori` varchar(30) NOT NULL,
  `NamaKategori` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`ID_Kategori`, `NamaKategori`) VALUES
('1', 'Sci fi'),
('2', 'Action'),
('3', 'Superhero');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `ID_Pelanggan` varchar(30) NOT NULL,
  `ID_Bank` varchar(30) NOT NULL,
  `NamaDepan` varchar(30) NOT NULL,
  `NamaBelakang` varchar(30) NOT NULL,
  `NoTelp` varchar(30) NOT NULL,
  `Email` varchar(30) NOT NULL,
  `Password` varchar(30) NOT NULL,
  `NoRek` int(20) DEFAULT NULL,
  `TglRegis` date NOT NULL,
  `Saldo` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`ID_Pelanggan`, `ID_Bank`, `NamaDepan`, `NamaBelakang`, `NoTelp`, `Email`, `Password`, `NoRek`, `TglRegis`, `Saldo`) VALUES
('1', '2', 'Bob', 'Grimus', '083876594222', 'Rey@gmail.com', '123', 12344, '2025-06-14', 99488000),
('2', '2', 'John', 'Walker', '083876594222', 'Reyro@gmail.com', '123', 2147483647, '2025-06-16', 20000),
('3', '2', 'Bob', 'Walker', '08388686868', 'Boba@gmail.com', '123', 2147483647, '2025-06-16', 920000),
('4', '3', 'Eddy ', 'Sin', '1234556', 'Eddy@gmail.com', '123', 88888888, '2025-06-16', 920000);

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `ID_Pembayaran` varchar(30) NOT NULL,
  `ID_Pelanggan` varchar(30) NOT NULL,
  `TotalHarga` bigint(100) DEFAULT NULL,
  `TanggalPembayaran` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `penerbit`
--

CREATE TABLE `penerbit` (
  `ID_Penerbit` varchar(30) NOT NULL,
  `NamaPenerbit` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `penerbit`
--

INSERT INTO `penerbit` (`ID_Penerbit`, `NamaPenerbit`) VALUES
('1', 'Amazon'),
('2', 'Marvel Comics'),
('4', 'John\'s Books'),
('5', 'James');

-- --------------------------------------------------------

--
-- Table structure for table `penulis`
--

CREATE TABLE `penulis` (
  `ID_Penulis` varchar(30) NOT NULL,
  `NamaPenulis` varchar(100) NOT NULL,
  `FotoPenulis` longblob DEFAULT NULL,
  `TahunMulaiAktif` varchar(30) NOT NULL,
  `TahunBerhenti` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `penulis`
--

INSERT INTO `penulis` (`ID_Penulis`, `NamaPenulis`, `FotoPenulis`, `TahunMulaiAktif`, `TahunBerhenti`) VALUES
('1', 'John Lennon', NULL, '2000', NULL),
('2', 'Wirya', NULL, '2002', NULL),
('3', 'WRY', NULL, '2002', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `ID_Pesanan` varchar(30) NOT NULL,
  `ID_Pelanggan` varchar(30) NOT NULL,
  `DataBuku` varchar(30) NOT NULL,
  `ID_Pembayaran` varchar(30) DEFAULT NULL,
  `HargaSatuan` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bank`
--
ALTER TABLE `bank`
  ADD PRIMARY KEY (`ID_Bank`);

--
-- Indexes for table `databuku`
--
ALTER TABLE `databuku`
  ADD PRIMARY KEY (`DataBuku`),
  ADD KEY `NamaFranchise` (`ID_Franchise`),
  ADD KEY `NamaKategori` (`ID_Kategori`),
  ADD KEY `NamaPenerbit` (`ID_Penerbit`),
  ADD KEY `NamaPenulis` (`ID_Penulis`);

--
-- Indexes for table `franchise`
--
ALTER TABLE `franchise`
  ADD PRIMARY KEY (`ID_Franchise`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`ID_Kategori`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`ID_Pelanggan`),
  ADD KEY `ID_Bank` (`ID_Bank`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`ID_Pembayaran`),
  ADD KEY `ID_Pelanggan` (`ID_Pelanggan`);

--
-- Indexes for table `penerbit`
--
ALTER TABLE `penerbit`
  ADD PRIMARY KEY (`ID_Penerbit`);

--
-- Indexes for table `penulis`
--
ALTER TABLE `penulis`
  ADD PRIMARY KEY (`ID_Penulis`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`ID_Pesanan`),
  ADD KEY `ID_Pelanggan` (`ID_Pelanggan`),
  ADD KEY `ID_Pembayaran` (`ID_Pembayaran`),
  ADD KEY `DataBuku` (`DataBuku`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `databuku`
--
ALTER TABLE `databuku`
  ADD CONSTRAINT `databuku_ibfk_1` FOREIGN KEY (`ID_Franchise`) REFERENCES `franchise` (`ID_Franchise`),
  ADD CONSTRAINT `databuku_ibfk_2` FOREIGN KEY (`ID_Kategori`) REFERENCES `kategori` (`ID_Kategori`),
  ADD CONSTRAINT `databuku_ibfk_3` FOREIGN KEY (`ID_Penerbit`) REFERENCES `penerbit` (`ID_Penerbit`),
  ADD CONSTRAINT `databuku_ibfk_4` FOREIGN KEY (`ID_Penulis`) REFERENCES `penulis` (`ID_Penulis`);

--
-- Constraints for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD CONSTRAINT `pelanggan_ibfk_1` FOREIGN KEY (`ID_Bank`) REFERENCES `bank` (`ID_Bank`);

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`ID_Pelanggan`) REFERENCES `pelanggan` (`ID_Pelanggan`);

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`ID_Pelanggan`) REFERENCES `pelanggan` (`ID_Pelanggan`),
  ADD CONSTRAINT `pesanan_ibfk_3` FOREIGN KEY (`ID_Pembayaran`) REFERENCES `pembayaran` (`ID_Pembayaran`),
  ADD CONSTRAINT `pesanan_ibfk_4` FOREIGN KEY (`DataBuku`) REFERENCES `databuku` (`DataBuku`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
