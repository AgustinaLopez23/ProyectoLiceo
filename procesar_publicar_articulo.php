<?php
session_start();
session_regenerate_id(true); // Prevenir fijación de sesión

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}

// Incluye la conexión a la base de datos
include "conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = trim($_POST['titulo']);
    $contenido = trim($_POST['contenido']);
    $categoria = trim($_POST['categoria']);

    $imagen_portada_ruta = null;
    $imagen_articulo_ruta = null;

    // Función para generar un slug amigable
    function generarSlug($titulo) {
        $slug = strtolower(trim(preg_replace('/[^a-z0-9-]+/', '-', $titulo)));
        return $slug;
    }

    // Función para verificar si un slug ya existe en la base de datos
    function verificarSlugUnico($conn, $slug) {
        $sql = "SELECT COUNT(*) FROM articulos WHERE slug = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        return $count == 0;
    }

    // Generar el slug base y asegurarse que sea único
    $slug_base = generarSlug($titulo);
    $slug_final = $slug_base;
    $contador = 1;
    while (!verificarSlugUnico($conn, $slug_final)) {
        $slug_final = $slug_base . '-' . $contador;
        $contador++;
    }

    // --- Manejo de la imagen de portada ---
    if (isset($_FILES['imagen_portada']) && $_FILES['imagen_portada']['error'] === UPLOAD_ERR_OK) {
        $carpeta_destino = "imagenes/";
        if (!is_dir($carpeta_destino)) {
            mkdir($carpeta_destino, 0755, true);
        }
        $nombre_base = preg_replace('/[^A-Za-z0-9_.-]/', '', basename($_FILES["imagen_portada"]["name"]));
        $nombre_archivo = uniqid() . "_" . $nombre_base;
        $ruta_destino = $carpeta_destino . $nombre_archivo;
        $tipos_permitidos = array("jpg", "jpeg", "webp", "png", "gif");
        $extension = strtolower(pathinfo($nombre_base, PATHINFO_EXTENSION));
        if (in_array($extension, $tipos_permitidos) && $_FILES['imagen_portada']['size'] <= 40000000) { // 40MB max
            if (move_uploaded_file($_FILES["imagen_portada"]["tmp_name"], $ruta_destino)) {
                $imagen_portada_ruta = $ruta_destino;
            } else {
                echo "Error al guardar la imagen de portada.";
                exit();
            }
        } else {
            echo "Formato de imagen de portada no válido o tamaño demasiado grande.";
            exit();
        }
    } elseif (isset($_FILES['imagen_portada']) && $_FILES['imagen_portada']['error'] != UPLOAD_ERR_NO_FILE) {
        // Depuración: mostrar el código de error de PHP si no es que no se subió ningún archivo
        echo "Error al subir la imagen de portada. Código de error: " . $_FILES['imagen_portada']['error'];
        exit();
    }

    // --- Manejo de la imagen del artículo ---
    if (isset($_FILES['imagen_articulo']) && $_FILES['imagen_articulo']['error'] === UPLOAD_ERR_OK) {
        $carpeta_destino = "imagenes/";
        if (!is_dir($carpeta_destino)) {
            mkdir($carpeta_destino, 0755, true);
        }
        $nombre_base = preg_replace('/[^A-Za-z0-9_.-]/', '', basename($_FILES["imagen_articulo"]["name"]));
        $nombre_archivo = uniqid() . "_" . $nombre_base;
        $ruta_destino = $carpeta_destino . $nombre_archivo;
        $tipos_permitidos = array("jpg", "jpeg", "webp", "png", "gif");
        $extension = strtolower(pathinfo($nombre_base, PATHINFO_EXTENSION));
        if (in_array($extension, $tipos_permitidos) && $_FILES['imagen_articulo']['size'] <= 40000000) { // 40MB max
            if (move_uploaded_file($_FILES["imagen_articulo"]["tmp_name"], $ruta_destino)) {
                $imagen_articulo_ruta = $ruta_destino;
            } else {
                echo "Error al guardar la imagen del artículo.";
                exit();
            }
        } else {
            echo "Formato de imagen del artículo no válido o tamaño demasiado grande.";
            exit();
        }
    } elseif (isset($_FILES['imagen_articulo']) && $_FILES['imagen_articulo']['error'] != UPLOAD_ERR_NO_FILE) {
        // Depuración: mostrar el código de error de PHP si no es que no se subió ningún archivo
        echo "Error al subir la imagen del artículo. Código de error: " . $_FILES['imagen_articulo']['error'];
        exit();
    }

    // Insertar el nuevo artículo
    $sql_insert = "INSERT INTO articulos (usuario_id, titulo, slug, contenido, categoria, fecha_publicacion, imagen_portada, imagen_articulo)
                   VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param(
        "issssss",
        $_SESSION["usuario_id"],
        $titulo,
        $slug_final,
        $contenido,
        $categoria,
        $imagen_portada_ruta,
        $imagen_articulo_ruta
    );

    if ($stmt_insert->execute()) {
        header("Location: blog.php");
        exit();
    } else {
        echo "Error al publicar el artículo: " . htmlspecialchars($stmt_insert->error);
    }

    $stmt_insert->close();
    $conn->close();

} else {
    // Si se accede a este archivo por GET (error o acceso directo)
    header("Location: publicar_articulo.php");
    exit();
}
?>