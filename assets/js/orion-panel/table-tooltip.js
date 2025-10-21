const tableTooltip = document.getElementById("table-tooltip");

document.querySelectorAll(".tooltip-btn").forEach((btn) => {
  btn.addEventListener("mouseenter", (e) => {
    const text = btn.getAttribute("data-tooltip");
    tableTooltip.textContent = text;
    tableTooltip.classList.remove("hidden");
    const rect = btn.getBoundingClientRect();
    tableTooltip.style.left = `${rect.left + rect.width / 2}px`;
    tableTooltip.style.top = `${rect.top - 30}px`;
    tableTooltip.style.transform = "translateX(-50%)";
  });

  btn.addEventListener("mouseleave", () => {
    tableTooltip.classList.add("hidden");
  });

  // Para móviles (tocar muestra el tooltip un instante)
  btn.addEventListener("touchstart", (e) => {
    const text = btn.getAttribute("data-tooltip");
    tableTooltip.textContent = text;
    tableTooltip.classList.remove("hidden");
    const rect = btn.getBoundingClientRect();
    tableTooltip.style.left = `${rect.left + rect.width / 2}px`;
    tableTooltip.style.top = `${rect.top - 30}px`;
    tableTooltip.style.transform = "translateX(-50%)";
    setTimeout(() => tableTooltip.classList.add("hidden"), 1500);
  });
});
