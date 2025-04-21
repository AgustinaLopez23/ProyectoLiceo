<?php
// Habilitar informes de errores en desarrollo (descomentar en modo desarrollo)
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

if (!isset($conn)) { // Verifica si la conexión ya existe
    $server = "localhost";
    $user = "root";
    $pass = "";
    $db = "portafolio_db";

    $conn = new mysqli($server, $user, $pass, $db);

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
}

