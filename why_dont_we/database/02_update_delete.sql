-- ============================================================================
-- tahap 2: UPDATE dan DELETE
-- Database: why_dont_we  (jalankan setelah 02_create_read.sql)
-- Yang diubah/dihapus hanya baris demo dari tahap 1.
-- ============================================================================

-- ================================ UPDATE ====================================

-- ---- Musik -----------------------------------------------------------------
UPDATE albums
SET title = 'Demo Album (Updated)', type = 'album', release_year = 2027
WHERE title = 'Demo Album';

UPDATE songs
SET title = 'Demo Song (Updated)', duration_sec = 215, is_featured = TRUE
WHERE title = 'Demo Song';

UPDATE videos
SET title = 'Demo Video (Updated)', start_second = 30
WHERE youtube_id = 'DEMO0000001';

-- ---- Personel --------------------------------------------------------------
UPDATE members
SET full_name = 'Demo Member (Updated)', roles = 'Vocals · Drums', hometown = 'Singkawang'
WHERE slug = 'demo';

UPDATE member_trivia
SET content = 'Trivia demo sudah diperbarui.'
WHERE content = 'Trivia demo untuk member demo.';

UPDATE limelight_members
SET full_name = 'Fan Demo (Updated)', date_of_birth = '2001-01-01'
WHERE email = 'fan.demo@example.com';

UPDATE limelight_benefits
SET title = 'Demo Benefit (Updated)', is_active = FALSE
WHERE title = 'Demo Benefit';

-- ---- Berita ----------------------------------------------------------------
UPDATE news_categories
SET name = 'Demo Updated'
WHERE name = 'Demo';

UPDATE news
SET title = 'Berita Demo (Updated)', is_featured = TRUE, read_time_min = 5
WHERE slug = 'berita-demo';

-- ---- Tiket konser ----------------------------------------------------------
UPDATE concerts
SET city = 'Singkawang', status = 'sold_out'
WHERE venue_name = 'Demo Arena';

UPDATE ticket_categories
SET price = 90.00, quota = 150
WHERE name = 'Demo Regular';

UPDATE ticket_orders
SET status = 'paid', quantity = 3, total = unit_price * 3
WHERE invoice_no = 'WDW-9001';

-- ---- Lain-lain -------------------------------------------------------------
UPDATE products
SET name = 'Demo Product (Updated)', price = 120.00, is_active = FALSE
WHERE name = 'Demo Product';

UPDATE subscribers
SET source = 'tour'
WHERE email = 'subscriber.demo@example.com';

-- ---- Admin -----------------------------------------------------------------
UPDATE admins
SET email = 'admin.updated@example.com'
WHERE username = 'demo_admin';

-- Cek hasil update (opsional)
SELECT * FROM ticket_orders WHERE invoice_no = 'WDW-9001';
SELECT * FROM members WHERE slug = 'demo';


-- ================================ DELETE ====================================
-- Urutan: tabel anak dulu, baru tabel induk, supaya tidak melanggar foreign key.
-- (ticket_categories -> concerts, ticket_orders -> ticket_categories, dst.)

-- ---- Tiket konser ----------------------------------------------------------
DELETE FROM ticket_orders
WHERE invoice_no = 'WDW-9001';

DELETE FROM ticket_categories
WHERE name = 'Demo Regular';

DELETE FROM concerts
WHERE venue_name = 'Demo Arena';

-- ---- Berita ----------------------------------------------------------------
DELETE FROM news
WHERE slug = 'berita-demo';

DELETE FROM news_categories
WHERE name = 'Demo Updated';

-- ---- Personel --------------------------------------------------------------
DELETE FROM limelight_members
WHERE email = 'fan.demo@example.com';

DELETE FROM member_trivia
WHERE content = 'Trivia demo sudah diperbarui.';

DELETE FROM members
WHERE slug = 'demo';

DELETE FROM limelight_benefits
WHERE title = 'Demo Benefit (Updated)';

-- ---- Musik -----------------------------------------------------------------
DELETE FROM songs
WHERE title = 'Demo Song (Updated)';

DELETE FROM albums
WHERE title = 'Demo Album (Updated)';

DELETE FROM videos
WHERE youtube_id = 'DEMO0000001';

-- ---- Lain-lain -------------------------------------------------------------
DELETE FROM products
WHERE name = 'Demo Product (Updated)';

DELETE FROM subscribers
WHERE email = 'subscriber.demo@example.com';

-- ---- Admin -----------------------------------------------------------------
DELETE FROM admins
WHERE username = 'demo_admin';

-- Cek hasil delete (opsional): semua harus kosong
SELECT * FROM members WHERE slug = 'demo';
SELECT * FROM ticket_orders WHERE invoice_no = 'WDW-9001';
SELECT * FROM admins WHERE username = 'demo_admin';
