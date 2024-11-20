<?php
include('../API USUARIO/config.php');

header('Content-Type: application/json');

$user_id = $_POST['user_id'] ?? null;
$community_id = $_POST['community_id'] ?? null;
$content = $_POST['content'] ?? null;

if (!$user_id || !$community_id || !$content) {
    echo json_encode(['success' => false, 'error' => 'Todos los campos son obligatorios']);
    exit;
}

$sql = "INSERT INTO Mensaje_Comunidad (ID_Comunidad, ID_Usuario, Contenido, Fecha_Hora) VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Error en la preparación de la consulta: ' . $conn->error]);
    exit;
}

$stmt->bind_param("iis", $community_id, $user_id, $content);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Mensaje enviado con éxito']);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al enviar el mensaje']);
}
?>