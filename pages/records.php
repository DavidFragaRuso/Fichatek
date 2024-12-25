<?php
session_start();
require_once BASE_PATH . '/config.php';

$db = new Db($pdo);

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'worker') {
    header('Location: login');
    exit();
}

$user_id = $_SESSION['user_id'];
$records = $db->getRecordFromUser($user_id);
//echo "<pre>";
//var_dump($records);
//echo "</pre>";
// Determina el tipo de fichaje
$lastRecord = end($records); // Obtener el último registro
//var_dump($lastRecord);
$nextType = 'Entrada'; // Por defecto, asumimos que será "Entrada"

if ($lastRecord && $lastRecord['type'] === 'Entrada') {
    $nextType = 'Salida'; // Si el último registro es "Entrada", el siguiente será "Salida"
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $type = $_POST['type'];
    $time = date('H:i:s');

    // Inserta el registro en la base de datos
    $stmt = $pdo->prepare("INSERT INTO work_records (user_id, type, time, date) VALUES (?, ?, ?, CURDATE())");
    $stmt->execute([$user_id, $type, $time]);

    // Mensaje de éxito (puede almacenarse en la sesión para mostrar tras la redirección)
    $_SESSION['flash_message'] = "Fichaje de $type registrado correctamente.";

    // Redirige a la misma página para evitar la re-submisión
    header("Location: records");
    exit();
}

// Mostrar mensajes flash si existen
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}

$pageTitle = 'Panel de fichajes';

require_once BASE_PATH . '/header.php';
?>
<div class="user-data">
    <p><b>Trabajador:</b> <?php echo $_SESSION['name']; ?></p>
    <p><b>Centro de trabajo:</b> RubiBike - Carrer de Montserrat, 5 08191 Rubí (Barcelona)</p>
</div>

<div class="panel records-container">
    <h1>Fichaje de <?php echo $nextType; ?></h1>
    <p>Hora actual: <?php echo date('H:i:s'); ?></p>
    <form method="POST">
    <input type="hidden" name="type" value="<?php echo $nextType; ?>">
    <button type="submit">Marcar <?php echo $nextType; ?></button>
    </form>
</div>

<div class="panel records-list">
    <h2>Historial de fichajes</h2>
    <?php
    if ($records) {
        $records = array_reverse($records);
        // Variables para almacenar el último valor de año, mes y día
        $lastYear = null;
        $lastMonth = null;
        $lastDate = null;

        // Contadores para rowspan
        $yearCount = 0;
        $monthCount = 0;
        $dayCount = 0;

        ?>
        <table>
            <thead>
                <tr>
                    <th scope="col">Año</th>
                    <th scope="col">Mes</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Registros</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                foreach ($records as $record) {
                    $date = $record['date'];
                    $dateTime = new DateTime($date);

                    $year = $dateTime->format('Y');
                    $month = $dateTime->format('F');
                    $dayName = $dateTime->format('l');
                    $day = $dateTime->format('d');

                    // Verificar si el año ha cambiado
                    if ($year !== $lastYear) {
                        $yearCount = count(array_filter($records, function($rec) use ($year) {
                            return (new DateTime($rec['date']))->format('Y') === $year;
                        }));
                        $lastYear = $year;
                    }

                    // Verificar si el mes ha cambiado
                    if ($month !== $lastMonth) {
                        $monthCount = count(array_filter($records, function($rec) use ($month) {
                            return (new DateTime($rec['date']))->format('F') === $month;
                        }));
                        $lastMonth = $month;
                    }

                    // Verificar si el día ha cambiado
                    if ($date !== $lastDate) {
                        $dayCount = count(array_filter($records, function($rec) use ($date) {
                            return $rec['date'] === $date;
                        }));
                        $lastDate = $date;
                    }

                    ?>
                    <tr>
                        <?php if ($yearCount > 0): ?>
                            <td rowspan="<?php echo $yearCount; ?>"><?php echo $year; ?></td>
                            <?php $yearCount = 0; ?>
                        <?php endif; ?>

                        <?php if ($monthCount > 0): ?>
                            <td rowspan="<?php echo $monthCount; ?>"><?php echo $month; ?></td>
                            <?php $monthCount = 0; ?>
                        <?php endif; ?>

                        <?php if ($dayCount > 0): ?>
                            <td rowspan="<?php echo $dayCount; ?>"><?php echo $dayName . " " . $day; ?></td>
                            <?php $dayCount = 0; ?>
                        <?php endif; ?>

                        <td><?php echo $record['time'] . " - " . $record['type']; ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <?php
    } else {
        echo "<p>No hay fichajes registrados.</p>";
    }
    ?>
</div>


<?php require_once BASE_PATH . '/footer.php'; ?>
