<?php

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

    // Manejar la carga de la imagen
    if (isset($_FILES["imagen_principal"]) && $_FILES["imagen_principal"]["error"] == 0) {
        $carpeta_destino = "images/blog/"; // Crea esta carpeta en tu proyecto si no existe
        $nombre_base = basename($_FILES["imagen_principal"]["name"]);
        $nombre_archivo = uniqid() . "_" . $nombre_base; // Añadir un prefijo único para evitar conflictos
        $ruta_destino = $carpeta_destino . $nombre_archivo;
        $tipos_permitidos = array("jpg", "jpeg", "png", "gif");
        $extension = strtolower(pathinfo($nombre_base, PATHINFO_EXTENSION));

        if (in_array($extension, $tipos_permitidos)) {
            if (move_uploaded_file($_FILES["imagen_principal"]["tmp_name"], $ruta_destino)) {
                $imagen_principal = $ruta_destino; // Guardar la ruta en la base de datos
            } else {
                echo "Error al subir la imagen.";
            }
        } else {
            echo "Solo se permiten archivos JPG, JPEG, PNG y GIF.";
        }
    }

    // Insertar los datos en la tabla 'articulos'
    $sql = "INSERT INTO articulos (titulo, contenido, fecha_publicacion, autor, categoria, slug, imagen_principal)
            VALUES ('$titulo', '$contenido', NOW(), '$autor', '$categoria', '$slug', '$imagen_principal')";

    if ($conn->query($sql) === TRUE) {
        echo "¡Artículo publicado exitosamente! <a href='blog.php'>Volver al Blog</a>";
    } else {
        echo "Error al publicar el artículo: " . $conn->error;
    }

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