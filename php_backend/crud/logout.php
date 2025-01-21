<?php
session_start();

header("Access-Control-Allow-Origin: http://localhost:4200"); // Permite solicitudes desde tu aplicación Angular
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header('Content-Type: application/json');


// Eliminar cookies de sesión, si existen
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}



// Destruir la sesión
session_unset();
session_destroy();

// Devolver respuesta de cierre de sesión
echo json_encode(['status' => 'success', 'message' => 'Sesión cerrada']);
?>
