<?php

session_start();
session_regenerate_id(true); // Prevenir fijación de sesión

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
    $nombre_usuario_o_correo = $conn->real_escape_string($_POST["usuario"]);
    $contrasena = $_POST["contrasena"];

    if (empty($nombre_usuario_o_correo) || empty($contrasena)) {
        header("Location: login.php?error=Por favor, introduce tu nombre de usuario o correo electrónico y contraseña.");
        exit();
    }

    // Buscar al usuario por nombre de usuario o correo electrónico
    $sql = "SELECT id, nombre_usuario, contrasena FROM usuarios WHERE nombre_usuario = ? OR correo_electronico = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nombre_usuario_o_correo, $nombre_usuario_o_correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows == 1) {
        $usuario = $resultado->fetch_assoc();
        if (password_verify($contrasena, $usuario["contrasena"])) {
            // La contraseña es correcta, iniciar sesión
            $_SESSION["usuario_id"] = $usuario["id"];
            $_SESSION["nombre_usuario"] = $usuario["nombre_usuario"];

            // Redirigir a la página principal del blog
            header("Location: /ProyectoLiceo/ProyectoLiceo.php#inicio");
            exit();
        } else {
            // Contraseña incorrecta
            header("Location: login.php?error=Contraseña incorrecta.");
            exit();
        }
    } else {
        // No se encontró ningún usuario con ese nombre de usuario o correo electrónico
        header("Location: login.php?error=Nombre de usuario o correo electrónico incorrecto.");
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    // Si se intenta acceder directamente al script sin enviar el formulario
    header("Location: login.php");
    exit();
}

?>