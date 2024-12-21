<?php 
//header( 'Location: pages/login.php' );
//exit();
require_once 'config.php';

$request = $_GET['route'] ?? 'home';

switch ($request) {
    case 'login':
        require 'pages/login.php';
        break;
    case 'admin':
        require 'pages/admin.php';
        break;
    case 'records':
        require 'pages/records.php';
        break;
    default:
        echo "Página no encontrada.";
        break;
}

?>