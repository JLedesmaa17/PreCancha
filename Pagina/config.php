<?php
/**
 * CONFIGURACIÓN - PRE-CANCHA
 * 
 * Este archivo contiene las constantes de configuración del sistema
 */

// ============================================
// CONFIGURACIÓN DE BASE DE DATOS
// ============================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'precancha');
define('DB_USER', 'root');
define('DB_PASS', '');

// ============================================
// CONFIGURACIÓN DE APLICACIÓN
// ============================================

define('APP_NAME', 'PRE-CANCHA');
define('APP_URL', 'http://localhost/');
define('APP_TIMEZONE', 'America/Argentina/Buenos_Aires');

// ============================================
// CONFIGURACIÓN DE RESERVAS
// ============================================

define('ANTICIPACION_MINIMA', 0);      // Días mínimos de anticipación (0 = hoy mismo)
define('ANTICIPACION_MAXIMA', 30);     // Días máximos de anticipación
define('DURACION_TURNO', 60);          // Duración en minutos
define('HORA_INICIO', '09:00:00');     // Hora de inicio de operaciones
define('HORA_FIN', '23:00:00');        // Hora de cierre

// ============================================
// CONFIGURACIÓN DE SEGURIDAD
// ============================================

define('REQUIRE_APROBACION', true);    // Las reservas requieren aprobación
define('CSRF_PROTECTION', true);       // Protección CSRF activa
define('SESSION_TIMEOUT', 3600);       // Tiempo de sesión en segundos (1 hora)

// ============================================
// CONFIGURACIÓN DE NOTIFICACIONES
// ============================================

define('ENVIAR_EMAIL', true);
define('ENVIAR_WHATSAPP', false);
define('EMAIL_CONTACTO', 'info@precancha.com');
define('TELEFONO_CONTACTO', '+54 11 1234-5678');

// ============================================
// CONFIGURACIÓN DE MONEDA
// ============================================

define('MONEDA', 'ARS');               // Código ISO de moneda
define('SIMBOLO_MONEDA', '$');         // Símbolo para mostrar
define('DECIMALES', 2);                // Decimales a mostrar

// ============================================
// CONFIGURACIÓN DE DEBUG
// ============================================

define('DEBUG', false);                // Mostrar errores en pantalla
define('LOG_ERRORS', true);            // Registrar errores en archivo

// ============================================
// INFORMACIÓN DE ROLES
// ============================================

define('ROLES', [
    'admin' => 'Administrador',
    'usuario' => 'Usuario Regular'
]);

// ============================================
// INFORMACIÓN DE ESTADOS DE RESERVA
// ============================================

define('ESTADOS_RESERVA', [
    'pendiente' => '⏳ Pendiente de Confirmación',
    'confirmada' => '✓ Confirmada',
    'rechazada' => '✗ Rechazada',
    'cancelada' => '✗ Cancelada',
    'completada' => '✓ Completada'
]);

?>
