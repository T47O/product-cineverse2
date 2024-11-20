<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso No Autorizado</title>
    <link rel="icon" type="image/png" href="../img/logoCFN.png">
    <link rel="stylesheet" href="../html/style.css">
</head>
<body>
    <div class="unauthorized-container">
        <h1>Acceso No Autorizado</h1>
        <p>Lo sentimos, no tienes permiso para acceder a esta comunidad.</p>
        <a href="communities.php" class="back-button">Volver a Mis Comunidades</a>
    </div>
</body>
</html>