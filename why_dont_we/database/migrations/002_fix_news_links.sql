-- ============================================================================
-- 002 — link di isi berita (news.content) mengarah ke halaman .php
-- Artikel sekarang ditampilkan oleh src/article/article.php, jadi link dibuat
-- relatif dari folder src/article/. Aman dijalankan lebih dari sekali.
-- ============================================================================

USE wdw_db;

UPDATE news SET content = REPLACE(content, 'href="tour.html"',  'href="../tour/tour.php"');
UPDATE news SET content = REPLACE(content, 'href="merch.html"', 'href="../merch/merch.php"');
