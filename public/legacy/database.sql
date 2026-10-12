CREATE DATABASE IF NOT EXISTS `veter_otomotif`;
USE `veter_otomotif`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'customer') DEFAULT 'customer',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `cars` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_mobil` VARCHAR(100) NOT NULL,
  `kategori` VARCHAR(50) NOT NULL,
  `harga` BIGINT NOT NULL,
  `tahun` INT NOT NULL,
  `transmisi` ENUM('Manual', 'Otomatis') NOT NULL,
  `bahan_bakar` VARCHAR(50) NOT NULL,
  `gambar` VARCHAR(255) NOT NULL,
  `deskripsi` TEXT,
  `stok` INT DEFAULT 1
);

CREATE TABLE IF NOT EXISTS `test_drive` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT,
  `car_id` INT,
  `nama_lengkap` VARCHAR(100) NOT NULL,
  `telepon` VARCHAR(20) NOT NULL,
  `tanggal` DATE NOT NULL,
  `jam` TIME NOT NULL,
  `status` ENUM('Menunggu', 'Dikonfirmasi', 'Selesai', 'Dibatalkan') DEFAULT 'Menunggu',
  FOREIGN KEY (`car_id`) REFERENCES `cars`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `services` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT,
  `nama_pemilik` VARCHAR(100) NOT NULL,
  `telepon` VARCHAR(20) NOT NULL,
  `nomor_polisi` VARCHAR(20) NOT NULL,
  `tipe_kendaraan` VARCHAR(100) NOT NULL,
  `jenis_servis` VARCHAR(100) NOT NULL,
  `tanggal_servis` DATE NOT NULL,
  `keluhan` TEXT,
  `status` ENUM('Pendaftaran', 'Dikerjakan', 'Selesai') DEFAULT 'Pendaftaran'
);

-- Data Awal Pengguna (Admin: admin@veter.com / password123)
INSERT INTO `users` (`nama`, `email`, `password`, `role`) VALUES
('Administrator', 'admin@veter.com', '$2y$10$wN1rYp1yVbH6jM/W8ZJ3q.6fI7E28E93qVjB2l4N0gK0s1V8f1XqW', 'admin');

-- Data Katalog Mobil Awal
INSERT INTO `cars` (`nama_mobil`, `kategori`, `harga`, `tahun`, `transmisi`, `bahan_bakar`, `gambar`, `deskripsi`, `stok`) VALUES
('Toyota Fortuner GR Sport', 'SUV', 620000000, 2024, 'Otomatis', 'Diesel', 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=600', 'Ketangguhan SUV premium berjiwa sporty dengan mesin bertenaga dan fitur keamanan TSS.', 3),
('Honda Civic RS Turbo', 'Sedan', 610000000, 2024, 'Otomatis', 'Bensin', 'https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?w=600', 'Sedan legendaris bertenaga VTEC Turbo dengan interior mewah dan aerodinamika presisi.', 2),
('Hyundai Ioniq 5 EV', 'Electric', 780000000, 2024, 'Otomatis', 'Listrik', 'https://images.unsplash.com/photo-1563720223185-11003d516935?w=600', 'Kendaraan listrik ramah lingkungan masa depan dengan jarak tempuh hingga 480 km per charge.', 4);