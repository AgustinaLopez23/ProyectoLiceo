<?php

// Datos de conexión a la base de datos
$host = 'localhost';
$usuario = 'root';
$contrasena = '';
$base_de_datos = 'portafolio_db';

$conn = new mysqli($host, $usuario, $contrasena, $base_de_datos);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_usuario = trim($_POST["nombre_usuario"]);
    $correo_electronico = trim($_POST["correo_electronico"]);
    $contrasena = $_POST["contrasena"];
    $confirmar_contrasena = $_POST["confirmar_contrasena"];

    // Variables para almacenar mensajes de error
    $errores = array();

    // Validaciones
    if (empty($nombre_usuario)) {
        $errores[] = "El nombre de usuario es requerido.";
    } elseif (!preg_match("/^[a-zA-Z0-9_]+$/", $nombre_usuario)) {
        $errores[] = "El nombre de usuario solo puede contener letras, números y guiones bajos.";
    } elseif (strlen($nombre_usuario) < 4 || strlen($nombre_usuario) > 20) {
        $errores[] = "El nombre de usuario debe tener entre 4 y 20 caracteres.";
    }

    if (empty($correo_electronico)) {
        $errores[] = "El correo electrónico es requerido.";
    } elseif (!filter_var($correo_electronico, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo electrónico no es válido.";
    }

    if (empty($contrasena)) {
        $errores[] = "La contraseña es requerida.";
    } elseif (strlen($contrasena) < 6) {
        $errores[] = "La contraseña debe tener al menos 6 caracteres.";
    } // elseif (!preg_match("/[a-z]/", $contrasena) || !preg_match("/[A-Z]/", $contrasena) || !preg_match("/[0-9]/", $contrasena)) {
    //     $errores[] = "La contraseña debe contener al menos una letra minúscula, una mayúscula y un número.";
    // }

    if ($contrasena !== $confirmar_contrasena) {
        $errores[] = "Las contraseñas no coinciden.";
    }

    // Si no hay errores, proceder con el registro
    if (empty($errores)) {
        // Verificar si el nombre de usuario o correo electrónico ya existen
        $sql_check = "SELECT id FROM usuarios WHERE nombre_usuario = ? OR correo_electronico = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("ss", $nombre_usuario, $correo_electronico);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $errores[] = "El nombre de usuario o el correo electrónico ya están registrados.";
        } else {
            // Hash de la contraseña de forma segura
            $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

            // Insertar el nuevo usuario en la base de datos
            $sql_insert = "INSERT INTO usuarios (nombre_usuario, correo_electronico, contrasena) VALUES (?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("sss", $nombre_usuario, $correo_electronico, $contrasena_hash);

            if ($stmt_insert->execute()) {
                $mensaje_exito = "Registro exitoso. Ahora puedes <a href='login.php'>iniciar sesión</a>.";
            } else {
                $errores[] = "Error al registrar el usuario: " . $conn->error;
            }

            $stmt_insert->close();
        }

        $stmt_check->close();
    }

    $conn->close();
} else {
    // Si se intenta acceder directamente al script sin enviar el formulario
    header("Location: registro.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado del Registro</title>

</head>
<body>
    <h1>Resultado del Registro</h1>

    <div class="mensaje">
        <?php if (!empty($errores)): ?>
            <div class="error">
                <?php foreach ($errores as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
                <p><a href="registro.php">Volver al formulario de registro</a></p>
            </div>
        <?php endif; ?>

        <?php if (isset($mensaje_exito)): ?>
            <div class="success">
                <p><?php echo $mensaje_exito; ?></p>
            </div>
        <?php endif; ?>
    </div>

    <div class="volver">
        <p><a href="index.php">Volver a la página principal</a></p>
    </div>
</body>
</html>