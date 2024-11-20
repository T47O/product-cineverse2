<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../html/login.html');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts - CINEVERSE</title>
    <link rel="icon" type="image/png" href="../img/logoCFN.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../html/style.css">
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
            <div class="search-bar">
                <input type="text" placeholder="Buscar..." class="search-input" id="searchInput">
                <img src="../img/search.svg" alt="Buscar" class="search-icon" id="searchButton">
            </div>
        </div>
    </nav>

    <div id="searchResults" class="search-results"></div>

    <div class="explore-container">
        <div class="posts-grid" id="postsContainer">
            <!-- Posts will be loaded here dynamically -->
        </div>
    </div>

    <a href="create-post.php" class="floating-button">
        <i class="fas fa-plus"></i>
    </a>

    <script>
        var userId = <?php echo json_encode($_SESSION['user_id']); ?>;
    </script>
    <script src="../js/posts.js"></script>
</body>
</html>