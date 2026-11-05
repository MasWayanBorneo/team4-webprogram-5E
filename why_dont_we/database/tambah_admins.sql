-- ============================================================================
-- TAMBAHAN: tabel admins untuk login dashboard admin
-- Untuk database yang SUDAH dipakai (TinkerHost / XAMPP) — hanya menambah tabel
-- baru, tidak menghapus tabel atau data lain. Aman dijalankan lebih dari sekali.
--
-- Cara: phpMyAdmin → pilih database → tab Import → pilih file ini → Go.
-- (Database baru cukup import why_dont_we.sql; tabel ini sudah ada di sana.)
-- ============================================================================

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS admins (
  id             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  username       VARCHAR(50)   NOT NULL,
  email          VARCHAR(100)  NOT NULL,
  password_hash  VARCHAR(255)  NOT NULL,           -- hasil password_hash(), bukan password asli
  created_at     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_admins_username (username),
  UNIQUE KEY uq_admins_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Akun admin pertama TIDAK dibuat di sini, supaya tidak ada password bawaan
-- yang tertulis di file. Akun dibuat nanti lewat halaman dashboard admin.
