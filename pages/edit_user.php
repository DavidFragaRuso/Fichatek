<?php
session_start();
require_once BASE_PATH . '/config.php';

$db = new Db($pdo);

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php?route=login');
    exit();
}

if (!isset($_GET['worker_id'])) {
    echo "No se proporcionó un ID de usuario.";
    exit();
}

$worker_id = intval($_GET['worker_id']);

// Obtén los datos del usuario
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$worker_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Usuario no encontrado.";
    exit();
}

require_once BASE_PATH . '/header.php';
?>

<div class="edit-user-container">
    <h1>Editar Usuario: <?php echo htmlspecialchars($user['username']); ?></h1>
    <form method="POST" action="update_user.php">
        <input type="hidden" name="worker_id" value="<?php echo $worker_id; ?>">
        <label for="username">Nombre de Usuario:</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        
        <label for="password">Nueva Contraseña (opcional):</label>
        <input type="password" name="password">

        <label for="role">Rol:</label>
        <select name="role">
            <option value="worker" <?php echo $user['role'] === 'worker' ? 'selected' : ''; ?>>Trabajador</option>
            <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Administrador</option>
        </select>

        <button type="submit">Actualizar Usuario</button>
    </form>
</div>
<div class="edit-records">
    <h2>Editar registros horarios de usuario</h2>
    <?php
    $records = $db->getRecordFromUser($user['id']);
    if ($records) {
        echo "<form method='POST' action=''>";
        echo "<ul>";
        foreach ($records as $record) {
            echo "<li>
                Fecha: {$record['date']} <br>
                Entrada: <input type='time' name='entry_time_{$record['id']}' value='{$record['entry_time']}' required>
                Salida: <input type='time' name='exit_time_{$record['id']}' value='{$record['exit_time']}' required>
                <button type='submit' name='update_record' value='{$record['id']}'>Actualizar</button>
            </li>";
        }
        echo "</ul>";
        echo "</form>";
    } else {
        echo "<p>No hay fichajes registrados.</p>";
    }

    // Manejo de la actualización de registros
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_record'])) {
        $record_id = intval($_POST['update_record']);
        $entry_time = $_POST['entry_time_' . $record_id];
        $exit_time = $_POST['exit_time_' . $record_id];

        // Validar los datos (opcional, pero recomendado)
        if (strtotime($entry_time) !== false && strtotime($exit_time) !== false) {
            $stmt = $pdo->prepare("UPDATE work_records SET entry_time = ?, exit_time = ? WHERE id = ?");
            $stmt->execute([$entry_time, $exit_time, $record_id]);

            echo "<p>Registro actualizado correctamente.</p>";
            // Redirigir para evitar resubmisión del formulario
            header("Location: index.php?route=edit_user&worker_id={$user['id']}");
            exit();
        } else {
            echo "<p>Error: Hora inválida.</p>";
        }
    }
    ?>
</div>

<?php require_once BASE_PATH . '/footer.php'; ?>
