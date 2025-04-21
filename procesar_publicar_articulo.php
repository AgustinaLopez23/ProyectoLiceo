<?php
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}

// Incluye la conexión a la base de datos
include "conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $contenido = $_POST['contenido'];
    $categoria = $_POST['categoria'];

    // Función para generar un slug amigable
    function generarSlug($titulo) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $titulo)));
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
        return $count == 0; // Devuelve true si el slug es único
    }

    // Generar el slug base
    $slug_base = generarSlug($titulo);
    $slug_final = $slug_base;
    $contador = 1;

    // Verificar si el slug ya existe y generar uno nuevo si es necesario
    while (!verificarSlugUnico($conn, $slug_final)) {
        $slug_final = $slug_base . '-' . $contador;
        $contador++;
    }

    // Insertar el nuevo artículo con el slug generado
    $sql_insert = "INSERT INTO articulos (usuario_id, titulo, slug, contenido, categoria, fecha_publicacion) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("issss", $_SESSION["usuario_id"], $titulo, $slug_final, $contenido, $categoria);

    if ($stmt_insert->execute()) {
        // Artículo publicado con éxito
        header("Location: blog.php");
        exit();
    } else {
        echo "Error al publicar el artículo: " . $stmt_insert->error;
    }

    $stmt_insert->close();
    $conn->close();

} else {
    // Si se accede a este archivo por GET (error o acceso directo)
    header("Location: publicar_articulo.php");
    exit();
}
?>