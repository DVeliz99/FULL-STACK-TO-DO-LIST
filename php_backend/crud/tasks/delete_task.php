<?php

session_start();

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '/Users/Dario/Desktop/Desarrollador_FULL-STACK/TO-DO-LIST/php_backend/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true); // Decodifica el JSON recibido
    // var_dump($data); // Verifica que los datos se estén recibiendo correctamente
    $task_id = isset($data['task_id']) ? mysqli_real_escape_string($db, $data['task_id']) : false;

    if ($task_id) {
        $delete_task_query = "DELETE FROM tasks WHERE id_task = '$task_id';";
        $result_delete_task = mysqli_query($db, $delete_task_query);

        if ($result_delete_task && mysqli_affected_rows($db) > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Tarea eliminada con éxito.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se pudo eliminar la tarea.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID de tarea no proporcionado.']);
    }
    exit;
}
?>
