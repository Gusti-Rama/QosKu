-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 22, 2025 at 10:48 AM
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
  `namaAdmin` varchar(255) NOT NULL,
  `peran` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`idAdmin`, `username`, `password`, `namaAdmin`, `peran`) VALUES
(1, 'admin', '$2y$10$kw4byBCIw2RqFXB57QTmgua97xhV30m7BHeZ47ue4eB.wPTGpjvy2', 'Aku Admin', 'admin'),
(2, 'owner', '$2y$10$hHHOlI4bJ1.NGKgtMewi0.PqJ1nPx111mkpdj4cgfiUMrm3ciPBaq', 'Aku Owner', 'owner');

-- --------------------------------------------------------

--
-- Table structure for table `biaya_tambahan`
--

CREATE TABLE `biaya_tambahan` (
  `idBiayaTambahan` int(11) NOT NULL,
  `namaBiaya` varchar(255) NOT NULL,
  `jumlahBiaya` int(11) NOT NULL,
  `Periode` varchar(255) NOT NULL,
  `statusPembayaran` varchar(255) NOT NULL,
  `idPelanggan` int(11) NOT NULL,
  `jenisBiaya` enum('Listrik','Air','Lainnya') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `biaya_tambahan`
--

INSERT INTO `biaya_tambahan` (`idBiayaTambahan`, `namaBiaya`, `jumlahBiaya`, `Periode`, `statusPembayaran`, `idPelanggan`, `jenisBiaya`) VALUES
(1, '', 100000, '2025-06', 'belum_lunas', 2, 'Listrik'),
(2, 'Rice cooker', 20000, '2025-06', 'belum_lunas', 2, 'Listrik'),
(3, 'Rice cooker', 20000, '2025-06', 'belum_lunas', 2, 'Listrik');

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

--
-- Dumping data for table `fasilitas`
--

INSERT INTO `fasilitas` (`idFasilitas`, `luasKamar`, `perabotan`, `kamarMandi`, `idKamar`) VALUES
(1, '3x3', 'Kursi, Meja, Lemari, Kasur', 'dalam', 1),
(2, '5 x 5', 'Meja, Kursi, Kasur, TV, Kipas', 'dalam', 9);

-- --------------------------------------------------------

--
-- Table structure for table `kamar_images`
--

CREATE TABLE `kamar_images` (
  `idImage` int(11) NOT NULL,
  `idKamar` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL
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
  `statusKetersediaan` enum('Tersedia','Ditempati','Dalam Perbaikan') NOT NULL DEFAULT 'Tersedia',
  `deskripsi` varchar(255) NOT NULL,
  `gambar` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kamar_kos`
--

INSERT INTO `kamar_kos` (`idKamar`, `nomorKamar`, `tipeKamar`, `harga`, `statusKetersediaan`, `deskripsi`, `gambar`) VALUES
(1, 1, 'Standard', 600000, 'Ditempati', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla tempor eros et porttitor rutrum. In placerat quis tellus ac luctus. Cras pulvinar nisi eu sem consequat, non tempor est porta. In semper dictum nisi, sed placerat massa facilisis eu. Donec ult', '6848260ab1a2c_Kamar1.png'),
(5, 5, 'asdasd', 123123, 'Ditempati', 'asdad', '68483de0a1729_Kamar2.png'),
(7, 8, 'asd', 800000, 'Ditempati', 'kamar kos', '684938a9555c6_Kamar3.png'),
(9, 6, 'Standard', 800000, 'Ditempati', 'Lorem ipsum dolor sit amet', 'room_1750584991_e50489ce.png');

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

CREATE TABLE `laporan` (
  `idLaporan` int(11) NOT NULL,
  `periodeLaporan` varchar(255) NOT NULL,
  `totalProfit` int(11) NOT NULL,
  `totalOmset` int(11) NOT NULL,
  `totalPengeluaran` int(11) NOT NULL
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
  `profilePicture` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`idPelanggan`, `username`, `password`, `namaLengkap`, `nomorHp`, `email`, `alamat`, `profilePicture`) VALUES
(1, 'rama', '123', 'gusti rama', '123', 'rama@mail.com', 'jalan', ''),
(2, 'adis', '$2y$10$LoOeuxlNk355xUqwXbYnwOohp/NAVISmwU5opif2oVwDY.wzVK3EO', 'Adi Setya', '082108210821', 'adi@gmail.com', 'Jl. Jalan No. 22', '68515fb236d78_images (2).jpg'),
(6, 'jokowi', '$2y$10$t69fabhRQAkT5lOoOWmzieZJrR0S9bFNeeuLU.RjK09BdP1niXLWG', 'jkw dodo', '-', 'jkw@gmail.com', '-', ''),
(7, 'bahlil', '$2y$10$Rorse7B04m.8LNbPu7AdLul8SYeTi1VHM8UocXjH/FtyElearwT1S', 'bahlil silver', '-', 'bahlil@gmail.com', '-', '');

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
  `bukti_transfer` varchar(255) DEFAULT NULL,
  `idPemesanan` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`idPembayaran`, `tanggalPembayaran`, `metodePembayaran`, `jumlahPembayaran`, `statusPembayaran`, `bukti_transfer`, `idPemesanan`) VALUES
(1, '2025-06-15', 'cash', 600000, 'Menunggu Konfirmasi', NULL, 0),
(2, '2025-06-15', 'cash', 600000, 'Lunas', NULL, 2),
(3, '2025-06-15', 'cash', 600000, 'Lunas', NULL, 3),
(4, '2025-06-15', 'cash', 600000, 'Lunas', NULL, 4),
(5, '2025-06-15', 'cash', 600000, 'Lunas', NULL, 5),
(6, '2025-06-17', 'cash', 123123, 'Lunas', NULL, 9),
(7, '2025-06-22', 'cash', 123123, 'Lunas', NULL, 10),
(8, '2025-06-22', 'cash', 4800000, 'Lunas', NULL, 11),
(9, '2025-06-22', 'cash', 800000, 'Lunas', NULL, 12),
(10, '2025-06-22', 'cash', 800000, 'Lunas', NULL, 12),
(11, '2025-06-22', 'cash', 800000, 'Lunas', NULL, 12),
(12, '2025-06-22', 'cash', 800000, 'Lunas', NULL, 12);

-- --------------------------------------------------------

--
-- Table structure for table `pemesanan`
--

CREATE TABLE `pemesanan` (
  `idPemesanan` int(11) NOT NULL,
  `tanggalPemesanan` date NOT NULL,
  `lamaSewa` int(11) NOT NULL,
  `jenis_sewa` enum('bulanan','mingguan','harian') NOT NULL,
  `harga_per_periode` int(11) NOT NULL,
  `totalHarga` int(11) NOT NULL,
  `statusPemesanan` enum('Tertunda','Terkonfirmasi','Dibatalkan') NOT NULL,
  `idPelanggan` int(11) NOT NULL,
  `idPelanggan_aktif` int(11) DEFAULT NULL,
  `idKamar` int(11) NOT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `jenisPemesanan` enum('pesan','perpanjang') NOT NULL DEFAULT 'pesan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pemesanan`
--

INSERT INTO `pemesanan` (`idPemesanan`, `tanggalPemesanan`, `lamaSewa`, `jenis_sewa`, `harga_per_periode`, `totalHarga`, `statusPemesanan`, `idPelanggan`, `idPelanggan_aktif`, `idKamar`, `tanggal_mulai`, `tanggal_selesai`, `is_active`, `jenisPemesanan`) VALUES
(2, '2025-06-15', 1, 'bulanan', 0, 600000, 'Terkonfirmasi', 2, 2, 1, '2025-06-14', '2025-07-14', 1, 'pesan'),
(3, '2025-06-15', 1, 'bulanan', 0, 600000, 'Tertunda', 2, NULL, 1, NULL, NULL, 1, 'pesan'),
(4, '2025-06-15', 1, 'bulanan', 600000, 600000, 'Tertunda', 2, NULL, 1, NULL, NULL, 1, 'pesan'),
(5, '2025-06-15', 1, 'bulanan', 600000, 600000, 'Terkonfirmasi', 2, NULL, 1, NULL, NULL, 1, 'pesan'),
(9, '2025-06-17', 1, 'bulanan', 123123, 123123, 'Terkonfirmasi', 6, 6, 5, '2025-06-17', '1970-01-01', 1, 'pesan'),
(10, '2025-06-22', 1, 'bulanan', 123123, 123123, 'Terkonfirmasi', 2, 2, 5, '2025-06-22', '1970-01-01', 1, 'pesan'),
(11, '2025-06-22', 6, 'bulanan', 800000, 4800000, 'Terkonfirmasi', 2, 2, 9, '2025-06-22', '1970-01-01', 1, 'pesan'),
(12, '2025-06-22', 1, 'bulanan', 800000, 800000, 'Terkonfirmasi', 7, 7, 7, '2025-06-22', '2025-09-22', 1, 'pesan');

-- --------------------------------------------------------

--
-- Table structure for table `pengeluaran`
--

CREATE TABLE `pengeluaran` (
  `idPengeluaran` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `jenisPengeluaran` enum('Listrik','Air','Wifi','Lainnya') NOT NULL,
  `jumlah` int(11) NOT NULL,
  `namaPengeluaran` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengeluaran`
--

INSERT INTO `pengeluaran` (`idPengeluaran`, `tanggal`, `keterangan`, `jenisPengeluaran`, `jumlah`, `namaPengeluaran`) VALUES
(2, '2025-06-21', '', 'Listrik', 1000000, 'PLN'),
(3, '2025-06-21', '', 'Air', 100000, 'PDAM'),
(4, '2025-06-21', '', 'Lainnya', 50000, 'Sampah');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`idAdmin`);

--
-- Indexes for table `biaya_tambahan`
--
ALTER TABLE `biaya_tambahan`
  ADD PRIMARY KEY (`idBiayaTambahan`),
  ADD KEY `idPelanggan` (`idPelanggan`);

--
-- Indexes for table `fasilitas`
--
ALTER TABLE `fasilitas`
  ADD PRIMARY KEY (`idFasilitas`),
  ADD KEY `idKamar` (`idKamar`);

--
-- Indexes for table `kamar_images`
--
ALTER TABLE `kamar_images`
  ADD PRIMARY KEY (`idImage`),
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
  ADD PRIMARY KEY (`idLaporan`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`idPelanggan`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`idPembayaran`),
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
  ADD PRIMARY KEY (`idPengeluaran`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `idAdmin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `biaya_tambahan`
--
ALTER TABLE `biaya_tambahan`
  MODIFY `idBiayaTambahan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `fasilitas`
--
ALTER TABLE `fasilitas`
  MODIFY `idFasilitas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `kamar_images`
--
ALTER TABLE `kamar_images`
  MODIFY `idImage` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kamar_kos`
--
ALTER TABLE `kamar_kos`
  MODIFY `idKamar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `idLaporan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `idPelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `idPembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `pemesanan`
--
ALTER TABLE `pemesanan`
  MODIFY `idPemesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  MODIFY `idPengeluaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `biaya_tambahan`
--
ALTER TABLE `biaya_tambahan`
  ADD CONSTRAINT `biaya_tambahan_ibfk_1` FOREIGN KEY (`idPelanggan`) REFERENCES `pelanggan` (`idPelanggan`);

--
-- Constraints for table `fasilitas`
--
ALTER TABLE `fasilitas`
  ADD CONSTRAINT `fasilitas_ibfk_1` FOREIGN KEY (`idKamar`) REFERENCES `kamar_kos` (`idKamar`);

--
-- Constraints for table `kamar_images`
--
ALTER TABLE `kamar_images`
  ADD CONSTRAINT `kamar_images_ibfk_1` FOREIGN KEY (`idKamar`) REFERENCES `kamar_kos` (`idKamar`) ON DELETE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`idPemesanan`) REFERENCES `pemesanan` (`idPemesanan`);

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
