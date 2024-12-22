<?php
session_start();
require_once BASE_PATH . '/config.php';

$db = new Db($pdo);

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'worker') {
    header('Location: login');
    exit();
} else {
    $user_id = $_SESSION['user_id']; 
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
    $_SESSION['message'] = "Fichaje registrado correctamente.";

    // Redirige a la misma página (patrón PRG)
    header('Location: records');
    exit();

}

// Obtén el mensaje de la sesión, si existe
$message = $_SESSION['message'] ?? null;
unset($_SESSION['message']); // Limpia el mensaje después de mostrarlo

require_once BASE_PATH . '/header.php';
?>
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
    <div class="records-list">
        <h2>Fichajes del trabajador</h2>
        <?php
            $records = $db->getRecordFromUser($user_id);
            if ($records) {
                echo "<ul>";
                foreach ($records as $record) {
                    echo "<li>{$record['date']} - Entrada: {$record['entry_time']}, Salida: {$record['exit_time']}</li>";
                }
                echo "</ul>";
                
            } else {
                echo "<p>No hay fichajes registrados.</p>";
            }
        ?>
    </div>
<?php require_once BASE_PATH . '/footer.php'; ?>