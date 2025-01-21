<?php 
if (isset($_SESSION['user_id'])) {
    // El usuario está autenticado
    echo json_encode([
        'status' => 'success',
        'message' => 'Usuario autenticado',
        'user_id' => $_SESSION['user_id'],
        'user_name' => $_SESSION['user_name']
    ]);
} else {
    // El usuario no está autenticado
    echo json_encode(['status' => 'error', 'message' => 'No autenticado']);
}



?>