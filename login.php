<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body class="login-body">
  <div class="circle-bg circle-top-left"></div>
  <div class="circle-bg circle-bottom-right"></div>
  <div class="login-container">
    <h2 class="login-h2">Iniciar Sesión</h2>


    <form class="login-form" action="procesar_login.php" method="POST" autocomplete="on">
      <div class="mb-3">
        <label for="usuario" class="form-label">Nombre de Usuario o Correo Electrónico:</label>
        <input type="text" id="usuario" name="usuario" required autofocus>
      </div>
      <div class="mb-3 password-toggle-container">
        <label for="contrasena" class="form-label">Contraseña:</label>
        <div class="password-input-wrapper">
          <input type="password" id="contrasena" name="contrasena" required>
          <span class="password-toggle-icon" id="togglePassword" tabindex="0" aria-label="Mostrar/Ocultar contraseña">
            <!-- OJO ABIERTO: Contraseña visible -->
            <svg id="icon-eye" viewBox="0 0 24 24" width="22" height="22" fill="none" style="display:none;">
              <path d="M1 12S5 5 12 5s11 7 11 7-4 7-11 7S1 12 1 12z" stroke="#333" stroke-width="2" fill="none"/>
              <circle cx="12" cy="12" r="3" stroke="#333" stroke-width="2" fill="none"/>
            </svg>
            <!-- OJO CERRADO: Contraseña oculta -->
            <svg id="icon-eye-off" viewBox="0 0 24 24" width="22" height="22" fill="none" style="display:inline;">
              <path d="M1 12S5 5 12 5s11 7 11 7-4 7-11 7S1 12 1 12z" stroke="#333" stroke-width="2" fill="none"/>
              <circle cx="12" cy="12" r="3" stroke="#333" stroke-width="2" fill="none"/>
              <line x1="4" y1="4" x2="20" y2="20" stroke="#333" stroke-width="2"/>
            </svg>
          </span>
        </div>
      </div>
      <button type="submit">Iniciar Sesión</button>
    </form>
    <p class="login-signup-link">¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a></p>
  </div>

  <!-- Enlaza tu JS personalizado aquí -->
  <script src="login_script.js"></script>
</body>
</html>