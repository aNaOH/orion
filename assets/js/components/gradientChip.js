class GradientChipElement extends HTMLElement {
  wrapper;
  iconContainer;
  textContainer;

  static get observedAttributes() {
    return [
      "base-color",
      "size",
      "icon",
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
        padding: 4px 12px;
        gap: 8px;
        position: relative;
        transition: background 0.3s ease, color 0.3s ease;
        min-height: 28px;
        width: 100%;
        box-sizing: border-box;
      }
      .icon-container {
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        flex-shrink: 0;
        overflow: hidden;
      }
      .icon-container img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
      }
      .text-container {
        font-family: 'Lexend', sans-serif;
        font-size: 10px;
        font-weight: 700;
        line-height: 1;
        white-space: nowrap;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        flex-grow: 1;
        min-width: 0;
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
    let r, g, b;
    if (color.startsWith("#")) {
      if (color.length === 4) {
        r = parseInt(color[1] + color[1], 16);
        g = parseInt(color[2] + color[2], 16);
        b = parseInt(color[3] + color[3], 16);
      } else {
        r = parseInt(color.substring(1, 3), 16);
        g = parseInt(color.substring(3, 5), 16);
        b = parseInt(color.substring(5, 7), 16);
      }
    } else {
      // Default to black if invalid
      r = g = b = 0;
    }

    const lighterColor = `rgb(${Math.min(r + 60, 255)}, ${Math.min(g + 60, 255)}, ${Math.min(b + 60, 255)})`;
    const darkerColor = `rgb(${Math.max(r - 20, 0)}, ${Math.max(g - 20, 0)}, ${Math.max(b - 20, 0)})`;
    return `linear-gradient(135deg, ${darkerColor} 0%, ${lighterColor} 100%)`;
  }

  calculateIconColor(baseColor) {
    let r, g, b;
    if (baseColor.startsWith("#")) {
      if (baseColor.length === 4) {
        r = parseInt(baseColor[1] + baseColor[1], 16);
        g = parseInt(baseColor[2] + baseColor[2], 16);
        b = parseInt(baseColor[3] + baseColor[3], 16);
      } else {
        r = parseInt(baseColor.substring(1, 3), 16);
        g = parseInt(baseColor.substring(3, 5), 16);
        b = parseInt(baseColor.substring(5, 7), 16);
      }
    } else {
      r = g = b = 0;
    }
    const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
    return luminance > 0.6 ? "#111827" : "#ffffff";
  }

  updateStyles() {
    const baseColor = this.getAttribute("base-color") || "#7c3aed";
    const size = Math.max(10, parseInt(this.getAttribute("size") || "16", 10));
    const borderRadius = Math.max(
      0,
      parseInt(this.getAttribute("border-radius") || "8", 10),
    );

    const iconColor = this.calculateIconColor(baseColor);

    Object.assign(this.wrapper.style, {
      background: this.generateTonalGradient(baseColor),
      borderRadius: `${borderRadius}px`,
      color: iconColor,
      border: `1px solid ${baseColor}88`
    });

    Object.assign(this.iconContainer.style, {
      width: `${size}px`,
      height: `${size}px`,
    });

    // 🔹 Ocultar icon-container y ajustar el gap si no hay icono
    if (!this.iconContainer.innerHTML.trim()) {
      this.iconContainer.style.display = "none";
      this.wrapper.style.gap = "0px";
      this.wrapper.style.padding = "4px 10px";
    } else {
      this.iconContainer.style.display = "flex";
      this.wrapper.style.gap = "8px";
      this.wrapper.style.padding = "4px 12px";
    }
  }

  async updateIcon() {
    const iconUrl = this.getAttribute("icon");
    const iconPath = this.getAttribute("icon-path");
    const path = iconUrl || iconPath;

    if (!path) {
      this.iconContainer.innerHTML = "";
      this.updateStyles();
      return;
    }

    // Comprobar si es una imagen (extensión) o si se ha pasado por el atributo 'icon'
    const isImage = iconUrl || /\.(png|jpg|jpeg|webp|gif|svg)$/i.test(path);
    
    // Si es SVG pero por icon-path, intentamos fetch para inyectar y poder colorear
    if (iconPath && path.toLowerCase().endsWith(".svg")) {
      try {
        const response = await fetch(path);
        if (!response.ok) throw new Error("Failed to fetch icon");
        const svgText = await response.text();
        this.iconContainer.innerHTML = svgText;

        const svg = this.iconContainer.querySelector("svg");
        if (svg) {
          svg.style.fill = "currentColor";
        }
        this.updateStyles();
        return;
      } catch (error) {
        console.error("Error loading SVG:", error);
      }
    }

    // Si no es un SVG para inyectar, o el fetch falló, lo tratamos como imagen normal
    if (path) {
      this.iconContainer.innerHTML = `<img src="${path}" alt="icon">`;
    } else {
      this.iconContainer.innerHTML = "";
    }

    this.updateStyles();
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

    if (name === "icon" || name === "icon-path") {
      this.updateIcon();
    } else if (name === "text") {
      this.updateText();
    } else {
      this.updateStyles();
    }
  }
}

customElements.define("gradient-chip", GradientChipElement);
