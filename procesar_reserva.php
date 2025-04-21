<?php

// Habilitar la visualización de errores para desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir los archivos de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Ajusta estas rutas PARA QUE COINCIDAN EXACTAMENTE con las de send_mail.php
require 'C:\xampp\htdocs\ProyectoLiceo\phpmailer\PHPMailer-master\src\Exception.php';
require 'C:\xampp\htdocs\ProyectoLiceo\phpmailer\PHPMailer-master\src\PHPMailer.php';
require 'C:\xampp\htdocs\ProyectoLiceo\phpmailer\PHPMailer-master\src\SMTP.php';

// Datos de conexión a la base de datos
$host = 'localhost';
$usuario_db = 'root';
$contrasena_db = '';
$nombre_db = 'portafolio_db';
$tabla_reservas = 'reservas';

// Dirección de correo electrónico para las notificaciones a Agustina (si la necesitas en el futuro)
$email_agustina = 'al5261486@gmail.com';
$nombre_agustina = 'Agustina Fotografia';

// Asunto del correo electrónico para el cliente
$asunto_cliente = 'Confirmacion de Reserva - Sesiones de Fotos Agustina';

// Página de confirmación de reserva
$pagina_confirmacion = 'confirmacion_reserva.php';

// Conectar a la base de datos
$conn = new mysqli($host, $usuario_db, $contrasena_db, $nombre_db);

if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// Función para validar datos (simplificada)
function validarDatos($datos, $conn) {
    $errores = [];
    if (!isset($datos['plan']) || empty($datos['plan'])) $errores[] = 'El plan es requerido.';
    if (!isset($datos['nombre']) || empty($datos['nombre'])) $errores[] = 'El nombre es requerido.';
    if (!isset($datos['email']) || !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) $errores[] = 'El email no es válido.';
    if (!isset($datos['fecha_deseada']) || empty($datos['fecha_deseada'])) $errores[] = 'La fecha deseada es requerida.';
    return $errores;
}

// Función para enviar correo electrónico con PHPMailer
function enviarEmailPHPMailer(string $destinatario, string $asunto, string $mensaje, string $nombreDestinatario = '') {
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->SMTPDebug = SMTP::DEBUG_OFF; // Cambiar a SMTP::DEBUG_SERVER para depuración detallada
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';  // Servidor SMTP de Gmail
        $mail->SMTPAuth   = true;
        $mail->Username   = 'al5261486@gmail.com'; // Tu dirección de correo electrónico de Gmail
        $mail->Password   = 'uplmjkxrrbtgspie'; // ¡UTILIZANDO LA CONTRASEÑA DE APLICACIÓN DE send_mail.php!
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Usar SSL
        $mail->Port       = 465;                   // Puerto TCP para SSL

        // Remitente y destinatario
        $mail->setFrom('al5261486@gmail.com', $GLOBALS['nombre_agustina']); // Tu correo y nombre
        $mail->addAddress($destinatario, $nombreDestinatario); // Destinatario

        // Contenido del correo
        $mail->isHTML(false);
        $mail->Subject = $asunto;
        $mail->Body    = $mensaje;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Error al enviar el correo con PHPMailer a " . $destinatario . ": " . $mail->ErrorInfo);
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errores_validacion = validarDatos($_POST, $conn);

    if (empty($errores_validacion)) {
        $plan = $conn->real_escape_string($_POST['plan']);
        $nombre = $conn->real_escape_string($_POST['nombre']);
        $email = $conn->real_escape_string($_POST['email']);
        $fecha_deseada = $conn->real_escape_string($_POST['fecha_deseada']);

        $sql = "INSERT INTO $tabla_reservas (plan, nombre_cliente, email_cliente, fecha_deseada, fecha_reserva) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $plan, $nombre, $email, $fecha_deseada);

        if ($stmt->execute()) {
            $mensaje_cliente = "Hola " . $nombre . ",\n\nGracias por reservar una sesión de fotos con Agustina.\n\nDetalles de tu reserva:\nPlan: " . ucfirst($plan) . "\nFecha Deseada: " . $fecha_deseada . "\n\nNos pondremos en contacto contigo a la brevedad para coordinar los detalles.\n\n¡Gracias!\n\nAtentamente,\n" . $nombre_agustina;
            enviarEmailPHPMailer($email, $asunto_cliente, $mensaje_cliente, $nombre);

            header("Location: " . $pagina_confirmacion . "?reserva_exitosa=true&nombre=" . urlencode($nombre) . "&email=" . urlencode($email));
            exit();
        } else {
            echo "Error al guardar la reserva: " . $stmt->error;
        }
    } else {
        echo "Errores de validación:<br>";
        foreach ($errores_validacion as $error) {
            echo "- " . htmlspecialchars($error) . "<br>";
        }
    }
} else {
    echo "Acceso no permitido.";
}

$conn->close();

?>