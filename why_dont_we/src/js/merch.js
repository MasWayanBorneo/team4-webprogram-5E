/* ============================================================================
   HALAMAN MERCH — mengisi <template> di src/merch/merch.php dengan produk,
   menghitung isi keranjang, dan menampilkan ringkasan keranjang (tombol Checkout).
   Field produk sama persis dengan tabel `products`. Keranjang disimpan di browser
   (localStorage); pembayaran online belum tersedia.
   ============================================================================ */
(() => {
  const ROOT = "../../"; // merch.php → root project (path gambar relatif dari root)
  const CART_KEY = "wdw-cart"; // { [idProduk]: jumlah }
  // harga bulat tanpa sen ($65), harga desimal dengan dua angka sen ($49.50)
  const money = {
    format: (amount) => new Intl.NumberFormat("en-US", {
      style: "currency", currency: "USD",
      minimumFractionDigits: Number.isInteger(amount) ? 0 : 2, maximumFractionDigits: 2,
    }).format(amount),
  };

  /* --- sumber data: baris tabel `products` dari api/products.php ----------- */
  function loadProducts() {
    return fetch(ROOT + "api/products.php").then((res) => {
      if (!res.ok) throw new Error("HTTP " + res.status);
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
        el.src = ROOT + product.image;
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
  let productsById = {}; // diisi setelah api/products.php selesai dimuat

  /* --- ringkasan keranjang (modal yang dibuka tombol Checkout) ------------- */
  const cartModal = document.getElementById("cart-modal");

  function renderCart() {
    const list = document.getElementById("cart-items");
    let total = 0;
    list.replaceChildren();

    for (const [id, qty] of Object.entries(cart)) {
      const product = productsById[id];
      if (!product) continue; // produk sudah tidak dijual (dinonaktifkan di dashboard)
      const subtotal = Number(product.price) * qty;
      total += subtotal;

      const item = document.createElement("li");
      item.className = "cart-item";
      item.dataset.productId = id;
      item.innerHTML = `
        <img alt="">
        <div class="cart-item-info"><p class="cart-item-name"></p><p class="cart-item-price"></p></div>
        <div class="cart-qty">
          <button type="button" data-action="cart-dec" aria-label="Kurangi">−</button>
          <span></span>
          <button type="button" data-action="cart-inc" aria-label="Tambah">+</button>
        </div>
        <p class="cart-item-subtotal"></p>
        <button type="button" class="cart-remove" data-action="cart-remove" aria-label="Hapus">&times;</button>`;
      // isi teks lewat textContent (bukan innerHTML) supaya nama produk tidak bisa menyisipkan HTML
      item.querySelector("img").src = ROOT + product.image;
      item.querySelector("img").alt = product.name;
      item.querySelector(".cart-item-name").textContent = product.name;
      item.querySelector(".cart-item-price").textContent = money.format(Number(product.price)) + " each";
      item.querySelector(".cart-qty span").textContent = qty;
      item.querySelector(".cart-item-subtotal").textContent = money.format(subtotal);
      list.append(item);
    }

    const isEmpty = list.children.length === 0;
    document.getElementById("cart-empty").hidden = !isEmpty;
    document.getElementById("cart-total-row").hidden = isEmpty;
    cartModal.querySelector('[data-action="clear-cart"]').hidden = isEmpty;
    document.getElementById("cart-total").textContent = money.format(total);
  }

  function changeQty(id, delta) {
    cart[id] = (cart[id] || 0) + delta;
    if (cart[id] <= 0) delete cart[id];
    saveCart();
    updateCartCount();
    renderCart();
  }

  document.addEventListener("click", (event) => {
    const button = event.target.closest("[data-action]");

    // klik area gelap di luar kartu modal = tutup
    if (event.target === cartModal) return cartModal.close();
    if (!button) return;

    const action = button.dataset.action;
    const id = button.closest("[data-product-id]")?.dataset.productId;

    if (action === "add-to-cart") {
      cart[id] = (cart[id] || 0) + 1;
      saveCart();
      updateCartCount();
    } else if (action === "open-cart") {
      renderCart();
      cartModal.showModal();
    } else if (action === "close-cart") {
      cartModal.close();
    } else if (action === "cart-inc") {
      changeQty(id, 1);
    } else if (action === "cart-dec") {
      changeQty(id, -1);
    } else if (action === "cart-remove") {
      changeQty(id, -cart[id]);
    } else if (action === "clear-cart") {
      Object.keys(cart).forEach((key) => delete cart[key]);
      saveCart();
      updateCartCount();
      renderCart();
    }
  });

  updateCartCount();
  loadProducts()
    .then((products) => {
      productsById = Object.fromEntries(products.map((p) => [String(p.id), p]));
      // buang produk di keranjang yang sudah tidak dijual, supaya jumlahnya sesuai
      Object.keys(cart).forEach((key) => {
        if (!productsById[key]) delete cart[key];
      });
      saveCart();
      updateCartCount();
      render(products);
    })
    .catch((error) => {
      console.error("Could not load products:", error);
      showStatus("Products could not be loaded. Please try again later.");
    });
})();
