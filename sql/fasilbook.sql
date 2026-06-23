-- Database FasilBook Final
CREATE DATABASE IF NOT EXISTS fasilbook;
USE fasilbook;

DROP TABLE IF EXISTS pesanan;
DROP TABLE IF EXISTS lapangan;
DROP TABLE IF EXISTS admin;

CREATE TABLE `lapangan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_lapangan` varchar(100) NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `harga_per_jam` int(11) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `lapangan` (`id`, `nama_lapangan`, `lokasi`, `harga_per_jam`, `gambar`) VALUES
(1, 'Arcamanik Futsal Camp', 'Jl. Arcamanik Endah No 15, Arcamanik, Bandung', 75000, 'img/lapangan_1.png'),
(2, 'Cisaranten Futsal Corner', 'Jl. Cisaranten Kulon No 22, Arcamanik, Bandung', 60000, 'img/lapangan_2.png'),
(3, 'Antapani Futsal Sederhana', 'Jl. Terusan Jakarta No 88, Antapani, Bandung', 65000, 'img/lapangan_3.png'),
(4, 'Sukamiskin Sport Lapang', 'Jl. Sukamiskin No 40, Arcamanik, Bandung', 50000, 'img/lapangan_4.png'),
(5, 'Endah Futsal Kampung', 'Jl. Endah Raya No 5, Arcamanik, Bandung', 55000, 'img/lapangan_5.png'),
(6, 'Ujungberung Futsal Rakyat', 'Jl. Ujungberung Raya No 10, Ujungberung, Bandung', 45000, 'img/lapangan_6.png');

CREATE TABLE `pesanan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lapangan_id` int(11) NOT NULL,
  `nama_pemesan` varchar(100) NOT NULL,
  `tanggal_booking` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `status_pembayaran` enum('Belum Bayar','Lunas') DEFAULT 'Belum Bayar',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`lapangan_id`) REFERENCES `lapangan`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data Dummy untuk Simulasi Jadwal yang Terisi hari ini agar tombol otomatis "Disabled"
INSERT INTO `pesanan` (`lapangan_id`, `nama_pemesan`, `tanggal_booking`, `jam_mulai`, `status_pembayaran`) VALUES
(1, 'Tim Fauzil FC', CURDATE(), '09:00:00', 'Lunas'),
(1, 'Hagi & Co', CURDATE(), '14:00:00', 'Belum Bayar'),
(2, 'Anak-Anak IF Poltek SCI', CURDATE(), '19:00:00', 'Lunas');

CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Akun Default: Username (admin), Password (admin) menggunakan bcrypt hash
INSERT INTO `admin` (`username`, `password`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
