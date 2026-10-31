/* ============================================================================
   PRODUK MERCH — sumber data sementara untuk src/merch/merch.php.
   Setiap objek = satu baris tabel `products` (lihat database/merch.sql),
   dengan nama kolom yang sama. Saat database siap, hapus file ini dan arahkan
   loadProducts() di src/js/merch.js ke endpoint API.

   image       → path relatif dari root project
   is_featured → 1 = juga tampil di kartu "featured" besar di bagian atas
   ============================================================================ */
window.MERCH_PRODUCTS = [
  { id: 1, slug: "bracelet",               name: "Bracelet",               collection: "Covid-19 Collection", price: 65, image: "assets/image/gelang.png",   is_featured: 1 },
  { id: 2, slug: "limited-edition-hoodie", name: "Limited Edition Hoodie", collection: "New Era Collection",  price: 65, image: "assets/image/hoodie.png",   is_featured: 1 },
  { id: 3, slug: "socks",                  name: "Socks",                  collection: "New Era Collection",  price: 65, image: "assets/image/kaoskaki.png", is_featured: 0 },
  { id: 4, slug: "black-t-shirt",          name: "Black T-Shirt",          collection: "New Era Collection",  price: 65, image: "assets/image/pngtree-black-tshirt.png", is_featured: 0 },
  { id: 5, slug: "dark-green-t-shirt",     name: "Dark Green T-Shirt",     collection: "New Era Collection",  price: 65, image: "assets/image/pngtree-green-tshirt.png", is_featured: 0 },
  { id: 6, slug: "cowboy-hat",             name: "Cowboy Hat",             collection: "New Era Collection",  price: 65, image: "assets/image/vecteezy_ai-generated-cowboy-hat-png_34616681.png", is_featured: 0 },
  { id: 7, slug: "seafoam-t-shirt",        name: "Seafoam T-Shirt",        collection: "New Era Collection",  price: 65, image: "assets/image/vecteezy_seafoam-green-short-sleeve-crew-neck-tshirt-mockup-for_59901009.png", is_featured: 0 },
];
