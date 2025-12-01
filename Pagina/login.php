<?php
require 'bd.php';

if (isLogged()) redirect('index.php');

$error = '';
$email_value = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $email_value = $email;

    if (empty($email) || empty($password)) {
        $error = 'El email y la contraseña son requeridos';
    } else {
        $user = fetchOne("SELECT * FROM usuarios WHERE email = ?", [$email]);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['rol'] = $user['rol'];
            
            // Actualizar último acceso
            query("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?", [$user['id']]);
            
            redirect('index.php');
        } else {
            $error = 'Email o contraseña incorrectos';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - PRE-CANCHA</title>
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
        .container-login {
            width: 100%;
            max-width: 450px;
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
            margin-bottom: 25px;
        }
        label {
            display: block;
            color: #DAA520;
            margin-bottom: 10px;
            font-weight: bold;
            font-size: 14px;
        }
        input {
            width: 100%;
            padding: 14px 12px;
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
        .error {
            background: rgba(255, 68, 68, 0.2);
            border: 2px solid #f44;
            color: #f44;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
            text-align: center;
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
    </style>
</head>
<body>
    <div class="container-login">
        <header>
            <h1>⚽ PRE-CANCHA</h1>
            <p>Iniciar Sesión</p>
        </header>

        <div class="form-container">
            <?php if ($error): ?>
                <div class="error">✗ <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="<?php echo htmlspecialchars($email_value); ?>" 
                        required 
                        placeholder="tu@email.com"
                        autofocus
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
                    >
                </div>

                <button type="submit" class="btn">Iniciar Sesión</button>
            </form>

            <div class="links">
                <p>¿No tienes cuenta?</p>
                <a href="registro.php">Registrarse aquí</a>
            </div>

            <a href="index.php" class="back-link">← Volver al inicio</a>
        </div>
    </div>
</body>
</html>
