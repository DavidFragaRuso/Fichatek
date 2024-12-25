<?php 
//header( 'Location: pages/login.php' );
//exit();
require_once 'config.php';

$request = $_GET['route'] ?? 'home';

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