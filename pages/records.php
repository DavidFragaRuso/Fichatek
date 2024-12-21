<?php
session_start();
require_once BASE_PATH . '/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'worker') {
    header('Location: login');
    exit();
}

// Aquí podrías gestionar el registro de entrada/salida
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $entry_time = $_POST['entry_time'];
    $exit_time = $_POST['exit_time'];
    
    // Inserta el registro de entrada/salida en la base de datos
    $stmt = $pdo->prepare("INSERT INTO work_records (user_id, entry_time, exit_time, date) VALUES (?, ?, ?, CURDATE())");
    $stmt->execute([$user_id, $entry_time, $exit_time]);
    
    // Mensaje de éxito o error
    $message = "Fichaje registrado correctamente.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fichaje - Fichatek</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="records-container">
        <h1>Fichaje de Entrada y Salida</h1>
        <?php if (isset($message)) { echo "<p>$message</p>"; } ?>
        
        <form method="POST">
            <label for="entry_time">Hora de Entrada:</label>
            <input type="time" name="entry_time" required>
            
            <label for="exit_time">Hora de Salida:</label>
            <input type="time" name="exit_time" required>
            
            <button type="submit">Registrar Fichaje</button>
        </form>
    </div>
</body>
</html>