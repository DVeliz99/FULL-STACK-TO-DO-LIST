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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // echo "Se recibio una solicitud por POST ";


    $user_id = isset($_POST['user_id']) ? mysqli_real_escape_string($db, $_POST['user_id']) : false;
    $task_name = isset($_POST['name']) ? mysqli_real_escape_string($db, $_POST['name']) : false;
    $category = isset($_POST['category']) ? mysqli_real_escape_string($db, $_POST['category']) : false;
    $status = isset($_POST['status']) ? mysqli_real_escape_string($db, $_POST['status']) : false;
    $start_date = isset($_POST['startDate']) ? mysqli_real_escape_string($db, $_POST['startDate']) : false;
    $end_date = isset($_POST['endDate']) ? mysqli_real_escape_string($db, $_POST['endDate']) : false;

 


    // echo "$user_id";
    // echo "<br>";
    // echo "$task_name";
    // echo "<br>";
    // echo "$category";
    // echo "<br>";
    // echo "$start_date";
    // echo "<br>";
    // echo " $end_date";


    if ($user_id && $task_name && $category && $status && $start_date && $end_date) {

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
                $check_query = "SELECT id_task FROM tasks WHERE task_name = '$task_name' AND category_id = '$category_id' AND status_id = '$status_id' AND start_date_task = '$start_date' AND due_date = '$end_date' AND user_id= '$user_id';";


                $check_result = mysqli_query($db, $check_query);
                if (mysqli_num_rows($check_result) > 0) {
                    echo json_encode(['status' => 'error', 'message' => 'Ya existe una tarea con los mismos valores.']);
                    exit;
                }


                $insert_task_query = "INSERT INTO tasks (task_name, start_date_task, due_date, category_id, user_id, status_id) VALUES ('$task_name', '$start_date', '$end_date', '$category_id', '$user_id', '$status_id');";

                $result_task_query = mysqli_query($db, $insert_task_query);


                if ($result_task_query && mysqli_affected_rows($db) > 0) {

                    echo json_encode(['status' => 'success', 'message' => 'Tarea registrada con exito.']);
                    exit;
                } else {

                    echo json_encode(['status' => 'error', 'message' => 'Hubo un error al ingresar la tarea.']);
                    exit;
                }
            } else {

                echo json_encode(['status' => 'error', 'message' => 'Categoría no encontrada.']);
                exit;
            }
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Todos los campos son obligatorios.']);
        exit;
    }
}
