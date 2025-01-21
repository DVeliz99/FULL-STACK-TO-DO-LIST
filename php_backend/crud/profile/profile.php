<?php
session_start(); // Inicia la sesión

header("Access-Control-Allow-Origin: http://localhost:4200"); // Permite solicitudes desde tu aplicación Angular
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header('Content-Type: application/json');


require_once '/Users/Dario/Desktop/Desarrollador_FULL-STACK/TO-DO-LIST/php_backend/db_connection.php';
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

    if ($user_id > 0) {
        $query = " SELECT u.id_user, u.user_name, u.name, u.email, u.start_date_app, u.avatar, COUNT(CASE WHEN t.status_id = 1 THEN 1 END) AS tareas_pendientes, COUNT(CASE WHEN t.status_id = 3 THEN 1 END) AS tareas_completadas, COUNT(CASE WHEN t.status_id = 2 THEN 1 END) AS tareas_expiradas FROM users u LEFT JOIN tasks t ON u.id_user = t.user_id WHERE u.id_user = $user_id GROUP BY u.id_user; ";
        if ($result = mysqli_query($db, $query)) {
            $data = mysqli_fetch_assoc($result); // Obtener los datos como un array asociativo
            $login_date = new DateTime();
            $formatted_login_date = $login_date->format('Y-m-d');
            if ($data) { // Verificar que todas las claves necesarias existen en $data
                $user_data = [
                    'user_id'=>$data['id_user']??null,
                    'user_name' => $data['user_name'] ?? null,
                    'name' => $data['name'] ?? null,
                    'email' => $data['email'] ?? null,
                    'app_start_date' => $data['start_date_app'] ?? null,
                    'avatar' => $data['avatar'] ?? null,
                    'pending_tasks' => $data['tareas_pendientes'] ?? null,
                    'completed_tasks' => $data['tareas_completadas'] ?? null,
                    'expired_tasks' => $data['tareas_expiradas'] ?? null,
                    'last_login_date' => $formatted_login_date
                ];
                // Enviar respuesta JSON con los datos obtenidos
                echo json_encode(['status' => 'success', 'user' => $user_data]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'El usuario no existe.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error en la consulta a la base de datos.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Parámetro user_id inválido.']);
    }
}
