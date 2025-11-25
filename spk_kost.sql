-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 25, 2025 at 05:27 PM
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
-- Database: `spk_kost`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`, `nama`) VALUES
(1, 'admin', '$2y$10$PdPdTH9nnrfL5A6ocnZiS.d/P0sC8LddEwfGW0/f0KHQ0mzFAfZAO', 'Administrator');

-- --------------------------------------------------------

--
-- Table structure for table `alternatif`
--

CREATE TABLE `alternatif` (
  `id_alternatif` int(11) NOT NULL,
  `nama_kost` varchar(100) DEFAULT NULL,
  `harga` decimal(10,2) DEFAULT NULL,
  `jarak` decimal(10,2) DEFAULT NULL,
  `fasilitas` int(11) DEFAULT NULL,
  `keamanan` int(11) DEFAULT NULL,
  `kebersihan` int(11) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alternatif`
--

INSERT INTO `alternatif` (`id_alternatif`, `nama_kost`, `harga`, `jarak`, `fasilitas`, `keamanan`, `kebersihan`, `alamat`, `foto`) VALUES
(1, 'Kost Melati Residence', 850000.00, 1.20, 8, 9, 8, 'Jl. Sudirman No.15', 'default.jpg'),
(2, 'Kost Anggrek Putih', 1200000.00, 0.50, 9, 9, 9, 'Jl. Gatot Subroto No.20', 'default.jpg'),
(3, 'Kost Mawar Indah', 650000.00, 2.50, 6, 7, 7, 'Jl. Ahmad Yani No.45', 'default.jpg'),
(4, 'Kost Flamboyan', 950000.00, 1.80, 7, 8, 8, 'Jl. Diponegoro No.12', 'default.jpg'),
(5, 'Kost Sakura House', 1500000.00, 0.30, 10, 10, 9, 'Jl. Imam Bonjol No.8', 'default.jpg'),
(6, 'Kost Kenanga Asri', 700000.00, 3.00, 6, 6, 7, 'Jl. Pahlawan No.33', 'default.jpg'),
(7, 'Kost Teratai Hijau', 800000.00, 1.50, 7, 8, 7, 'Jl. Veteran No.22', 'default.jpg'),
(8, 'Kost Dahlia Premium', 1300000.00, 0.80, 9, 9, 9, 'Jl. Merdeka No.5', 'default.jpg'),
(9, 'Kost Tulip Garden', 600000.00, 3.50, 5, 6, 6, 'Jl. Kertajaya No.88', 'default.jpg'),
(10, 'Kost Cempaka Sari', 900000.00, 1.00, 8, 8, 8, 'Jl. Basuki Rahmat No.17', 'default.jpg'),
(11, 'Kost Bougenville', 750000.00, 2.00, 7, 7, 7, 'Jl. Pemuda No.40', 'default.jpg'),
(12, 'Kost Lavender Hills', 1100000.00, 0.70, 8, 9, 8, 'Jl. Supratman No.11', 'default.jpg'),
(13, 'Kost Lily White', 850000.00, 1.30, 7, 8, 8, 'Jl. Airlangga No.25', 'default.jpg'),
(14, 'Kost Orchid Palace', 1400000.00, 0.40, 9, 10, 9, 'Jl. Thamrin No.3', 'default.jpg'),
(15, 'Kost Jasmine Tower', 1000000.00, 1.10, 8, 8, 8, 'Jl. Brawijaya No.14', 'default.jpg'),
(16, 'Kost Azalea Residence', 700000.00, 2.80, 6, 7, 7, 'Jl. Raya Darmo No.55', 'default.jpg'),
(17, 'Kost Magnolia', 950000.00, 1.60, 7, 8, 7, 'Jl. Mayjen Sungkono No.30', 'default.jpg'),
(18, 'Kost Peony House', 650000.00, 3.20, 6, 6, 6, 'Jl. Kedung Cowek No.77', 'default.jpg'),
(19, 'Kost Sunflower', 800000.00, 1.90, 7, 7, 7, 'Jl. Kalimantan No.21', 'default.jpg'),
(20, 'Kost Camellia Park', 1250000.00, 0.60, 9, 9, 9, 'Jl. HR Muhammad No.9', 'default.jpg'),
(21, 'Kost Edelweiss', 900000.00, 1.40, 8, 8, 8, 'Jl. Semeru No.18', 'default.jpg'),
(22, 'Kost Iris Garden', 750000.00, 2.30, 7, 7, 7, 'Jl. Pandegiling No.42', 'default.jpg'),
(23, 'Kost Carnation', 600000.00, 3.80, 5, 6, 6, 'Jl. Ngagel No.90', 'default.jpg'),
(24, 'Kost Poppy Meadow', 1050000.00, 1.00, 8, 8, 8, 'Jl. Darmokali No.16', 'default.jpg'),
(25, 'Kost Violet Valley', 850000.00, 1.70, 7, 8, 7, 'Jl. Nginden No.28', 'default.jpg'),
(26, 'Kost Zinnia House', 700000.00, 2.60, 6, 7, 7, 'Jl. Tenggilis No.51', 'default.jpg'),
(27, 'Kost Geranium', 950000.00, 1.20, 8, 8, 8, 'Jl. Manyar No.19', 'default.jpg'),
(28, 'Kost Petunia Place', 1150000.00, 0.90, 8, 9, 8, 'Jl. Rungkut No.13', 'default.jpg'),
(29, 'Kost Primrose', 800000.00, 2.10, 7, 7, 7, 'Jl. Wonokromo No.35', 'default.jpg'),
(30, 'Kost Daffodil Den', 650000.00, 3.30, 6, 6, 6, 'Jl. Kalijudan No.66', 'default.jpg'),
(31, 'Kost Hydrangea Hub', 1300000.00, 0.50, 9, 9, 9, 'Jl. Gubernur Suryo No.7', 'default.jpg'),
(32, 'Kost Begonia Bay', 900000.00, 1.50, 7, 8, 8, 'Jl. Ketintang No.24', 'default.jpg'),
(33, 'Kost Freesia Field', 750000.00, 2.40, 7, 7, 7, 'Jl. Jagir No.38', 'default.jpg'),
(34, 'Kost Hibiscus Haven', 1000000.00, 1.10, 8, 8, 8, 'Jl. Tambaksari No.15', 'default.jpg'),
(35, 'Kost Protea Point', 850000.00, 1.80, 7, 8, 7, 'Jl. Simokerto No.29', 'default.jpg'),
(36, 'Kost Aster Abode', 700000.00, 2.70, 6, 7, 7, 'Jl. Genteng No.47', 'default.jpg'),
(37, 'Kost Cosmos Corner', 950000.00, 1.30, 8, 8, 8, 'Jl. Bubutan No.20', 'default.jpg'),
(38, 'Kost Gardenia Grove', 1200000.00, 0.70, 9, 9, 8, 'Jl. Embong Malang No.10', 'default.jpg'),
(39, 'Kost Fuchsia Fort', 800000.00, 2.00, 7, 7, 7, 'Jl. Pasar Besar No.32', 'default.jpg'),
(40, 'Kost Lotus Lane', 650000.00, 3.40, 6, 6, 6, 'Jl. Tandes No.72', 'default.jpg'),
(41, 'Kost Narcissus Nest', 1100000.00, 0.80, 8, 9, 8, 'Jl. Ciliwung No.12', 'default.jpg'),
(42, 'Kost Ranunculus Row', 900000.00, 1.60, 7, 8, 8, 'Jl. Brantas No.26', 'default.jpg'),
(43, 'Kost Wisteria Way', 750000.00, 2.50, 7, 7, 7, 'Jl. Kapuas No.41', 'default.jpg'),
(44, 'Kost Amaryllis Arms', 1050000.00, 1.00, 8, 8, 8, 'Jl. Bengawan Solo No.17', 'default.jpg'),
(45, 'Kost Bluebell Base', 850000.00, 1.90, 7, 8, 7, 'Jl. Mahakam No.31', 'default.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `kriteria`
--

CREATE TABLE `kriteria` (
  `id_kriteria` int(11) NOT NULL,
  `kode_kriteria` varchar(10) DEFAULT NULL,
  `nama_kriteria` varchar(100) DEFAULT NULL,
  `bobot` decimal(10,4) DEFAULT NULL,
  `jenis` enum('benefit','cost') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kriteria`
--

INSERT INTO `kriteria` (`id_kriteria`, `kode_kriteria`, `nama_kriteria`, `bobot`, `jenis`) VALUES
(1, 'C1', 'Harga', 0.3196, 'cost'),
(2, 'C2', 'Jarak ke Kampus', 0.2371, 'cost'),
(3, 'C3', 'Fasilitas', 0.1902, 'benefit'),
(4, 'C4', 'Keamanan', 0.1629, 'benefit'),
(5, 'C5', 'Kebersihan', 0.0902, 'benefit');

-- --------------------------------------------------------

--
-- Table structure for table `perbandingan_ahp`
--

CREATE TABLE `perbandingan_ahp` (
  `id` int(11) NOT NULL,
  `kriteria1` varchar(10) DEFAULT NULL,
  `kriteria2` varchar(10) DEFAULT NULL,
  `nilai` decimal(10,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `alternatif`
--
ALTER TABLE `alternatif`
  ADD PRIMARY KEY (`id_alternatif`);

--
-- Indexes for table `kriteria`
--
ALTER TABLE `kriteria`
  ADD PRIMARY KEY (`id_kriteria`);

--
-- Indexes for table `perbandingan_ahp`
--
ALTER TABLE `perbandingan_ahp`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `alternatif`
--
ALTER TABLE `alternatif`
  MODIFY `id_alternatif` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `kriteria`
--
ALTER TABLE `kriteria`
  MODIFY `id_kriteria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `perbandingan_ahp`
--
ALTER TABLE `perbandingan_ahp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
