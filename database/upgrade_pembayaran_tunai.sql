-- Upgrade: uang diterima + kembalian (tunai) dan bukti pembayaran (non-tunai).
-- Jika Anda sudah menjalankan versi sebelumnya yang hanya berisi jumlah_bayar dan kembalian,
-- jalankan hanya: ALTER TABLE `transaksi` ADD COLUMN `bukti_bayar` VARCHAR(100) NULL AFTER `kembalian`;
-- Jalankan sekali di phpMyAdmin (pilih database db_tiketing, tab SQL / Import).
ALTER TABLE `transaksi`
  ADD COLUMN `jumlah_bayar` DECIMAL(14,2) NULL AFTER `metode_bayar`,
  ADD COLUMN `kembalian`    DECIMAL(14,2) NULL AFTER `jumlah_bayar`,
  ADD COLUMN `bukti_bayar`  VARCHAR(100) NULL AFTER `kembalian`;

-- Transaksi lunas yang lama dianggap dibayar pas.
UPDATE `transaksi` SET `jumlah_bayar` = `total_harga`, `kembalian` = 0 WHERE `status` = 'lunas' AND `jumlah_bayar` IS NULL;
