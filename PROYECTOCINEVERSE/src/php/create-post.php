<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Post - CINEVERSE</title>
    <link rel="icon" type="image/png" href="../img/logoCFN.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../html/style.css">
</head>
<body>
<nav class="nav-bar">
        <img src="../img/logoCFN.png" alt="Cineverse Logo" class="logo">
        <div class="nav-right">
            <div class="nav-icons">
            <a href="posts.php"><img src="../img/explore-icon.svg"  class="nav-icon"></a>
                <a href="evento.php"><img src="../img/eventos.svg"  class="nav-icon"></a>
                <a href="communities.php"><img src="../img/comunidad-icon.svg"  class="nav-icon"></a>
                <a href="mensajeria.php"><img src="../img/chat-icon.svg" class="nav-icon"></a>
                <a href="perfil.php"><img src="../img/profile-icon.svg" class="nav-icon"></a>
                <a href="logout.php"><img src="../img/log-icon.svg"  class="nav-icon"></a>
            </div>
        </div>
    </nav>


    <div class="create-post-container">
        <h2>Crear Nuevo Post</h2>
        <form id="createPostForm" enctype="multipart/form-data">
            <input type="text" name="titulo" placeholder="Título" required>
            <textarea name="descripcion" placeholder="Descripción" required></textarea>
            <input type="file" name="contenido" accept="image/*" required>
            <input type="hidden" name="mayor" value="0">
            <button type="submit">Publicar</button>
        </form>
    </div>

    <script>
        document.getElementById('createPostForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('id_usuario', <?php echo $_SESSION['user_id']; ?>);

            // Log form data for debugging
            for (let [key, value] of formData.entries()) {
                console.log(key, value);
            }

            fetch('../API POST/create_post.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Server response:', data);
                if (data.success) {
                    alert('Post creado exitosamente');
                    window.location.href = 'posts.php';
                } else {
                    alert('Error al crear el post: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al crear el post');
            });
        });
    </script>
</body>
</html>