<?php
session_start();
require_once BASE_PATH . '/config.php';

$db = new Db($pdo);

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'worker') {
    header('Location: login');
    exit();
}

$user_id = $_SESSION['user_id'];

// Consulta el último registro del usuario
$stmt = $pdo->prepare("SELECT * FROM work_records WHERE user_id = ? ORDER BY date DESC, time DESC LIMIT 1");
$stmt->execute([$user_id]);
$lastRecord = $stmt->fetch(PDO::FETCH_ASSOC);

// Determina el tipo de fichaje
$nextType = ($lastRecord && $lastRecord['type'] === 'Entrada') ? 'Salida' : 'Entrada';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Registrar el nuevo fichaje
    $stmt = $pdo->prepare("INSERT INTO work_records (user_id, type, time, date) VALUES (?, ?, ?, CURDATE())");
    $stmt->execute([$user_id, $nextType, date('H:i:s')]);

    // Redirige para evitar reenvíos de formularios
    header('Location: records');
    exit();
}

$pageTitle = 'Panel de fichajes';

require_once BASE_PATH . '/header.php';
?>
<div class="user-data">
    <p><b>Trabajador:</b> <?php echo $_SESSION['name']; ?></p>
    <p><b>Centro de trabajo:</b> RubiBike - Carrer de Montserrat, 5 08191 Rubí (Barcelona)</p>
</div>

<div class="panel records-container">
    <h1>Fichar</h1>
    <p>Hora actual: <?php echo date('H:i:s'); ?></p>
    <form method="POST">
        <button type="submit">Marcar <?php echo $nextType === 'entry' ? 'Entrada' : 'Salida'; ?></button>
    </form>
</div>

<div class="panel records-list">
    <h2>Historial de fichajes</h2>
    <?php
    // Listar todos los registros del usuario
    //$stmt = $pdo->prepare("SELECT * FROM work_records WHERE user_id = ? ORDER BY date DESC, time DESC");
    //$stmt->execute([$user_id]);
    //$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $records = $db->getRecordFromUser($user_id);

    echo "<pre>";
    var_dump($records);
    echo "</pre>";

    if ($records) {
        echo "<ul>";
        foreach ($records as $record) {
            echo "<li>{$record['date']} - {$record['type']}: {$record['time']}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No hay fichajes registrados.</p>";
    }
    ?>
</div>

<?php require_once BASE_PATH . '/footer.php'; ?>
