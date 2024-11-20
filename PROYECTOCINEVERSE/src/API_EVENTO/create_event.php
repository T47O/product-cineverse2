<?php
include('../API USUARIO/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar que se han recibido todos los datos requeridos
    if (isset($_POST['titulo'], $_POST['descripcion'], $_POST['id_usuario'], $_POST['fecha_hora'], $_POST['ubicacion']) && isset($_FILES['contenido_multimedia'])) {
        $titulo = $_POST['titulo'];
        $descripcion = $_POST['descripcion'];
        $id_usuario = (int)$_POST['id_usuario'];
        $fecha_hora = $_POST['fecha_hora'];
        $ubicacion = $_POST['ubicacion'];
        
        // Leer el contenido multimedia como un blob
        $contenido_multimedia = file_get_contents($_FILES['contenido_multimedia']['tmp_name']);

        // Iniciar transacción
        $conn->begin_transaction();

        try {
            // 1. Insertar en la tabla Publicacion
            $stmt_publicacion = $conn->prepare("INSERT INTO Publicacion (Titulo, Contenido_Multimedia, Descripcion, FechaHora_Publicacion, ID_Usuario) VALUES (?, ?, ?, NOW(), ?)");
            $stmt_publicacion->bind_param("sbsi", $titulo, $contenido_multimedia, $descripcion, $id_usuario);

            // Enviar contenido multimedia como blob
            $stmt_publicacion->send_long_data(1, $contenido_multimedia);

            if (!$stmt_publicacion->execute()) {
                throw new Exception("Error al crear la publicación: " . $stmt_publicacion->error);
            }

            $id_publicacion = $stmt_publicacion->insert_id;

            // 2. Insertar en la tabla Evento
            $stmt_evento = $conn->prepare("INSERT INTO Evento (ID_Publicacion, FechaHora_Realizacion, Ubicacion) VALUES (?, ?, ?)");
            $stmt_evento->bind_param("iss", $id_publicacion, $fecha_hora, $ubicacion);

            if (!$stmt_evento->execute()) {
                throw new Exception("Error al crear el evento: " . $stmt_evento->error);
            }

            // Si todo salió bien, confirmar la transacción
            $conn->commit();

            echo json_encode(["success" => true, "message" => "Evento creado exitosamente.", "id_evento" => $stmt_evento->insert_id]);
        } catch (Exception $e) {
            // Si algo salió mal, revertir la transacción
            $conn->rollback();
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Faltan datos requeridos."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método de solicitud no permitido."]);
}
?>