<?php
include('../API USUARIO/config.php');

// Ensure proper error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set JSON header
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id_comunidad'])) {
        $id_comunidad = (int)$_GET['id_comunidad'];

        // Simplified query to avoid UNION and complex joins
        $sql = "SELECT DISTINCT 
                    u.ID_Usuario, 
                    u.Nombre, 
                    u.Email,
                    CASE 
                        WHEN c.ID_Administrador = u.ID_Usuario THEN 'admin'
                        ELSE 'member'
                    END AS rol
                FROM Usuario u
                LEFT JOIN Miembro_Comunidad mc ON u.ID_Usuario = mc.ID_Usuario
                LEFT JOIN Comunidad c ON (c.ID_comunidad = mc.ID_Comunidad OR c.ID_Administrador = u.ID_Usuario)
                WHERE (mc.ID_Comunidad = ? OR c.ID_comunidad = ?) AND c.ID_comunidad = ?
                ORDER BY rol DESC, u.Nombre ASC";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("iii", $id_comunidad, $id_comunidad, $id_comunidad);
            
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $members = [];
                
                while ($row = $result->fetch_assoc()) {
                    $members[] = $row;
                }
                
                echo json_encode([
                    "success" => true, 
                    "members" => $members
                ]);
            } else {
                throw new Exception("Error executing query: " . $stmt->error);
            }
            
            $stmt->close();
        } else {
            throw new Exception("Error preparing statement: " . $conn->error);
        }
    } else {
        throw new Exception("Invalid request method or missing community ID");
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "Error processing request",
        "debug" => $e->getMessage()
    ]);
}

$conn->close();
?>