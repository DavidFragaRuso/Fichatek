<?php
require_once 'config.php';
require_once BASE_PATH .'/assets/incl/pdf/fpdf.php';

if (!isset($_GET['worker_id'])) {
    exit('ID de usuario no proporcionado.');
}

$worker_id = intval($_GET['worker_id']);

// Obtener información del usuario
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$worker_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    exit('Usuario no encontrado.');
}

// Obtener registros agrupados por mes y día
$stmt = $pdo->prepare("
    SELECT DATE_FORMAT(date, '%Y-%m') as month, date, entry_time, exit_time 
    FROM work_records 
    WHERE user_id = ? 
    ORDER BY date, entry_time
");
$stmt->execute([$worker_id]);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agrupar registros por mes y día
$groupedRecords = [];
foreach ($records as $record) {
    $month = $record['month'];
    $day = $record['date'];
    $groupedRecords[$month][$day][] = $record;
}

// Crear el PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Título
$pdf->Cell(0, 10, utf8_decode('Registros Horarios de Usuario'), 0, 1, 'C');
$pdf->Ln(5);

// Información del usuario
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Usuario: ' . utf8_decode($user['username']), 0, 1);
$pdf->Cell(0, 10, 'Fecha de impresión: ' . date('d/m/Y H:i'), 0, 1);
$pdf->Ln(10);

// Listado de registros
foreach ($groupedRecords as $month => $days) {
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, utf8_decode('Mes: ') . $month, 0, 1);
    $pdf->Ln(5);

    foreach ($days as $day => $records) {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, utf8_decode('Día: ') . $day, 0, 1);
        $pdf->Ln(3);

        $pdf->SetFont('Arial', '', 10);
        foreach ($records as $record) {
            $line = sprintf(
                "   Entrada: %s - Salida: %s",
                $record['entry_time'],
                $record['exit_time']
            );
            $pdf->Cell(0, 10, utf8_decode($line), 0, 1);
        }
        $pdf->Ln(5);
    }
    $pdf->Ln(10);
}

// Salida del archivo
$pdf->Output('D', 'Registros_' . $user['username'] . '.pdf');
exit;
?>
