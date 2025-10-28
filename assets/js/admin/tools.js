const sendEmailForm = document.getElementById("sendTestEmailForm");

sendEmailForm.addEventListener("submit", async (event) => {
  event.preventDefault();
  const formData = new FormData(sendEmailForm);
  const response = await fetch("/api/admin/tools", {
    method: "POST",
    body: JSON.stringify({
      tool: "email",
      email: formData.get("email"),
      template: formData.get("template"),
    }),
  });

  if (response.ok) {
    alert("Email sent successfully!");
  } else {
    alert("Failed to send email.");
  }
});
