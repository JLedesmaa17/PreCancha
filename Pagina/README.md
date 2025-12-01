# PRE-CANCHA - Sistema de Reservas de Canchas de Fútbol

## 📋 Descripción
Sistema web para gestionar reservas de canchas de fútbol. Permite a usuarios registrarse, iniciar sesión y reservar canchas disponibles.

## 🚀 Instalación y Configuración

### 1. Requisitos
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache, Nginx, etc.)

### 2. Pasos de instalación

#### a. Crear la base de datos
```bash
mysql -u root -p < schema.sql
```

#### b. Configurar la conexión
Editar el archivo `bd.php` si es necesario:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'precancha');
define('DB_USER', 'root');
define('DB_PASS', '');
```

#### c. Copiar los archivos
Copiar todos los archivos `.php` a la carpeta raíz de tu servidor web (htdocs para XAMPP, www para WAMP, etc.)

### 3. Acceder a la aplicación
```
http://localhost/TRABAJO\ 3/Pagina/index.php
```

## 📁 Estructura de Archivos

```
├── index.php              # Página principal - Lista de canchas
├── login.php              # Página de inicio de sesión
├── registro.php           # Página de registro de usuarios
├── reservas.php           # Página para hacer reservas
├── mis_reservas.php       # Ver y gestionar reservas del usuario
├── logout.php             # Cerrar sesión
├── bd.php                 # Conexión a base de datos
└── schema.sql             # Esquema de base de datos
```

## 👤 Usuarios Predefinidos

### Administrador
- **Email**: admin@precancha.com
- **Contraseña**: admin123

## 🎮 Funcionalidades

### Usuarios
- ✅ Registro de nuevas cuentas
- ✅ Inicio de sesión
- ✅ Visualización de canchas disponibles
- ✅ Realizar reservas
- ✅ Gestionar mis reservas (ver, cancelar)
- ✅ Cerrar sesión

### Administrador (futuro)
- Gestionar canchas
- Aprobar/rechazar reservas
- Ver reportes
- Gestionar horarios

## 🔒 Seguridad

- Contraseñas encriptadas con PASSWORD_DEFAULT (bcrypt)
- Validación de emails con FILTER_VALIDATE_EMAIL
- Protección contra inyección SQL con prepared statements
- Sesiones seguras en servidor

## 💰 Información de Canchas

### Canchas Disponibles Inicialmente

| Número | Tipo | Jugadores | Superficie | Precio/Hora |
|--------|------|-----------|-----------|-------------|
| 1 | Fútbol 5 | 10 | Césped Sintético Premium | $5000 |
| 2 | Fútbol 5 | 10 | Césped Sintético Premium | $5000 |
| 3 | Fútbol 5 | 10 | Césped Sintético Premium | $5000 |
| 4 | Fútbol 5 | 10 | Césped Sintético Premium | $5000 |
| 5 | Fútbol 8 | 16 | Césped Sintético Profesional | $8000 |

## 🕐 Horarios de Funcionamiento

- **Inicio**: 09:00
- **Cierre**: 23:00
- **Duración de turno**: 1 hora
- **Días disponibles**: Todos los días de la semana

## 📝 Notas

- Los horarios están disponibles de 9:00 a 23:00 horas
- Las reservas son de 1 hora de duración
- Se pueden reservar con hasta 30 días de anticipación
- Las reservas inicialmente están en estado "pendiente" (requieren confirmación del administrador)

## 🛠️ Próximas Mejoras

- [ ] Panel de administrador
- [ ] Notificaciones por email
- [ ] Sistema de pagos integrado
- [ ] Más opciones de duración de turno
- [ ] Historial de reservas completadas
- [ ] Sistema de calificaciones y comentarios

## 📧 Soporte

Para reportar problemas o sugerencias, contacta con: info@precancha.com

---

**Versión**: 1.0.0  
**Última actualización**: Diciembre 2025
