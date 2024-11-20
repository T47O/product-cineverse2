<?php
include('../API USUARIO/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id_publicacion'])) {
        $id_publicacion = (int)$_GET['id_publicacion'];

        // Contar el número de likes para la publicación
        $stmt = $conn->prepare("SELECT COUNT(*) AS Total_Likes FROM Likea WHERE ID_Publicacion = ?");
        $stmt->bind_param("i", $id_publicacion);
        $stmt->execute();
        $result = $stmt->get_result();
        $total_likes = $result->fetch_assoc()['Total_Likes'];

        echo json_encode(["success" => true, "total_likes" => $total_likes]);
    } else {
        echo json_encode(["success" => false, "message" => "ID de publicación no especificado."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método de solicitud no permitido."]);
}
?>