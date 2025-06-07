<?php
//conexion.php
$host = 'localhost';
$usuario = 'root';
$contrasena = '';
$base_de_datos = 'portafolio_db';

$conn = new mysqli($host, $usuario, $contrasena, $base_de_datos);
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}
$conn->set_charset("utf8");

session_start(); // Para usar $_SESSION['user_id']

if (!isset($_GET['slug'])) {
    header("Location: blog.php");
    exit;
}

$articulo_slug = $conn->real_escape_string($_GET['slug']);

$sql = "SELECT id, titulo, contenido, fecha_publicacion, autor, imagen_articulo, slug FROM articulos WHERE slug = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $articulo_slug);
$stmt->execute();
$resultado = $stmt->get_result();
$stmt->close();

$articulo = null;
if ($resultado && $resultado->num_rows > 0) {
    $articulo = $resultado->fetch_assoc();
} else {
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
        <h1> Blog </h1>
    </header>

    <main>
        <div class="articulo-container">
            <?php if (isset($mensaje_error)): ?>
                <h2 class="articulo-titulo"><?php echo $mensaje_error; ?></h2>
                <p><a href="blog.php" class="back-to-blog">Volver al Blog</a></p>
            <?php elseif ($articulo): ?>
                <h2 class="articulo-titulo"><?php echo htmlspecialchars($articulo['titulo']); ?></h2>
                <span class="articulo-fecha">Publicado el: <?php echo date('d/m/Y', strtotime($articulo['fecha_publicacion'])); ?> por <?php echo htmlspecialchars($articulo['autor']); ?></span>
                
                <?php if (!empty($articulo['imagen_articulo'])): ?>
                    <img src="<?php echo htmlspecialchars($articulo['imagen_articulo']); ?>" alt="<?php echo htmlspecialchars($articulo['titulo']); ?>" class="articulo-imagen">
                <?php endif; ?>

                <div class="articulo-contenido">
                    <?php echo nl2br(htmlspecialchars($articulo['contenido'])); ?>
                </div>

                <div class="contenedor-botones">
                    <a href="blog.php" class="back-to-blog">Volver al Blog</a>
                    <a href="editar_articulo.php?id=<?php echo $articulo['id']; ?>" class="boton-editar">Editar Artículo</a>
                </div>
            <?php else: ?>
                <p>Cargando artículo...</p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
    </footer>
</body>
</html>

<?php $conn->close(); ?>
