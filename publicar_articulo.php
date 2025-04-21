<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}

$categoria_seleccionada = isset($_GET['categoria']) ? $_GET['categoria'] : '';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Publicar Nuevo Artículo</title>
</head>
<body class="pagina-articulo-publicar">
    <header>
        <h1>Publicar Nuevo Artículo</h1>
    </header>

    <main>
        <form action="procesar_publicar_articulo.php" method="POST" enctype="multipart/form-data">
            <div>
                <label for="titulo">Título del Artículo:</label>
                <input type="text" id="titulo" name="titulo" required>
            </div>

            <div>
                <label for="categoria">Categoría:</label>
                <select id="categoria" name="categoria" required>
                    <option value="">Seleccionar Categoría</option>
                    <option value="consejos" <?php if ($categoria_seleccionada == 'consejos') echo 'selected'; ?>>Consejos</option>
                    <option value="experiencias" <?php if ($categoria_seleccionada == 'experiencias') echo 'selected'; ?>>Experiencias y Viajes</option>
                    <option value="comentarios" <?php if ($categoria_seleccionada == 'comentarios') echo 'selected'; ?>>Comentarios y Sugerencias</option>
                </select>
            </div>

            <div>
                <label for="contenido">Contenido del Artículo:</label>
                <textarea id="contenido" name="contenido" rows="20" required></textarea>
            </div>

            <div>
                <label for="autor">Tu Nombre:</label>
                <input type="text" id="autor" name="autor" value="<?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?>" readonly>
            </div>

            <div>
                <label for="imagen_principal">Imagen Principal:</label>
                <input type="file" id="imagen_principal" name="imagen_principal">
            </div>

            <button type="submit">Publicar Artículo</button>
        </form>

        <p><a href="panel_usuario.php">Volver a mi panel</a></p>
        <p><a href="blog.php">Volver al Blog</a></p>
    </main>

    <footer>
    </footer>
</body>
</html>