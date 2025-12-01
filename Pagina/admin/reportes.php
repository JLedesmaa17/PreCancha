<?php
require '../bd.php';

if (!isLogged() || !isAdmin()) redirect('../login.php');

// Estadísticas generales
$total_reservas = fetchOne("SELECT COUNT(*) as total FROM reservas")['total'];
$ingresos_totales = fetchOne("SELECT SUM(monto) as total FROM reservas WHERE estado IN ('pendiente', 'confirmada')")['total'] ?? 0;
$promedio_reservas = fetchOne("SELECT AVG(monto) as promedio FROM reservas WHERE estado IN ('pendiente', 'confirmada')")['promedio'] ?? 0;

// Reservas por estado
$reservas_estado = fetchAll(
    "SELECT estado, COUNT(*) as cantidad FROM reservas GROUP BY estado"
);

// Canchas más reservadas
$canchas_top = fetchAll(
    "SELECT c.numero, c.tipo, COUNT(r.id) as total_reservas, SUM(r.monto) as ingresos
     FROM canchas c
     LEFT JOIN reservas r ON c.id = r.cancha_id
     GROUP BY c.id
     ORDER BY total_reservas DESC
     LIMIT 5"
);

// Usuarios más activos
$usuarios_top = fetchAll(
    "SELECT u.nombre, u.email, COUNT(r.id) as total_reservas
     FROM usuarios u
     LEFT JOIN reservas r ON u.id = r.usuario_id
     WHERE u.rol = 'usuario'
     GROUP BY u.id
     ORDER BY total_reservas DESC
     LIMIT 5"
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - PRE-CANCHA Admin</title>
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
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-top: 4px solid #DAA520; }
        .stat-card h3 { color: #666; font-size: 14px; margin-bottom: 10px; }
        .stat-number { font-size: 32px; font-weight: bold; color: #DAA520; }
        .content-card { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px; }
        .content-card h2 { color: #DAA520; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        table th { background: #f5f5f5; padding: 12px; text-align: left; border-bottom: 2px solid #DAA520; font-weight: bold; }
        table td { padding: 12px; border-bottom: 1px solid #ddd; }
        table tr:hover { background: #f9f9f9; }
        .badge { display: inline-block; padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .badge-pendiente { background: #fff3cd; color: #856404; }
        .badge-confirmada { background: #d4edda; color: #155724; }
        .badge-cancelada { background: #f8d7da; color: #721c24; }
        .no-data { text-align: center; padding: 40px; color: #999; }
        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .main { margin-left: 0; }
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>⚙️ ADMIN</h2>
        <a href="index.php">📊 Dashboard</a>
        <a href="reservas.php">📅 Reservas</a>
        <a href="canchas.php">🏟️ Canchas</a>
        <a href="usuarios.php">👥 Usuarios</a>
        <a href="reportes.php" class="active">📈 Reportes</a>
        <a href="../logout.php">🚪 Salir</a>
    </div>

    <div class="main">
        <header>
            <h1>📈 Reportes y Estadísticas</h1>
            <a href="../logout.php" class="logout-btn">Cerrar Sesión</a>
        </header>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>📊 TOTAL DE RESERVAS</h3>
                <div class="stat-number"><?php echo $total_reservas; ?></div>
            </div>

            <div class="stat-card">
                <h3>💰 INGRESOS TOTALES</h3>
                <div class="stat-number">$<?php echo number_format($ingresos_totales, 0); ?></div>
            </div>

            <div class="stat-card">
                <h3>📈 PROMEDIO POR RESERVA</h3>
                <div class="stat-number">$<?php echo number_format($promedio_reservas, 0); ?></div>
            </div>
        </div>

        <div class="content-card">
            <h2>Reservas por Estado</h2>
            <?php if (empty($reservas_estado)): ?>
                <div class="no-data">No hay datos</div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Estado</th>
                            <th>Cantidad</th>
                            <th>Porcentaje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = array_sum(array_column($reservas_estado, 'cantidad'));
                        foreach ($reservas_estado as $estado): 
                            $porcentaje = $total > 0 ? ($estado['cantidad'] / $total * 100) : 0;
                        ?>
                            <tr>
                                <td>
                                    <span class="badge badge-<?php echo $estado['estado']; ?>">
                                        <?php echo ucfirst($estado['estado']); ?>
                                    </span>
                                </td>
                                <td><?php echo $estado['cantidad']; ?></td>
                                <td><?php echo number_format($porcentaje, 1); ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="content-card">
            <h2>🏆 Canchas Más Reservadas</h2>
            <?php if (empty($canchas_top)): ?>
                <div class="no-data">No hay datos</div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Cancha</th>
                            <th>Tipo</th>
                            <th>Total Reservas</th>
                            <th>Ingresos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($canchas_top as $cancha): ?>
                            <tr>
                                <td><strong>Cancha <?php echo $cancha['numero']; ?></strong></td>
                                <td><?php echo htmlspecialchars($cancha['tipo']); ?></td>
                                <td><?php echo $cancha['total_reservas']; ?></td>
                                <td>$<?php echo number_format($cancha['ingresos'] ?? 0, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="content-card">
            <h2>🎖️ Usuarios Más Activos</h2>
            <?php if (empty($usuarios_top)): ?>
                <div class="no-data">No hay datos</div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Total Reservas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios_top as $usuario): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($usuario['nombre']); ?></strong></td>
                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td><?php echo $usuario['total_reservas']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
