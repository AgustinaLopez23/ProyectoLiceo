<?php

// Datos de conexión a la base de datos (asegúrate de que sean correctos)
$host = 'localhost';
$usuario = 'root';
$contrasena = '';
$base_de_datos = 'portafolio_db';

// Establecer la conexión
$conn = new mysqli($host, $usuario, $contrasena, $base_de_datos);

// Verificar si hubo un error en la conexión
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

// Establecer el juego de caracteres a utf8
$conn->set_charset("utf8");

$articulo_slug = null;
if (isset($_GET['slug'])) {
    $articulo_slug = $_GET['slug'];
    // Sanitizar el slug para prevenir inyecciones SQL
    $articulo_slug = $conn->real_escape_string($articulo_slug);
} else {
    // Si no se proporciona un slug, podemos redirigir al blog o mostrar un error
    header("Location: blog.php"); // Redirigir al blog
    exit;
}

$sql = "SELECT id, titulo, contenido, fecha_publicacion, autor, imagen_principal FROM articulos WHERE slug = '$articulo_slug'";

$resultado = $conn->query($sql);

$articulo = null;
if ($resultado && $resultado->num_rows > 0) {
    $articulo = $resultado->fetch_assoc();
} else {
    // Si no se encuentra el artículo, podemos mostrar un mensaje de error
    $mensaje_error = "Artículo no encontrado.";
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title><?php echo htmlspecialchars($articulo['titulo'] ?? 'Artículo no encontrado'); ?></title>
</head>
<body class="pagina-articulo">
    <header>
        <h1>Nuestro Rincón de Fotografía - Blog</h1>
    </header>

    <main>
        <div class="articulo-container">
            <?php if (isset($mensaje_error)): ?>
                <h2 class="articulo-titulo"><?php echo $mensaje_error; ?></h2>
                <p><a href="blog.php" class="back-to-blog">Volver al Blog</a></p>
            <?php elseif ($articulo): ?>
                <h2 class="articulo-titulo"><?php echo htmlspecialchars($articulo['titulo']); ?></h2>
                <span class="articulo-fecha">Publicado el: <?php echo date('d/m/Y', strtotime($articulo['fecha_publicacion'])); ?> por <?php echo htmlspecialchars($articulo['autor']); ?></span>
                <?php if (!empty($articulo['imagen_principal'])): ?>
                    <img src="<?php echo htmlspecialchars($articulo['imagen_principal']); ?>" alt="<?php echo htmlspecialchars($articulo['titulo']); ?>" class="articulo-imagen">
                <?php endif; ?>
                <div class="articulo-contenido">
                    <?php echo nl2br(htmlspecialchars($articulo['contenido'])); ?>
                </div>
                <a href="blog.php" class="back-to-blog">Volver al Blog</a>
            <?php else: ?>
                <p>Cargando artículo...</p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
    </footer>
</body>
</html>