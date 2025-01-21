<?php
session_start(); // Inicia la sesión

header("Access-Control-Allow-Origin: http://localhost:4200"); // Permite solicitudes desde la aplicación Angular
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header('Content-Type: application/json');

require_once '/Users/Dario/Desktop/Desarrollador_FULL-STACK/TO-DO-LIST/php_backend/db_connection.php';

// Consulta SQL para obtener estados
$query = "SELECT status FROM statuses;";

$result = mysqli_query($db, $query);


if (mysqli_num_rows($result) > 0){
    $categorias = array();
    while ($row = $result->fetch_assoc()) {
        $categorias[] = $row;
    
    }

    echo json_encode([
        'status' => 'success',
        'data' => $categorias
    ]);
}else{
    echo json_encode([
        'status' => 'error',
        'message' => ' Query no fue ejecutada correctamente.'
    ]);
}
