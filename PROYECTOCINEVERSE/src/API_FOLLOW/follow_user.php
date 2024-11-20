<?php
include('../API USUARIO/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Leer y decodificar JSON
    $data = json_decode(file_get_contents("php://input"), true);

    // Verificar si se han recibido los datos requeridos
    if (isset($data['seguidor'], $data['seguido'])) {
        $seguidor = (int)$data['seguidor'];
        $seguido = (int)$data['seguido'];

        // Verificar si el usuario ya está siguiendo al otro usuario
        $stmt_check = $conn->prepare("SELECT * FROM Seguir WHERE Seguidor = ? AND Seguido = ?");
        $stmt_check->bind_param("ii", $seguidor, $seguido);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            echo json_encode(["success" => false, "message" => "Ya estás siguiendo a este usuario."]);
        } else {
            // Insertar la relación de seguimiento
            $stmt_follow = $conn->prepare("INSERT INTO Seguir (Seguidor, Seguido) VALUES (?, ?)");
            $stmt_follow->bind_param("ii", $seguidor, $seguido);

            if ($stmt_follow->execute()) {
                echo json_encode(["success" => true, "message" => "Has comenzado a seguir a este usuario."]);
            } else {
                echo json_encode(["success" => false, "message" => "Error al seguir al usuario."]);
            }
        }
    } else {
        echo json_encode(["success" => false, "message" => "Faltan datos requeridos."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método de solicitud no permitido."]);
}
?>
