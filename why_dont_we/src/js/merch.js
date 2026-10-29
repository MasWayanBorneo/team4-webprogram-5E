/* ============================================================================
   HALAMAN MERCH — mengisi <template> di src/merch/merch.html dengan produk
   dan menghitung isi keranjang. Field produk sama persis dengan tabel `products`.
   ============================================================================ */
(() => {
  const ROOT = "../../"; // merch.html → root project (path gambar relatif dari root)
  const CART_KEY = "wdw-cart"; // { [idProduk]: jumlah }
  const money = new Intl.NumberFormat("en-US", { style: "currency", currency: "USD", maximumFractionDigits: 0 });

  /* --- sumber data ----------------------------------------------------------
     Produk diambil dari database lewat api/products.php (tabel `products`).
     Halaman harus dibuka lewat http://localhost/..., bukan file://, supaya
     PHP-nya jalan. */
  function loadProducts() {
    return fetch(ROOT + "api/products.php").then((res) => {
      if (!res.ok) throw new Error(`API produk membalas status ${res.status}`);
      return res.json();
    });
  }

  // MySQL/PHP sering mengembalikan angka dan boolean sebagai teks ("1", "65.00")
  const isFeatured = (p) => Number(p.is_featured) === 1 || p.is_featured === true;

  /* --- tampilkan ----------------------------------------------------------- */
  function fromTemplate(templateId, product) {
    const node = document.getElementById(templateId).content.firstElementChild.cloneNode(true);
    node.dataset.productId = product.id;
    node.querySelectorAll("[data-field]").forEach((el) => {
      const field = el.dataset.field;
      if (field === "image") {
        if (product.image) el.src = ROOT + product.image; // kolom image boleh kosong (NULL)
        el.alt = product.name;
      } else if (field === "price") {
        el.textContent = money.format(Number(product.price));
      } else {
        el.textContent = product[field] ?? "";
      }
    });
    return node;
  }

  function render(products) {
    const featured = document.getElementById("featured-list");
    const all = document.getElementById("product-list");

    featured.replaceChildren(...products.filter(isFeatured).map((p) => fromTemplate("tpl-featured", p)));
    all.replaceChildren(...products.map((p) => fromTemplate("tpl-product", p)));

    featured.closest("section").hidden = featured.children.length === 0;
    if (products.length === 0) showStatus("No products available yet.");
  }

  function showStatus(message) {
    const status = document.getElementById("merch-status");
    status.textContent = message;
    status.hidden = false;
  }

  /* --- keranjang ----------------------------------------------------------- */
  function readCart() {
    try {
      return JSON.parse(localStorage.getItem(CART_KEY)) || {};
    } catch {
      return {};
    }
  }

  function saveCart() {
    try {
      localStorage.setItem(CART_KEY, JSON.stringify(cart));
    } catch {
      /* storage tidak tersedia (mode private) — keranjang tetap jalan selama kunjungan ini */
    }
  }

  function updateCartCount() {
    const count = Object.values(cart).reduce((sum, qty) => sum + qty, 0);
    document.getElementById("cart-count").textContent = `${count} ${count === 1 ? "Item" : "Items"}`;
  }

  const cart = readCart();

  document.addEventListener("click", (event) => {
    const button = event.target.closest('[data-action="add-to-cart"]');
    if (!button) return;
    const id = button.closest("[data-product-id]").dataset.productId;
    cart[id] = (cart[id] || 0) + 1;
    saveCart();
    updateCartCount();
  });

  updateCartCount();
  loadProducts()
    .then(render)
    .catch((error) => {
      console.error("Could not load products:", error);
      showStatus("Products could not be loaded. Please try again later.");
    });
})();
