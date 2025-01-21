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


if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Obtener los datos crudos de la solicitud PUT
    $input = file_get_contents('php://input');

    //Decodificar el input desde JSON
    $data = json_decode($input, true);

    // Verificar que $data no esté vacío y sea un array 
    if (!is_array($data)) {
        echo json_encode(['status' => 'error', 'message' => 'Datos recibidos no válidos.']);
        exit;
    }

    // var_dump($data);

    // Log para verificar los datos recibidos 
    // file_put_contents('php://stderr', "Datos recibidos: " . print_r($data, true));

    $task_id = isset($data['task_id']) ? mysqli_real_escape_string($db, $data['task_id']) : false;
    $task_name = isset($data['name']) ? mysqli_real_escape_string($db, $data['name']) : false;
    $category = isset($data['category']) ? mysqli_real_escape_string($db, $data['category']) : false;
    $status = isset($data['status']) ? mysqli_real_escape_string($db, $data['status']) : false;
    $start_date = isset($data['startDate']) ? mysqli_real_escape_string($db, $data['startDate']) : false;
    $end_date = isset($data['endDate']) ? mysqli_real_escape_string($db, $data['endDate']) : false;

    // Log para verificar campos específicos 
    // file_put_contents('php://stderr', "task_id: $task_id, name: $task_name, category: $category, status: $status, startDate: $start_date, endDate: $end_date");


    if (!$task_id || !$task_name || !$category || !$status || !$start_date || !$end_date) {
        echo json_encode(['status' => 'error', 'message' => 'Faltan datos necesarios.']);
        exit;
    }

   

    //Obtencion de la categoria_id
    $categoria_id_query = "SELECT id_category from categories WHERE category='$category';";

    $result_category_id = mysqli_query($db, $categoria_id_query);




    if ($result_category_id && mysqli_num_rows($result_category_id) > 0) {

        $row_category = mysqli_fetch_assoc($result_category_id);
        $category_id = $row_category['id_category'];


        $status_id_query = "SELECT id_status FROM statuses WHERE status = '$status';";

        $result_status_id = mysqli_query($db, $status_id_query);



        if ($result_status_id && mysqli_num_rows($result_status_id) > 0) {

            $row_status = mysqli_fetch_assoc($result_status_id);
            $status_id = $row_status['id_status'];

            // Verificar si ya existe una tarea con los mismos valores 
            $check_query = "SELECT id_task FROM tasks WHERE task_name = '$task_name' AND category_id = '$category_id' AND status_id = '$status_id' AND start_date_task = '$start_date' AND due_date = '$end_date' AND id_task != '$task_id';";

            $check_result = mysqli_query($db, $check_query);
            if (mysqli_num_rows($check_result) > 0) {
                echo json_encode(['status' => 'error', 'message' => 'Ya existe una tarea con los mismos valores.']);
                exit;
            }



            $query = "UPDATE tasks
                          SET task_name = '$task_name', category_id = '$category_id', status_id='$status_id', start_date_task='$start_date', due_date = '$end_date'
                          WHERE id_task = '$task_id';";

            $result_query = mysqli_query($db, $query);

            if (mysqli_affected_rows($db) > 0) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Tarea actualizada exitosamente.' . mysqli_error($db)
                ]);
                exit;
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error al actualizar tarea.' . mysqli_error($db)
                ]);
            }
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Categoría no encontrada.']);
        exit;
    }

    exit;
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error en la solicitud. Revisa los datos enviados o las consultas de la base de datos.']);
}
