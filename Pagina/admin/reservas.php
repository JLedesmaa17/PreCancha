<?php
require '../bd.php';

if (!isLogged() || !isAdmin()) redirect('../login.php');

$accion = $_GET['accion'] ?? null;
$reserva_id = $_GET['id'] ?? null;
$filtro = $_GET['filtro'] ?? 'todas';

// Procesar acciones
if ($accion && $reserva_id) {
    if ($accion === 'confirmar') {
        query("UPDATE reservas SET estado = 'confirmada' WHERE id = ?", [$reserva_id]);
        $_GET['mensaje'] = 'Reserva confirmada';
    } elseif ($accion === 'rechazar') {
        query("UPDATE reservas SET estado = 'rechazada' WHERE id = ?", [$reserva_id]);
        $_GET['mensaje'] = 'Reserva rechazada';
    } elseif ($accion === 'cancelar') {
        query("UPDATE reservas SET estado = 'cancelada' WHERE id = ?", [$reserva_id]);
        $_GET['mensaje'] = 'Reserva cancelada';
    }
}

// Obtener reservas según filtro
$query_filtro = "SELECT r.*, c.numero, u.nombre, u.email 
                 FROM reservas r 
                 JOIN canchas c ON r.cancha_id = c.id 
                 JOIN usuarios u ON r.usuario_id = u.id";

if ($filtro === 'pendientes') {
    $query_filtro .= " WHERE r.estado = 'pendiente'";
} elseif ($filtro === 'confirmadas') {
    $query_filtro .= " WHERE r.estado = 'confirmada'";
}

$query_filtro .= " ORDER BY r.fecha_creacion DESC";
$reservas = fetchAll($query_filtro);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Reservas - PRE-CANCHA Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; color: #333; }
        .sidebar {
            position: fixed; left: 0; top: 0; width: 250px; height: 100vh;
            background: linear-gradient(135deg, #DAA520, #FFD700);
            padding: 20px 0; box-shadow: 2px 0 10px rgba(0,0,0,0.2); overflow-y: auto;
        }
        .sidebar h2 { color: #000; padding: 20px; text-align: center; border-bottom: 2px solid rgba(0,0,0,0.2); }
        .sidebar a {
            display: block; color: #000; padding: 15px 20px; text-decoration: none;
            border-left: 4px solid transparent; transition: 0.3s; font-weight: 500;
        }
        .sidebar a:hover { background: rgba(0,0,0,0.1); border-left-color: #000; }
        .sidebar a.active { background: rgba(0,0,0,0.2); border-left-color: #000; }
        .main { margin-left: 250px; padding: 30px; }
        header { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px; display: flex; justify-content: space-between; }
        header h1 { color: #DAA520; }
        .logout-btn { padding: 10px 15px; background: #f44; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; font-weight: bold; }
        .logout-btn:hover { background: #d32f2f; }
        .filters { background: #fff; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; gap: 10px; }
        .filters a {
            padding: 10px 15px; border-radius: 5px; text-decoration: none; font-weight: bold;
            background: #f0f0f0; color: #333; transition: 0.3s;
        }
        .filters a:hover, .filters a.active { background: #DAA520; color: white; }
        .content-card { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        table th { background: #f5f5f5; padding: 12px; text-align: left; border-bottom: 2px solid #DAA520; font-weight: bold; }
        table td { padding: 12px; border-bottom: 1px solid #ddd; }
        table tr:hover { background: #f9f9f9; }
        .badge { display: inline-block; padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .badge-pendiente { background: #fff3cd; color: #856404; }
        .badge-confirmada { background: #d4edda; color: #155724; }
        .badge-rechazada { background: #f8d7da; color: #721c24; }
        .badge-cancelada { background: #f8d7da; color: #721c24; }
        .action-btn {
            padding: 6px 12px; margin: 0 3px; border: none; border-radius: 4px;
            cursor: pointer; font-size: 12px; font-weight: bold;
        }
        .btn-confirmar { background: #4CAF50; color: white; }
        .btn-confirmar:hover { background: #45a049; }
        .btn-rechazar { background: #f44; color: white; }
        .btn-rechazar:hover { background: #d32f2f; }
        .btn-cancelar { background: #ff9800; color: white; }
        .btn-cancelar:hover { background: #e68900; }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; border-left: 4px solid #28a745; color: #155724; }
        .no-data { text-align: center; padding: 40px; color: #999; }
        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .main { margin-left: 0; }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>⚙️ ADMIN</h2>
        <a href="index.php">📊 Dashboard</a>
        <a href="reservas.php" class="active">📅 Reservas</a>
        <a href="canchas.php">🏟️ Canchas</a>
        <a href="usuarios.php">👥 Usuarios</a>
        <a href="reportes.php">📈 Reportes</a>
        <a href="../logout.php">🚪 Salir</a>
    </div>

    <div class="main">
        <header>
            <h1>📅 Gestión de Reservas</h1>
            <a href="../logout.php" class="logout-btn">Cerrar Sesión</a>
        </header>

        <?php if (isset($_GET['mensaje'])): ?>
            <div class="alert alert-success">✓ <?php echo htmlspecialchars($_GET['mensaje']); ?></div>
        <?php endif; ?>

        <div class="filters">
            <a href="reservas.php" class="<?php echo $filtro === 'todas' ? 'active' : ''; ?>">Todas</a>
            <a href="reservas.php?filtro=pendientes" class="<?php echo $filtro === 'pendientes' ? 'active' : ''; ?>">Pendientes</a>
            <a href="reservas.php?filtro=confirmadas" class="<?php echo $filtro === 'confirmadas' ? 'active' : ''; ?>">Confirmadas</a>
        </div>

        <div class="content-card">
            <?php if (empty($reservas)): ?>
                <div class="no-data">
                    <p>No hay reservas para mostrar</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Cancha</th>
                            <th>Fecha</th>
                            <th>Horario</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservas as $r): ?>
                            <tr>
                                <td><strong>#<?php echo $r['id']; ?></strong></td>
                                <td><?php echo htmlspecialchars($r['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($r['email']); ?></td>
                                <td>Cancha <?php echo $r['numero']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($r['fecha_reserva'])); ?></td>
                                <td><?php echo substr($r['hora_inicio'], 0, 5); ?> - <?php echo date('H:i', strtotime($r['hora_fin'])); ?></td>
                                <td>$<?php echo number_format($r['monto'], 2); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $r['estado']; ?>">
                                        <?php 
                                        $estados = ['pendiente' => 'Pendiente', 'confirmada' => 'Confirmada', 'rechazada' => 'Rechazada', 'cancelada' => 'Cancelada'];
                                        echo $estados[$r['estado']] ?? $r['estado'];
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($r['estado'] === 'pendiente'): ?>
                                        <a href="reservas.php?accion=confirmar&id=<?php echo $r['id']; ?>" class="action-btn btn-confirmar" onclick="return confirm('¿Confirmar?')">Confirmar</a>
                                        <a href="reservas.php?accion=rechazar&id=<?php echo $r['id']; ?>" class="action-btn btn-rechazar" onclick="return confirm('¿Rechazar?')">Rechazar</a>
                                    <?php elseif ($r['estado'] === 'confirmada'): ?>
                                        <a href="reservas.php?accion=cancelar&id=<?php echo $r['id']; ?>" class="action-btn btn-cancelar" onclick="return confirm('¿Cancelar?')">Cancelar</a>
                                    <?php else: ?>
                                        <span style="color: #999;">Sin acciones</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
