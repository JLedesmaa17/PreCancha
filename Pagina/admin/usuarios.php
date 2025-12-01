<?php
require '../bd.php';

if (!isLogged() || !isAdmin()) redirect('../login.php');

$usuarios = fetchAll("SELECT * FROM usuarios ORDER BY fecha_registro DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - PRE-CANCHA Admin</title>
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
        .content-card { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        table th { background: #f5f5f5; padding: 12px; text-align: left; border-bottom: 2px solid #DAA520; font-weight: bold; }
        table td { padding: 12px; border-bottom: 1px solid #ddd; }
        table tr:hover { background: #f9f9f9; }
        .badge { display: inline-block; padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .badge-admin { background: #c3e6cb; color: #155724; }
        .badge-usuario { background: #d1ecf1; color: #0c5460; }
        .badge-activo { background: #d4edda; color: #155724; }
        .badge-inactivo { background: #f8d7da; color: #721c24; }
        .no-data { text-align: center; padding: 40px; color: #999; }
        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .main { margin-left: 0; }
            table { font-size: 12px; }
            table td, table th { padding: 8px; }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>⚙️ ADMIN</h2>
        <a href="index.php">📊 Dashboard</a>
        <a href="reservas.php">📅 Reservas</a>
        <a href="canchas.php">🏟️ Canchas</a>
        <a href="usuarios.php" class="active">👥 Usuarios</a>
        <a href="reportes.php">📈 Reportes</a>
        <a href="../logout.php">🚪 Salir</a>
    </div>

    <div class="main">
        <header>
            <h1>👥 Gestión de Usuarios</h1>
            <a href="../logout.php" class="logout-btn">Cerrar Sesión</a>
        </header>

        <div class="content-card">
            <h2>Usuarios Registrados</h2>
            
            <?php if (empty($usuarios)): ?>
                <div class="no-data">
                    <p>No hay usuarios registrados</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Registrado</th>
                            <th>Último Acceso</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($usuario['nombre']); ?></strong></td>
                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['telefono']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo strtolower($usuario['rol']); ?>">
                                        <?php echo ucfirst($usuario['rol']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo strtolower($usuario['estado']); ?>">
                                        <?php echo ucfirst($usuario['estado']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($usuario['fecha_registro'])); ?></td>
                                <td>
                                    <?php 
                                    if ($usuario['ultimo_acceso']) {
                                        echo date('d/m/Y H:i', strtotime($usuario['ultimo_acceso']));
                                    } else {
                                        echo '<span style="color: #999;">Nunca</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div style="margin-top: 30px; background: #fff; padding: 20px; border-radius: 8px;">
            <h3 style="color: #DAA520; margin-bottom: 15px;">📊 Estadísticas</h3>
            
            <?php 
            $total_usuarios = count($usuarios);
            $admins = count(array_filter($usuarios, fn($u) => $u['rol'] === 'admin'));
            $usuarios_reg = $total_usuarios - $admins;
            $activos = count(array_filter($usuarios, fn($u) => $u['estado'] === 'activo'));
            $inactivos = $total_usuarios - $activos;
            ?>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div style="background: #f9f9f9; padding: 15px; border-radius: 5px; border-left: 4px solid #DAA520;">
                    <div style="font-size: 24px; font-weight: bold; color: #DAA520;"><?php echo $total_usuarios; ?></div>
                    <div style="font-size: 12px; color: #666;">Total de Usuarios</div>
                </div>
                <div style="background: #f9f9f9; padding: 15px; border-radius: 5px; border-left: 4px solid #4CAF50;">
                    <div style="font-size: 24px; font-weight: bold; color: #4CAF50;"><?php echo $usuarios_reg; ?></div>
                    <div style="font-size: 12px; color: #666;">Usuarios Regulares</div>
                </div>
                <div style="background: #f9f9f9; padding: 15px; border-radius: 5px; border-left: 4px solid #2196F3;">
                    <div style="font-size: 24px; font-weight: bold; color: #2196F3;"><?php echo $admins; ?></div>
                    <div style="font-size: 12px; color: #666;">Administradores</div>
                </div>
                <div style="background: #f9f9f9; padding: 15px; border-radius: 5px; border-left: 4px solid #FFC107;">
                    <div style="font-size: 24px; font-weight: bold; color: #FFC107;"><?php echo $activos; ?></div>
                    <div style="font-size: 12px; color: #666;">Usuarios Activos</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
