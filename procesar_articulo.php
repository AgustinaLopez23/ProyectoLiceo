<?php

session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}

// Incluir el código de conexión a la base de datos
$host = 'localhost';
$usuario = 'root';
$contrasena = '';
$base_de_datos = 'portafolio_db';

$conn = new mysqli($host, $usuario, $contrasena, $base_de_datos);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// Verificar si se recibieron datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir y sanitizar los datos del formulario
    $titulo = $conn->real_escape_string($_POST["titulo"]);
    $categoria = $conn->real_escape_string($_POST["categoria"]);
    $contenido = $conn->real_escape_string($_POST["contenido"]);
    $autor = $conn->real_escape_string($_POST["autor"]);
    $slug = !empty($_POST["slug"]) ? $conn->real_escape_string($_POST["slug"]) : generarSlug($titulo);
    $imagen_principal = null;

    // Manejar la carga de la imagen principal (portada)
    if (isset($_FILES["imagen_principal"]) && $_FILES["imagen_principal"]["error"] == 0) {
        $carpeta_destino = "images/blog/"; // Asegúrate de que esta carpeta exista y tenga permisos de escritura
        $nombre_base = basename($_FILES["imagen_principal"]["name"]);
        $nombre_archivo = uniqid() . "_" . $nombre_base; // Añadir un prefijo único para evitar conflictos
        $ruta_destino = $carpeta_destino . $nombre_archivo;
        $tipos_permitidos = array("jpg", "jpeg", "png", "gif");
        $extension = strtolower(pathinfo($nombre_base, PATHINFO_EXTENSION));

        if (in_array($extension, $tipos_permitidos)) {
            if (move_uploaded_file($_FILES["imagen_principal"]["tmp_name"], $ruta_destino)) {
                $imagen_principal = $ruta_destino; // Guardar la ruta para la base de datos
            } else {
                echo "Error al subir la imagen de portada.";
            }
        } else {
            echo "Solo se permiten archivos JPG, JPEG, PNG y GIF para la imagen de portada.";
        }
    }

    // Insertar los datos en la tabla 'articulos'
    $sql = "INSERT INTO articulos (usuario_id, titulo, contenido, fecha_publicacion, autor, categoria, slug, imagen)
            VALUES (?, ?, NOW(), ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssss", $_SESSION["usuario_id"], $titulo, $autor, $categoria, $slug, $imagen_principal, $contenido);

    if ($stmt->execute()) {
        echo "¡Artículo publicado exitosamente! <a href='blog.php'>Volver al Blog</a>";
    } else {
        echo "Error al publicar el artículo: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

} else {
    echo "Acceso no permitido.";
}

// Función para generar un slug amigable desde un título
function generarSlug($titulo) {
    $slug = strtolower(trim($titulo));
    $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
    return $slug;
}

?>