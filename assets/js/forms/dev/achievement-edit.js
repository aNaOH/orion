let formAchievement = document.getElementById("editAchievementForm");
let submitAchievement = document.getElementById("submitButton");
let typeSelect = document.getElementById("type");
let statContainer = document.getElementById("statContainer");

typeSelect.onchange = () => {
  if (typeSelect.value === "1") {
    statContainer.classList.remove("hidden");
  } else {
    statContainer.classList.add("hidden");
  }
};

const toggleSpinner = (button, show, text = "Procesando...") => {
  button.disabled = show;
  button.innerHTML = show
    ? `<div class="flex items-center justify-center gap-2">
         <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>${text}
       </div>`
    : button.dataset.defaultText || "Enviar";
};

formAchievement.onsubmit = (e) => {
  e.preventDefault();
  toggleSpinner("submitButton", "spinnerAchievement", true);

  const fields = e.target.elements;

  resetField("name");
  resetField("description");
  resetField("type");
  resetField("stat");
  resetField("stat_value");
  resetField("icon");
  resetField("lockedIcon");

  let formData = new FormData();

  formData.append("tript_token", fields["tript_token"].value);
  formData.append("achievement", fields["achievement"].value);
  formData.append("game", fields["game"].value);
  formData.append("name", fields["name"].value);
  formData.append("description", fields["description"].value);
  formData.append("type", fields["type"].value);
  formData.append("stat", fields["stat"].value);
  formData.append("stat_value", fields["stat_value"].value);

  if (document.getElementById("icon").getFileInput()) {
    formData.append("icon", document.getElementById("icon").getFileInput()[0]);
  }

  if (document.getElementById("lockedIcon").getFileInput()) {
    formData.append(
      "lockedIcon",
      document.getElementById("lockedIcon").getFileInput()[0],
    );
  }

  submitAchievement.classList.add("disabled");

  $.ajax({
    url: "/api/dev/achievement-edit",
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    success: function () {
      Orion.showToast('success', 'Logro actualizado correctamente');
      setTimeout(() => {
        location.href =
          "/dev/panel/games/" + fields["game"].value + "/community/achievements";
      }, 1500);
    },
    error: function (xhr) {
      const info = xhr.responseJSON;
      console.log(info);
      Orion.showToast('error', info?.message || 'Error al actualizar logro');
      if (typeof showError === 'function') showError(info);
      toggleSpinner("submitButton", "spinnerAchievement", false);
      submitAchievement.classList.remove("disabled");
    },
  });
};
