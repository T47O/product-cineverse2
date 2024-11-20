<?php
include('../bd/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id_comunidad'])) {
    $id_comunidad = (int)$_GET['id_comunidad'];

    // Consulta para obtener los mensajes de la comunidad
    $stmt = $conn->prepare("SELECT Mensaje_Comunidad.ID_MensajeComunidad, Mensaje_Comunidad.Contenido, Mensaje_Comunidad.Fecha_Hora, Usuario.Nombre AS Autor 
                            FROM Mensaje_Comunidad
                            JOIN Usuario ON Mensaje_Comunidad.ID_Usuario = Usuario.ID_Usuario
                            WHERE Mensaje_Comunidad.ID_Comunidad = ?
                            ORDER BY Mensaje_Comunidad.Fecha_Hora ASC");
    $stmt->bind_param("i", $id_comunidad);
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }

    echo json_encode(["success" => true, "messages" => $messages]);
} else {
    echo json_encode(["success" => false, "message" => "Faltan datos requeridos o método de solicitud no permitido."]);
}
?>
