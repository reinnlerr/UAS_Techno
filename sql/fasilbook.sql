CREATE DATABASE IF NOT EXISTS fasilbook;
USE fasilbook;

CREATE TABLE `lapangan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_lapangan` varchar(100) NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `harga_per_jam` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `lapangan` (`nama_lapangan`, `lokasi`, `harga_per_jam`) VALUES
('Futsal Merdeka Bandung', 'Jl. Merdeka No 12, Bandung', 150000),
('Supratman Futsal', 'Jl. Supratman, Bandung', 120000),
('Antapani Sport Center', 'Jl. Terusan Jakarta, Antapani', 100000);

CREATE TABLE `pesanan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lapangan_id` int(11) NOT NULL,
  `nama_pemesan` varchar(100) NOT NULL,
  `tanggal` date NOT NULL,
  `jam` varchar(20) NOT NULL,
  `status` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
);
