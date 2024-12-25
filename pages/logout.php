<?php
require_once BASE_PATH . '/config.php';

// Instancia de la clase Auth
$auth = new Auth($pdo);

// Llamar al método logout
$auth->logout();
