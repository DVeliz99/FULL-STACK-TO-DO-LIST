<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json'); // para asegurarse de que la respuesta sea JSON

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '/Users/Dario/Desktop/Desarrollador_FULL-STACK/TO-DO-LIST/php_backend/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_POST['user_id']) ? mysqli_real_escape_string($db, $_POST['user_id']) : false;
    
    if ($user_id && isset($_FILES['new_avatar']) && $_FILES['new_avatar']['error'] === UPLOAD_ERR_OK) {
        $newAvatar = $_FILES['new_avatar'];

        $query_avatarPath = "SELECT avatar FROM users WHERE id_user = '$user_id';";
        $result_avatarPath = mysqli_query($db, $query_avatarPath);

        if ($result_avatarPath && mysqli_num_rows($result_avatarPath) > 0) {
            $row_avatar = mysqli_fetch_assoc($result_avatarPath);
            $avatar = $row_avatar['avatar'];

            $pathtoDelete = __DIR__ . '/../' . $avatar; // para asegurarse de que la ruta completa sea correcta

            if (file_exists($pathtoDelete)) {
                if (unlink($pathtoDelete)) {
                    // Eliminar con éxito
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'No se pudo eliminar la imagen.']);
                    exit;
                }
            }

            $uploadDir = __DIR__ . '/../uploads/'; // ../ subir de nivel para acceder a la carpeta uploads
            $newavatarName = basename($newAvatar['name']); // Sanear el nombre del archivo
            $uploadFile = $uploadDir . $newavatarName; // Ruta completa del archivo

            // para asegurarse de que el directorio de destino exista
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true); // Crear directorios si no existen
            }

            // Mover el archivo al directorio de uploads
            if (move_uploaded_file($newAvatar['tmp_name'], $uploadFile)) {
                $imagePath = '/uploads/' . $newavatarName; // Guardar la ruta relativa para la base de datos

                // Inserción en la base de datos
                $sql = "UPDATE users SET avatar = '$imagePath' WHERE id_user = '$user_id';";
                $result_query = mysqli_query($db, $sql);

                if (mysqli_affected_rows($db) > 0) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Imagen de perfil actualizada con éxito',
                        'avatarPath' => $imagePath
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'No se pudo actualizar la base de datos.'
                    ]);
                }
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Hubo un error al mover la imagen al servidor.'
                ]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Faltan datos o archivo no válido.']);
    }
    exit;
}
?>
