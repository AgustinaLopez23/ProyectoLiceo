<?php
session_start();
session_regenerate_id(true); // Previene la fijación de sesión

// Elimina todas las variables de sesión
$_SESSION = array();

// Borra la cookie de sesión si está habilitada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruye la sesión completamente
session_destroy();

// Redirige al usuario a la página principal
header("Location: login.php");
exit();
?>
