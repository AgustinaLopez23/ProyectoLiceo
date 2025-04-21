// Funciones de Lightbox
const lightbox = document.getElementById('lightbox');
const lightboxImg = document.getElementById('lightbox-img');
const lightboxDescription = document.getElementById('lightbox-description');
const lightboxPrev = document.querySelector('.lightbox-prev'); // Select the previous arrow element
const lightboxNext = document.querySelector('.lightbox-next'); // Select the next arrow element

let currentGalleryImages = [];
let currentImageIndex = 0;
let currentGalleryId = null;

function openLightbox(imgSrc, description, galleryId) {
    if (!lightbox || !lightboxImg || !lightboxDescription) {
        console.error('Error: No se encontró uno o más elementos de lightbox.');
        return;
    }

    lightboxImg.src = imgSrc;
    lightboxDescription.textContent = description;
    lightbox.style.display = 'flex';
    currentGalleryId = galleryId;
    currentGalleryImages = getGalleryImages(galleryId);
    currentImageIndex = currentGalleryImages.findIndex(img => img.src === imgSrc);

    // Show/Hide arrows based on the number of images
    if (currentGalleryImages.length <= 1) {
        if (lightboxPrev) lightboxPrev.style.display = 'none'; // Hide prev arrow if it exists
        if (lightboxNext) lightboxNext.style.display = 'none'; // Hide next arrow if it exists
    } else {
        if (lightboxPrev) lightboxPrev.style.display = 'block'; // Show prev arrow if it exists
        if (lightboxNext) lightboxNext.style.display = 'block'; // Show next arrow if it exists
    }

    // Asegurar que solo haya un listener para keydown
    document.removeEventListener('keydown', handleKeydown);
    document.addEventListener('keydown', handleKeydown);
}

function closeLightbox() {
    if (lightbox) {
        lightbox.style.display = 'none';
        document.removeEventListener('keydown', handleKeydown);
        currentGalleryImages = [];
        currentImageIndex = 0;
        currentGalleryId = null;
        if (lightboxPrev) lightboxPrev.style.display = 'none'; // Hide prev arrow if it exists
        if (lightboxNext) lightboxNext.style.display = 'none'; // Hide next arrow if it exists
    }
}

function getGalleryImages(galleryId) {
    const gallery = document.getElementById(galleryId);
    if (gallery) {
        const images = gallery.querySelectorAll('.item img');
        return Array.from(images).map(img => ({
            src: img.src,
            alt: img.alt,
            description: img.getAttribute('data-description')
        }));
    }
    return [];
}

function changeLightboxImage(direction) {
    if (currentGalleryImages.length <= 1 || currentGalleryId === null) {
        return; // No hacer nada si no hay galería o solo una imagen
    }

    currentImageIndex += direction;

    if (currentImageIndex < 0) {
        currentImageIndex = currentGalleryImages.length - 1; // Volver a la última imagen
    } else if (currentImageIndex >= currentGalleryImages.length) {
        currentImageIndex = 0; // Volver a la primera imagen
    }

    lightboxImg.src = currentGalleryImages[currentImageIndex].src;
    lightboxImg.alt = currentGalleryImages[currentImageIndex].alt;
    lightboxDescription.textContent = currentGalleryImages[currentImageIndex].description;
}

function handleKeydown(event) {
    if (event.key === 'Escape') {
        closeLightbox();
    } else if (event.key === 'ArrowLeft') {
        changeLightboxImage(-1);
    } else if (event.key === 'ArrowRight') {
        changeLightboxImage(1);
    }
}

window.addEventListener('load', () => {
    const galleries = document.querySelectorAll('.gallery'); // Selecciona todos los contenedores de galería

    galleries.forEach(gallery => {
        const galleryId = gallery.id || 'gallery-' + Math.random().toString(36).substring(7); // Generar ID si no tiene
        gallery.id = galleryId; // Asignar ID a la galería

        const images = gallery.querySelectorAll('.item img');
        images.forEach(image => {
            image.addEventListener('click', () => {
                const imgSrc = image.src;
                const description = image.getAttribute('data-description');
                const galleryElement = image.closest('.gallery');
                const currentGalleryId = galleryElement ? galleryElement.id : null;
                openLightbox(imgSrc, description, currentGalleryId); // Pasar el ID de la galería
            });
        });
    });

    if (lightbox) {
        lightbox.addEventListener('click', (event) => {
            if (event.target === lightbox) {
                closeLightbox();
            }
        });
    }

    // Event listeners for the navigation arrows
    if (lightboxPrev) {
        lightboxPrev.addEventListener('click', () => {
            changeLightboxImage(-1);
        });
    }
    if (lightboxNext) {
        lightboxNext.addEventListener('click', () => {
            changeLightboxImage(1);
        });
    }
});

// Sección de Blog - Manejo de comentarios
const comentariosFormularios = document.querySelectorAll('.comentarios form');
comentariosFormularios.forEach(formulario => {
    formulario.addEventListener('submit', evento => {
        evento.preventDefault();

        const textarea = formulario.querySelector('textarea');
        const comentario = textarea.value.trim();

        if (comentario !== '') {
            const comentariosLista = formulario.parentNode.querySelector('.chat-messages');
            if (!comentariosLista) return;

            const comentarioElemento = document.createElement('div');
            comentarioElemento.classList.add('chat-message');
            comentarioElemento.textContent = comentario; // Usar textContent
            comentariosLista.appendChild(comentarioElemento);
            textarea.value = '';
        }
    });
});

// Animación de tarjetas de planes
const animateElement = (element, delay) => {
    setTimeout(() => {
        element.classList.add('animated');
    }, delay);
};

const planCards = document.querySelectorAll('.plan-card');
planCards.forEach((card, index) => {
    animateElement(card, index * 200);
});



// Responsive Design
const getScreenSize = () => ({
    width: window.innerWidth || document.documentElement.clientWidth,
    height: window.innerHeight || document.documentElement.clientHeight
});

const applyResponsiveStyles = () => {
    const screenSize = getScreenSize();
    const container = document.querySelector('.container');
    if (!container) return;

    if (screenSize.width < 768) {
        container.style.maxWidth = '100%';
        container.style.padding = '20px';
    } else if (screenSize.width < 992) {
        container.style.maxWidth = '720px';
        container.style.margin = '0 auto';
    } else if (screenSize.width < 1200) {
        container.style.maxWidth = '960px';
    } else {
        container.style.maxWidth = '1140px';
    }
};

// Aplicar estilos responsivos al cargar y redimensionar
window.addEventListener('load', applyResponsiveStyles);
window.addEventListener('resize', applyResponsiveStyles);

// **Corrección Importante para las Imágenes del Blog (Asumiendo que las imágenes del blog NO están dentro de las galerías del lightbox):**
document.addEventListener('DOMContentLoaded', () => {
    const blogImages = document.querySelectorAll('.blog-card a img'); // Selecciona las imágenes dentro de los enlaces de las tarjetas del blog

    blogImages.forEach(img => {
        // Evitar que la imagen dentro del enlace del blog active el lightbox de la galería
        img.addEventListener('click', (event) => {
            event.stopPropagation(); // Detiene la propagación del evento click al elemento padre (<a>)
            
        });
    });
});



/* Funcion para el like */

document.addEventListener('DOMContentLoaded', () => {
    const likeButtons = document.querySelectorAll('.like-btn');

    likeButtons.forEach(button => {
        button.addEventListener('click', async () => {
            const imageId = button.dataset.imageId;
            const tablaOrigen = button.dataset.tablaOrigen;
            const likeCountSpan = button.nextElementSibling;
            const heartIcon = button.querySelector('.bi-heart');

            if (!imageId || !tablaOrigen) {
                console.error('Error: Faltan data-image-id o data-tabla-origen en el botón de like.');
                return;
            }

            try {
                const response = await fetch('http://localhost/ProyectoLiceo/procesar_like.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `item_id=${imageId}&tabla_origen=${tablaOrigen}`
                });

                if (!response.ok) {
                    console.error('Error en la respuesta del servidor:', response.status);
                    return;
                }

                const data = await response.json();
                console.log('Datos recibidos del servidor:', data);

                if (data.success) {
                    likeCountSpan.textContent = data.likes;
                    button.classList.toggle('liked'); // Agrega o elimina la clase 'liked' del botón

                    if (heartIcon) {
                        heartIcon.classList.toggle('bi-heart');
                        heartIcon.classList.toggle('bi-heart-fill');
                    }
                    console.log('Clase liked toggled:', button.classList.contains('liked'));
                } else {
                    console.error('Error al procesar el like:', data.error);
                }

            } catch (error) {
                console.error('Error de red:', error);
            }
        });
    });

    // ... (la función initializeLikeButtons es opcional para cargar el estado inicial)
});



/* Funcion para ocultar/ver contraseña (login.php)*/


document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('contrasena');
    const togglePasswordIcon = document.getElementById('togglePassword');

    togglePasswordIcon.addEventListener('click', function() {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            // Cambia la clase del icono para mostrar el "ojo abierto"
            togglePasswordIcon.classList.remove('bi-eye-slash');
            togglePasswordIcon.classList.add('bi-eye');
        } else {
            passwordInput.type = 'password';
            // Cambia la clase del icono para mostrar el "ojo tachado"
            togglePasswordIcon.classList.remove('bi-eye');
            togglePasswordIcon.classList.add('bi-eye-slash');
        }
    });
});