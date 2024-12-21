<?php
if (!defined('BASE_PATH')) {
    exit('No se permite el acceso directo');
}
require_once BASE_PATH . '/config.php';
require_once BASE_PATH . '/header.php';

$db = new Db($pdo);

$pageTitle = 'Panel de adminsitración';

if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new-name']) && isset($_POST['new-password'])) {
    $newName = $_POST['new-name'];
    $newPassword = $_POST['new-password'];

    if (!empty($newName) && !empty($newPassword)) {
        // Hashear la contraseña antes de guardarla
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        //$stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        //$stmt->execute([$newName, $hashedPassword, 'worker']); // 'worker' es el rol asignado

        $db->AddUser( $newName, $hashedPassword );

        //$successMessage = "Nuevo trabajador creado con éxito.";

    } else {
        $errorMessage = "Por favor, complete todos los campos.";
    }
}

?>

    <div class="admin-panel">
        <h1>Panel de adminsitración</h1>
        <div class="new-user-panel">
            <h2>Crear nuevo usuario</h2>
            <?php if (isset($successMessage)) { echo "<p class='success'>$successMessage</p>"; } ?>
            <?php if (isset($errorMessage)) { echo "<p class='error'>$errorMessage</p>"; } ?>
            <form method="POST">
                <input type="text" name="new-name" placeholder="Nuevo Usuario" required />
                <input type="text" name="new-password" placeholder="Contraseña nuevo usuario" required />
                <button type="submit">Crear Nuevo</button>
            </form>
        </div>
        <div class="view-user">
            <h2>Acceso a registro horario</h2>
            <?php
            $stmt = $pdo->prepare("SELECT id, username FROM users WHERE role = 'worker'");
            $stmt->execute();
            $workers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <form method="POST">
                <select name="worker_id">
                    <option>Selecciona un usuario</option>
                    <?php foreach ($workers as $worker): ?>
                        <option value="<?php echo $worker['id']; ?>"><?php echo $worker['username']; ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Ver registro horario</button>
            </form>
        </div>
    </div>