<?php
include('../API USUARIO/config.php');

// Habilitar la visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Decodificar el JSON recibido
    $data = json_decode(file_get_contents("php://input"), true);

    // Verificar que se ha recibido el ID del evento
    if (isset($data['id_evento'])) {
        $id_evento = (int)$data['id_evento'];

        // Iniciar una transacción
        $conn->begin_transaction();

        try {
            // 1. Obtener el ID de la publicación asociada al evento
            $stmt_get_publicacion = $conn->prepare("SELECT ID_Publicacion FROM Evento WHERE ID_Evento = ?");
            $stmt_get_publicacion->bind_param("i", $id_evento);
            $stmt_get_publicacion->execute();
            $result = $stmt_get_publicacion->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $id_publicacion = $row['ID_Publicacion'];

                // 2. Eliminar los comentarios relacionados con la publicación
                $stmt_delete_comments = $conn->prepare("DELETE FROM Comenta WHERE ID_Publicacion = ?");
                $stmt_delete_comments->bind_param("i", $id_publicacion);
                $stmt_delete_comments->execute();

                // 3. Eliminar los likes relacionados con la publicación
                $stmt_delete_likes = $conn->prepare("DELETE FROM Likea WHERE ID_Publicacion = ?");
                $stmt_delete_likes->bind_param("i", $id_publicacion);
                $stmt_delete_likes->execute();

                // 4. Eliminar el evento de la tabla Evento
                $stmt_delete_evento = $conn->prepare("DELETE FROM Evento WHERE ID_Evento = ?");
                $stmt_delete_evento->bind_param("i", $id_evento);
                $stmt_delete_evento->execute();

                // 5. Eliminar la publicación de la tabla Publicacion
                $stmt_delete_publicacion = $conn->prepare("DELETE FROM Publicacion WHERE ID_Publicacion = ?");
                $stmt_delete_publicacion->bind_param("i", $id_publicacion);
                $stmt_delete_publicacion->execute();

                // Si todo salió bien, confirmar la transacción
                $conn->commit();
                echo json_encode(["success" => true, "message" => "Evento y datos relacionados eliminados exitosamente."]);
            } else {
                throw new Exception("Evento no encontrado.");
            }
        } catch (Exception $e) {
            // Si algo salió mal, revertir la transacción
            $conn->rollback();
            echo json_encode(["success" => false, "message" => "Error al eliminar el evento: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Faltan datos requeridos."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método de solicitud no permitido."]);
}
?>