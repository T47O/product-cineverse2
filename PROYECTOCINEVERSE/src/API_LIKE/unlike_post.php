<?php
include('../API USUARIO/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id_usuario'], $_POST['id_publicacion'])) {
        $id_usuario = (int)$_POST['id_usuario'];
        $id_publicacion = (int)$_POST['id_publicacion'];

        // Eliminar el like
        $stmt = $conn->prepare("DELETE FROM Likea WHERE ID_Usuario = ? AND ID_Publicacion = ?");
        $stmt->bind_param("ii", $id_usuario, $id_publicacion);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Like eliminado exitosamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al eliminar el like."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Faltan datos requeridos."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método de solicitud no permitido."]);
}
?>
