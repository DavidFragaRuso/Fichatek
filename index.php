<?php 
require_once 'config.php';

// Verifica si la ruta es logout y realiza el cierre de sesión
if ($_GET['route'] === 'logout') {
    // Inicia sesión
    session_start();
    session_destroy();
    header('Location: index.php?route=login'); // Redirige al login
    exit();
}

// Maneja las demás rutas
$request = $_GET['route'] ?? 'home';
$workerId = $_GET['worker_id'] ?? null; // Captura el parámetro worker_id si existe

switch ($request) {
    case 'login':
        require 'pages/login.php';
        break;
    case 'logout':
        require 'pages/logout.php';
        break;
    case 'admin':
        require 'pages/admin.php';
        break;
    case 'records':
        require 'pages/records.php';
        break;
    case 'edit_user':
        require 'pages/edit_user.php';
        break;
    case 'update_record':
        require 'pages/update_record.php';
        break;
    case 'export_user_records':
        require 'pages/export_user_records.php';
        break;
    default:
        http_response_code(404);
        echo "Página no encontrada.";
        break;
}
?>
