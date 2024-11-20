<?php
include('../API USUARIO/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['id_comunidad'], $data['titulo'], $data['descripcion'])) {
        $id_comunidad = (int)$data['id_comunidad'];
        $titulo = $data['titulo'];
        $descripcion = $data['descripcion'];

        // Actualizar la comunidad en la base de datos
        $stmt = $conn->prepare("UPDATE Comunidad SET Titulo = ?, Descripcion = ? WHERE ID_comunidad = ?");
        $stmt->bind_param("ssi", $titulo, $descripcion, $id_comunidad);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Comunidad actualizada exitosamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al actualizar la comunidad."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Faltan datos requeridos."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método de solicitud no permitido."]);
}
?>
