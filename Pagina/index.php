<?php
require 'bd.php';

$message = $_GET['logout'] ?? null;
$error = $_GET['error'] ?? null;

// Obtener todas las canchas disponibles
$canchas = fetchAll("SELECT * FROM canchas WHERE estado = 'disponible' ORDER BY numero");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRE-CANCHA - Sistema de Reservas</title>
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
        .container { max-width: 1200px; margin: 0 auto; padding: 30px 20px; }
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .alert-success { background: rgba(0, 200, 0, 0.2); border-left: 5px solid #0c0; color: #0c0; }
        .alert-error { background: rgba(255, 68, 68, 0.2); border-left: 5px solid #f44; color: #f44; }
        .bienvenida {
            background: rgba(218, 165, 32, 0.15);
            border: 2px solid #DAA520;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            text-align: center;
        }
        .bienvenida h2 { color: #DAA520; margin-bottom: 10px; }
        .bienvenida p { font-size: 16px; }
        .usuario-info {
            color: #FFD700;
            font-weight: bold;
        }
        .grid-canchas {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        .cancha-card {
            background: #1a1a1a;
            border: 2px solid #DAA520;
            border-radius: 12px;
            padding: 20px;
            transition: 0.3s;
            cursor: pointer;
        }
        .cancha-card:hover {
            border-color: #FFD700;
            box-shadow: 0 8px 25px rgba(218, 165, 32, 0.4);
            transform: translateY(-5px);
        }
        .cancha-numero {
            font-size: 32px;
            font-weight: bold;
            color: #DAA520;
            margin-bottom: 10px;
        }
        .cancha-tipo {
            color: #FFD700;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .cancha-detalle {
            font-size: 14px;
            color: #bbb;
            margin: 8px 0;
        }
        .cancha-precio {
            font-size: 20px;
            color: #0f0;
            font-weight: bold;
            margin-top: 15px;
        }
        .btn-reservar {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #DAA520, #FFD700);
            color: #000;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 15px;
            transition: 0.3s;
        }
        .btn-reservar:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 20px rgba(218, 165, 32, 0.5);
        }
        .no-disponible {
            text-align: center;
            padding: 50px 20px;
            color: #999;
        }
        .no-disponible p {
            font-size: 18px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <header>
        <h1>⚽ PRE-CANCHA</h1>
        <p>Sistema de Reserva de Canchas de Fútbol</p>
    </header>

    <div class="navbar">
        <div class="nav-left">
            <a href="index.php">Inicio</a>
            <a href="mis_reservas.php">Mis Reservas</a>
        </div>
        <div class="nav-right">
            <?php if (isLogged()): ?>
                <span><?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="logout.php">Cerrar Sesión</a>
            <?php else: ?>
                <a href="login.php">Iniciar Sesión</a>
                <a href="registro.php">Registrarse</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <?php if ($message === 'success'): ?>
            <div class="alert alert-success">✓ Sesión cerrada correctamente</div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">✗ Error: <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (isLogged()): ?>
            <div class="bienvenida">
                <h2>¡Bienvenido, <span class="usuario-info"><?php echo htmlspecialchars($_SESSION['nombre']); ?></span>!</h2>
                <p>Selecciona una cancha para hacer tu reserva</p>
            </div>
        <?php else: ?>
            <div class="bienvenida">
                <h2>¡Bienvenido a PRE-CANCHA!</h2>
                <p><a href="login.php" style="color: #DAA520; text-decoration: underline;">Inicia sesión</a> o <a href="registro.php" style="color: #DAA520; text-decoration: underline;">regístrate</a> para reservar una cancha</p>
            </div>
        <?php endif; ?>

        <h3 style="color: #DAA520; margin: 30px 0 20px 0; font-size: 24px;">Canchas Disponibles</h3>

        <?php if (empty($canchas)): ?>
            <div class="no-disponible">
                <p>No hay canchas disponibles en este momento</p>
                <p style="font-size: 14px; color: #666;">Por favor, intenta más tarde</p>
            </div>
        <?php else: ?>
            <div class="grid-canchas">
                <?php foreach ($canchas as $cancha): ?>
                    <div class="cancha-card">
                        <div class="cancha-numero">Cancha <?php echo $cancha['numero']; ?></div>
                        <div class="cancha-tipo">▪ <?php echo htmlspecialchars($cancha['tipo']); ?></div>
                        <div class="cancha-detalle">👥 <?php echo $cancha['jugadores']; ?> jugadores</div>
                        <div class="cancha-detalle">🏟 <?php echo htmlspecialchars($cancha['superficie']); ?></div>
                        <div class="cancha-detalle">
                            <?php echo $cancha['iluminacion'] ? '💡 Iluminada' : ''; ?>
                            <?php echo $cancha['vestuarios'] ? ' | 🚪 Vestuarios' : ''; ?>
                            <?php echo $cancha['estacionamiento'] ? ' | 🅿️ Estacionamiento' : ''; ?>
                        </div>
                        <div class="cancha-precio">$<?php echo number_format($cancha['precio_hora'], 2); ?>/hora</div>
                        
                        <?php if (isLogged()): ?>
                            <a href="reservas.php?cancha=<?php echo $cancha['id']; ?>">
                                <button class="btn-reservar">Reservar Ahora</button>
                            </a>
                        <?php else: ?>
                            <a href="login.php">
                                <button class="btn-reservar">Inicia sesión para reservar</button>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
