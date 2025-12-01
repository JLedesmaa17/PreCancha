<?php
require '../bd.php';

if (!isLogged() || !isAdmin()) redirect('../login.php');

// Obtener estadísticas
$total_usuarios = fetchOne("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'usuario'")['total'];
$total_canchas = fetchOne("SELECT COUNT(*) as total FROM canchas WHERE estado = 'disponible'")['total'];
$total_reservas = fetchOne("SELECT COUNT(*) as total FROM reservas WHERE estado IN ('pendiente', 'confirmada')")['total'];
$reservas_pendientes = fetchOne("SELECT COUNT(*) as total FROM reservas WHERE estado = 'pendiente'")['total'];

// Últimas reservas
$ultimas_reservas = fetchAll(
    "SELECT r.*, c.numero, u.nombre 
     FROM reservas r 
     JOIN canchas c ON r.cancha_id = c.id 
     JOIN usuarios u ON r.usuario_id = u.id 
     ORDER BY r.fecha_creacion DESC 
     LIMIT 5"
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - PRE-CANCHA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, sans-serif; 
            background: #f5f5f5; 
            color: #333; 
        }
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            height: 100vh;
            background: linear-gradient(135deg, #DAA520, #FFD700);
            padding: 20px 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.2);
            overflow-y: auto;
        }
        .sidebar h2 {
            color: #000;
            padding: 20px;
            text-align: center;
            border-bottom: 2px solid rgba(0,0,0,0.2);
            font-size: 18px;
        }
        .sidebar a {
            display: block;
            color: #000;
            padding: 15px 20px;
            text-decoration: none;
            border-left: 4px solid transparent;
            transition: 0.3s;
            font-weight: 500;
        }
        .sidebar a:hover {
            background: rgba(0,0,0,0.1);
            border-left-color: #000;
        }
        .sidebar a.active {
            background: rgba(0,0,0,0.2);
            border-left-color: #000;
        }
        .main {
            margin-left: 250px;
            padding: 30px;
        }
        header {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header h1 { color: #DAA520; font-size: 28px; }
        .logout-btn {
            padding: 10px 15px;
            background: #f44;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
        }
        .logout-btn:hover { background: #d32f2f; }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-top: 4px solid #DAA520;
        }
        .stat-card h3 { color: #666; font-size: 14px; margin-bottom: 10px; }
        .stat-number { font-size: 32px; font-weight: bold; color: #DAA520; }
        .stat-desc { font-size: 12px; color: #999; margin-top: 5px; }
        .content-card {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .content-card h2 { color: #DAA520; margin-bottom: 20px; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table th {
            background: #f5f5f5;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #DAA520;
            font-weight: bold;
            color: #333;
        }
        table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        table tr:hover { background: #f9f9f9; }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-pendiente { background: #fff3cd; color: #856404; }
        .badge-confirmada { background: #d4edda; color: #155724; }
        .badge-cancelada { background: #f8d7da; color: #721c24; }
        .action-btn {
            padding: 6px 12px;
            margin: 0 3px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
        }
        .btn-confirmar {
            background: #4CAF50;
            color: white;
        }
        .btn-confirmar:hover { background: #45a049; }
        .btn-rechazar {
            background: #f44;
            color: white;
        }
        .btn-rechazar:hover { background: #d32f2f; }
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert-info {
            background: #e3f2fd;
            border-left: 4px solid #2196F3;
            color: #1976D2;
        }
        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .main { margin-left: 0; }
            .stats-grid { grid-template-columns: 1fr; }
            header { flex-direction: column; gap: 15px; }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>⚙️ ADMIN</h2>
        <a href="index.php" class="active">📊 Dashboard</a>
        <a href="reservas.php">📅 Reservas</a>
        <a href="canchas.php">🏟️ Canchas</a>
        <a href="usuarios.php">👥 Usuarios</a>
        <a href="reportes.php">📈 Reportes</a>
        <a href="../logout.php">🚪 Salir</a>
    </div>

    <div class="main">
        <header>
            <div>
                <h1>🔧 Panel de Administración</h1>
                <p style="color: #999; font-size: 14px; margin-top: 5px;">Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></p>
            </div>
            <a href="../logout.php" class="logout-btn">Cerrar Sesión</a>
        </header>

        <div class="alert alert-info">
            💡 Panel de administración - Gestiona el sistema de reservas
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>👥 USUARIOS REGISTRADOS</h3>
                <div class="stat-number"><?php echo $total_usuarios; ?></div>
                <div class="stat-desc">Usuarios activos en el sistema</div>
            </div>

            <div class="stat-card">
                <h3>🏟️ CANCHAS DISPONIBLES</h3>
                <div class="stat-number"><?php echo $total_canchas; ?></div>
                <div class="stat-desc">Listas para reservar</div>
            </div>

            <div class="stat-card">
                <h3>📅 RESERVAS ACTIVAS</h3>
                <div class="stat-number"><?php echo $total_reservas; ?></div>
                <div class="stat-desc">Pendientes y confirmadas</div>
            </div>

            <div class="stat-card">
                <h3>⏳ PENDIENTES DE CONFIRMAR</h3>
                <div class="stat-number"><?php echo $reservas_pendientes; ?></div>
                <div class="stat-desc">Requieren acción</div>
            </div>
        </div>

        <div class="content-card">
            <h2>📋 Últimas Reservas</h2>
            
            <?php if (empty($ultimas_reservas)): ?>
                <p style="color: #999; text-align: center; padding: 20px;">No hay reservas aún</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Cancha</th>
                            <th>Fecha</th>
                            <th>Horario</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ultimas_reservas as $r): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($r['nombre']); ?></td>
                                <td>Cancha <?php echo $r['numero']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($r['fecha_reserva'])); ?></td>
                                <td><?php echo substr($r['hora_inicio'], 0, 5); ?> - <?php echo date('H:i', strtotime($r['hora_fin'])); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $r['estado']; ?>">
                                        <?php 
                                        $estados = ['pendiente' => 'Pendiente', 'confirmada' => 'Confirmada', 'cancelada' => 'Cancelada'];
                                        echo $estados[$r['estado']] ?? $r['estado'];
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($r['estado'] === 'pendiente'): ?>
                                        <button class="action-btn btn-confirmar" onclick="confirmarReserva(<?php echo $r['id']; ?>)">Confirmar</button>
                                        <button class="action-btn btn-rechazar" onclick="rechazarReserva(<?php echo $r['id']; ?>)">Rechazar</button>
                                    <?php else: ?>
                                        <span style="color: #999;">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function confirmarReserva(id) {
            if (confirm('¿Confirmar esta reserva?')) {
                window.location.href = 'reservas.php?accion=confirmar&id=' + id;
            }
        }

        function rechazarReserva(id) {
            if (confirm('¿Rechazar esta reserva?')) {
                window.location.href = 'reservas.php?accion=rechazar&id=' + id;
            }
        }
    </script>
</body>
</html>
