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

    $id_task = isset($_GET['taskId']) ? mysqli_real_escape_string($db, $_GET['taskId']) : null;
    


    if ($id_task) {
        $query = "SELECT id_task, t.task_name, c.category, s.status, 
        DATE(t.start_date_task) AS fecha_inicio_tarea, 
        DATE(t.due_date) AS due_date 
        FROM tasks t
        JOIN categories c ON t.category_id = c.id_category
        JOIN statuses s ON t.status_id = s.id_status 
        WHERE t.id_task = '$id_task';";


        // Ejecutar la consulta
        $result = mysqli_query($db, $query);


        if ($result) {
            $data = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }

            if (mysqli_num_rows($result) > 0) {
                // Enviar respuesta JSON con los datos obtenidos 
                echo json_encode([
                    'status' => 'success',
                    'data' => $data
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se encontraron datos']);
            }
        } 
    } else{
        echo json_encode(['status' => 'error', 'message' => 'ID de tarea no proporcionado']);
    
    }
}
