<?php
// bd.php - Conexión simple a base de datos
session_start();

// Configuración
define('DB_HOST', 'localhost');
define('DB_NAME', 'precancha');
define('DB_USER', 'root');
define('DB_PASS', '');

// Conexión PDO
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Funciones útiles
function query($sql, $params = []) {
    global $pdo;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function fetchAll($sql, $params = []) {
    return query($sql, $params)->fetchAll();
}

function fetchOne($sql, $params = []) {
    return query($sql, $params)->fetch();
}

function isLogged() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function json($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}