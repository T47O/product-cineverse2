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
    <title>Crear Evento - CINEVERSE</title>
    <link rel="icon" type="image/png" href="../img/logoCFN.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../html/create-evento.css">
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

    <div class="create-event-container">
        <h2>Crear Nuevo Evento</h2>
        <form id="createEventForm" enctype="multipart/form-data">
            <div class="form-group">
                <label for="titulo">Título del Evento</label>
                <input type="text" id="titulo" name="titulo" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" required></textarea>
            </div>
            <div class="form-group">
                <label for="fecha_hora">Fecha y Hora</label>
                <input type="datetime-local" id="fecha_hora" name="fecha_hora" required>
            </div>
            <div class="form-group">
                <label for="ubicacion">Ubicación</label>
                <input type="text" id="ubicacion" name="ubicacion" required>
            </div>
            <div class="form-group">
                <label for="contenido_multimedia">Imagen del Evento</label>
                <input type="file" id="contenido_multimedia" name="contenido_multimedia" accept="image/*" required>
            </div>
            <button type="submit" class="submit-button">Crear Evento</button>
        </form>
    </div>

    <script>
        document.getElementById('createEventForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            var formData = new FormData(this);
            formData.append('id_usuario', <?php echo $_SESSION['user_id']; ?>);

            fetch('../API_EVENTO/create_event.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Evento creado exitosamente');
                    window.location.href = 'evento.php';
                } else {
                    alert('Error al crear el evento: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al crear el evento');
            });
        });
    </script>
</body>
</html>