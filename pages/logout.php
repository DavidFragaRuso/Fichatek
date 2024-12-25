<?php
require_once BASE_PATH . '/config.php';

// Instancia de la clase Auth
$auth = new Auth($pdo);

if (isset($_SESSION['user_id'])) {
    header('Location: admin');
    exit();
}