<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

include('../API USUARIO/config.php');

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['id_emisor'], $data['id_receptor'], $data['contenido'])) {
            $id_emisor = (int)$data['id_emisor'];
            $id_receptor = (int)$data['id_receptor'];
            $contenido = $data['contenido'];

            $stmt = $conn->prepare("INSERT INTO Mensajes (ID_Emisor, ID_Receptor, Contenido) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $id_emisor, $id_receptor, $contenido);

            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Mensaje enviado exitosamente."]);
            } else {
                throw new Exception("Error al enviar el mensaje: " . $stmt->error);
            }
        } else {
            throw new Exception("Faltan datos requeridos.");
        }
    } else {
        throw new Exception("Método de solicitud no permitido.");
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>