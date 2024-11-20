<?php
include('../API USUARIO/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id_usuario'], $_POST['id_publicacion'], $_POST['contenido'])) {
        $id_usuario = (int)$_POST['id_usuario'];
        $id_publicacion = (int)$_POST['id_publicacion'];
        $contenido = $_POST['contenido'];

        // Insertar el comentario en la tabla Comenta
        $stmt = $conn->prepare("INSERT INTO Comenta (Contenido, Fecha, ID_Usuario, ID_Publicacion) VALUES (?, NOW(), ?, ?)");
        $stmt->bind_param("sii", $contenido, $id_usuario, $id_publicacion);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Comentario agregado exitosamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al agregar el comentario."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Faltan datos requeridos."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método de solicitud no permitido."]);
}
?>
