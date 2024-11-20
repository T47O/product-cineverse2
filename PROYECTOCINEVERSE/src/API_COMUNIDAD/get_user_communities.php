<?php
include('../API USUARIO/config.php');

$user_id = $_GET['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['error' => 'User ID is required']);
    exit;
}

$response = [];

// Comunidades de las que el usuario es miembro o administrador
$sql_member = "SELECT DISTINCT c.* FROM Comunidad c 
               LEFT JOIN Miembro_Comunidad uc ON c.ID_comunidad = uc.ID_Comunidad 
               WHERE uc.ID_Usuario = ? OR c.ID_Administrador = ?";
$stmt = $conn->prepare($sql_member);

if (!$stmt) {
    die("Error en la consulta de comunidades de las que es miembro o administrador: " . $conn->error);
}

$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$member_result = $stmt->get_result();
$response['member_communities'] = $member_result->fetch_all(MYSQLI_ASSOC);

// Comunidades de las que el usuario no es miembro ni administrador
$sql_non_member = "SELECT * FROM Comunidad c 
                   WHERE c.ID_comunidad NOT IN (
                       SELECT ID_Comunidad FROM Miembro_Comunidad WHERE ID_Usuario = ?
                   ) AND c.ID_Administrador != ?";
$stmt = $conn->prepare($sql_non_member);

if (!$stmt) {
    die("Error en la consulta de comunidades de las que no es miembro ni administrador: " . $conn->error);
}

$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$non_member_result = $stmt->get_result();
$response['non_member_communities'] = $non_member_result->fetch_all(MYSQLI_ASSOC);

// Enviar la respuesta en formato JSON
echo json_encode($response);
?>