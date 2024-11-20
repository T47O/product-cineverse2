<?php
include('../API USUARIO/config.php');

if (isset($_GET['id_publicacion'])) {
    $id_publicacion = $_GET['id_publicacion'];

    $stmt = $conn->prepare("SELECT Publicacion.ID_Publicacion, Publicacion.Titulo, Publicacion.Descripcion, Publicacion.FechaHora_Publicacion 
                            FROM Publicacion
                            JOIN Post ON Publicacion.ID_Publicacion = Post.ID_Publicacion
                            WHERE Publicacion.ID_Publicacion = ?");
    $stmt->bind_param("i", $id_publicacion);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $post = $result->fetch_assoc();
            echo json_encode(["success" => true, "post" => $post]);
        } else {
            echo json_encode(["success" => false, "message" => "Post no encontrado."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Error al obtener el Post."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "ID de Publicacion no especificado."]);
}
?>
