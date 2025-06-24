<?php
    // Inicia el buffer de salida con compresión GZIP para optimizar la transferencia
    ob_start("ob_gzhandler");
    // Inicia la sesión para manejar variables de sesión
    session_start();

    // Generar el token CSRF si no existe (protección contra ataques CSRF)
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Token seguro de 32 bytes
    }

    // Incluye el archivo de conexión a la base de datos
    include "conexion.php";

    // Manejo de errores para la conexión a la base de datos
    if (!$conn) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Cargar imagen de inicio solo una vez (imagen por defecto si no hay en BD)
    $imagen_inicio = 'images/default-hero.jpg';
    $sql_inicio = "SELECT ruta FROM inicio LIMIT 1";
    if ($result_inicio = $conn->query($sql_inicio)) {
        if ($row_inicio = $result_inicio->fetch_assoc()) {
            // Sanitiza la ruta de la imagen y construye la ruta completa
            $imagen_inicio = 'imagenes/' . htmlspecialchars($row_inicio["ruta"]);
        }
    }

    // Cargar imágenes del blog en un solo array asociativo por categoría
    $imagenes_blog = [];
    $sql_blog_images = "SELECT ruta, categoria FROM blog";
    if ($result_blog_images = $conn->query($sql_blog_images)) {
        while ($row_blog = $result_blog_images->fetch_assoc()) {
            // Corrige las rutas con barras invertidas
            $ruta_corregida = str_replace('\\', '/', $row_blog["ruta"]);
            // Construye la ruta final, verificando si ya incluye 'imagenes/'
            $ruta_final = (strpos(strtolower($ruta_corregida), 'imagenes/') === 0)
                ? htmlspecialchars($ruta_corregida)
                : 'imagenes/' . htmlspecialchars($ruta_corregida);
            // Almacena en el array usando la categoría como clave
            $imagenes_blog[strtolower($row_blog["categoria"])] = $ruta_final;
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Metadatos básicos del sitio -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portafolio de Agustina Lopez, fotógrafa aficionada. Descubre sus mejores trabajos.">
    <meta name="keywords" content="fotografía, portfolio, Agustina Lopez, fotografía aficionada">
    <title>Portafolio de Agustina Lopez</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Barra de navegación principal -->

    <nav class="main-nav">
        <ul>
            <li><a href="#inicio">INICIO</a></li>
            <li><a href="#sobremi">SOBRE MÍ</a></li>
            <li><a href="#portfolio">GALERIA</a></li>
            <li><a href="#servicios">SERVICIOS</a></li>
            <li><a href="#blog">BLOG</a></li>
            <li><a href="#contacto">CONTACTO</a></li>
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <!-- Opción de cerrar sesión si el usuario está logueado -->
                <li class="logout-nav">
                    <a href="logout.php">
                        <span class="login-icon" style="vertical-align: middle;">
                            <!-- Icono SVG de puerta abierta -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-door-open-fill" viewBox="0 0 16 16">
                                <path d="M1.5 15a.5.5 0 0 0 0 1h13a.5.5 0 0 0 0-1H13V2.5A1.5 1.5 0 0 0 11.5 1H11V.5a.5.5 0 0 0-.57-.495l-7 1A.5.5 0 0 0 3 1.5V15zM11 2h.5a.5.5 0 0 1 .5.5V15h-1zm-2.5 8c-.276 0-.5-.448-.5-1s.22.5.5-.5z"/>
                            </svg>
                        </span>
                        Cerrar sesión
                    </a>
                </li>
            <?php else: ?>
                <!-- Opción de iniciar sesión si el usuario no está logueado -->
                <li class="login-nav">
                    <a href="login.php">
                        <span class="login-icon" style="vertical-align: middle;">
                            <!-- Icono SVG de puerta cerrada -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-door-closed-fill" viewBox="0 0 16 16">
                                <path d="M12 1a1 1 0 0 1 1 1v13h1.5a.5.5 0 0 1 0 1h-13a.5.5 0 0 1 0-1H3V2a1 1 0 0 1 1-1zm-2 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
                            </svg>
                        </span>
                        Iniciar sesión
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Sección Hero/Inicio -->

    <section id="inicio" class="hero">
        <div class="hero-image">
            <!-- Imagen principal, cargada desde la base de datos o por defecto -->
            <img src="<?php echo $imagen_inicio; ?>" alt="Imagen principal de bienvenida" width="1200" height="600" fetchpriority="high"
                style="object-fit: cover; width: 100%; height: 100%; display: block;"
            >
        </div>
        <div class="hero-content">
            <h1>Bienvenidos a mi portafolio</h1>
            <a href="#portfolio" class="button">Ver galería</a>
        </div>
    </section>

    <!-- Sección Sobre Mí -->

    <section id="sobremi" class="sobremi intro-section">
        <div class="intro-seccion">
            <p class="titulo-principal">¡Hola! Soy Agustina Lopez, fotógrafa aficionada y creadora visual.</p>
            <p class="mensaje-bienvenida">Explora mi mundo a través de mis imágenes y sígueme en Instagram para ver más.</p>
            <div class="redes-sociales">
                <!-- Enlace a Instagram con icono SVG -->
                <a href="https://www.instagram.com/aguslopez_fotografia/"
                   target="_blank"
                   rel="noopener noreferrer"
                   aria-label="Instagram de Agustina Lopez">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor"
                         class="bi bi-instagram" viewBox="0 0 16 16" aria-hidden="true" focusable="false">
                        <title>Instagram</title>
                        <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.853.174 1.434.372 1.943.198.51.493.975.923 1.417a3.9 3.9 0 0 0 1.417.923c.509.197 1.09.332 1.943.372.853.039 1.125.048 3.297.048 2.172 0 2.444-.009 3.297-.048.853-.04 1.434-.175 1.943-.372a3.9 3.9 0 0 0 1.417-.923 3.9 3.9 0 0 0 .923-1.417c.197-.509.332-1.09.372-1.943.039-.853.048-1.125.048-3.297 0-2.174-.009-2.446-.048-3.299-.04-.853-.175-1.434-.372-1.943a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.509-.198-1.09-.332-1.943-.372C10.444.01 10.172 0 8 0zm0 1.438c2.135 0 2.389.008 3.229.047.78.036 1.203.166 1.486.277.374.146.641.324.921.604.28.28.458.547.604.921.111.283.241.706.277 1.486.039.84.047 1.094.047 3.229s-.008 2.389-.047 3.229c-.036.78-.166 1.203-.277 1.486a2.44 2.44 0 0 1-.604.921 2.44 2.44 0 0 1-.921.604c-.283.111-.706.241-1.486.277-.84.039-1.094.047-3.229.047s-2.389-.008-3.229-.047c-.78-.036-1.203-.166-1.486-.277a2.44 2.44 0 0 1-.921-.604 2.44 2.44 0 0 1-.604-.921c-.111-.283-.241-.706-.277-1.486C1.446 10.389 1.438 10.135 1.438 8s.008-2.389.047-3.229c.036-.78.166-1.203.277-1.486.146-.374.324-.641.604-.921.28-.28.547-.458.921-.604.283-.111.706-.241 1.486-.277C5.611 1.446 5.865 1.438 8 1.438zM8 3.938A4.062 4.062 0 1 0 8 12.062 4.062 4.062 0 0 0 8 3.938zm0 1.438a2.625 2.625 0 1 1 0 5.25 2.625 2.625 0 0 1 0-5.25zm5.406-1.25a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                    </svg>
                    <span class="sr-only">Instagram de Agustina Lopez</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Modal para requerir inicio de sesión -->

    <div id="modal-login-required" class="modal-login-required">
        <div class="modal-box">
            <button id="btn-close-modal" aria-label="Cerrar">&times;</button>
            <h3>¡Atención!</h3>
            <p>Debes iniciar sesión o registrarte para dar like.</p>
            <div style="margin-top:1.5em;display:flex;gap:15px;justify-content:center;">
                <a href="login.php" class="button">Iniciar sesión</a>
                <a href="registro.php" class="button registro">Registrarse</a>
            </div>
        </div>
    </div>

    <!-- Sección Portfolio/Galería -->

    <section id="portfolio" class="portfolio">
        <h2 class="sr-only">Galería</h2>
        <nav>
            <ul>
                <li><a href="#paisajes">Paisajes</a></li>
                <li><a href="#autos">Autos</a></li>
                <li><a href="#eventos">Eventos</a></li>
                <li><a href="#viajes">Viajes</a></li>
            </ul>
        </nav>
    </section>

<?php

// Función para renderizar una galería de imágenes
function render_galeria($origen, $titulo, $id_col, $tabla, $likes_all, $user_likes, $conn, $categoria = null) {
    echo '<section id="'.$origen.'" class="gallery">';
    echo '<h3>'.$titulo.'</h3>';
    echo '<div class="galeria">';

    // Filtro por categoría para eventos
    $filtro_categoria = '';
    if (strpos($origen, 'eventos-') === 0 && $categoria !== null) {
        $categoria_esc = $conn->real_escape_string($categoria);
        $filtro_categoria = " WHERE categoria = '$categoria_esc' ";
    }

    // Consulta para obtener las imágenes de la galería
    $sql = "SELECT * FROM $tabla $filtro_categoria";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $item_id = htmlspecialchars($row[$id_col]);
            $ruta = htmlspecialchars($row['ruta']);
            $nombre = htmlspecialchars($row['nombre']);
            $descripcion = isset($row['descripcion']) ? htmlspecialchars($row['descripcion']) : '';
            
            // Obtener conteo de likes y estado de like del usuario
            $likes_count = isset($likes_all[$origen][$item_id]) ? $likes_all[$origen][$item_id] : 0;
            $liked_class = (isset($user_likes[$origen][$item_id])) ? ' liked' : '';
            $display_empty = ($liked_class) ? 'none' : 'block';
            $display_filled = ($liked_class) ? 'block' : 'none';
            $texto_like = ($liked_class) ? 'Quitar me gusta de ' . $nombre : 'Dar me gusta a ' . $nombre;

            echo '<div class="item">';

            // Obtener dimensiones de la imagen para atributos width/height
            $img_path_fs = $ruta;
            if (!file_exists($img_path_fs) && file_exists(__DIR__ . '/' . $img_path_fs)) {
                $img_path_fs = __DIR__ . '/' . $img_path_fs;
            }
            $dimensiones = @getimagesize($img_path_fs);
            $ancho = $dimensiones ? $dimensiones[0] : 800;
            $alto  = $dimensiones ? $dimensiones[1] : 600;

            // Mostrar la imagen
            echo '<img src="' . $ruta . '" alt="' . $nombre . '" data-description="' . $descripcion . '" loading="lazy" width="' . $ancho . '" height="' . $alto . '">';

            // Mostrar nombre solo para autos
            if ($origen === 'autos' && !empty($nombre)) {
                echo '<p class="nombre">' . $nombre . '</p>';
            }

            // Mostrar descripción solo para paisajes y viajes
            if (in_array($origen, ['paisajes', 'viajes']) && !empty($descripcion)) {
                echo '<p class="descripcion">' . $descripcion . '</p>';
            }

            // Botón de like con iconos SVG
            echo '<div class="like-container">';
            echo '<button class="like-btn' . $liked_class . '" data-image-id="' . $item_id . '" data-tabla-origen="' . $origen . '" aria-label="' . $texto_like . '" title="' . $texto_like . '">';
            echo '<span class="sr-only">' . $texto_like . '</span>';
            echo '<svg class="heart-icon heart-empty-icon" width="30" height="30" viewBox="0 0 16 16" style="display: ' . $display_empty . ';">';
            echo '<path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"/>';
            echo '</svg>';
            echo '<svg class="heart-icon heart-filled-icon" width="30" height="30" fill="#e74c3c" viewBox="0 0 16 16" style="display: ' . $display_filled . ';">';
            echo '<path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314"/>';
            echo '</svg>';
            echo '</button>';
            echo '<span class="like-count">' . $likes_count . '</span>';
            echo '</div>'; // .like-container

            echo '</div>'; // .item
        }
    } else {
        echo "<p>No hay imágenes disponibles.</p>";
    }

    echo '</div></section>';
}

// Consulta para obtener todos los likes de todas las imágenes
$likes_all = [];
$user_likes = [];
$usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;

// Definición de tablas y sus estructuras
$tablas = [
    'paisajes' => ['id' => 'paisajes_id', 'tabla' => 'fotos_paisajes'],
    'autos' => ['id' => 'autos_id', 'tabla' => 'fotos_autos'],
    'eventos-motocross' => ['id' => 'eventos_id', 'tabla' => 'fotos_eventos'],
    'eventos-musicales' => ['id' => 'eventos_id', 'tabla' => 'fotos_eventos'],
    'viajes' => ['id' => 'viajes_id', 'tabla' => 'fotos_viajes']
];

// Obtener conteo de likes por imagen
foreach ($tablas as $origen => $info) {
    $sql_likes = "SELECT item_id, COUNT(*) as likes FROM likes WHERE tabla_origen = '$origen' GROUP BY item_id";
    $result_likes = $conn->query($sql_likes);
    if ($result_likes) {
        while ($row = $result_likes->fetch_assoc()) {
            $likes_all[$origen][$row['item_id']] = (int)$row['likes'];
        }
    }

    // Obtener likes del usuario actual si está logueado
    if ($usuario_id) {
        $sql_user_likes = "SELECT item_id FROM likes WHERE tabla_origen = '$origen' AND usuario_id = $usuario_id";
        $result_user_likes = $conn->query($sql_user_likes);
        if ($result_user_likes) {
            while ($row = $result_user_likes->fetch_assoc()) {
                $user_likes[$origen][$row['item_id']] = true;
            }
        }
    }
}

// Renderizar las galerías
render_galeria('paisajes', 'Fotografías de Paisajes', 'paisajes_id', 'fotos_paisajes', $likes_all, $user_likes, $conn);
render_galeria('autos', 'Fotografías de Autos', 'autos_id', 'fotos_autos', $likes_all, $user_likes, $conn);

// Galerías de eventos agrupadas en una sección
echo '<section id="eventos">';
render_galeria('eventos-motocross', 'Eventos de Motocross', 'eventos_id', 'fotos_eventos', $likes_all, $user_likes, $conn, 'motocross');
render_galeria('eventos-musicales', 'Eventos Musicales', 'eventos_id', 'fotos_eventos', $likes_all, $user_likes, $conn, 'musicales');
echo '</section>';

// Galería de viajes
render_galeria('viajes', 'Fotografías de Viajes', 'viajes_id', 'fotos_viajes', $likes_all, $user_likes, $conn);
?>

<!-- Lightbox para visualización de imágenes en pantalla completa -->
<div id="lightbox" class="lightbox">
    <span class="close-fullscreen" onclick="closeLightbox()">&times;</span>
    <div class="lightbox-prev" onclick="changeLightboxImage(-1)">&#10094;</div>
    <img id="lightbox-img" class="lightbox-img" src="" alt="Imagen Lightbox">
    <div class="lightbox-next" onclick="changeLightboxImage(1)">&#10095;</div>
    <p id="lightbox-description"></p>
</div>

<!-- Sección de Servicios/Planes -->

<section id="servicios">
  <div class="planes-section">
    <h2 class="planes-titulo">Sesiones de Fotos</h2>
    <div class="planes-descripcion">
      Elige tu plan y reservá tu sesión. Todos incluyen edición profesional y entrega digital de alta calidad.
    </div>
    <div class="plan-container">
      <!-- Plan Básico -->
      <div class="plan-card bento-plan basico">
        <div class="plan-body">
          <div class="plan-contenido">
            <div class="plan-header">Plan Básico</div>
            <div class="plan-precio">$100</div>
            <ul class="plan-list">
              <li>• Sesión de 1 hora</li>
              <li>• 20 fotos editadas</li>
              <li>• Entrega digital en alta resolución</li>
              <li>• 1 impresión en tamaño 10x15 a elección</li>
            </ul>
          </div>
          <form action="procesar_reserva.php" method="POST" class="plan-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="plan" value="basico">
            <input type="text" name="nombre" id="nombre_basico" placeholder="Nombre" required>
            <input type="email" name="email" id="email_basico" placeholder="Correo electrónico" required>
            <label for="fecha_deseada_basico">Elegí una fecha</label>
            <input type="date" name="fecha_deseada" id="fecha_deseada_basico" required>
            <button type="submit" class="plan-btn">Reservar</button>
          </form>
        </div>
      </div>

      <!-- Plan Intermedio -->
      <div class="plan-card bento-plan intermedio">
        <div class="plan-body">
          <div class="plan-contenido">
            <div class="plan-header">Plan Intermedio</div>
            <div class="plan-precio">$250</div>
            <ul class="plan-list">
              <li>• Sesión de 2 horas</li>
              <li>• 40 fotos editadas</li>
              <li>• Entrega digital en alta resolución</li>
              <li>• Asesoramiento de vestuario básico</li>
              <li>• Cambio de locación dentro de la ciudad</li>
            </ul>
          </div>
          <form action="procesar_reserva.php" method="POST" class="plan-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="plan" value="intermedio">
            <input type="text" name="nombre" id="nombre_intermedio" placeholder="Nombre" required>
            <input type="email" name="email" id="email_intermedio" placeholder="Correo electrónico" required>
            <label for="fecha_deseada_intermedio">Elegí una fecha</label>
            <input type="date" name="fecha_deseada" id="fecha_deseada_intermedio" required>
            <button type="submit" class="plan-btn">Reservar</button>
          </form>
        </div>
      </div>

      <!-- Plan Premium -->
      <div class="plan-card bento-plan premium">
        <div class="plan-body">
          <div class="plan-contenido">
            <div class="plan-header">Plan Premium</div>
            <div class="plan-precio">$400</div>
            <ul class="plan-list">
              <li>• Sesión de 3 horas</li>
              <li>• 80 fotos editadas</li>
              <li>• Entrega digital en alta resolución</li>
              <li>• Video corto con momentos destacados</li>
              <li>• Asesoramiento de vestuario y estilismo</li>
              <li>• Hasta dos locaciones dentro de la ciudad</li>
            </ul>
          </div>
          <form action="procesar_reserva.php" method="POST" class="plan-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="plan" value="premium">
            <input type="text" name="nombre" id="nombre_premium" placeholder="Nombre" required>
            <input type="email" name="email" id="email_premium" placeholder="Correo electrónico" required>
            <label for="fecha_deseada_premium">Elegí una fecha</label>
            <input type="date" name="fecha_deseada" id="fecha_deseada_premium" required>
            <button type="submit" class="plan-btn">Reservar</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Pie de página -->
<footer>
    <p>¿No encuentras lo que buscas? <a href="#contacto">¡Contáctame para una sesión completamente personalizada!</a></p>
</footer>

<!-- Sección Blog con diseño Bento Grid -->

<section id="blog" class="blog-section bento-section">
  <div class="container">
    <h2>Blog</h2>
    <p class="blog-intro">¡Bienvenido a nuestro espacio dedicado a la pasión por la fotografía! Aquí podrás compartir, aprender y conectar con otros entusiastas.</p>
    <div class="bento-grid">

      <!-- Tarjeta de Consejos -->
      <article class="bento-card consejos">
        <a href="blog.php?categoria=consejos">
          <?php
            // Manejo de imágenes para la categoría consejos
            $imgBase = isset($imagenes_blog['consejos']) && !empty($imagenes_blog['consejos'])
              ? $imagenes_blog['consejos']
              : 'images/consejos-placeholder.webp';
            $img400 = str_replace('.webp', '-400.webp', $imgBase);
            $img800 = $imgBase;
          ?>
          <img 
            src="<?php echo $img800; ?>"
            srcset="<?php echo $img400; ?> 400w, <?php echo $img800; ?> 800w"
            sizes="(max-width: 600px) 400px, 800px"
            alt="Consejos de Fotografía"
            width="800" height="533"
            loading="lazy"
          >
          <div class="bento-content">
            <h3>Consejos</h3>
            <p class="card-description">Aquí puedes recibir consejos sobre cómo mejorar en la fotografía. ¡Comparte tus trucos y aprende de la comunidad!</p>
            <span class="read-more">Ver Consejos</span>
          </div>
        </a>
      </article>

      <!-- Tarjeta de Experiencias -->
      <article class="bento-card experiencias">
        <a href="blog.php?categoria=experiencias">
          <?php
            // Manejo de imágenes para la categoría experiencias
            $imgBase = isset($imagenes_blog['experiencias']) && !empty($imagenes_blog['experiencias'])
              ? $imagenes_blog['experiencias']
              : 'images/experiencias-placeholder.webp';
            $img400 = str_replace('.webp', '-400.webp', $imgBase);
            $img800 = $imgBase;
          ?>
          <img 
            src="<?php echo $img800; ?>"
            srcset="<?php echo $img400; ?> 400w, <?php echo $img800; ?> 800w"
            sizes="(max-width: 600px) 400px, 800px"
            alt="Experiencias y Viajes"
            width="800" height="533"
            loading="lazy"
          >
          <div class="bento-content">
            <h3>Experiencias y Viajes</h3>
            <p class="card-description">Aquí puedes contar historias y experiencias de viajes y aventuras que hayas tenido. ¡Inspira a otros con tus relatos fotográficos!</p>
            <span class="read-more">Ver Experiencias</span>
          </div>
        </a>
      </article>

      <!-- Tarjeta de Comentarios -->
      <article class="bento-card comentarios">
        <a href="blog.php?categoria=comentarios">
          <?php
            // Manejo de imágenes para la categoría comentarios
            $imgBase = isset($imagenes_blog['comentarios']) && !empty($imagenes_blog['comentarios'])
              ? $imagenes_blog['comentarios']
              : 'images/comentarios-placeholder.webp';
            $img400 = str_replace('.webp', '-400.webp', $imgBase);
            $img800 = $imgBase;
          ?>
          <img 
            src="<?php echo $img800; ?>"
            srcset="<?php echo $img400; ?> 400w, <?php echo $img800; ?> 800w"
            sizes="(max-width: 600px) 400px, 800px"
            alt="Comentarios y Sugerencias"
            width="800" height="533"
            loading="lazy"
          >
          <div class="bento-content">
            <h3>Comentarios y Sugerencias</h3>
            <p class="card-description">Aquí puedes comentar y opinar sobre el sitio web. ¡Tu feedback es valioso para seguir mejorando!</p>
            <span class="read-more">Ver Comentarios</span>
          </div>
        </a>
      </article>
    </div>
  </div>
</section>

<!-- Sección de Contacto con mapa de fondo -->

<section id="contacto" class="contact-bg">
  <iframe
    class="contact-map-bg"
    src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d10165.234435936516!2d-64.2960613883883!3d-36.63003485794948!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses-419!2sar!4v1749700089168"
    allowfullscreen=""
    loading="lazy"
    referrerpolicy="no-referrer-when-downgrade"
    title="Mapa de ubicación del estudio fotográfico"
  ></iframe>
  <div class="contact-modal">
    <h2>Ponete en contacto</h2>
    <form class="contact-form" action="send_email.php" method="POST" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
      <div class="row">
        <div class="form-group">
          <label for="name">Nombre</label>
          <input type="text" id="name" name="nombre" placeholder="Ingresá tu nombre" required>
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="Ingresá tu correo electrónico" required>
        </div>
      </div>
      <div class="form-group full-width">
        <label for="message">Mensaje</label>
        <textarea id="message" name="mensaje" placeholder="Tu mensaje aquí..." rows="4" required></textarea>
      </div>
      <div class="form-actions">
        <button type="submit" class="submit-btn">
          Enviar <span class="arrow">&rarr;</span>
        </button>
      </div>
    </form>
  </div>
</section>

<!-- Script para manejar el estado de login -->

<script>
  const isLoggedIn = <?php echo isset($_SESSION['usuario_id']) ? 'true' : 'false'; ?>;
</script>
<script src="scripts.js" defer></script>

</body>
</html>