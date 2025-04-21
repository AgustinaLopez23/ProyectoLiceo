<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="/ProyectoLiceo/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
</head>

<body class="login-body">
    <div class="login-container">
        <h2 class="login-h2">Iniciar Sesión</h2>
        <?php if (isset($_GET['error'])): ?>
            <p class="login-error-message"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>
        <form class="login-form" action="procesar_login.php" method="POST">
            <div class="mb-3">
                <label for="usuario" class="form-label">Nombre de Usuario o Correo Electrónico:</label>
                <input type="text" class="form-control" id="usuario" name="usuario" required aria-required="true">
            </div>
            <div class="mb-3 password-toggle-container">
                <label for="contrasena" class="form-label">Contraseña:</label>
                <input type="password" class="form-control" id="contrasena" name="contrasena" required aria-required="true">
                <i id="togglePassword" class="bi bi-eye-slash password-toggle-icon" onclick="togglePasswordVisibility()"></i>
            </div>
            <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
        </form>
        <p class="login-signup-link">¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a></p>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('contrasena');
            const togglePasswordIcon = document.getElementById('togglePassword');
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                togglePasswordIcon.classList.remove('bi-eye-slash');
                togglePasswordIcon.classList.add('bi-eye');
            } else {
                passwordInput.type = "password";
                togglePasswordIcon.classList.remove('bi-eye');
                togglePasswordIcon.classList.add('bi-eye-slash');
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>