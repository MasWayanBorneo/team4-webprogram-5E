-- ============================================================================
-- TAMBAHAN: tabel limelight_benefits
-- Untuk database yang SUDAH dipakai (TinkerHost / XAMPP) — hanya menambah tabel
-- baru, tidak menghapus tabel atau data lain. Aman dijalankan lebih dari sekali.
--
-- Cara: phpMyAdmin → pilih database → tab Import → pilih file ini → Go.
-- (Database baru cukup import why_dont_we.sql; tabel ini sudah ada di sana.)
-- ============================================================================

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS limelight_benefits (
  id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  icon          VARCHAR(50)   NOT NULL,             -- nama ikon Font Awesome, mis. fa-ticket
  title         VARCHAR(100)  NOT NULL,
  description   VARCHAR(255)  NOT NULL,
  sort_order    TINYINT UNSIGNED NOT NULL DEFAULT 0, -- urutan tampil
  is_active     BOOLEAN       NOT NULL DEFAULT TRUE, -- FALSE = disembunyikan tanpa dihapus
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- INSERT IGNORE: baris dengan id yang sudah ada dilewati, jadi tidak dobel
INSERT IGNORE INTO limelight_benefits (id, icon, title, description, sort_order) VALUES
  (1, 'fa-ticket', 'Early Ticket Access', 'Pre-sale codes and priority booking before tickets go public.', 1),
  (2, 'fa-video', 'Exclusive Content', 'Behind-the-scenes footage, unreleased clips, and livestreams.', 2),
  (3, 'fa-bag-shopping', 'Merch Discounts', 'Members-only drops and discount codes on official merch.', 3),
  (4, 'fa-comments', 'Direct Connection', 'Monthly Q&As and video calls with Daniel, Corbyn, Jack, Jonah, and Zach.', 4),
  (5, 'fa-cake-candles', 'Birthday Shoutouts', 'A little something special from the band on your big day.', 5),
  (6, 'fa-star', 'Meet & Greet Priority', 'First in line for meet-and-greets and VIP experiences at shows.', 6);
