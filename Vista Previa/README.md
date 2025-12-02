# PRE-CANCHA - Sistema de Reservas de Canchas (Versión HTML)

Sistema de reservas de canchas de fútbol completamente funcional en HTML5 con sesiones simuladas usando localStorage.

## 📋 Características

✅ **Autenticación de Usuarios**: Login y registro con sesiones persistentes
✅ **Gestión de Reservas**: Crear, ver y cancelar reservas
✅ **Panel de Administrador**: Vista completa de estadísticas, reservas, usuarios y canchas
✅ **Sistema de Sesiones**: Basado en localStorage (funciona sin backend)
✅ **Interfaz Moderna**: Diseño oscuro con colores dorados
✅ **Completamente Responsivo**: Funciona en desktop y móvil

## 🗂️ Archivos del Sistema

- **index.html** - Página principal con listado de canchas
- **login.html** - Página de inicio de sesión
- **registro.html** - Página de registro de nuevos usuarios
- **mis_reservas.html** - Ver y gestionar tus reservas
- **admin.html** - Panel de administrador (solo para admins)
- **app.js** - Sistema de gestión de sesiones y datos
- **estilos.css** - Estilos CSS completos
- **landing.html** - Página de aterrizaje (original, no modificada)

## 🚀 Cómo Usar

### 1. Abrir el Sistema
Simplemente abre `index.html` en tu navegador web. No necesita servidor, todo funciona en el navegador.

### 2. Cuentas de Prueba

**Usuario Regular:**
- Email: `juan@example.com`
- Contraseña: `123456`

**Administrador:**
- Email: `admin@example.com`
- Contraseña: `admin123`

También puedes crear nuevas cuentas usando la página de registro.

### 3. Crear una Reserva

1. Inicia sesión con una cuenta de usuario
2. Haz clic en "Inicio" para ver las canchas disponibles
3. Haz clic en el botón "Reservar" de una cancha
4. Selecciona la fecha y horario deseado
5. Añade observaciones si lo deseas
6. Confirma la reserva

### 4. Ver Tus Reservas

1. Después de iniciar sesión, verás un botón "Mis Reservas"
2. Aquí puedes ver todas tus reservas
3. Puedes filtrar por estado (Confirmada, Pendiente, Cancelada)
4. Puedes cancelar reservas directamente desde aquí

### 5. Panel de Administrador

Si inicias sesión como admin (admin@example.com):

1. Verás un botón "Panel Admin" en la navegación
2. **Estadísticas**: Ve resumen del sistema
3. **Todas las Reservas**: Consulta todas las reservas del sistema
4. **Usuarios**: Gestiona y observa todos los usuarios
5. **Canchas**: Ve información de todas las canchas

## 💾 Datos y Almacenamiento

El sistema utiliza **localStorage** del navegador para almacenar:

- **Usuarios**: Información de cuentas registradas
- **Canchas**: Listado de canchas disponibles
- **Reservas**: Todas las reservas realizadas
- **Sesión**: Información del usuario actualmente logueado

**Nota**: Los datos se pierden si limpias el caché/localStorage del navegador.

## 🔐 Seguridad

Este es un sistema de demostración/vista previa. En producción:

- Las contraseñas deberían estar encriptadas
- Se debe usar autenticación del lado del servidor
- Se debe implementar base de datos real
- Se debe usar HTTPS

## 📱 Funcionalidades por Rol

### Usuario Regular
- ✅ Ver canchas disponibles
- ✅ Crear reservas
- ✅ Ver mis reservas
- ✅ Cancelar reservas
- ✅ Gestionar perfil

### Administrador
- ✅ Ver todas las reservas
- ✅ Ver estadísticas del sistema
- ✅ Gestionar usuarios
- ✅ Gestionar canchas
- ✅ Ver ingresos totales

## 🎨 Diseño

- **Colores Principales**: Oro (#DAA520) y Amarillo (#FFD700)
- **Fondo**: Negro y gris oscuro
- **Tipografía**: Segoe UI, Tahoma, Geneva
- **Responsive**: Se adapta a tablets y móviles

## 🛠️ Tecnologías Utilizadas

- HTML5
- CSS3 (con variables CSS)
- JavaScript Vanilla
- localStorage API

## 📝 Notas Importantes

1. Los datos se guardan en el navegador (localStorage)
2. Cada navegador tiene su propio almacenamiento
3. Al limpiar el caché se borran todos los datos
4. No requiere conexión a internet (excepto para descargar los archivos)

## 🔄 Flujo de Navegación

```
Inicio (index.html)
├── Login (login.html) → Inicia sesión
├── Registro (registro.html) → Crea una cuenta
├── Mis Reservas (mis_reservas.html) → Gestiona tus reservas
└── Admin (admin.html) → Panel de administrador (si eres admin)
```

## ✨ Ejemplos de Uso

### Crear una Reserva:
1. Login con juan@example.com / 123456
2. Haz clic en "Reservar" en una cancha
3. Selecciona fecha y hora
4. Confirma

### Ver Panel Admin:
1. Login con admin@example.com / admin123
2. Haz clic en "Panel Admin"
3. Navega entre pestañas para ver diferentes secciones

## 🐛 Solución de Problemas

**P: No veo mis reservas después de crear una**
R: Verifica que hayas iniciado sesión y que estés en la página correcta.

**P: ¿Por qué se borran mis datos al cerrar el navegador?**
R: No se borran, están guardados en localStorage. Se borran solo si limpias el caché.

**P: ¿Cómo agrego más canchas?**
R: Abre la consola del navegador (F12) y ejecuta:
```javascript
const canchas = JSON.parse(localStorage.getItem('canchas'));
canchas.push({
  id: 6, 
  numero: '6', 
  tipo: '5 vs 5', 
  precio: 50, 
  estado: 'disponible', 
  tamaño: 'Pequeña'
});
localStorage.setItem('canchas', JSON.stringify(canchas));
```

## 📞 Soporte

Este es un sistema de demostración educativo. Para soporte o mejoras, revisa el código en los archivos incluidos.

---

**Versión**: 1.0  
**Última actualización**: 1 de Diciembre de 2025
