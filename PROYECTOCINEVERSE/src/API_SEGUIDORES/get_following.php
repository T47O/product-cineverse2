<?php
include('../API USUARIO/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id_usuario'])) {
    $id_usuario = (int)$_GET['id_usuario'];

    // Consulta para obtener la lista de usuarios que el usuario sigue
    $stmt = $conn->prepare("SELECT Usuario.ID_Usuario, Usuario.Nombre 
                            FROM Seguir 
                            JOIN Usuario ON Seguir.Seguido = Usuario.ID_Usuario 
                            WHERE Seguir.Seguidor = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    $following = [];
    while ($row = $result->fetch_assoc()) {
        $following[] = $row;
    }

    echo json_encode(["success" => true, "following" => $following]);
} else {
    echo json_encode(["success" => false, "message" => "Faltan datos requeridos o método de solicitud no permitido."]);
}
?>
