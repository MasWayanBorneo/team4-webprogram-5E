/* ============================================================================
   HALAMAN TOUR — mengambil konser dari database (api/concerts.php), lalu:
   - menampilkan konser yang akan datang, bisa difilter per region
   - menampilkan arsip konser yang sudah lewat, dipilih per tahun
   ============================================================================ */
(() => {
  const ROOT = "../../";
  const dateFormat = new Intl.DateTimeFormat("en-GB", { day: "numeric", month: "long", year: "numeric" });

  // status di database → [class badge, teks badge]
  const STATUS = {
    available: ["available", "Available"],
    coming_soon: ["coming", "Coming Soon"],
    sold_out: ["sold", "Sold Out"],
  };

  const upcomingList = document.getElementById("tour-list");
  const upcomingEmpty = document.getElementById("tour-empty");
  const yearSelect = document.getElementById("archive-year");
  const archiveList = document.getElementById("archive-list");
  const archiveEmpty = document.getElementById("archive-empty");

  let concerts = [];
  let region = "all";

  // "2027-02-13 19:00:00" dari MySQL → Date
  const toDate = (value) => new Date(value.replace(" ", "T"));

  function badge(className, text) {
    const span = document.createElement("span");
    span.className = className;
    span.textContent = text;
    return span;
  }

  function concertCard(concert) {
    const node = document.getElementById("tpl-concert").content.firstElementChild.cloneNode(true);
    const image = node.querySelector('[data-field="image"]');
    image.src = ROOT + concert.image;
    image.alt = concert.venue_name;
    node.querySelector('[data-field="venue_name"]').textContent = concert.venue_name;
    node.querySelector('[data-field="location"]').textContent = `${concert.city}, ${concert.country}`;
    node.querySelector('[data-field="date"]').textContent = dateFormat.format(toDate(concert.event_date));

    const status = node.querySelector(".tour-status");
    if (concert.is_past) {
      status.append(badge("ended", "Ended"));
      return node;
    }

    const [className, text] = STATUS[concert.status] || STATUS.sold_out;
    status.append(badge(className, text));

    if (concert.status === "available") {
      const link = document.createElement("a");
      link.className = "ticket-link";
      link.href = `../ticket/ticket.html?concert=${concert.id}`;
      link.textContent = "Get Ticket";
      status.append(link);
    } else if (concert.status === "coming_soon") {
      const button = document.createElement("button");
      button.type = "button";
      button.disabled = true;
      button.textContent = "Pre Order";
      status.append(button);
    }
    return node;
  }

  function renderUpcoming() {
    const shown = concerts.filter((c) => !c.is_past && (region === "all" || c.region.toLowerCase() === region));
    upcomingList.replaceChildren(...shown.map(concertCard));
    upcomingEmpty.hidden = shown.length > 0;
  }

  function renderArchive() {
    const year = yearSelect.value;
    const shown = concerts
      .filter((c) => c.is_past && c.event_date.startsWith(year))
      .reverse(); // terbaru dulu
    archiveList.replaceChildren(...shown.map(concertCard));
  }

  function setupArchive() {
    const years = [...new Set(concerts.filter((c) => c.is_past).map((c) => c.event_date.slice(0, 4)))].sort().reverse();
    yearSelect.replaceChildren(...years.map((y) => new Option(y, y)));
    yearSelect.hidden = years.length === 0;
    archiveEmpty.hidden = years.length > 0;
    if (years.length) renderArchive();
  }

  // tombol filter region
  document.querySelectorAll(".filters [data-region]").forEach((button) => {
    button.addEventListener("click", () => {
      region = button.dataset.region;
      document.querySelectorAll(".filters [data-region]").forEach((b) => {
        b.classList.toggle("is-active", b === button);
        b.setAttribute("aria-pressed", String(b === button));
      });
      renderUpcoming();
    });
  });

  yearSelect.addEventListener("change", renderArchive);

  fetch(ROOT + "api/concerts.php")
    .then((res) => {
      if (!res.ok) throw new Error(`API konser membalas status ${res.status}`);
      return res.json();
    })
    .then((data) => {
      concerts = data;
      renderUpcoming();
      setupArchive();
    })
    .catch((error) => {
      console.error("Could not load concerts:", error);
      upcomingEmpty.textContent = "Concerts could not be loaded. Please try again later.";
      upcomingEmpty.hidden = false;
    });
})();
