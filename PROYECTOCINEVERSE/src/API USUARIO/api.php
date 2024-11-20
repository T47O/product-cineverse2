<?php
require_once 'config.php';
require_once 'usuario.php';

header('Content-Type: application/json');

try {
    $usuarioObj = new Usuario($conn);

    $method = $_SERVER['REQUEST_METHOD'];
    $endpoint = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';

    switch ($method) {
        case 'GET':
            if (preg_match('/^\/usuarios\/(\d+)$/', $endpoint, $matches)) {
                $usuarioId = $matches[1];
                $usuario = $usuarioObj->getUsuarioById($usuarioId);
                if ($usuario) {
                    echo json_encode(['success' => true, 'user' => $usuario]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
                }
            } elseif (preg_match('/^\/usuarios\/search$/', $endpoint)) {
                $searchTerm = $_GET['term'] ?? '';
                $user = $usuarioObj->searchUserById($searchTerm);
                if ($user) {
                    echo json_encode(['success' => true, 'user' => $user]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
                }
            } else {
                throw new Exception('Endpoint no válido');
            }
            break;

        case 'POST':
            if ($endpoint === '/usuarios') {
                $data = json_decode(file_get_contents('php://input'), true);
                $result = $usuarioObj->createUsuario($data);
                if ($result === true) {
                    echo json_encode(['success' => true, 'message' => 'Usuario creado con éxito']);
                } else {
                    throw new Exception($result);
                }
            } elseif ($endpoint === '/login') {
                $data = json_decode(file_get_contents('php://input'), true);
                $result = $usuarioObj->login($data['email'], $data['password']);
                if ($result) {
                    session_start();
                    $_SESSION['user_id'] = $result['ID_Usuario'];
                    echo json_encode(['success' => true, 'user_id' => $result['ID_Usuario']]);
                } else {
                    throw new Exception('Credenciales inválidas');
                }
            } elseif (preg_match('/^\/usuarios\/(\d+)$/', $endpoint, $matches)) {
                $usuarioId = $matches[1];
                $data = $_POST;
                $foto_perfil = isset($_FILES['Foto_perfil']) ? $_FILES['Foto_perfil'] : null;
                $result = $usuarioObj->updateUsuario($usuarioId, $data, $foto_perfil);
                if ($result) {
                    echo json_encode(['success' => true]);
                } else {
                    throw new Exception('Error al actualizar el usuario');
                }
            } else {
                throw new Exception('Endpoint no válido');
            }
            break;

        case 'PUT':
            if (preg_match('/^\/usuarios\/(\d+)$/', $endpoint, $matches)) {
                $usuarioId = $matches[1];
                $data = json_decode(file_get_contents('php://input'), true);
                $result = $usuarioObj->updateUsuario($usuarioId, $data);
                echo json_encode(['success' => $result]);
            } else {
                echo json_encode(['error' => 'Endpoint no válido']);
            }
            break;
    
        case 'DELETE':
            if (preg_match('/^\/usuarios\/(\d+)$/', $endpoint, $matches)) {
                $usuarioId = $matches[1];
                $result = $usuarioObj->deleteUsuario($usuarioId);
                echo json_encode(['success' => $result]);
            } else {
                echo json_encode(['error' => 'Endpoint no válido']);
            }
            break;

        default:
            throw new Exception('Método no permitido');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>