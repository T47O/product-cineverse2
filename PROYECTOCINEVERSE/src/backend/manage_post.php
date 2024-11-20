<?php
include '../API USUARIO/config.php'; // Configuración de la base de datos

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id']);

    if ($action === 'list') {
        $query = "SELECT * FROM Publicacion WHERE ID_Publicacion = $id";
        $result = mysqli_query($conn, $query);
        $post = mysqli_fetch_assoc($result);
        echo "<pre>" . print_r($post, true) . "</pre>";
    } elseif ($action === 'delete') {
        $deleteLikes = "DELETE FROM Likea WHERE ID_Publicacion = $id";
        $deleteComments = "DELETE FROM Comenta WHERE ID_Publicacion = $id";
        $deletePost = "DELETE FROM Publicacion WHERE ID_Publicacion = $id";

        mysqli_query($conn, $deleteLikes);
        mysqli_query($conn, $deleteComments);
        mysqli_query($conn, $deletePost);

        echo "Publicación y sus dependencias eliminadas.";
    }
}
?>
