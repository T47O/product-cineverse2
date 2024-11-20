<?php
// Configuración de la base de datos
$hostname = $_ENV['MASTER_HOST']; // Cambiar según tu entorno Docker
$username = $_ENV['MYSQL_USER'];
$password = $_ENV['MYSQL_PASSWORD'];
$database = $_ENV['MYSQL_DATABASE'];

// Establecer conexión a la base de datos
$conn = mysqli_connect($hostname, $username, $password, $database);

// Verificar la conexión
if (!$conn) {
    die('Error de conexión: ' . mysqli_connect_error()); // Solo muestra error en caso de fallo
}
?>
