<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);
require_once 'config.php';
require_once 'usuario.php';

header('Content-Type: application/json');

function logError($message) {
    file_put_contents('debug.log', date('Y-m-d H:i:s') . " - " . $message . "\n", FILE_APPEND);
}

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Usuario no autenticado');
    }

    if (!isset($_FILES['Foto_perfil'])) {
        throw new Exception('No se ha enviado ninguna imagen');
    }

    $usuarioObj = new Usuario($conn);
    $userId = $_SESSION['user_id'];

    $imageData = file_get_contents($_FILES['Foto_perfil']['tmp_name']);
    if ($imageData === false) {
        throw new Exception('Error al leer el archivo de imagen');
    }

    logError("Attempting to upload image for user: " . $userId);

    $result = $usuarioObj->updateProfilePicture($userId, $imageData);
    if (!$result) {
        throw new Exception('Error al actualizar la imagen en la base de datos');
    }

    echo json_encode(['success' => true, 'message' => 'Imagen de perfil actualizada con éxito']);
} catch (Exception $e) {
    logError("Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}