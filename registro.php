<?php
ob_start("ob_gzhandler");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de Nuevo Usuario</title>
  <link rel="stylesheet" href="styles.css">
  <script src="login_script.js"></script>
</head>
<style>
  .registro-body {
  margin: 0;
  padding: 0;
  min-height: 100vh;
  font-family: 'Poppins', Arial, sans-serif;
  background:rgba(21, 66, 144, 0.8);
  background-image: url('imagenes/ishfaq-ahmed-W-qgMo-YV08-unsplas.webp');
  background-size: cover;
  background-position: center;
  display: flex;
  align-items: center;
  justify-content: center;
}

.registro-layout {
      width: 100vw;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: flex-start;
      gap: 5vw;
      position: relative;
      background: rgba(24,28,35,0.76);
      padding-left: 6vw;
      padding-right: 2vw;
      box-sizing: border-box;
    }
    .welcome-section {
      flex: 1 1 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      color: #fff;
      z-index: 1;
      position: relative;
      min-width: 360px;
      padding: 40px 0;
    }
    .welcome-section h2 {
      font-size: 2.2rem;
      font-weight: 700;
      margin-bottom: 20px;
      letter-spacing: 1px;
    }
    .welcome-section p {
      font-size: 1.05rem;
      margin-bottom: 30px;
      color: #eaeaea;
      font-weight: 400;
    }
    .btn-iniciar {
      background: -webkit-linear-gradient(90deg,rgb(39, 57, 68),rgb(77, 163, 156));
      background: linear-gradient(90deg,rgb(39, 52, 68),rgba(77, 153, 163, 0.64));
      color: #fff;
      border: none;
      padding: 16px 36px;
      border-radius: 30px;
      font-size: 1.08rem;
      font-weight: 500;
      cursor: pointer;
      box-shadow: 0 3px 16px 0 rgba(57,80,250,0.17);
      transition: background 0.2s, box-shadow 0.2s;
      outline: none;
      margin-bottom: 20px;
    }
    .btn-iniciar:hover, .btn-iniciar:focus {
      background: -webkit-linear-gradient(90deg,rgb(39, 57, 68),rgb(77, 163, 156));
      box-shadow: 0 6px 32px 0 rgba(57, 186, 250, 0.22);
    }
    .arrow {
      width: 80px;
      position: relative;
      top: 0;
      left: 10px;
      margin-top: 18px;
      filter: drop-shadow(0 0 8px #fff8) brightness(1.15);
      z-index: 1;
    }
    .register-section {
      flex: 1 1 0;
      max-width: 370px;
      min-width: 340px;
      background: rgba(24,28,35,0.78);
      border-radius: 16px;
      box-shadow: 0 8px 40px 0 rgba(0,0,0,0.30);
      padding: 42px 32px 30px 32px;
      display: flex;
      flex-direction: column;
      align-items: center;
      z-index: 1;
      position: relative;
      margin-right: 5vw;
    }
    .register-section h2 {
      font-size: 1.55rem;
      font-weight: 700;
      margin-bottom: 18px;
      color: #fff;
      letter-spacing: .5px;
      text-align: center;
    }
    
    .register-section .register-free {
      color: #fff;
      font-size: 0.97rem;
      margin-bottom: 24px;
      text-align: center;
      font-weight: 400;
      letter-spacing: .1px;
    }
    .registro-form {
      width: 100%;
      display: flex;
      flex-direction: column;
      gap: 16px;
    }
    .registro-form input[type="text"],
    .registro-form input[type="email"],
    .registro-form input[type="password"] {
      width: 100%;
      margin-bottom: 6px;
      background: transparent;
      border: none;
      border-bottom: 2px solid #008996;
      padding: 9px 7px;
      color: #fff;
      font-size: 1.05rem;
      outline: none;
      margin-top: 2px;
      transition: border-color 0.18s;
      border-radius: 0;
    }
    .registro-form input:focus {
      border-bottom: 2.5px solidrgb(57, 208, 250);
    }
    .registro-form label {
      font-size: 0.99rem;
      color: #aaa;
      margin-bottom: 2px;
      font-weight: 400;
      letter-spacing: 0;
    }

     .registro-form input::placeholder {
      color: #fff;
    }

    .registro-form button {
      background: #fff;
      color: #23273c;
      border: none;
      padding: 13px 0;
      border-radius: 30px;
      font-size: 1.09rem;
      font-weight: 600;
      cursor: pointer;
      margin-top: 15px;
      box-shadow: 0 3px 10px 0 #0002;
      transition: background 0.18s, color 0.18s;
      letter-spacing: .3px;
    }
    .registro-form button:hover, .registro-form button:focus {
      background: #008996;
      color: #fff;
    }
    /* PASSWORD TOGGLE */
    .password-toggle-container {
      margin-bottom: 1.5rem;
    }
    .password-input-wrapper {
      position: relative;
      display: flex;
      align-items: center;
    }
    #contrasena, #confirmar_contrasena {
      width: 100%;
      box-sizing: border-box;
      padding-right: 44px;
    }
    .password-toggle-icon {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      z-index: 2;
      background: transparent;
      padding: 2px;
      display: flex;
      align-items: center;
      border-radius: 4px;
      transition: background 0.2s;
    }
    .password-toggle-icon:focus,
    .password-toggle-icon:hover {
      background: #e0e0e0;
    }
    
</style>
<body class="registro-body">
  <div class="registro-layout">
    <div class="welcome-section">
      <h2>Bienvenidos a mi Portfolio</h2>
      <p>Si ya tienes una cuenta por favor inicia sesion aqui</p>
      <a href="login.php" class="btn-iniciar">Iniciar Sesion</a>
     <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-arrow-90deg-up" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M4.854 1.146a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L4 2.707V12.5A2.5 2.5 0 0 0 6.5 15h8a.5.5 0 0 0 0-1h-8A1.5 1.5 0 0 1 5 12.5V2.707l3.146 3.147a.5.5 0 1 0 .708-.708z"/>
</svg>
    </div>
    <div class="register-section">
      <h2>Crear una cuenta</h2>
      <form class="registro-form" action="procesar_registro.php" method="POST" autocomplete="on">
        <input type="text" id="nombre_usuario" name="nombre_usuario" placeholder="Nombre de Usuario" required>
        <input type="email" id="correo_electronico" name="correo_electronico" placeholder="Correo electronico" required>
        <div class="password-toggle-container">
          <div class="password-input-wrapper">
            <input type="password" id="contrasena" name="contrasena" placeholder="Contraseña" required>
            <span class="password-toggle-icon" tabindex="0" aria-label="Mostrar/Ocultar contraseña" data-input="contrasena">
              <!-- OJO ABIERTO: Contraseña visible -->
              <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16" style="display: none;">
                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
              </svg>
              <!-- OJO CERRADO: Contraseña oculta -->
              <svg class="icon-eye-off" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16" style="display: inline;">
                <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z"/>
                <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/>
                <path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z"/>
              </svg>
            </span>
          </div>
        </div>
        <button type="submit">Registrarse</button>
      </form>
    </div>
  </div>
</body>
</html>