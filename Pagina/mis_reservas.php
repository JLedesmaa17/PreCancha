<?php
require 'bd.php';

if (!isLogged()) redirect('login.php');

// Obtener las reservas del usuario
$reservas = fetchAll(
    "SELECT r.*, c.numero, c.tipo, c.precio_hora 
     FROM reservas r 
     JOIN canchas c ON r.cancha_id = c.id 
     WHERE r.usuario_id = ? 
     ORDER BY r.fecha_reserva DESC, r.hora_inicio DESC",
    [$_SESSION['user_id']]
);

$error = '';
$success = '';

// Procesar cancelación de reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $accion = $_POST['accion'];
    $reserva_id = $_POST['reserva_id'] ?? null;
    
    if ($accion === 'cancelar' && $reserva_id) {
        // Verificar que la reserva pertenezca al usuario
        $reserva = fetchOne(
            "SELECT * FROM reservas WHERE id = ? AND usuario_id = ?",
            [$reserva_id, $_SESSION['user_id']]
        );
        
        if ($reserva) {
            if ($reserva['estado'] === 'cancelada') {
                $error = 'Esta reserva ya fue cancelada';
            } else {
                try {
                    query(
                        "UPDATE reservas SET estado = 'cancelada' WHERE id = ?",
                        [$reserva_id]
                    );
                    $success = 'Reserva cancelada exitosamente';
                    // Recargar reservas
                    $reservas = fetchAll(
                        "SELECT r.*, c.numero, c.tipo, c.precio_hora 
                         FROM reservas r 
                         JOIN canchas c ON r.cancha_id = c.id 
                         WHERE r.usuario_id = ? 
                         ORDER BY r.fecha_reserva DESC, r.hora_inicio DESC",
                        [$_SESSION['user_id']]
                    );
                } catch (Exception $e) {
                    $error = 'Error al cancelar la reserva';
                }
            }
        } else {
            $error = 'Reserva no encontrada';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reservas - PRE-CANCHA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #000, #1a1a1a);
            color: #fff;
            min-height: 100vh;
            padding: 0;
        }
        header {
            background: linear-gradient(135deg, #DAA520, #FFD700);
            color: #000;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(218, 165, 32, 0.4);
        }
        header h1 { font-size: 36px; font-weight: bold; }
        header p { font-size: 14px; margin-top: 5px; opacity: 0.9; }
        .navbar {
            background: #1a1a1a;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            border-bottom: 2px solid #DAA520;
        }
        .nav-left a {
            color: #fff;
            text-decoration: none;
            margin-right: 20px;
            padding: 12px 0;
            display: inline-block;
            border-bottom: 3px solid transparent;
            transition: 0.3s;
        }
        .nav-left a:hover { border-bottom-color: #DAA520; color: #DAA520; }
        .nav-right {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .nav-right a, .nav-right button {
            padding: 10px 15px;
            border-radius: 5px;
            border: 2px solid #DAA520;
            background: transparent;
            color: #fff;
            cursor: pointer;
            text-decoration: none;
            transition: 0.3s;
            font-size: 14px;
        }
        .nav-right a:hover, .nav-right button:hover {
            background: #DAA520;
            color: #000;
        }
        .container { max-width: 1000px; margin: 0 auto; padding: 30px 20px; }
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .alert-success { background: rgba(0, 200, 0, 0.2); border-left: 5px solid #0c0; color: #0c0; }
        .alert-error { background: rgba(255, 68, 68, 0.2); border-left: 5px solid #f44; color: #f44; }
        h2 { color: #DAA520; margin-bottom: 20px; }
        .reservas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .reserva-card {
            background: #1a1a1a;
            border: 2px solid #DAA520;
            border-radius: 12px;
            padding: 20px;
            transition: 0.3s;
        }
        .reserva-card:hover {
            border-color: #FFD700;
            box-shadow: 0 8px 25px rgba(218, 165, 32, 0.4);
        }
        .reserva-numero {
            font-size: 28px;
            font-weight: bold;
            color: #DAA520;
            margin-bottom: 10px;
        }
        .reserva-detalle {
            font-size: 14px;
            color: #bbb;
            margin: 8px 0;
        }
        .reserva-detalle strong { color: #FFD700; }
        .estado {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            margin-top: 10px;
            font-weight: bold;
            font-size: 12px;
        }
        .estado-pendiente {
            background: rgba(255, 193, 7, 0.3);
            color: #FFC107;
            border: 1px solid #FFC107;
        }
        .estado-confirmada {
            background: rgba(76, 175, 80, 0.3);
            color: #4CAF50;
            border: 1px solid #4CAF50;
        }
        .estado-cancelada {
            background: rgba(244, 67, 54, 0.3);
            color: #F44336;
            border: 1px solid #F44336;
        }
        .btn-cancelar {
            width: 100%;
            padding: 10px;
            background: #f44;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 15px;
            transition: 0.3s;
        }
        .btn-cancelar:hover {
            background: #d32f2f;
        }
        .btn-cancelar:disabled {
            background: #999;
            cursor: not-allowed;
        }
        .no-reservas {
            text-align: center;
            padding: 50px 20px;
            color: #999;
        }
        .no-reservas p {
            font-size: 18px;
            margin-bottom: 15px;
        }
        .no-reservas a {
            color: #DAA520;
            text-decoration: none;
            font-weight: bold;
        }
        .no-reservas a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1>⚽ PRE-CANCHA</h1>
        <p>Mis Reservas</p>
    </header>

    <div class="navbar">
        <div class="nav-left">
            <a href="index.php">Inicio</a>
            <a href="mis_reservas.php">Mis Reservas</a>
        </div>
        <div class="nav-right">
            <span><?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
            <a href="logout.php">Cerrar Sesión</a>
        </div>
    </div>

    <div class="container">
        <?php if ($success): ?>
            <div class="alert alert-success">✓ <?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">✗ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <h2>📋 Tus Reservas</h2>

        <?php if (empty($reservas)): ?>
            <div class="no-reservas">
                <p>No tienes reservas aún</p>
                <p style="font-size: 14px; color: #666;">Ve a la página de inicio para hacer una reserva</p>
                <a href="index.php">← Volver al inicio</a>
            </div>
        <?php else: ?>
            <div class="reservas-grid">
                <?php foreach ($reservas as $reserva): ?>
                    <div class="reserva-card">
                        <div class="reserva-numero">Cancha <?php echo $reserva['numero']; ?></div>
                        <div class="reserva-detalle"><strong>⚽ Tipo:</strong> <?php echo htmlspecialchars($reserva['tipo']); ?></div>
                        <div class="reserva-detalle"><strong>📅 Fecha:</strong> <?php echo date('d/m/Y', strtotime($reserva['fecha_reserva'])); ?></div>
                        <div class="reserva-detalle"><strong>⏰ Horario:</strong> <?php echo substr($reserva['hora_inicio'], 0, 5); ?> - <?php echo date('H:i', strtotime($reserva['hora_fin'])); ?></div>
                        <div class="reserva-detalle"><strong>💰 Precio:</strong> $<?php echo number_format($reserva['monto'], 2); ?></div>
                        
                        <?php if ($reserva['observaciones']): ?>
                            <div class="reserva-detalle"><strong>📝 Notas:</strong> <?php echo htmlspecialchars($reserva['observaciones']); ?></div>
                        <?php endif; ?>
                        
                        <span class="estado estado-<?php echo $reserva['estado']; ?>">
                            <?php 
                            $estados = ['pendiente' => '⏳ Pendiente', 'confirmada' => '✓ Confirmada', 'cancelada' => '✗ Cancelada'];
                            echo $estados[$reserva['estado']] ?? $reserva['estado'];
                            ?>
                        </span>
                        
                        <?php if ($reserva['estado'] !== 'cancelada'): ?>
                            <form method="POST" style="display: inline-block; width: 100%;">
                                <input type="hidden" name="accion" value="cancelar">
                                <input type="hidden" name="reserva_id" value="<?php echo $reserva['id']; ?>">
                                <button type="submit" class="btn-cancelar" onclick="return confirm('¿Estás seguro de que deseas cancelar esta reserva?')">Cancelar Reserva</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
