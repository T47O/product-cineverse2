<?php
include('../API USUARIO/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id_usuario'], $_POST['id_publicacion'])) {
        $id_usuario = (int)$_POST['id_usuario'];
        $id_publicacion = (int)$_POST['id_publicacion'];

        // Verificar si el like ya existe
        $check_stmt = $conn->prepare("SELECT * FROM Likea WHERE ID_Usuario = ? AND ID_Publicacion = ?");
        $check_stmt->bind_param("ii", $id_usuario, $id_publicacion);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            echo json_encode(["success" => false, "message" => "Ya le diste like a esta publicación."]);
        } else {
            // Insertar el nuevo like
            $stmt = $conn->prepare("INSERT INTO Likea (ID_Usuario, ID_Publicacion) VALUES (?, ?)");
            $stmt->bind_param("ii", $id_usuario, $id_publicacion);

            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Like agregado exitosamente."]);
            } else {
                echo json_encode(["success" => false, "message" => "Error al dar like."]);
            }
        }
    } else {
        echo json_encode(["success" => false, "message" => "Faltan datos requeridos."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método de solicitud no permitido."]);
}
?>
