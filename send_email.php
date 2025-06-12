<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/phpmailer/PHPMailer-master/src/Exception.php';
require __DIR__ . '/phpmailer/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/phpmailer/PHPMailer-master/src/SMTP.php';

function mostrar_mensaje($titulo, $mensaje, $esExito = true) {
    // Mismo estilo que confirmacion_reserva.php
    ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo); ?></title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            min-height: 100vh;
            background-color:rgba(176, 228, 170, 0.83);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            width: 70%;
            max-width: 1300px;
            margin: 32px auto;
            background-color: #fff;
            padding: 40px 0px 40px 0px;
            border-radius: 10px;
            box-shadow: 0 0 16px rgba(0, 0, 0, 0.58), 0 1.5px 7px #79a97622;
            text-align: center;
        }
        h1 {
            color: <?php echo $esExito ? '#5cb85c' : '#d9534f'; ?>;
            font-size: 2.5em;
            margin-bottom: 20px;
            font-weight: 700;
        }
        p {
            margin-bottom: 25px;
            font-size: 1.2em;
        }
        .boton-volver {
            display: inline-block;
            padding: 12px 28px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 7px;
            font-size: 1.1em;
            font-weight: 500;
            border: none;
            cursor: pointer;
            box-shadow: 0 2px 6px #6ea16b33;
            transition: background .2s, transform .16s, box-shadow .2s;
        }
        .boton-volver:hover {
            background-color: #0056b3;
            transform: translateY(-2px) scale(1.04);
            box-shadow: 0 6px 18px #6ea16b22;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($titulo); ?></h1>
        <p><?php echo nl2br(htmlspecialchars($mensaje)); ?></p>
        <a href="ProyectoLiceo.php" class="boton-volver">Volver a la página principal</a>
    </div>
</body>
</html>
    <?php
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Usar el operador null coalescente para evitar warnings si algún campo falta
    $nombre = strip_tags(trim($_POST["nombre"] ?? ''));
    $email = filter_var(trim($_POST["email"] ?? ''), FILTER_SANITIZE_EMAIL);
    $mensaje = strip_tags(trim($_POST["mensaje"] ?? ''));

    if (empty($nombre) || empty($mensaje) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        mostrar_mensaje("Error", "Por favor, completa todos los campos correctamente.", false);
    }

    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'al5261486@gmail.com';
        $mail->Password   = 'uplmjkxrrbtgspie';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Para evitar problemas de spoofing, se recomienda que el setFrom sea tu propio correo
        $mail->setFrom('al5261486@gmail.com', $nombre); // El nombre del usuario se muestra como nombre
        $mail->addReplyTo($email, $nombre); // Así puedes responder al contacto real
        $mail->addAddress('al5261486@gmail.com', 'Agustina Lopez');

        $mail->isHTML(false);
        $mail->Subject = 'Nuevo mensaje de contacto desde tu sitio web';
        $mail->Body    = "Nombre: $nombre\n";
        $mail->Body   .= "Email: $email\n\n";
        $mail->Body   .= "Mensaje:\n$mensaje\n";

        $mail->send();
        mostrar_mensaje("¡Gracias!", "Tu mensaje ha sido enviado.");
    } catch (Exception $e) {
        mostrar_mensaje("Error", "Hubo un error al enviar tu mensaje. Por favor, intenta nuevamente más tarde. Error: {$mail->ErrorInfo}", false);
    }
} else {
    mostrar_mensaje("Acceso prohibido", "No tienes permiso para acceder a esta página.", false);
}
?>