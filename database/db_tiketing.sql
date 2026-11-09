-- =====================================================================
--  Database : db_tiketing
--  Aplikasi : Sistem Tiketing Wisata / Event Lokal (CodeIgniter 3)
--  Cara pakai: Import file ini lewat phpMyAdmin (tab Import) atau CLI:
--              mysql -u root < db_tiketing.sql
--  Login awal: admin / admin123 (pengelola)  |  pembeli / pembeli123 (pembeli)
--  Tanggal event contoh dibuat RELATIF terhadap tanggal saat di-import,
--  jadi selalu ada event yang akan datang, berlangsung, dan selesai.
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `db_tiketing` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `db_tiketing`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `transaksi`;
DROP TABLE IF EXISTS `jenis_tiket`;
DROP TABLE IF EXISTS `event`;
DROP TABLE IF EXISTS `pemesan`;
DROP TABLE IF EXISTS `lokasi`;
DROP TABLE IF EXISTS `kategori`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- Kategori event
-- ---------------------------------------------------------------------
CREATE TABLE `kategori` (
  `id_kategori`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_kategori` VARCHAR(100) NOT NULL,
  `keterangan`    VARCHAR(255) NULL,
  PRIMARY KEY (`id_kategori`),
  UNIQUE KEY `uk_kategori_nama` (`nama_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- Lokasi
-- ---------------------------------------------------------------------
CREATE TABLE `lokasi` (
  `id_lokasi`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_lokasi` VARCHAR(150) NOT NULL,
  `alamat`      VARCHAR(255) NULL,
  `kota`        VARCHAR(100) NOT NULL,
  `kapasitas`   INT UNSIGNED NULL,
  PRIMARY KEY (`id_lokasi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- Wisata / Event
-- ---------------------------------------------------------------------
CREATE TABLE `event` (
  `id_event`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_event`      VARCHAR(150) NOT NULL,
  `id_kategori`     INT UNSIGNED NOT NULL,
  `id_lokasi`       INT UNSIGNED NOT NULL,
  `tanggal_mulai`   DATE NOT NULL,
  `tanggal_selesai` DATE NOT NULL,
  `jam_mulai`       TIME NULL,
  `deskripsi`       TEXT NULL,
  `status`          ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_event`),
  KEY `idx_event_kategori` (`id_kategori`),
  KEY `idx_event_lokasi` (`id_lokasi`),
  KEY `idx_event_tanggal` (`tanggal_mulai`, `tanggal_selesai`),
  CONSTRAINT `fk_event_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_event_lokasi`   FOREIGN KEY (`id_lokasi`)   REFERENCES `lokasi` (`id_lokasi`)     ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- Jenis tiket (harga + kuota). sisa = kuota - terjual
-- ---------------------------------------------------------------------
CREATE TABLE `jenis_tiket` (
  `id_tiket`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_event`   INT UNSIGNED NOT NULL,
  `nama_tiket` VARCHAR(100) NOT NULL,
  `harga`      DECIMAL(12,2) NOT NULL DEFAULT 0,
  `kuota`      INT UNSIGNED NOT NULL,
  `terjual`    INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_tiket`),
  KEY `idx_tiket_event` (`id_event`),
  CONSTRAINT `fk_tiket_event` FOREIGN KEY (`id_event`) REFERENCES `event` (`id_event`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `chk_tiket_harga`   CHECK (`harga` >= 0),
  CONSTRAINT `chk_tiket_kuota`   CHECK (`kuota` > 0),
  CONSTRAINT `chk_tiket_terjual` CHECK (`terjual` <= `kuota`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- Pemesan / pengunjung
-- ---------------------------------------------------------------------
CREATE TABLE `pemesan` (
  `id_pemesan` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama`       VARCHAR(100) NOT NULL,
  `email`      VARCHAR(100) NOT NULL,
  `no_hp`      VARCHAR(20)  NOT NULL,
  `alamat`     VARCHAR(255) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pemesan`),
  KEY `idx_pemesan_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- Pengguna. role: admin (pengelola) | pembeli (hanya Loket & Kasir)
-- Akun pembeli terhubung ke satu data pemesan (id_pemesan)
-- ---------------------------------------------------------------------
CREATE TABLE `users` (
  `id_user`    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama`       VARCHAR(100) NOT NULL,
  `username`   VARCHAR(50)  NOT NULL,
  `password`   VARCHAR(255) NOT NULL,
  `role`       ENUM('admin','pembeli') NOT NULL DEFAULT 'pembeli',
  `id_pemesan` INT UNSIGNED NULL,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `uk_users_username` (`username`),
  KEY `idx_users_pemesan` (`id_pemesan`),
  CONSTRAINT `fk_users_pemesan` FOREIGN KEY (`id_pemesan`) REFERENCES `pemesan` (`id_pemesan`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
-- Transaksi pemesanan tiket
-- status: menunggu (kuota sudah dipesan, belum bayar) | lunas | batal (kuota dikembalikan)
-- ---------------------------------------------------------------------
CREATE TABLE `transaksi` (
  `id_transaksi`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_transaksi` VARCHAR(30) NOT NULL,
  `id_pemesan`     INT UNSIGNED NOT NULL,
  `id_tiket`       INT UNSIGNED NOT NULL,
  `jumlah`         INT UNSIGNED NOT NULL,
  `harga_satuan`   DECIMAL(12,2) NOT NULL,
  `total_harga`    DECIMAL(14,2) NOT NULL,
  `status`         ENUM('menunggu','lunas','batal') NOT NULL DEFAULT 'menunggu',
  `metode_bayar`   VARCHAR(30) NULL,
  `jumlah_bayar`   DECIMAL(14,2) NULL,
  `kembalian`      DECIMAL(14,2) NULL,
  `bukti_bayar`    VARCHAR(100) NULL,
  `catatan`        VARCHAR(255) NULL,
  `tanggal_pesan`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tanggal_bayar`  DATETIME NULL,
  PRIMARY KEY (`id_transaksi`),
  UNIQUE KEY `uk_transaksi_kode` (`kode_transaksi`),
  KEY `idx_trx_pemesan` (`id_pemesan`),
  KEY `idx_trx_tiket` (`id_tiket`),
  KEY `idx_trx_status` (`status`),
  CONSTRAINT `fk_trx_pemesan` FOREIGN KEY (`id_pemesan`) REFERENCES `pemesan` (`id_pemesan`)   ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_trx_tiket`   FOREIGN KEY (`id_tiket`)   REFERENCES `jenis_tiket` (`id_tiket`) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `chk_trx_jumlah` CHECK (`jumlah` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================================
--  DATA AWAL
-- =====================================================================
INSERT INTO `kategori` (`id_kategori`, `nama_kategori`, `keterangan`) VALUES
(1, 'Wisata Alam',      'Objek wisata alam dan pemandangan'),
(2, 'Festival Budaya',  'Festival adat, tradisi, dan budaya daerah'),
(3, 'Pertunjukan Seni', 'Teater, tari, dan pertunjukan seni lainnya'),
(4, 'Konser Musik',     'Konser dan pentas musik lokal'),
(5, 'Pameran',          'Pameran seni, UMKM, dan produk lokal');

INSERT INTO `lokasi` (`id_lokasi`, `nama_lokasi`, `alamat`, `kota`, `kapasitas`) VALUES
(1, 'Taman Wisata Pelangi',   'Jl. Raya Pelangi No. 1',         'Bandung',    500),
(2, 'Alun-Alun Kota',         'Jl. Merdeka No. 10',             'Cirebon',   2000),
(3, 'Gedung Kesenian Rumentang', 'Jl. Baranangsiang No. 1',     'Bandung',    300),
(4, 'Lapangan Gasibu',        'Jl. Diponegoro',                 'Bandung',   5000),
(5, 'Galeri Nusantara',       'Jl. Braga No. 45',               'Bandung',    200);

INSERT INTO `event` (`id_event`, `nama_event`, `id_kategori`, `id_lokasi`, `tanggal_mulai`, `tanggal_selesai`, `jam_mulai`, `deskripsi`, `status`) VALUES
(1, 'Wisata Kebun Bunga Pelangi',   1, 1, DATE_SUB(CURDATE(), INTERVAL 30 DAY), DATE_ADD(CURDATE(), INTERVAL 60 DAY), '08:00:00', 'Tiket masuk taman bunga dengan spot foto dan wahana keluarga.', 'aktif'),
(2, 'Festival Budaya Nusantara',    2, 2, DATE_ADD(CURDATE(), INTERVAL 14 DAY), DATE_ADD(CURDATE(), INTERVAL 16 DAY), '09:00:00', 'Tiga hari festival budaya: tari tradisional, kuliner, dan kerajinan.', 'aktif'),
(3, 'Pentas Teater Legenda Sangkuriang', 3, 3, DATE_ADD(CURDATE(), INTERVAL 7 DAY), DATE_ADD(CURDATE(), INTERVAL 7 DAY), '19:30:00', 'Pementasan teater kolosal legenda Sangkuriang.', 'aktif'),
(4, 'Konser Musisi Lokal Bersama',  4, 4, DATE_ADD(CURDATE(), INTERVAL 30 DAY), DATE_ADD(CURDATE(), INTERVAL 30 DAY), '16:00:00', 'Konser musik menampilkan band dan solois lokal.', 'aktif'),
(5, 'Pameran UMKM & Kriya',         5, 5, DATE_ADD(CURDATE(), INTERVAL 20 DAY), DATE_ADD(CURDATE(), INTERVAL 24 DAY), '10:00:00', 'Pameran produk UMKM dan kerajinan tangan lokal.', 'aktif'),
(6, 'Festival Jajanan Tempo Dulu',  2, 2, DATE_SUB(CURDATE(), INTERVAL 20 DAY), DATE_SUB(CURDATE(), INTERVAL 18 DAY), '10:00:00', 'Festival kuliner tradisional (sudah selesai).', 'aktif');

INSERT INTO `jenis_tiket` (`id_tiket`, `id_event`, `nama_tiket`, `harga`, `kuota`, `terjual`) VALUES
(1,  1, 'Dewasa',          35000, 300, 0),
(2,  1, 'Anak-anak',       20000, 150, 0),
(3,  2, 'Reguler',         25000, 500, 0),
(4,  2, 'VIP',            100000,  50, 0),
(5,  3, 'Ekonomi',         50000, 100, 0),
(6,  3, 'VIP',            150000,  30, 0),
(7,  4, 'Festival',        75000, 100, 0),
(8,  4, 'VIP',            200000,  40, 0),
(9,  5, 'Masuk Umum',      10000, 200, 0),
(10, 6, 'Reguler',         15000, 200, 0);

INSERT INTO `pemesan` (`id_pemesan`, `nama`, `email`, `no_hp`, `alamat`) VALUES
(1, 'Budi Santoso',   'budi@example.com',  '081234567801', 'Jl. Melati No. 5, Bandung'),
(2, 'Siti Aminah',    'siti@example.com',  '081234567802', 'Jl. Kenanga No. 12, Cimahi'),
(3, 'Andi Wijaya',    'andi@example.com',  '081234567803', 'Jl. Mawar No. 7, Bandung'),
(4, 'Dewi Lestari',   'dewi@example.com',  '081234567804', 'Jl. Anggrek No. 3, Cirebon'),
(5, 'Rudi Hartono',   'rudi@example.com',  '081234567805', 'Jl. Dahlia No. 9, Garut'),
(6, 'Pembeli Demo',   'pembeli@example.com', '081234567806', 'Jl. Contoh No. 1, Bandung');

INSERT INTO `users` (`nama`, `username`, `password`, `role`, `id_pemesan`) VALUES
('Administrator', 'admin',   '$2y$10$S8.nSkhdg/0e6rCaZ4OAaeKjFekbDCMmQfLasfNzgC8p8TYwirLhS', 'admin',   NULL),
('Pembeli Demo',  'pembeli', '$2y$10$DmMQkGkoYbnGUoDhSovu5O.4pX90fIuQ9EzzPDQcqN.gZDrzycXO2', 'pembeli', 6);

INSERT INTO `transaksi` (`kode_transaksi`, `id_pemesan`, `id_tiket`, `jumlah`, `harga_satuan`, `total_harga`, `status`, `catatan`, `tanggal_pesan`, `tanggal_bayar`) VALUES
('TRX-DEMO-0001', 1, 1, 4, 35000,  140000, 'lunas',    NULL,                 DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY)),
('TRX-DEMO-0002', 2, 2, 2, 20000,   40000, 'lunas',    NULL,                 DATE_SUB(NOW(), INTERVAL 9 DAY),  DATE_SUB(NOW(), INTERVAL 9 DAY)),
('TRX-DEMO-0003', 3, 3, 10, 25000, 250000, 'lunas',    'Rombongan kantor',   DATE_SUB(NOW(), INTERVAL 5 DAY),  DATE_SUB(NOW(), INTERVAL 5 DAY)),
('TRX-DEMO-0004', 4, 4, 2, 100000, 200000, 'menunggu', NULL,                 DATE_SUB(NOW(), INTERVAL 2 DAY),  NULL),
('TRX-DEMO-0005', 5, 5, 3, 50000,  150000, 'lunas',    NULL,                 DATE_SUB(NOW(), INTERVAL 3 DAY),  DATE_SUB(NOW(), INTERVAL 3 DAY)),
('TRX-DEMO-0006', 1, 7, 5, 75000,  375000, 'menunggu', NULL,                 DATE_SUB(NOW(), INTERVAL 1 DAY),  NULL),
('TRX-DEMO-0007', 2, 6, 1, 150000, 150000, 'batal',    'Dibatalkan pemesan', DATE_SUB(NOW(), INTERVAL 4 DAY),  NULL),
('TRX-DEMO-0008', 3, 10, 20, 15000, 300000, 'lunas',   NULL,                 DATE_SUB(NOW(), INTERVAL 21 DAY), DATE_SUB(NOW(), INTERVAL 21 DAY));

-- Sinkronkan jumlah terjual (transaksi batal tidak dihitung)
UPDATE `jenis_tiket` jt
SET jt.terjual = (SELECT COALESCE(SUM(t.jumlah), 0) FROM `transaksi` t WHERE t.id_tiket = jt.id_tiket AND t.status <> 'batal');
