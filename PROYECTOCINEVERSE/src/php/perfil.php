<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../html/login.html');
    exit();
}
require_once '../API USUARIO/config.php';
require_once '../API USUARIO/usuario.php';
$usuarioObj = new Usuario($conn);

// Determinar si estamos viendo el perfil propio o de otro usuario
$profileId = isset($_GET['id']) ? $_GET['id'] : $_SESSION['user_id'];
$usuario = $usuarioObj->getUsuarioById($profileId);

if (!$usuario) {
    // Si no se encuentra el usuario, redirigir al perfil propio
    header('Location: perfil.php');
    exit();
}

// Verificar si el usuario actual está siguiendo al usuario del perfil
$isOwnProfile = ($profileId == $_SESSION['user_id']);
$isFollowing = false;
if (!$isOwnProfile) {
    $stmt = $conn->prepare("SELECT * FROM Seguir WHERE Seguidor = ? AND Seguido = ?");
    $stmt->bind_param("ii", $_SESSION['user_id'], $profileId);
    $stmt->execute();
    $result = $stmt->get_result();
    $isFollowing = $result->num_rows > 0;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario - CINEVERSE</title>
    <link rel="icon" type="image/png" href="../img/logoCFN.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../html/style.css">
    <script src="../js/search.js" defer></script>
    <script>var userId = <?php echo json_encode($_SESSION['user_id']); ?>;</script>
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

    <div class="profile-header">
        <img src="../img/background-Posts.jpg" alt="Banner de perfil" class="profile-banner">
        <div class="profile-picture-container">
            <img src="get_profile_picture.php?id=<?php echo $profileId; ?>" alt="Foto de perfil" class="profile-picture" id="profilePicture">
        </div>
    </div>

    <div class="profile-info">
        <h1 class="profile-name"><?php echo htmlspecialchars($usuario['Nombre']); ?></h1>
        <p class="profile-username">@<?php echo htmlspecialchars($usuario['ID_Usuario']); ?></p>
        <?php if (!empty($usuario['Descripcion'])): ?>
            <p class="profile-description"><?php echo htmlspecialchars($usuario['Descripcion']); ?></p>
        <?php endif; ?>
        <div class="profile-stats">
            <div class="stat">
                <div class="stat-value" id="followersCount">Cargando...</div>
                <div class="stat-label">Seguidores</div>
            </div>
            <div class="stat">
                <div class="stat-value" id="followingCount">Cargando...</div>
                <div class="stat-label">Siguiendo</div>
            </div>
        </div>
        <?php if ($isOwnProfile): ?>
            <button class="settings-button" id="settingsButton">
                <i class="fas fa-cog"></i> Ajustes
            </button>
        <?php else: ?>
            <button class="follow-button" id="followButton">
                <?php echo $isFollowing ? 'Dejar de seguir' : 'Seguir'; ?>
            </button>
        <?php endif; ?>
        <?php if ($isOwnProfile): ?>
            <div class="settings-menu" id="settingsMenu">
                <form id="settingsForm" enctype="multipart/form-data">
                    <input type="text" id="newName" name="Nombre" placeholder="Nuevo nombre" value="<?php echo htmlspecialchars($usuario['Nombre']); ?>" required>
                    <input type="email" id="newEmail" name="Email" placeholder="Nuevo email" value="<?php echo htmlspecialchars($usuario['Email']); ?>" required>
                    <input type="password" id="newPassword" name="Contrasenia" placeholder="Nueva contraseña (opcional)">
                    <input type="date" id="newBirthdate" name="Fecha_nacimiento" value="<?php echo htmlspecialchars($usuario['Fecha_Nacimiento']); ?>" required>
                    <textarea id="newDescription" name="Descripcion" placeholder="Descripción (máx. 50 caracteres)" maxlength="50"><?php echo htmlspecialchars($usuario['Descripcion'] ?? ''); ?></textarea>
                    <button type="submit" id="saveSettings">Guardar cambios</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <script>
        var userId = <?php echo json_encode($_SESSION['user_id']); ?>;
        var profileId = <?php echo json_encode($profileId); ?>;
        var isOwnProfile = <?php echo json_encode($isOwnProfile); ?>;
        var isFollowing = <?php echo json_encode($isFollowing); ?>;
    </script>
    <script src="../js/profile.js"></script>
</body>
</html>