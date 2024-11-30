class GalleryVote extends HTMLElement {
    constructor() {
      super();
      this.attachShadow({ mode: 'open' });
    }

    connectedCallback(){
      this.value = parseInt(this.getAttribute('value')) || 0;
      this.popupOpen = false;

      this.render();
      this.addEventListeners();
    }

    render(){
      // Contenido del shadow DOM
      this.shadowRoot.innerHTML = `
      <style>
        :host {
          display: inline-block;
          position: relative;
        }
        .popup {
          display: none;
          position: absolute;
          top: 50%;
          right: 100%;
          transform: translateY(-50%);
          background-color: #DEAB18;
          border: 1px solid #C88A15;
          border-radius: 0.5rem;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
          padding: 0.5rem;
          z-index: 100;
          gap: 0.5rem;
        }
        .popup.open,
        .wrapper:hover .popup {
          display: flex;
        }
        .svg-placeholder {
          width: 2.2rem;
          height: 2.2rem;
          display: flex;
          align-items: center;
          justify-content: center;
          background-color: #DEAB18;
          border-radius: 50%;
          cursor: pointer;
          transition: background-color 0.3s;
        }
        .svg-placeholder:hover {
          background-color: #C88A15;
        }
        button {
          position: relative; /* Necesario para tooltip */
          width: 2rem;
          height: 2rem;
          display: flex;
          align-items: center;
          justify-content: center;
          background-color: #1B2A49;
          border-radius: 50%;
          border: none;
          cursor: pointer;
          font-size: 1.25rem;
          transition: background-color 0.2s;
        }
        button:hover {
          background-color: #15213a;
        }

        /* Estilo del tooltip */
        .tooltip {
          position: absolute;
          bottom: 110%; /* Posiciona el tooltip encima del botón */
          left: 50%;
          transform: translateX(-50%);
          background-color: rgba(0, 0, 0, 0.8);
          color: white;
          padding: 0.3rem 0.6rem;
          border-radius: 0.25rem;
          font-size: 0.75rem;
          white-space: nowrap;
          opacity: 0;
          pointer-events: none;
          transition: opacity 0.2s ease-in-out;
        }
        button:hover .tooltip {
          opacity: 1;
          pointer-events: auto;
        }
      </style>
      <div class="wrapper">
        <!-- Icono principal -->
        <div class="svg-placeholder">${this.getSVG(this.value)}</div>

        <!-- Popup con opciones -->
        <div class="popup">
          ${this.getIcons()}
        </div>
      </div>
      `;

      // Referencias a elementos
      this.svgPlaceholder = this.shadowRoot.querySelector('.svg-placeholder');
      this.popup = this.shadowRoot.querySelector('.popup');
      this.buttons = this.shadowRoot.querySelectorAll('button');
    }

    addEventListeners() {
      // Eventos
      this.svgPlaceholder.addEventListener('click', () => this.togglePopup());
      this.buttons.forEach((button) => {
        button.addEventListener('click', (e) => this.handleIconClick(e));
      });
      document.addEventListener('click', (e) => this.handleDocumentClick(e));
    }
  
    static get observedAttributes() {
      return ['value'];
    }
  
    // Actualizar el atributo value dinámicamente
    attributeChangedCallback(name, oldValue, newValue) {
      if (name === 'value') {
        this.value = parseInt(newValue);
        this.updateSVG();
      }
    }
  
    // Actualizar el SVG principal
    updateSVG() {
      this.svgPlaceholder.innerHTML = this.getSVG(this.value);
    }
  
    // Mostrar u ocultar el popup con clic
    togglePopup() {
      this.popupOpen = !this.popupOpen;
      this.updatePopupState();
    }
  
    // Controlar el estado del popup
    updatePopupState() {
      if (this.popupOpen) {
        this.popup.classList.add('open');
      } else {
        this.popup.classList.remove('open');
      }
    }
  
    // Ocultar el popup si se hace clic fuera del componente
    handleDocumentClick(event) {
      if (!this.contains(event.target) && this.popupOpen) {
        this.popupOpen = false;
        this.updatePopupState();
      }
    }
  
    // Generar botones con íconos y tooltips
    getIcons() {
      return `
        <button data-value="-2">
          <span class="tooltip">Lo odio</span>
          <img src="/assets/img/gallery/button/hate.svg" alt="Hate"></img>
        </button>
        <button data-value="-1">
          <span class="tooltip">No me gusta</span>
          <img src="/assets/img/gallery/button/dislike.svg" alt="Dislike"></img>
        </button>
        <button data-value="0">
          <span class="tooltip">Ni fu ni fa</span>
          <img src="/assets/img/gallery/button/novote.svg" alt="No vote"></img>
        </button>
        <button data-value="1">
          <span class="tooltip">Me gusta</span>
          <img src="/assets/img/gallery/button/like.svg" alt="Like"></img>
        </button>
        <button data-value="2">
          <span class="tooltip">Lo amo</span>
          <img src="/assets/img/gallery/button/love.svg" alt="Love"></img>
        </button>
      `;
    }
  
    // Obtener el SVG principal según el valor
    getSVG(value) {
      switch (value) {
        case -2: return '<img src="/assets/img/gallery/hate.svg" alt="Hate"></img>';
        case -1: return '<img src="/assets/img/gallery/dislike.svg" alt="Dislike"></img>';
        case 1: return '<img src="/assets/img/gallery/like.svg" alt="Like"></img>';
        case 2: return '<img src="/assets/img/gallery/love.svg" alt="Love"></img>';
        default: return '<img src="/assets/img/gallery/novote.svg" alt="No vote"></img>';
      }
    }
  
    // Manejar clics en los botones
    handleIconClick(event) {
        // Buscar el botón más cercano al elemento clickeado
        const button = event.target.closest('button');
        if (!button) return;
      
        // Obtener el valor del atributo data-value
        const newValue = parseInt(button.getAttribute('data-value'));
        if (isNaN(newValue)) return;
      
        // Actualizar el valor del componente
        const previousValue = this.value ?? 0;
        this.value = newValue;
        this.setAttribute('value', this.value);
      
        // Cambiar el ícono principal
        this.updateSVG();
      
        // Cerrar el popup
        this.popupOpen = false;
        this.updatePopupState(); // Cierra el popup automáticamente
      
        // Emitir el evento de cambio de valor
        this.dispatchEvent(
          new CustomEvent('valueChange', {
            detail: { value: this.value, previousValue },
            bubbles: true,
            composed: true,
          })
        );
      }      
      
  }
  
  // Definir el Custom Element
  customElements.define('gallery-vote', GalleryVote);
  