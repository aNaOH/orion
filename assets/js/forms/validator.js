const resetField = (fieldId) => {
    document.getElementById(fieldId).classList.remove("is-invalid");
    document.getElementById(fieldId + "Error").innerHTML = "";
}

const showError = (errorInfo) => {
    const field = document.getElementById(errorInfo.field);
    field.classList.add("is-invalid");
    field.focus();
    document.getElementById(errorInfo.field + "Error").innerHTML = errorInfo.message;
}