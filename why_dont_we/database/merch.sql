-- ============================================================================
-- MERCH — products table for src/merch/merch.html
-- Columns match the objects in src/data/products.js one to one.
-- Import in phpMyAdmin (XAMPP) into the database of your choice.
-- ============================================================================

CREATE TABLE IF NOT EXISTS products (
  id           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  slug         VARCHAR(100)  NOT NULL,
  name         VARCHAR(150)  NOT NULL,
  collection   VARCHAR(150)  NULL,
  price        DECIMAL(10,2) NOT NULL,
  image        VARCHAR(255)  NOT NULL,             -- path relative to the project root
  is_featured  TINYINT(1)    NOT NULL DEFAULT 0,   -- 1 = shown in the featured cards
  created_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_products_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO products (id, slug, name, collection, price, image, is_featured) VALUES
  (1, 'bracelet',               'Bracelet',               'Covid-19 Collection', 65.00, 'assets/image/gelang.png',   1),
  (2, 'limited-edition-hoodie', 'Limited Edition Hoodie', 'New Era Collection',  65.00, 'assets/image/hoodie.png',   1),
  (3, 'socks',                  'Socks',                  'New Era Collection',  65.00, 'assets/image/kaoskaki.png', 0),
  (4, 'black-t-shirt',          'Black T-Shirt',          'New Era Collection',  65.00, 'assets/image/—Pngtree—black t-shirt_5609353.png', 0),
  (5, 'dark-green-t-shirt',     'Dark Green T-Shirt',     'New Era Collection',  65.00, 'assets/image/—Pngtree—plain dark green t shirt_14667981.png', 0),
  (6, 'cowboy-hat',             'Cowboy Hat',             'New Era Collection',  65.00, 'assets/image/vecteezy_ai-generated-cowboy-hat-png_34616681.png', 0),
  (7, 'seafoam-t-shirt',        'Seafoam T-Shirt',        'New Era Collection',  65.00, 'assets/image/vecteezy_seafoam-green-short-sleeve-crew-neck-tshirt-mockup-for_59901009.png', 0);
