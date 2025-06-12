 document.addEventListener('DOMContentLoaded', function() {
      document.querySelectorAll('.password-toggle-icon').forEach(function(toggle) {
        const inputId = toggle.getAttribute('data-input');
        const input = document.getElementById(inputId);
        const iconEye = toggle.querySelector('.icon-eye');
        const iconEyeOff = toggle.querySelector('.icon-eye-off');

        // Inicialización: ojos
        function setIcons() {
          if (input.type === 'password') {
            iconEye.style.display = 'none';
            iconEyeOff.style.display = 'inline';
          } else {
            iconEye.style.display = 'inline';
            iconEyeOff.style.display = 'none';
          }
        }
        setIcons();

        toggle.addEventListener('click', function() {
          if (input.type === 'password') {
            input.type = 'text';
          } else {
            input.type = 'password';
          }
          setIcons();
        });

        // Accesibilidad: alternar con Enter o Espacio
        toggle.addEventListener('keydown', function(e) {
          if (e.key === " " || e.key === "Enter") {
            toggle.click();
            e.preventDefault();
          }
        });
      });
    });