-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2025 at 01:40 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.3.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `qosku`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `idAdmin` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `namaAdmin` int(11) NOT NULL,
  `peran` varchar(255) NOT NULL,
  `idLaporan` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `biaya_tambahan`
--

CREATE TABLE `biaya_tambahan` (
  `idBiayaTambahan` int(11) NOT NULL,
  `jenisBiaya` varchar(255) NOT NULL,
  `jumlahBiaya` int(11) NOT NULL,
  `Periode` varchar(255) NOT NULL,
  `statusPembayaran` varchar(255) NOT NULL,
  `idPelanggan` int(11) NOT NULL,
  `idPembayaran` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fasilitas`
--

CREATE TABLE `fasilitas` (
  `idFasilitas` int(11) NOT NULL,
  `luasKamar` varchar(255) NOT NULL,
  `perabotan` varchar(255) NOT NULL,
  `kamarMandi` varchar(255) NOT NULL,
  `idKamar` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kamar_kos`
--

CREATE TABLE `kamar_kos` (
  `idKamar` int(11) NOT NULL,
  `nomorKamar` int(11) NOT NULL,
  `tipeKamar` varchar(255) NOT NULL,
  `harga` int(11) NOT NULL,
  `statusKetersediaan` varchar(255) NOT NULL,
  `deskripsi` varchar(255) NOT NULL,
  `gambar` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

CREATE TABLE `laporan` (
  `idLaporan` int(11) NOT NULL,
  `periodeLaporan` varchar(255) NOT NULL,
  `totalProfit` int(11) NOT NULL,
  `totalOmset` int(11) NOT NULL,
  `totalPengeluaran` int(11) NOT NULL,
  `idPembayaran` int(11) NOT NULL,
  `idPengeluaran` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `idPelanggan` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `namaLengkap` varchar(255) NOT NULL,
  `nomorHp` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `idPemesanan` int(11) NOT NULL,
  `idPembayaran` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `idPembayaran` int(11) NOT NULL,
  `tanggalPembayaran` date NOT NULL,
  `metodePembayaran` varchar(255) NOT NULL,
  `jumlahPembayaran` int(11) NOT NULL,
  `statusPembayaran` varchar(255) NOT NULL,
  `idPelanggan` int(11) NOT NULL,
  `idPemesanan` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pemesanan`
--

CREATE TABLE `pemesanan` (
  `idPemesanan` int(11) NOT NULL,
  `tanggalPemesanan` date NOT NULL,
  `lamaSewa` time NOT NULL,
  `totalHarga` int(11) NOT NULL,
  `statusPemesanan` varchar(255) NOT NULL,
  `idPelanggan` int(11) NOT NULL,
  `idKamar` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengeluaran`
--

CREATE TABLE `pengeluaran` (
  `idPengeluaran` int(11) NOT NULL,
  `listrik` int(11) NOT NULL,
  `air` int(11) NOT NULL,
  `idLaporan` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`idAdmin`),
  ADD KEY `idLaporan` (`idLaporan`);

--
-- Indexes for table `biaya_tambahan`
--
ALTER TABLE `biaya_tambahan`
  ADD PRIMARY KEY (`idBiayaTambahan`),
  ADD KEY `idPelanggan` (`idPelanggan`),
  ADD KEY `idPembayaran` (`idPembayaran`);

--
-- Indexes for table `fasilitas`
--
ALTER TABLE `fasilitas`
  ADD PRIMARY KEY (`idFasilitas`),
  ADD KEY `idKamar` (`idKamar`);

--
-- Indexes for table `kamar_kos`
--
ALTER TABLE `kamar_kos`
  ADD PRIMARY KEY (`idKamar`);

--
-- Indexes for table `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`idLaporan`),
  ADD KEY `idPembayaran` (`idPembayaran`),
  ADD KEY `idPengeluaran` (`idPengeluaran`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`idPelanggan`),
  ADD KEY `idPemesanan` (`idPemesanan`),
  ADD KEY `idPembayaran` (`idPembayaran`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`idPembayaran`),
  ADD KEY `idPelanggan` (`idPelanggan`),
  ADD KEY `idPemesanan` (`idPemesanan`);

--
-- Indexes for table `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD PRIMARY KEY (`idPemesanan`),
  ADD KEY `idPelanggan` (`idPelanggan`),
  ADD KEY `idKamar` (`idKamar`);

--
-- Indexes for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  ADD PRIMARY KEY (`idPengeluaran`),
  ADD KEY `idLaporan` (`idLaporan`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`idLaporan`) REFERENCES `laporan` (`idLaporan`);

--
-- Constraints for table `biaya_tambahan`
--
ALTER TABLE `biaya_tambahan`
  ADD CONSTRAINT `biaya_tambahan_ibfk_1` FOREIGN KEY (`idPelanggan`) REFERENCES `pelanggan` (`idPelanggan`),
  ADD CONSTRAINT `biaya_tambahan_ibfk_2` FOREIGN KEY (`idPembayaran`) REFERENCES `pembayaran` (`idPembayaran`);

--
-- Constraints for table `fasilitas`
--
ALTER TABLE `fasilitas`
  ADD CONSTRAINT `fasilitas_ibfk_1` FOREIGN KEY (`idKamar`) REFERENCES `kamar_kos` (`idKamar`);

--
-- Constraints for table `laporan`
--
ALTER TABLE `laporan`
  ADD CONSTRAINT `laporan_ibfk_1` FOREIGN KEY (`idPembayaran`) REFERENCES `pembayaran` (`idPembayaran`),
  ADD CONSTRAINT `laporan_ibfk_2` FOREIGN KEY (`idPengeluaran`) REFERENCES `pengeluaran` (`idPengeluaran`);

--
-- Constraints for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD CONSTRAINT `pelanggan_ibfk_1` FOREIGN KEY (`idPemesanan`) REFERENCES `pemesanan` (`idPemesanan`),
  ADD CONSTRAINT `pelanggan_ibfk_2` FOREIGN KEY (`idPembayaran`) REFERENCES `pembayaran` (`idPembayaran`);

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`idPemesanan`) REFERENCES `pemesanan` (`idPemesanan`),
  ADD CONSTRAINT `pembayaran_ibfk_2` FOREIGN KEY (`idPelanggan`) REFERENCES `pelanggan` (`idPelanggan`);

--
-- Constraints for table `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD CONSTRAINT `pemesanan_ibfk_1` FOREIGN KEY (`idPelanggan`) REFERENCES `pelanggan` (`idPelanggan`),
  ADD CONSTRAINT `pemesanan_ibfk_2` FOREIGN KEY (`idKamar`) REFERENCES `kamar_kos` (`idKamar`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
