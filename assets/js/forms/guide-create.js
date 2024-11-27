document.addEventListener("DOMContentLoaded", () => {
    const selectElement = document.getElementById("guideType");
    const container = document.getElementById("guideContainer");
    const icon = document.getElementById("guideIcon");

    container.style.backgroundColor = LightenDarkenColor(selectElement.options[selectElement.selectedIndex].getAttribute("data-color"), -50);

    selectElement.addEventListener("change", (event) => {
        // Obtener la opción seleccionada
        const selectedOption = event.target.options[event.target.selectedIndex];
        // Obtener el atributo data-color
        const color = selectedOption.getAttribute("data-color");
        if (color) {
            container.style.backgroundColor = LightenDarkenColor(color, -50);
            icon.setAttribute('base-color', color);

        }

        const iconPath = selectedOption.getAttribute("data-icon");
        if (iconPath) {
            icon.setAttribute('icon-path', iconPath);
        }
    });
});
