/* ============================================================================
   MUSIC PAGE — places the shared "Stream" popover (#dropdown) right under the
   Stream button that opened it, with the same width as that button.
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

  // click runs before the browser opens the popover, so it opens already in place
  document.querySelectorAll('[popovertarget="dropdown"]').forEach((button) => {
    button.addEventListener("click", () => {
      opener = button;
      placeUnder(button);
    });
  });

  // keep it attached to its button when the layout changes (rotate, resize)
  window.addEventListener("resize", () => {
    if (opener && popover.matches(":popover-open")) placeUnder(opener);
  });
})();
