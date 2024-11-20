<?php
// Suppress warnings and notices that could break JSON output
error_reporting(0);
ini_set('display_errors', 0);

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$community_id = $_GET['community_id'] ?? null;

// Verificar si el usuario pertenece a la comunidad
function userBelongsToCommunity($user_id, $community_id) {
    include('../API USUARIO/config.php');
    $stmt = $conn->prepare("SELECT 1 FROM Miembro_Comunidad WHERE ID_Usuario = ? AND ID_Comunidad = ? UNION SELECT 1 FROM Comunidad WHERE ID_Administrador = ? AND ID_comunidad = ?");
    $stmt->bind_param("iiii", $user_id, $community_id, $user_id, $community_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

if (!userBelongsToCommunity($user_id, $community_id)) {
    header('Location: unauthorized.php');
    exit;
}

if (!$community_id) {
    header('Location: communities.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat de la Comunidad</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="../img/logoCFN.png">
    <link rel="stylesheet" href="../html/chat.css">
</head>
<body data-user-id="<?php echo $user_id; ?>" data-community-id="<?php echo $community_id; ?>">
    <nav class="nav-bar">
        <img src="../img/logoCFN.png" alt="Cineverse Logo" class="logo">
        <div class="nav-right">
            <div class="nav-icons">
                <a href="communities.php"><img src="../img/comunidad-icon.svg" class="nav-icon"></a>
                <a href="posts.php"><img src="../img/explore-icon.svg" class="nav-icon"></a>
                <a href="mensajeria.php"><img src="../img/chat-icon.svg" class="nav-icon"></a>
                <a href="perfil.php"><img src="../img/profile-icon.svg" class="nav-icon"></a>
                <a href="logout.php"><img src="../img/log-icon.svg" class="nav-icon"></a>
            </div>
        </div>
    </nav>
    <div class="chat-container">
        <div class="member-list" id="memberList">
            <h3 style="color: #fff; margin-bottom: 1rem;">Miembros</h3>
        </div>
        <div class="chat-messages">
            <div class="message-list" id="messageList"></div>
            <form id="messageForm" class="message-form">
                <input type="text" id="messageInput" class="message-input" placeholder="Escribe un mensaje..." required>
                <button type="submit" class="settings-button">Enviar</button>
            </form>
        </div>
    </div>

    <script src="../js/communities.js"></script>
</body>
</html>