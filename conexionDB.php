<?php

$host = 'localhost';
$user = 'root';
$password = '12345678';
$database = 'ventas_servicios';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
hjgjghf
date_default_timezone_set('America/Bogota');
?>
