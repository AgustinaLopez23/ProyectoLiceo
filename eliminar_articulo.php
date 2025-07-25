<?php

session_start();
session_regenerate_id(true); // Prevenir fijación de sesión

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}

// Datos de conexión a la base de datos
$host = 'localhost';
$usuario = 'root';
$contrasena = '';
$base_de_datos = 'portafolio_db';

$conn = new mysqli($host, $usuario, $contrasena, $base_de_datos);

if ($conn->connect_error) {
    die(mostrarMensajeErrorEliminar("Error de conexión: " . $conn->connect_error));
}

$conn->set_charset("utf8");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $articulo_id = $conn->real_escape_string($_POST["id"]);
    $usuario_id = $_SESSION["usuario_id"];

    // Primero, obtener el usuario_id y la imagen_principal del artículo
    $sql_select = "SELECT usuario_id, imagen_portada, imagen_articulo FROM articulos WHERE id = ?";
    $stmt_select = $conn->prepare($sql_select);
    if (!$stmt_select) {
        die(mostrarMensajeErrorEliminar("Error al preparar la consulta de selección: " . $conn->error));
    }
    $stmt_select->bind_param("i", $articulo_id);
    $stmt_select->execute();
    $resultado_select = $stmt_select->get_result();

    if ($resultado_select->num_rows == 1) {
        $articulo = $resultado_select->fetch_assoc();

        // Verificar si el usuario logueado es el autor del artículo
        if ($articulo['usuario_id'] == $usuario_id) {
            $rutas_imagenes_a_eliminar = [];
            if (!empty($articulo['imagen_portada']) && file_exists($articulo['imagen_portada'])) {
                $rutas_imagenes_a_eliminar[] = $articulo['imagen_portada'];
            }
            if (!empty($articulo['imagen_articulo']) && file_exists($articulo['imagen_articulo'])) {
                $rutas_imagenes_a_eliminar[] = $articulo['imagen_articulo'];
            }

            // Eliminar las imágenes asociadas si existen
            foreach ($rutas_imagenes_a_eliminar as $ruta_imagen) {
                if (unlink($ruta_imagen)) {
                    // Éxito al eliminar la imagen
                } else {
                    // No detenemos la eliminación del artículo aunque falle la de la imagen
                }
            }

            // Eliminar el artículo
            $sql_delete = "DELETE FROM articulos WHERE id = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            if (!$stmt_delete) {
                die(mostrarMensajeErrorEliminar("Error al preparar la consulta de eliminación: " . $conn->error));
            }
            $stmt_delete->bind_param("i", $articulo_id);

            if ($stmt_delete->execute()) {
                mostrarMensajeExitoEliminar();
            } else {
                echo mostrarMensajeErrorEliminar("Error al eliminar el artículo: " . $stmt_delete->error);
            }

            $stmt_delete->close();
        } else {
            echo mostrarMensajeErrorEliminar("No tienes permiso para eliminar este artículo.");
        }
    } else {
        echo mostrarMensajeErrorEliminar("Artículo no encontrado.");
    }

    $stmt_select->close();
    $conn->close();
} else {
    echo mostrarMensajeErrorEliminar("Acceso no permitido.");
}

// Función para mostrar mensaje de éxito estilizado
function mostrarMensajeExitoEliminar() {
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Artículo Eliminado</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body class="cuerpo-mensaje">
        <div class="mensaje-exito-eliminar">
            ✅ Artículo eliminado exitosamente.<br>
            <a href="panel_usuario.php">Volver a mi panel</a>
        </div>
    </body>
    </html>';
}

// Función para mostrar mensajes de error estilizados
function mostrarMensajeErrorEliminar($mensaje) {
    return '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body class="cuerpo-mensaje">
        <div class="mensaje-error-eliminar">
            ⚠️ ' . htmlspecialchars($mensaje) . '
        </div>
    </body>
    </html>';
}

?>