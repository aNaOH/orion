let urlSplitted = document.URL.split('/');
let game = urlSplitted[urlSplitted.length-3];

mediaContainers = $('*[data-galleryslot="media"]');

async function loadMedia(mediaContainer, uuid) {
    try {
        // Aquí la URL del archivo se construye a partir del UUID
        const mediaUrl = `/media/gallery/` + uuid;
        
        const response = await fetch(mediaUrl, { method: 'HEAD' }); // Usamos 'HEAD' para obtener solo los encabezados
        const contentType = response.headers.get('Content-Type');

        mediaContainer.innerHTML = '';  // Limpiar contenido anterior

        if (contentType.includes('image')) {
            // Si es una imagen
            const img = document.createElement('img');
            img.src = mediaUrl;
            img.alt = "Media content";
            img.classList.add('w-full', 'h-auto', 'rounded-lg');
            mediaContainer.appendChild(img);
        } else if (contentType.includes('video')) {
            // Si es un video
            const video = document.createElement('video');
            video.src = mediaUrl;
            video.controls = true;
            video.classList.add('w-full', 'h-auto', 'rounded-lg');
            mediaContainer.appendChild(video);
        } else {
            mediaContainer.innerHTML = "<p>Tipo de archivo no soportado</p>";
        }
    } catch (error) {
        console.error('Error al cargar el contenido multimedia:', error);
    }
}

for (const media of mediaContainers) {
    loadMedia(media, media.dataset.uuid);  
}

shareBtns = $('*[data-galleryslot="shareBtn"]');
shareLinks = $('*[data-galleryslot="shareLink"]');
linkInputs = $('*[data-galleryslot="linkInput"]');
galleryVotes = $('gallery-vote');

function onShareBtnClick(index, postId) {
    var linkBox = shareLinks[index];
    var linkInput = linkInputs[index];

    // Muestra la caja de texto
    linkBox.classList.toggle("hidden");  // Alterna la visibilidad de la caja

    // Asegúrate de que el contenedor padre tenga la clase 'relative'
    linkBox.parentElement.classList.toggle("relative");

    // Posiciona linkBox a la derecha
    linkBox.classList.toggle("absolute");  // Se posiciona a la derecha del contenedor
    linkBox.classList.toggle("left-10");  
    linkBox.classList.toggle("translate-y-[-25%]");  

    // Si el enlace es dinámico, cambia el valor del input aquí (opcional)
    var hostname = window.location.hostname;
    if (hostname != "localhost") hostname = "www." + hostname;
    linkInput.value = hostname + "/communities/" + game + "/gallery/" + postId;  // Cambia a tu enlace dinámico
    linkInput.select();
}


for (let index = 0; index < shareBtns.length; index++) {
    const shareBtn = shareBtns[index];
    shareBtn.addEventListener("click", function() {
        onShareBtnClick(index, shareBtn.dataset.postid);
    });

    galleryVotes[index].addEventListener('valueChange', (event) => {
        const postId = shareBtn.dataset.postid;
        const newValue = event.detail.value;
        const previousValue = event.detail.previousValue;

        const token = document.getElementById("tript_token").value;

        console.log("/api/communities/vote/" + postId, previousValue, newValue, token);
    });
}