class GradientChipElement extends HTMLElement {
  wrapper;
  iconContainer;
  textContainer;

  static get observedAttributes() {
    return [
      "base-color",
      "size",
      "icon-path",
      "use-tailwind",
      "border-radius",
      "text",
      "full-width",
    ];
  }

  constructor() {
    super();
    this.attachShadow({ mode: "open" });

    this.wrapper = document.createElement("div");
    this.wrapper.className = "wrapper";

    this.iconContainer = document.createElement("div");
    this.iconContainer.className = "icon-container";

    this.textContainer = document.createElement("span");
    this.textContainer.className = "text-container";

    this.wrapper.appendChild(this.iconContainer);
    this.wrapper.appendChild(this.textContainer);

    const style = document.createElement("style");
    style.textContent = `
      :host {
        display: inline-block;
        vertical-align: middle;
        min-width: 0;
      }
      :host([full-width]), :host([full-width="true"]) {
        display: block;
        width: 100%;
      }
      .wrapper {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        padding: 6px 12px;
        gap: 10px;
        position: relative;
        transition: background 0.3s ease, color 0.3s ease;
        min-height: 36px;
        width: 100%;
        box-sizing: border-box;
      }
      .icon-container {
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        flex-shrink: 0;
      }
      .text-container {
        font-size: 13px;
        font-weight: 500;
        line-height: 1.25;
        white-space: normal;
        word-break: break-word;
        flex-grow: 1;
        min-width: 0;
        padding: 2px 0;
      }
      svg {
        width: 100%;
        height: 100%;
        fill: currentColor;
        display: block;
      }
    `;

    this.shadowRoot?.append(style, this.wrapper);
  }

  generateTonalGradient(color) {
    const [r, g, b] = color.match(/\w\w/g).map((hex) => parseInt(hex, 16));
    const lighterColor = `rgb(${Math.min(r + 40, 255)}, ${Math.min(g + 40, 255)}, ${Math.min(b + 40, 255)})`;
    return `linear-gradient(135deg, ${color} 0%, ${lighterColor} 100%)`;
  }

  calculateIconColor(baseColor) {
    const [r, g, b] = baseColor
      .match(/\w\w/g)
      .map((hex) => parseInt(hex, 16) / 255);
    const luminance = 0.2126 * r + 0.7152 * g + 0.0722 * b;
    return luminance > 0.5 ? "#000000" : "#ffffff";
  }

  updateStyles() {
    const baseColor = this.getAttribute("base-color") || "#000000";
    const size = Math.max(10, parseInt(this.getAttribute("size") || "40", 10));
    const borderRadius = Math.max(
      0,
      parseInt(this.getAttribute("border-radius") || "16", 10),
    );
    const useTailwind = this.getAttribute("use-tailwind") !== "false";

    const iconColor = this.calculateIconColor(baseColor);

    if (useTailwind) {
      this.wrapper.className =
        "wrapper flex items-center cursor-pointer";
      this.iconContainer.className =
        "icon-container flex items-center justify-center";
      this.textContainer.className = "text-container text-sm font-medium whitespace-normal";
    } else {
      this.wrapper.className = "wrapper";
      this.iconContainer.className = "icon-container";
      this.textContainer.className = "text-container";
    }

    Object.assign(this.wrapper.style, {
      background: this.generateTonalGradient(baseColor),
      borderRadius: `${borderRadius}px`,
      color: iconColor,
    });

    Object.assign(this.iconContainer.style, {
      width: `${size}px`,
      height: `${size}px`,
    });

    // 🔹 Ocultar icon-container y ajustar el gap si no hay icono
    if (!this.iconContainer.innerHTML.trim()) {
      this.iconContainer.style.display = "none";
      this.wrapper.style.gap = "0px";
    } else {
      this.iconContainer.style.display = "flex";
      this.wrapper.style.gap = "8px";
    }
  }

  async updateIcon() {
    const iconPath = this.getAttribute("icon-path");
    if (!iconPath) {
      this.iconContainer.innerHTML = "";
      this.updateStyles(); // Asegurar que se quite el espacio
      return;
    }

    try {
      const response = await fetch(iconPath);
      if (!response.ok) throw new Error("Failed to fetch icon");
      const svgText = await response.text();
      this.iconContainer.innerHTML = svgText;

      const svg = this.iconContainer.querySelector("svg");
      if (svg) {
        svg.style.fill = "currentColor";
      }
    } catch (error) {
      console.error("Error loading SVG:", error);
      this.iconContainer.innerHTML = "";
    }

    this.updateStyles(); // Actualiza visualmente el espaciado según haya o no icono
  }

  updateText() {
    const text = this.getAttribute("text") || "";
    this.textContainer.textContent = text;
  }

  connectedCallback() {
    this.updateStyles();
    this.updateIcon();
    this.updateText();
  }

  attributeChangedCallback(name, oldValue, newValue) {
    if (oldValue === newValue) return;

    if (name === "icon-path") {
      this.updateIcon();
    } else if (name === "text") {
      this.updateText();
    } else {
      this.updateStyles();
    }
  }
}

customElements.define("gradient-chip", GradientChipElement);
