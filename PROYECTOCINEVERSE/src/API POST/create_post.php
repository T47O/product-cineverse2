<?php
include('../API USUARIO/config.php');

// Habilitar la visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Logging detallado
error_log("Iniciando create_post.php");
error_log("Datos POST recibidos: " . print_r($_POST, true));
error_log("Archivos recibidos: " . print_r($_FILES, true));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    error_log("Método POST detectado");
    
    // Validación de los datos enviados en la solicitud
    if (isset($_POST['titulo'], $_POST['descripcion'], $_POST['mayor'], $_POST['id_usuario'], $_FILES['contenido'])) {
        error_log("Todos los campos requeridos están presentes");
        
        $titulo = $_POST['titulo'];
        $descripcion = $_POST['descripcion'];
        $mayor = (int)$_POST['mayor'];
        $id_usuario = (int)$_POST['id_usuario'];

        error_log("Título: " . $titulo);
        error_log("Descripción: " . $descripcion);
        error_log("Mayor: " . $mayor);
        error_log("ID Usuario: " . $id_usuario);

        // Obtener el contenido multimedia como un blob
        $contenido = file_get_contents($_FILES['contenido']['tmp_name']);
        error_log("Contenido multimedia obtenido, tamaño: " . strlen($contenido) . " bytes");

        // Verificar la conexión a la base de datos
        if ($conn->connect_error) {
            error_log("Error de conexión a la base de datos: " . $conn->connect_error);
            die("Connection failed: " . $conn->connect_error);
        }

        // Verificar la estructura de la tabla
        $result = $conn->query("DESCRIBE Publicacion");
        if ($result) {
            error_log("Estructura de la tabla Publicacion:");
            while ($row = $result->fetch_assoc()) {
                error_log(print_r($row, true));
            }
        } else {
            error_log("Error al obtener la estructura de la tabla: " . $conn->error);
        }

        // Preparar la consulta SQL de inserción
        $sql = "INSERT INTO Publicacion (Titulo, Contenido_Multimedia, Descripcion, Mayor, FechaHora_Publicacion, ID_Usuario) VALUES (?, ?, ?, ?, NOW(), ?)";
        error_log("SQL Query: " . $sql);
        
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            error_log("Error en la preparación de la consulta: " . $conn->error);
            echo json_encode(["success" => false, "message" => "Error en la preparación de la consulta: " . $conn->error]);
            exit;
        }

        error_log("Consulta SQL preparada exitosamente");

        // Enlace de los parámetros usando 'ssssi' para especificar tipos (string, string, string, string, integer)
        if (!$stmt->bind_param("ssssi", $titulo, $contenido, $descripcion, $mayor, $id_usuario)) {
            error_log("Error en bind_param: " . $stmt->error);
            echo json_encode(["success" => false, "message" => "Error al vincular parámetros: " . $stmt->error]);
            exit;
        }

        error_log("Parámetros vinculados exitosamente");

        // Ejecutar la inserción
        if ($stmt->execute()) {
            $id_publicacion = $stmt->insert_id;
            error_log("Inserción exitosa. ID de publicación: " . $id_publicacion);

            // Verificar que los datos se insertaron correctamente
            $verify_query = "SELECT * FROM Publicacion WHERE ID_Publicacion = ?";
            $verify_stmt = $conn->prepare($verify_query);
            $verify_stmt->bind_param("i", $id_publicacion);
            $verify_stmt->execute();
            $result = $verify_stmt->get_result();
            $inserted_data = $result->fetch_assoc();
            error_log("Datos insertados: " . print_r($inserted_data, true));

            // Insertar en la tabla Post con el ID de Publicacion
            $stmt_post = $conn->prepare("INSERT INTO Post (ID_Publicacion) VALUES (?)");
            $stmt_post->bind_param("i", $id_publicacion);
            if ($stmt_post->execute()) {
                error_log("Inserción en tabla Post exitosa");
                echo json_encode(["success" => true, "message" => "Post creado exitosamente.", "id_publicacion" => $id_publicacion]);
            } else {
                error_log("Error al insertar en tabla Post: " . $stmt_post->error);
                echo json_encode(["success" => false, "message" => "Error al crear el Post en la tabla Post: " . $stmt_post->error]);
            }
            $stmt_post->close();
        } else {
            error_log("Error al ejecutar la inserción: " . $stmt->error);
            echo json_encode(["success" => false, "message" => "Error al crear el Post: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        $missing = [];
        if (!isset($_POST['titulo'])) $missing[] = 'titulo';
        if (!isset($_POST['descripcion'])) $missing[] = 'descripcion';
        if (!isset($_POST['mayor'])) $missing[] = 'mayor';
        if (!isset($_POST['id_usuario'])) $missing[] = 'id_usuario';
        if (!isset($_FILES['contenido'])) $missing[] = 'contenido';
        
        error_log("Faltan datos requeridos: " . implode(', ', $missing));
        echo json_encode(["success" => false, "message" => "Faltan datos requeridos: " . implode(', ', $missing)]);
    }
} else {
    error_log("Método de solicitud no permitido: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode(["success" => false, "message" => "Método de solicitud no permitido."]);
}

error_log("Finalizando create_post.php");
?>