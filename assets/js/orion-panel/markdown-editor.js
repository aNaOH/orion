(function (global) {
  function setupMarkdownEditor(opts = {}) {
    const { selector = "#description", uniqueId = "Orion_MDEditor" } = opts;

    return new SimpleMDE({
      element: document.querySelector(selector),
      autosave: {
        enabled: true,
        uniqueId: uniqueId,
        delay: 1000,
      },
      insertTexts: {
        horizontalRule: ["", "\n\n-----\n\n"],
        link: ["[", "](http://)"],
        table: [
          "",
          "\n\n| Column 1 | Column 2 | Column 3 |\n| -------- | -------- | -------- |\n| Text     | Text      | Text     |\n\n",
        ],
      },
      placeholder: "Type here...",
      toolbar: [
        {
          name: "bold",
          action: SimpleMDE.toggleBold,
          className: "fa fa-bold !text-alt",
          title: "Bold",
        },
        {
          name: "italic",
          action: SimpleMDE.toggleItalic,
          className: "fa fa-italic !text-alt",
          title: "Italic",
        },
        {
          name: "underline",
          action: SimpleMDE.toggleUnderline,
          className: "fa fa-underline !text-alt",
          title: "Underline",
        },
        {
          name: "heading",
          action: SimpleMDE.toggleHeadingSmaller,
          className: "fa fa-header !text-alt",
          title: "Heading",
        },
        "|",
        {
          name: "link",
          action: SimpleMDE.toggleLink,
          className: "fa fa-link !text-alt",
          title: "Link",
        },
        {
          name: "table",
          action: SimpleMDE.drawTable, // toggleTable no existe en SimpleMDE
          className: "fa fa-table !text-alt",
          title: "Table",
        },
        "|",
        {
          name: "preview",
          action: SimpleMDE.togglePreview,
          className: "fa fa-eye !text-alt no-disable",
          title: "Preview",
        },
      ],
      previewRender: function (plainText) {
        const html = SimpleMDE.prototype.markdown(plainText);
        const temp = document.createElement("div");
        temp.innerHTML = html;

        const applyClass = (sel, cls) =>
          temp.querySelectorAll(sel).forEach((el) => (el.className = cls));

        applyClass("h1", "text-4xl font-bold my-4");
        applyClass("h2", "text-2xl font-bold my-4");
        applyClass("h3", "text-xl font-bold my-4");
        applyClass("h4", "text-lg font-bold my-4");
        applyClass("p", "text-base leading-relaxed my-2");
        applyClass("ul", "list-disc pl-5 my-2");
        applyClass("ol", "list-decimal pl-5 my-2");
        applyClass("a", "text-blue-500 hover:underline");
        applyClass("img", "max-w-full h-auto rounded");

        return temp.innerHTML;
      },
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
