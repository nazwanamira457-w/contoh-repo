-- Upgrade untuk database lama (tanpa menghapus data). Jalankan sekali di phpMyAdmin tab SQL,
-- setelah memilih database db_tiketing.
ALTER TABLE `users` ADD COLUMN `role` ENUM('admin','pembeli') NOT NULL DEFAULT 'pembeli' AFTER `password`;
ALTER TABLE `users` ADD COLUMN `id_pemesan` INT UNSIGNED NULL AFTER `role`;
ALTER TABLE `users` ADD KEY `idx_users_pemesan` (`id_pemesan`);
ALTER TABLE `users` ADD CONSTRAINT `fk_users_pemesan` FOREIGN KEY (`id_pemesan`) REFERENCES `pemesan` (`id_pemesan`) ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE `transaksi` ADD COLUMN `metode_bayar` VARCHAR(30) NULL AFTER `status`;
UPDATE `users` SET `role` = 'admin' WHERE `username` = 'admin';
INSERT INTO `pemesan` (`nama`, `email`, `no_hp`, `alamat`) VALUES ('Pembeli Demo', 'pembeli@example.com', '081234567806', 'Jl. Contoh No. 1, Bandung');
INSERT INTO `users` (`nama`, `username`, `password`, `role`, `id_pemesan`)
VALUES ('Pembeli Demo', 'pembeli', '$2y$10$DmMQkGkoYbnGUoDhSovu5O.4pX90fIuQ9EzzPDQcqN.gZDrzycXO2', 'pembeli', LAST_INSERT_ID());
