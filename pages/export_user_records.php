<?php
require_once 'config.php';
require_once BASE_PATH . '/assets/incl/pdf/fpdf.php';

if (!isset($_GET['worker_id']) || !isset($_GET['month']) || !isset($_GET['year'])) {
    die("Faltan parámetros.");
}

$worker_id = intval($_GET['worker_id']);
$month = intval($_GET['month']);
$year = intval($_GET['year']);

// Obtener datos del trabajador
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$worker_id]);
$worker = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$worker) {
    die("Usuario no encontrado.");
}

// Obtener registros de fichajes del mes y año seleccionados
$stmt = $pdo->prepare("SELECT date, time, type FROM work_records WHERE user_id = ? AND MONTH(date) = ? AND YEAR(date) = ? ORDER BY date, time");
$stmt->execute([$worker_id, $month, $year]);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$records) {
    die("No hay registros para el período seleccionado.");
}

// Agrupar registros por fecha
$groupedRecords = [];
foreach ($records as $record) {
    $date = $record['date'];
    if (!isset($groupedRecords[$date])) {
        $groupedRecords[$date] = ['Entrada' => '', 'Salida' => ''];
    }
    $groupedRecords[$date][$record['type']] = $record['time'];
}

// Crear el PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Encabezado con datos de la empresa
$pdf->Cell(190, 10, "REGISTRO DE JORNADA LABORAL", 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(100, 5, "Empresa: [Nombre de la Empresa]", 0, 0);
$pdf->Cell(90, 5, "Centro de Trabajo: [Dirección]", 0, 1);
$pdf->Cell(100, 5, "Trabajador: " . utf8_decode($worker['username']), 0, 1);
$pdf->Cell(100, 5, "Periodo: " . sprintf("%02d-%d", $month, $year), 0, 1);
$pdf->Ln(5);

// Tabla de registros
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(63, 10, "Fecha", 1, 0, 'C');
$pdf->Cell(63, 10, "Hora Entrada", 1, 0, 'C');
$pdf->Cell(63, 10, "Hora Salida", 1, 1, 'C');

$pdf->SetFont('Arial', '', 10);
foreach ($groupedRecords as $date => $times) {
    $pdf->Cell(63, 8, $date, 1, 0, 'C');
    $pdf->Cell(63, 8, $times['Entrada'], 1, 0, 'C');
    $pdf->Cell(63, 8, $times['Salida'], 1, 1, 'C');
}

// Firma del trabajador
//$pdf->Ln(10);
//$pdf->Cell(190, 10, "Firma del trabajador: ____________________", 0, 1, 'L');

// Generar el PDF
$pdf->Output("D", "registro_fichajes_$month-$year.pdf");
?>
