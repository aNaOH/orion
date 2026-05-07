class GalleryVote extends HTMLElement {
  constructor() {
    super();
    this.attachShadow({ mode: 'open' });
    this.value = 0;
    this.popupOpen = false;
  }

  connectedCallback() {
    this.value = parseInt(this.getAttribute('value')) || 0;
    this.render();
    this.addEventListeners();
  }

  render() {
    this.shadowRoot.innerHTML = `
    <style>
      :host {
        display: inline-block;
        position: relative;
        font-family: inherit;
      }

      .wrapper {
        position: relative;
        display: flex;
        align-items: center;
      }

      .svg-placeholder {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 14px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
      }

      .svg-placeholder:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: #DEAB18;
        transform: translateY(-2px);
      }

      .svg-placeholder img {
        width: 24px;
        height: 24px;
        transition: transform 0.3s ease;
      }

      .svg-placeholder:hover img {
        transform: scale(1.1);
      }

      .popup {
        display: flex;
        position: absolute;
        bottom: calc(100% + 12px);
        right: 0;
        background: rgba(13, 17, 23, 0.85);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(222, 171, 24, 0.3);
        border-radius: 18px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        padding: 8px;
        z-index: 1000;
        gap: 8px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px) scale(0.95);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      }

      .popup.open {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
      }

      button {
        position: relative;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border-radius: 12px;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
      }

      button:hover {
        background: rgba(222, 171, 24, 0.1);
        transform: scale(1.1);
      }

      button img {
        width: 22px;
        height: 22px;
      }

      /* Custom Tooltip */
      .tooltip {
        position: absolute;
        bottom: calc(100% + 10px);
        left: 50%;
        transform: translateX(-50%) translateY(5px);
        background: #DEAB18;
        color: #000;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: all 0.2s ease;
        box-shadow: 0 4px 10px rgba(222, 171, 24, 0.3);
      }

      .tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 5px solid transparent;
        border-top-color: #DEAB18;
      }

      button:hover .tooltip {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
      }
    </style>
    <div class="wrapper">
      <div class="svg-placeholder" title="Votar">
        ${this.getSVG(this.value)}
      </div>

      <div class="popup">
        ${this.getIcons()}
      </div>
    </div>
    `;

    this.svgPlaceholder = this.shadowRoot.querySelector('.svg-placeholder');
    this.popup = this.shadowRoot.querySelector('.popup');
    this.buttons = this.shadowRoot.querySelectorAll('button');
  }

  addEventListeners() {
    this.svgPlaceholder.addEventListener('click', (e) => {
      e.stopPropagation();
      this.togglePopup();
    });

    this.buttons.forEach((button) => {
      button.addEventListener('click', (e) => {
        e.stopPropagation();
        this.handleIconClick(e);
      });
    });

    // Cierre al hacer clic fuera usando composedPath para Shadow DOM
    window.addEventListener('click', (e) => {
      if (!e.composedPath().includes(this)) {
        this.popupOpen = false;
        this.updatePopupState();
      }
    });
  }

  static get observedAttributes() {
    return ['value'];
  }

  attributeChangedCallback(name, oldValue, newValue) {
    if (name === 'value' && oldValue !== newValue) {
      this.value = parseInt(newValue);
      this.updateSVG();
    }
  }

  updateSVG() {
    if (this.svgPlaceholder) {
      this.svgPlaceholder.innerHTML = this.getSVG(this.value);
    }
  }

  togglePopup() {
    this.popupOpen = !this.popupOpen;
    this.updatePopupState();
  }

  updatePopupState() {
    if (this.popupOpen) {
      this.popup.classList.add('open');
    } else {
      this.popup.classList.remove('open');
    }
  }

  getIcons() {
    const options = [
      { val: -2, label: 'Lo odio', img: 'hate' },
      { val: -1, label: 'No me gusta', img: 'dislike' },
      { val: 0, label: 'Neutral', img: 'novote' },
      { val: 1, label: 'Me gusta', img: 'like' },
      { val: 2, label: 'Lo amo', img: 'love' }
    ];

    return options.map(opt => `
      <button data-value="${opt.val}">
        <span class="tooltip">${opt.label}</span>
        <img src="/assets/img/gallery/button/${opt.img}.svg" alt="${opt.label}">
      </button>
    `).join('');
  }

  getSVG(value) {
    const imgs = {
      '-2': 'hate',
      '-1': 'dislike',
      '1': 'like',
      '2': 'love'
    };
    const name = imgs[value] || 'novote';
    return `<img src="/assets/img/gallery/${name}.svg" alt="Vote status">`;
  }

  handleIconClick(event) {
    const button = event.target.closest('button');
    if (!button) return;

    const newValue = parseInt(button.getAttribute('data-value'));
    const previousValue = this.value;

    this.value = newValue;
    this.setAttribute('value', this.value);
    this.updateSVG();
    
    this.popupOpen = false;
    this.updatePopupState();

    this.dispatchEvent(new CustomEvent('valueChange', {
      detail: { value: this.value, previousValue },
      bubbles: true,
      composed: true,
    }));
  }
}

customElements.define('gallery-vote', GalleryVote);
  