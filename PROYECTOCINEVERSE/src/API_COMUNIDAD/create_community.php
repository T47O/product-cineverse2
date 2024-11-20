<?php
include('../API USUARIO/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    // Verificar que se han recibido todos los datos necesarios
    if (isset($data['titulo'], $data['descripcion'], $data['id_administrador'])) {
        $titulo = $data['titulo'];
        $descripcion = $data['descripcion'];
        $id_administrador = (int)$data['id_administrador'];

        // Insertar en la tabla Comunidad
        $stmt = $conn->prepare("INSERT INTO Comunidad (Titulo, Descripcion, ID_Administrador) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $titulo, $descripcion, $id_administrador);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Comunidad creada exitosamente.", "id_comunidad" => $stmt->insert_id]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al crear la comunidad."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Faltan datos requeridos."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método de solicitud no permitido."]);
}
?>