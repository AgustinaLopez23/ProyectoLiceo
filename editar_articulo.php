<?php
session_start();
session_regenerate_id(true);
if (!isset($_SESSION['usuario_id'])) {
    die("No estás autenticado.");
}

$host = 'localhost';
$usuario = 'root';
$contrasena = '';
$base_de_datos = 'portafolio_db';

$conn = new mysqli($host, $usuario, $contrasena, $base_de_datos);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
$conn->set_charset("utf8");

if (!isset($_GET['id'])) {
    die("Acceso inválido.");
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM articulos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
if (!$resultado || $resultado->num_rows == 0) {
    die("Artículo no encontrado.");
}
$articulo = $resultado->fetch_assoc();

if ($articulo['usuario_id'] != $_SESSION['usuario_id']) {
    die("No tienes permiso para editar este artículo.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Artículo</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="pagina-editar-articulo">
    <header class="pagina-editar-articulo-header">
        Editar Artículo
    </header>
    <main class="pagina-editar-articulo-main">
        <form action="procesar_editar_articulo.php" method="POST" enctype="multipart/form-data" class="pagina-editar-articulo-form">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($articulo['id']); ?>">
            <div>
                <label for="titulo">Título del Artículo:</label>
                <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($articulo['titulo']); ?>" required>
            </div>
            <div>
                <label for="categoria">Categoría:</label>
                <input type="text" id="categoria" name="categoria" value="<?php echo htmlspecialchars($articulo['categoria']); ?>" required>
            </div>
            <div>
                <label for="autor">Autor:</label>
                <input type="text" id="autor" name="autor" value="<?php echo htmlspecialchars($articulo['autor']); ?>" required>
            </div>
            <div>
                <label for="contenido">Contenido del Artículo:</label>
                <textarea id="contenido" name="contenido" rows="10" required><?php echo htmlspecialchars($articulo['contenido']); ?></textarea>
            </div>
            <div>
                <label>Imagen de Portada Actual:</label>
                <?php if ($articulo['imagen_portada']): ?>
                    <div class="imagen-actual">
                        <img src="<?php echo htmlspecialchars($articulo['imagen_portada']); ?>" alt="Imagen de portada actual">
                    </div>
                    <label>
                        <input type="checkbox" name="eliminar_imagen_portada" value="1">
                        Eliminar imagen portada
                    </label>
                <?php else: ?>
                    <em>No hay imagen de portada.</em>
                <?php endif; ?>
                <input type="file" name="imagen_portada_nueva" accept="image/*">
            </div>
            <div>
                <label>Imagen del Artículo Actual:</label>
                <?php if ($articulo['imagen_articulo']): ?>
                    <div class="imagen-actual">
                        <img src="<?php echo htmlspecialchars($articulo['imagen_articulo']); ?>" alt="Imagen de artículo actual">
                    </div>
                    <label>
                        <input type="checkbox" name="eliminar_imagen_articulo" value="1">
                        Eliminar imagen artículo
                    </label>
                <?php else: ?>
                    <em>No hay imagen de artículo.</em>
                <?php endif; ?>
                <input type="file" name="imagen_articulo_nueva" accept="image/*">
            </div>
            <button type="submit">Guardar Cambios</button>
            <div class="opciones">
                <a href="panel_usuario.php">Volver al Panel de Usuario</a>
            </div>
        </form>
    </main>
    <footer class="pagina-editar-articulo-footer">
        &copy; <?php echo date("Y"); ?> - Panel de Edición de Artículos
    </footer>
</body>
</html>