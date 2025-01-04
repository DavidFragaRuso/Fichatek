<?php
if (!defined('BASE_PATH')) {
    exit('No se permite el acceso directo');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Fichatek</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
    <script src="<?php echo BASE_URL ?>/assets/js/fichatek.js" defer></script>
</head>
<body class="container">
    <header class="bg-grey">
        <div class="logo">
            <span>FichaTeck</span>
        </div>
        <nav>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="<?php echo BASE_URL; ?>/logout" class="logout-button">Salir</a>
        <?php endif; ?>
        </nav>
    </header>
