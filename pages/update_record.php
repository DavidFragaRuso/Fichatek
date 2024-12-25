<?php
require_once BASE_PATH . '/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $record_id = $_POST['record_id'];
    $new_date = $_POST['new_date'];
    $new_time = $_POST['new_time'];
    $new_type = $_POST['new_type'];
    
    // Validación de datos
    if (empty($record_id) || empty($new_date) || empty($new_time) || empty($new_type)) {
        die('Todos los campos son requeridos.');
    }

    // Formato correcto de la fecha y hora
    $new_date = date('Y-m-d', strtotime($new_date));
    $new_time = date('H:i:s', strtotime($new_time));
    
    // Preparar la consulta para actualizar el registro
    $stmt = $pdo->prepare("UPDATE work_records SET date = ?, time = ?, type = ? WHERE id = ?");
    $stmt->execute([$new_date, $new_time, $new_type, $record_id]);

    // Verifica si la actualización fue exitosa
    if ($stmt->rowCount() > 0) {
        // Redirige al administrador después de la actualización
        header('Location: admin.php');
        exit();
    } else {
        echo "Error al actualizar el registro.";
    }
}
?>
