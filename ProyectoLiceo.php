<?php
	ob_start("ob_gzhandler");
	session_start();

	// Generar el token CSRF si no existe
	if (empty($_SESSION['csrf_token'])) {
		$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
	}

	include "conexion.php";

	// Manejo de errores para la conexión a la base de datos
	if (!$conn) {
		die("Conexión fallida: " . $conn->connect_error);
	}

	// Cargar imagen de inicio solo una vez
	$imagen_inicio = 'images/default-hero.jpg';
	$sql_inicio = "SELECT ruta FROM inicio LIMIT 1";
	if ($result_inicio = $conn->query($sql_inicio)) {
		if ($row_inicio = $result_inicio->fetch_assoc()) {
			$imagen_inicio = 'imagenes/' . htmlspecialchars($row_inicio["ruta"]);
		}
	}

	// Cargar imágenes del blog en un solo array
	$imagenes_blog = [];
	$sql_blog_images = "SELECT ruta, categoria FROM blog";
	if ($result_blog_images = $conn->query($sql_blog_images)) {
		while ($row_blog = $result_blog_images->fetch_assoc()) {
			$ruta_corregida = str_replace('\\', '/', $row_blog["ruta"]);
			$ruta_final = (strpos(strtolower($ruta_corregida), 'imagenes/') === 0)
				? htmlspecialchars($ruta_corregida)
				: 'imagenes/' . htmlspecialchars($ruta_corregida);
			$imagenes_blog[strtolower($row_blog["categoria"])] = $ruta_final;
		}
	}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Portafolio de Agustina Lopez, fotógrafa aficionada. Descubre sus mejores trabajos.">
	<meta name="keywords" content="fotografía, portfolio, Agustina Lopez, fotografía aficionada">
	<title>Portafolio de Agustina Lopez</title>
	<link rel="stylesheet" href="styles.css">
	<script>
		var usuarioLogueado = <?php echo isset($_SESSION['usuario_id']) ? 'true' : 'false'; ?>;
	</script>
	<script src="scripts.js" defer></script>

	<style>
		/* Global Styles (from styles.css) - Essential for initial layout */
		* { box-sizing: border-box; }
		body { margin: 0; font-family: Arial, sans-serif; }
		.container { max-width: 1200px; margin: 0 auto; padding: 0 15px; }
		img { max-width: 100%; height: auto; }

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
		.main-nav li { margin: 0 10px; }
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
		@media (max-width: 768px) {
			.main-nav ul {
				flex-direction: column;
				align-items: center;
			}
			.main-nav li { margin: 5px 0; }
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
		}
		.hero-image img {
			width: 100%;
			height: 100%;
			object-fit: cover;
			display: block;
		}
		.hero-content {
			padding: 20px;
			z-index: 10;
			position: relative;
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
		.hero-content .button:hover { background-color: #1f522b66; }
		@media (max-width: 768px) {
			.hero-content h1 { font-size: 2em; }
			.hero-content .button { font-size: 0.9em; padding: 10px 15px; }
		}

		/* SECCIÓN SOBRE MÍ  */
		#sobremi.intro-section {
			background: -webkit-linear-gradient(90deg, #274427, #64a34d);
			background: linear-gradient(90deg, #274427, #64a34d);
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
		.redes-sociales {
			display: flex;
			justify-content: center;
			gap: 10px;
			margin-top: 10px;
		}
		.redes-sociales a {
			color: #000;
			text-decoration: none;
			opacity: 0.7;
			transition: opacity 0.3s ease;
		}
		.redes-sociales a:hover { opacity: 1; }
		@media (max-width: 768px) {
			#sobremi.intro-section .titulo-principal { font-size: 1.5em; }
			#sobremi.intro-section .mensaje-bienvenida { max-width: 90%; }
			.redes-sociales a .bi-instagram { font-size: 20px; }
		}
		.sr-only {
			position: absolute !important;
			width: 1px;
			height: 1px;
			padding: 0;
			margin: -1px;
			overflow: hidden;
			clip: rect(0,0,0,0);
			border: 0;
		}
	</style>
</head>
<body>

	<nav class="main-nav">
		<ul>
			<li><a href="#inicio">INICIO</a></li>
			<li><a href="#sobremi">SOBRE MÍ</a></li>
			<li><a href="#portfolio">GALERIA</a></li>
			<li><a href="#servicios">SERVICIOS</a></li>
			<li><a href="#blog">BLOG</a></li>
			<li><a href="#contacto">CONTACTO</a></li>
			<?php if (isset($_SESSION['usuario_id'])): ?>
				<li class="logout-nav">
					<a href="logout.php">
						<span class="login-icon" style="vertical-align: middle;">
							<!-- SVG puerta ABIERTA (sesión iniciada) -->
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-door-open-fill" viewBox="0 0 16 16">
								<path d="M1.5 15a.5.5 0 0 0 0 1h13a.5.5 0 0 0 0-1H13V2.5A1.5 1.5 0 0 0 11.5 1H11V.5a.5.5 0 0 0-.57-.495l-7 1A.5.5 0 0 0 3 1.5V15zM11 2h.5a.5.5 0 0 1 .5.5V15h-1zm-2.5 8c-.276 0-.5-.448-.5-1s.224-1 .5-1 .5.448.5 1-.224 1-.5 1"/>
							</svg>
						</span>
						Cerrar sesión
					</a>
				</li>
			<?php else: ?>
				<li class="login-nav">
					<a href="login.php">
						<span class="login-icon" style="vertical-align: middle;">
							<!-- SVG puerta CERRADA (sesión no iniciada) -->
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

	<section id="inicio" class="hero">
		<div class="hero-image">
			<img 
				src="<?php echo $imagen_inicio; ?>"
				alt="Imagen principal de bienvenida"
				width="1200"
				height="600"
				fetchpriority="high"
				style="object-fit: cover; width: 100%; height: 100%; display: block;"
			>
		</div>
		<div class="hero-content">
			<h1>Bienvenidos a mi portafolio</h1>
			<a href="#portfolio" class="button">Ver galería</a>
		</div>
	</section>

	<section id="sobremi" class="sobremi intro-section">
		<div class="intro-seccion">
			<p class="titulo-principal">¡Hola! Soy Agustina Lopez, fotógrafa aficionada y creadora visual.</p>
			<p class="mensaje-bienvenida">Explora mi mundo a través de mis imágenes y sígueme en Instagram para ver más.</p>
			<div class="redes-sociales">
				<a href="https://www.instagram.com/aguslopez_fotografia/"
				   target="_blank"
				   rel="noopener noreferrer"
				   aria-label="Instagram de Agustina Lopez">
					<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor"
						 class="bi bi-instagram" viewBox="0 0 16 16" aria-hidden="true" focusable="false">
						<title>Instagram</title>
						<path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.11.281-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/>
					</svg>
					<span class="sr-only">Instagram de Agustina Lopez</span>
				</a>
			</div>
		</div>
	</section>

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
	//Trae todos los likes de todas las imágenes de todas las galerías de una vez
	$likes_all = [];
	$user_likes = [];
	$usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;
	$tablas = [
		'paisajes' => ['id' => 'paisajes_id', 'tabla' => 'fotos_paisajes'],
		'autos' => ['id' => 'autos_id', 'tabla' => 'fotos_autos'],
		'eventos' => ['id' => 'eventos_id', 'tabla' => 'fotos_eventos'],
		'viajes' => ['id' => 'viajes_id', 'tabla' => 'fotos_viajes']
	];
	foreach ($tablas as $origen => $info) {
		// Likes totales por imagen para la galería
		$sql_likes = "SELECT item_id, COUNT(*) as likes FROM likes WHERE tabla_origen = '$origen' GROUP BY item_id";
		$result_likes = $conn->query($sql_likes);
		if ($result_likes) {
			while ($row = $result_likes->fetch_assoc()) {
				$likes_all[$origen][$row['item_id']] = (int)$row['likes'];
			}
		}
		// Likes del usuario actual (solo si está logueado)
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

	function render_galeria($origen, $titulo, $id_col, $tabla, $likes_all, $user_likes, $conn) {
		echo '<section id="'.$origen.'" class="gallery">';
		echo '<h3>'.$titulo.'</h3>';
		echo '<div class="galeria">';
		$sql = "SELECT * FROM $tabla" . ($origen === 'autos' ? " WHERE categoria = 'autos'" : "");
		$result = $conn->query($sql);
		if ($result && $result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$item_id = htmlspecialchars($row[$id_col]);
				$ruta = htmlspecialchars($row['ruta']);
				$nombre = htmlspecialchars($row['nombre']);
				$descripcion = isset($row['descripcion']) ? htmlspecialchars($row['descripcion']) : '';
				$likes_count = isset($likes_all[$origen][$item_id]) ? $likes_all[$origen][$item_id] : 0;
				$liked_class = (isset($user_likes[$origen][$item_id])) ? ' liked' : '';
				$display_empty = ($liked_class) ? 'none' : 'block';
				$display_filled = ($liked_class) ? 'block' : 'none';
				$texto_like = ($liked_class) ? 'Quitar me gusta de ' . $nombre : 'Dar me gusta a ' . $nombre;

				echo '<div class="item">';
				$img_path_fs = $ruta;
				if (!file_exists($img_path_fs) && file_exists(__DIR__ . '/' . $img_path_fs)) {
					$img_path_fs = __DIR__ . '/' . $img_path_fs;
				}
				$dimensiones = @getimagesize($img_path_fs);
				$ancho = $dimensiones ? $dimensiones[0] : 800;
				$alto  = $dimensiones ? $dimensiones[1] : 600;

				echo '<img src="' . $ruta . '" alt="' . $nombre . '" data-description="' . $descripcion . '" loading="lazy" width="' . $ancho . '" height="' . $alto . '">';
				
				if ($origen === 'paisajes' || $origen === 'viajes') {
					if (!empty($descripcion)) {
						echo '<p class="descripcion">' . $descripcion . '</p>';
					}
				} elseif ($origen === 'eventos') {
					if (!empty($nombre)) {
						echo '<p>' . $nombre . '</p>';
					}
				} else {
					echo '<p>' . $nombre . '</p>';
				}

				echo '<div class="like-container">';
				echo '<button class="like-btn' . $liked_class . '" data-image-id="' . $item_id . '" data-tabla-origen="' . $origen . '" aria-label="' . $texto_like . '" title="' . $texto_like . '">';
				echo '<span class="sr-only">' . $texto_like . '</span>';
				// SVGs
				echo '<svg class="heart-icon heart-empty-icon" width="30" height="30" viewBox="0 0 16 16" style="display: ' . $display_empty . ';">';
				echo '<path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"/>';
				echo '</svg>';
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
		echo '</div></section>';
	}
	render_galeria('paisajes','Fotografías de Paisajes','paisajes_id','fotos_paisajes',$likes_all,$user_likes,$conn);
	render_galeria('autos','Fotografías de Autos','autos_id','fotos_autos',$likes_all,$user_likes,$conn);
	render_galeria('eventos','Evento de Motocross','eventos_id','fotos_eventos',$likes_all,$user_likes,$conn);
	render_galeria('viajes','Fotografías de Viajes','viajes_id','fotos_viajes',$likes_all,$user_likes,$conn);
	?>

	<div id="lightbox" class="lightbox">
		<span class="close-fullscreen" onclick="closeLightbox()">&times;</span>
		<div class="lightbox-prev" onclick="changeLightboxImage(-1)">&#10094;</div> 
		<img id="lightbox-img" class="lightbox-img" src="" alt="Imagen Lightbox">
		<div class="lightbox-next" onclick="changeLightboxImage(1)">&#10095;</div> 
		<p id="lightbox-description"></p>
	</div>

	<script src="scripts.js"></script>

<section id="servicios">
  <div class="planes-section">
    <h2 class="planes-titulo">Sesiones de Fotos</h2>
    <div class="planes-descripcion">
      Elige tu plan y reserva tu sesión. Todos incluyen edición profesional y entrega digital de alta calidad.
    </div>
    <div class="plan-container">
      <!-- Plan Básico -->
      <div class="plan-card">
        <div class="plan-header">Plan Básico</div>
        <div class="plan-body">
          <div class="plan-precio">$100</div>
          <ul class="plan-list">
            <li>• Sesión de 1 hora</li>
            <li>• 20 fotos editadas</li>
            <li>• Entrega digital en alta resolución</li>
            <li>• 1 impresión en tamaño 10x15 a elección</li>
          </ul>
          <form action="procesar_reserva.php" method="POST" class="plan-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="plan" value="basico">
            <input type="text" name="nombre" placeholder="Nombre" required>
            <input type="email" name="email" placeholder="Correo electrónico" required>
            <label for="fecha_deseada_basico">Elegí una fecha</label>
			<input type="date" name="fecha_deseada" required>
            <button type="submit" class="plan-btn">Reservar</button>
          </form>
        </div>
      </div>
      <!-- Plan Intermedio -->
      <div class="plan-card">
        <div class="plan-header">Plan Intermedio</div>
        <div class="plan-body">
          <div class="plan-precio">$250</div>
          <ul class="plan-list">
            <li>• Sesión de 2 horas</li>
            <li>• 40 fotos editadas</li>
            <li>• Entrega digital en alta resolución</li>
            <li>• Asesoramiento de vestuario básico</li>
            <li>• Cambio de locación dentro de la ciudad</li>
          </ul>
          <form action="procesar_reserva.php" method="POST" class="plan-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="plan" value="intermedio">
            <input type="text" name="nombre" placeholder="Nombre" required>
            <input type="email" name="email" placeholder="Correo electrónico" required>
            <label for="fecha_deseada_basico">Elegí una fecha</label>
			<input type="date" name="fecha_deseada" required>
            <button type="submit" class="plan-btn">Reservar</button>
          </form>
        </div>
      </div>
      <!-- Plan Premium -->
      <div class="plan-card">
        <div class="plan-header">Plan Premium</div>
        <div class="plan-body">
          <div class="plan-precio">$400</div>
          <ul class="plan-list">
            <li>• Sesión de 3 horas</li>
            <li>• 80 fotos editadas</li>
            <li>• Entrega digital en alta resolución</li>
            <li>• Video corto con momentos destacados</li>
            <li>• Asesoramiento de vestuario y estilismo</li>
            <li>• Hasta dos locaciones dentro de la ciudad</li>
          </ul>
          <form action="procesar_reserva.php" method="POST" class="plan-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="plan" value="premium">
            <input type="text" name="nombre" placeholder="Nombre" required>
            <input type="email" name="email" placeholder="Correo electrónico" required>
            <label for="fecha_deseada_basico">Elegí una fecha</label>
			<input type="date" name="fecha_deseada" required>
            <button type="submit" class="plan-btn">Reservar</button>
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
			<p class="blog-intro">¡Bienvenido a nuestro espacio dedicado a la pasión por la fotografía! Aquí podrás compartir, aprender y conectar con otros entusiastas.</p>
			<div class="blog-grid">

				<!-- Consejos -->
				<article class="blog-card consejos">
					<a href="blog.php?categoria=consejos">
						<?php
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
						<h3>Consejos</h3>
						<p class="card-description">Aquí puedes recibir consejos sobre cómo mejorar en la fotografía. ¡Comparte tus trucos y aprende de la comunidad!</p>
						<span class="read-more">Ver Consejos</span>
					</a>
				</article>

				<!-- Experiencias -->
				<article class="blog-card experiencias">
					<a href="blog.php?categoria=experiencias">
						<?php
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
						<h3>Experiencias y Viajes</h3>
						<p class="card-description">Aquí puedes contar historias y experiencias de viajes y aventuras que hayas tenido. ¡Inspira a otros con tus relatos fotográficos!</p>
						<span class="read-more">Ver Experiencias</span>
					</a>
				</article>

				<!-- Comentarios -->
				<article class="blog-card comentarios">
					<a href="blog.php?categoria=comentarios">
						<?php
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
						<h3>Comentarios y Sugerencias</h3>
						<p class="card-description">Aquí puedes comentar y opinar sobre el sitio web. ¡Tu feedback es valioso para seguir mejorando!</p>
						<span class="read-more">Ver Comentarios</span>
					</a>
				</article>
			</div>
		</div>
	</section>

	<div class="contacto-wrapper" id="contacto">
		<div class="contacto-card">
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
	</div>
</body>
</html>