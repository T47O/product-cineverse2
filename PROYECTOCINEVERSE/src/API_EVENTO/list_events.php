<?php
include('../API USUARIO/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Consulta para obtener todos los eventos junto con sus detalles y el nombre del usuario que los creó
    $sql = "SELECT Evento.ID_Evento, Publicacion.ID_Publicacion, Publicacion.Titulo, Publicacion.Descripcion, 
            Evento.FechaHora_Realizacion, Evento.Ubicacion, Usuario.Nombre AS NombreUsuario, Publicacion.ID_Usuario
            FROM Evento
            JOIN Publicacion ON Evento.ID_Publicacion = Publicacion.ID_Publicacion
            JOIN Usuario ON Publicacion.ID_Usuario = Usuario.ID_Usuario
            ORDER BY Evento.FechaHora_Realizacion ASC";

    $result = $conn->query($sql);

    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }

    echo json_encode(["success" => true, "events" => $events]);
} else {
    echo json_encode(["success" => false, "message" => "Método de solicitud no permitido."]);
}
?>