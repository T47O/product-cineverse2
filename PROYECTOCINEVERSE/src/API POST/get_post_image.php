<?php
header('Content-Type: image/jpeg');
include('../API USUARIO/config.php');

if (isset($_GET['id_publicacion'])) {
    $id_publicacion = (int)$_GET['id_publicacion'];

    $stmt = $conn->prepare("SELECT Contenido_Multimedia FROM Publicacion WHERE ID_Publicacion = ?");
    $stmt->bind_param("i", $id_publicacion);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo $row['Contenido_Multimedia'];
    } else {
        // Si no se encuentra la imagen, devolver una imagen por defecto
        readfile('../img/default-img.jpg');
    }
} else {
    // Si no se proporciona ID, devolver una imagen por defecto
    readfile('../img/default-img.jpg');
}
?>