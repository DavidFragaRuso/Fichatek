<?php
require_once 'config.php';

$username = 'APavonHolgado';
$password = password_hash('12345Segura!', PASSWORD_DEFAULT);
$role = 'admin';

try {
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute([$username, $password, $role]);
    echo "Usuario creado con éxito.";
} catch (PDOException $e) {
    echo "Error al crear el usuario: " . $e->getMessage();
}
