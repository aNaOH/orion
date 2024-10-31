class GameCommunity extends HTMLElement {
    constructor() {
      super();
  
      // Crear shadow DOM
      this.shadow = this.attachShadow({ mode: "open" });
  
      // Crear contenedor principal
      this.mainDiv = document.createElement("div");
      this.mainDiv.classList.add("col-lg-4", "mb-4", "mb-lg-0");
  
      // Crear contenedor de imagen con máscara
      this.bgImageDiv = document.createElement("div");
      this.bgImageDiv.classList.add("bg-image", "rounded-6");
  
      // Crear imagen
      this.img = document.createElement("img");
      this.img.classList.add("w-100");
      this.img.alt = "Alternative text"; // Texto alternativo fijo por defecto
  
      // Crear máscara
      const maskDiv = document.createElement("div");
      maskDiv.classList.add("mask");
      maskDiv.style.background = `
        linear-gradient(
          to bottom,
          hsla(0, 0%, 0%, 0),
          hsla(263, 80%, 20%, 0.5)
        )
      `;
  
      // Crear contenedor de texto
      const textContainerDiv = document.createElement("div");
      textContainerDiv.classList.add(
        "bottom-0",
        "d-flex",
        "align-items-end",
        "h-100",
        "text-center",
        "justify-content-center"
      );
  
      // Crear contenedor de título
      const titleDiv = document.createElement("div");
      this.titleText = document.createElement("h2");
      titleText.classList.add("fw-bold", "text-white", "mb-4");
      titleText.textContent = "";
  
      // Armar la estructura
      titleDiv.appendChild(titleText);
      textContainerDiv.appendChild(titleDiv);
      maskDiv.appendChild(textContainerDiv);
      this.bgImageDiv.appendChild(this.img);
      this.bgImageDiv.appendChild(maskDiv);
      this.mainDiv.appendChild(this.bgImageDiv);
  
      // Añadir todo al shadow DOM
      this.shadow.appendChild(this.mainDiv);
    }
  
    // Declarar atributos observables
    static get observedAttributes() {
      return ["game-title", "game-id"];
    }
  
    // Manejar cambios en los atributos
    attributeChangedCallback(name, oldValue, newValue) {
      if (name === "game-id") {
        this.img.src = "/media/game/cover/" + newValue;
      }
      if (name === "game-title") {
        this.titleText.textContent = newValue;
      }
    }
}
  
customElements.define("game-community", GameCommunity);