/* ============================================================================
   MERCH PRODUCTS — temporary data source for src/merch/merch.html.
   Each object is one row of the `products` table in wdw_db (database/wdw_db.sql),
   with the same column names. When the database is ready, remove this file
   and point loadProducts() in src/js/merch.js at the API endpoint instead.

   image       → path relative to the project root
   is_featured → 1 = also shown in the large "featured" cards at the top
   ============================================================================ */
window.MERCH_PRODUCTS = [
  { id: 1, slug: "bracelet",               name: "Bracelet",               collection: "Covid-19 Collection", price: 65, image: "assets/image/gelang.png",   is_featured: 1 },
  { id: 2, slug: "limited-edition-hoodie", name: "Limited Edition Hoodie", collection: "New Era Collection",  price: 65, image: "assets/image/hoodie.png",   is_featured: 1 },
  { id: 3, slug: "socks",                  name: "Socks",                  collection: "New Era Collection",  price: 65, image: "assets/image/kaoskaki.png", is_featured: 0 },
  { id: 4, slug: "black-t-shirt",          name: "Black T-Shirt",          collection: "New Era Collection",  price: 65, image: "assets/image/—Pngtree—black t-shirt_5609353.png", is_featured: 0 },
  { id: 5, slug: "dark-green-t-shirt",     name: "Dark Green T-Shirt",     collection: "New Era Collection",  price: 65, image: "assets/image/—Pngtree—plain dark green t shirt_14667981.png", is_featured: 0 },
  { id: 6, slug: "cowboy-hat",             name: "Cowboy Hat",             collection: "New Era Collection",  price: 65, image: "assets/image/vecteezy_ai-generated-cowboy-hat-png_34616681.png", is_featured: 0 },
  { id: 7, slug: "seafoam-t-shirt",        name: "Seafoam T-Shirt",        collection: "New Era Collection",  price: 65, image: "assets/image/vecteezy_seafoam-green-short-sleeve-crew-neck-tshirt-mockup-for_59901009.png", is_featured: 0 },
];
