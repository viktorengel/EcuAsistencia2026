<?php
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/models/Attendance.php';
require_once BASE_PATH . '/models/Course.php';
require_once BASE_PATH . '/models/Subject.php';
require_once BASE_PATH . '/models/SchoolYear.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/Institution.php';
require_once BASE_PATH . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReportController {
    private $attendanceModel;
    private $courseModel;
    private $subjectModel;
    private $schoolYearModel;
    private $userModel;
    private $institutionModel;

    public function __construct() {
        Security::requireLogin();
        if (!Security::hasRole(['autoridad', 'inspector', 'docente'])) {
            die('Acceso denegado');
        }

        $db = new Database();
        $this->attendanceModel = new Attendance($db);
        $this->courseModel = new Course($db);
        $this->subjectModel = new Subject($db);
        $this->schoolYearModel = new SchoolYear($db);
        $this->userModel = new User($db);
        $this->institutionModel = new Institution($db);
    }

    public function index() {
        $courses = $this->courseModel->getAll();
        $subjects = $this->subjectModel->getAll();
        
        $data = [];
        $course = null;
        $startDate = null;
        $endDate = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['preview'])) {
            $courseId = (int)$_POST['course_id'];
            $startDate = $_POST['start_date'];
            $endDate = $_POST['end_date'];
            
            $data = $this->attendanceModel->getReportData($courseId, $startDate, $endDate);
            $course = $this->getCourseInfo($courseId);
        }
        
        include BASE_PATH . '/views/reports/index.php';
    }

    public function generatePDF() {
        $courseId = (int)$_POST['course_id'];
        $startDate = $_POST['start_date'];
        $endDate = $_POST['end_date'];

        $data = $this->attendanceModel->getReportData($courseId, $startDate, $endDate);
        $course = $this->getCourseInfo($courseId);
        $institution = $this->institutionModel->getById($_SESSION['institution_id']);

        $pdf = new \TCPDF('L', 'mm', 'A4', true, 'UTF-8');
        
        $pdf->SetCreator('EcuAsist 2026');
        $pdf->SetAuthor('Sistema de Asistencia');
        $pdf->SetTitle('Reporte de Asistencia');
        
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        
        $pdf->AddPage();
        
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'REPORTE DE ASISTENCIA', 0, 1, 'C');
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 5, $institution['name'] ?? 'Institución Educativa', 0, 1, 'C');
        $pdf->Ln(5);
        
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(50, 6, 'Curso:', 0, 0);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 6, html_entity_decode($course['name'], ENT_QUOTES, 'UTF-8'), 0, 1);
        
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(50, 6, 'Período:', 0, 0);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 6, date('d/m/Y', strtotime($startDate)) . ' al ' . date('d/m/Y', strtotime($endDate)), 0, 1);
        $pdf->Ln(3);

        $html = '<table border="1" cellpadding="4">
                    <thead>
                        <tr style="background-color: #007bff; color: white; font-weight: bold;">
                            <th width="16.666666%">Fecha</th>
                            <th width="16.66666%">Estudiante</th>
                            <th width="16.66666%">Asignatura</th>
                            <th width="16.66666%">Hora</th>
                            <th width="16.66666%">Estado</th>
                            <th width="16.66666%">Observación</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($data as $row) {
            $statusColor = $this->getStatusColor($row['status']);
            $html .= '<tr>
                        <td>' . date('d/m/Y', strtotime($row['date'])) . '</td>
                        <td>' . $row['student_name'] . '</td>
                        <td>' . $row['subject_name'] . '</td>
                        <td>' . $row['hour_period'] . '</td>
                        <td style="background-color: ' . $statusColor . ';">' . ucfirst($row['status']) . '</td>
                        <td>' . ($row['observation'] ?: '-') . '</td>
                    </tr>';
        }

        $html .= '</tbody></table>';
        
        $pdf->SetFont('helvetica','',9);

        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Generar nombre de archivo con curso
        $courseName = $this->sanitizeFilename($course['name']);
        $filename = 'reporte_asistencia_' . $courseName . '_' . date('YmdHis') . '.pdf';
        
        $pdf->Output($filename, 'D');
        exit;
    }

    public function generateExcel() {
        $courseId = (int)$_POST['course_id'];
        $startDate = $_POST['start_date'];
        $endDate = $_POST['end_date'];

        $data = $this->attendanceModel->getReportData($courseId, $startDate, $endDate);
        $course = $this->getCourseInfo($courseId);
        $institution = $this->institutionModel->getById($_SESSION['institution_id']);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Asistencia');

        // Encabezado
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'REPORTE DE ASISTENCIA');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:F2');
        $sheet->setCellValue('A2', $institution['name'] ?? 'Institución Educativa');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A4', 'Curso:');
        $sheet->setCellValue('B4', html_entity_decode($course['name'], ENT_QUOTES, 'UTF-8'));
        $sheet->getStyle('A4')->getFont()->setBold(true);

        $sheet->setCellValue('A5', 'Período:');
        $sheet->setCellValue('B5', date('d/m/Y', strtotime($startDate)) . ' al ' . date('d/m/Y', strtotime($endDate)));
        $sheet->getStyle('A5')->getFont()->setBold(true);

        // Cabecera de tabla
        $row = 7;
        $headers = ['Fecha', 'Estudiante', 'Asignatura', 'Hora', 'Estado', 'Observación'];
        $col = 'A';
        
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle($col . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('007bff');
            $sheet->getStyle($col . $row)->getFont()->getColor()->setRGB('FFFFFF');
            $col++;
        }

        // Datos
        $row = 8;
        foreach ($data as $record) {
            $sheet->setCellValue('A' . $row, date('d/m/Y', strtotime($record['date'])));
            $sheet->setCellValue('B' . $row, $record['student_name']);
            $sheet->setCellValue('C' . $row, $record['subject_name']);
            $sheet->setCellValue('D' . $row, $record['hour_period']);
            $sheet->setCellValue('E' . $row, ucfirst($record['status']));
            $sheet->setCellValue('F' . $row, $record['observation'] ?: '-');

            $statusColor = $this->getStatusColorHex($record['status']);
            $sheet->getStyle('E' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB($statusColor);

            $row++;
        }

        // Ajustar anchos
        $sheet->getColumnDimension('A')->setWidth(12);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(25);

        // Bordes
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A7:F' . ($row - 1))->applyFromArray($styleArray);

        $writer = new Xlsx($spreadsheet);
        
        // Generar nombre de archivo con curso
        $courseName = $this->sanitizeFilename($course['name']);
        $filename = 'reporte_asistencia_' . $courseName . '_' . date('YmdHis') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    private function getCourseInfo($courseId) {
        $courses = $this->courseModel->getAll();
        foreach ($courses as $course) {
            if ($course['id'] == $courseId) {
                return $course;
            }
        }
        return null;
    }

    private function getStatusColor($status) {
        switch ($status) {
            case 'presente': return '#d4edda';
            case 'ausente': return '#f8d7da';
            case 'tardanza': return '#fff3cd';
            case 'justificado': return '#d1ecf1';
            default: return '#ffffff';
        }
    }

    private function getStatusColorHex($status) {
        switch ($status) {
            case 'presente': return '28a745';
            case 'ausente': return 'dc3545';
            case 'tardanza': return 'ffc107';
            case 'justificado': return '17a2b8';
            default: return 'FFFFFF';
        }
    }

    private function sanitizeFilename($filename) {
        // Decodificar HTML entities
        $filename = html_entity_decode($filename, ENT_QUOTES, 'UTF-8');
        
        // Reemplazar caracteres no permitidos en nombres de archivo
        $filename = str_replace(['"', '/', '\\', ':', '*', '?', '<', '>', '|'], '', $filename);
        
        // Reemplazar espacios con guiones bajos
        $filename = str_replace(' ', '_', $filename);
        
        // Reemplazar guiones múltiples
        $filename = preg_replace('/-+/', '-', $filename);
        
        // Limitar longitud (máximo 50 caracteres)
        if (strlen($filename) > 50) {
            $filename = substr($filename, 0, 50);
        }
        
        // Limpiar caracteres al inicio y final
        $filename = trim($filename, '-_');
        
        return $filename;
    }
}