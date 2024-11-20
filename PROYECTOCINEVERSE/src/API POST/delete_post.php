<?php
include('../API USUARIO/config.php');

// Habilitar la visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id_publicacion'])) {
        $id_publicacion = $_POST['id_publicacion'];

        // Iniciar una transacción
        $conn->begin_transaction();

        try {
            // Primero, eliminar los comentarios relacionados
            $stmt_comments = $conn->prepare("DELETE FROM Comenta WHERE ID_Publicacion = ?");
            $stmt_comments->bind_param("i", $id_publicacion);
            $stmt_comments->execute();

            // Luego, eliminar de la tabla Likea
            $stmt_like = $conn->prepare("DELETE FROM Likea WHERE ID_Publicacion = ?");
            $stmt_like->bind_param("i", $id_publicacion);
            $stmt_like->execute();

            // Después, eliminar de la tabla Post
            $stmt_post = $conn->prepare("DELETE FROM Post WHERE ID_Publicacion = ?");
            $stmt_post->bind_param("i", $id_publicacion);
            $stmt_post->execute();

            // Finalmente, eliminar de la tabla Publicacion
            $stmt_publicacion = $conn->prepare("DELETE FROM Publicacion WHERE ID_Publicacion = ?");
            $stmt_publicacion->bind_param("i", $id_publicacion);
            $stmt_publicacion->execute();

            // Si todo salió bien, confirmar la transacción
            $conn->commit();
            echo json_encode(["success" => true, "message" => "Post y comentarios relacionados eliminados exitosamente."]);
        } catch (Exception $e) {
            // Si algo salió mal, revertir la transacción
            $conn->rollback();
            echo json_encode(["success" => false, "message" => "Error al eliminar la publicación: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "ID de Publicacion no especificado."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método de solicitud no permitido."]);
}
?>