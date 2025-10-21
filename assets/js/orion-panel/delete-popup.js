/* delete-popup.js
 * Compatible: import { setupDeletePopup } from "...";
 *             OR window.setupDeletePopup({...});
 *
 * Requiere animate.css (v4) en la página para animaciones.
 */
(function (global) {
  function setupDeletePopup(opts = {}) {
    const {
      selector = ".delete-btn",
      getName = (btn) => btn.dataset.name || "este elemento",
      getDeleteUrl = (btn) =>
        btn.dataset.url || btn.dataset.id ? `/${btn.dataset.id}/delete/` : "#",
      title = "¿Eliminar elemento?",
      confirmText = "Eliminar",
      cancelText = "Cancelar",
      onConfirm = (url) => (window.location.href = url),
      animationDuration = "0.3s",
    } = opts;

    // Evitar crear popup más de una vez
    if (document.getElementById("deletePopup")) {
      // Si ya existe, actualizamos listeners delegados (no duplicamos elementos)
      return attachDelegatedListener(
        selector,
        getName,
        getDeleteUrl,
        title,
        confirmText,
        cancelText,
        onConfirm,
        animationDuration,
      );
    }

    const popupHTML = `
      <div id="deletePopup" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 hidden animate__animated">
        <div id="deletePopupContent" class="bg-[#111827] rounded-lg shadow-lg p-6 w-[90%] max-w-md text-gray-200 animate__animated">
          <h2 id="deletePopupTitle" class="text-lg font-semibold text-alt mb-4">${escapeHtml(title)}</h2>
          <p id="deleteMessage" class="text-gray-400 mb-6"></p>
          <div class="flex justify-end gap-3">
            <button id="cancelDelete" class="px-4 py-2 rounded-lg bg-gray-600 hover:bg-gray-500 transition">${escapeHtml(cancelText)}</button>
            <button id="confirmDelete" class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-500 transition">${escapeHtml(confirmText)}</button>
          </div>
        </div>
      </div>
    `;
    document.body.insertAdjacentHTML("beforeend", popupHTML);

    // set animation speed
    document.querySelectorAll(".animate__animated").forEach((el) => {
      el.style.setProperty("--animate-duration", animationDuration);
    });

    // Elements
    const popup = document.getElementById("deletePopup");
    const popupContent = document.getElementById("deletePopupContent");
    const deleteMessage = document.getElementById("deleteMessage");
    const titleElem = document.getElementById("deletePopupTitle");
    const cancelBtn = document.getElementById("cancelDelete");
    const confirmBtn = document.getElementById("confirmDelete");

    let currentDeleteUrl = null;

    // open/close helpers
    function openPopup(btn) {
      const name = getName(btn);
      currentDeleteUrl = getDeleteUrl(btn);
      titleElem.textContent = title;
      deleteMessage.textContent = `¿Seguro que deseas eliminar "${name}"? Esta acción no se puede deshacer.`;

      popup.classList.remove("hidden", "animate__fadeOut");
      popup.classList.add("animate__fadeIn");
      popupContent.classList.remove("animate__fadeOutUp");
      popupContent.classList.add("animate__fadeInDown");
    }

    function closePopup() {
      popupContent.classList.remove("animate__fadeInDown");
      popupContent.classList.add("animate__fadeOutUp");
      popup.classList.remove("animate__fadeIn");
      popup.classList.add("animate__fadeOut");

      popupContent.addEventListener(
        "animationend",
        () => {
          popup.classList.add("hidden");
          popupContent.classList.remove("animate__fadeOutUp");
          popup.classList.remove("animate__fadeOut");
        },
        { once: true },
      );
      currentDeleteUrl = null;
    }

    // attach delegated listener so dynamic rows are supported
    function attachDelegatedListener(
      sel,
      getNameFn,
      getUrlFn,
      ttl,
      confText,
      cancText,
      onConfirmFn,
      animDuration,
    ) {
      // remove previous delegated if exists
      if (document._deletePopupHandler) {
        document.removeEventListener("click", document._deletePopupHandler);
      }

      document._deletePopupHandler = function (e) {
        const btn = e.target.closest(sel);
        if (!btn) return;
        e.preventDefault();
        openPopup(btn);
      };

      document.addEventListener("click", document._deletePopupHandler);
    }

    // initial attach
    attachDelegatedListener(
      selector,
      getName,
      getDeleteUrl,
      title,
      confirmText,
      cancelText,
      onConfirm,
      animationDuration,
    );

    // cancel / confirm / overlay click / ESC
    cancelBtn.addEventListener("click", closePopup);
    confirmBtn.addEventListener("click", () => {
      if (!currentDeleteUrl) return;
      onConfirm(currentDeleteUrl);
    });

    popup.addEventListener("click", (e) => {
      if (e.target === popup) closePopup();
    });

    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && !popup.classList.contains("hidden"))
        closePopup();
    });

    // expose a small API to update or close from outside
    return {
      open: openPopup,
      close: closePopup,
    };
  }

  // helpers
  function escapeHtml(s) {
    if (!s) return s;
    return String(s)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  // Export for modules and attach to window for classic usage
  try {
    if (typeof module !== "undefined") {
      module.exports = { setupDeletePopup };
    }
  } catch (e) {}

  if (typeof global !== "undefined") {
    global.setupDeletePopup = setupDeletePopup;
  } else if (typeof window !== "undefined") {
    window.setupDeletePopup = setupDeletePopup;
  }

  // allow import { setupDeletePopup } in module contexts
  if (typeof define === "function" && define.amd) {
    define([], function () {
      return { setupDeletePopup };
    });
  }
})(typeof window !== "undefined" ? window : this);
