<?php
include('../API USUARIO/config.php');
header('Content-Type: application/json');

session_start();
$user_id = $_SESSION['user_id'] ?? null;
$community_id = $_GET['community_id'] ?? null;

if (!$user_id || !$community_id) {
    echo json_encode(['success' => false, 'error' => 'Usuario o comunidad no especificados']);
    exit;
}

// Verificar si el usuario pertenece a la comunidad
$stmt = $conn->prepare("SELECT 1 FROM Miembro_Comunidad WHERE ID_Usuario = ? AND ID_Comunidad = ? UNION SELECT 1 FROM Comunidad WHERE ID_Administrador = ? AND ID_comunidad = ?");
$stmt->bind_param("iiii", $user_id, $community_id, $user_id, $community_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode(['success' => false, 'error' => 'Usuario no autorizado para esta comunidad']);
    exit;
}

// Consultar los mensajes de la comunidad
$sql = "SELECT mc.ID_MensajeComunidad, mc.Contenido, mc.Fecha_Hora, mc.ID_Usuario, u.Nombre AS Usuario 
        FROM Mensaje_Comunidad mc
        JOIN Usuario u ON mc.ID_Usuario = u.ID_Usuario
        WHERE mc.ID_Comunidad = ?
        ORDER BY mc.Fecha_Hora ASC";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Error en la preparación de la consulta: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $community_id);
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode(['success' => true, 'messages' => $messages]);
?>