<?php
include('../API USUARIO/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Consulta para obtener todas las comunidades y el nombre del administrador
    $sql = "SELECT Comunidad.ID_comunidad, Comunidad.Titulo, Comunidad.Descripcion, Usuario.Nombre AS Administrador
            FROM Comunidad
            JOIN Usuario ON Comunidad.ID_Administrador = Usuario.ID_Usuario
            ORDER BY Comunidad.Titulo ASC";

    $result = $conn->query($sql);

    $communities = [];
    while ($row = $result->fetch_assoc()) {
        $communities[] = $row;
    }

    echo json_encode(["success" => true, "communities" => $communities]);
} else {
    echo json_encode(["success" => false, "message" => "Método de solicitud no permitido."]);
}
?>
