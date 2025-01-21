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
    // Obtener parámetros de la petición GET

    $user_id = isset($_GET['user_id']) ? mysqli_real_escape_string($db, $_GET['user_id']) : null;
    $start_date = isset($_GET['start_date']) ? mysqli_real_escape_string($db, $_GET['start_date']) : null;
    $end_date = isset($_GET['end_date']) ? mysqli_real_escape_string($db, $_GET['end_date']) : null;
    $category = isset($_GET['category']) ? mysqli_real_escape_string($db, $_GET['category']) : null;

    if (!$user_id || !$start_date || !$end_date || !$category) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Parámetros faltantes o inválidos'
        ]);
        exit;
    }

    // Validar el rango de fechas
    if (strtotime($start_date) > strtotime($end_date)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'La fecha de inicio no puede ser mayor que la fecha de fin'
        ]);
        exit;
    }

    $id_categoria = 'Seleccionar categoría:';
    $categoria_filtrada = false;
    if ($category && $category !== 'Seleccionar categoría:') {
        $category_query = "SELECT id_category FROM categories WHERE category = '$category'";
        $category_result = mysqli_query($db, $category_query);
        if ($category_result && mysqli_num_rows($category_result) > 0) {
            $category_row = mysqli_fetch_assoc($category_result);
            $id_categoria = $category_row['id_category'];
            $categoria_filtrada = true;
        }
    }


    // Consulta SQL principal usando id_categoria
    $query = "  SELECT u.user_name, u.name, 
                COALESCE(SUM(CASE WHEN t.status_id = 1 THEN 1 ELSE 0 END), 0) AS 'tareas_pendientes',
                COALESCE(SUM(CASE WHEN t.status_id = 3 THEN 1 ELSE 0 END), 0) AS 'tareas_completadas',
                COALESCE(SUM(CASE WHEN t.status_id = 2 THEN 1 ELSE 0 END), 0) AS 'tareas_expiradas', 
                COALESCE(SUM(CASE WHEN t.status_id IN (1, 2, 3) THEN 1 ELSE 0 END), 0) AS 'total_tareas'
                FROM users u 
                LEFT JOIN tasks t ON u.id_user = t.user_id AND t.start_date_task BETWEEN '$start_date' AND '$end_date'";

    if ($categoria_filtrada) {
        $query .= " AND t.category_id = '$id_categoria'";
    }
    $query .= " WHERE u.id_user = '$user_id' GROUP BY u.user_name, u.name;";
    $result = mysqli_query($db, $query);



    if (!$result) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error en la ejecución de la consulta: ' . mysqli_error($db)
        ]);
        exit;
    }

    $data = [];
    if (mysqli_num_rows($result) > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
        
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No se encontraron datos'
        ]);
    }
}
