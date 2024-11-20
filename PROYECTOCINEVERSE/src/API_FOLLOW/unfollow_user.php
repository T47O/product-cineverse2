<?php
include('../API USUARIO/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Leer y decodificar JSON
    $data = json_decode(file_get_contents("php://input"), true);

    // Verificar si se han recibido los datos requeridos
    if (isset($data['seguidor'], $data['seguido'])) {
        $seguidor = (int)$data['seguidor'];
        $seguido = (int)$data['seguido'];

        // Verificar si el usuario está siguiendo al otro usuario
        $stmt_check = $conn->prepare("SELECT * FROM Seguir WHERE Seguidor = ? AND Seguido = ?");
        $stmt_check->bind_param("ii", $seguidor, $seguido);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            // Eliminar la relación de seguimiento
            $stmt_unfollow = $conn->prepare("DELETE FROM Seguir WHERE Seguidor = ? AND Seguido = ?");
            $stmt_unfollow->bind_param("ii", $seguidor, $seguido);

            if ($stmt_unfollow->execute()) {
                echo json_encode(["success" => true, "message" => "Has dejado de seguir a este usuario."]);
            } else {
                echo json_encode(["success" => false, "message" => "Error al dejar de seguir al usuario."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "No estás siguiendo a este usuario."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Faltan datos requeridos."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método de solicitud no permitido."]);
}
?>
