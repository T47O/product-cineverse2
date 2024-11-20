<?php
include('../API USUARIO/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['id_usuario'], $data['id_publicacion'])) {
        $id_usuario = (int)$data['id_usuario'];
        $id_publicacion = (int)$data['id_publicacion'];

        // Verificar si el like ya existe
        $check_stmt = $conn->prepare("SELECT * FROM Likea WHERE ID_Usuario = ? AND ID_Publicacion = ?");
        $check_stmt->bind_param("ii", $id_usuario, $id_publicacion);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            // Si el like existe, lo eliminamos
            $stmt = $conn->prepare("DELETE FROM Likea WHERE ID_Usuario = ? AND ID_Publicacion = ?");
        } else {
            // Si el like no existe, lo añadimos
            $stmt = $conn->prepare("INSERT INTO Likea (ID_Usuario, ID_Publicacion) VALUES (?, ?)");
        }

        $stmt->bind_param("ii", $id_usuario, $id_publicacion);

        if ($stmt->execute()) {
            // Contar el número total de likes para esta publicación
            $count_stmt = $conn->prepare("SELECT COUNT(*) AS total_likes FROM Likea WHERE ID_Publicacion = ?");
            $count_stmt->bind_param("i", $id_publicacion);
            $count_stmt->execute();
            $count_result = $count_stmt->get_result();
            $total_likes = $count_result->fetch_assoc()['total_likes'];

            echo json_encode(["success" => true, "likes_count" => $total_likes]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al actualizar el like."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Faltan datos requeridos."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método de solicitud no permitido."]);
}
?>