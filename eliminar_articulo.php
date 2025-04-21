<?php

session_start();

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
    die("Error de conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $articulo_id = $conn->real_escape_string($_POST["id"]);
    $usuario_id = $_SESSION["usuario_id"];

    // Primero, obtener el usuario_id del artículo
    $sql_select = "SELECT usuario_id, imagen_principal FROM articulos WHERE id = ?";
    $stmt_select = $conn->prepare($sql_select);
    $stmt_select->bind_param("i", $articulo_id);
    $stmt_select->execute();
    $resultado_select = $stmt_select->get_result();

    if ($resultado_select->num_rows == 1) {
        $articulo = $resultado_select->fetch_assoc();

        // Verificar si el usuario logueado es el autor del artículo
        if ($articulo['usuario_id'] === $usuario_id) {
            // Eliminar la imagen asociada si existe
            if (!empty($articulo['imagen_principal']) && file_exists($articulo['imagen_principal'])) {
                unlink($articulo['imagen_principal']);
            }

            // Eliminar el artículo
            $sql_delete = "DELETE FROM articulos WHERE id = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bind_param("i", $articulo_id);

            if ($stmt_delete->execute()) {
                echo "Artículo eliminado exitosamente. <a href='panel_usuario.php'>Volver a mi panel</a>";
            } else {
                echo "Error al eliminar el artículo: " . $conn->error;
            }

            $stmt_delete->close();
        } else {
            echo "No tienes permiso para eliminar este artículo.";
        }
    } else {
        echo "Artículo no encontrado.";
    }

    $stmt_select->close();
    $conn->close();
} else {
    echo "Acceso no permitido.";
}

?>