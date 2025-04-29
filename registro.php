<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Nuevo Usuario</title>
    <link rel="stylesheet" href="/ProyectoLiceo/styles.css"> </head>
<body class="login-body">
    <div class="login-container">
        <h2 class="login-h2">Registro de Nuevo Usuario</h2>
        <?php if (isset($registration_error)): ?>
            <p class="login-error-message"><?php echo htmlspecialchars($registration_error); ?></p>
        <?php endif; ?>
        <form class="login-form" action="procesar_registro.php" method="POST">
        <label for="nombre_usuario">Nombre de Usuario:</label>
        <input type="text" id="nombre_usuario" name="nombre_usuario" required>

        <label for="correo_electronico">Correo Electrónico:</label>
        <input type="email" id="correo_electronico" name="correo_electronico" required>
        
            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required>

            <label for="confirmar_contrasena">Confirmar Contraseña:</label>
            <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" required>

            <button type="submit">Registrarse</button>
        </form>
        <p class="login-signup-link">¿Ya tienes una cuenta? <a href="login.php">Iniciar Sesión</a></p>
    </div>
</body>
</html>