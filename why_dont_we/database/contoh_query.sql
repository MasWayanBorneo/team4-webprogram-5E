-- ============================================================================
-- CONTOH QUERY UNTUK PRESENTASI (pengganti halaman admin)
-- Jalankan per bagian di phpMyAdmin → pilih database → tab SQL → Go.
-- Setiap bagian ditulis lengkap, jadi bisa dijalankan sendiri-sendiri.
-- Setelah dijalankan, buka halaman yang disebut di "CEK:" untuk melihat hasilnya.
--
-- Catatan: LAST_INSERT_ID() = id dari baris yang BARU SAJA dibuat. Karena itu
-- bagian yang memakainya harus dijalankan sekaligus dalam satu kali klik Go.
-- ============================================================================


-- ============================================================================
-- 1. KONSER + KATEGORI TIKET (halaman Tour, Home, form tiket)
-- ============================================================================

-- 1a. Konser yang sudah dijual (tombol "Get Ticket")
INSERT INTO concerts (venue_name, city, country, region, event_date, image, status)
VALUES ('Singapore Indoor Stadium', 'Singapore', 'Singapore', 'asia',
        '2027-04-10 20:00:00', 'assets/image/why-dont-we-just.jpg', 'available');
SET @konser = LAST_INSERT_ID();
INSERT INTO ticket_categories (concert_id, name, description, price, quota) VALUES
  (@konser, 'Regular', 'Standing area access, official merch discount voucher.', 60.00, 800),
  (@konser, 'VIP', 'Front-row standing area, exclusive merch pack, meet & greet pass.', 120.00, 150);
-- CEK: halaman Tour (filter Asia), Home bagian On Tour, lalu klik Get Ticket

-- 1b. Konser pre-order (tombol "Pre Order", pesanan tersimpan 'pending')
INSERT INTO concerts (venue_name, city, country, region, event_date, image, status)
VALUES ('The O2 Arena', 'London', 'United Kingdom', 'europe',
        '2027-06-05 19:30:00', 'assets/image/why-dont-we-just.jpg', 'coming_soon');
SET @konser = LAST_INSERT_ID();
INSERT INTO ticket_categories (concert_id, name, description, price, quota) VALUES
  (@konser, 'Regular', 'Standing area access.', 70.00, 1000),
  (@konser, 'VIP', 'Front-row area and meet & greet pass.', 150.00, 200);
-- CEK: halaman Tour (filter Europe) → label kuning Coming Soon + tombol Pre Order

-- 1c. Ubah status konser
UPDATE concerts SET status = 'available' WHERE venue_name = 'The O2 Arena';  -- pre-order → mulai dijual
UPDATE concerts SET status = 'sold_out'  WHERE venue_name = 'Singapore Indoor Stadium'; -- habis
-- CEK: label di halaman Tour berubah; konser sold out hilang dari Home


-- ============================================================================
-- 2. PESANAN TIKET (biasanya dari form tiket; ini untuk konfirmasi pre-order)
-- ============================================================================

-- 2a. Lihat pre-order yang menunggu konfirmasi
SELECT o.invoice_no, o.full_name, o.quantity, o.total, k.venue_name
FROM ticket_orders o
JOIN ticket_categories c ON c.id = o.ticket_category_id
JOIN concerts k ON k.id = c.concert_id
WHERE o.status = 'pending';

-- 2b. Konfirmasi pre-order → tiket dengan sobekan terbit di PDF invoice
UPDATE ticket_orders SET status = 'paid' WHERE invoice_no = 'WDW-XXXXXXXX';   -- ganti dengan nomor invoice

-- 2c. Batalkan pesanan → kuotanya kembali, invoice berlabel Cancelled
UPDATE ticket_orders SET status = 'cancelled' WHERE invoice_no = 'WDW-XXXXXXXX';

-- 2d. Laporan penjualan per konser & kategori (tanpa pesanan batal)
SELECT k.venue_name, c.name AS kategori, c.quota,
       COALESCE(SUM(o.quantity), 0) AS terjual,
       c.quota - COALESCE(SUM(o.quantity), 0) AS sisa,
       COALESCE(SUM(o.total), 0) AS pendapatan
FROM ticket_categories c
JOIN concerts k ON k.id = c.concert_id
LEFT JOIN ticket_orders o ON o.ticket_category_id = c.id AND o.status <> 'cancelled'
GROUP BY c.id
ORDER BY k.event_date, c.price;


-- ============================================================================
-- 3. BERITA (halaman News, Home bagian Latest News, artikel)
-- ============================================================================

-- 3a. Kategori berita baru
INSERT INTO news_categories (name) VALUES ('Behind the Scenes');

-- 3b. Berita baru (slug = alamat artikel: article.php?slug=rehearsal-week-diary)
INSERT INTO news (category_id, slug, title, excerpt, content, thumbnail, read_time_min, is_featured, published_at)
VALUES (
  (SELECT id FROM news_categories WHERE name = 'Behind the Scenes'),
  'rehearsal-week-diary',
  'Rehearsal Week Diary',
  'A look inside the band''s final week of rehearsals before the world tour kicks off.',
  '<p>The band spent seven days in a Los Angeles rehearsal space getting every song tour-ready.</p>
<h2>Day One</h2>
<p>Setlist planning, new arrangements, and a lot of coffee.</p>
<blockquote>"Rehearsals are where the show really comes together." — Daniel Seavey</blockquote>',
  'assets/image/hdylsthumb-5_0.jpg',
  3,
  0,
  NOW()
);
-- CEK: halaman News (paling bawah) dan Home bagian Latest News (paling depan karena terbaru)
--      lalu buka article.php?slug=rehearsal-week-diary
-- Catatan: tanda ' di dalam teks ditulis dua kali ('') — contoh: band''s


-- ============================================================================
-- 4. PERSONEL & TRIVIA (halaman About dan halaman personel)
-- ============================================================================

-- 4a. Tambah trivia untuk personel yang sudah ada
INSERT INTO member_trivia (member_id, content, sort_order)
VALUES ((SELECT id FROM members WHERE slug = 'jonah'), 'Loves playing video games on tour.', 4);
-- CEK: member.php?m=jonah → trivia bertambah di urutan ke-4

-- 4b. Ubah urutan personel di halaman About
UPDATE members SET sort_order = 0 WHERE slug = 'zach';   -- Zach jadi paling depan
-- CEK: halaman About dan Home bagian The Band

-- 4c. Contoh menambah personel baru (personel fiktif, hanya untuk demo)
INSERT INTO members (slug, full_name, roles, birth_date, zodiac, hometown,
                     extra_label, extra_value, bio, photo, hero_photo, sort_order)
VALUES ('alex', 'Alex Demo', 'Vocals · Drums', '2000-08-15', 'Leo', 'Seattle, Washington',
        'Instruments', 'Drums, Bass', 'Alex is a demo member added for the presentation.',
        'assets/image/zach.jpg', NULL, 6);
-- CEK: halaman About (kartu ke-6) dan member.php?m=alex
-- Hapus lagi setelah demo (trivia-nya ikut terhapus otomatis — ON DELETE CASCADE):
-- DELETE FROM members WHERE slug = 'alex';


-- ============================================================================
-- 5. BENEFIT LIMELIGHT (halaman About, bagian Limelight Membership)
-- ============================================================================

-- 5a. Benefit baru (icon = nama ikon Font Awesome, lihat fontawesome.com)
INSERT INTO limelight_benefits (icon, title, description, sort_order)
VALUES ('fa-gift', 'Welcome Gift', 'A digital welcome pack with exclusive wallpapers when you join.', 7);
-- CEK: halaman About → kartu benefit ke-7

-- 5b. Sembunyikan benefit tanpa menghapus
UPDATE limelight_benefits SET is_active = 0 WHERE title = 'Welcome Gift';


-- ============================================================================
-- 6. PRODUK MERCH (halaman Merch, Home bagian Merch)
-- ============================================================================

-- 6a. Produk baru
INSERT INTO products (name, collection, price, image, is_featured, is_active)
VALUES ('Tour Hoodie 2027', 'World Tour Collection', 75.00, 'assets/image/hoodie.png', 1, 1);
-- CEK: halaman Merch (kartu besar karena is_featured = 1) dan Home bagian Merch

-- 6b. Ubah harga / nonaktifkan produk
UPDATE products SET price = 55.00 WHERE name = 'Socks';
UPDATE products SET is_active = 0 WHERE name = 'Tour Hoodie 2027';   -- hilang dari situs


-- ============================================================================
-- 7. MUSIK (halaman Music)
-- ============================================================================

-- 7a. Album baru
INSERT INTO albums (title, type, release_year, cover_image)
VALUES ('The Good Times and The Bad Ones', 'album', 2021, 'assets/image/wdw-cover-final.jpg');
-- CEK: halaman Music bagian All albums (paling akhir)

-- 7b. Lagu populer baru, terhubung ke album di atas
INSERT INTO songs (album_id, title, duration_sec, audio_file, cover_image, lyrics,
                   spotify_url, apple_url, youtube_url, is_featured)
VALUES (
  (SELECT id FROM albums WHERE title = 'The Good Times and The Bad Ones'),
  'Slow Down', 185, 'assets/audio/wdw.mp3', 'assets/image/wdw-cover-final.jpg',
  '<p class="verse">Contoh lirik baris pertama<br>Contoh lirik baris kedua</p>',
  'https://open.spotify.com/artist/2jnIB6XdLvnJUeNTy5A0J2',
  'https://music.apple.com/us/artist/why-dont-we/1161814111',
  'https://music.youtube.com/@whydontwe8155',
  1
);
-- CEK: halaman Music bagian Top popular songs → kartu ke-5, coba tombol Show lyric

-- 7c. Video baru — ganti KODE_VIDEO dengan kode asli dari YouTube:
--     youtube.com/watch?v=dQw4w9WgXcQ → kodenya dQw4w9WgXcQ (11 karakter)
INSERT INTO videos (title, youtube_id, start_second)
VALUES ('Why Don''t We — Live Performance', 'KODE_VIDEO', 0);
-- CEK: halaman Music bagian All videos (paling akhir)
-- Catatan: youtube_id UNIQUE — video yang sama tidak bisa dimasukkan dua kali


-- ============================================================================
-- 8. DATA DARI FORM PENGUNJUNG (biasanya masuk lewat situs, bukan INSERT manual)
-- ============================================================================

-- 8a. Subscriber (sama dengan form Subscribe)
INSERT INTO subscribers (email, source) VALUES ('demo.subscriber@example.com', 'home');

-- 8b. Anggota Limelight (sama dengan form Become a Limelight)
INSERT INTO limelight_members (full_name, email, date_of_birth, favorite_member_id)
VALUES ('Demo Fan', 'demo.fan@example.com', '2004-09-21', (SELECT id FROM members WHERE slug = 'daniel'));

-- 8c. Lihat data yang masuk
SELECT email, source, subscribed_at FROM subscribers ORDER BY subscribed_at DESC;
SELECT l.full_name, l.email, m.full_name AS favorit, l.joined_at
FROM limelight_members l LEFT JOIN members m ON m.id = l.favorite_member_id
ORDER BY l.joined_at DESC;

-- 8d. Demo aturan UNIQUE: jalankan 8a sekali lagi → ERROR #1062 Duplicate entry
--     (inilah yang membuat situs menampilkan "You're already subscribed")


-- ============================================================================
-- 9. MEMBERSIHKAN DATA DEMO (jalankan setelah presentasi)
-- ============================================================================
-- DELETE FROM subscribers       WHERE email = 'demo.subscriber@example.com';
-- DELETE FROM limelight_members WHERE email = 'demo.fan@example.com';
-- DELETE FROM member_trivia     WHERE content = 'Loves playing video games on tour.';
-- DELETE FROM members           WHERE slug  = 'alex';
-- UPDATE members  SET sort_order = 5     WHERE slug = 'zach';    -- kembalikan urutan Zach
-- UPDATE products SET price      = 65.00 WHERE name = 'Socks';   -- kembalikan harga Socks
-- DELETE FROM limelight_benefits WHERE title = 'Welcome Gift';
-- DELETE FROM products          WHERE name  = 'Tour Hoodie 2027';
-- DELETE FROM videos            WHERE youtube_id = 'KODE_VIDEO';   -- sesuaikan dengan kode yang dipakai
-- DELETE FROM songs             WHERE title = 'Slow Down';
-- DELETE FROM albums            WHERE title = 'The Good Times and The Bad Ones';
-- DELETE FROM news              WHERE slug  = 'rehearsal-week-diary';
-- DELETE FROM news_categories   WHERE name  = 'Behind the Scenes';
-- Konser: hapus kategori tiket dulu, baru konsernya (aturan foreign key)
-- DELETE FROM ticket_categories WHERE concert_id IN (SELECT id FROM concerts WHERE venue_name IN ('Singapore Indoor Stadium', 'The O2 Arena'));
-- DELETE FROM concerts          WHERE venue_name IN ('Singapore Indoor Stadium', 'The O2 Arena');
