<?php
include '../API USUARIO/config.php';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id']);

    if ($action === 'list') {
        $query = "SELECT * FROM Usuario WHERE ID_Usuario = $id";
        $result = mysqli_query($conn, $query);
        $user = mysqli_fetch_assoc($result);
        echo "<pre>" . print_r($user, true) . "</pre>";
    } elseif ($action === 'delete') {
        $deletePosts = "DELETE FROM Publicacion WHERE ID_Usuario = $id";
        $deleteLikes = "DELETE FROM Likea WHERE ID_Usuario = $id";
        $deleteComments = "DELETE FROM Comenta WHERE ID_Usuario = $id";
        $deleteUser = "DELETE FROM Usuario WHERE ID_Usuario = $id";

        mysqli_query($conn, $deletePosts);
        mysqli_query($conn, $deleteLikes);
        mysqli_query($conn, $deleteComments);
        mysqli_query($conn, $deleteUser);

        echo "Usuario y sus dependencias eliminadas.";
    }
}
?>
