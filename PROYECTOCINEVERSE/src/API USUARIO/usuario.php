<?php
class Usuario
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function createUsuario($data)
    {
        $nombre = $data['Nombre'];
        $email = $data['Email'];
        $contrasenia = password_hash($data['Contrasenia'], PASSWORD_BCRYPT);
        $fecha_nacimiento = $data['Fecha_nacimiento'];

        // Verificar si el email ya existe
        $checkEmail = "SELECT * FROM Usuario WHERE Email = ?";
        $stmtCheck = $this->conn->prepare($checkEmail);
        $stmtCheck->bind_param("s", $email);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();
        if ($result->num_rows > 0) {
            return "El email ya está registrado";
        }

        $query = "INSERT INTO Usuario (Nombre, Email, Contrasena, Fecha_Nacimiento) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssss", $nombre, $email, $contrasenia, $fecha_nacimiento);

        if ($stmt->execute()) {
            return true;
        } else {
            return "Error al crear el usuario: " . $stmt->error;
        }
    }

    public function getUsuarioById($userId) {
        $sql = "SELECT * FROM Usuario WHERE ID_Usuario = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparing statement: " . $this->conn->error);
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    public function updateUsuario($id, $data, $foto_perfil = null)
    {
        $nombre = $data['Nombre'];
        $email = $data['Email'];
        $contrasenia = !empty($data['Contrasenia']) ? password_hash($data['Contrasenia'], PASSWORD_BCRYPT) : null;
        $fecha_nacimiento = $data['Fecha_nacimiento'];
        $descripcion = substr($data['Descripcion'], 0, 25); // Limitar a 25 caracteres

        $query = "UPDATE Usuario SET Nombre = ?, Email = ?, Fecha_Nacimiento = ?, Descripcion = ?";
        $types = "ssss";
        $params = [$nombre, $email, $fecha_nacimiento, $descripcion];

        if ($contrasenia) {
            $query .= ", Contrasena = ?";
            $types .= "s";
            $params[] = $contrasenia;
        }

        if ($foto_perfil && $foto_perfil['error'] === UPLOAD_ERR_OK) {
            $imagen_contenido = file_get_contents($foto_perfil['tmp_name']);
            $query .= ", Foto_perfil = ?";
            $types .= "b";
            $params[] = $imagen_contenido;
        }

        $query .= " WHERE ID_Usuario = ?";
        $types .= "i";
        $params[] = $id;

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$params);

        return $stmt->execute();
    }

    public function deleteUsuario($id)
    {
        $query = "DELETE FROM Usuario WHERE ID_Usuario = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    public function login($email, $password)
    {
        $query = "SELECT * FROM Usuario WHERE Email = ?";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['Contrasena'])) {
            return $user;
        }

        return false;
    }

    public function searchUserById($id)
    {
        $query = "SELECT ID_Usuario, Nombre, Email FROM Usuario WHERE ID_Usuario = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    public function updateProfilePicture($userId, $imageData) {
        $sql = "UPDATE Usuario SET Foto_perfil = ? WHERE ID_Usuario = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparing statement: " . $this->conn->error);
        }
        $null = NULL;
        $stmt->bind_param("bi", $null, $userId);
        $stmt->send_long_data(0, $imageData);
        $result = $stmt->execute();
        if (!$result) {
            throw new Exception("Error executing statement: " . $stmt->error);
        }
        return $result;
    }

    public function getProfilePicture($userId) {
        $sql = "SELECT Foto_perfil FROM Usuario WHERE ID_Usuario = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparing statement: " . $this->conn->error);
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->store_result();
        
        $imageData = null; // Declarar la variable antes de usarla
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($imageData);
            $stmt->fetch();
            return $imageData;
        }
        return null;
    }


}
?>