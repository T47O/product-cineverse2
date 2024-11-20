<?php
include('../API USUARIO/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id_usuario'])) {
    $id_usuario = (int)$_GET['id_usuario'];

    // Consulta para obtener los posts del usuario
    $sql_posts = "SELECT ID_Publicacion, Titulo, Descripcion, FechaHora_Publicacion, 'post' AS tipo
                  FROM Publicacion
                  WHERE ID_Usuario = ? AND ID_Publicacion NOT IN (SELECT ID_Publicacion FROM Evento)";
    
    $stmt_posts = $conn->prepare($sql_posts);
    $stmt_posts->bind_param("i", $id_usuario);
    $stmt_posts->execute();
    $result_posts = $stmt_posts->get_result();

    $posts = [];
    while ($row = $result_posts->fetch_assoc()) {
        $posts[] = $row;
    }

    // Consulta para obtener los eventos del usuario
    $sql_events = "SELECT Publicacion.ID_Publicacion, Publicacion.Titulo, Publicacion.Descripcion, Publicacion.FechaHora_Publicacion, Evento.FechaHora_Realizacion, Evento.Ubicacion, 'evento' AS tipo
                   FROM Publicacion
                   JOIN Evento ON Publicacion.ID_Publicacion = Evento.ID_Publicacion
                   WHERE Publicacion.ID_Usuario = ?";
    
    $stmt_events = $conn->prepare($sql_events);
    $stmt_events->bind_param("i", $id_usuario);
    $stmt_events->execute();
    $result_events = $stmt_events->get_result();

    $events = [];
    while ($row = $result_events->fetch_assoc()) {
        $events[] = $row;
    }

    // Combinar posts y eventos en un solo arreglo
    $user_content = array_merge($posts, $events);

    echo json_encode(["success" => true, "content" => $user_content]);
} else {
    echo json_encode(["success" => false, "message" => "Faltan datos requeridos o método de solicitud no permitido."]);
}
?>
