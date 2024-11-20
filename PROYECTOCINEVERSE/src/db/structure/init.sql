CREATE DATABASE Cineverse;

USE Cineverse;

CREATE TABLE Usuario (
    ID_Usuario INT PRIMARY KEY AUTO_INCREMENT,
    Descripcion VARCHAR(25),
    Foto_perfil longblob,
    Nombre VARCHAR(100),
    Email VARCHAR(100) UNIQUE,
    Contrasena VARCHAR(255),
    Fecha_Nacimiento DATE,
    Conectado BOOLEAN
);

CREATE TABLE Moderador (
    ID_Moderador INT PRIMARY KEY,
    FOREIGN KEY (ID_Moderador) REFERENCES Usuario(ID_Usuario)
);

CREATE TABLE Bloquear (
    Bloqueado INT,
    Bloqueador INT,
    Tiempo_Bloqueado DATETIME,
    FOREIGN KEY (Bloqueado) REFERENCES Usuario(ID_Usuario),
    FOREIGN KEY (Bloqueador) REFERENCES Usuario(ID_Usuario)
);

CREATE TABLE IF NOT EXISTS Seguir (
    ID_Seguir INT AUTO_INCREMENT PRIMARY KEY,
    Seguidor INT,
    Seguido INT,
    FOREIGN KEY (Seguidor) REFERENCES Usuario(ID_Usuario),
    FOREIGN KEY (Seguido) REFERENCES Usuario(ID_Usuario)
);

CREATE TABLE Comunidad (
    ID_comunidad INT AUTO_INCREMENT PRIMARY KEY,
    Titulo VARCHAR(255),
    Descripcion TEXT,
    ID_Administrador INT,
    FOREIGN KEY (ID_Administrador) REFERENCES Usuario(ID_Usuario)
);

CREATE TABLE IF NOT EXISTS Mensaje_Comunidad (
    ID_MensajeComunidad INT AUTO_INCREMENT PRIMARY KEY,
    ID_Comunidad INT,
    ID_Usuario INT,
    Contenido TEXT,
    Fecha_Hora DATETIME DEFAULT NOW(),
    FOREIGN KEY (ID_Comunidad) REFERENCES Comunidad(ID_Comunidad),
    FOREIGN KEY (ID_Usuario) REFERENCES Usuario(ID_Usuario)
);

CREATE TABLE IF NOT EXISTS Miembro_Comunidad (
    ID_MiembroComunidad INT AUTO_INCREMENT PRIMARY KEY,
    ID_Usuario INT,
    ID_Comunidad INT,
    Fecha_Unido DATETIME DEFAULT NOW(),
    FOREIGN KEY (ID_Usuario) REFERENCES Usuario(ID_Usuario),
    FOREIGN KEY (ID_Comunidad) REFERENCES Comunidad(ID_Comunidad),
    UNIQUE KEY (ID_Usuario, ID_Comunidad)
);

CREATE TABLE Publicacion (
    ID_Publicacion INT AUTO_INCREMENT PRIMARY KEY,
    Titulo VARCHAR(255),
    Contenido_Multimedia longblob,
    Descripcion TEXT,
    Mayor BOOLEAN,
    FechaHora_Publicacion DATETIME,
    ID_Usuario INT,
    FOREIGN KEY (ID_Usuario) REFERENCES Usuario(ID_Usuario)
);

CREATE TABLE Likea (
    ID_Likea INT PRIMARY KEY AUTO_INCREMENT,
    ID_Usuario INT,
    ID_Publicacion INT,
    FOREIGN KEY (ID_Usuario) REFERENCES Usuario(ID_Usuario),
    FOREIGN KEY (ID_Publicacion) REFERENCES Publicacion(ID_Publicacion)
);

CREATE TABLE Comenta (
    ID_Comenta INT PRIMARY KEY AUTO_INCREMENT,
    Contenido TEXT,
    Fecha DATETIME,
    ID_Usuario INT,
    ID_Publicacion INT,
    FOREIGN KEY (ID_Usuario) REFERENCES Usuario(ID_Usuario),
    FOREIGN KEY (ID_Publicacion) REFERENCES Publicacion(ID_Publicacion)
);

CREATE TABLE Post (
    ID_Post INT PRIMARY KEY AUTO_INCREMENT,
    ID_Publicacion INT,
    FOREIGN KEY (ID_Publicacion) REFERENCES Publicacion(ID_Publicacion)
);

CREATE TABLE IF NOT EXISTS Evento (
    ID_Evento INT AUTO_INCREMENT PRIMARY KEY,
    ID_Publicacion INT,
    FechaHora_Realizacion DATETIME,
    Ubicacion VARCHAR(255),
    FOREIGN KEY (ID_Publicacion) REFERENCES Publicacion(ID_Publicacion)
);

-- Nueva tabla para mensajes privados
CREATE TABLE Mensajes (
    ID_Mensaje INT AUTO_INCREMENT PRIMARY KEY,
    ID_Emisor INT NOT NULL,
    ID_Receptor INT NOT NULL,
    Contenido TEXT NOT NULL,
    Fecha_Hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ID_Emisor) REFERENCES Usuario(ID_Usuario),
    FOREIGN KEY (ID_Receptor) REFERENCES Usuario(ID_Usuario)
);

-- Vistas existentes
CREATE VIEW Usuarios_Conectados AS
SELECT ID_Usuario, Nombre, Email, Foto_perfil
FROM Usuario
WHERE Conectado = TRUE;

CREATE VIEW Eventos_Programados AS
SELECT Evento.ID_Evento, Publicacion.Titulo AS Titulo_Evento, Publicacion.Descripcion, Evento.FechaHora_Realizacion
FROM Evento
JOIN Publicacion ON Evento.ID_Publicacion = Publicacion.ID_Publicacion
WHERE Evento.FechaHora_Realizacion > NOW();

CREATE VIEW Publicaciones_y_Likes AS
SELECT Publicacion.ID_Publicacion, Publicacion.Titulo, Publicacion.Descripcion, COUNT(Likea.ID_Likea) AS Total_Likes
FROM Publicacion
LEFT JOIN Likea ON Publicacion.ID_Publicacion = Likea.ID_Publicacion
GROUP BY Publicacion.ID_Publicacion, Publicacion.Titulo, Publicacion.Descripcion;

CREATE VIEW Publicaciones_Post AS
SELECT Publicacion.ID_Publicacion, Publicacion.Titulo, Publicacion.Descripcion, Publicacion.FechaHora_Publicacion
FROM Publicacion
JOIN Post ON Publicacion.ID_Publicacion = Post.ID_Publicacion;

CREATE VIEW Vista_Publicaciones_Usuario AS
SELECT 
    Publicacion.ID_Publicacion,
    Publicacion.Titulo,
    Publicacion.Descripcion,
    Publicacion.FechaHora_Publicacion,
    CASE 
        WHEN Evento.ID_Evento IS NOT NULL THEN 'evento'
        ELSE 'post'
    END AS tipo,
    Evento.FechaHora_Realizacion,
    Evento.Ubicacion,
    Publicacion.ID_Usuario
FROM 
    Publicacion
LEFT JOIN 
    Evento ON Publicacion.ID_Publicacion = Evento.ID_Publicacion;

-- Nueva vista para mensajes
CREATE VIEW Vista_Mensajes_Usuario AS
SELECT 
    m.ID_Mensaje,
    m.Contenido,
    m.Fecha_Hora,
    e.Nombre AS Emisor,
    r.Nombre AS Receptor
FROM 
    Mensajes m
JOIN 
    Usuario e ON m.ID_Emisor = e.ID_Usuario
JOIN 
    Usuario r ON m.ID_Receptor = r.ID_Usuario;