<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: panel_usuario.php");
    exit();
}

$host = 'localhost';
$usuario = 'root';
$contrasena = '';
$base_de_datos = 'portafolio_db';

$conn = new mysqli($host, $usuario, $contrasena, $base_de_datos);
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// Consultar todos los artículos (con paginación)
$articulos_por_pagina = 10; // Número de artículos por página
$pagina_actual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
$inicio = ($pagina_actual - 1) * $articulos_por_pagina;

$sql = "SELECT id, titulo, slug FROM articulos ORDER BY fecha_publicacion DESC LIMIT $inicio, $articulos_por_pagina";
$resultado = $conn->query($sql);
if (!$resultado) {
    die("Error en la consulta: " . $conn->error);
}

// Calcular el total de artículos
$sql_total = "SELECT COUNT(*) AS total FROM articulos";
$resultado_total = $conn->query($sql_total);
$total_articulos = $resultado_total->fetch_assoc()['total'];

// Calcular el número total de páginas
$total_paginas = ceil($total_articulos / $articulos_por_pagina);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración del Blog</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Administración del Blog</h1>

    <?php if ($resultado->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?= $fila['id'] ?></td>
                        <td><?= htmlspecialchars($fila['titulo']) ?></td>
                        <td>
                            <a href="editar_articulo.php?id=<?= $fila['id'] ?>" title="Editar artículo">Editar</a>
                            <form method="POST" action="eliminar_articulo.php" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $fila['id'] ?>">
                                <button type="submit" onclick="return confirm('¿Eliminar este artículo?')" title="Eliminar artículo">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Paginación -->
        <div class="paginacion">
            <?php if ($pagina_actual > 1): ?>
                <a href="?pagina=<?= $pagina_actual - 1 ?>">Anterior</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <a href="?pagina=<?= $i ?>" <?= ($i == $pagina_actual) ? 'class="active"' : '' ?>><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($pagina_actual < $total_paginas): ?>
                <a href="?pagina=<?= $pagina_actual + 1 ?>">Siguiente</a>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <p>No hay artículos publicados.</p>
    <?php endif; ?>

    <p><a href="publicar_articulo.php" title="Publicar un nuevo artículo">Publicar Nuevo</a></p>
    <p><a href="panel_usuario.php" title="Volver al panel de usuario">Volver al Panel</a></p>

    <?php $conn->close(); ?>
</body>
</html>