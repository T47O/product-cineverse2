<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    include('../API USUARIO/config.php');

    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        throw new Exception("Método de solicitud no permitido.");
    }

    if (!isset($_GET['id_emisor']) || !isset($_GET['id_receptor'])) {
        throw new Exception("Faltan parámetros requeridos.");
    }

    $id_emisor = filter_var($_GET['id_emisor'], FILTER_VALIDATE_INT);
    $id_receptor = filter_var($_GET['id_receptor'], FILTER_VALIDATE_INT);
    $last_id = isset($_GET['last_id']) ? filter_var($_GET['last_id'], FILTER_VALIDATE_INT) : 0;

    if ($id_emisor === false || $id_receptor === false) {
        throw new Exception("ID de usuario inválido.");
    }

    $stmt = $conn->prepare("SELECT * FROM Mensajes 
                            WHERE ((ID_Emisor = ? AND ID_Receptor = ?)
                               OR (ID_Emisor = ? AND ID_Receptor = ?))
                               AND ID_Mensaje > ?
                            ORDER BY Fecha_Hora ASC");

    if (!$stmt) {
        throw new Exception("Error en la preparación de la consulta: " . $conn->error);
    }

    $stmt->bind_param("iiiii", $id_emisor, $id_receptor, $id_receptor, $id_emisor, $last_id);

    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $messages = [];

    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }

    echo json_encode(["success" => true, "messages" => $messages]);

} catch (Exception $e) {
    error_log("Error en get_messages.php: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Error del servidor: " . $e->getMessage()]);
}
?>