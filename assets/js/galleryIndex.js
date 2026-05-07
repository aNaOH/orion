const CDN_URL = "https://cdn.orion.moonnastd.com/community/gallery/";

async function loadMedia(mediaContainer, uuid) {
  if (!uuid) return;
  
  try {
    const mediaUrl = CDN_URL + uuid;
    mediaContainer.innerHTML = ""; // Limpiar spinner

    // Detección mejorada
    const isVideo = uuid.toLowerCase().includes("video") || uuid.toLowerCase().endsWith(".mp4") || uuid.toLowerCase().endsWith(".webm");
    
    if (isVideo) {
      const video = document.createElement("video");
      video.src = mediaUrl;
      video.controls = true;
      video.classList.add("w-full", "h-auto", "max-h-[70vh]", "bg-black");
      mediaContainer.appendChild(video);
    } else {
      const img = document.createElement("img");
      img.src = mediaUrl;
      img.alt = "Gallery content";
      img.classList.add("w-full", "h-auto", "object-contain");
      // Manejar error de carga (por si era video sin tag en uuid)
      img.onerror = async () => {
          if (!mediaContainer.dataset.retry) {
              mediaContainer.dataset.retry = "true";
              const response = await fetch(mediaUrl, { method: "HEAD" });
              const contentType = response.headers.get("Content-Type");
              if (contentType && contentType.includes("video")) {
                  mediaContainer.innerHTML = "";
                  const video = document.createElement("video");
                  video.src = mediaUrl;
                  video.controls = true;
                  video.classList.add("w-full", "h-auto", "max-h-[70vh]");
                  mediaContainer.appendChild(video);
              }
          }
      };
      mediaContainer.appendChild(img);
    }
  } catch (error) {
    console.error("Error al cargar el contenido multimedia:", error);
    mediaContainer.innerHTML = "<p class='text-gray-500 text-xs'>Error al cargar medios</p>";
  }
}

// Inicialización de tarjetas
const galleryCards = document.querySelectorAll('[data-gameid]');

galleryCards.forEach((card, index) => {
    const mediaContainer = card.querySelector('[data-galleryslot="media"]');
    const shareBtn = card.querySelector('[data-galleryslot="shareBtn"]');
    const shareLinkBox = card.querySelector('[data-galleryslot="shareLink"]');
    const linkInput = card.querySelector('[data-galleryslot="linkInput"]');
    const voteValue = card.querySelector('[data-galleryslot="value"]');
    const galleryVote = card.querySelector('gallery-vote');
    const gameId = card.dataset.gameid;
    const postId = shareBtn ? shareBtn.dataset.postid : null;

    if (mediaContainer) {
        loadMedia(mediaContainer, mediaContainer.dataset.uuid);
    }

    if (shareBtn && shareLinkBox && linkInput) {
        shareBtn.addEventListener("click", () => {
            shareLinkBox.classList.toggle("hidden");
            
            var hostname = window.location.origin;
            linkInput.value = hostname + "/communities/" + gameId + "/gallery/" + postId;
            linkInput.select();
            
            // Copiar al portapapeles opcionalmente
            try {
                navigator.clipboard.writeText(linkInput.value);
            } catch(e) {}
        });
    }

    if (galleryVote && postId) {
        galleryVote.addEventListener("valueChange", (event) => {
            const newValue = event.detail.value;
            const previousValue = event.detail.previousValue;
            const tokenElement = document.getElementById("tript_token");
            
            if (!tokenElement) {
                console.error("Token CSRF no encontrado");
                return;
            }

            $.ajax({
                url: "/api/communities/vote/" + postId,
                type: "POST",
                data: {
                    previousValue,
                    newValue,
                    token: tokenElement.value,
                },
                success: function (response) {
                    if (voteValue) voteValue.innerHTML = response.new_value;
                },
                error: function (xhr) {
                    console.error("Error en la votación:", xhr.responseJSON);
                    galleryVote.setAttribute("value", previousValue);
                    alert("No se pudo registrar tu voto. Por favor, intenta de nuevo.");
                },
            });
        });
    }
});
