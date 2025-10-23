const resetField = (fieldId) => {
  const field = document.getElementById(fieldId);
  const errorText = document.getElementById(fieldId + "Error");

  // Remover estilos de error
  field.classList.remove("border-red-500", "ring-red-500");
  field.classList.add("border-gray-600");
  if (errorText) {
    errorText.classList.add("hidden");
    errorText.innerHTML = "";
  }
};

const showError = (errorInfo) => {
  const field = document.getElementById(errorInfo.field);
  const errorText = document.getElementById(errorInfo.field + "Error");

  // Aplicar estilos de error
  field.classList.remove("border-gray-600");
  field.classList.add("border-red-500", "ring-1", "ring-red-500");

  // Mostrar mensaje
  if (errorText) {
    errorText.innerHTML = errorInfo.message;
    errorText.classList.remove("hidden");
    errorText.classList.add("text-red-500");
  }

  field.focus();
};
