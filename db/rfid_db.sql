-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 25 Des 2024 pada 16.36
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rfid_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id`, `Username`, `Password`) VALUES
(1, 'NASRUL', '123');

-- --------------------------------------------------------

--
-- Struktur dari tabel `rfid_logs`
--

CREATE TABLE `rfid_logs` (
  `id` int(11) NOT NULL,
  `uid` varchar(50) NOT NULL,
  `scan_time` datetime DEFAULT current_timestamp(),
  `status` enum('KELUAR','MASUK','','') NOT NULL DEFAULT 'MASUK',
  `nim` int(20) NOT NULL,
  `plat` varchar(20) NOT NULL,
  `jenis_kendaraan` enum('MOTOR','MOBIL') NOT NULL,
  `waktu_masuk` datetime DEFAULT NULL,
  `waktu_keluar` datetime DEFAULT NULL,
  `tarif` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `rfid_logs`
--

INSERT INTO `rfid_logs` (`id`, `uid`, `scan_time`, `status`, `nim`, `plat`, `jenis_kendaraan`, `waktu_masuk`, `waktu_keluar`, `tarif`) VALUES
(49, '9384C726', '2024-12-24 02:25:31', 'KELUAR', 0, '', 'MOTOR', NULL, '2024-12-24 02:25:53', 0),
(50, '', '2024-12-24 02:26:59', 'KELUAR', 2011331548, 'B2430QOL', 'MOTOR', '2024-12-24 02:26:00', '2024-12-24 02:27:05', 2000),
(51, '', '2024-12-24 02:27:17', 'KELUAR', 2011331548, 'B2430QOL', 'MOTOR', '2024-12-24 02:27:17', '2024-12-24 02:29:46', 2000),
(52, '', '2024-12-24 02:28:53', 'KELUAR', 2011331548, 'B2430QOL', 'MOTOR', '2024-12-24 00:00:00', '2024-12-24 02:29:03', 4000),
(53, '', '2024-12-24 02:29:30', 'KELUAR', 2011331548, 'B2430QOL', 'MOTOR', '2024-08-17 02:29:00', '2024-12-24 02:29:31', 3096000),
(54, '53F9402A', '2024-12-24 02:30:24', 'KELUAR', 0, '', 'MOTOR', NULL, '2024-12-24 02:31:38', 0),
(55, '53F9402A', '2024-12-24 02:46:58', 'KELUAR', 0, '', 'MOTOR', NULL, '2024-12-24 02:47:18', 0),
(56, '', '2024-12-25 22:01:09', 'KELUAR', 123123, 'plat', 'MOTOR', '2024-12-25 22:01:09', '2024-12-25 22:09:36', 2000),
(57, '', '2024-12-25 22:16:07', 'KELUAR', 1234, 'plat1234', 'MOTOR', '2024-12-25 22:16:07', '2024-12-25 22:16:09', 2000),
(58, '', '2024-12-25 22:16:19', 'KELUAR', 12345, '12345', 'MOTOR', '2024-12-25 22:16:19', '2024-12-25 22:16:21', 2000),
(59, '', '2024-12-25 22:27:33', 'MASUK', 12, '123', 'MOTOR', '2024-12-25 22:27:33', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `rfid_users`
--

CREATE TABLE `rfid_users` (
  `uid` varchar(20) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `nim` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `rfid_users`
--

INSERT INTO `rfid_users` (`uid`, `nama`, `nim`) VALUES
('53F9402A', 'PANCA SHAKA SATYA', '211010150026'),
('9384C726', 'PUTRA SUMARDI', '211010150030');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- Indeks untuk tabel `rfid_logs`
--
ALTER TABLE `rfid_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `rfid_users`
--
ALTER TABLE `rfid_users`
  ADD PRIMARY KEY (`uid`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `rfid_logs`
--
ALTER TABLE `rfid_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
