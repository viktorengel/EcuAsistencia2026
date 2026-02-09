<?php
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/models/Attendance.php';
require_once BASE_PATH . '/models/Course.php';
require_once BASE_PATH . '/models/Subject.php';
require_once BASE_PATH . '/models/SchoolYear.php';
require_once BASE_PATH . '/models/Shift.php';

class AttendanceController {
    private $attendanceModel;
    private $courseModel;
    private $subjectModel;
    private $schoolYearModel;
    private $shiftModel;

    public function __construct() {
        Security::requireLogin();
        $db = new Database();
        
        $this->attendanceModel = new Attendance($db);
        $this->courseModel = new Course($db);
        $this->subjectModel = new Subject($db);
        $this->schoolYearModel = new SchoolYear($db);
        $this->shiftModel = new Shift($db);
    }

    public function register() {
        if (!Security::hasRole(['docente', 'autoridad'])) {
            die('Acceso denegado');
        }

        $courses = $this->courseModel->getAll();
        $subjects = $this->subjectModel->getAll();
        $shifts = $this->shiftModel->getAll();
        $activeYear = $this->schoolYearModel->getActive();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $courseId = (int)$_POST['course_id'];
            $subjectId = (int)$_POST['subject_id'];
            $shiftId = (int)$_POST['shift_id'];
            $date = $_POST['date'];
            $hourPeriod = $_POST['hour_period'];

            $students = $this->courseModel->getStudents($courseId);

            foreach ($students as $student) {
                $status = $_POST['status_' . $student['id']] ?? 'presente';
                $observation = $_POST['obs_' . $student['id']] ?? '';

                $this->attendanceModel->create([
                    ':student_id' => $student['id'],
                    ':course_id' => $courseId,
                    ':subject_id' => $subjectId,
                    ':teacher_id' => $_SESSION['user_id'],
                    ':school_year_id' => $activeYear['id'],
                    ':shift_id' => $shiftId,
                    ':date' => $date,
                    ':hour_period' => $hourPeriod,
                    ':status' => $status,
                    ':observation' => $observation
                ]);
            }

            header('Location: ?action=attendance_register&success=1');
            exit;
        }

        include BASE_PATH . '/views/attendance/register.php';
    }

    public function getStudents() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $courseId = (int)$_POST['course_id'];
            $students = $this->courseModel->getStudents($courseId);
            
            header('Content-Type: application/json');
            echo json_encode($students);
            exit;
        }
    }

    public function view() {
        if (!Security::hasRole(['docente', 'inspector', 'autoridad'])) {
            die('Acceso denegado');
        }

        $courses = $this->courseModel->getAll();
        $attendances = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $courseId = (int)$_POST['course_id'];
            $date = $_POST['date'];
            $attendances = $this->attendanceModel->getByCourse($courseId, $date);
        }

        include BASE_PATH . '/views/attendance/view.php';
    }

    public function myAttendance() {
        if (!Security::hasRole('estudiante')) {
            die('Acceso denegado');
        }

        $studentId = $_SESSION['user_id'];
        $attendances = $this->attendanceModel->getByStudent($studentId);

        include BASE_PATH . '/views/attendance/my_attendance.php';
    }
}