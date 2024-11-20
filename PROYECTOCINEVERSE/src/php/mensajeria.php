<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirige al login si no hay sesión
    exit();
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajería - Cineverse</title>
    <link rel="icon" type="image/png" href="../img/logoCFN.png">
    <link rel="stylesheet" href="../html/message.css">
    <script src="../js/search.js" defer></script>
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

    <div class="messaging-container">
        <div class="followers-list">
            <h2>Chats</h2>
            <ul id="mutualFollowersList"></ul>
        </div>
        <div class="chat-area">
            <div id="chatHeader"></div>
            <div id="messagesList"></div>
            <div class="message-input">
                <input type="text" id="messageInput" placeholder="Escribe un mensaje...">
                <button id="sendButton">Enviar</button>
            </div>
        </div>
    </div>

    <script>
        // Pasamos el ID del usuario a JavaScript
        const currentUserId = <?php echo $user_id; ?>;
    </script>
    <script src="../js/messaging.js"></script>
</body>
</html>