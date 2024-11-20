<?php
include('../API USUARIO/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id_usuario'])) {
    $id_usuario = (int)$_GET['id_usuario'];

    // Consulta para obtener usuarios con seguimiento mutuo
    $sql = "SELECT u.ID_Usuario, u.Nombre
            FROM Seguir s1
            JOIN Seguir s2 ON s1.Seguidor = s2.Seguido AND s1.Seguido = s2.Seguidor
            JOIN Usuario u ON s1.Seguido = u.ID_Usuario
            WHERE s1.Seguidor = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    $mutual_followers = [];
    while ($row = $result->fetch_assoc()) {
        $mutual_followers[] = $row;
    }

    echo json_encode(["success" => true, "mutual_followers" => $mutual_followers]);
} else {
    echo json_encode(["success" => false, "message" => "Faltan datos requeridos o método de solicitud no permitido."]);
}
?>
