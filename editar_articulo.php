<?php

session_start();
session_regenerate_id(true); // Prevenir fijación de sesión

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
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

$articulo_id = isset($_GET['id']) ? $conn->real_escape_string($_GET['id']) : null;

if (!$articulo_id) {
    die("ID de artículo no válido.");
}

$sql = "SELECT id, titulo, contenido, categoria, autor, slug, imagen_articulo, imagen_portada, usuario_id FROM articulos WHERE id = '$articulo_id'";
$resultado = $conn->query($sql);

$articulo = $resultado->fetch_assoc();

if (!$articulo) {
    die("Artículo no encontrado.");
}

if ((int)$articulo['usuario_id'] !== (int)$_SESSION["usuario_id"]) {
    die("No tienes permiso para editar este artículo.");
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Editar Artículo: <?php echo htmlspecialchars($articulo['titulo']); ?></title>
</head>

<body class="pagina-editar-articulo">
    <header>
        <h1>Editar Artículo</h1>
    </header>

    <main>
        <form action="procesar_editar_articulo.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $articulo['id']; ?>">

            <div>
                <label for="titulo">Título del Artículo:</label>
                <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($articulo['titulo']); ?>" required>
            </div>

            <div>
                <label for="contenido">Contenido del Artículo:</label>
                <textarea id="contenido" name="contenido" rows="20" required style="width: 95%; box-sizing: border-box;"><?php echo htmlspecialchars($articulo['contenido']); ?></textarea>
            </div>

            <div>
                <label for="categoria">Categoría:</label>
                <select id="categoria" name="categoria" required>
                    <option value="">Seleccionar Categoría</option>
                    <option value="consejos" <?php if ($articulo['categoria'] == 'consejos') echo 'selected'; ?>>Consejos</option>
                    <option value="experiencias" <?php if ($articulo['categoria'] == 'experiencias') echo 'selected'; ?>>Experiencias y Viajes</option>
                    <option value="comentarios" <?php if ($articulo['categoria'] == 'comentarios') echo 'selected'; ?>>Comentarios y Sugerencias</option>
                </select>
            </div>

            <div>
                <label for="autor">Autor:</label>
                <input type="text" id="autor" name="autor" value="<?php echo htmlspecialchars($articulo['autor']); ?>" required>
            </div>

            <div>
                <label for="imagen_portada_nueva">Imagen de Portada:</label>
                <?php if (!empty($articulo['imagen_portada'])): ?>
                    <p>Imagen actual: <img src="<?php echo htmlspecialchars($articulo['imagen_portada']); ?>" alt="Imagen de portada actual" style="max-width: 100px; height: auto;"></p>
                    <label><input type="checkbox" name="eliminar_imagen_portada" value="1"> Eliminar imagen actual</label>
                <?php else: ?>
                    <p>No hay imagen de portada actual.</p>
                <?php endif; ?>
                <input type="file" id="imagen_portada_nueva" name="imagen_portada_nueva">
                <small>Selecciona una nueva imagen para reemplazar la actual (opcional).</small>
            </div>

            <div>
                <label for="imagen_articulo_nueva">Imagen Principal del Artículo:</label>
                <?php if (!empty($articulo['imagen_articulo'])): ?>
                    <p>Imagen actual: <img src="<?php echo htmlspecialchars($articulo['imagen_articulo']); ?>" alt="Imagen principal actual" style="max-width: 100px; height: auto;"></p>
                    <label><input type="checkbox" name="eliminar_imagen_articulo" value="1"> Eliminar imagen actual</label>
                <?php else: ?>
                    <p>No hay imagen principal actual.</p>
                <?php endif; ?>
                <input type="file" id="imagen_articulo_nueva" name="imagen_articulo_nueva">
                <small>Selecciona una nueva imagen para reemplazar la actual (opcional).</small>
            </div>

            <button type="submit">Guardar Cambios</button>
        </form>

        <p><a href="panel_usuario.php">Volver a mi panel</a></p>
    </main>

    <footer>
    </footer>
</body>
</html>