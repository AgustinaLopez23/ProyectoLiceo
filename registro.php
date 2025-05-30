<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de Nuevo Usuario</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body class="registro-body">
  <div class="circle-bg circle-top-left"></div>
  <div class="circle-bg circle-bottom-right"></div>
  <div class="registro-container">
    <h2 class="registro-h2">Registro de Nuevo<br>Usuario</h2>

    <form class="registro-form" action="procesar_registro.php" method="POST" autocomplete="on">
      <div class="mb-3">
        <label for="nombre_usuario" class="form-label">Nombre de Usuario:</label>
        <input type="text" id="nombre_usuario" name="nombre_usuario" required>
      </div>
      <div class="mb-3">
        <label for="correo_electronico" class="form-label">Correo Electrónico:</label>
        <input type="email" id="correo_electronico" name="correo_electronico" required>
      </div>
      <div class="mb-3 password-toggle-container">
        <label for="contrasena" class="form-label">Contraseña:</label>
        <div class="password-input-wrapper">
          <input type="password" id="contrasena" name="contrasena" required>
          <span class="password-toggle-icon" tabindex="0" aria-label="Mostrar/Ocultar contraseña" data-input="contrasena">
            <!-- OJO ABIERTO: Contraseña visible -->
            <svg class="icon-eye" viewBox="0 0 24 24" width="22" height="22" fill="none" style="display:none;">
              <path d="M1 12S5 5 12 5s11 7 11 7-4 7-11 7S1 12 1 12z" stroke="#333" stroke-width="2" fill="none"/>
              <circle cx="12" cy="12" r="3" stroke="#333" stroke-width="2" fill="none"/>
            </svg>
            <!-- OJO CERRADO: Contraseña oculta -->
            <svg class="icon-eye-off" viewBox="0 0 24 24" width="22" height="22" fill="none" style="display:inline;">
              <path d="M1 12S5 5 12 5s11 7 11 7-4 7-11 7S1 12 1 12z" stroke="#333" stroke-width="2" fill="none"/>
              <circle cx="12" cy="12" r="3" stroke="#333" stroke-width="2" fill="none"/>
              <line x1="4" y1="4" x2="20" y2="20" stroke="#333" stroke-width="2"/>
            </svg>
          </span>
        </div>
      </div>
      <div class="mb-3 password-toggle-container">
        <label for="confirmar_contrasena" class="form-label">Confirmar Contraseña:</label>
        <div class="password-input-wrapper">
          <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" required>
          <span class="password-toggle-icon" tabindex="0" aria-label="Mostrar/Ocultar contraseña" data-input="confirmar_contrasena">
            <!-- OJO ABIERTO: Contraseña visible -->
            <svg class="icon-eye" viewBox="0 0 24 24" width="22" height="22" fill="none" style="display:none;">
              <path d="M1 12S5 5 12 5s11 7 11 7-4 7-11 7S1 12 1 12z" stroke="#333" stroke-width="2" fill="none"/>
              <circle cx="12" cy="12" r="3" stroke="#333" stroke-width="2" fill="none"/>
            </svg>
            <!-- OJO CERRADO: Contraseña oculta -->
            <svg class="icon-eye-off" viewBox="0 0 24 24" width="22" height="22" fill="none" style="display:inline;">
              <path d="M1 12S5 5 12 5s11 7 11 7-4 7-11 7S1 12 1 12z" stroke="#333" stroke-width="2" fill="none"/>
              <circle cx="12" cy="12" r="3" stroke="#333" stroke-width="2" fill="none"/>
              <line x1="4" y1="4" x2="20" y2="20" stroke="#333" stroke-width="2"/>
            </svg>
          </span>
        </div>
      </div>
      <button type="submit">Registrarme</button>
    </form>
    <p class="registro-link-login">¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      document.querySelectorAll('.password-toggle-icon').forEach(function(toggle) {
        const inputId = toggle.getAttribute('data-input');
        const input = document.getElementById(inputId);
        const iconEye = toggle.querySelector('.icon-eye');
        const iconEyeOff = toggle.querySelector('.icon-eye-off');

        toggle.addEventListener('click', function() {
          if (input.type === 'password') {
            input.type = 'text';
            iconEye.style.display = 'inline';
            iconEyeOff.style.display = 'none';
          } else {
            input.type = 'password';
            iconEye.style.display = 'none';
            iconEyeOff.style.display = 'inline';
          }
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
  </script>
</body>
</html>