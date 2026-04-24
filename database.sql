-- ============================================================
-- Database: buku_tamu_digital
-- Tema: Buku Tamu Digital
-- ============================================================

CREATE DATABASE IF NOT EXISTS buku_tamu_digital CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE buku_tamu_digital;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'petugas') DEFAULT 'petugas',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tamu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_tamu VARCHAR(100) NOT NULL,
    instansi VARCHAR(150),
    keperluan TEXT NOT NULL,
    yang_dituju VARCHAR(100) NOT NULL,
    nomor_hp VARCHAR(20),
    tanggal_kunjungan DATE NOT NULL,
    jam_masuk TIME NOT NULL,
    jam_keluar TIME,
    status ENUM('hadir', 'menunggu', 'selesai') DEFAULT 'menunggu',
    keterangan TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Password default: password (bcrypt)
INSERT INTO users (nama, username, password, role) VALUES
('Administrator', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Petugas Resepsionis', 'petugas', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'petugas');

INSERT INTO tamu (nama_tamu, instansi, keperluan, yang_dituju, nomor_hp, tanggal_kunjungan, jam_masuk, jam_keluar, status, created_by) VALUES
('Budi Santoso', 'PT. Maju Bersama', 'Pertemuan bisnis mengenai kerjasama proyek', 'Bapak Direktur', '081234567890', CURDATE(), '09:00:00', '10:30:00', 'selesai', 1),
('Siti Rahayu', 'Dinas Pendidikan', 'Koordinasi program beasiswa', 'Ibu Kepala Bagian', '082345678901', CURDATE(), '10:00:00', NULL, 'hadir', 1),
('Ahmad Fauzi', 'Universitas Negeri', 'Pengambilan dokumen kerja sama', 'Bagian Administrasi', '083456789012', CURDATE(), '11:15:00', NULL, 'menunggu', 2),
('Dewi Lestari', 'CV. Sejahtera', 'Presentasi produk baru', 'Bapak Manager', '084567890123', DATE_SUB(CURDATE(), INTERVAL 1 DAY), '13:00:00', '14:00:00', 'selesai', 1),
('Rudi Hartono', '-', 'Keperluan pribadi', 'Bapak HRD', '085678901234', DATE_SUB(CURDATE(), INTERVAL 1 DAY), '14:30:00', '15:00:00', 'selesai', 2);
