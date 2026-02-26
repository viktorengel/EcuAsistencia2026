<?php
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/vendor/autoload.php';
require_once BASE_PATH . '/models/Course.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ExportController {
    private $courseModel;

    public function __construct() {
        Security::requireLogin();
        if (!Security::hasRole(['autoridad', 'docente'])) {
            die('Acceso denegado');
        }

        $db = new Database();
        $this->courseModel = new Course($db);
    }

    public function exportStudentList() {
        $courseId = (int)$_GET['course_id'];
        $students = $this->courseModel->getEnrolledStudents($courseId);
        
        $courses = $this->courseModel->getAll();
        $course = array_filter($courses, fn($c) => $c['id'] == $courseId);
        $course = reset($course);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Nómina');

        // Encabezado
        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A1', 'NÓMINA DE ESTUDIANTES');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A3', 'Curso:');
        $sheet->setCellValue('B3', $course['name'] . ' - ' . $course['shift_name']);
        $sheet->getStyle('A3')->getFont()->setBold(true);

        $sheet->setCellValue('A4', 'Total:');
        $sheet->setCellValue('B4', count($students) . ' estudiantes');
        $sheet->getStyle('A4')->getFont()->setBold(true);

        // Cabecera tabla
        $headers = ['#', 'Apellidos', 'Nombres', 'Cédula', 'Fecha Matrícula'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '6', $header);
            $sheet->getStyle($col . '6')->getFont()->setBold(true);
            $sheet->getStyle($col . '6')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('007bff');
            $sheet->getStyle($col . '6')->getFont()->getColor()->setRGB('FFFFFF');
            $col++;
        }

        // Datos
        $row = 7;
        $num = 1;
        foreach ($students as $student) {
            $sheet->setCellValue('A' . $row, $num++);
            $sheet->setCellValue('B' . $row, $student['last_name']);
            $sheet->setCellValue('C' . $row, $student['first_name']);
            $sheet->setCellValue('D' . $row, $student['dni'] ?? '-');
            $sheet->setCellValue('E' . $row, date('d/m/Y', strtotime($student['enrollment_date'])));
            $row++;
        }

        // Ajustar anchos
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);

        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="nomina_' . $course['name'] . '_' . date('YmdHis') . '.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}