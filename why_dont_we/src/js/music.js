/* ============================================================================
   HALAMAN MUSIC — kenyamanan untuk dropdown Stream (<details class="stream-dropdown">).
   Dropdown sudah bisa dibuka/ditutup tanpa JavaScript; script ini hanya:
   - menutup dropdown lain saat satu dropdown dibuka
   - menutup dropdown saat klik di luar atau menekan tombol Esc
   Selain itu mengukur tinggi navbar untuk posisi popup lirik (lihat bawah).
   ============================================================================ */
(() => {
  const dropdowns = document.querySelectorAll(".stream-dropdown");

  function closeAll(except) {
    dropdowns.forEach((dropdown) => {
      if (dropdown !== except) dropdown.open = false;
    });
  }

  dropdowns.forEach((dropdown) => {
    dropdown.addEventListener("toggle", () => {
      if (dropdown.open) closeAll(dropdown);
    });
  });

  document.addEventListener("click", (event) => {
    if (!event.target.closest(".stream-dropdown")) closeAll();
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") closeAll();
  });

  /* --- popup lirik ------------------------------------------------------------
     Tinggi navbar (1 baris di laptop, 2 baris di HP) disimpan sebagai --nav-h,
     dipakai .lyric-popup di music.css supaya popup muncul tepat di bawah navbar. */
  const header = document.querySelector(".site-header");
  if (header) {
    const setNavHeight = () =>
      document.documentElement.style.setProperty("--nav-h", header.offsetHeight + "px");
    setNavHeight();
    window.addEventListener("resize", setNavHeight);
  }
})();
