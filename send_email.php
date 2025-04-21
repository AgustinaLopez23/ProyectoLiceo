<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'C:\xampp\htdocs\ProyectoLiceo\phpmailer\PHPMailer-master\src\Exception.php';
require 'C:\xampp\htdocs\ProyectoLiceo\phpmailer\PHPMailer-master\src\PHPMailer.php';
require 'C:\xampp\htdocs\ProyectoLiceo\phpmailer\PHPMailer-master\src\SMTP.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir los datos del formulario
    $nombre = strip_tags(trim($_POST["nombre"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $mensaje = strip_tags(trim($_POST["mensaje"]));

    // Validar los datos
    if (empty($nombre) || empty($mensaje) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo "Por favor, completa todos los campos correctamente.";
        exit;
    }

    // Crear una instancia de PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->SMTPDebug = SMTP::DEBUG_OFF; // 0 = off (producción), 1 = client messages, 2 = client and server messages
        $mail->isSMTP();                                            // Usar SMTP para enviar
        $mail->Host       = 'smtp.gmail.com';                     // Servidor SMTP de Gmail
        $mail->SMTPAuth   = true;                                   // Habilitar la autenticación SMTP
        $mail->Username   = 'al5261486@gmail.com';                // Tu dirección de correo electrónico de Gmail
        $mail->Password   = 'uplmjkxrrbtgspie';        // ¡REEMPLAZA CON TU CONTRASEÑA DE APLICACIÓN DE GMAIL!
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;        // Usar TLS
        $mail->Port       = 465;                                    // Puerto TCP para TLS

        // Remitente y destinatario
        $mail->setFrom($email, $nombre);
        $mail->addAddress('al5261486@gmail.com', 'Agustina Lopez');     // Tu dirección de correo electrónico de destino

        // Contenido del correo
        $mail->isHTML(false);                                  // Establecer el formato del correo como texto plano
        $mail->Subject = 'Nuevo mensaje de contacto desde tu sitio web';
        $mail->Body    = "Nombre: $nombre\n";
        $mail->Body   .= "Email: $email\n\n";
        $mail->Body   .= "Mensaje:\n$mensaje\n";

        $mail->send();
        http_response_code(200);
        echo '¡Gracias! Tu mensaje ha sido enviado.';
    } catch (Exception $e) {
        http_response_code(500);
        echo "Hubo un error al enviar tu mensaje. Por favor, intenta nuevamente más tarde. Error: {$mail->ErrorInfo}";
    }
} else {
    http_response_code(403);
    echo "Acceso prohibido.";
}
?>