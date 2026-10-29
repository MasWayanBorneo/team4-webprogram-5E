/* ============================================================================
   HALAMAN MUSIC — menaruh popover "Stream" (#dropdown) tepat di bawah tombol
   Stream yang diklik, dengan lebar yang sama seperti tombol itu.
   ============================================================================ */
(() => {
  const popover = document.getElementById("dropdown");
  if (!popover) return;

  let opener = null;

  function placeUnder(button) {
    const rect = button.getBoundingClientRect();
    popover.style.width = `${rect.width}px`;
    popover.style.left = `${rect.left + window.scrollX}px`;
    popover.style.top = `${rect.bottom + window.scrollY + 8}px`;
  }

  // click dijalankan sebelum browser membuka popover, jadi popover langsung muncul di posisinya
  document.querySelectorAll('[popovertarget="dropdown"]').forEach((button) => {
    button.addEventListener("click", () => {
      opener = button;
      placeUnder(button);
    });
  });

  // tetap menempel ke tombolnya saat layout berubah (layar diputar, ukuran jendela berubah)
  window.addEventListener("resize", () => {
    if (opener && popover.matches(":popover-open")) placeUnder(opener);
  });
})();
