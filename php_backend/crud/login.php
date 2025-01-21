<?php

// Asegúrate de que se está recibiendo la petición POST
$data = json_decode(file_get_contents("php://input"), true);


// // Configuración de las cookies de sesión en el servidor PHP
// ini_set('session.cookie_secure', 'true'); // Habilitar solo cookies seguras
// ini_set('session.cookie_httponly', 'true'); // Hacer cookies accesibles solo a través de HTTP
// session_set_cookie_params([
//     'lifetime' => 3600,  // Tiempo de vida de la sesión en segundos (1 hora)
//     'path' => '/',
//     'domain' => 'localhost',  // Cambié a 'localhost' para pruebas locales
//     'secure' => false,  // Para desarrollo local, debe ser 'false'
//     'httponly' => true,  // No accesibles por JavaScript
// ]);

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



// Comprobamos si los parámetros existen en el cuerpo de la solicitud
if (isset($data['user_name']) && isset($data['password'])) {
    $user_name = mysqli_real_escape_string($db, $data['user_name']);
    $password = mysqli_real_escape_string($db, $data['password']);

    if ($user_name && $password) {
        // Verificar si el usuario existe
        $query = "SELECT * FROM users WHERE user_name = '$user_name'";
        $result = mysqli_query($db, $query);

        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            // Verificar la contraseña
            if (password_verify($password, $user['password'])) {
                // Establecer variables de sesión
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['user_name'] = $user['user_name'];
                // Obtener la fecha y hora actual 
                $login_date = new DateTime(); 
                $formatted_login_date = $login_date->format('Y-m-d');

                // Responder con éxito
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Usuario autenticado con éxito.',
                    'user' => [
                        'user_id' => $user['id_user'],
                        'user_name' => $user['user_name'],
                        'login_date' => $formatted_login_date,
                        'avatar' => $user['avatar']
                    ],
                ]);
            } else {

                echo json_encode([
                    'status' => 'error',
                    'message' => 'Contraseña incorrecta.'

                  ]);
            }
        } else {

            echo json_encode([
                'status' => 'error',
                'message' => ' El usuario no existe.'
              ]);

            
        }
    } else {

        echo json_encode([
            'status' => 'error',
            'message' => ' Faltan parámetros.'
          ]);
    }
} else {
  

        // Parámetros faltantes
        echo json_encode([
          'status' => 'error',
        'message' => 'Método no permitido.'
        ]);
    
}


?>
