<?php
session_start();

// Verificación de autenticación
if (!isset($_SESSION['usuario_id'])) {
    die("<div class='mensaje-error'>No estás autenticado.</div>");
}

$host = 'localhost';
$usuario = 'root';
$contrasena = '';
$base_de_datos = 'portafolio_db';

$conn = new mysqli($host, $usuario, $contrasena, $base_de_datos);
if ($conn->connect_error) {
    die("<div class='mensaje-error'>Error de conexión: " . $conn->connect_error . "</div>");
}

$conn->set_charset("utf8");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $titulo = $_POST["titulo"];
    $categoria = $_POST["categoria"];
    $contenido = $_POST["contenido"];
    $autor = $_POST["autor"];
    $usuario_logueado_id = $_SESSION['usuario_id'];
    $imagen_principal = null;
    $eliminar_imagen = isset($_POST['eliminar_imagen']) && $_POST['eliminar_imagen'] == 1;

    // Consultar el artículo existente
    $stmt = $conn->prepare("SELECT usuario_id, imagen_principal FROM articulos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows > 0) {
        $articulo = $resultado->fetch_assoc();
        if ($usuario_logueado_id != $articulo['usuario_id']) {
            die(mostrarMensajeError("No tienes permiso para editar este artículo."));
        }
        $imagen_actual = $articulo['imagen_principal'];
    } else {
        die(mostrarMensajeError("Artículo no encontrado."));
    }

    // Manejo de la imagen
    if (isset($_FILES["imagen_principal"]) && $_FILES["imagen_principal"]["error"] == 0) {
        $carpeta_destino = "imagenes/";
        $nombre_base = basename($_FILES["imagen_principal"]["name"]);
        $nombre_archivo = uniqid() . "_" . $nombre_base;
        $ruta_destino = $carpeta_destino . $nombre_archivo;
        $tipos_permitidos = array("jpg", "jpeg", "png", "gif");

        $extension = strtolower(pathinfo($nombre_base, PATHINFO_EXTENSION));
        if (!in_array($extension, $tipos_permitidos)) {
            die(mostrarMensajeError("Solo se permiten archivos JPG, JPEG, PNG y GIF."));
        }

        if (!move_uploaded_file($_FILES["imagen_principal"]["tmp_name"], $ruta_destino)) {
            die(mostrarMensajeError("Error al subir la nueva imagen."));
        }
        
        // Eliminar imagen anterior si existe
        if (!empty($imagen_actual) && file_exists($imagen_actual)) {
            unlink($imagen_actual);
        }
        $imagen_principal = $ruta_destino;
    } elseif ($eliminar_imagen) {
        if (!empty($imagen_actual) && file_exists($imagen_actual)) {
            unlink($imagen_actual);
        }
        $imagen_principal = null;
    } else {
        $imagen_principal = $imagen_actual;
    }

    // Actualizar el artículo
    $stmt = $conn->prepare("UPDATE articulos SET titulo = ?, contenido = ?, categoria = ?, autor = ?, imagen_principal = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $titulo, $contenido, $categoria, $autor, $imagen_principal, $id);

    if ($stmt->execute()) {
        mostrarMensajeExito();
    } else {
        echo mostrarMensajeError("Error al actualizar el artículo: " . $conn->error);
    }

    $stmt->close();
    $conn->close();
} else {
    echo mostrarMensajeError("Acceso no permitido.");
}

// Funciones para mostrar mensajes estilizados
function mostrarMensajeExito() {
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Artículo Actualizado</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
        <link rel="stylesheet" href="styles.css">
    </head>
    <body class="cuerpo-mensaje">
        <div class="mensaje-exito-icono">
            <i class="bi bi-check-circle-fill"></i> 
            Artículo actualizado exitosamente. 
            <a href="panel_usuario.php">Volver al Panel de Usuario</a>
        </div>
    </body>
    </html>';
}

function mostrarMensajeError($mensaje) {
    return '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body class="cuerpo-mensaje">
        <div class="mensaje-error">
            ⚠️ ' . $mensaje . '
        </div>
    </body>
    </html>';
}
?>