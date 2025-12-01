-- ============================================
-- PRE-CANCHA - ESQUEMA DE BASE DE DATOS
-- Sistema de Gestión de Reservas de Canchas
-- ============================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS precancha CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE precancha;

-- ============================================
-- TABLA: usuarios
-- Almacena información de usuarios del sistema
-- ============================================
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    telefono VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'usuario') DEFAULT 'usuario',
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_rol (rol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: canchas
-- Información de las canchas disponibles
-- ============================================
CREATE TABLE canchas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero INT NOT NULL UNIQUE,
    tipo VARCHAR(50) NOT NULL,
    jugadores INT NOT NULL,
    superficie VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio_hora DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    estado ENUM('disponible', 'mantenimiento', 'inactiva') DEFAULT 'disponible',
    iluminacion BOOLEAN DEFAULT TRUE,
    vestuarios BOOLEAN DEFAULT TRUE,
    estacionamiento BOOLEAN DEFAULT FALSE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_numero (numero),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: reservas
-- Gestión de todas las reservas
-- ============================================
CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cancha_id INT NOT NULL,
    usuario_id INT NOT NULL,
    fecha_reserva DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    estado ENUM('pendiente', 'confirmada', 'rechazada', 'cancelada', 'completada') DEFAULT 'pendiente',
    monto DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    observaciones TEXT,
    motivo_rechazo TEXT,
    horario_alternativo_sugerido VARCHAR(50),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    fecha_confirmacion TIMESTAMP NULL,
    fecha_rechazo TIMESTAMP NULL,
    admin_id INT NULL COMMENT 'ID del admin que procesó la solicitud',
    FOREIGN KEY (cancha_id) REFERENCES canchas(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_fecha_reserva (fecha_reserva),
    INDEX idx_estado (estado),
    INDEX idx_cancha_fecha (cancha_id, fecha_reserva),
    INDEX idx_usuario (usuario_id),
    UNIQUE KEY unique_reserva (cancha_id, fecha_reserva, hora_inicio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: horarios_disponibles
-- Define los horarios de operación
-- ============================================
CREATE TABLE horarios_disponibles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cancha_id INT NOT NULL,
    dia_semana TINYINT NOT NULL COMMENT '0=Domingo, 1=Lunes, ..., 6=Sábado',
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (cancha_id) REFERENCES canchas(id) ON DELETE CASCADE,
    INDEX idx_cancha_dia (cancha_id, dia_semana)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: bloqueos
-- Para bloquear fechas/horarios específicos
-- ============================================
CREATE TABLE bloqueos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cancha_id INT NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    hora_inicio TIME NULL,
    hora_fin TIME NULL,
    motivo VARCHAR(255),
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cancha_id) REFERENCES canchas(id) ON DELETE CASCADE,
    INDEX idx_cancha_fecha (cancha_id, fecha_inicio, fecha_fin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: historial_acciones
-- Registro de auditoría del sistema
-- ============================================
CREATE TABLE historial_acciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    accion VARCHAR(100) NOT NULL,
    tabla_afectada VARCHAR(50),
    registro_id INT,
    detalles TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_fecha (usuario_id, fecha),
    INDEX idx_tabla_registro (tabla_afectada, registro_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: configuracion
-- Configuraciones generales del sistema
-- ============================================
CREATE TABLE configuracion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(100) NOT NULL UNIQUE,
    valor TEXT,
    descripcion TEXT,
    tipo ENUM('texto', 'numero', 'boolean', 'json') DEFAULT 'texto',
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_clave (clave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DATOS INICIALES
-- ============================================

-- Usuario administrador por defecto
-- Password: admin123 (debes cambiarlo después)
INSERT INTO usuarios (nombre, email, telefono, password, rol) VALUES
('Administrador', 'admin@precancha.com', '+54 11 1234-5678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Canchas iniciales
INSERT INTO canchas (numero, tipo, jugadores, superficie, descripcion, precio_hora, iluminacion, vestuarios) VALUES
(1, 'Fútbol 5', 10, 'Césped Sintético Premium', 'Ideal para partidos rápidos', 5000.00, TRUE, TRUE),
(2, 'Fútbol 5', 10, 'Césped Sintético Premium', 'Ideal para partidos rápidos', 5000.00, TRUE, TRUE),
(3, 'Fútbol 5', 10, 'Césped Sintético Premium', 'Ideal para partidos rápidos', 5000.00, TRUE, TRUE),
(4, 'Fútbol 5', 10, 'Césped Sintético Premium', 'Ideal para partidos rápidos', 5000.00, TRUE, TRUE),
(5, 'Fútbol 8', 16, 'Césped Sintético Profesional', 'Perfecta para equipos grandes', 8000.00, TRUE, TRUE);

-- Horarios disponibles por defecto (9:00 a 23:00, todos los días)
INSERT INTO horarios_disponibles (cancha_id, dia_semana, hora_inicio, hora_fin) 
SELECT c.id, d.dia, '09:00:00', '23:00:00'
FROM canchas c
CROSS JOIN (
    SELECT 0 as dia UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL 
    SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6
) d;

-- Configuraciones iniciales del sistema
INSERT INTO configuracion (clave, valor, descripcion, tipo) VALUES
('nombre_sitio', 'PRE-CANCHA', 'Nombre del sitio web', 'texto'),
('email_contacto', 'info@precancha.com', 'Email de contacto', 'texto'),
('telefono_contacto', '+54 11 1234-5678', 'Teléfono de contacto', 'texto'),
('duracion_turno', '60', 'Duración de cada turno en minutos', 'numero'),
('anticipacion_minima', '2', 'Días de anticipación mínima para reservar', 'numero'),
('anticipacion_maxima', '30', 'Días de anticipación máxima para reservar', 'numero'),
('requiere_aprobacion', 'true', 'Las reservas requieren aprobación del admin', 'boolean'),
('notificaciones_email', 'true', 'Enviar notificaciones por email', 'boolean'),
('notificaciones_whatsapp', 'false', 'Enviar notificaciones por WhatsApp', 'boolean');

-- ============================================
-- VISTAS ÚTILES
-- ============================================

-- Vista de reservas con información completa
CREATE OR REPLACE VIEW v_reservas_completas AS
SELECT 
    r.id,
    r.fecha_reserva,
    r.hora_inicio,
    r.hora_fin,
    r.estado,
    r.monto,
    r.observaciones,
    r.fecha_creacion,
    c.numero as cancha_numero,
    c.tipo as cancha_tipo,
    u.nombre as usuario_nombre,
    u.email as usuario_email,
    u.telefono as usuario_telefono,
    a.nombre as admin_nombre
FROM reservas r
INNER JOIN canchas c ON r.cancha_id = c.id
INNER JOIN usuarios u ON r.usuario_id = u.id
LEFT JOIN usuarios a ON r.admin_id = a.id;

-- Vista de disponibilidad de canchas
CREATE OR REPLACE VIEW v_canchas_disponibles AS
SELECT 
    c.id,
    c.numero,
    c.tipo,
    c.jugadores,
    c.superficie,
    c.precio_hora,
    c.estado,
    COUNT(r.id) as reservas_activas
FROM canchas c
LEFT JOIN reservas r ON c.id = r.cancha_id 
    AND r.fecha_reserva >= CURDATE()
    AND r.estado IN ('pendiente', 'confirmada')
WHERE c.estado = 'disponible'
GROUP BY c.id;

-- ============================================
-- PROCEDIMIENTOS ALMACENADOS
-- ============================================

DELIMITER //

-- Procedimiento para verificar disponibilidad
CREATE PROCEDURE sp_verificar_disponibilidad(
    IN p_cancha_id INT,
    IN p_fecha DATE,
    IN p_hora_inicio TIME,
    IN p_hora_fin TIME
)
BEGIN
    SELECT 
        CASE 
            WHEN COUNT(*) > 0 THEN FALSE
            ELSE TRUE
        END as disponible
    FROM reservas
    WHERE cancha_id = p_cancha_id
      AND fecha_reserva = p_fecha
      AND estado IN ('pendiente', 'confirmada')
      AND (
          (hora_inicio < p_hora_fin AND hora_fin > p_hora_inicio)
      );
END //

-- Procedimiento para obtener horarios alternativos
CREATE PROCEDURE sp_horarios_alternativos(
    IN p_cancha_id INT,
    IN p_fecha DATE,
    IN p_hora_deseada TIME
)
BEGIN
    -- Buscar horarios disponibles antes y después de la hora deseada
    SELECT 
        hora_inicio,
        'disponible' as estado,
        ABS(TIME_TO_SEC(TIMEDIFF(hora_inicio, p_hora_deseada))) as diferencia_segundos
    FROM (
        SELECT DISTINCT hora_inicio
        FROM horarios_disponibles hd
        WHERE hd.cancha_id = p_cancha_id
          AND hd.activo = TRUE
          AND NOT EXISTS (
              SELECT 1 FROM reservas r
              WHERE r.cancha_id = p_cancha_id
                AND r.fecha_reserva = p_fecha
                AND r.hora_inicio = hd.hora_inicio
                AND r.estado IN ('pendiente', 'confirmada')
          )
    ) as disponibles
    ORDER BY diferencia_segundos
    LIMIT 3;
END //

-- Procedimiento para registrar acción en historial
CREATE PROCEDURE sp_registrar_accion(
    IN p_usuario_id INT,
    IN p_accion VARCHAR(100),
    IN p_tabla VARCHAR(50),
    IN p_registro_id INT,
    IN p_detalles TEXT,
    IN p_ip VARCHAR(45),
    IN p_user_agent TEXT
)
BEGIN
    INSERT INTO historial_acciones 
        (usuario_id, accion, tabla_afectada, registro_id, detalles, ip_address, user_agent)
    VALUES 
        (p_usuario_id, p_accion, p_tabla, p_registro_id, p_detalles, p_ip, p_user_agent);
END //

DELIMITER ;

-- ============================================
-- TRIGGERS
-- ============================================

DELIMITER //

-- Trigger para actualizar fecha de modificación en reservas
CREATE TRIGGER trg_reservas_before_update
BEFORE UPDATE ON reservas
FOR EACH ROW
BEGIN
    SET NEW.fecha_modificacion = CURRENT_TIMESTAMP;
    
    IF NEW.estado = 'confirmada' AND OLD.estado != 'confirmada' THEN
        SET NEW.fecha_confirmacion = CURRENT_TIMESTAMP;
    END IF;
    
    IF NEW.estado = 'rechazada' AND OLD.estado != 'rechazada' THEN
        SET NEW.fecha_rechazo = CURRENT_TIMESTAMP;
    END IF;
END //

DELIMITER ;

-- ============================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- ============================================

-- Índices compuestos para consultas frecuentes
CREATE INDEX idx_reservas_fecha_estado ON reservas(fecha_reserva, estado);
CREATE INDEX idx_reservas_usuario_fecha ON reservas(usuario_id, fecha_reserva DESC);
CREATE INDEX idx_usuarios_rol_estado ON usuarios(rol, estado);

-- ============================================
-- PERMISOS Y SEGURIDAD
-- ============================================

-- Crear usuario de base de datos (ajusta según tu configuración)
-- CREATE USER 'precancha_user'@'localhost' IDENTIFIED BY 'tu_password_seguro';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON precancha.* TO 'precancha_user'@'localhost';
-- FLUSH PRIVILEGES;

-- ============================================
-- BACKUP RECOMENDADO
-- ============================================
-- Para hacer backup: mysqldump -u root -p precancha > backup_precancha.sql
-- Para restaurar: mysql -u root -p precancha < backup_precancha.sql