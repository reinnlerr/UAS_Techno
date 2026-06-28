/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/* Create database if not exists and select it */
CREATE DATABASE IF NOT EXISTS `fasilbook`;
USE `fasilbook`;

/* Drop tables in correct dependency order to ensure clean import */
DROP TABLE IF EXISTS `pesanan`;
DROP TABLE IF EXISTS `lapangan`;
DROP TABLE IF EXISTS `mitra`;
DROP TABLE IF EXISTS `admin`;

/* -------------------------------------------------------- */
/* Struktur dari tabel `admin` */

CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/* Dumping data untuk tabel `admin` */
/* Login: admin / admin */

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$cYNZ6fRfaX550BXaE3Go0.Fx.tjDr1mGzaoGzcnT5mMykokuqohIu');

/* -------------------------------------------------------- */
/* Struktur dari tabel `mitra` */

CREATE TABLE `mitra` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_mitra` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `paket` enum('Starter','Growth','Scale') NOT NULL DEFAULT 'Starter',
  `komisi_persen` int(11) NOT NULL DEFAULT 8,
  `status_akun` varchar(20) NOT NULL DEFAULT 'Aktif',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/* Dumping data untuk tabel `mitra` */

INSERT INTO `mitra` (`id`, `nama_mitra`, `username`, `password`, `paket`, `komisi_persen`, `status_akun`) VALUES
(1, 'Futsal Arcamanik', 'mitra1', '123', 'Starter', 8, 'Aktif'),
(2, 'Antapani Sport Center', 'mitra2', '123', 'Growth', 6, 'Aktif'),
(3, 'Endah Futsal (Premium)', 'mitra3', '123', 'Scale', 4, 'Aktif'),
(4, 'Fu Sport Arena', 'mitra7', '123', 'Scale', 4, 'Aktif');

/* -------------------------------------------------------- */
/* Struktur dari tabel `lapangan` */
/* Harga disesuaikan dengan harga realistis lapangan futsal di Bandung (area Arcamanik) */

CREATE TABLE `lapangan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_mitra` int(11) NOT NULL,
  `nama_lapangan` varchar(100) NOT NULL,
  `lokasi` text NOT NULL,
  `harga_per_jam` int(11) NOT NULL,
  `harga_promo` int(11) DEFAULT NULL,
  `gambar` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_mitra` (`id_mitra`),
  CONSTRAINT `fk_lapangan_mitra` FOREIGN KEY (`id_mitra`) REFERENCES `mitra` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/* Dumping data untuk tabel `lapangan` */

INSERT INTO `lapangan` (`id`, `id_mitra`, `nama_lapangan`, `lokasi`, `harga_per_jam`, `harga_promo`, `gambar`) VALUES
(1, 1, 'Arcamanik Court A (Sintetis)', 'Jl. Pacuan Kuda No. 50, Arcamanik, Bandung', 120000, NULL, 'img/lapangan_1.png'),
(2, 2, 'Antapani Futsal Center', 'Jl. Terusan Jakarta No. 12, Antapani, Bandung', 150000, NULL, 'img/lapangan_2.png'),
(3, 3, 'Endah Arena 1 (Vinyl Premium)', 'Jl. Cisaranten Endah No. 8, Arcamanik, Bandung', 200000, 160000, 'img/lapangan_3.png'),
(4, 3, 'Endah Arena 2 (Sintetis)', 'Jl. Cisaranten Endah No. 8, Arcamanik, Bandung', 150000, 130000, 'img/lapangan_4.png'),
(5, 3, 'Endah Arena 3 (Interlock)', 'Jl. Cisaranten Endah No. 8, Arcamanik, Bandung', 175000, NULL, 'img/lapangan_5.png'),
(6, 4, 'Fu Court', 'Jl. Soekarno-Hatta No. 45, Bandung', 100000, 90000, 'img/lapangan_6.png');

/* -------------------------------------------------------- */
/* Struktur dari tabel `pesanan` */

CREATE TABLE `pesanan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lapangan_id` int(11) NOT NULL,
  `nama_pemesan` varchar(100) NOT NULL,
  `tanggal_booking` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `status_pembayaran` varchar(50) NOT NULL DEFAULT 'Menunggu Konfirmasi',
  `status_dana` varchar(50) NOT NULL DEFAULT 'Ditahan',
  PRIMARY KEY (`id`),
  KEY `lapangan_id` (`lapangan_id`),
  CONSTRAINT `fk_pesanan_lapangan` FOREIGN KEY (`lapangan_id`) REFERENCES `lapangan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/* Dumping data untuk tabel `pesanan` */
/* Status dana disinkronkan dengan alur: */
/*   Menunggu Konfirmasi -> dana Ditahan */
/*   Lunas (mitra konfirmasi) -> dana Siap Dicairkan */
/*   Lunas (admin cairkan) -> dana Sudah Dicairkan */
/*   Dibatalkan (Refund) -> dana Ditahan (dikembalikan ke pelanggan) */

INSERT INTO `pesanan` (`id`, `lapangan_id`, `nama_pemesan`, `tanggal_booking`, `jam_mulai`, `status_pembayaran`, `status_dana`) VALUES
(1, 1, 'FC Bandung', '2026-06-24', '09:00:00', 'Lunas', 'Siap Dicairkan'),
(2, 2, 'Kickers United', '2026-06-24', '10:00:00', 'Lunas', 'Siap Dicairkan'),
(3, 3, 'Futsal Lovers BDG', '2026-06-25', '19:00:00', 'Menunggu Konfirmasi', 'Ditahan'),
(4, 1, 'Raja Futsal', '2026-06-23', '14:00:00', 'Dibatalkan (Refund)', 'Ditahan'),
(5, 5, 'UAS Squad', '2026-06-26', '16:00:00', 'Lunas', 'Sudah Dicairkan'),
(6, 4, 'Garuda FC', '2026-06-27', '20:00:00', 'Lunas', 'Sudah Dicairkan'),
(7, 6, 'Elang Jawa', '2026-06-28', '08:00:00', 'Menunggu Konfirmasi', 'Ditahan');

/* -------------------------------------------------------- */
/* Struktur dari tabel `pengajuan_upgrade` */

CREATE TABLE `pengajuan_upgrade` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_mitra` int(11) NOT NULL,
  `paket_asal` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending Premium',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `id_mitra` (`id_mitra`),
  CONSTRAINT `fk_upgrade_mitra` FOREIGN KEY (`id_mitra`) REFERENCES `mitra` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
