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

$pageTitle = 'Administrar usuario';

require_once BASE_PATH . '/header.php';
?>

<div class="edit-user-container">
    <h1>Editar Usuario: <?php echo htmlspecialchars($user['username']); ?></h1>
    <form method="POST" action="<?php echo BASE_URL; ?>/update_record">
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

<div class="filter-container">
    <h2>Filtrar registros</h2>
    <form method="GET" action="<?php echo BASE_URL; ?>/edit_user">
        <input type="hidden" name="worker_id" value="<?php echo $worker_id; ?>">
        
        <label for="month">Mes:</label>
        <select name="month">
            <option value="">Todos</option>
            <?php for ($i = 1; $i <= 12; $i++): ?>
                <option value="<?php echo $i; ?>" <?php echo isset($_GET['month']) && $_GET['month'] == $i ? 'selected' : ''; ?>>
                    <?php echo date('F', mktime(0, 0, 0, $i, 10)); ?>
                </option>
            <?php endfor; ?>
        </select>

        <label for="year">Año:</label>
        <select name="year">
            <option value="">Todos</option>
            <?php 
            $currentYear = date('Y');
            for ($i = $currentYear; $i >= $currentYear - 5; $i--): ?>
                <option value="<?php echo $i; ?>" <?php echo isset($_GET['year']) && $_GET['year'] == $i ? 'selected' : ''; ?>>
                    <?php echo $i; ?>
                </option>
            <?php endfor; ?>
        </select>

        <button type="submit">Filtrar</button>
    </form>
</div>

<div class="edit-records">
    <h2>Historial de fichajes de <?php echo htmlspecialchars($user['username']); ?></h2>
    <?php
    $month = isset($_GET['month']) ? intval($_GET['month']) : null;
    $year = isset($_GET['year']) ? intval($_GET['year']) : null;
     
    $records = $db->getRecordFromUser($user['id'], $month, $year);
    //echo "<pre>";
    //var_dump($records); 
    //echo "</pre>";
    ?>
    <?php if ($records): 
        $currentDate = null;

        echo '<table>';
        echo '<thead>';
        echo '<tr><th>Fecha</th><th>Hora</th><th>Tipo</th><th>Acciones</th></tr>';
        echo '</thead>';
        echo '<tbody>';
        
        foreach ($records as $record) {
            $date = $record['date'];
            $time = $record['time'];
            $type = $record['type'];
        
            // Si la fecha cambia, cerrar tbody anterior y abrir uno nuevo
            if ($currentDate !== $date) {
                if ($currentDate !== null) {
                    echo '</tbody>';
                }
                echo "<tbody>";
                echo "<tr class='opener'><td colspan='4'><strong>" . date('l, d F Y', strtotime($date)) . "</strong></td></tr>";
                $currentDate = $date;
            }
        
            // Mostrar cada registro dentro del tbody correcto
            echo "<tr class='hidden-panel'>";
            echo "<td>";
            ?>
                <form method="POST" action="<?php echo BASE_URL; ?>/update_record">
                    <input type="hidden" name="worker_id" value="<?php echo $worker_id ?>">
                    <input type="hidden" name="record_id" value="<?php echo $record['id']; ?>">
                    <input type="date" name="new_date" value="<?php echo htmlspecialchars($date); ?>">
                </td>
                <td>
                    <input type="time" name="new_time" value="<?php echo htmlspecialchars($time); ?>">
                </td>
                <td>
                    <select name="new_type">
                        <option value="Entrada" <?php echo $type === 'Entrada' ? 'selected' : ''; ?>>Entrada</option>
                        <option value="Salida" <?php echo $type === 'Salida' ? 'selected' : ''; ?>>Salida</option>
                    </select>
                </td>
                <td>
                    <button type="submit">Actualizar</button>
                </form>
                <form method="POST" action="<?php echo BASE_URL; ?>/delete_record" style="display:inline;">
                    <input type="hidden" name="worker_id" value="<?php echo $worker_id; ?>">
                    <input type="hidden" name="record_id" value="<?php echo $record['id']; ?>">
                    <button type="submit" class="delete-button" onclick="return confirm('¿Estás seguro de que deseas eliminar este registro?');">Eliminar</button>
                </form>
                </td>
            </tr>
            <?php
        }
        
        // Cerrar la última sección de registros
        echo '</tbody>';
        echo '</table>';
            
    else: ?>
        <p>No hay fichajes registrados para este usuario.</p>
    <?php endif; ?>
</div>

<div class="export-pdf">
    <h2>Exportar registros</h2>
    <form method="GET" action="<?php echo BASE_URL; ?>/export_user_records">
        <input type="hidden" name="worker_id" value="<?php echo $user['id']; ?>">
        
        <label for="month">Mes:</label>
        <select name="month">
            <?php
            for ($m = 1; $m <= 12; $m++) {
                printf('<option value="%02d">%s</option>', $m, date('F', mktime(0, 0, 0, $m, 1)));
            }
            ?>
        </select>

        <label for="year">Año:</label>
        <select name="year">
            <?php
            $currentYear = date('Y');
            for ($y = $currentYear; $y >= ($currentYear - 5); $y--) {
                echo "<option value='$y'>$y</option>";
            }
            ?>
        </select>

        <button type="submit" class="button">Descargar PDF</button>
    </form>
</div>



<?php require_once BASE_PATH . '/footer.php'; ?>
