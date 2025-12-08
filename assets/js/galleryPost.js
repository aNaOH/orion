let urlSplitted = document.URL.split("/");
let game = urlSplitted[urlSplitted.length - 3];

const mediaContainer = $('*[data-galleryslot="media"]')[0];

async function loadMedia(mediaContainer, uuid) {
  try {
    // Aquí la URL del archivo se construye a partir del UUID
    const mediaUrl = `https://cdn.orion.moonnastd.com/gallery/` + uuid;

    const response = await fetch(mediaUrl, { method: "HEAD" }); // Usamos 'HEAD' para obtener solo los encabezados
    const contentType = response.headers.get("Content-Type");

    mediaContainer.innerHTML = ""; // Limpiar contenido anterior

    if (contentType.includes("image")) {
      // Si es una imagen
      const img = document.createElement("img");
      img.src = mediaUrl;
      img.alt = "Media content";
      img.classList.add("w-full", "h-auto", "rounded-lg");
      mediaContainer.appendChild(img);
    } else if (contentType.includes("video")) {
      // Si es un video
      const video = document.createElement("video");
      video.src = mediaUrl;
      video.controls = true;
      video.classList.add("w-full", "h-auto", "rounded-lg");
      mediaContainer.appendChild(video);
    } else {
      mediaContainer.innerHTML = "<p>Tipo de archivo no soportado</p>";
    }
  } catch (error) {
    console.error("Error al cargar el contenido multimedia:", error);
  }
}

loadMedia(mediaContainer, mediaContainer.dataset.uuid);

const shareBtn = $('*[data-galleryslot="shareBtn"]')[0];
const linkBox = $('*[data-galleryslot="shareLink"]')[0];
const linkInput = $('*[data-galleryslot="linkInput"]')[0];
const value = $('*[data-galleryslot="value"]')[0];
const galleryVote = $("gallery-vote")[0];

function onShareBtnClick(postId) {
  // Muestra la caja de texto
  linkBox.classList.toggle("hidden"); // Alterna la visibilidad de la caja

  // Asegúrate de que el contenedor padre tenga la clase 'relative'
  linkBox.parentElement.classList.toggle("relative");

  // Posiciona linkBox a la derecha
  linkBox.classList.toggle("absolute"); // Se posiciona a la derecha del contenedor
  linkBox.classList.toggle("left-10");
  linkBox.classList.toggle("translate-y-[-25%]");

  // Si el enlace es dinámico, cambia el valor del input aquí (opcional)
  var hostname = window.location.hostname;
  if (hostname != "localhost") hostname = "www." + hostname;
  linkInput.value = hostname + "/communities/" + game + "/gallery/" + postId; // Cambia a tu enlace dinámico
  linkInput.select();
}

shareBtn.addEventListener("click", function () {
  onShareBtnClick(shareBtn.dataset.postid);
});

if (galleryVote) {
  galleryVote.addEventListener("valueChange", function (event) {
    const postId = shareBtn.dataset.postid;
    const newValue = event.detail.value;
    const previousValue = event.detail.previousValue;

    const token = document.getElementById("tript_token").value;

    $.ajax({
      url: "/api/communities/vote/" + postId, // The URL to which the request is sent
      type: "POST", // The HTTP method to use for the request (GET, POST, etc.)
      data: {
        previousValue,
        newValue,
        token,
      }, // Data to be sent to the server
      success: function (response) {
        values[index].innerHTML = response.new_value;
      },
      error: function (xhr, status, error) {
        const info = xhr.responseJSON;
        console.log(info);
        galleryVotes.setAttribute("value", previousValue);
      },
    });
  });
}
