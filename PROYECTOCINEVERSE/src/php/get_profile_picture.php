<?php
session_start();
require_once '../API USUARIO/config.php';
require_once '../API USUARIO/usuario.php';

$userId = isset($_GET['id']) ? $_GET['id'] : $_SESSION['user_id'];

$usuarioObj = new Usuario($conn);
$imageData = $usuarioObj->getProfilePicture($userId);

if ($imageData) {
    header("Content-Type: image/jpeg");
    echo $imageData;
} else {
    // Mostrar una imagen por defecto si no hay imagen de perfil
    readfile('../img/default-img.jpg');
}