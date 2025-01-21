<?php

// Primero, establecer la cabecera para indicar que la respuesta es JSON
header('Content-Type: application/json');

// Crear un arreglo para almacenar los mensajes
$response = [];

// Datos de conexión
$servername = '127.0.0.1';
$user = 'root';
$password = '';
$database = 'todolist';

// Crear la conexión usando MySQLi orientado a objetos
$db = new mysqli($servername, $user, $password, $database);

$databaseResponse[]=[];

// Comprobar si la conexión es exitosa
if ($db->connect_error) {
    // Si la conexión a la base de datos falla, agregar un mensaje de error
    echo json_encode([ 'status' => 'error', 'message' => 'Error al conectar a la base de datos: ' . $db->connect_error ]);
    exit;
} else {

    // Configurar la conexión para que soporte caracteres UTF-8
    $db->set_charset("utf8");
   $databaseResponse= [ 'status' => 'success', 'message' => 'conexión exitosa.' ];
   
}

