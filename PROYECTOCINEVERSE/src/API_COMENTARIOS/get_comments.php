<?php
include('../API USUARIO/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id_publicacion'])) {
        $id_publicacion = (int)$_GET['id_publicacion'];

        // Obtener los comentarios de la publicación
        $stmt = $conn->prepare("SELECT Comenta.ID_Comenta, Comenta.Contenido, Comenta.Fecha, Comenta.ID_Usuario, Usuario.Nombre AS Autor
                                FROM Comenta
                                JOIN Usuario ON Comenta.ID_Usuario = Usuario.ID_Usuario
                                WHERE Comenta.ID_Publicacion = ?
                                ORDER BY Comenta.Fecha DESC");
        $stmt->bind_param("i", $id_publicacion);
        $stmt->execute();
        $result = $stmt->get_result();

        $comments = [];
        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }
        
        echo json_encode(["success" => true, "comments" => $comments]);
    } else {
        echo json_encode(["success" => false, "message" => "ID de publicación no especificado."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método de solicitud no permitido."]);
}
?>