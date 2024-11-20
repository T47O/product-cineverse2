<?php
include('../bd/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Leer y decodificar JSON
    $data = json_decode(file_get_contents("php://input"), true);

    // Verificar si se han recibido los datos requeridos
    if (isset($data['id_comunidad'], $data['id_usuario'], $data['contenido'])) {
        $id_comunidad = (int)$data['id_comunidad'];
        $id_usuario = (int)$data['id_usuario'];
        $contenido = $data['contenido'];

        // Insertar el mensaje en la tabla Mensaje_Comunidad
        $stmt = $conn->prepare("INSERT INTO Mensaje_Comunidad (ID_Comunidad, ID_Usuario, Contenido, Fecha_Hora) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $id_comunidad, $id_usuario, $contenido);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Mensaje enviado exitosamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al enviar el mensaje."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Faltan datos requeridos."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método de solicitud no permitido."]);
}
?>
