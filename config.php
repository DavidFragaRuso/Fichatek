<?php 
define('BASE_PATH', __DIR__);

/**
 * DB Conection
 */
$host = 'localhost';
$dbname = 'fichatek_bbdd';
$username = 'root';
$password = ''; // Cambia según tu configuración

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

?>