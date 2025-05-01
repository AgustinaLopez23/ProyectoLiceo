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

    $imagen_portada_nueva = null;
    $imagen_articulo_nueva = null;

    $eliminar_imagen_portada = isset($_POST['eliminar_imagen_portada']) && $_POST['eliminar_imagen_portada'] == 1;
    $eliminar_imagen_articulo = isset($_POST['eliminar_imagen_articulo']) && $_POST['eliminar_imagen_articulo'] == 1;

    $ruta_imagen_portada_anterior = null;
    $ruta_imagen_articulo_anterior = null;

    // Consultar el artículo existente
    $stmt = $conn->prepare("SELECT usuario_id, imagen_portada, imagen_articulo FROM articulos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows > 0) {
        $articulo = $resultado->fetch_assoc();
        if ($usuario_logueado_id != $articulo['usuario_id']) {
            die(mostrarMensajeError("No tienes permiso para editar este artículo."));
        }
        $ruta_imagen_portada_anterior = $articulo['imagen_portada'];
        $ruta_imagen_articulo_anterior = $articulo['imagen_articulo'];
    } else {
        die(mostrarMensajeError("Artículo no encontrado."));
    }

    // --- Manejo de la nueva imagen de portada ---
    if (isset($_FILES["imagen_portada_nueva"]) && $_FILES["imagen_portada_nueva"]["error"] == 0) {
        $carpeta_destino = "imagenes/"; // MODIFICADO
        $nombre_base = basename($_FILES["imagen_portada_nueva"]["name"]);
        $nombre_archivo = uniqid() . "_" . $nombre_base;
        $ruta_destino = $carpeta_destino . $nombre_archivo;
        $tipos_permitidos = array("jpg", "jpeg", "png", "gif");
        $extension = strtolower(pathinfo($nombre_base, PATHINFO_EXTENSION));

        if (!in_array($extension, $tipos_permitidos)) {
            die(mostrarMensajeError("Solo se permiten archivos JPG, JPEG, PNG y GIF para la nueva imagen de portada."));
        }
        if (!move_uploaded_file($_FILES["imagen_portada_nueva"]["tmp_name"], $ruta_destino)) {
            die(mostrarMensajeError("Error al subir la nueva imagen de portada."));
        }
        if (!empty($ruta_imagen_portada_anterior) && file_exists($ruta_imagen_portada_anterior)) {
            unlink($ruta_imagen_portada_anterior);
        }
        $imagen_portada_nueva = $ruta_destino;
    } elseif ($eliminar_imagen_portada) {
        if (!empty($ruta_imagen_portada_anterior) && file_exists($ruta_imagen_portada_anterior)) {
            unlink($ruta_imagen_portada_anterior);
        }
        $imagen_portada_nueva = null;
    } else {
        $imagen_portada_nueva = $ruta_imagen_portada_anterior;
    }

    // --- Manejo de la nueva imagen del artículo ---
    if (isset($_FILES["imagen_articulo_nueva"]) && $_FILES["imagen_articulo_nueva"]["error"] == 0) {
        $carpeta_destino = "imagenes/"; // MODIFICADO
        $nombre_base = basename($_FILES["imagen_articulo_nueva"]["name"]);
        $nombre_archivo = uniqid() . "_" . $nombre_base;
        $ruta_destino = $carpeta_destino . $nombre_archivo;
        $tipos_permitidos = array("jpg", "jpeg", "png", "gif");
        $extension = strtolower(pathinfo($nombre_base, PATHINFO_EXTENSION));

        if (!in_array($extension, $tipos_permitidos)) {
            die(mostrarMensajeError("Solo se permiten archivos JPG, JPEG, PNG y GIF para la nueva imagen del artículo."));
        }
        if (!move_uploaded_file($_FILES["imagen_articulo_nueva"]["tmp_name"], $ruta_destino)) {
            die(mostrarMensajeError("Error al subir la nueva imagen del artículo."));
        }
        if (!empty($ruta_imagen_articulo_anterior) && file_exists($ruta_imagen_articulo_anterior)) {
            unlink($ruta_imagen_articulo_anterior);
        }
        $imagen_articulo_nueva = $ruta_destino;
    } elseif ($eliminar_imagen_articulo) {
        if (!empty($ruta_imagen_articulo_anterior) && file_exists($ruta_imagen_articulo_anterior)) {
            unlink($ruta_imagen_articulo_anterior);
        }
        $imagen_articulo_nueva = null;
    } else {
        $imagen_articulo_nueva = $ruta_imagen_articulo_anterior;
    }

    // Actualizar el artículo incluyendo las imágenes
    $stmt = $conn->prepare("UPDATE articulos SET titulo = ?, contenido = ?, categoria = ?, autor = ?, imagen_portada = ?, imagen_articulo = ? WHERE id = ?");
    $stmt->bind_param("ssssssi", $titulo, $contenido, $categoria, $autor, $imagen_portada_nueva, $imagen_articulo_nueva, $id);

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