/* ============================================================================
   MERCH PAGE — fills the <template>s in src/merch/merch.html with products
   and keeps the cart count. Product fields map 1:1 to the `products` table.
   ============================================================================ */
(() => {
  const ROOT = "../../"; // merch.html → project root (image paths are root-relative)
  const CART_KEY = "wdw-cart"; // { [productId]: quantity }
  const money = new Intl.NumberFormat("en-US", { style: "currency", currency: "USD", maximumFractionDigits: 0 });

  /* --- data source ---------------------------------------------------------
     The only thing to change when the database is ready, e.g.:
       return fetch(ROOT + "api/products.php").then((res) => res.json());
     The endpoint just has to return the rows of `products` as a JSON array. */
  function loadProducts() {
    return Promise.resolve(window.MERCH_PRODUCTS || []);
  }

  // MySQL/PHP often returns numbers and booleans as strings ("1", "65.00")
  const isFeatured = (p) => Number(p.is_featured) === 1 || p.is_featured === true;

  /* --- render -------------------------------------------------------------- */
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

  /* --- cart ---------------------------------------------------------------- */
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
      /* storage unavailable (private mode) — cart still works for this visit */
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
