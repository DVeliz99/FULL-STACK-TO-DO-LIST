<?php


header("Access-Control-Allow-Origin: *"); // Permite todas las solicitudes 
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header('Content-Type: application/json');

// Obtener la ruta de la solicitud
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Lógica para manejar las rutas
switch (true) {
    case preg_match('/^\/crud\/dashboard/', $request):
        require __DIR__ . '/crud/dashboard/dashboard1.php';
        break;

    case preg_match('/^\/crud\/filter\/get_category/', $request):
        require __DIR__ . '/crud/filter/get_category.php';
        break;

    case preg_match('/^\/crud\/filter\/get_state/', $request):
        require __DIR__ . '/crud/filter/get_state.php';
        break;

    case preg_match('/^\/crud\/profile/', $request):
        require __DIR__ . '/crud/profile/profile.php';
        break;

    case preg_match('/^\/crud\/tasks\/create_task/', $request):
        require __DIR__ . '/crud/tasks/create_task.php';
        break;

    case preg_match('/^\/crud\/tasks\/delete_task/', $request):
        require __DIR__ . '/crud/tasks/delete_task.php';
        break;

    case preg_match('/^\/crud\/tasks\/edit_task/', $request):
        require __DIR__ . '/crud/tasks/edit_task.php';
        break;

    case preg_match('/^\/crud\/tasks\/expire_task/', $request):
        require __DIR__ . '/crud/tasks/expire_task.php';
        break;

    case preg_match('/^\/crud\/tasks\/specificTask/', $request):
        require __DIR__ . '/crud/tasks/specificTask.php';
        break;

    case preg_match('/^\/crud\/tasks/', $request): // Ruta general para las tareas
        require __DIR__ . '/crud/tasks/tasks.php';
        break;

    case preg_match('/^\/crud\/login/', $request):
        require __DIR__ . '/crud/login.php';
        break;

    case preg_match('/^\/crud\/logout/', $request):
        require __DIR__ . '/crud/logout.php';
        break;

    case preg_match('/^\/crud\/registration/', $request):
        require __DIR__ . '/crud/registration.php';
        break;

    case preg_match('/^\/crud\/check_session/', $request):
        require __DIR__ . '/check_session.php';
        break;

        case preg_match('/^\/db_connection/', $request):
            require __DIR__ . '/db_connection.php';
            break;

    

    default:  // Si la ruta no coincide con ninguna
        http_response_code(404);  // Devuelve un error 404
        echo json_encode(["error" => "Endpoint no encontrado"]);
        break;
}