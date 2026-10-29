/* ============================================================================
   HALAMAN TICKET — form pemesanan tiket yang tersambung ke database:
   - pilihan konser dari api/concerts.php (hanya yang sedang dijual)
   - konser bisa dipilih lewat URL: ticket.html?concert=2
   - kategori, harga, dan sisa kuota dari api/concerts.php?id=...
   - pesanan dikirim ke api/ticket-orders.php, lalu pindah ke invoice.html
   ============================================================================ */
(() => {
  const ROOT = "../../";
  const MAX_PER_ORDER = 10;
  const money = new Intl.NumberFormat("en-US", { style: "currency", currency: "USD", minimumFractionDigits: 0, maximumFractionDigits: 2 });
  const dateFormat = new Intl.DateTimeFormat("en-GB", { day: "numeric", month: "long", year: "numeric" });

  const form = document.getElementById("ticket-form");
  const concertSelect = document.getElementById("concert");
  const categoryBox = document.getElementById("category-list");
  const legend = categoryBox.querySelector("legend");
  const quantityInput = document.getElementById("quantity");
  const totalEl = document.getElementById("total-price");
  const errorEl = document.getElementById("ticket-error");
  const submitBtn = form.querySelector(".ticket-submit");

  const getJSON = (url) =>
    fetch(url).then((res) => {
      if (!res.ok) throw new Error(`${url} membalas status ${res.status}`);
      return res.json();
    });

  function showError(message) {
    errorEl.textContent = message;
    errorEl.hidden = !message;
  }

  function selectedCategory() {
    return categoryBox.querySelector('input[name="category"]:checked');
  }

  function updateTotal() {
    const selected = selectedCategory();
    if (!selected) {
      totalEl.textContent = money.format(0);
      return;
    }
    // jumlah maksimal mengikuti sisa kuota kategori yang dipilih
    quantityInput.max = Math.min(MAX_PER_ORDER, Number(selected.dataset.remaining));
    const quantity = Math.max(1, Number(quantityInput.value) || 1);
    totalEl.textContent = money.format(Number(selected.dataset.price) * quantity);
  }

  function categoryCard(category) {
    const node = document.getElementById("tpl-category").content.firstElementChild.cloneNode(true);
    const input = node.querySelector("input");
    input.value = category.id;
    input.dataset.price = category.price;
    input.dataset.remaining = category.remaining;
    input.disabled = category.remaining <= 0;

    node.querySelector('[data-field="name"]').textContent = category.name;
    node.querySelector('[data-field="description"]').textContent =
      category.remaining > 0 ? category.description : "Sold out";
    node.querySelector('[data-field="price"]').textContent = money.format(category.price);
    return node;
  }

  function loadCategories(concertId) {
    categoryBox.replaceChildren(legend);
    updateTotal();
    return getJSON(`${ROOT}api/concerts.php?id=${concertId}`).then((concert) => {
      categoryBox.replaceChildren(legend, ...concert.categories.map(categoryCard));
      const firstOpen = categoryBox.querySelector('input[name="category"]:not(:disabled)');
      if (firstOpen) firstOpen.checked = true;
      submitBtn.disabled = !firstOpen;
      updateTotal();
    });
  }

  function loadConcerts() {
    return getJSON(`${ROOT}api/concerts.php`).then((concerts) => {
      const onSale = concerts.filter((c) => c.status === "available" && !c.is_past);
      if (onSale.length === 0) {
        concertSelect.replaceChildren(new Option("No concerts on sale right now", ""));
        submitBtn.disabled = true;
        return;
      }

      concertSelect.replaceChildren(
        ...onSale.map((c) => new Option(`${c.venue_name} — ${dateFormat.format(new Date(c.event_date.replace(" ", "T")))}`, c.id))
      );

      // pilih konser dari URL (?concert=2) kalau ada dan sedang dijual
      const wanted = new URLSearchParams(location.search).get("concert");
      if (wanted && onSale.some((c) => String(c.id) === wanted)) concertSelect.value = wanted;

      return loadCategories(concertSelect.value);
    });
  }

  concertSelect.addEventListener("change", () => {
    showError("");
    history.replaceState(null, "", `?concert=${concertSelect.value}`);
    loadCategories(concertSelect.value).catch(() => showError("Ticket categories could not be loaded. Please try again."));
  });
  categoryBox.addEventListener("change", updateTotal);
  quantityInput.addEventListener("input", updateTotal);

  form.addEventListener("submit", (event) => {
    event.preventDefault();
    showError("");

    const selected = selectedCategory();
    if (!selected) {
      showError("Please choose a ticket category.");
      return;
    }

    const order = {
      category_id: Number(selected.value),
      full_name: document.getElementById("fullname").value.trim(),
      email: document.getElementById("email").value.trim(),
      phone: document.getElementById("phone").value.trim(),
      quantity: Number(quantityInput.value),
    };

    const label = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = "Processing...";

    fetch(`${ROOT}api/ticket-orders.php`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(order),
    })
      .then((res) => res.json().then((data) => ({ ok: res.ok, data })))
      .then(({ ok, data }) => {
        if (!ok) throw new Error(data.error || "The order could not be saved.");
        location.href = `invoice.html?invoice=${encodeURIComponent(data.invoice_no)}`;
      })
      .catch((error) => {
        // pesan dari server (mis. kuota habis) ditampilkan apa adanya
        showError(error instanceof SyntaxError ? "The order could not be saved. Please try again." : error.message);
        submitBtn.disabled = false;
        submitBtn.textContent = label;
        loadCategories(concertSelect.value).catch(() => {}); // perbarui sisa kuota
      });
  });

  loadConcerts().catch((error) => {
    console.error("Could not load concerts:", error);
    concertSelect.replaceChildren(new Option("Concerts could not be loaded", ""));
    submitBtn.disabled = true;
    showError("Concerts could not be loaded. Please open this page through http://localhost and try again.");
  });
})();
