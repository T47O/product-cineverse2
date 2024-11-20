<?php
header('Content-Type: application/json');
include('../API USUARIO/config.php');

if (!$conn) {
    echo json_encode([
        "success" => false,
        "message" => "Error de conexión a la base de datos"
    ]);
    exit;
}

$sql = "SELECT p.ID_Publicacion, p.Titulo, p.Descripcion, p.FechaHora_Publicacion, p.ID_Usuario, u.Nombre as NombreUsuario 
        FROM Publicacion p
        JOIN Post po ON p.ID_Publicacion = po.ID_Publicacion
        JOIN Usuario u ON p.ID_Usuario = u.ID_Usuario
        ORDER BY p.FechaHora_Publicacion DESC";

$result = $conn->query($sql);

if ($result === false) {
    echo json_encode([
        "success" => false,
        "message" => "Error en la consulta: " . $conn->error
    ]);
    exit;
}

$posts = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    echo json_encode([
        "success" => true,
        "posts" => $posts
    ]);
} else {
    echo json_encode([
        "success" => true,
        "posts" => []
    ]);
}
?>