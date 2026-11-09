-- Upgrade: kolom bukti pembayaran (untuk yang sudah menjalankan upgrade_pembayaran_tunai.sql sebelumnya).
ALTER TABLE `transaksi` ADD COLUMN `bukti_bayar` VARCHAR(100) NULL AFTER `kembalian`;
