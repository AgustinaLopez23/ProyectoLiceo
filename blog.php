<?php

session_start();

// Datos de conexión a la base de datos (asegúrate de que sean correctos)
$host = 'localhost';
$usuario = 'root';
$contrasena = '';
$base_de_datos = 'portafolio_db';

$conn = new mysqli($host, $usuario, $contrasena, $base_de_datos);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// Obtener artículos por categoría
function obtenerArticulosPorCategoria($conn, $categoria) {
    $sql = "SELECT id, titulo, slug, contenido, fecha_publicacion FROM articulos WHERE categoria = ? ORDER BY fecha_publicacion DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $categoria);
    $stmt->execute();
    $resultado = $stmt->get_result();
    return $resultado;
}

// Obtener las diferentes categorías
$sql_categorias = "SELECT DISTINCT categoria FROM articulos";
$resultado_categorias = $conn->query($sql_categorias);
$categorias_existentes = [];
if ($resultado_categorias->num_rows > 0) {
    while ($fila = $resultado_categorias->fetch_assoc()) {
        $categorias_existentes[] = $fila['categoria'];
    }
} else {
    // Si no hay categorías dinámicas, puedes definirlas aquí como respaldo
    $categorias_existentes = ['consejos', 'experiencias', 'comentarios'];
}

// Determinar qué categoría mostrar
$categoria_seleccionada = isset($_GET['categoria']) ? $_GET['categoria'] : null;

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Mi Blog</title>
</head>
<body class="blog-list-page">
    <header>
        <h1>Blog</h1>
        <nav>
            <ul>
                <li><a href="ProyectoLiceo.php">Inicio</a></li>
                <li><a href="ProyectoLiceo.php#blog">Blog</a></li>
                <?php if (isset($_SESSION["usuario_id"])): ?>
                    <li><a href="panel_usuario.php">Mi Panel</a></li>
                    <li><a href="cerrar_sesion.php">Cerrar Sesión</a></li>
                <?php else: ?>
                    <li><a href="login.php">Iniciar Sesión</a></li>
                    <li><a href="registro.php">Registrarse</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main class="blog-list-page">
        <?php if ($categoria_seleccionada && in_array($categoria_seleccionada, $categorias_existentes)): ?>
            <section id="<?php echo strtolower(str_replace(' ', '-', $categoria_seleccionada)); ?>">
                <h2><?php echo htmlspecialchars(ucfirst($categoria_seleccionada)); ?></h2>
                <?php if (isset($_SESSION["usuario_id"])): ?>
                    <p><a href="publicar_articulo.php?categoria=<?php echo htmlspecialchars($categoria_seleccionada); ?>">Compartir mis <?php echo htmlspecialchars(ucfirst($categoria_seleccionada)); ?></a></p>
                <?php else: ?>
                    <p><a href="login.php">Inicia sesión</a> para compartir tu experiencia en <?php echo htmlspecialchars(ucfirst($categoria_seleccionada)); ?>.</p>
                <?php endif; ?>

                <?php
                $articulos = obtenerArticulosPorCategoria($conn, $categoria_seleccionada);
                if ($articulos->num_rows > 0):
                    while ($articulo = $articulos->fetch_assoc()):
                ?>
                    <article>
                        <h3><a href="articulo.php?slug=<?php echo htmlspecialchars($articulo['slug']); ?>"><?php echo htmlspecialchars($articulo['titulo']); ?></a></h3>
                        <p class="fecha">Publicado el: <?php echo date('d/m/Y', strtotime($articulo['fecha_publicacion'])); ?></p>
                        <div class="contenido-breve">
                            <?php
                            $extracto = substr(strip_tags($articulo['contenido']), 0, 200);
                            echo $extracto . (strlen(strip_tags($articulo['contenido'])) > 200 ? '...' : '');
                            ?>
                            <p><a href="articulo.php?slug=<?php echo htmlspecialchars($articulo['slug']); ?>">Leer más</a></p>
                        </div>
                    </article>
                <?php
                    endwhile;
                else:
                ?>
                    <p>No hay artículos en esta categoría todavía.</p>
                <?php endif; ?>
            </section>
        <?php else: ?>
            <?php foreach ($categorias_existentes as $categoria): ?>
                <section>
                    <h2><a href="?categoria=<?php echo htmlspecialchars($categoria); ?>"><?php echo htmlspecialchars(ucfirst($categoria)); ?></a></h2>
                    <?php
                    $articulos = obtenerArticulosPorCategoria($conn, $categoria);
                    if ($articulos->num_rows > 0):
                        while ($articulo = $articulos->fetch_assoc()):
                    ?>
                        <article>
                            <h3><a href="articulo.php?slug=<?php echo htmlspecialchars($articulo['slug']); ?>"><?php echo htmlspecialchars($articulo['titulo']); ?></a></h3>
                            <p class="fecha">Publicado el: <?php echo date('d/m/Y', strtotime($articulo['fecha_publicacion'])); ?></p>
                            <div class="contenido-breve">
                                <?php
                                $extracto = substr(strip_tags($articulo['contenido']), 0, 200);
                                echo $extracto . (strlen(strip_tags($articulo['contenido'])) > 200 ? '...' : '');
                                ?>
                                <p><a href="articulo.php?slug=<?php echo htmlspecialchars($articulo['slug']); ?>">Leer más</a></p>
                            </div>
                        </article>
                    <?php
                        endwhile;
                    else:
                    ?>
                        <p>No hay artículos en esta categoría todavía.</p>
                    <?php endif; ?>
                    <?php if (isset($_SESSION["usuario_id"])): ?>
                        <p><a href="publicar_articulo.php?categoria=<?php echo htmlspecialchars($categoria); ?>">Compartir mi experiencia en <?php echo htmlspecialchars(ucfirst($categoria)); ?></a></p>
                    <?php else: ?>
                        <p><a href="login.php">Inicia sesión</a> para compartir tu experiencia en <?php echo htmlspecialchars(ucfirst($categoria)); ?>.</p>
                    <?php endif; ?>
                </section>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Mi Blog</p>
    </footer>
</body>
</html>

<?php
$conn->close();
?>