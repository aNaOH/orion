(function (global) {
  function setupMarkdownEditor(opts = {}) {
    const {
      selector = "#description",
      uniqueId = "Orion_MDEditor",
      placeholder = "Escribe aquí el contenido..."
    } = opts;

    return new SimpleMDE({
      element: document.querySelector(selector),
      autosave: {
        enabled: true,
        uniqueId: uniqueId,
        delay: 1000,
      },
      placeholder: placeholder,
      spellChecker: false,
      status: ["lines", "words"],
      toolbar: [
        {
          name: "bold",
          action: SimpleMDE.toggleBold,
          className: "bi bi-type-bold",
          title: "Negrita",
        },
        {
          name: "italic",
          action: SimpleMDE.toggleItalic,
          className: "bi bi-type-italic",
          title: "Cursiva",
        },
        {
          name: "strikethrough",
          action: SimpleMDE.toggleStrikethrough,
          className: "bi bi-type-strikethrough",
          title: "Tachado",
        },
        {
          name: "heading",
          action: SimpleMDE.toggleHeadingSmaller,
          className: "bi bi-type-h1",
          title: "Encabezado",
        },
        "|",
        {
          name: "unordered-list",
          action: SimpleMDE.toggleUnorderedList,
          className: "bi bi-list-ul",
          title: "Lista",
        },
        {
          name: "ordered-list",
          action: SimpleMDE.toggleOrderedList,
          className: "bi bi-list-ol",
          title: "Lista numerada",
        },
        "|",
        {
          name: "link",
          action: SimpleMDE.drawLink,
          className: "bi bi-link-45deg",
          title: "Enlace",
        },
        {
          name: "image",
          action: SimpleMDE.drawImage,
          className: "bi bi-image",
          title: "Imagen",
        },
        {
          name: "table",
          action: SimpleMDE.drawTable,
          className: "bi bi-table",
          title: "Tabla",
        },
        "|",
        {
          name: "preview",
          action: SimpleMDE.togglePreview,
          className: "bi bi-eye no-disable",
          title: "Vista previa",
        },
      ],
    });
  }

  // Export universal
  try {
    if (typeof module !== "undefined" && module.exports) {
      module.exports = { setupMarkdownEditor };
    }
  } catch (e) {}

  if (typeof define === "function" && define.amd) {
    define([], function () {
      return { setupMarkdownEditor };
    });
  }

  if (typeof global !== "undefined") {
    global.setupMarkdownEditor = setupMarkdownEditor;
  } else if (typeof window !== "undefined") {
    window.setupMarkdownEditor = setupMarkdownEditor;
  }
})(typeof window !== "undefined" ? window : this);
