<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Comunidades</title>
    <link rel="stylesheet" href="../html/style.css">
    <link rel="icon" type="image/png" href="../img/logoCFN.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="../js/search.js"></script>
</head>
<body data-user-id="<?php echo $user_id; ?>">
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

    <div class="container">
        <h1 class="profile-name">Mis Comunidades</h1>
        
        <!-- Botón para crear comunidad -->
        <button id="createCommunityBtn" class="settings-button">Crear Comunidad</button>

        <!-- Modal para crear comunidad -->
        <div id="createCommunityModal" class="settings-menu">
            <h2>Crear Nueva Comunidad</h2>
            <form id="createCommunityForm">
                <input type="text" id="communityTitle" name="title" placeholder="Título de la Comunidad" required>
                <textarea id="communityDescription" name="description" placeholder="Descripción de la Comunidad" required></textarea>
                <button type="submit" class="settings-button">Crear Comunidad</button>
                <button type="button" class="settings-button" onclick="closeCreateModal()">Cancelar</button>
            </form>
        </div>
        
        <!-- Sección Mis Comunidades -->
        <div class="explore-container">
            <h2 class="profile-username">Mis Comunidades</h2>
            <div class="posts-grid" id="member-communities"></div>
        </div>

        <!-- Sección Comunidades Recomendadas -->
        <div class="explore-container">
            <h2 class="profile-username">Comunidades Recomendadas</h2>
            <div class="posts-grid" id="non-member-communities"></div>
        </div>

        <!-- Modal para Editar Comunidad -->
        <div id="editModal" class="settings-menu">
            <h2>Editar Comunidad</h2>
            <form id="editCommunityForm">
                <input type="hidden" id="communityId" name="communityId">
                <input type="text" id="editTitle" name="title" placeholder="Título" required>
                <textarea id="editDescription" name="description" placeholder="Descripción" required></textarea>
                <button type="submit">Guardar Cambios</button>
                <button type="button" onclick="closeEditModal()">Cancelar</button>
            </form>
        </div>
    </div>
    <script src="../js/communities.js"></script>
</body>
</html>