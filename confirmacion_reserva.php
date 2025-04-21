<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva Recibida</title>
    <style>
        body { font-family: sans-serif; margin: 20px; background-color: #f4f4f4; }
        .container { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); text-align: center; }
        h1 { color: #5cb85c; }
        p { margin-bottom: 15px; }
        .boton-volver { display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; }
        .boton-volver:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($_GET['reserva_exitosa'])): ?>
            <h1>¡Reserva Recibida!</h1>
            <?php if (isset($_GET['nombre']) && isset($_GET['email'])): ?>
                <p>Gracias, <?php echo htmlspecialchars($_GET['nombre']); ?>, por tu reserva con Agustina Fotografía.</p>
                <p>Hemos recibido tu solicitud y te enviaremos un correo electrónico a <?php echo htmlspecialchars($_GET['email']); ?> con los detalles y los próximos pasos para coordinar tu sesión de fotos.</p>
            <?php else: ?>
                <p>Gracias por tu reserva con Agustina Fotografía.</p>
                <p>Hemos recibido tu solicitud y nos pondremos en contacto contigo a la brevedad.</p>
            <?php endif; ?>
            <p>Por favor, revisa tu bandeja de entrada (y la carpeta de spam o correo no deseado) en los próximos minutos.</p>
            <p>¡Estamos emocionados de trabajar contigo!</p>
            <a href="ProyectoLiceo.php" class="boton-volver">Volver a la página principal</a>
        <?php else: ?>
            <h1>Error</h1>
            <p>Hubo un problema al procesar tu reserva.</p>
            <a href="ProyectoLiceo.php" class="boton-volver">Volver a la página principal</a>
        <?php endif; ?>
    </div>
</body>
</html>