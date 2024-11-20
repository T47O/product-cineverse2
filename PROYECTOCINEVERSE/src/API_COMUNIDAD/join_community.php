<?php
include('../API USUARIO/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['id_usuario'], $data['id_comunidad'])) {
        $id_usuario = (int)$data['id_usuario'];
        $id_comunidad = (int)$data['id_comunidad'];

        // Verificar si el usuario ya es miembro de la comunidad
        $stmt_check = $conn->prepare("SELECT * FROM Miembro_Comunidad WHERE ID_Usuario = ? AND ID_Comunidad = ?");
        $stmt_check->bind_param("ii", $id_usuario, $id_comunidad);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            echo json_encode(["success" => false, "message" => "Ya eres miembro de esta comunidad."]);
        } else {
            // Insertar al usuario en la comunidad
            $stmt_join = $conn->prepare("INSERT INTO Miembro_Comunidad (ID_Usuario, ID_Comunidad, Fecha_Unido) VALUES (?, ?, NOW())");
            $stmt_join->bind_param("ii", $id_usuario, $id_comunidad);

            if ($stmt_join->execute()) {
                echo json_encode(["success" => true, "message" => "Te has unido a la comunidad exitosamente."]);
            } else {
                echo json_encode(["success" => false, "message" => "Error al unirse a la comunidad."]);
            }
        }
    } else {
        echo json_encode(["success" => false, "message" => "Faltan datos requeridos."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método de solicitud no permitido."]);
}
?>
