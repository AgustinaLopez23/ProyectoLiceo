<?php

// Iniciar la sesión (si no está ya iniciada)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
    session_regenerate_id(true); // Prevenir fijación de sesión
}

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario_id"])) {
    header("Location: /ProyectoLiceo/login.php"); // Usando ruta absoluta para la redirección
    exit();
}

// Datos de conexión a la base de datos
$host = 'localhost';
$usuario = 'root';
$contrasena = '';
$base_de_datos = 'portafolio_db';

// Crear una nueva conexión a la base de datos
$conn = new mysqli($host, $usuario, $contrasena, $base_de_datos);

// Verificar si la conexión fue exitosa
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

// Establecer la codificación de caracteres a UTF-8
$conn->set_charset("utf8");

// Obtener el ID del usuario de la sesión
$usuario_id = $_SESSION["usuario_id"];

// Consultar los artículos del usuario logueado
$sql = "SELECT id, titulo, slug FROM articulos WHERE usuario_id = ? ORDER BY fecha_publicacion DESC";
$stmt = $conn->prepare($sql);

// Verificar si la preparación de la consulta fue exitosa
if (!$stmt) {
    die("Error al preparar la consulta: " . $conn->error);
}

// Vincular el parámetro a la consulta preparada
$stmt->bind_param("i", $usuario_id);

// Ejecutar la consulta
$stmt->execute();

// Obtener el resultado de la consulta
$resultado = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuario</title>
    <link rel="stylesheet" href="/ProyectoLiceo/styles.css">
</head>

<body class="panel-usuario-body">
    <div class="panel-usuario-container">
       <h1 class="panel-titulo panel-usuario-h1">
        Panel de Usuario
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-fill-gear icono-panel" viewBox="0 0 16 16">
        <path d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-9 8c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4m9.886-3.54c.18-.613 1.048-.613 1.229 0l.043.148a.64.64 0 0 0 .921.382l.136-.074c.561-.306 1.175.308.87.869l-.075.136a.64.64 0 0 0 .382.92l.149.045c.612.18.612 1.048 0 1.229l-.15.043a.64.64 0 0 0-.38.921l.074.136c.305.561-.309 1.175-.87.87l-.136-.075a.64.64 0 0 0-.92.382l-.045.149c-.18.612-1.048.612-1.229 0l-.043-.15a.64.64 0 0 0-.921-.38l-.136.074c-.561.305-1.175-.309-.87-.87l.075-.136a.64.64 0 0 0-.382-.92l-.148-.045c-.613-.18-.613-1.048 0-1.229l.148-.043a.64.64 0 0 0 .382-.921l-.074-.136c-.306-.561.308-1.175.869-.87l.136.075a.64.64 0 0 0 .92-.382zM14 12.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0"/>
        </svg>
    </h1>


        <p class="panel-usuario-logout"><a href="/ProyectoLiceo/cerrar_sesion.php">Cerrar Sesión</a></p>

        <h2 class="panel-titulo panel-usuario-h2">Mis Artículos Publicados</h2>

        <?php if ($resultado->num_rows > 0): ?>
            <table class="articulos-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título del Artículo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($fila = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $fila['id']; ?></td>
                            <td><?php echo htmlspecialchars($fila['titulo']); ?></td>
                            <td class="actions">
                                <a href="/ProyectoLiceo/editar_articulo.php?id=<?php echo $fila['id']; ?>">Editar</a>
                                <form method="POST" action="/ProyectoLiceo/eliminar_articulo.php" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo $fila['id']; ?>">
                                    <button type="submit" class="delete-btn" onclick="return confirm('¿Estás seguro de que quieres eliminar este artículo?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-articulos">No has publicado ningún artículo aún.</p>
        <?php endif; ?>

        <div class="panel-usuario-acciones">
            <a href="/ProyectoLiceo/publicar_articulo.php">Publicar Nuevo Artículo</a>
            <a href="/ProyectoLiceo/blog.php" class="volver-blog">Volver al Blog</a>
        </div>
    </div>

    <?php
    // Cerrar la declaración y la conexión
    if ($stmt) {
        $stmt->close();
    }
    if ($conn) {
        $conn->close();
    }
    ?>
</body>
</html>