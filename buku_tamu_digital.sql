-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 25 Apr 2026 pada 06.28
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
-- Database: `buku_tamu_digital`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `ruangan`
--

CREATE TABLE `ruangan` (
  `id` int(11) NOT NULL,
  `kode_ruangan` varchar(20) NOT NULL,
  `nama_ruangan` varchar(100) NOT NULL,
  `lokasi` varchar(150) DEFAULT NULL,
  `kapasitas` int(11) DEFAULT 1,
  `fasilitas` text DEFAULT NULL,
  `status` enum('tersedia','digunakan','maintenance') DEFAULT 'tersedia',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `ruangan`
--

INSERT INTO `ruangan` (`id`, `kode_ruangan`, `nama_ruangan`, `lokasi`, `kapasitas`, `fasilitas`, `status`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'R-001', 'Ruang Rapat Utama', 'Lantai 2', 20, 'AC, Proyektor, Whiteboard, TV', 'tersedia', NULL, '2026-04-25 02:28:28', '2026-04-25 02:28:28'),
(2, 'R-002', 'Ruang Direktur', 'Lantai 3', 5, 'AC, Sofa, TV', 'tersedia', NULL, '2026-04-25 02:28:28', '2026-04-25 02:28:28'),
(3, 'R-003', 'Ruang Meeting Kecil', 'Lantai 1', 8, 'AC, Whiteboard', 'digunakan', NULL, '2026-04-25 02:28:28', '2026-04-25 02:28:28'),
(4, 'R-004', 'Aula Serbaguna', 'Lantai 1', 100, 'AC, Sound System, Proyektor, Panggung', 'tersedia', NULL, '2026-04-25 02:28:28', '2026-04-25 02:28:28'),
(5, 'R-005', 'Ruang HRD', 'Lantai 2', 6, 'AC, Komputer', 'maintenance', NULL, '2026-04-25 02:28:28', '2026-04-25 02:28:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tamu`
--

CREATE TABLE `tamu` (
  `id` int(11) NOT NULL,
  `nama_tamu` varchar(100) NOT NULL,
  `instansi` varchar(150) DEFAULT NULL,
  `keperluan` text NOT NULL,
  `yang_dituju` varchar(100) NOT NULL,
  `nomor_hp` varchar(20) DEFAULT NULL,
  `tanggal_kunjungan` date NOT NULL,
  `jam_masuk` time NOT NULL,
  `jam_keluar` time DEFAULT NULL,
  `status` enum('hadir','menunggu','selesai') DEFAULT 'menunggu',
  `keterangan` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tamu`
--

INSERT INTO `tamu` (`id`, `nama_tamu`, `instansi`, `keperluan`, `yang_dituju`, `nomor_hp`, `tanggal_kunjungan`, `jam_masuk`, `jam_keluar`, `status`, `keterangan`, `created_by`, `created_at`, `updated_at`) VALUES
(2, 'Siti Rahayu', 'Dinas Pendidikan', 'Koordinasi program beasiswa', 'Ibu Kepala Bagian', '082345678901', '2026-04-24', '10:00:00', NULL, 'hadir', NULL, 1, '2026-04-23 20:58:17', '2026-04-23 20:58:17'),
(3, 'Ahmad Fauzi', 'Universitas Negeri', 'Pengambilan dokumen kerja sama', 'Bagian Administrasi', '083456789012', '2026-04-24', '11:15:00', NULL, 'menunggu', NULL, 2, '2026-04-23 20:58:17', '2026-04-23 20:58:17'),
(4, 'Dewi Lestari', 'CV. Sejahtera', 'Presentasi produk baru', 'Bapak Manager', '084567890123', '2026-04-23', '13:00:00', '14:00:00', 'selesai', NULL, 1, '2026-04-23 20:58:17', '2026-04-23 20:58:17'),
(5, 'Rudi Hartono', '-', 'Keperluan pribadi', 'Bapak HRD', '085678901234', '2026-04-23', '14:30:00', '15:00:00', 'selesai', NULL, 2, '2026-04-23 20:58:17', '2026-04-23 20:58:17'),
(6, 'Budi Santoso', 'PT. Maju Bersama', 'Pertemuan bisnis mengenai kerjasama proyek', 'Bapak Direktur', '081234567890', '2026-04-25', '09:00:00', '10:30:00', 'selesai', NULL, 1, '2026-04-25 02:28:28', '2026-04-25 02:28:28'),
(7, 'Siti Rahayu', 'Dinas Pendidikan', 'Koordinasi program beasiswa', 'Ibu Kepala Bagian', '082345678901', '2026-04-25', '10:00:00', NULL, 'hadir', NULL, 1, '2026-04-25 02:28:28', '2026-04-25 02:28:28'),
(8, 'Ahmad Fauzi', 'Universitas Negeri', 'Pengambilan dokumen kerja sama', 'Bagian Administrasi', '083456789012', '2026-04-25', '11:15:00', NULL, 'menunggu', NULL, 2, '2026-04-25 02:28:28', '2026-04-25 02:28:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','petugas') DEFAULT 'petugas',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin1', '$2y$10$Q4iWSAzmHa/0VJ.hDKCSXOPXitvFQ4Tq75NoiIOYrhxjO7iWngEWO', 'admin', '2026-04-23 20:58:17', '2026-04-24 12:35:10'),
(2, 'Petugas Resepsionis', 'Resepsionis 1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'petugas', '2026-04-23 20:58:17', '2026-04-24 12:35:39'),
(3, 'Administrator', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2026-04-25 02:28:28', '2026-04-25 02:28:28'),
(4, 'Petugas Resepsionis', 'petugas', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'petugas', '2026-04-25 02:28:28', '2026-04-25 02:28:28');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `ruangan`
--
ALTER TABLE `ruangan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_ruangan` (`kode_ruangan`);

--
-- Indeks untuk tabel `tamu`
--
ALTER TABLE `tamu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `ruangan`
--
ALTER TABLE `ruangan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `tamu`
--
ALTER TABLE `tamu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `tamu`
--
ALTER TABLE `tamu`
  ADD CONSTRAINT `tamu_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
