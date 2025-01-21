<?php

session_start();

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '/Users/Dario/Desktop/Desarrollador_FULL-STACK/TO-DO-LIST/php_backend/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $current_date = date('Y-m-d');

    $user_id = isset($_GET['user_id']) ? mysqli_real_escape_string($db, $_GET['user_id']) : null;

    if ($user_id) {
        $expire_tasks_query = "UPDATE tasks SET expiry_date = '$current_date', status_id = 2 WHERE user_id = '$user_id' AND due_date < '$current_date' AND expiry_date IS NULL AND status_id!=3;";
        $result_update = mysqli_query($db, $expire_tasks_query);

        if (mysqli_affected_rows($db) > 0) {

            $expired_tasks_query = "SELECT id_task FROM tasks WHERE expiry_date= '$current_date' AND user_id='$user_id';";
            $result_expired_tasks_query = mysqli_query($db, $expired_tasks_query);

            if ($result_expired_tasks_query) {
                $data = [];

                while ($row_id_expired_tasks = mysqli_fetch_assoc($result_expired_tasks_query)) {
                    $data[] = $row_id_expired_tasks;
                }

                $expired_tasks = count($data);

                echo json_encode(['status' => 'success', 'message' => 'Cantidad de tareas expiradas: ' . $expired_tasks]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al obtener tareas expiradas.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se encontraron tareas para expirar o ya están expiradas.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al obtener user_id.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método de solicitud no permitido.']);
}
