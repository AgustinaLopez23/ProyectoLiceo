<?php
session_start();
session_regenerate_id(true); // Prevenir fijación de sesión 

// Datos de conexión a la base de datos
$host = 'localhost';
$usuario = 'root';
$contrasena = '';
$base_de_datos = 'portafolio_db'; // Asegúrate de que este sea el nombre correcto de tu base de datos

$conn = new mysqli($host, $usuario, $contrasena, $base_de_datos);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// Verificar si se recibieron los datos necesarios
if (isset($_POST['item_id']) && isset($_POST['tabla_origen'])) {
    $itemId = $_POST['item_id'];
    $tablaOrigen = $_POST['tabla_origen'];

    // Validación adicional para la tabla de origen (opcional pero recomendado)
    $tablasValidas = ['paisajes', 'autos', 'eventos', 'viajes'];
    if (!in_array($tablaOrigen, $tablasValidas)) {
        $response = array('success' => false, 'error' => 'Tabla de origen no válida.');
        header('Content-Type: application/json');
        echo json_encode($response);
        $conn->close();
        exit();
    }

    // Obtener el ID del usuario de la sesión si está logueado
    $usuarioId = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;

    // SOLO PERMITIR LIKES SI ESTÁ LOGUEADO
    if ($usuarioId === null) {
        $response = array('success' => false, 'error' => 'Debes iniciar sesión para dar like.');
        header('Content-Type: application/json');
        echo json_encode($response);
        $conn->close();
        exit();
    }

    // Verificar si el usuario ya dio "me gusta" a este ítem en esta tabla
    $sql_check = "SELECT id_like FROM likes WHERE item_id = ? AND tabla_origen = ? AND usuario_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("isi", $itemId, $tablaOrigen, $usuarioId);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Ya dio like, lo quitamos
        $sql_delete = "DELETE FROM likes WHERE item_id = ? AND tabla_origen = ? AND usuario_id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("isi", $itemId, $tablaOrigen, $usuarioId);
        if ($stmt_delete->execute()) {
            $response = array('success' => true, 'likes' => contarLikes($conn, $itemId, $tablaOrigen));
        } else {
            $response = array('success' => false, 'error' => 'Error al quitar el like: ' . $stmt_delete->error);
        }
        $stmt_delete->close();
    } else {
        // Antes de insertar, revisa si ya existe el like para ese usuario (por seguridad)
        $sql_exists = "SELECT 1 FROM likes WHERE item_id = ? AND tabla_origen = ? AND usuario_id = ?";
        $stmt_exists = $conn->prepare($sql_exists);
        $stmt_exists->bind_param("isi", $itemId, $tablaOrigen, $usuarioId);
        $stmt_exists->execute();
        $stmt_exists->store_result();

        if ($stmt_exists->num_rows == 0) {
            // No ha dado like, lo agregamos
            $sql_insert = "INSERT INTO likes (item_id, tabla_origen, usuario_id) VALUES (?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("isi", $itemId, $tablaOrigen, $usuarioId);
            if ($stmt_insert->execute()) {
                $response = array('success' => true, 'likes' => contarLikes($conn, $itemId, $tablaOrigen));
            } else {
                $response = array('success' => false, 'error' => 'Error al dar like: ' . $stmt_insert->error);
            }
            $stmt_insert->close();
        } else {
            // Ya existe el like, solo contamos
            $response = array('success' => true, 'likes' => contarLikes($conn, $itemId, $tablaOrigen));
        }
        $stmt_exists->close();
    }
    $stmt_check->close();

    // Devolver la respuesta en formato JSON
    header('Content-Type: application/json');
    echo json_encode($response);

} else {
    // Si no se recibieron los datos necesarios
    $response = array('success' => false, 'error' => 'Datos incompletos.');
    header('Content-Type: application/json');
    echo json_encode($response);
}

// Función para contar los likes de un ítem específico en una tabla (solo de usuarios logueados)
function contarLikes($conn, $itemId, $tablaOrigen) {
    $sql_count = "SELECT COUNT(*) FROM likes WHERE item_id = ? AND tabla_origen = ? AND usuario_id IS NOT NULL";
    $stmt_count = $conn->prepare($sql_count);
    $stmt_count->bind_param("is", $itemId, $tablaOrigen);
    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    $row_count = $result_count->fetch_row();
    $stmt_count->close();
    return (int)$row_count[0];
}

$conn->close();
?>