<?php
require '../bd.php';

if (!isLogged() || !isAdmin()) redirect('../login.php');

$canchas = fetchAll("SELECT * FROM canchas ORDER BY numero");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? null;
    $id = $_POST['id'] ?? null;
    
    if ($accion === 'cambiar_estado' && $id) {
        $nuevo_estado = $_POST['estado'] ?? 'disponible';
        query("UPDATE canchas SET estado = ? WHERE id = ?", [$nuevo_estado, $id]);
        header("Location: canchas.php?mensaje=Cancha actualizada");
        exit;
    } elseif ($accion === 'actualizar_precio' && $id) {
        $nuevo_precio = $_POST['precio'] ?? 0;
        query("UPDATE canchas SET precio_hora = ? WHERE id = ?", [$nuevo_precio, $id]);
        header("Location: canchas.php?mensaje=Precio actualizado");
        exit;
    }
}

$canchas = fetchAll("SELECT * FROM canchas ORDER BY numero");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Canchas - PRE-CANCHA Admin</title>
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
        .canchas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .cancha-card {
            border: 2px solid #DAA520;
            border-radius: 8px;
            padding: 20px;
            background: #f9f9f9;
        }
        .cancha-numero { font-size: 24px; font-weight: bold; color: #DAA520; margin-bottom: 10px; }
        .cancha-info { font-size: 14px; margin: 8px 0; color: #666; }
        .cancha-info strong { color: #333; }
        .form-group { margin: 15px 0; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        .form-group select,
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .form-group select:focus,
        .form-group input:focus {
            outline: none;
            border-color: #DAA520;
            box-shadow: 0 0 5px rgba(218,165,32,0.3);
        }
        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            font-size: 12px;
        }
        .btn-guardar {
            background: #4CAF50;
            color: white;
            width: 100%;
            margin-top: 10px;
        }
        .btn-guardar:hover { background: #45a049; }
        .estado-disponible { color: #28a745; font-weight: bold; }
        .estado-mantenimiento { color: #ffc107; font-weight: bold; }
        .estado-inactiva { color: #dc3545; font-weight: bold; }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; border-left: 4px solid #28a745; color: #155724; }
        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .main { margin-left: 0; }
            .canchas-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>⚙️ ADMIN</h2>
        <a href="index.php">📊 Dashboard</a>
        <a href="reservas.php">📅 Reservas</a>
        <a href="canchas.php" class="active">🏟️ Canchas</a>
        <a href="usuarios.php">👥 Usuarios</a>
        <a href="reportes.php">📈 Reportes</a>
        <a href="../logout.php">🚪 Salir</a>
    </div>

    <div class="main">
        <header>
            <h1>🏟️ Gestión de Canchas</h1>
            <a href="../logout.php" class="logout-btn">Cerrar Sesión</a>
        </header>

        <?php if (isset($_GET['mensaje'])): ?>
            <div class="alert alert-success">✓ <?php echo htmlspecialchars($_GET['mensaje']); ?></div>
        <?php endif; ?>

        <div class="content-card">
            <h2>Canchas Disponibles</h2>
            
            <?php if (empty($canchas)): ?>
                <p style="text-align: center; padding: 20px; color: #999;">No hay canchas registradas</p>
            <?php else: ?>
                <div class="canchas-grid">
                    <?php foreach ($canchas as $cancha): ?>
                        <div class="cancha-card">
                            <div class="cancha-numero">Cancha <?php echo $cancha['numero']; ?></div>
                            
                            <div class="cancha-info">
                                <strong>Tipo:</strong> <?php echo htmlspecialchars($cancha['tipo']); ?>
                            </div>
                            <div class="cancha-info">
                                <strong>Jugadores:</strong> <?php echo $cancha['jugadores']; ?>
                            </div>
                            <div class="cancha-info">
                                <strong>Superficie:</strong> <?php echo htmlspecialchars($cancha['superficie']); ?>
                            </div>
                            <div class="cancha-info">
                                <strong>Precio/h:</strong> $<?php echo number_format($cancha['precio_hora'], 2); ?>
                            </div>
                            <div class="cancha-info">
                                <strong>Estado:</strong>
                                <span class="estado-<?php echo $cancha['estado']; ?>">
                                    <?php echo ucfirst($cancha['estado']); ?>
                                </span>
                            </div>

                            <form method="POST" style="margin-top: 15px;">
                                <input type="hidden" name="accion" value="cambiar_estado">
                                <input type="hidden" name="id" value="<?php echo $cancha['id']; ?>">
                                <div class="form-group">
                                    <label>Estado:</label>
                                    <select name="estado">
                                        <option value="disponible" <?php echo $cancha['estado'] === 'disponible' ? 'selected' : ''; ?>>Disponible</option>
                                        <option value="mantenimiento" <?php echo $cancha['estado'] === 'mantenimiento' ? 'selected' : ''; ?>>Mantenimiento</option>
                                        <option value="inactiva" <?php echo $cancha['estado'] === 'inactiva' ? 'selected' : ''; ?>>Inactiva</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-guardar">Actualizar Estado</button>
                            </form>

                            <form method="POST" style="margin-top: 15px;">
                                <input type="hidden" name="accion" value="actualizar_precio">
                                <input type="hidden" name="id" value="<?php echo $cancha['id']; ?>">
                                <div class="form-group">
                                    <label>Precio por hora ($):</label>
                                    <input type="number" name="precio" value="<?php echo $cancha['precio_hora']; ?>" step="0.01" min="0">
                                </div>
                                <button type="submit" class="btn btn-guardar">Actualizar Precio</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
