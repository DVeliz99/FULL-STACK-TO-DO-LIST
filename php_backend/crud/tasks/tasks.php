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

    //Parametros en la peticion GET 
    $user_id = isset($_GET['user_id']) ? mysqli_real_escape_string($db, $_GET['user_id']) : null;
    $start_date = isset($_GET['start_date']) ? mysqli_real_escape_string($db, $_GET['start_date']) : null;
    $end_date = isset($_GET['end_date']) ? mysqli_real_escape_string($db, $_GET['end_date']) : null;
    $category = isset($_GET['category']) ? mysqli_real_escape_string($db, $_GET['category']) : null;
    $status = isset($_GET['status']) ? mysqli_real_escape_string($db, $_GET['status']) : null;

    // echo "$user_id";
    // echo "</br>";
    // echo "$start_date";
    // echo "</br>";
    // echo "$end_date";
    // echo "</br>";
    // echo "$category";
    // echo "</br>";
    // echo "$status";
    // echo "</br>";

    if (empty($user_id) || empty($start_date) || empty($end_date)) {
        echo json_encode(['status' => 'error', 'message' => 'Faltan parámetros necesarios']);
        exit;
    }

    // Si el estado llega vacio quiere decir que los filtros se enviaron desde el /dashboard
    if ($status === '' || $status == null) {
        $query = "SELECT id_task, t.task_name, c.category, s.status, 
        DATE(t.start_date_task) AS fecha_inicio_tarea, 
        DATE(t.due_date) AS due_date,
        DATE(t.expiry_date) AS fecha_expiracion
                  FROM tasks t 
                  JOIN categories c ON t.category_id = c.id_category
                  JOIN statuses s ON t.status_id = s.id_status
                  WHERE t.user_id = '$user_id' 
                  AND t.start_date_task BETWEEN '$start_date' AND '$end_date'";

        // Agregar el filtro de categoría si está presente y no es 'seleccionar categoria' 
        if (!empty($category) && strtolower(trim($category, ':')) !== 'seleccionar categoría') {
            $query .= " AND c.category = '$category'";
        }

        $query .= " ORDER BY t.start_date_task DESC LIMIT 4;";



        // Log de la consulta final para depuración
    //  echo "$query";

    } else {
        //De lo contrario la peticion se hizo desde /tasks
        $query = "SELECT id_task, t.task_name, c.category, s.status,
        DATE(t.expiry_date) AS fecha_expiracion, 
        DATE(t.start_date_task) AS fecha_inicio_tarea, 
        DATE(t.due_date) AS due_date 
                  FROM tasks t 
                  JOIN categories c ON t.category_id = c.id_category
                  JOIN statuses s ON t.status_id = s.id_status 
                  WHERE t.user_id = '$user_id' 
                  AND t.start_date_task BETWEEN '$start_date' AND '$end_date'";

        // Agregar el filtro de categoría si está presente y no es 'seleccionar categoria'
        if (!empty($category) && strtolower(trim($category, ':')) !== 'seleccionar categoría') {
            $query .= " AND c.category = '$category'";
        }

        if (!empty($status) && strtolower((trim($status,':'))) !== 'seleccionar estado') {
            $query .= " AND s.status = '$status'";
        }

        $query .= ";";


        // echo "$query";
    }

    // Ejecutar la consulta
    $result = mysqli_query($db, $query);
    if ($result) {
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        // Enviar respuesta JSON con los datos obtenidos 
        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error ejecutando la consulta'
        ]);
    }
}
