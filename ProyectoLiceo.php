<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Portafolio de Agustina Lopez, fotógrafa aficionada. Descubre sus mejores trabajos.">
    <meta name="keywords" content="fotografía, portfolio, Agustina Lopez, fotografía aficionada">
    <title>Portafolio de Agustina Lopez</title>

    <style>
        /* Global Styles (from styles.css) - Essential for initial layout */
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif; 
            
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        /* Estilos para el menú de navegación principal  */
        .main-nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: #00000032;
            color: #fff;
            z-index: 100;
            padding: 20px 0;
            margin: 0;
        }

        .main-nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
        }

        .main-nav li {
            margin: 0 10px;
        }

        .main-nav a {
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
        }

        .main-nav a:hover {
            background-color: #79a976;
            color: #000;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Media query para pantallas pequeñas */
        @media (max-width: 768px) {
            .main-nav ul {
                flex-direction: column;
                align-items: center;
            }
            .main-nav li {
                margin: 5px 0;
            }
        }

        /* Estilos para la sección de inicio (Hero)  */
        .hero {
            position: relative;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: #fff;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }

        .hero-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-size: cover;
            background-position: center;
        }

        .hero-content {
            padding: 20px;
            z-index: 10;
        }

        .hero-content h1 {
            font-size: 3em;
            margin-bottom: 20px;
            font-weight: 400;
            text-shadow: 2px 2px 4px #00000039;
        }

        .hero-content .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #00000098;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            border: 1px solid #fff;
            transition: background-color 0.3s ease;
            font-size: 1.1em;
        }

        .hero-content .button:hover {
            background-color: #1f522b66;
        }

        /* Media query para pantallas pequeñas */
        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2em;
            }
            .hero-content .button {
                font-size: 0.9em;
                padding: 10px 15px;
            }
        }

        /* SECCIÓN SOBRE MÍ  */
        #sobremi.intro-section {
            background: -webkit-linear-gradient(90deg, #274427,#64a34d);
            background: linear-gradient(90deg, #274427,#64a34d);
            color: #000;
            padding: 40px 20px;
            text-align: center;
            font-family: 'Arial', sans-serif; 
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 200px;
        }

        #sobremi.intro-section .titulo-principal {
            font-size: 2em;
            font-weight: 300;
            margin-bottom: 15px;
        }

        #sobremi.intro-section .mensaje-bienvenida {
            font-size: 1em;
            line-height: 1.5;
            color: #eee;
            margin: 0 auto 15px;
        }

        /* Estilos para el contenedor de redes sociales */
        .redes-sociales {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }

        /* Estilos para el enlace del icono */
        .redes-sociales a {
            color: #000;
            text-decoration: none;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }

        .redes-sociales a:hover {
            opacity: 1;
        }



        /* Media query para pantallas pequeñas */
        @media (max-width: 768px) {
            #sobremi.intro-section .titulo-principal {
                font-size: 1.5em;
            }
            #sobremi.intro-section .mensaje-bienvenida {
                max-width: 90%;
            }
            .redes-sociales a .bi-instagram {
                font-size: 20px;
            }
        }

        /* CSS para los íconos de like SVG */
        .heart-icon {
            vertical-align: middle;
        }

        .like-btn.liked .heart-empty-icon {
            display: none !important; 
        }
        .like-btn.liked .heart-filled-icon {
            display: block !important; 
            color: #E74C3C; 
        }
    </style>

    <link rel="preload" href="styles.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="styles.css"></noscript>
    

<?php
    include "conexion.php"; // Incluye la conexión a la base de datos

    // Manejo de errores para la conexión a la base de datos
    if (!$conn) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    $sql_inicio = "SELECT ruta FROM inicio LIMIT 1";
    $result_inicio = $conn->query($sql_inicio);

    if ($result_inicio && $result_inicio->num_rows > 0) {
        $row_inicio = $result_inicio->fetch_assoc();
        $imagen_inicio = 'imagenes/' . htmlspecialchars($row_inicio["ruta"]);
    } else {
        $imagen_inicio = 'images/default-hero.jpg'; // Valor por defecto para la imagen de inicio
    }
?>
</head>
<body>
    <?php
    // BLOQUE PHP PARA OBTENER LAS IMÁGENES DEL BLOG
    $sql_blog_images = "SELECT ruta, categoria FROM blog";
    $result_blog_images = $conn->query($sql_blog_images);
    $imagenes_blog = array();

    if ($result_blog_images && $result_blog_images->num_rows > 0) {
        while ($row_blog = $result_blog_images->fetch_assoc()) {
            $ruta_corregida = str_replace('\\', '/', $row_blog["ruta"]);
            $ruta_final = '';
            // Verifica si la ruta ya comienza con 'imagenes/'
            if (strpos(strtolower($ruta_corregida), 'imagenes/') === 0) {
                $ruta_final = htmlspecialchars($ruta_corregida);
            } else {
                $ruta_final = 'imagenes/' . htmlspecialchars($ruta_corregida);
            }
            $imagenes_blog[strtolower($row_blog["categoria"])] = $ruta_final;
        }
    } else {
        echo "<p class='error-message'>Error al cargar las imágenes del blog.</p>";
        if ($conn->error) {
            echo "<p class='error-details'>" . htmlspecialchars($conn->error) . "</p>";
        }
    }
    ?>

    <nav class="main-nav">
        <ul>
            <li><a href="#inicio">INICIO</a></li>
            <li><a href="#sobremi">SOBRE MÍ</a></li>
            <li><a href="#portfolio">GALERIA</a></li>
            <li><a href="#servicios">SERVICIOS</a></li>
            <li><a href="#blog">BLOG</a></li>
            <li><a href="#contacto">CONTACTO</a></li>
        </ul>
    </nav>

    <section id="inicio" class="hero">
        <div class="hero-image" style="background-image: url('<?php echo $imagen_inicio; ?>')">
        </div>
        <div class="hero-content">
            <h1>Bienvenidos a mi portafolio</h1>
            <a href="#portfolio" class="button">Ver galeria</a>
        </div>
    </section>

    <section id="sobremi" class="sobremi intro-section">
        <div class="intro-seccion">
            <p class="titulo-principal">¡Hola! Soy Agustina Lopez, fotógrafa aficionada y creadora visual.</p>
            <p class="mensaje-bienvenida">Explora mi mundo a través de mis imágenes y sígueme en Instagram para ver más.
            </p>
            <div class="redes-sociales">
                <a href="https://www.instagram.com/aguslopez_fotografia/" target="_blank" rel="noopener noreferrer">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-instagram" viewBox="0 0 16 16">
                <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.11.281-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/>
                </svg>
                </a>
            </div>
        </div>
    </section>

<section id="portfolio" class="portfolio">
    <nav>
        <ul>
            <li><a href="#paisajes">Paisajes</a></li>
            <li><a href="#autos">Autos</a></li>
            <li><a href="#eventos">Eventos</a></li>
            <li><a href="#viajes">Viajes</a></li>
        </ul>
    </nav>
</section>

<section id="paisajes" class="gallery">
    <h3>Fotografías de Paisajes</h3>
    <div class="galeria">
        <?php
        $sql = "SELECT * FROM fotos_paisajes";
        $result = $conn->query($sql);

        if ($result) {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $paisajes_id = htmlspecialchars($row["paisajes_id"]);
                    $ruta = htmlspecialchars($row["ruta"]);
                    $nombre = htmlspecialchars($row["nombre"]);
                    $descripcion = htmlspecialchars($row["descripcion"]);

                    // Contar los likes para esta imagen
                    $sql_count = "SELECT COUNT(*) FROM likes WHERE item_id = ? AND tabla_origen = 'paisajes'";
                    $stmt_count = $conn->prepare($sql_count);
                    $stmt_count->bind_param("i", $paisajes_id);
                    $stmt_count->execute();
                    $result_count = $stmt_count->get_result();
                    $likes_count = (int)$result_count->fetch_row()[0];
                    $stmt_count->close();

                    // Verificar si el usuario actual ya dio like (si hay sesión de usuario)
                    $liked_class = '';
                    $display_empty = 'block'; // Mostrar corazón vacío por defecto
                    $display_filled = 'none'; // Ocultar corazón lleno por defecto

                    if (isset($_SESSION['usuario_id'])) {
                        $sql_check_liked = "SELECT id_like FROM likes WHERE item_id = ? AND tabla_origen = 'paisajes' AND usuario_id = ?";
                        $stmt_check_liked = $conn->prepare($sql_check_liked);
                        $stmt_check_liked->bind_param("ii", $paisajes_id, $_SESSION['usuario_id']);
                        $stmt_check_liked->execute();
                        $result_check_liked = $stmt_check_liked->get_result();
                        if ($result_check_liked->num_rows > 0) {
                            $liked_class = ' liked';
                            $display_empty = 'none';    // Si ya dio like, ocultar vacío
                            $display_filled = 'block';  // y mostrar lleno
                        }
                        $stmt_check_liked->close();
                    }

                    echo '<div class="item">';
                    echo '<img src="' . $ruta . '" alt="' . $nombre . '" data-description="' . $descripcion . '">';
                    echo '<p>' . $descripcion . '</p>';
                    echo '<div class="like-container">';
                    echo '<button class="like-btn' . $liked_class . '" data-image-id="' . $paisajes_id . '" data-tabla-origen="paisajes">';
                    // SVG del corazón vacío - RUTA CORREGIDA
                    echo '<svg class="heart-icon heart-empty-icon" width="30" height="30" fill="currentColor" viewBox="0 0 16 16" style="display: ' . $display_empty . ';">';
                    echo '<path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"/>';
                    echo '</svg>';
                    // SVG del corazón lleno
                    echo '<svg class="heart-icon heart-filled-icon" width="30" height="30" fill="#e74c3c" viewBox="0 0 16 16" style="display: ' . $display_filled . ';">';
                    echo '<path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314"/>';
                    echo '</svg>';
                    echo '</button>';
                    echo '<span class="like-count">' . $likes_count . '</span>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "<p>No se encontraron imágenes.</p>";
            }
        } else {
            echo "<p>Error en la consulta de la base de datos: " . $conn->error . "</p>";
        }
        ?>
    </div>
</section>


<section id="autos" class="gallery">
    <h3>Fotografías de Autos</h3>
    <div class="galeria">
        <?php
        $sql_autos = "SELECT * FROM fotos_autos WHERE categoria = 'autos'";
        $result_autos = $conn->query($sql_autos);

        if ($result_autos) {
            if ($result_autos->num_rows > 0) {
                while ($fila = $result_autos->fetch_assoc()) {
                    $autos_id = htmlspecialchars($fila["autos_id"]);
                    $ruta = htmlspecialchars($fila['ruta']);
                    $nombre = htmlspecialchars($fila['nombre']);

                    // Contar los likes para esta imagen
                    $sql_count = "SELECT COUNT(*) FROM likes WHERE item_id = ? AND tabla_origen = 'autos'";
                    $stmt_count = $conn->prepare($sql_count);
                    $stmt_count->bind_param("i", $autos_id);
                    $stmt_count->execute();
                    $result_count = $stmt_count->get_result();
                    $likes_count = (int)$result_count->fetch_row()[0];
                    $stmt_count->close();

                    // Verificar si el usuario actual ya dio like (si hay sesión de usuario)
                    $liked_class = '';
                    $display_empty = 'block';
                    $display_filled = 'none';
                    if (isset($_SESSION['usuario_id'])) {
                        $sql_check_liked = "SELECT id_like FROM likes WHERE item_id = ? AND tabla_origen = 'autos' AND usuario_id = ?";
                        $stmt_check_liked = $conn->prepare($sql_check_liked);
                        $stmt_check_liked->bind_param("ii", $autos_id, $_SESSION['usuario_id']);
                        $stmt_check_liked->execute();
                        $result_check_liked = $stmt_check_liked->get_result();
                        if ($result_check_liked->num_rows > 0) {
                            $liked_class = ' liked';
                            $display_empty = 'none';
                            $display_filled = 'block';
                        }
                        $stmt_check_liked->close();
                    }

                    echo '<div class="item">';
                    echo '<img src="' . $ruta . '" alt="' . $nombre . '" data-description="' . $nombre . '">';
                    echo '<p>' . $nombre . '</p>';
                    echo '<div class="like-container">';
                    echo '<button class="like-btn' . $liked_class . '" data-image-id="' . $autos_id . '" data-tabla-origen="autos">';
                    // SVG del corazón vacío - RUTA CORREGIDA
                    echo '<svg class="heart-icon heart-empty-icon" width="30" height="30" fill="currentColor" viewBox="0 0 16 16" style="display: ' . $display_empty . ';">';
                    echo '<path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"/>';
                    echo '</svg>';
                    // SVG del corazón lleno
                    echo '<svg class="heart-icon heart-filled-icon" width="30" height="30" fill="#e74c3c" viewBox="0 0 16 16" style="display: ' . $display_filled . ';">';
                    echo '<path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314"/>';
                    echo '</svg>';
                    echo '</button>';
                    echo '<span class="like-count">' . $likes_count . '</span>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "<p>No hay imágenes disponibles.</p>";
            }
        } else {
            echo "<p>Error en la consulta de la base de datos: " . $conn->error . "</p>";
        }
        ?>
    </div>
</section>


<section id="eventos" class="gallery">
    <h3>Evento de Motocross</h3>
    <div class="galeria">
        <?php
        $sql_motocross = "SELECT * FROM fotos_eventos WHERE categoria = 'eventos_motocross'";
        $result_motocross = $conn->query($sql_motocross);

        if ($result_motocross) {
            if ($result_motocross->num_rows > 0) {
                while ($fila = $result_motocross->fetch_assoc()) {
                    $eventos_id = htmlspecialchars($fila["eventos_id"]);
                    $ruta = htmlspecialchars($fila['ruta']);
                    $nombre = htmlspecialchars($fila['nombre']);

                    // Contar los likes para esta imagen
                    $sql_count = "SELECT COUNT(*) FROM likes WHERE item_id = ? AND tabla_origen = 'eventos'";
                    $stmt_count = $conn->prepare($sql_count);
                    $stmt_count->bind_param("i", $eventos_id);
                    $stmt_count->execute();
                    $result_count = $stmt_count->get_result();
                    $likes_count = (int)$result_count->fetch_row()[0];
                    $stmt_count->close();

                    // Verificar si el usuario actual ya dio like (si hay sesión de usuario)
                    $liked_class = '';
                    $display_empty = 'block';
                    $display_filled = 'none';
                    if (isset($_SESSION['usuario_id'])) {
                        $sql_check_liked = "SELECT id_like FROM likes WHERE item_id = ? AND tabla_origen = 'eventos' AND usuario_id = ?";
                        $stmt_check_liked = $conn->prepare($sql_check_liked);
                        $stmt_check_liked->bind_param("ii", $eventos_id, $_SESSION['usuario_id']);
                        $stmt_check_liked->execute();
                        $result_check_liked = $stmt_check_liked->get_result();
                        if ($result_check_liked->num_rows > 0) {
                            $liked_class = ' liked';
                            $display_empty = 'none';
                            $display_filled = 'block';
                        }
                        $stmt_check_liked->close();
                    }

                    echo '<div class="item">';
                    echo '<img src="' . $ruta . '" alt="' . $nombre . '" data-description="' . $nombre . '">';
                    echo '<p>' . $nombre . '</p>';
                    echo '<div class="like-container">';
                    echo '<button class="like-btn' . $liked_class . '" data-image-id="' . $eventos_id . '" data-tabla-origen="eventos">';
                    // SVG del corazón vacío - RUTA CORREGIDA
                    echo '<svg class="heart-icon heart-empty-icon" width="30" height="30" fill="currentColor" viewBox="0 0 16 16" style="display: ' . $display_empty . ';">';
                    echo '<path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"/>';
                    echo '</svg>';
                    // SVG del corazón lleno
                    echo '<svg class="heart-icon heart-filled-icon" width="30" height="30" fill="#e74c3c" viewBox="0 0 16 16" style="display: ' . $display_filled . ';">';
                    echo '<path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314"/>';
                    echo '</svg>';
                    echo '</button>';
                    echo '<span class="like-count">' . $likes_count . '</span>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "<p>No hay imágenes disponibles.</p>";
            }
        } else {
            echo "<p>Error en la consulta de la base de datos: " . $conn->error . "</p>";
        }
        ?>
    </div>
</section>


<section id="viajes" class="gallery">
    <h3>Fotografías de Viajes</h3>
    <div class="galeria">
        <?php
        $sql_viajes = "SELECT * FROM fotos_viajes";
        $result_viajes = $conn->query($sql_viajes);

        if ($result_viajes) {
            if ($result_viajes->num_rows > 0) {
                while ($fila = $result_viajes->fetch_assoc()) {
                    $viajes_id = htmlspecialchars($fila["viajes_id"]);
                    $ruta = htmlspecialchars($fila['ruta']);
                    $descripcion = htmlspecialchars($fila['descripcion']);
                    // Asegúrate de que 'nombre' también se extraiga si es necesario para el alt o la descripción
                    $nombre_viajes = isset($fila['nombre']) ? htmlspecialchars($fila['nombre']) : $descripcion; // Usar descripción si no hay nombre

                    // Contar los likes para esta imagen
                    $sql_count = "SELECT COUNT(*) FROM likes WHERE item_id = ? AND tabla_origen = 'viajes'";
                    $stmt_count = $conn->prepare($sql_count);
                    $stmt_count->bind_param("i", $viajes_id);
                    $stmt_count->execute();
                    $result_count = $stmt_count->get_result();
                    $likes_count = (int)$result_count->fetch_row()[0];
                    $stmt_count->close();

                    // Verificar si el usuario actual ya dio like (si hay sesión de usuario)
                    $liked_class = '';
                    $display_empty = 'block';
                    $display_filled = 'none';
                    if (isset($_SESSION['usuario_id'])) {
                        $sql_check_liked = "SELECT id_like FROM likes WHERE item_id = ? AND tabla_origen = 'viajes' AND usuario_id = ?";
                        $stmt_check_liked = $conn->prepare($sql_check_liked);
                        $stmt_check_liked->bind_param("ii", $viajes_id, $_SESSION['usuario_id']);
                        $stmt_check_liked->execute();
                        $result_check_liked = $stmt_check_liked->get_result();
                        if ($result_check_liked->num_rows > 0) {
                            $liked_class = ' liked';
                            $display_empty = 'none';
                            $display_filled = 'block';
                        }
                        $stmt_check_liked->close();
                    }

                    echo '<div class="item">';
                    echo '<img src="' . $ruta . '" alt="' . $nombre_viajes . '" data-description="' . $descripcion . '">';
                    echo '<p class="descripcion">' . $descripcion . '</p>';
                    echo '<div class="like-container">';
                    echo '<button class="like-btn' . $liked_class . '" data-image-id="' . $viajes_id . '" data-tabla-origen="viajes">';
                    // SVG del corazón vacío - RUTA CORREGIDA
                    echo '<svg class="heart-icon heart-empty-icon" width="30" height="30" fill="currentColor" viewBox="0 0 16 16" style="display: ' . $display_empty . ';">';
                    echo '<path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"/>';
                    echo '</svg>';
                    // SVG del corazón lleno
                    echo '<svg class="heart-icon heart-filled-icon" width="30" height="30" fill="#e74c3c" viewBox="0 0 16 16" style="display: ' . $display_filled . ';">';
                    echo '<path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314"/>';
                    echo '</svg>';
                    echo '</button>';
                    echo '<span class="like-count">' . $likes_count . '</span>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "<p>No hay imágenes disponibles.</p>";
            }
        } else {
            echo "<p>Error en la consulta de la base de datos: " . $conn->error . "</p>";
        }
        ?>
    </div>
</section>


    <div id="lightbox" class="lightbox">
        <span class="close-fullscreen" onclick="closeLightbox()">&times;</span>
        <div class="lightbox-prev" onclick="changeLightboxImage(-1)">&#10094;</div> <img id="lightbox-img" class="lightbox-img" src="" alt="Imagen Lightbox">
        <div class="lightbox-next" onclick="changeLightboxImage(1)">&#10095;</div> <p id="lightbox-description"></p>
    </div>

    <script src="scripts.js"></script>

<section id="servicios">
    <div class="container mt-5">
        <h2 class="text-center mb-4">Sesiones de Fotos</h2>
        <div class="plan-container">
            <div class="plan-card">
                <div class="plan-header bg-primary text-white text-center">
                    <h3>Plan Básico</h3>
                </div>
                <div class="plan-body text-center">
                    <h1 class="plan-title">$100</h1>
                    <ul class="plan-list">
                        <li>• Sesión de 1 hora</li>
                        <li>• 20 fotos editadas</li>
                        <li>• Entrega digital en alta resolución</li>
                        <li>• 1 impresión en tamaño 10x15 a elección</li>
                    </ul>
                    <form action="procesar_reserva.php" method="POST">
                        <input type="hidden" name="plan" value="basico">
                        <div>
                            <label for="nombre_basico">Nombre:</label>
                            <input type="text" id="nombre_basico" name="nombre" required>
                        </div>
                        <div>
                            <label for="email_basico">Correo Electrónico:</label>
                            <input type="email" id="email_basico" name="email" required>
                        </div>
                        <div>
                            <label for="fecha_basico">Fecha Deseada:</label>
                            <input type="date" id="fecha_basico" name="fecha_deseada" required>
                        </div>
                        <button type="submit" class="btn btn-outline-primary mt-3">Reservar</button>
                    </form>
                </div>
            </div>
            <div class="plan-card">
                <div class="plan-header bg-success text-white text-center">
                    <h3>Plan Intermedio</h3>
                </div>
                <div class="plan-body text-center">
                    <h1 class="plan-title">$250</h1>
                    <ul class="plan-list">
                        <li>• Sesión de 2 horas</li>
                        <li>• 40 fotos editadas</li>
                        <li>• Entrega digital en alta resolución</li>
                        <li>• Asesoramiento de vestuario básico</li>
                        <li>• Posibilidad de un cambio de locación dentro de la ciudad</li>
                        
                    </ul>
                    <form action="procesar_reserva.php" method="POST">
                        <input type="hidden" name="plan" value="intermedio">
                        <div>
                            <label for="nombre_intermedio">Nombre:</label>
                            <input type="text" id="nombre_intermedio" name="nombre" required>
                        </div>
                        <div>
                            <label for="email_intermedio">Correo Electrónico:</label>
                            <input type="email" id="email_intermedio" name="email" required>
                        </div>
                        <div>
                            <label for="fecha_intermedio">Fecha Deseada:</label>
                            <input type="date" id="fecha_intermedio" name="fecha_deseada" required>
                        </div>
                        <button type="submit" class="btn btn-outline-success mt-3">Reservar</button>
                    </form>
                </div>
            </div>
            <div class="plan-card">
                <div class="plan-header bg-warning text-white text-center">
                    <h3>Plan Premium</h3>
                </div>
                <div class="plan-body text-center">
                    <h1 class="plan-title">$400</h1>
                    <ul class="plan-list">
                        <li>• Sesión de 3 horas</li>
                        <li>• 80 fotos editadas</li>
                        <li>• Entrega digital en alta resolución</li>
                        <li>• Video corto con momentos destacados de la sesión</li>
                        <li>• Asesoramiento de vestuario y estilismo</li>
                        <li>• Posibilidad de hasta dos locaciones dentro de la ciudad</li>
                    </ul>
                    <form action="procesar_reserva.php" method="POST">
                        <input type="hidden" name="plan" value="premium">
                        <div>
                            <label for="nombre_premium">Nombre:</label>
                            <input type="text" id="nombre_premium" name="nombre" required>
                        </div>
                        <div>
                            <label for="email_premium">Correo Electrónico:</label>
                            <input type="email" id="email_premium" name="email" required>
                        </div>
                        <div>
                            <label for="fecha_premium">Fecha Deseada:</label>
                            <input type="date" id="fecha_premium" name="fecha_deseada" required>
                        </div>
                        <button type="submit" class="btn btn-outline-warning mt-3">Reservar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<footer>
  <p>¿No encuentras lo que buscas? <a href="#contacto">¡Contáctame para una sesión completamente personalizada!</a></p>
</footer>


    <section id="blog" class="blog-section cards-layout">
        <div class="container">
            <h2>Blog</h2>
            <p class="blog-intro">¡Bienvenido a nuestro espacio dedicado a la pasión por la fotografía! Aquí podrás
                compartir, aprender y conectar con otros entusiastas.</p>

            <div class="blog-grid">
                <article class="blog-card consejos">
                    <a href="blog.php?categoria=consejos">
                        <?php
                        if (isset($imagenes_blog['consejos']) && !empty($imagenes_blog['consejos'])) {
                            echo '<img src="' . $imagenes_blog['consejos'] . '" alt="Consejos de Fotografía">';
                        } else {
                            echo '<img src="images/consejos-placeholder.jpg" alt="Consejos de Fotografía">';
                        }
                        ?>
                        <h3>Consejos</h3>
                        <p class="card-description">Aquí puedes recibir consejos sobre cómo mejorar en la fotografía.
                            ¡Comparte tus trucos y aprende de la comunidad!</p>
                        <span class="read-more">Ver Consejos</span>
                    </a>
                </article>

                <article class="blog-card experiencias">
                    <a href="blog.php?categoria=experiencias">
                        <?php
                        if (isset($imagenes_blog['experiencias']) && !empty($imagenes_blog['experiencias'])) {
                            echo '<img src="' . $imagenes_blog['experiencias'] . '" alt="Experiencias y Viajes">';
                        } else {
                            echo '<img src="images/experiencias-placeholder.jpg" alt="Experiencias y Viajes">';
                        }
                        ?>
                        <h3>Experiencias y Viajes</h3>
                        <p class="card-description">Aquí puedes contar historias y experiencias de viajes y aventuras
                            que hayas tenido. ¡Inspira a otros con tus relatos fotográficos!</p>
                        <span class="read-more">Ver Experiencias</span>
                    </a>
                </article>

                <article class="blog-card comentarios">
                <a href="blog.php?categoria=comentarios">
                    
                        <?php
                        if (isset($imagenes_blog['comentarios']) && !empty($imagenes_blog['comentarios'])) {
                            echo '<img src="' . $imagenes_blog['comentarios'] . '" alt="Comentarios y Sugerencias">';
                        } else {
                            echo '<img src="images/comentarios-placeholder.jpg" alt="Comentarios y Sugerencias">';
                        }
                        ?>
                        <h3>Comentarios y Sugerencias</h3>
                        <p class="card-description">Aquí puedes comentar y opinar sobre el sitio web. ¡Tu feedback es
                            valioso para seguir mejorando!</p>
                        <span class="read-more">Ver Comentarios</span>
                    </a>
                </article>
            </div>

        </div>
    </section>

    <section id="contacto" class="contacto">
    <div class="contenido-seccion">
        <h2>Contacto</h2>
        <p>¿Tienes alguna pregunta o te gustaría reservar una sesión de fotos personalizada? ¡No dudes en ponerte en contacto conmigo!</p>
        <div class="formulario-contacto">
            <form action="send_email.php" method="POST">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="mensaje">Mensaje:</label>
                    <textarea id="mensaje" name="mensaje" rows="5" required></textarea>
                </div>
                <button type="submit" class="button">Enviar Mensaje</button>
            </form>
        </div>
        <div class="info-contacto">
            <h3>Información de Contacto</h3>
            <p><i class="bi bi-envelope"></i> Email: al5261486@gmail.com</p>
            <p><i class="bi bi-instagram"></i> Instagram: <a href="https://www.instagram.com/aguslopez_fotografia/" target="_blank" rel="noopener noreferrer">@aguslopez_fotografia</a></p>
            </div>
    </div>
</section>

</body>

</html>