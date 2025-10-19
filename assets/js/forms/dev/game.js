let form = document.getElementById("newGameForm");
let asEditor = document.getElementById("asEditor");
let developerNameContainer = document.getElementById("developerNameContainer");
let submit = document.getElementById("submitButton");
let spinner = document.getElementById("spinner");

asEditor.onchange = () => {
  if (asEditor.checked) {
    developerNameContainer.classList.remove("hidden");
  } else {
    developerNameContainer.classList.add("hidden");
  }
};

form.onsubmit = async (e) => {
  e.preventDefault();

  let fields = e.target.elements;

  resetField("title");
  resetField("shortDescription");
  resetField("developerName");

  // Desactivar botón y mostrar spinner
  submit.disabled = true;
  spinner.classList.remove("hidden");

  try {
    const response = await fetch("/api/dev/game", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        title: fields["title"].value,
        shortDescription: fields["shortDescription"].value,
        asEditor: asEditor.checked,
        developerName: fields["developerName"].value,
      }),
    });

    if (response.ok) {
      //window.location.href = "/dev/panel/games";
      return;
    }

    const info = await response.json();
    showError(info);
  } catch (err) {
    console.error("Error:", err);
  } finally {
    submit.disabled = false;
    spinner.classList.add("hidden");
  }
};
