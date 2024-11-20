<?php
include('../API USUARIO/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['id_usuario'], $data['id_comunidad'])) {
        $id_usuario = (int)$data['id_usuario'];
        $id_comunidad = (int)$data['id_comunidad'];

        // Verificar si el usuario es miembro de la comunidad
        $stmt_check = $conn->prepare("SELECT * FROM Miembro_Comunidad WHERE ID_Usuario = ? AND ID_Comunidad = ?");
        $stmt_check->bind_param("ii", $id_usuario, $id_comunidad);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            // Eliminar al usuario de la comunidad
            $stmt_leave = $conn->prepare("DELETE FROM Miembro_Comunidad WHERE ID_Usuario = ? AND ID_Comunidad = ?");
            $stmt_leave->bind_param("ii", $id_usuario, $id_comunidad);

            if ($stmt_leave->execute()) {
                echo json_encode(["success" => true, "message" => "Has salido de la comunidad exitosamente."]);
            } else {
                echo json_encode(["success" => false, "message" => "Error al salir de la comunidad."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "No eres miembro de esta comunidad."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Faltan datos requeridos."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método de solicitud no permitido."]);
}
?>
