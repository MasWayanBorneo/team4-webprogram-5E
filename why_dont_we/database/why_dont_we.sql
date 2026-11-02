-- ============================================================================
-- WHY DON'T WE — database situs
-- Sesuai rancangan di dbdiagram.io (tanpa tabel admins).
--
-- Cara import (phpMyAdmin, XAMPP atau TinkerHost):
--   1. Pilih / buat database dulu (di TinkerHost database dibuat lewat panel).
--   2. Tab Import → pilih file ini → Go.
--
-- PERHATIAN: file ini menghapus tabel dengan nama yang sama sebelum membuatnya
-- lagi, termasuk tabel `products` lama dari merch.sql. Data di tabel-tabel
-- itu akan hilang.
-- ============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS ticket_orders, ticket_categories, concerts,
                     limelight_members, member_trivia, members,
                     news, news_categories,
                     songs, albums, videos,
                     products, subscribers;

SET FOREIGN_KEY_CHECKS = 1;

-- ---- Musik -----------------------------------------------------------------

CREATE TABLE albums (
  id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  title         VARCHAR(150)  NOT NULL,
  type          ENUM('album','ep','single') NOT NULL,
  release_year  YEAR          NULL,
  cover_image   VARCHAR(255)  NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE songs (
  id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  album_id      INT UNSIGNED  NULL,                 -- kosong = single tanpa album
  title         VARCHAR(150)  NOT NULL,
  duration_sec  SMALLINT UNSIGNED NULL,
  audio_file    VARCHAR(255)  NULL,
  cover_image   VARCHAR(255)  NULL,
  lyrics        TEXT          NULL,
  spotify_url   VARCHAR(255)  NULL,                 -- link lagu ini di tiap platform
  apple_url     VARCHAR(255)  NULL,
  youtube_url   VARCHAR(255)  NULL,
  is_featured   BOOLEAN       NOT NULL DEFAULT FALSE,
  PRIMARY KEY (id),
  CONSTRAINT fk_songs_album FOREIGN KEY (album_id) REFERENCES albums (id)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE videos (
  id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  title         VARCHAR(150)  NOT NULL,
  youtube_id    VARCHAR(20)   NOT NULL,             -- kode video, mis. dQw4w9WgXcQ
  start_second  SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uq_videos_youtube_id (youtube_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---- Personel --------------------------------------------------------------

CREATE TABLE members (
  id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  slug          VARCHAR(50)   NOT NULL,             -- nama pendek untuk URL: jonah, zach, ...
  full_name     VARCHAR(100)  NOT NULL,
  roles         VARCHAR(100)  NOT NULL,
  birth_date    DATE          NULL,
  zodiac        VARCHAR(20)   NULL,
  hometown      VARCHAR(100)  NULL,
  extra_label   VARCHAR(50)   NULL,                 -- judul fakta ke-4: Sports, Position, Instruments, Skills
  extra_value   VARCHAR(150)  NULL,                 -- isi fakta ke-4
  bio           TEXT          NULL,
  photo         VARCHAR(255)  NULL,
  hero_photo    VARCHAR(255)  NULL,
  sort_order    TINYINT UNSIGNED NOT NULL DEFAULT 0, -- urutan tampil di halaman About
  PRIMARY KEY (id),
  UNIQUE KEY uq_members_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE member_trivia (
  id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  member_id     INT UNSIGNED  NOT NULL,
  content       VARCHAR(255)  NOT NULL,
  sort_order    TINYINT UNSIGNED NOT NULL DEFAULT 0, -- urutan trivia di halaman personel
  PRIMARY KEY (id),
  CONSTRAINT fk_trivia_member FOREIGN KEY (member_id) REFERENCES members (id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE limelight_members (
  id                  INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  full_name           VARCHAR(100)  NOT NULL,
  email               VARCHAR(100)  NOT NULL,
  date_of_birth       DATE          NOT NULL,
  favorite_member_id  INT UNSIGNED  NULL,
  joined_at           TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_limelight_email (email),
  CONSTRAINT fk_limelight_member FOREIGN KEY (favorite_member_id) REFERENCES members (id)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---- Berita ----------------------------------------------------------------

CREATE TABLE news_categories (
  id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  name          VARCHAR(50)   NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_news_categories_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE news (
  id             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  category_id    INT UNSIGNED  NOT NULL,
  slug           VARCHAR(150)  NOT NULL,
  title          VARCHAR(200)  NOT NULL,
  excerpt        VARCHAR(300)  NULL,                -- ringkasan 1–2 kalimat untuk kartu berita
  content        MEDIUMTEXT    NOT NULL,            -- isi artikel dalam HTML
  thumbnail      VARCHAR(255)  NULL,
  read_time_min  TINYINT UNSIGNED NULL,
  is_featured    BOOLEAN       NOT NULL DEFAULT FALSE,
  published_at   DATETIME      NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_news_slug (slug),
  CONSTRAINT fk_news_category FOREIGN KEY (category_id) REFERENCES news_categories (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---- Tiket konser ----------------------------------------------------------

CREATE TABLE concerts (
  id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  venue_name    VARCHAR(150)  NOT NULL,
  city          VARCHAR(100)  NOT NULL,
  country       VARCHAR(100)  NOT NULL,
  region        ENUM('asia','america','europe','other') NOT NULL,  -- tombol filter di halaman Tour
  event_date    DATETIME      NOT NULL,
  image         VARCHAR(255)  NULL,
  status        ENUM('available','coming_soon','sold_out') NOT NULL DEFAULT 'available',
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ticket_categories (
  id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  concert_id    INT UNSIGNED  NOT NULL,
  name          VARCHAR(50)   NOT NULL,             -- Regular, VIP
  description   VARCHAR(255)  NULL,
  price         DECIMAL(12,2) NOT NULL,             -- harga yang berlaku sekarang
  quota         INT UNSIGNED  NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_ticket_categories_concert FOREIGN KEY (concert_id) REFERENCES concerts (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ticket_orders (
  id                  INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  invoice_no          VARCHAR(30)   NOT NULL,       -- format WDW-XXXX
  ticket_category_id  INT UNSIGNED  NOT NULL,
  full_name           VARCHAR(100)  NOT NULL,
  email               VARCHAR(100)  NOT NULL,
  phone               VARCHAR(20)   NOT NULL,
  quantity            TINYINT UNSIGNED NOT NULL,
  unit_price          DECIMAL(12,2) NOT NULL,       -- harga 1 tiket SAAT dipesan
  total               DECIMAL(12,2) NOT NULL,       -- unit_price × quantity
  status              ENUM('pending','paid','cancelled') NOT NULL DEFAULT 'pending',
  created_at          TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_ticket_orders_invoice (invoice_no),
  CONSTRAINT fk_ticket_orders_category FOREIGN KEY (ticket_category_id) REFERENCES ticket_categories (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---- Lain-lain -------------------------------------------------------------

CREATE TABLE products (
  id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  name          VARCHAR(150)  NOT NULL,
  collection    VARCHAR(100)  NULL,
  price         DECIMAL(12,2) NOT NULL,
  image         VARCHAR(255)  NULL,                 -- path relatif dari root project
  is_featured   BOOLEAN       NOT NULL DEFAULT FALSE, -- TRUE = juga tampil di kartu besar halaman Merch
  is_active     BOOLEAN       NOT NULL DEFAULT TRUE,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE subscribers (
  id             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  email          VARCHAR(100)  NOT NULL,
  source         ENUM('home','news','tour') NOT NULL, -- form subscribe yang dipakai
  subscribed_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_subscribers_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- DATA AWAL — diambil dari isi halaman situs saat ini
-- ============================================================================

INSERT INTO members (id, slug, full_name, roles, birth_date, zodiac, hometown, extra_label, extra_value, bio, photo, hero_photo, sort_order) VALUES
  (1, 'daniel', 'Daniel Seavey', 'Vocals · Guitar · Producer', '1999-04-02', 'Aries', 'Portland, Oregon', 'Instruments', 'Guitar, Cello, Piano', 'Daniel James Seavey is the main singer, songwriter, and producer of the group. He first gained public attention as a contestant on American Idol season 14 at the age of 15.', 'assets/image/daniel.jpg', 'assets/image/seavey-daniel-image.jpg', 1),
  (2, 'corbyn', 'Corbyn Besson', 'Vocals · Guitar', '1998-11-25', 'Sagittarius', 'Fairfax, Virginia', 'Instruments', 'Guitar, Piano', 'Corbyn Matthew Besson grew up in Fairfax, Virginia. Before Why Don''t We he built a following by livestreaming and posting song covers online, and in 2016 he joined the band as one of its five vocalists.', 'assets/image/corbyn.jpg', 'assets/image/besson-corbyn-image.jpg', 2),
  (3, 'jack', 'Jack Avery', 'Vocals · Guitar', '1999-07-01', 'Cancer', 'Burbank, California', 'Skills', 'Skateboarding', 'Jack Robert Avery started his career by uploading song covers on YouTube and participating in the IMPACT music tour before joining Why Don''t We.', 'assets/image/jack.jpg', 'assets/image/avery-jack-image.jpg', 3),
  (4, 'jonah', 'Jonah Marais', 'Vocals · Keys', '1998-06-16', 'Gemini', 'Stillwater, Minnesota', 'Sports', 'Baseball', 'Jonah Marais Roth Frantzich is the oldest member of the group. Like Corbyn, he also gained early popularity through the YouNow app and previously released a solo EP titled "When the Daylight''s Gone".', 'assets/image/jonah.jpg', 'assets/image/marais-jonah-image.jpg', 4),
  (5, 'zach', 'Zach Herron', 'Vocals', '2001-05-27', 'Gemini', 'Dallas, Texas', 'Position', 'Youngest Member (Maknae)', 'Zachary Dean Herron is the youngest member of Why Don''t We. He went viral on social media after a video of him singing Shawn Mendes''s "Stitches" at school spread widely.', 'assets/image/zach.jpg', 'assets/image/herron-zach-image.jpg', 5);

INSERT INTO member_trivia (member_id, content, sort_order) VALUES
  (1, 'Can play more than 20 musical instruments.', 1),
  (1, 'Huge fan of The Beatles and Ed Sheeran.', 2),
  (1, 'Has a pet dog named Kobe.', 3),
  (2, 'Built his first fanbase on YouNow and YouTube.', 1),
  (2, 'Plays guitar and piano.', 2),
  (2, 'The only member from the East Coast of the US.', 3),
  (3, 'Has a daughter named Lavender May Avery.', 1),
  (3, 'Initially more interested in basketball before focusing on music.', 2),
  (3, 'Can play the piano and guitar.', 3),
  (4, 'Huge fan of Harry Potter.', 1),
  (4, 'His father is a musician and music teacher.', 2),
  (4, 'Has a habit of drinking coffee every morning.', 3),
  (5, 'Joined the school choir before becoming famous.', 1),
  (5, 'Cites Justin Bieber and Shawn Mendes as his inspirations.', 2),
  (5, 'Released his debut solo single "Timelapse" in 2016.', 3);

INSERT INTO news_categories (id, name) VALUES
  (1, 'Announcement'),
  (2, 'Event'),
  (3, 'Interview'),
  (4, 'Music'),
  (5, 'Merch'),
  (6, 'Tour');

INSERT INTO news (id, category_id, slug, title, excerpt, content, thumbnail, read_time_min, is_featured, published_at) VALUES
  (1, 1, 'new-album-drops-this-summer-full-details', 'New Album Drops This Summer — Full Details', 'Why Don''t We is officially ready to share what they have been working on behind closed studio doors for the past eighteen months. The band confirmed today that their highly anticipated new album will drop this summer, and the details are every bit as exciting as fans had hoped.', '<p> Why Don''t We is officially ready to share what they have been working on behind closed studio doors for the past eighteen months. The band confirmed today that their highly anticipated new album will drop this summer, and the details are every bit as exciting as fans had hoped. </p>
<h2>The Album</h2>
<p> Titled <em>Unfinished Business</em>, the record is a 14-track collection that the group describes as their most personal work yet. Written and produced largely by the five members themselves, it moves between the polished pop-rock sound that put them on the map and a rawer, more acoustic-driven direction they have been quietly developing since their break. </p>
<blockquote>"We went into this one with zero expectations and just wrote what felt true. That freedom changed everything." — Jonah Marais</blockquote>
<h2>Release Date &amp; Pre-Save</h2>
<p>
<em>Unfinished Business</em> arrives on all major streaming platforms on <strong>August 22, 2026</strong>. A physical deluxe edition — featuring a 48-page booklet, two bonus tracks, and a poster — is available for pre-order now through the official store. </p>
<h2>Lead Singles</h2>
<p>The album''s lead single, <em>Come Around</em>, is already climbing the charts after its surprise release last month. A second single is scheduled to drop two weeks before the album, so keep notifications on.</p>
<p>Pre-save <em>Unfinished Business</em> on Spotify and Apple Music now, and stay tuned to the band''s socials for daily countdowns and exclusive track previews.</p>', 'assets/image/featured-news.jpg', 3, 1, '2026-05-15 00:00:00'),
  (2, 1, 'tour-dates-revealed', 'Tour Dates Revealed', 'The wait is over. Why Don''t We has officially announced their 2026 North American headline tour — 32 cities, three months, and what promises to be the biggest production the band has ever brought to the road.', '<p> The wait is over. Why Don''t We has officially announced their 2026 North American headline tour — 32 cities, three months, and what promises to be the biggest production the band has ever brought to the road. </p>
<h2>Full Tour Routing</h2>
<p> The <strong>Unfinished Business Tour</strong> kicks off on <strong>September 5</strong> in Seattle, WA and wraps on <strong>November 28</strong> in Miami, FL. Major stops include New York, Los Angeles, Chicago, Dallas, and Toronto. </p>
<blockquote> "We have been dying to get back out there. This tour is going to feel like one long reunion with everyone who has stuck with us." — Jack Avery </blockquote>
<h2>Tickets</h2>
<p> Fan club pre-sale opens <strong>May 20 at 10 AM local time</strong>. General on-sale follows on <strong>May 22</strong> via all major ticketing platforms. VIP packages — including soundcheck access, a meet-and-greet, and exclusive merchandise — are available in limited quantities. </p>
<h2>Support Acts</h2>
<p> Support acts will be announced on a city-by-city basis over the coming weeks. Follow the band''s Instagram and Twitter for the latest updates as each date is confirmed. </p>
<p> Full dates and venue listings are available on the <a href="../tour/tour.php" style="color:rgba(160,220,255,.9);">Tour page</a>. Don''t sleep on tickets — several markets sold out within hours during the last run. </p>', 'assets/image/hdylsthumb-5_0.jpg', 3, 0, '2026-05-15 00:00:00'),
  (3, 2, 'summer-festival-appearance', 'Summer Festival Appearance', 'Why Don''t We will take the mainstage at Sunwave Festival 2026 this July, marking their first major festival headline slot since the band''s return. The annual three-day event in Austin, Texas draws over 80,000 fans and has become one of the premier pop music gatherings in the country.', '<p> Why Don''t We will take the mainstage at <strong>Sunwave Festival 2026</strong> this July, marking their first major festival headline slot since the band''s return. The annual three-day event in Austin, Texas draws over 80,000 fans and has become one of the premier pop music gatherings in the country. </p>
<h2>The Set</h2>
<p> The band promises a career-spanning set blending fan favourites with fresh material from the upcoming album <em>Unfinished Business</em>. Expect a full production — custom lighting rigs, live strings, and a few surprises the guys are keeping close to their chests. </p>
<blockquote> "Playing a festival is a different energy entirely. Fifty thousand people singing back your lyrics all at once — there is nothing like it." — Corbyn Besson </blockquote>
<h2>Festival Details</h2>
<p> Sunwave Festival runs <strong>July 18–20, 2026</strong> at Zilker Park, Austin, TX. Why Don''t We headlines the main stage on <strong>Saturday, July 19</strong> at 9 PM CT. The full lineup features over 60 artists across four stages. </p>
<h2>Tickets &amp; Travel</h2>
<p> Single-day and three-day passes are available now at the Sunwave Festival website. Camping packages and shuttle passes can also be added at checkout. The event recommends booking accommodation early, as Austin hotels fill up quickly during festival weekend. </p>', 'assets/image/jfthumbnail.jpg', 4, 0, '2026-05-18 00:00:00'),
  (4, 3, 'exclusive-studio-session', 'Exclusive Studio Session', 'We were given rare access to the Los Angeles studio where Why Don''t We has been putting the finishing touches on their new record. What we found was five friends who have grown into genuine artists — confident, collaborative, and completely in sync.', '<p> We were given rare access to the Los Angeles studio where Why Don''t We has been putting the finishing touches on their new record. What we found was five friends who have grown into genuine artists — confident, collaborative, and completely in sync. </p>
<h2>Inside the Room</h2>
<p> The session started at noon and ran past midnight. Daniel Seavey sat behind a grand piano in the corner, working through chord progressions with a notebook open beside him. Across the room Zach Herron and Jonah Marais traded verses on acoustic guitars, stopping occasionally to replay lines into a voice memo app. The vibe was relaxed — almost domestic — broken only by bursts of laughter when a harmony landed perfectly on the first try. </p>
<blockquote> "We are writing for ourselves this time. Not for a demographic or a release window — just for the five of us in this room." — Daniel Seavey </blockquote>
<h2>The Sound</h2>
<p> Producer Evan Blair, who helmed the session, described the album''s sonic palette as "open-air pop" — big choruses with space left inside them. Live drums, real bass, and analogue synths sit beneath polished vocals that the band tracked mostly together, live in the room, to capture a sense of energy that overdubs rarely replicate. </p>
<h2>What''s Next</h2>
<p> Mixing wraps this month, with mastering scheduled for early July ahead of the August release. The band will share a studio diary series on their YouTube channel leading up to the drop — subscribe now so you don''t miss an episode. </p>', 'assets/image/flower.jpg', 5, 0, '2026-05-22 00:00:00'),
  (5, 4, 'new-music-video-released', 'New Music Video Released', 'The official music video for Why Don''t We''s latest single Come Around is now live on YouTube, and it has already surpassed 4 million views in its first 48 hours — making it the fastest-viewed video in the band''s catalogue.', '<p> The official music video for Why Don''t We''s latest single <em>Come Around</em> is now live on YouTube, and it has already surpassed 4 million views in its first 48 hours — making it the fastest-viewed video in the band''s catalogue. </p>
<h2>The Concept</h2>
<p> Directed by Malia James, the video unfolds across a series of split-screen vignettes that mirror the song''s theme of parallel lives barely missing each other. Shot over two days in downtown Los Angeles and the Malibu coast, it trades the high-gloss visuals of earlier releases for a warmer, more intimate look — all golden-hour light and handheld camera work. </p>
<blockquote> "We wanted the video to feel like a memory you can''t quite hold onto. Malia captured exactly that." — Zach Herron </blockquote>
<h2>Behind the Scenes</h2>
<p> A behind-the-scenes documentary of the shoot is available exclusively on the band''s Patreon. Members get an uncut 22-minute look at how the split-screen sequences were choreographed and a director''s commentary track recorded the morning after the wrap. </p>
<h2>Watch Now</h2>
<p> The <em>Come Around</em> music video is streaming now on YouTube. Hit the like button and share it with a friend who needs to hear this song — every stream helps the album chart when it drops in August. </p>', 'assets/image/Polaroid5_0.png', 4, 0, '2026-05-25 00:00:00'),
  (6, 5, 'limited-edition-merch-drop', 'Limited Edition Merch Drop', 'To celebrate the album announcement, Why Don''t We has dropped a limited-edition merch collection exclusively through the official store — and quantities are extremely limited. If you want a piece, move fast.', '<p> To celebrate the album announcement, Why Don''t We has dropped a limited-edition merch collection exclusively through the official store — and quantities are extremely limited. If you want a piece, move fast. </p>
<h2>What''s in the Drop</h2>
<p> The collection includes a heavyweight pullover hoodie in midnight navy, a washed-cotton crewneck in faded sage, a short-sleeve graphic tee featuring original artwork by LA-based illustrator Cara Rios, and a enamel pin set. Each item is screen-printed by hand in small batches in Los Angeles. </p>
<blockquote> "We wanted merch that feels like something you actually want to wear, not just a souvenir. Cara''s artwork made that possible." — Corbyn Besson </blockquote>
<h2>Album Bundle</h2>
<p> Bundle any merch item with the deluxe physical album pre-order and save 15%. Bundles also include a signed lyric card — hand-signed by all five members — while supplies last. </p>
<h2>How to Order</h2>
<p> The collection is live now at the official store. Items ship worldwide. Orders placed before June 10 will arrive before the album release date. Head to the <a href="../merch/merch.php" style="color:rgba(160,220,255,.9);">Merch page</a> to grab yours before they sell out. </p>', 'assets/image/hoodie.png', 2, 0, '2026-05-28 00:00:00'),
  (7, 6, 'international-tour-begins', 'International Tour Begins', 'Following the sold-out North American run, Why Don''t We has confirmed an international leg that will take them to Europe, Asia, and Latin America for the first time since 2019. The global extension of the Unfinished Business Tour runs from December 2026 through March 2027.', '<p> Following the sold-out North American run, Why Don''t We has confirmed an international leg that will take them to Europe, Asia, and Latin America for the first time since 2019. The global extension of the <strong>Unfinished Business Tour</strong> runs from December 2026 through March 2027. </p>
<h2>European Dates</h2>
<p> The European leg opens on <strong>December 5 in London</strong> at the O2 Arena, followed by dates in Paris, Amsterdam, Berlin, Madrid, and Stockholm. The band will also make their first-ever appearance in Dublin and Warsaw before wrapping Europe on December 30 in Milan. </p>
<blockquote> "International fans have been waiting a long time and so have we. We cannot wait to finally be in the same room as you." — Jack Avery </blockquote>
<h2>Asia-Pacific</h2>
<p> January brings the band to the Asia-Pacific region, with confirmed shows in Tokyo, Seoul, Manila, Jakarta, Singapore, and Sydney. The Southeast Asia leg alone spans 8 cities across 3 weeks — the most comprehensive run the band has ever done in the region. </p>
<h2>Latin America</h2>
<p> The tour closes out in Latin America through February and March, hitting São Paulo, Buenos Aires, Bogotá, Mexico City, and Santiago. Fan-club pre-sale for all international dates opens <strong>June 10</strong>. General on-sale follows on <strong>June 14</strong>. </p>
<p> Full international dates are listed on the <a href="../tour/tour.php" style="color:rgba(160,220,255,.9);">Tour page</a>. This one is going to be special — do not miss your city. </p>', 'assets/image/something-different.png', 4, 0, '2026-06-01 00:00:00');

-- satu-satunya konser di halaman Tour & form tiket; jam 19:00 dan kuota hanya contoh
INSERT INTO concerts (id, venue_name, city, country, region, event_date, image, status) VALUES
  (1, 'Jakarta Convention Center (JCC)', 'Jakarta Pusat', 'Indonesia', 'asia', '2026-08-12 19:00:00', 'assets/image/why-dont-we-just.jpg', 'available');

INSERT INTO ticket_categories (id, concert_id, name, description, price, quota) VALUES
  (1, 1, 'Regular', 'Standing area access, official merch discount voucher.', 50.00, 500),
  (2, 1, 'VIP', 'Front-row standing area, exclusive merch pack, meet & greet pass.', 100.00, 100);

INSERT INTO products (id, name, collection, price, image, is_featured, is_active) VALUES
  (1, 'Bracelet', 'Covid-19 Collection', 65.0, 'assets/image/gelang.png', 1, 1),
  (2, 'Limited Edition Hoodie', 'New Era Collection', 65.0, 'assets/image/hoodie.png', 1, 1),
  (3, 'Socks', 'New Era Collection', 65.0, 'assets/image/kaoskaki.png', 0, 1),
  (4, 'Black T-Shirt', 'New Era Collection', 65.0, 'assets/image/pngtree-black-tshirt.png', 0, 1),
  (5, 'Dark Green T-Shirt', 'New Era Collection', 65.0, 'assets/image/pngtree-green-tshirt.png', 0, 1),
  (6, 'Cowboy Hat', 'New Era Collection', 65.0, 'assets/image/vecteezy_ai-generated-cowboy-hat-png_34616681.png', 0, 1),
  (7, 'Seafoam T-Shirt', 'New Era Collection', 65.0, 'assets/image/vecteezy_seafoam-green-short-sleeve-crew-neck-tshirt-mockup-for_59901009.png', 0, 1);
