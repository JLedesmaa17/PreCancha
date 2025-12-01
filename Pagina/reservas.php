<?php
require 'bd.php';

if (!isLogged()) redirect('login.php');

$error = '';
$success = '';
$cancha_id = $_GET['cancha'] ?? null;

if (!$cancha_id) redirect('index.php');

$cancha = fetchOne("SELECT * FROM canchas WHERE id = ? AND estado = 'disponible'", [$cancha_id]);
if (!$cancha) redirect('index.php?error=cancha_no_disponible');

$user = fetchOne("SELECT * FROM usuarios WHERE id = ?", [$_SESSION['user_id']]);

// Procesar formulario POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'] ?? '';
    $hora_inicio = $_POST['horario'] ?? '';
    $observaciones = $_POST['observaciones'] ?? '';
    
    // Validar datos
    if (empty($fecha) || empty($hora_inicio)) {
        $error = 'Fecha y horario son requeridos';
    } else {
        // Verificar que la fecha sea válida
        $fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha);
        if (!$fecha_obj || $fecha_obj->format('Y-m-d') !== $fecha) {
            $error = 'Fecha inválida';
        } elseif (strtotime($fecha) < strtotime('today')) {
            $error = 'No puedes reservar en fechas pasadas';
        } else {
            // Verificar que no exista reserva en ese horario
            $existe = fetchOne(
                "SELECT id FROM reservas WHERE cancha_id = ? AND fecha_reserva = ? AND hora_inicio = ? AND estado IN ('pendiente', 'confirmada')",
                [$cancha_id, $fecha, $hora_inicio]
            );
            
            if ($existe) {
                $error = 'Ese horario ya está reservado';
            } else {
                // Crear la reserva
                try {
                    $hora_fin = date('H:i:s', strtotime($hora_inicio . ' +1 hour'));
                    
                    query(
                        "INSERT INTO reservas (usuario_id, cancha_id, fecha_reserva, hora_inicio, hora_fin, estado, monto, observaciones) 
                         VALUES (?, ?, ?, ?, ?, 'pendiente', ?, ?)",
                        [$_SESSION['user_id'], $cancha_id, $fecha, $hora_inicio, $hora_fin, $cancha['precio_hora'], $observaciones]
                    );
                    
                    $success = 'Reserva creada exitosamente. Serás redirigido en unos momentos...';
                } catch (Exception $e) {
                    $error = 'Error al crear la reserva: ' . $e->getMessage();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar Cancha <?= $cancha['numero'] ?> - PRE-CANCHA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; background: linear-gradient(135deg, #000, #1a1a1a); color: #fff; min-height: 100vh; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        header { background: #1a1a1a; border: 2px solid #DAA520; border-radius: 15px; padding: 25px; margin-bottom: 25px; }
        .header-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px; }
        h1 { font-size: 28px; color: #DAA520; }
        .btn-back { padding: 10px 20px; background: rgba(255,255,255,0.1); color: #fff; border: 2px solid #DAA520; border-radius: 8px; text-decoration: none; transition: 0.3s; }
        .btn-back:hover { background: #DAA520; color: #000; }
        .cancha-info { background: rgba(218,165,32,0.1); padding: 20px; border-radius: 10px; display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; text-align: center; }
        .cancha-detail strong { color: #DAA520; display: block; margin-bottom: 5px; }
        .section { background: #1a1a1a; border: 2px solid #DAA520; border-radius: 15px; padding: 30px; margin-bottom: 25px; }
        h3 { color: #DAA520; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; color: #DAA520; margin-bottom: 8px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 2px solid rgba(218,165,32,0.3); border-radius: 8px; color: #fff; font-size: 16px; font-family: inherit; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #FFD700; background: rgba(218,165,32,0.1); }
        select option { background: #000; color: #fff; }
        .btn { width: 100%; padding: 15px; background: linear-gradient(135deg, #DAA520, #FFD700); color: #000; border: none; border-radius: 8px; font-size: 18px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn:hover:not(:disabled) { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(218,165,32,0.5); }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
        .alert-success { background: rgba(0,255,0,0.2); border: 2px solid #0f0; color: #0f0; }
        .alert-danger { background: rgba(255,68,68,0.2); border: 2px solid #f44; color: #f44; }
        .resumen { background: rgba(218,165,32,0.15); padding: 20px; border-radius: 10px; margin-bottom: 20px; border: 2px solid rgba(218,165,32,0.3); }
        .resumen h4 { color: #DAA520; margin-bottom: 15px; }
        .resumen p { margin: 8px 0; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="header-top">
                <h1>⚽ Reservar Turno</h1>
                <a href="index.php" class="btn-back">← Volver</a>
            </div>
            <div class="cancha-info">
                <div class="cancha-detail"><strong>🏟️ Cancha</strong> Nº <?= $cancha['numero'] ?></div>
                <div class="cancha-detail"><strong>⚽ Tipo</strong> <?= htmlspecialchars($cancha['tipo']) ?></div>
                <div class="cancha-detail"><strong>👥 Jugadores</strong> <?= $cancha['jugadores'] ?></div>
                <div class="cancha-detail"><strong>🌱 Superficie</strong> <?= htmlspecialchars($cancha['superficie']) ?></div>
                <div class="cancha-detail"><strong>💰 Precio</strong> $<?= number_format($cancha['precio_hora'], 0, ',', '.') ?>/h</div>
            </div>
        </header>

        <div class="section">
            <h3>📅 Completa tu Reserva</h3>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">❌ <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">✅ <?php echo htmlspecialchars($success); ?></div>
                <script>
                    setTimeout(() => location.href = 'reservas.php', 2000);
                </script>
            <?php endif; ?>
            
            <form method="POST" id="formReserva">
                <div class="form-group">
                    <label>📅 Fecha</label>
                    <input type="date" name="fecha" id="fecha" required min="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d', strtotime('+30 days')) ?>" onchange="actualizarResumen()">
                </div>
                <div class="form-group">
                    <label>⏰ Horario (1 hora)</label>
                    <select name="horario" id="horario" required onchange="actualizarResumen()">
                        <option value="">Selecciona un horario</option>
                        <?php for ($h = 9; $h <= 22; $h++): ?>
                            <option value="<?= sprintf('%02d:00:00', $h) ?>"><?= sprintf('%02d:00 - %02d:00', $h, $h+1) ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>📝 Observaciones (opcional)</label>
                    <textarea name="observaciones" id="observaciones" rows="3" placeholder="Comentarios adicionales..."></textarea>
                </div>
                <div class="resumen" id="resumen" style="display:none;">
                    <h4>✅ Resumen</h4>
                    <div id="resumenTexto"></div>
                </div>
                <button type="submit" class="btn" id="btnEnviar">✅ Confirmar Reserva</button>
            </form>
        </div>
    </div>

    <script>
        const precio = <?= (float)$cancha['precio_hora'] ?>;

        function actualizarResumen() {
            const fecha = document.getElementById('fecha').value;
            const horario = document.getElementById('horario').value;
            if (fecha && horario) {
                const h = horario.substr(0, 5);
                const hFin = String(parseInt(h.split(':')[0]) + 1).padStart(2, '0') + ':00';
                const f = new Date(fecha + 'T00:00:00');
                const fechaFormat = f.toLocaleDateString('es-AR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                document.getElementById('resumenTexto').innerHTML = `
                    <p><strong>📅 Fecha:</strong> ${fechaFormat}</p>
                    <p><strong>⏰ Horario:</strong> ${h} a ${hFin}</p>
                    <p><strong>💰 Total:</strong> $${precio.toLocaleString('es-AR')}</p>
                `;
                document.getElementById('resumen').style.display = 'block';
            } else {
                document.getElementById('resumen').style.display = 'none';
            }
        }
    </script>
</body>
</html>