class GradientSquareElement extends HTMLElement {
    wrapper;
    svgContainer;
  
    static get observedAttributes() {
      return ['base-color', 'size', 'icon-path', 'use-tailwind', 'border-radius'];
    }
  
    constructor() {
      super();
      this.attachShadow({ mode: 'open' });
  
      this.wrapper = document.createElement('div');
      this.wrapper.className = 'wrapper';
      this.svgContainer = document.createElement('div');
      this.svgContainer.className = 'svg-container';
      this.wrapper.appendChild(this.svgContainer);
  
      const style = document.createElement('style');
      style.textContent = `
        :host {
          display: inline-block;
        }
        .wrapper {
          display: flex;
          align-items: center;
          justify-content: center;
          overflow: hidden;
          position: relative;
        }
        .svg-container {
          display: flex;
          align-items: center;
          justify-content: center;
          position: relative;
          z-index: 1;
          transition: color 0.3s ease;
          width: 100%;
          height: 100%;
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
      const [r, g, b] = baseColor.match(/\w\w/g).map((hex) => parseInt(hex, 16) / 255);
      const luminance = 0.2126 * r + 0.7152 * g + 0.0722 * b;
      return luminance > 0.5 ? '#000000' : '#ffffff';
    }
  
    updateStyles() {
      const baseColor = this.getAttribute('base-color') || '#000000';
      const size = Math.max(10, parseInt(this.getAttribute('size') || '300', 10));
      const borderRadius = Math.max(0, parseInt(this.getAttribute('border-radius') || '8', 10));
      const useTailwind = this.getAttribute('use-tailwind') !== 'false';
  
      const iconColor = this.calculateIconColor(baseColor);
  
      // Aplica clases de Tailwind si está activado
      if (useTailwind) {
        this.wrapper.className = 'wrapper relative flex items-center justify-center overflow-hidden';
        this.svgContainer.className = 'svg-container flex items-center justify-center';
      } else {
        // Asegura el centrado manual sin Tailwind
        this.wrapper.className = 'wrapper';
        this.svgContainer.className = 'svg-container';
        Object.assign(this.svgContainer.style, {
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'center',
        });
      }
  
      Object.assign(this.wrapper.style, {
        width: `${size}px`,
        height: `${size}px`,
        background: this.generateTonalGradient(baseColor),
        borderRadius: `${borderRadius}px`,
      });
  
      Object.assign(this.svgContainer.style, {
        color: iconColor,
        width: `${size * 0.6}px`,
        height: `${size * 0.6}px`,
      });
    }
  
    async updateIcon() {
      const iconPath = this.getAttribute('icon-path');
      if (!iconPath) return;
  
      try {
        const response = await fetch(iconPath);
        if (!response.ok) throw new Error('Failed to fetch icon');
        const svgText = await response.text();
        this.svgContainer.innerHTML = svgText;
  
        const svg = this.svgContainer.querySelector('svg');
        if (svg) {
          svg.style.fill = 'currentColor';
        }
      } catch (error) {
        console.error('Error loading SVG:', error);
      }
    }
  
    connectedCallback() {
      this.updateStyles();
      this.updateIcon();
    }
  
    attributeChangedCallback(name, oldValue, newValue) {
      if (oldValue === newValue) return;
  
      if (name === 'icon-path') {
        this.updateIcon();
      } else {
        this.updateStyles();
      }
    }
  }
  
  customElements.define('gradient-square', GradientSquareElement);
  