<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json'); // Asegúrate de que la respuesta sea JSON

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require '/Users/Dario/Desktop/Desarrollador_FULL-STACK/TO-DO-LIST/php_backend/db_connection.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $user_name = isset($_POST['user_name']) ? mysqli_real_escape_string($db, $_POST['user_name']) : false;
    $nombre = isset($_POST['name']) ? mysqli_real_escape_string($db, $_POST['name']) : false;
    $email = isset($_POST['email']) ? mysqli_real_escape_string($db, $_POST['email']) : false;
    $password = isset($_POST['password']) ? mysqli_real_escape_string($db, $_POST['password']) : false;

    if (!$user_name || !$nombre || !$email || !$password) {
        echo json_encode(['status' => 'error', 'message' => 'Faltan datos necesarios para la solicitud.']);
        exit;
    }


    $secure_password = password_hash($password, PASSWORD_BCRYPT); // Encriptar la contraseña

    // Imagen
    $imagePath = '/uploads/no_photo.jpg'; // Ruta predeterminada para la imagen

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = $_FILES['image'];
        $uploadDir = __DIR__ . '/../uploads/'; // ../ sube un nivel y accede a la carpeta uploads
        $imageName = basename($image['name']); // Sanear el nombre del archivo
        $uploadFile = $uploadDir . $imageName; // Ruta completa del archivo

        // Asegúrate de que el directorio de destino exista
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Crear directorios si no existen
        }

        // Mover el archivo al directorio de uploads
        if (move_uploaded_file($image['tmp_name'], $uploadFile)) {
            $imagePath = '/uploads/' . $imageName; // Guardar la ruta relativa para la base de datos
        } else {

            echo json_encode([
                'status' => 'error',
                'message' => 'Hubo un error al mover la imagen al servidor.'
            ]);
            exit;
        }
    }

    // Comprobar si el correo o el nombre de usuario ya existen
    $sqlEmailCheck = "SELECT * FROM users WHERE email = '$email'";
    $resultEmailCheck = mysqli_query($db, $sqlEmailCheck);
    if (mysqli_num_rows($resultEmailCheck) > 0) {

        echo json_encode([
            'status' => 'error',
            'message' => 'El correo electrónico ya está registrado.'
        ]);
        exit;
    }

    $sqlUserNameCheck = "SELECT * FROM users WHERE user_name = '$user_name'";
    $resultUserNameCheck = mysqli_query($db, $sqlUserNameCheck);
    if (mysqli_num_rows($resultUserNameCheck) > 0) {

        echo json_encode([
            'status' => 'error',
            'message' => 'El nombre de usuario ya está en uso.'
        ]);
        exit;
    }

    // Inserción en la base de datos
    $sql = "INSERT INTO users (user_name, name, email, password, start_date_app, avatar) 
            VALUES ('$user_name', '$nombre', '$email', '$secure_password', CURDATE(), '$imagePath')";

    if (mysqli_query($db, $sql)) {

        echo json_encode([
            'status' => 'success',
            'message' => 'Usuario registrado exitosamente.'

        ]); // Devuelve la respuesta en formato JSON
        exit;
    } else {

        echo json_encode([
            'status' => 'error',
            'message' => 'Hubo un error al registrar el usuario.'
        ]);
    }
}
