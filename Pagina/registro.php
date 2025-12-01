<?php
require 'bd.php';

if (isLogged()) redirect('index.php');

$error = '';
$success = '';
$nombre_value = '';
$email_value = '';
$telefono_value = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    $nombre_value = $nombre;
    $email_value = $email;
    $telefono_value = $telefono;

    // Validaciones
    if (empty($nombre) || empty($email) || empty($telefono) || empty($password)) {
        $error = 'Todos los campos son requeridos';
    } elseif (strlen($nombre) < 3) {
        $error = 'El nombre debe tener al menos 3 caracteres';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El email no es válido';
    } elseif (strlen($telefono) < 9) {
        $error = 'El teléfono debe tener al menos 9 dígitos';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres';
    } elseif ($password !== $password_confirm) {
        $error = 'Las contraseñas no coinciden';
    } else {
        // Verificar si el email ya existe
        $existe = fetchOne("SELECT id FROM usuarios WHERE email = ?", [$email]);
        
        if ($existe) {
            $error = 'El email ya está registrado';
        } else {
            // Registrar nuevo usuario
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            try {
                query(
                    "INSERT INTO usuarios (nombre, email, telefono, password, rol) VALUES (?, ?, ?, ?, 'usuario')",
                    [$nombre, $email, $telefono, $password_hash]
                );
                
                $success = 'Registro exitoso. Ahora puedes <a href="login.php" style="color: #DAA520; font-weight: bold;">iniciar sesión</a>';
                $nombre_value = '';
                $email_value = '';
                $telefono_value = '';
            } catch (Exception $e) {
                $error = 'Error al registrar. Intenta de nuevo';
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
    <title>Registrarse - PRE-CANCHA</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #000, #1a1a1a);
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container-registro {
            width: 100%;
            max-width: 500px;
        }
        header {
            background: linear-gradient(135deg, #DAA520, #FFD700);
            color: #000;
            padding: 30px;
            text-align: center;
            border-radius: 12px 12px 0 0;
            box-shadow: 0 4px 15px rgba(218, 165, 32, 0.4);
        }
        header h1 { font-size: 32px; font-weight: bold; }
        header p { font-size: 12px; margin-top: 5px; opacity: 0.9; }
        .form-container {
            background: #1a1a1a;
            border: 2px solid #DAA520;
            border-top: none;
            border-radius: 0 0 12px 12px;
            padding: 40px 30px;
            box-shadow: 0 4px 15px rgba(218, 165, 32, 0.2);
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            color: #DAA520;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 14px;
        }
        input {
            width: 100%;
            padding: 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(218, 165, 32, 0.3);
            border-radius: 8px;
            color: #fff;
            font-size: 15px;
            font-family: inherit;
            transition: 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #FFD700;
            background: rgba(218, 165, 32, 0.1);
            box-shadow: 0 0 10px rgba(218, 165, 32, 0.3);
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
            text-align: center;
        }
        .alert-error { 
            background: rgba(255, 68, 68, 0.2); 
            border: 2px solid #f44; 
            color: #f44; 
        }
        .alert-success { 
            background: rgba(0, 200, 0, 0.2); 
            border: 2px solid #0c0; 
            color: #0c0; 
        }
        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #DAA520, #FFD700);
            color: #000;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(218, 165, 32, 0.5);
        }
        .btn:active {
            transform: translateY(-1px);
        }
        .links {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 2px solid rgba(218, 165, 32, 0.2);
        }
        .links p {
            color: #bbb;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .links a {
            color: #DAA520;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }
        .links a:hover {
            color: #FFD700;
            text-decoration: underline;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #DAA520;
            text-decoration: none;
            font-size: 14px;
            transition: 0.3s;
        }
        .back-link:hover {
            color: #FFD700;
        }
        .password-info {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container-registro">
        <header>
            <h1>⚽ PRE-CANCHA</h1>
            <p>Crear Cuenta</p>
        </header>

        <div class="form-container">
            <?php if ($error): ?>
                <div class="alert alert-error">✗ <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">✓ <?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="nombre">Nombre Completo</label>
                    <input 
                        type="text" 
                        id="nombre" 
                        name="nombre" 
                        value="<?php echo htmlspecialchars($nombre_value); ?>" 
                        required 
                        placeholder="Juan Pérez"
                        minlength="3"
                    >
                </div>

                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="<?php echo htmlspecialchars($email_value); ?>" 
                        required 
                        placeholder="tu@email.com"
                    >
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input 
                        type="tel" 
                        id="telefono" 
                        name="telefono" 
                        value="<?php echo htmlspecialchars($telefono_value); ?>" 
                        required 
                        placeholder="+34 123 456 789"
                        minlength="9"
                    >
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        placeholder="••••••••"
                        minlength="6"
                    >
                    <div class="password-info">Mínimo 6 caracteres</div>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Confirmar Contraseña</label>
                    <input 
                        type="password" 
                        id="password_confirm" 
                        name="password_confirm" 
                        required 
                        placeholder="••••••••"
                        minlength="6"
                    >
                </div>

                <button type="submit" class="btn">Registrarse</button>
            </form>

            <div class="links">
                <p>¿Ya tienes cuenta?</p>
                <a href="login.php">Inicia sesión aquí</a>
            </div>

            <a href="index.php" class="back-link">← Volver al inicio</a>
        </div>
    </div>
</body>
</html>
