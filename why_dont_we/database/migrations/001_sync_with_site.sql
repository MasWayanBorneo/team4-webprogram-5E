-- ============================================================================
-- 001 — sync wdw_db with the current website
--   1. ticket prices in USD (Regular $50, VIP $100), as on src/ticket/ticket.html
--   2. complete Corbyn's profile (4th fact card, bio, trivia), as on src/member/corbyn.html
--   3. products.is_featured for the "Featured merch" cards on src/merch/merch.html
-- Safe to run more than once.
-- ============================================================================

USE wdw_db;

-- 1. ticket prices → USD
UPDATE ticket_categories SET price = 50.00  WHERE name = 'Regular';
UPDATE ticket_categories SET price = 100.00 WHERE name = 'VIP';

-- 2. Corbyn's profile
UPDATE members
SET extra_label = 'Instruments',
    extra_value = 'Guitar, Piano',
    bio = 'Corbyn Matthew Besson grew up in Fairfax, Virginia. Before Why Don''t We he built a following by livestreaming and posting song covers online, and in 2016 he joined the band as one of its five vocalists.'
WHERE slug = 'corbyn';

DELETE t FROM member_trivia t JOIN members m ON m.id = t.member_id WHERE m.slug = 'corbyn';
INSERT INTO member_trivia (member_id, content, sort_order)
SELECT id, 'Built his first fanbase on YouNow and YouTube.', 1 FROM members WHERE slug = 'corbyn' UNION ALL
SELECT id, 'Plays guitar and piano.',                         2 FROM members WHERE slug = 'corbyn' UNION ALL
SELECT id, 'The only member from the East Coast of the US.',  3 FROM members WHERE slug = 'corbyn';

-- 3. featured merch (Bracelet + Limited Edition Hoodie)
ALTER TABLE products ADD COLUMN IF NOT EXISTS is_featured TINYINT(1) NOT NULL DEFAULT 0 AFTER image;
UPDATE products SET is_featured = (name IN ('Bracelet', 'Limited Edition Hoodie'));
