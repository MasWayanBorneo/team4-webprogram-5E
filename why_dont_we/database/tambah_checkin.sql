-- ============================================================================
-- TAMBAHAN: kolom ticket_orders.checked_in_at untuk check-in tiket di pintu masuk
-- Untuk database yang SUDAH dipakai (TinkerHost / XAMPP) — hanya menambah satu
-- kolom kosong, tidak mengubah atau menghapus data pesanan yang sudah ada.
--
-- Cara: phpMyAdmin → pilih database → tab Import → pilih file ini → Go.
-- Cukup dijalankan SEKALI. Kalau dijalankan lagi akan muncul error
-- "Duplicate column name 'checked_in_at'" — artinya kolomnya sudah ada, tidak apa-apa.
-- (Database baru cukup import why_dont_we.sql; kolom ini sudah ada di sana.)
-- ============================================================================

ALTER TABLE ticket_orders
  ADD COLUMN checked_in_at DATETIME NULL AFTER status;
