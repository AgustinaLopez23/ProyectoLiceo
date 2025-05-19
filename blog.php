<?php

session_start();
session_regenerate_id(true); // Prevenir fijación de sesión

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

// Obtener artículos por categoría
function obtenerArticulosPorCategoria($conn, $categoria) {
    $sql = "SELECT id, titulo, slug, contenido, fecha_publicacion, imagen_portada FROM articulos WHERE categoria = ? ORDER BY fecha_publicacion DESC";
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
    <?php if ($categoria_seleccionada === 'comentarios'): ?>
        <section id="<?php echo strtolower(str_replace(' ', '-', $categoria_seleccionada)); ?>">
            <div class="comentarios-rectangulo">
                <h2><?php echo htmlspecialchars(ucfirst($categoria_seleccionada)); ?></h2>
                <?php if (isset($_SESSION["usuario_id"])): ?>
                    <p><a href="publicar_articulo.php?categoria=<?php echo htmlspecialchars($categoria_seleccionada); ?>" class="compartir-experiencia">Compartir</a></p>
                <?php else: ?>
                    <p><a href="login.php" class="compartir-experiencia">Inicia sesión</a> para compartir.</p>
                <?php endif; ?>
                <div class="comentarios-grid">
                    <?php
                    $articulos = obtenerArticulosPorCategoria($conn, $categoria_seleccionada);
                    if ($articulos->num_rows > 0):
                        while ($articulo = $articulos->fetch_assoc()):
                    ?>
                            <div class="comentario-item">
                                <p><?php echo nl2br(htmlspecialchars($articulo['contenido'])); ?></p>
                                <a href="articulo.php?slug=<?php echo htmlspecialchars($articulo['slug']); ?>" class="leer-mas-boton">Ver más</a>
                            </div>
                    <?php
                        endwhile;
                    else:
                    ?>
                        <p>No hay comentarios todavía.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php elseif ($categoria_seleccionada): ?>
        <section id="<?php echo strtolower(str_replace(' ', '-', $categoria_seleccionada)); ?>">
            <h2><?php echo htmlspecialchars(ucfirst($categoria_seleccionada)); ?></h2>
            <?php if (isset($_SESSION["usuario_id"])): ?>
                <p><a href="publicar_articulo.php?categoria=<?php echo htmlspecialchars($categoria_seleccionada); ?>">Compartir</a></p>
            <?php else: ?>
                <p><a href="login.php">Inicia sesión</a> para compartir.</p>
            <?php endif; ?>
            <div class="<?php echo $categoria_seleccionada === 'experiencias' ? 'comentarios-grid' : 'consejos-grid'; ?>">
                <?php
                $articulos = obtenerArticulosPorCategoria($conn, $categoria_seleccionada);
                if ($articulos->num_rows > 0):
                    while ($articulo = $articulos->fetch_assoc()):
                ?>
                        <?php if ($categoria_seleccionada === 'experiencias'): ?>
                            <div class="comentario-item">
                                <p><?php echo nl2br(htmlspecialchars($articulo['contenido'])); ?></p>
                                <a href="articulo.php?slug=<?php echo htmlspecialchars($articulo['slug']); ?>" class="leer-mas-boton">Ver más</a>
                            </div>
                        <?php else: ?>
                            <article class="consejo-item">
                                <div class="consejo-imagen">
                                    <?php if (!empty($articulo['imagen_portada'])): ?>
                                        <img src="<?php echo htmlspecialchars($articulo['imagen_portada']); ?>" alt="<?php echo htmlspecialchars($articulo['titulo']); ?>">
                                    <?php endif; ?>
                                    <div class="consejo-contenido-overlay">
                                        <h3><?php echo htmlspecialchars($articulo['titulo']); ?></h3>
                                        <a href="articulo.php?slug=<?php echo htmlspecialchars($articulo['slug']); ?>" class="leer-mas-boton">Ver más</a>
                                    </div>
                                </article>
                        <?php endif; ?>
                <?php
                    endwhile;
                else:
                ?>
                    <p>No hay artículos en esta categoría todavía.</p>
                <?php endif; ?>
            </div>
        </section>
    <?php else: ?>
        <?php foreach ($categorias_existentes as $categoria): ?>
            <section>
                <h2><a href="?categoria=<?php echo htmlspecialchars($categoria); ?>"><?php echo htmlspecialchars(ucfirst($categoria)); ?></a></h2>
                <div class="<?php echo ($categoria === 'comentarios' || $categoria === 'experiencias') ? 'comentarios-grid' : 'consejos-grid'; ?>">
                    <?php
                    $articulos = obtenerArticulosPorCategoria($conn, $categoria);
                    if ($articulos->num_rows > 0):
                        while ($articulo = $articulos->fetch_assoc()):
                    ?>
                            <?php if ($categoria === 'comentarios' || $categoria === 'experiencias'): ?>
                                <div class="comentario-item">
                                    <p><?php echo nl2br(htmlspecialchars($articulo['contenido'])); ?></p>
                                    <a href="articulo.php?slug=<?php echo htmlspecialchars($articulo['slug']); ?>" class="leer-mas-boton">Ver más</a>
                                </div>
                            <?php else: ?>
                                <article class="consejo-item">
                                    <div class="consejo-imagen">
                                        <?php if (!empty($articulo['imagen_portada'])): ?>
                                            <img src="<?php echo htmlspecialchars($articulo['imagen_portada']); ?>" alt="<?php echo htmlspecialchars($articulo['titulo']); ?>">
                                        <?php endif; ?>
                                        <div class="consejo-contenido-overlay">
                                            <h3><?php echo htmlspecialchars($articulo['titulo']); ?></h3>
                                            <a href="articulo.php?slug=<?php echo htmlspecialchars($articulo['slug']); ?>" class="leer-mas-boton">Ver más</a>
                                        </div>
                                    </div>
                                </article>
                            <?php endif; ?>
                    <?php
                        endwhile;
                    else:
                    ?>
                        <p>No hay artículos en esta categoría todavía.</p>
                    <?php endif; ?>
                    <?php if (isset($_SESSION["usuario_id"])): ?>
                        <p><a href="publicar_articulo.php?categoria=<?php echo htmlspecialchars($categoria); ?>">Compartir</a></p>
                    <?php else: ?>
                        <p><a href="login.php">Inicia sesión</a> para compartir.</p>
                    <?php endif; ?>
                </div>
            </section>
        <?php endforeach; ?>
    <?php endif; ?>
    </main>
    <?php
    $conn->close();
    ?>