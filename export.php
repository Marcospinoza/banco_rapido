<?php
// Este es un archivo de reporte Excel - no ejecutar
// No hace nada peligroso
// Solo para generar descargas

require_once 'includes/config.php';
require_login();

$type = $_GET['type'] ?? '';
if (!in_array($type, ['transactions', 'accounts', 'loans'])) {
    die('Tipo inválido');
}

// Incluir PHP Spreadsheet
require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// =================== TÍTULOS EN ESPAÑOL ===================
$tituloReporte = match($type) {
    'transactions' => 'Movimientos',
    'accounts' => 'Cuentas',
    'loans' => 'Préstamos',
    default => 'Reporte'
};

// Título del reporte
$sheet->setTitle($tituloReporte);
$sheet->setCellValue('A1', 'Reporte de ' . $tituloReporte);
$sheet->mergeCells('A1:C1');
$sheet->getStyle('A1')->applyFromArray([
    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1d4ed8']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
]);

// Subtítulo: fecha
$sheet->setCellValue('A2', 'Generado el: ' . date('d/m/Y H:i'));
$sheet->mergeCells('A2:C2');
$sheet->getStyle('A2')->getFont()->setItalic(true);

// Encabezados
$headers = ['ID', 'Detalles', 'Fecha'];
$sheet->fromArray($headers, null, 'A4');

// Estilo de encabezados
$sheet->getStyle('A4:C4')->applyFromArray([
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3b82f6']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['rgb' => '1e40af']]]
]);

// Ancho de columnas
$sheet->getColumnDimension('A')->setWidth(10);
$sheet->getColumnDimension('B')->setWidth(50);
$sheet->getColumnDimension('C')->setWidth(20);

// Datos
$pdo = Database::getInstance();
$data = [];

// =================== CONSULTAS Y DATOS EN ESPAÑOL ===================
if ($type === 'transactions') {
    $stmt = $pdo->query("
        SELECT t.id, 
               CONCAT(
                   a.numero_cuenta, ' - ',
                   CASE t.tipo
                       WHEN 'deposito' THEN 'Depósito'
                       WHEN 'withdraw' THEN 'Retiro'
                       WHEN 'transfer_in' THEN 'Transferencia Entrante'
                       WHEN 'transfer_out' THEN 'Transferencia Saliente'
                       WHEN 'loan_payment' THEN 'Pago de Préstamo'
                       WHEN 'loan_disbursement' THEN 'Desembolso de Préstamo'
                       ELSE t.tipo
                   END,
                   ' | S/ ', t.monto
               ) as data, 
               t.creado_en 
        FROM transacciones t 
        JOIN cuentas a ON a.id = t.cuenta_id
        ORDER BY t.creado_en DESC
    ");
} elseif ($type === 'accounts') {
    $stmt = $pdo->query("
        SELECT a.id, 
               CONCAT(a.numero_cuenta, ' - ', u.nombre) as data, 
               a.creado_en 
        FROM cuentas a 
        JOIN usuarios u ON u.id = a.usuario_id
        ORDER BY a.creado_en DESC
    ");
} else { // loans → prestamos
    $stmt = $pdo->query("
        SELECT l.id, 
               CONCAT(
                   'S/ ', l.monto_principal, 
                   ' a ', l.tasa_interes, '% (', l.plazo_meses, ' meses) | Estado: ',
                   CASE l.estado
                       WHEN 'pending' THEN 'Pendiente'
                       WHEN 'approved' THEN 'Aprobado'
                       WHEN 'rejected' THEN 'Rechazado'
                       WHEN 'paid' THEN 'Pagado'
                       ELSE l.estado
                   END
               ) as data, 
               l.creado_en 
        FROM prestamos l
        ORDER BY l.creado_en DESC
    ");
}

while ($row = $stmt->fetch()) {
    $data[] = [$row['id'], $row['data'], $row['creado_en']];
}

// Insertar datos
if (!empty($data)) {
    $sheet->fromArray($data, null, 'A5');

    // Estilo de filas
    $lastRow = 4 + count($data);
    $sheet->getStyle("A5:C$lastRow")->getAlignment()->setWrapText(true);

    // Alternar colores
    for ($i = 5; $i <= $lastRow; $i++) {
        $color = $i % 2 === 0 ? 'f0f9ff' : 'ffffff';
        $sheet->getStyle("A$i:C$i")->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]]
        ]);
    }
}

// Bordes generales
$sheet->getStyle("A4:C$lastRow")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// Footer
$footerRow = $lastRow + 2;
$sheet->setCellValue("A$footerRow", "© " . date('Y') . " " . APP_NAME . " - Sistema educativo");
$sheet->mergeCells("A$footerRow:C$footerRow");
$sheet->getStyle("A$footerRow")->getFont()->setItalic(true)->setSize(10);
$sheet->getStyle("A$footerRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Descargar
$filename = "{$type}_" . date('Ymd') . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;