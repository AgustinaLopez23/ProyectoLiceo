(() => {
  // Lightbox
  const lightbox = document.getElementById('lightbox');
  const lightboxImg = document.getElementById('lightbox-img');
  const lightboxDescription = document.getElementById('lightbox-description');
  const lightboxPrev = document.querySelector('.lightbox-prev');
  const lightboxNext = document.querySelector('.lightbox-next');
  const lightboxClose = document.querySelector('.close-fullscreen'); // Cruz de cerrar

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

    const showArrows = currentGalleryImages.length > 1;
    if (lightboxPrev) lightboxPrev.style.display = showArrows ? 'block' : 'none';
    if (lightboxNext) lightboxNext.style.display = showArrows ? 'block' : 'none';

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
      if (lightboxPrev) lightboxPrev.style.display = 'none';
      if (lightboxNext) lightboxNext.style.display = 'none';
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
    if (currentGalleryImages.length <= 1 || currentGalleryId === null) return;

    currentImageIndex += direction;
    if (currentImageIndex < 0) currentImageIndex = currentGalleryImages.length - 1;
    else if (currentImageIndex >= currentGalleryImages.length) currentImageIndex = 0;

    const currentImage = currentGalleryImages[currentImageIndex];
    lightboxImg.src = currentImage.src;
    lightboxImg.alt = currentImage.alt;
    lightboxDescription.textContent = currentImage.description;
  }

  function handleKeydown(event) {
    if (event.key === 'Escape') closeLightbox();
    else if (event.key === 'ArrowLeft') changeLightboxImage(-1);
    else if (event.key === 'ArrowRight') changeLightboxImage(1);
  }

  window.addEventListener('load', () => {
    const galleries = document.querySelectorAll('.gallery');
    galleries.forEach(gallery => {
      const galleryId = gallery.id || 'gallery-' + Math.random().toString(36).substring(7);
      gallery.id = galleryId;

      const images = gallery.querySelectorAll('.item img');
      images.forEach(image => {
        image.addEventListener('click', () => {
          const imgSrc = image.src;
          const description = image.getAttribute('data-description');
          const galleryElement = image.closest('.gallery');
          const currentGalleryId = galleryElement ? galleryElement.id : null;
          openLightbox(imgSrc, description, currentGalleryId);
        });
      });
    });

    // Permitir cerrar el lightbox al hacer click en el fondo oscuro
    if (lightbox) {
      lightbox.addEventListener('click', (event) => {
        if (event.target === lightbox) closeLightbox();
      });
    }

    // Permitir cerrar el lightbox haciendo click en la cruz
    if (lightboxClose) {
      lightboxClose.addEventListener('click', (event) => {
        event.stopPropagation();
        closeLightbox();
      });
    }

    [['.lightbox-prev', -1], ['.lightbox-next', 1]].forEach(([selector, direction]) => {
      const arrow = document.querySelector(selector);
      if (arrow) arrow.addEventListener('click', () => changeLightboxImage(direction));
    });
  });

  // Comentarios y likes
  document.addEventListener('DOMContentLoaded', () => {
    // Comentarios
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
          comentarioElemento.textContent = comentario;
          comentariosLista.appendChild(comentarioElemento);
          textarea.value = '';
        }
      });
    });

    // Prevención de propagación en imágenes del blog
    const blogImages = document.querySelectorAll('.blog-card a img');
    blogImages.forEach(img => {
      img.addEventListener('click', (event) => {
        event.stopPropagation();
      });
    });

    // Likes
    const likeButtons = document.querySelectorAll('.like-btn');
    likeButtons.forEach(button => {
      button.addEventListener('click', async () => {
        const imageId = button.dataset.imageId;
        const tablaOrigen = button.dataset.tablaOrigen;
        const likeCountSpan = button.nextElementSibling;
        const heartEmptyIcon = button.querySelector('.heart-empty-icon');
        const heartFilledIcon = button.querySelector('.heart-filled-icon');

        if (!imageId || !tablaOrigen) {
          console.error('Error: Faltan atributos data-image-id o data-tabla-origen.');
          return;
        }

        try {
          const response = await fetch('http://localhost/ProyectoLiceo/procesar_like.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `item_id=${imageId}&tabla_origen=${tablaOrigen}`
          });

          if (!response.ok) {
            console.error('Error en la respuesta del servidor:', response.status);
            return;
          }

          const data = await response.json();
          if (data.success) {
            likeCountSpan.textContent = data.likes;
            button.classList.toggle('liked');
            if (heartEmptyIcon && heartFilledIcon) {
              heartEmptyIcon.style.display = button.classList.contains('liked') ? 'none' : 'block';
              heartFilledIcon.style.display = button.classList.contains('liked') ? 'block' : 'none';
            }
          } else {
            console.error('Error al procesar el like:', data.error);
          }
        } catch (error) {
          console.error('Error de red:', error);
        }
      });
    });
  });

  // Animación de tarjetas
  const animateElement = (element, delay) => {
    setTimeout(() => {
      element.classList.add('animated');
    }, delay);
  };

  const planCards = document.querySelectorAll('.plan-card');
  planCards.forEach((card, index) => {
    animateElement(card, index * 200);
  });

  // Responsive
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

  window.addEventListener('load', applyResponsiveStyles);
  window.addEventListener('resize', applyResponsiveStyles);

})();

    // Mostrar modal si no logueado y se intenta dar like
    document.addEventListener('DOMContentLoaded', function() {
      document.querySelectorAll('.like-btn').forEach(function(btn) {
        btn.addEventListener('click', function(event) {
          if (typeof usuarioLogueado !== 'undefined' && !usuarioLogueado) {
            event.preventDefault();
            document.getElementById('modal-login-required').style.display = 'flex';
            return false;
          }
        });
      });
      document.getElementById('btn-close-modal').onclick = function() {
        document.getElementById('modal-login-required').style.display = 'none';
      };
      // Opcional: cerrar al hacer click fuera del modal
      window.onclick = function(e) {
        var modal = document.getElementById('modal-login-required');
        if (e.target === modal) modal.style.display = 'none';
      };
    });