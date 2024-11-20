<?php
include('../API USUARIO/config.php');

$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($data['id_mensaje'], $data['id_usuario'])) {
        $id_mensaje = (int)$data['id_mensaje'];
        $id_usuario = (int)$data['id_usuario'];

        $stmt = $conn->prepare("DELETE FROM Mensajes WHERE ID_Mensaje = ? AND ID_Emisor = ?");
        $stmt->bind_param("ii", $id_mensaje, $id_usuario);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Mensaje eliminado exitosamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al eliminar el mensaje."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Faltan datos requeridos."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método de solicitud no permitido."]);
}
?>