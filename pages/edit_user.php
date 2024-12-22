<?php
session_start();
require_once BASE_PATH . '/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login');
    exit();
}

if (!isset($_GET['worker_id'])) {
    echo "No se proporcionó un ID de usuario.";
    exit();
}

$worker_id = intval($_GET['worker_id']); // Sanitiza el ID

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

<?php require_once BASE_PATH . '/footer.php'; ?>
