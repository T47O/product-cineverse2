<?php
include('../API USUARIO/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id_comenta'], $_POST['id_usuario'])) {
        $id_comenta = (int)$_POST['id_comenta'];
        $id_usuario = (int)$_POST['id_usuario'];

        // Eliminar el comentario si el usuario es el autor del comentario o un administrador
        $stmt = $conn->prepare("DELETE FROM Comenta WHERE ID_Comenta = ? AND ID_Usuario = ?");
        $stmt->bind_param("ii", $id_comenta, $id_usuario);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Comentario eliminado exitosamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al eliminar el comentario."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Faltan datos requeridos."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método de solicitud no permitido."]);
}
?>