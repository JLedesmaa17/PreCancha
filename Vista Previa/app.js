// ============================================
// Gestión de Sesión y Datos en localStorage
// ============================================

class SessionManager {
    constructor() {
        this.initData();
    }

    initData() {
        // Inicializar datos si no existen
        if (!localStorage.getItem('usuarios')) {
            const usuarios = [
                { id: 1, nombre: 'Juan Pérez', email: 'juan@example.com', password: '123456', rol: 'usuario' },
                { id: 2, nombre: 'Admin System', email: 'admin@example.com', password: 'admin123', rol: 'admin' },
                { id: 3, nombre: 'Carlos López', email: 'carlos@example.com', password: '123456', rol: 'usuario' }
            ];
            localStorage.setItem('usuarios', JSON.stringify(usuarios));
        }

        if (!localStorage.getItem('canchas')) {
            const canchas = [
                { id: 1, numero: '1', tipo: '5 vs 5', precio: 50, estado: 'disponible', tamaño: 'Pequeña' },
                { id: 2, numero: '2', tipo: '7 vs 7', precio: 70, estado: 'disponible', tamaño: 'Mediana' },
                { id: 3, numero: '3', tipo: '11 vs 11', precio: 100, estado: 'disponible', tamaño: 'Grande' },
                { id: 4, numero: '4', tipo: '5 vs 5', precio: 50, estado: 'disponible', tamaño: 'Pequeña' },
                { id: 5, numero: '5', tipo: '7 vs 7', precio: 70, estado: 'disponible', tamaño: 'Mediana' }
            ];
            localStorage.setItem('canchas', JSON.stringify(canchas));
        }

        if (!localStorage.getItem('reservas')) {
            const reservas = [
                { id: 1, usuario_id: 1, cancha_id: 1, fecha: '2025-12-10', hora_inicio: '10:00', hora_fin: '11:00', estado: 'confirmada', observaciones: 'Reserva confirmada' },
                { id: 2, usuario_id: 1, cancha_id: 2, fecha: '2025-12-12', hora_inicio: '16:00', hora_fin: '17:00', estado: 'pendiente', observaciones: 'Esperando aprobación' },
                { id: 3, usuario_id: 3, cancha_id: 3, fecha: '2025-12-15', hora_inicio: '14:00', hora_fin: '15:00', estado: 'pendiente', observaciones: '' }
            ];
            localStorage.setItem('reservas', JSON.stringify(reservas));
        }
    }

    login(email, password) {
        const usuarios = JSON.parse(localStorage.getItem('usuarios'));
        const usuario = usuarios.find(u => u.email === email && u.password === password);
        
        if (usuario) {
            const sesion = {
                user_id: usuario.id,
                nombre: usuario.nombre,
                email: usuario.email,
                rol: usuario.rol,
                timestamp: new Date().toISOString()
            };
            localStorage.setItem('session', JSON.stringify(sesion));
            return true;
        }
        return false;
    }

    logout() {
        localStorage.removeItem('session');
    }

    isLogged() {
        return localStorage.getItem('session') !== null;
    }

    getSession() {
        return JSON.parse(localStorage.getItem('session')) || null;
    }

    // Métodos para canchas
    getCanchas() {
        return JSON.parse(localStorage.getItem('canchas')) || [];
    }

    getCancha(id) {
        const canchas = this.getCanchas();
        return canchas.find(c => c.id === parseInt(id));
    }

    // Métodos para reservas
    getReservas() {
        return JSON.parse(localStorage.getItem('reservas')) || [];
    }

    getReservasByUsuario(usuario_id) {
        const reservas = this.getReservas();
        return reservas.filter(r => r.usuario_id === usuario_id);
    }

    getReservasByCancha(cancha_id) {
        const reservas = this.getReservas();
        return reservas.filter(r => r.cancha_id === parseInt(cancha_id));
    }

    crearReserva(cancha_id, fecha, hora_inicio, observaciones = '') {
        const reservas = this.getReservas();
        const session = this.getSession();
        
        // Verificar que no exista reserva en ese horario
        const existe = reservas.find(r => 
            r.cancha_id === parseInt(cancha_id) && 
            r.fecha === fecha && 
            r.hora_inicio === hora_inicio &&
            (r.estado === 'pendiente' || r.estado === 'confirmada')
        );

        if (existe) {
            return { success: false, message: 'Ya existe una reserva en ese horario' };
        }

        const nuevaReserva = {
            id: Math.max(...reservas.map(r => r.id), 0) + 1,
            usuario_id: session.user_id,
            cancha_id: parseInt(cancha_id),
            fecha: fecha,
            hora_inicio: hora_inicio,
            hora_fin: calcularHoraFin(hora_inicio),
            estado: 'pendiente',
            observaciones: observaciones,
            fecha_creacion: new Date().toISOString()
        };

        reservas.push(nuevaReserva);
        localStorage.setItem('reservas', JSON.stringify(reservas));
        return { success: true, message: 'Reserva creada exitosamente', id: nuevaReserva.id };
    }

    cancelarReserva(reserva_id) {
        const reservas = this.getReservas();
        const indice = reservas.findIndex(r => r.id === parseInt(reserva_id));
        
        if (indice !== -1) {
            reservas[indice].estado = 'cancelada';
            localStorage.setItem('reservas', JSON.stringify(reservas));
            return { success: true, message: 'Reserva cancelada exitosamente' };
        }
        return { success: false, message: 'Reserva no encontrada' };
    }

    // Métodos para Admin
    obtenerEstadisticas() {
        const reservas = this.getReservas();
        const usuarios = JSON.parse(localStorage.getItem('usuarios')) || [];
        const canchas = this.getCanchas();

        return {
            totalReservas: reservas.length,
            reservasConfirmadas: reservas.filter(r => r.estado === 'confirmada').length,
            reservasCanceladas: reservas.filter(r => r.estado === 'cancelada').length,
            totalUsuarios: usuarios.length,
            totalCanchas: canchas.length,
            ingresoTotal: reservas
                .filter(r => r.estado === 'confirmada')
                .reduce((total, r) => {
                    const cancha = canchas.find(c => c.id === r.cancha_id);
                    return total + (cancha ? cancha.precio : 0);
                }, 0)
        };
    }

    obtenerTodasLasReservas() {
        const reservas = this.getReservas();
        const usuarios = JSON.parse(localStorage.getItem('usuarios')) || [];
        const canchas = this.getCanchas();

        return reservas.map(r => ({
            ...r,
            usuario_nombre: usuarios.find(u => u.id === r.usuario_id)?.nombre || 'Desconocido',
            cancha_numero: canchas.find(c => c.id === r.cancha_id)?.numero || 'N/A'
        }));
    }

    obtenerTodosLosUsuarios() {
        return JSON.parse(localStorage.getItem('usuarios')) || [];
    }

    // Obtener solicitudes pendientes (para admin)
    obtenerSolicitudesPendientes() {
        const reservas = this.getReservas();
        const usuarios = this.obtenerTodosLosUsuarios();
        const canchas = this.getCanchas();

        return reservas
            .filter(r => r.estado === 'pendiente')
            .map(r => ({
                ...r,
                usuario_nombre: usuarios.find(u => u.id === r.usuario_id)?.nombre || 'Desconocido',
                usuario_email: usuarios.find(u => u.id === r.usuario_id)?.email || 'N/A',
                cancha_numero: canchas.find(c => c.id === r.cancha_id)?.numero || 'N/A',
                cancha_precio: canchas.find(c => c.id === r.cancha_id)?.precio || 0
            }));
    }

    // Aprobar solicitud (cambiar estado a confirmada)
    aprobarSolicitud(reserva_id) {
        const reservas = this.getReservas();
        const indice = reservas.findIndex(r => r.id === parseInt(reserva_id));
        
        if (indice !== -1 && reservas[indice].estado === 'pendiente') {
            reservas[indice].estado = 'confirmada';
            localStorage.setItem('reservas', JSON.stringify(reservas));
            return { success: true, message: 'Solicitud aprobada' };
        }
        return { success: false, message: 'No se pudo aprobar la solicitud' };
    }

    // Rechazar solicitud
    rechazarSolicitud(reserva_id) {
        const reservas = this.getReservas();
        const indice = reservas.findIndex(r => r.id === parseInt(reserva_id));
        
        if (indice !== -1 && reservas[indice].estado === 'pendiente') {
            reservas[indice].estado = 'rechazada';
            localStorage.setItem('reservas', JSON.stringify(reservas));
            return { success: true, message: 'Solicitud rechazada' };
        }
        return { success: false, message: 'No se pudo rechazar la solicitud' };
    }
}

// Crear instancia global
const session = new SessionManager();

// Función auxiliar
function calcularHoraFin(hora_inicio) {
    const [horas, minutos] = hora_inicio.split(':').map(Number);
    const fin = new Date(0, 0, 0, horas + 1, minutos);
    return fin.toTimeString().slice(0, 5);
}

// Actualizar navbar según sesión
function actualizarNavbar() {
    const loginLink = document.getElementById('loginLink');
    const userInfo = document.getElementById('userInfo');
    const logoutBtn = document.getElementById('logoutBtn');
    const adminLink = document.getElementById('adminLink');

    if (session.isLogged()) {
        const sesion = session.getSession();
        if (loginLink) loginLink.style.display = 'none';
        if (userInfo) {
            userInfo.style.display = 'inline';
            userInfo.innerHTML = `
                <span>${sesion.nombre}</span>
                ${sesion.rol === 'admin' ? '<span style="color: #ff4444; margin-left: 10px;">[ADMIN]</span>' : ''}
            `;
        }
        if (logoutBtn) logoutBtn.style.display = 'inline-block';
        if (adminLink && sesion.rol === 'admin') adminLink.style.display = 'inline';
    } else {
        if (loginLink) loginLink.style.display = 'inline';
        if (userInfo) userInfo.style.display = 'none';
        if (logoutBtn) logoutBtn.style.display = 'none';
        if (adminLink) adminLink.style.display = 'none';
    }
}

// Logout
function doLogout() {
    session.logout();
    window.location.href = 'index.html';
}

// Ejecutar al cargar la página
document.addEventListener('DOMContentLoaded', actualizarNavbar);
