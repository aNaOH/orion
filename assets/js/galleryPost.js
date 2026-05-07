const CDN_URL = "https://cdn.orion.moonnastd.com/community/gallery/";

async function loadMedia(mediaContainer, uuid) {
  if (!uuid) return;
  
  try {
    const mediaUrl = CDN_URL + uuid;
    mediaContainer.innerHTML = ""; // Limpiar spinner

    // Detección inicial por nombre
    const isVideo = uuid.toLowerCase().includes("video") || uuid.toLowerCase().endsWith(".mp4") || uuid.toLowerCase().endsWith(".webm");
    
    if (isVideo) {
      renderVideo(mediaContainer, mediaUrl);
    } else {
      const img = document.createElement("img");
      img.src = mediaUrl;
      img.alt = "Gallery content";
      img.classList.add("w-full", "max-h-[600px]", "object-contain", "rounded-2xl", "shadow-2xl", "mx-auto", "block");
      
      // Si la imagen falla, intentamos cargar como vídeo (detección por error)
      img.onerror = async () => {
          if (!mediaContainer.dataset.retry) {
              mediaContainer.dataset.retry = "true";
              // Intentar verificar tipo real solo si falla la imagen
              try {
                  const response = await fetch(mediaUrl, { method: "HEAD" });
                  const contentType = response.headers.get("Content-Type");
                  if (contentType && contentType.includes("video")) {
                      renderVideo(mediaContainer, mediaUrl);
                  } else {
                      mediaContainer.innerHTML = "<p class='text-gray-500'>No se pudo cargar la imagen</p>";
                  }
              } catch (e) {
                  // Si falla el fetch (CORS), intentamos renderizar video de todos modos como último recurso
                  renderVideo(mediaContainer, mediaUrl);
              }
          }
      };
      mediaContainer.appendChild(img);
    }
  } catch (error) {
    console.error("Error al cargar el contenido multimedia:", error);
    mediaContainer.innerHTML = "<p class='text-gray-500'>Error al cargar contenido</p>";
  }
}

function renderVideo(container, url) {
    container.innerHTML = "";
    const video = document.createElement("video");
    video.src = url;
    video.controls = true;
    video.classList.add("w-full", "max-h-[600px]", "rounded-2xl", "shadow-2xl", "bg-black", "mx-auto", "block");
    container.appendChild(video);
}

// Inicialización
const card = document.querySelector('[data-gameid]');

if (card) {
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
        shareBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            shareLinkBox.classList.toggle("hidden");
            var hostname = window.location.origin;
            linkInput.value = hostname + "/communities/" + gameId + "/gallery/" + postId;
            linkInput.select();
            try { navigator.clipboard.writeText(linkInput.value); } catch(err) {}
        });
    }

    if (galleryVote && postId) {
        galleryVote.addEventListener("valueChange", (event) => {
            const newValue = event.detail.value;
            const previousValue = event.detail.previousValue;
            const tokenElement = document.getElementById("tript_token") || document.querySelector('input[name="token"]');
            
            if (!tokenElement) return;

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
                    galleryVote.setAttribute("value", previousValue);
                },
            });
        });
    }
}
