-- ============================================================================
-- tahap 1: CREATE (INSERT) dan READ (SELECT)
-- Database: why_dont_we  (jalankan setelah why_dont_we.sql di-import)
-- Setiap tabel diisi 1 baris data demo, urut dari tabel induk ke tabel anak.
-- ============================================================================

-- ============================ CREATE (INSERT) ===============================

-- ---- Musik -----------------------------------------------------------------
INSERT INTO albums (title, type, release_year, cover_image)
VALUES ('Demo Album', 'ep', 2026, 'assets/image/demo-album.jpg');

INSERT INTO songs (album_id, title, duration_sec, audio_file, cover_image, lyrics,
                   spotify_url, apple_url, youtube_url, is_featured)
SELECT id, 'Demo Song', 200, 'assets/audio/demo.mp3', 'assets/image/demo-album.jpg',
       '<p class="verse">Lirik demo</p>',
       'https://open.spotify.com/demo', 'https://music.apple.com/demo',
       'https://music.youtube.com/demo', FALSE
FROM albums WHERE title = 'Demo Album';

INSERT INTO videos (title, youtube_id, start_second)
VALUES ('Demo Video', 'DEMO0000001', 0);

-- ---- Personel --------------------------------------------------------------
INSERT INTO members (slug, full_name, roles, birth_date, zodiac, hometown,
                     extra_label, extra_value, bio, photo, hero_photo, sort_order)
VALUES ('demo', 'Demo Member', 'Vocals', '2000-01-01', 'Capricorn', 'Pontianak',
        'Skills', 'Drums', 'Bio member demo.', 'assets/image/demo.jpg',
        'assets/image/demo-hero.jpg', 6);

INSERT INTO member_trivia (member_id, content, sort_order)
SELECT id, 'Trivia demo untuk member demo.', 1
FROM members WHERE slug = 'demo';

INSERT INTO limelight_members (full_name, email, date_of_birth, favorite_member_id)
SELECT 'Fan Demo', 'fan.demo@example.com', '2002-05-10', id
FROM members WHERE slug = 'demo';

INSERT INTO limelight_benefits (icon, title, description, sort_order, is_active)
VALUES ('fa-gift', 'Demo Benefit', 'Deskripsi benefit demo.', 7, TRUE);

-- ---- Berita ----------------------------------------------------------------
INSERT INTO news_categories (name) VALUES ('Demo');

INSERT INTO news (category_id, slug, title, excerpt, content, thumbnail,
                  read_time_min, is_featured, published_at)
SELECT id, 'berita-demo', 'Berita Demo', 'Ringkasan berita demo.',
       '<p>Isi berita demo.</p>', 'assets/image/demo-news.jpg',
       2, FALSE, '2026-11-10 10:00:00'
FROM news_categories WHERE name = 'Demo';

-- ---- Tiket konser ----------------------------------------------------------
INSERT INTO concerts (venue_name, city, country, region, event_date, image, status)
VALUES ('Demo Arena', 'Pontianak', 'Indonesia', 'asia',
        '2026-12-20 19:00:00', 'assets/image/demo-concert.jpg', 'available');

INSERT INTO ticket_categories (concert_id, name, description, price, quota)
SELECT id, 'Demo Regular', 'Kategori tiket demo.', 75.00, 200
FROM concerts WHERE venue_name = 'Demo Arena';

INSERT INTO ticket_orders (invoice_no, ticket_category_id, full_name, email, phone,
                           quantity, unit_price, total, status)
SELECT 'WDW-9001', tc.id, 'Pembeli Demo', 'pembeli.demo@example.com', '081234567890',
       2, tc.price, tc.price * 2, 'pending'
FROM ticket_categories tc
JOIN concerts c ON c.id = tc.concert_id
WHERE tc.name = 'Demo Regular' AND c.venue_name = 'Demo Arena';

-- ---- Lain-lain -------------------------------------------------------------
INSERT INTO products (name, collection, price, image, is_featured, is_active)
VALUES ('Demo Product', 'Demo Collection', 99.00, 'assets/image/demo-product.png', FALSE, TRUE);

INSERT INTO subscribers (email, source)
VALUES ('subscriber.demo@example.com', 'home');

-- ---- Admin -----------------------------------------------------------------
INSERT INTO admins (username, email, password_hash)
VALUES ('demo_admin', 'admin.demo@example.com',
        '$2y$10$abcdefghijklmnopqrstuuABCDEFGHIJKLMNOPQRSTUVWXYZ0123456');


-- ============================== READ (SELECT) ===============================

-- ---- Musik -----------------------------------------------------------------
SELECT * FROM albums;
SELECT id, album_id, title, duration_sec, is_featured FROM songs;
SELECT * FROM videos;

-- ---- Personel --------------------------------------------------------------
SELECT id, slug, full_name, roles, birth_date, hometown, sort_order FROM members;
SELECT * FROM member_trivia;
SELECT * FROM limelight_members;
SELECT * FROM limelight_benefits;

-- ---- Berita ----------------------------------------------------------------
SELECT * FROM news_categories;
SELECT id, category_id, slug, title, read_time_min, is_featured, published_at FROM news;

-- ---- Tiket konser ----------------------------------------------------------
SELECT * FROM concerts;
SELECT * FROM ticket_categories;
SELECT * FROM ticket_orders;

-- ---- Lain-lain -------------------------------------------------------------
SELECT * FROM products;
SELECT * FROM subscribers;

-- ---- Admin -----------------------------------------------------------------
SELECT id, username, email, created_at FROM admins;

-- ---- READ dengan JOIN (opsional, menunjukkan relasi antar tabel) -----------
SELECT s.title AS lagu, a.title AS album
FROM songs s LEFT JOIN albums a ON a.id = s.album_id;

SELECT m.full_name AS member, t.content AS trivia
FROM member_trivia t JOIN members m ON m.id = t.member_id;

SELECT n.title AS berita, c.name AS kategori
FROM news n JOIN news_categories c ON c.id = n.category_id;

SELECT o.invoice_no, o.full_name, tc.name AS kategori_tiket, c.venue_name, o.total, o.status
FROM ticket_orders o
JOIN ticket_categories tc ON tc.id = o.ticket_category_id
JOIN concerts c ON c.id = tc.concert_id;
