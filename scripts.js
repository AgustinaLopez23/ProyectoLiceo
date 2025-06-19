(() => {
  // === Lightbox Module ===
  const Lightbox = (() => {
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const lightboxDescription = document.getElementById('lightbox-description');
    const lightboxPrev = document.querySelector('.lightbox-prev');
    const lightboxNext = document.querySelector('.lightbox-next');
    const lightboxClose = document.querySelector('.close-fullscreen');

    let currentGalleryImages = [];
    let currentImageIndex = 0;
    let currentGalleryId = null;

    const getGalleryImages = (galleryId) => {
      const gallery = document.getElementById(galleryId);
      if (!gallery) return [];
      return Array.from(gallery.querySelectorAll('.item img')).map(img => ({
        src: img.src,
        alt: img.alt,
        description: img.getAttribute('data-description')
      }));
    };

    const open = (imgSrc, description, galleryId) => {
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
    };

    const close = () => {
      if (lightbox) {
        lightbox.style.display = 'none';
        document.removeEventListener('keydown', handleKeydown);
        currentGalleryImages = [];
        currentImageIndex = 0;
        currentGalleryId = null;
        if (lightboxPrev) lightboxPrev.style.display = 'none';
        if (lightboxNext) lightboxNext.style.display = 'none';
      }
    };

    const changeImage = (direction) => {
      if (currentGalleryImages.length <= 1 || currentGalleryId === null) return;
      currentImageIndex = (currentImageIndex + direction + currentGalleryImages.length) % currentGalleryImages.length;
      const img = currentGalleryImages[currentImageIndex];
      lightboxImg.src = img.src;
      lightboxImg.alt = img.alt;
      lightboxDescription.textContent = img.description;
    };

    function handleKeydown(event) {
      if (event.key === 'Escape') close();
      else if (event.key === 'ArrowLeft') changeImage(-1);
      else if (event.key === 'ArrowRight') changeImage(1);
    }

    const setup = () => {
      // Galerías
      document.querySelectorAll('.gallery').forEach(gallery => {
        const galleryId = gallery.id || 'gallery-' + Math.random().toString(36).substring(7);
        gallery.id = galleryId;

        gallery.querySelectorAll('.item img').forEach(image => {
          image.addEventListener('click', () => {
            open(image.src, image.getAttribute('data-description'), galleryId);
          });
        });
      });
      // Cerrar al click en fondo oscuro
      if (lightbox) {
        lightbox.addEventListener('click', (e) => {
          if (e.target === lightbox) close();
        });
      }
      // Cerrar al click en cruz
      if (lightboxClose) {
        lightboxClose.addEventListener('click', (e) => {
          e.stopPropagation();
          close();
        });
      }
      // Flechas
      [
        [lightboxPrev, -1],
        [lightboxNext, 1]
      ].forEach(([arrow, dir]) => {
        if (arrow) arrow.addEventListener('click', () => changeImage(dir));
      });
    };

    return { setup };
  })();

  // === Comentarios y Likes Module ===
  const ComentariosLikes = (() => {
    const setup = () => {
      // Comentarios
      document.querySelectorAll('.comentarios form').forEach(formulario => {
        formulario.addEventListener('submit', e => {
          e.preventDefault();
          const textarea = formulario.querySelector('textarea');
          const comentario = textarea.value.trim();
          if (comentario) {
            const comentariosLista = formulario.parentNode.querySelector('.chat-messages');
            if (!comentariosLista) return;
            const mensaje = document.createElement('div');
            mensaje.classList.add('chat-message');
            mensaje.textContent = comentario;
            comentariosLista.appendChild(mensaje);
            textarea.value = '';
          }
        });
      });

      // Prevención de propagación en imágenes del blog
      document.querySelectorAll('.blog-card a img').forEach(img => {
        img.addEventListener('click', e => e.stopPropagation());
      });

      // Likes
      document.querySelectorAll('.like-btn').forEach(button => {
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
          } catch (err) {
            console.error('Error de red:', err);
          }
        });
      });
    };
    return { setup };
  })();

  // === Tarjetas animadas ===
  const AnimacionTarjetas = (() => {
    const animateElement = (element, delay) => {
      setTimeout(() => {
        element.classList.add('animated');
      }, delay);
    };
    const setup = () => {
      document.querySelectorAll('.plan-card').forEach((card, idx) => {
        animateElement(card, idx * 200);
      });
    };
    return { setup };
  })();

  // === Responsive ===
  const Responsive = (() => {
    const getScreenSize = () => ({
      width: window.innerWidth || document.documentElement.clientWidth,
      height: window.innerHeight || document.documentElement.clientHeight
    });

    const applyResponsiveStyles = () => {
      const { width } = getScreenSize();
      const container = document.querySelector('.container');
      if (!container) return;
      if (width < 768) {
        container.style.maxWidth = '100%';
        container.style.padding = '20px';
      } else if (width < 992) {
        container.style.maxWidth = '720px';
        container.style.margin = '0 auto';
      } else if (width < 1200) {
        container.style.maxWidth = '960px';
      } else {
        container.style.maxWidth = '1140px';
      }
    };

    const setup = () => {
      applyResponsiveStyles();
      window.addEventListener('resize', applyResponsiveStyles);
    };
    return { setup };
  })();

  // === Inicialización ===

  window.addEventListener('load', () => {
    Lightbox.setup();
    AnimacionTarjetas.setup();
    Responsive.setup();
  });
  document.addEventListener('DOMContentLoaded', ComentariosLikes.setup);

})();

// === Modal Login Requerido ===

document.addEventListener('DOMContentLoaded', function () {
  const likeButtons = document.querySelectorAll('.like-btn');
  const modal = document.getElementById('modal-login-required');
  const btnCloseModal = document.getElementById('btn-close-modal');

  likeButtons.forEach(button => {
    button.addEventListener('click', function (event) {
      if (!isLoggedIn) {
        event.preventDefault();
       modal.style.display = 'flex'; 
      }
    });
  });

  if (btnCloseModal) {
    btnCloseModal.addEventListener('click', function () {
      modal.style.display = 'none';
    });
  }

  window.addEventListener('click', function (event) {
    if (event.target === modal) {
      modal.style.display = 'none';
    }
  });
});

