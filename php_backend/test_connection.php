<?php
require 'vendor/autoload.php'; // Si estás usando Composer para cargar las variables de entorno

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = $_ENV['DB_HOST'];
$user = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];
$dbname = $_ENV['DB_NAME'];

// Crear la conexión
$connection = new mysqli($host, $user, $password, $dbname);

// Comprobar si la conexión fue exitosa
if ($connection->connect_error) {
    // Si hay un error en la conexión
    echo json_encode([
        'status' => 'error',
        'message' => 'Error de conexión: ' . $connection->connect_error
    ]);
    exit; // Detener la ejecución si hay un error
} else {
    // Si la conexión fue exitosa
    echo json_encode([
        'status' => 'success',
        'message' => 'Conexión exitosa a la base de datos'
    ]);
}
?>
