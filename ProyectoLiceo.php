<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Portafolio de Agustina Lopez, fotógrafa aficionada. Descubre sus mejores trabajos.">
    <meta name="keywords" content="fotografía, portfolio, Agustina Lopez, fotografía aficionada">
    <title>Portafolio de Agustina Lopez</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>

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
                    <i class="bi bi-instagram"></i>
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
                    if (isset($_SESSION['usuario_id'])) {
                        $sql_check_liked = "SELECT id_like FROM likes WHERE item_id = ? AND tabla_origen = 'paisajes' AND usuario_id = ?";
                        $stmt_check_liked = $conn->prepare($sql_check_liked);
                        $stmt_check_liked->bind_param("ii", $paisajes_id, $_SESSION['usuario_id']);
                        $stmt_check_liked->execute();
                        $result_check_liked = $stmt_check_liked->get_result();
                        if ($result_check_liked->num_rows > 0) {
                            $liked_class = ' liked';
                        }
                        $stmt_check_liked->close();
                    }

                    echo '<div class="item">';
                    echo '<img src="' . $ruta . '" alt="' . $nombre . '" data-description="' . $descripcion . '">';
                    echo '<p>' . $descripcion . '</p>';
                    echo '<div class="like-container">';
                    echo '<button class="like-btn' . $liked_class . '" data-image-id="' . $paisajes_id . '" data-tabla-origen="paisajes"><i class="bi bi-heart' . ($liked_class ? '-fill' : '') . '"></i></button>';
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
                    if (isset($_SESSION['usuario_id'])) {
                        $sql_check_liked = "SELECT id_like FROM likes WHERE item_id = ? AND tabla_origen = 'autos' AND usuario_id = ?";
                        $stmt_check_liked = $conn->prepare($sql_check_liked);
                        $stmt_check_liked->bind_param("ii", $autos_id, $_SESSION['usuario_id']);
                        $stmt_check_liked->execute();
                        $result_check_liked = $stmt_check_liked->get_result();
                        if ($result_check_liked->num_rows > 0) {
                            $liked_class = ' liked';
                        }
                        $stmt_check_liked->close();
                    }

                    echo '<div class="item">';
                    echo '<img src="' . $ruta . '" alt="' . $nombre . '" data-description="' . $nombre . '">';
                    echo '<p>' . $nombre . '</p>';
                    echo '<div class="like-container">';
                    echo '<button class="like-btn' . $liked_class . '" data-image-id="' . $autos_id . '" data-tabla-origen="autos"><i class="bi bi-heart' . ($liked_class ? '-fill' : '') . '"></i></button>';
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
                    if (isset($_SESSION['usuario_id'])) {
                        $sql_check_liked = "SELECT id_like FROM likes WHERE item_id = ? AND tabla_origen = 'eventos' AND usuario_id = ?";
                        $stmt_check_liked = $conn->prepare($sql_check_liked);
                        $stmt_check_liked->bind_param("ii", $eventos_id, $_SESSION['usuario_id']);
                        $stmt_check_liked->execute();
                        $result_check_liked = $stmt_check_liked->get_result();
                        if ($result_check_liked->num_rows > 0) {
                            $liked_class = ' liked';
                        }
                        $stmt_check_liked->close();
                    }

                    echo '<div class="item">';
                    echo '<img src="' . $ruta . '" alt="' . $nombre . '" data-description="' . $nombre . '">';
                    echo '<p>' . $nombre . '</p>';
                    echo '<div class="like-container">';
                    echo '<button class="like-btn' . $liked_class . '" data-image-id="' . $eventos_id . '" data-tabla-origen="eventos"><i class="bi bi-heart' . ($liked_class ? '-fill' : '') . '"></i></button>';
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
                    if (isset($_SESSION['usuario_id'])) {
                        $sql_check_liked = "SELECT id_like FROM likes WHERE item_id = ? AND tabla_origen = 'viajes' AND usuario_id = ?";
                        $stmt_check_liked = $conn->prepare($sql_check_liked);
                        $stmt_check_liked->bind_param("ii", $viajes_id, $_SESSION['usuario_id']);
                        $stmt_check_liked->execute();
                        $result_check_liked = $stmt_check_liked->get_result();
                        if ($result_check_liked->num_rows > 0) {
                            $liked_class = ' liked';
                        }
                        $stmt_check_liked->close();
                    }

                    echo '<div class="item">';
                    echo '<img src="' . $ruta . '" alt="' . htmlspecialchars($fila['nombre']) . '" data-description="' . $descripcion . '">';
                    echo '<p class="descripcion">' . $descripcion . '</p>';
                    echo '<div class="like-container">';
                    echo '<button class="like-btn' . $liked_class . '" data-image-id="' . $viajes_id . '" data-tabla-origen="viajes"><i class="bi bi-heart' . ($liked_class ? '-fill' : '') . '"></i></button>';
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
            <h2>Nuestro Rincón de Fotografía</h2>
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