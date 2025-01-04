<?php
session_start();
require_once BASE_PATH . '/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php?route=login');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['record_id']) && isset($_POST['worker_id'])) {
    $record_id = intval($_POST['record_id']);
    $worker_id = intval($_POST['worker_id']);

    $stmt = $pdo->prepare("DELETE FROM work_records WHERE id = ? AND user_id = ?");
    $stmt->execute([$record_id, $worker_id]);

    // Mensaje de éxito
    $_SESSION['flash_message'] = "El registro se eliminó correctamente.";

    // Redirige de vuelta a la página de edición del usuario
    header("Location: edit_user/$worker_id");
    exit();
} else {
    echo "Solicitud no válida.";
    exit();
}
