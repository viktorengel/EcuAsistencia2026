<?php
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/models/Course.php';
require_once BASE_PATH . '/models/Subject.php';
require_once BASE_PATH . '/models/SchoolYear.php';
require_once BASE_PATH . '/models/Shift.php';
require_once BASE_PATH . '/models/User.php';

class AcademicController {
    private $courseModel;
    private $subjectModel;
    private $schoolYearModel;
    private $shiftModel;
    private $userModel;
    private $db;

    public function __construct() {
        Security::requireLogin();
        if (!Security::hasRole('autoridad')) {
            die('Acceso denegado');
        }

        $this->db = new Database();
        $this->courseModel     = new Course($this->db);
        $this->subjectModel    = new Subject($this->db);
        $this->schoolYearModel = new SchoolYear($this->db);
        $this->shiftModel      = new Shift($this->db);
        $this->userModel       = new User($this->db);
    }

    public function index() {
        $courses     = $this->courseModel->getAllWithTutor();
        $subjects    = $this->subjectModel->getAll();
        $schoolYears = $this->schoolYearModel->getAll();
        $shifts      = $this->shiftModel->getAll();
        $teachers    = $this->userModel->getByRole('docente');

        // Asignaciones docente-materia
        require_once BASE_PATH . '/models/TeacherAssignment.php';
        $assignmentModel = new TeacherAssignment($this->db);
        $assignments     = $assignmentModel->getAll();
        // Solo asignaciones de docente-materia (no tutores)
        $assignments = array_filter($assignments, fn($a) => !$a['is_tutor']);

        // Datos para modal de estudiantes
        $activeYear        = $this->schoolYearModel->getActive();
        $availableStudents = $activeYear ? $this->userModel->getStudentsNotEnrolled($activeYear['id']) : [];
        $enrollmentsByCourse = [];
        foreach ($courses as $c) {
            $enrollmentsByCourse[$c['id']] = $this->courseModel->getEnrolledStudents($c['id']);
        }

        include BASE_PATH . '/views/academic/index.php';
    }

    public function createCourse() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        // Guardar datos del form en sesión para repoblar si hay error
        $_SESSION['course_form'] = [
            'education_type' => $_POST['education_type'] ?? '',
            'grade_level'    => $_POST['grade_level']    ?? '',
            'specialty'      => $_POST['specialty']      ?? '',
            'carrera'        => $_POST['carrera']        ?? '',
            'parallel'       => $_POST['parallel']       ?? '',
            'shift_id'       => $_POST['shift_id']       ?? '',
            'name'           => $_POST['name']           ?? '',
        ];

        $activeYear = $this->schoolYearModel->getActive();
        if (!$activeYear) {
            header('Location: ?action=academic&error=no_active_year'); exit;
        }

        $gradeLevel = html_entity_decode(Security::sanitize($_POST['grade_level']), ENT_QUOTES, 'UTF-8');
        $parallel   = Security::sanitize($_POST['parallel']);
        $shiftId    = (int)$_POST['shift_id'];

        // Validar curso duplicado
        $pdo = $this->db->connect();
        $stmtCheck = $pdo->prepare(
            "SELECT COUNT(*) as cnt FROM courses
             WHERE institution_id=:iid AND school_year_id=:syid
               AND grade_level=:gl AND parallel=:par AND shift_id=:sid"
        );
        $stmtCheck->execute([':iid'=>$_SESSION['institution_id'],':syid'=>$activeYear['id'],
                             ':gl'=>$gradeLevel,':par'=>$parallel,':sid'=>$shiftId]);
        if ($stmtCheck->fetch()['cnt'] > 0) {
            header('Location: ?action=academic&error=course_duplicate'); exit;
        }

        $data = [
            ':institution_id' => $_SESSION['institution_id'],
            ':school_year_id' => $activeYear['id'],
            ':name'        => html_entity_decode(Security::sanitize($_POST['name']), ENT_QUOTES, 'UTF-8'),
            ':grade_level' => $gradeLevel,
            ':parallel'    => $parallel,
            ':shift_id'    => $shiftId,
        ];
        $this->courseModel->create($data);
        $courseId = (int)$this->db->connect()->lastInsertId();

        // Auto-carga malla curricular
        $malla = $this->getMallaCurricular();
        $nuevas = 0;
        if (isset($malla[$gradeLevel]) && $courseId > 0) {
            foreach ($malla[$gradeLevel] as $nombre) {
                $stmtB = $pdo->prepare("SELECT id FROM subjects WHERE institution_id=:iid AND LOWER(TRIM(name))=LOWER(TRIM(:name)) LIMIT 1");
                $stmtB->execute([':iid'=>$_SESSION['institution_id'],':name'=>$nombre]);
                $existe = $stmtB->fetch();
                if ($existe) {
                    $sid = (int)$existe['id'];
                } else {
                    $codigo = strtoupper(substr(preg_replace('/[^a-zA-Z]/','',iconv('UTF-8','ASCII//TRANSLIT',$nombre)),0,4));
                    $this->subjectModel->create([':institution_id'=>$_SESSION['institution_id'],':name'=>$nombre,':code'=>$codigo]);
                    $sid = (int)$pdo->lastInsertId();
                    $nuevas++;
                }
                $this->linkSubjectToCourse($courseId, $sid);
            }
        }

        // Éxito: limpiar datos guardados
        unset($_SESSION['course_form']);
        header('Location: ?action=academic&course_success=1&subjects_loaded='.$nuevas); exit;
    }

    public function createSubject() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                ':institution_id' => $_SESSION['institution_id'],
                ':name' => Security::sanitize($_POST['name']),
                ':code' => Security::sanitize($_POST['code'])
            ];

            $this->subjectModel->create($data);
            header('Location: ?action=academic&subject_success=1');
            exit;
        }
    }

    // ============================================
    // CRUD CURSOS
    // ============================================

    public function editCourse() {
        $courseId = (int)$_GET['id'];
        $course = $this->courseModel->findById($courseId);

        if (!$course || $course['institution_id'] != $_SESSION['institution_id']) {
            header('Location: ?action=academic&error=course_not_found');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $shifts = $this->shiftModel->getAll();
            include BASE_PATH . '/views/academic/course_edit.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id' => $courseId,
                'name' => html_entity_decode(Security::sanitize($_POST['name']), ENT_QUOTES, 'UTF-8'),
                'grade_level' => html_entity_decode(Security::sanitize($_POST['grade_level']), ENT_QUOTES, 'UTF-8'),
                'parallel' => Security::sanitize($_POST['parallel']),
                'shift_id' => (int)$_POST['shift_id']
            ];

            if ($this->courseModel->update($data)) {
                header('Location: ?action=academic&course_updated=1');
                exit;
            } else {
                $errors[] = "Error al actualizar el curso";
                $shifts = $this->shiftModel->getAll();
                include BASE_PATH . '/views/academic/course_edit.php';
            }
        }
    }

    public function deleteCourse() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $courseId = (int)$_POST['course_id'];
            $course = $this->courseModel->findById($courseId);

            if (!$course || $course['institution_id'] != $_SESSION['institution_id']) {
                header('Location: ?action=academic&error=course_not_found');
                exit;
            }

            $db  = new Database();
            $pdo = $db->connect();

            // 1. Eliminar asignaciones docentes del curso
            $pdo->prepare("DELETE FROM teacher_assignments WHERE course_id = :id")
                ->execute([':id' => $courseId]);

            // 2. Eliminar horarios del curso
            $pdo->prepare("DELETE FROM class_schedule WHERE course_id = :id")
                ->execute([':id' => $courseId]);

            // 3. Eliminar matrículas
            $pdo->prepare("DELETE FROM course_students WHERE course_id = :id")
                ->execute([':id' => $courseId]);

            // 4. Eliminar relaciones curso-asignatura
            $pdo->prepare("DELETE FROM course_subjects WHERE course_id = :id")
                ->execute([':id' => $courseId]);

            // 5. Eliminar el curso
            if ($this->courseModel->delete($courseId)) {
                header('Location: ?action=academic&course_deleted=1');
                exit;
            } else {
                header('Location: ?action=academic&error=delete_failed');
                exit;
            }
        }
    }

    // ── Malla curricular ─────────────────────────────────────────────────────
    private function getMallaCurricular(): array {
        return [
            'Inicial 1 (0-3 años)' => ['Desarrollo Personal y Social','Expresión y Comunicación','Relación con el Entorno Natural y Cultural'],
            'Inicial 2 (3-5 años)' => ['Desarrollo Personal y Social','Expresión y Comunicación','Relación con el Entorno Natural y Cultural'],
            '1.º EGB - Preparatoria' => ['Currículo Integrador','Educación Física','Educación Cultural y Artística'],
            '2.º EGB'  => ['Lengua y Literatura','Matemática','Entorno Natural y Social','Inglés','Educación Física','Educación Cultural y Artística'],
            '3.º EGB'  => ['Lengua y Literatura','Matemática','Entorno Natural y Social','Inglés','Educación Física','Educación Cultural y Artística'],
            '4.º EGB'  => ['Lengua y Literatura','Matemática','Entorno Natural y Social','Inglés','Educación Física','Educación Cultural y Artística'],
            '5.º EGB'  => ['Lengua y Literatura','Matemática','Ciencias Naturales','Estudios Sociales','Inglés','Educación Física','Educación Cultural y Artística','Educación Financiera','Socioemocional','Cívica','Sostenibilidad','Seguridad Vial'],
            '6.º EGB'  => ['Lengua y Literatura','Matemática','Ciencias Naturales','Estudios Sociales','Inglés','Educación Física','Educación Cultural y Artística','Educación Financiera','Socioemocional','Cívica','Sostenibilidad','Seguridad Vial'],
            '7.º EGB'  => ['Lengua y Literatura','Matemática','Ciencias Naturales','Estudios Sociales','Inglés','Educación Física','Educación Cultural y Artística','Educación Financiera','Socioemocional','Cívica','Sostenibilidad','Seguridad Vial'],
            '8.º EGB'  => ['Lengua y Literatura','Matemática','Ciencias Naturales','Estudios Sociales','Inglés','Educación Física','Educación Cultural y Artística','Educación Financiera','Socioemocional','Cívica','Sostenibilidad','Seguridad Vial'],
            '9.º EGB'  => ['Lengua y Literatura','Matemática','Ciencias Naturales','Estudios Sociales','Inglés','Educación Física','Educación Cultural y Artística','Educación Financiera','Socioemocional','Cívica','Sostenibilidad','Seguridad Vial'],
            '10.º EGB' => ['Lengua y Literatura','Matemática','Ciencias Naturales','Estudios Sociales','Inglés','Educación Física','Educación Cultural y Artística','Educación Financiera','Socioemocional','Cívica','Sostenibilidad','Seguridad Vial'],
            '1.º BGU'  => ['Matemática','Física','Química','Biología','Historia','Educación para la Ciudadanía','Filosofía','Lengua y Literatura','Inglés','Educación Cultural y Artística','Educación Física','Emprendimiento y Gestión'],
            '2.º BGU'  => ['Matemática','Física','Química','Biología','Historia','Educación para la Ciudadanía','Filosofía','Lengua y Literatura','Inglés','Educación Cultural y Artística','Educación Física','Emprendimiento y Gestión'],
            '3.º BGU'  => ['Matemática','Física','Química','Biología','Historia','Lengua y Literatura','Inglés','Educación Física','Emprendimiento y Gestión'],
            '1.º BT'   => ['Lengua y Literatura','Matemática','Física','Química','Biología','Historia','Educación para la Ciudadanía','Filosofía','Inglés','Educación Física','Educación Cultural y Artística','Emprendimiento y Gestión','Módulos Técnicos'],
            '2.º BT'   => ['Lengua y Literatura','Matemática','Física','Química','Biología','Historia','Educación para la Ciudadanía','Filosofía','Inglés','Educación Física','Educación Cultural y Artística','Emprendimiento y Gestión','Módulos Técnicos'],
            '3.º BT'   => ['Lengua y Literatura','Matemática','Física','Química','Biología','Historia','Inglés','Educación Física','Emprendimiento y Gestión','Módulos Técnicos'],
        ];
    }

    // ── Asignaturas del curso CON docente ─────────────────────────────────
    private function getSubjectsByCourse(int $courseId): array {
        $activeYear = $this->schoolYearModel->getActive();
        $yearId = $activeYear ? $activeYear['id'] : 0;
        $stmt = $this->db->connect()->prepare(
            "SELECT s.id, s.name, s.code,
                    ta.id   AS assignment_id,
                    ta.teacher_id,
                    CONCAT(u.last_name,' ',u.first_name) AS teacher_name,
                    COALESCE(cs.hours_per_week, 1) AS hours_per_week
             FROM subjects s
             INNER JOIN course_subjects cs ON s.id = cs.subject_id AND cs.course_id = :cid
             LEFT JOIN teacher_assignments ta ON ta.subject_id = s.id
                    AND ta.course_id = :cid2 AND ta.school_year_id = :yid AND ta.is_tutor = 0
             LEFT JOIN users u ON u.id = ta.teacher_id
             ORDER BY s.name"
        );
        $stmt->execute([':cid'=>$courseId,':cid2'=>$courseId,':yid'=>$yearId]);
        return $stmt->fetchAll();
    }

    private function linkSubjectToCourse(int $courseId, int $subjectId): void {
        $stmt = $this->db->connect()->prepare(
            "INSERT IGNORE INTO course_subjects (course_id, subject_id) VALUES (:cid, :sid)"
        );
        $stmt->execute([':cid'=>$courseId,':sid'=>$subjectId]);
    }

    // ── Ver/gestionar asignaturas de un curso ─────────────────────────────
    public function courseSubjects() {
        $courseId = (int)($_GET['course_id'] ?? 0);
        if (!$courseId) { header('Location: ?action=academic'); exit; }

        $allCourses = $this->courseModel->getAll();
        $course = null;
        foreach ($allCourses as $c) { if ($c['id'] == $courseId) { $course = $c; break; } }
        if (!$course || $course['institution_id'] != $_SESSION['institution_id']) {
            header('Location: ?action=academic&error=course_not_found'); exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_subject_name'])) {
            $nombre = trim(Security::sanitize($_POST['new_subject_name']));
            $codigo = trim(Security::sanitize($_POST['new_subject_code'] ?? ''));
            if ($nombre !== '') {
                $pdo = $this->db->connect();
                $stmtB = $pdo->prepare("SELECT id FROM subjects WHERE institution_id=:iid AND LOWER(TRIM(name))=LOWER(TRIM(:name)) LIMIT 1");
                $stmtB->execute([':iid'=>$_SESSION['institution_id'],':name'=>$nombre]);
                $existe = $stmtB->fetch();
                if ($existe) { $sid = (int)$existe['id']; }
                else {
                    $this->subjectModel->create([':institution_id'=>$_SESSION['institution_id'],':name'=>$nombre,':code'=>$codigo]);
                    $sid = (int)$pdo->lastInsertId();
                }
                $this->linkSubjectToCourse($courseId, $sid);
            }
            header('Location: ?action=course_subjects&course_id='.$courseId.'&added=1'); exit;
        }

        $subjects  = $this->getSubjectsByCourse($courseId);
        $teachers  = $this->userModel->getByRole('docente');
        include BASE_PATH . '/views/academic/course_subjects.php';
    }

    // ── Asignar docente a asignatura desde el curso ───────────────────────
    public function assignSubjectTeacher() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ?action=academic'); exit; }

        $courseId   = (int)$_POST['course_id'];
        $subjectId  = (int)$_POST['subject_id'];
        $teacherId  = (int)$_POST['teacher_id'];
        $assignId   = (int)($_POST['assignment_id'] ?? 0);

        $activeYear = $this->schoolYearModel->getActive();
        if (!$activeYear) { header('Location: ?action=course_subjects&course_id='.$courseId.'&assign_error='.urlencode('No hay año lectivo activo')); exit; }

        $pdo = $this->db->connect();

        // Si ya existe asignación para esta asignatura en este curso, actualizarla
        if ($assignId > 0) {
            $pdo->prepare("UPDATE teacher_assignments SET teacher_id=:tid WHERE id=:id")
                ->execute([':tid'=>$teacherId,':id'=>$assignId]);
        } else {
            // Verificar que no exista ya
            $stmtCheck = $pdo->prepare("SELECT id FROM teacher_assignments WHERE course_id=:cid AND subject_id=:sid AND school_year_id=:yid AND is_tutor=0");
            $stmtCheck->execute([':cid'=>$courseId,':sid'=>$subjectId,':yid'=>$activeYear['id']]);
            $existe = $stmtCheck->fetch();
            if ($existe) {
                $pdo->prepare("UPDATE teacher_assignments SET teacher_id=:tid WHERE id=:id")
                    ->execute([':tid'=>$teacherId,':id'=>$existe['id']]);
            } else {
                $pdo->prepare("INSERT INTO teacher_assignments (teacher_id,course_id,subject_id,school_year_id,is_tutor) VALUES (:tid,:cid,:sid,:yid,0)")
                    ->execute([':tid'=>$teacherId,':cid'=>$courseId,':sid'=>$subjectId,':yid'=>$activeYear['id']]);
            }
        }
        header('Location: ?action=course_subjects&course_id='.$courseId.'&assigned=1'); exit;
    }

    // ── Quitar docente de una asignatura ──────────────────────────────────
    public function unassignSubjectTeacher() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ?action=academic'); exit; }
        $assignId = (int)$_POST['assignment_id'];
        $courseId = (int)$_POST['course_id'];
        $pdo = $this->db->connect();
        $pdo->prepare("DELETE FROM teacher_assignments WHERE id=:id AND is_tutor=0")->execute([':id'=>$assignId]);
        header('Location: ?action=course_subjects&course_id='.$courseId.'&unassigned=1'); exit;
    }

    // ── Editar asignatura desde el curso ──────────────────────────────────
    public function editCourseSubject() {
        $subjectId = (int)($_GET['subject_id'] ?? 0);
        $courseId  = (int)($_GET['course_id']  ?? 0);
        if (!$subjectId || !$courseId) { header('Location: ?action=academic'); exit; }
        $subject = $this->subjectModel->findById($subjectId);
        if (!$subject || $subject['institution_id'] != $_SESSION['institution_id']) {
            header('Location: ?action=course_subjects&course_id='.$courseId.'&error=not_found'); exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->subjectModel->update(['id'=>$subjectId,'name'=>Security::sanitize($_POST['name']),'code'=>Security::sanitize($_POST['code'])]);
            header('Location: ?action=course_subjects&course_id='.$courseId.'&updated=1'); exit;
        }
        include BASE_PATH . '/views/academic/course_subject_edit.php';
    }

    // ── Quitar asignatura de un curso ─────────────────────────────────────
    public function removeCourseSubject() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ?action=academic'); exit; }
        $courseId  = (int)$_POST['course_id'];
        $subjectId = (int)$_POST['subject_id'];
        $pdo  = $this->db->connect();
        $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM teacher_assignments WHERE course_id=:cid AND subject_id=:sid");
        $stmt->execute([':cid'=>$courseId,':sid'=>$subjectId]);
        if ($stmt->fetch()['cnt'] > 0) {
            header('Location: ?action=course_subjects&course_id='.$courseId.'&error=has_teacher'); exit;
        }
        $pdo->prepare("DELETE FROM course_subjects WHERE course_id=:cid AND subject_id=:sid")->execute([':cid'=>$courseId,':sid'=>$subjectId]);
        header('Location: ?action=course_subjects&course_id='.$courseId.'&removed=1'); exit;
    }

    // ============================================
    // CRUD ASIGNATURAS
    // ============================================

    public function editSubject() {
        $subjectId = (int)$_GET['id'];
        $subject = $this->subjectModel->findById($subjectId);

        if (!$subject || $subject['institution_id'] != $_SESSION['institution_id']) {
            header('Location: ?action=academic&error=subject_not_found');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            include BASE_PATH . '/views/academic/subject_edit.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id' => $subjectId,
                'name' => Security::sanitize($_POST['name']),
                'code' => Security::sanitize($_POST['code'])
            ];

            if ($this->subjectModel->update($data)) {
                header('Location: ?action=academic&subject_updated=1');
                exit;
            } else {
                $errors[] = "Error al actualizar la asignatura";
                include BASE_PATH . '/views/academic/subject_edit.php';
            }
        }
    }

    public function deleteSubject() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $subjectId = (int)$_POST['subject_id'];
            $subject = $this->subjectModel->findById($subjectId);

            if (!$subject || $subject['institution_id'] != $_SESSION['institution_id']) {
                header('Location: ?action=academic&error=subject_not_found');
                exit;
            }

            // Verificar si tiene asignaciones docentes
            $db = new Database();
            $stmt = $db->connect()->prepare("SELECT COUNT(*) as count FROM teacher_assignments WHERE subject_id = :id");
            $stmt->execute([':id' => $subjectId]);
            $result = $stmt->fetch();
            
            if ($result['count'] > 0) {
                header('Location: ?action=academic&error=subject_has_assignments');
                exit;
            }

            if ($this->subjectModel->delete($subjectId)) {
                header('Location: ?action=academic&subject_deleted=1');
                exit;
            } else {
                header('Location: ?action=academic&error=delete_failed');
                exit;
            }
        }
    }

    public function enrollStudents() {
        $activeYear = $this->schoolYearModel->getActive();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $courseId   = (int)$_POST['course_id'];
            $redirectTo = $_POST['redirect_to'] ?? 'enroll_students';
            $studentIds = $_POST['student_ids'] ?? [];

            $enrolled = 0;
            $errors   = 0;

            foreach ($studentIds as $studentId) {
                if ($this->courseModel->enrollStudent($courseId, (int)$studentId, $activeYear['id'])) {
                    $enrolled++;
                } else {
                    $errors++;
                }
            }

            header('Location: ?action=' . $redirectTo . '&course_id=' . $courseId . '&enrolled=' . $enrolled . '&errors=' . $errors);
            exit;
        }

        $courseId = (int)($_GET['course_id'] ?? 0);
        if (!$courseId) {
            header('Location: ?action=academic');
            exit;
        }

        $course = $this->courseModel->findById($courseId);
        if (!$course) {
            header('Location: ?action=academic');
            exit;
        }

        $availableStudents = $this->userModel->getStudentsNotEnrolled($activeYear['id']);
        $allStudents = $this->userModel->getByRole('estudiante');

        include BASE_PATH . '/views/academic/enroll.php';
    }

    public function unenrollStudent() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $studentId  = (int)$_POST['student_id'];
            $courseId   = (int)($_POST['course_id'] ?? 0);
            $redirectTo = $_POST['redirect_to'] ?? 'enroll_students';
            $activeYear = $this->schoolYearModel->getActive();

            $base = '?action=' . $redirectTo . '&course_id=' . $courseId;

            if (!$activeYear) {
                header('Location: ' . $base . '&error=no_active_year');
                exit;
            }

            $course = $this->userModel->getStudentCourse($studentId, $activeYear['id']);

            if (!$course) {
                header('Location: ' . $base . '&error=not_enrolled');
                exit;
            }

            if ($this->courseModel->unenrollStudent($studentId, $activeYear['id'])) {
                header('Location: ' . $base . '&unenrolled=1');
                exit;
            } else {
                header('Location: ' . $base . '&error=unenroll_failed');
                exit;
            }
        }
    }

    public function viewCourseStudents() {
        $courseId = (int)($_GET['course_id'] ?? 0);

        if (!$courseId) {
            header('Location: ?action=academic');
            exit;
        }

        $course = $this->courseModel->findById($courseId);
        if (!$course) {
            header('Location: ?action=academic');
            exit;
        }

        $activeYear        = $this->schoolYearModel->getActive();
        $students          = $this->courseModel->getEnrolledStudents($courseId);
        $availableStudents = $activeYear ? $this->userModel->getStudentsNotEnrolled($activeYear['id']) : [];

        include BASE_PATH . '/views/academic/course_students.php';
    }

    // ============================================
    // CRUD AÑOS LECTIVOS
    // ============================================

    public function createSchoolYear() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            include BASE_PATH . '/views/academic/school_year_create.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];

            // Validaciones
            if (empty($_POST['name'])) {
                $errors[] = "El nombre es obligatorio";
            }
            if (empty($_POST['start_date'])) {
                $errors[] = "La fecha de inicio es obligatoria";
            }
            if (empty($_POST['end_date'])) {
                $errors[] = "La fecha de fin es obligatoria";
            }

            if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
                if (strtotime($_POST['end_date']) <= strtotime($_POST['start_date'])) {
                    $errors[] = "La fecha de fin debe ser posterior a la fecha de inicio";
                }

                // Verificar solapamiento de fechas
                if ($this->schoolYearModel->checkOverlap($_POST['start_date'], $_POST['end_date'])) {
                    $errors[] = "Ya existe un año lectivo con fechas que se solapan";
                }
            }

            if (!empty($errors)) {
                include BASE_PATH . '/views/academic/school_year_create.php';
                return;
            }

            // Crear año lectivo
            $data = [
                'institution_id' => $_SESSION['institution_id'],
                'name' => Security::sanitize($_POST['name']),
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            // Si se marca como activo, desactivar los demás ANTES de insertar
            if ($data['is_active']) {
                $this->schoolYearModel->activate(0);
            }

            // create() ahora devuelve el ID insertado (o 0 si falla)
            $newId = $this->schoolYearModel->create($data);

            if ($newId > 0) {
                // Activar el nuevo año lectivo con el ID real
                if ($data['is_active']) {
                    $this->schoolYearModel->activate($newId);
                }
                header('Location: ?action=academic&sy_created=1');
                exit;
            } else {
                $errors[] = "Error al crear el año lectivo";
                include BASE_PATH . '/views/academic/school_year_create.php';
            }
        }
    }

    public function editSchoolYear() {
        $yearId = (int)$_GET['id'];
        $year = $this->schoolYearModel->findById($yearId);

        if (!$year) {
            header('Location: ?action=academic&error=year_not_found');
            exit;
        }

        // Verificar institución
        if ($year['institution_id'] != $_SESSION['institution_id']) {
            die('Acceso denegado');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            include BASE_PATH . '/views/academic/school_year_edit.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];

            // Validaciones
            if (empty($_POST['name'])) {
                $errors[] = "El nombre es obligatorio";
            }
            if (empty($_POST['start_date'])) {
                $errors[] = "La fecha de inicio es obligatoria";
            }
            if (empty($_POST['end_date'])) {
                $errors[] = "La fecha de fin es obligatoria";
            }

            if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
                if (strtotime($_POST['end_date']) <= strtotime($_POST['start_date'])) {
                    $errors[] = "La fecha de fin debe ser posterior a la fecha de inicio";
                }

                // Verificar solapamiento (excluyendo el actual)
                if ($this->schoolYearModel->checkOverlap($_POST['start_date'], $_POST['end_date'], $yearId)) {
                    $errors[] = "Ya existe otro año lectivo con fechas que se solapan";
                }
            }

            if (!empty($errors)) {
                include BASE_PATH . '/views/academic/school_year_edit.php';
                return;
            }

            // Actualizar año lectivo
            $data = [
                'id' => $yearId,
                'name' => Security::sanitize($_POST['name']),
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date']
            ];

            if ($this->schoolYearModel->update($data)) {
                header('Location: ?action=academic&sy_updated=1');
                exit;
            } else {
                $errors[] = "Error al actualizar el año lectivo";
                include BASE_PATH . '/views/academic/school_year_edit.php';
            }
        }
    }

    public function deleteSchoolYear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $yearId = (int)$_POST['year_id'];
            $year = $this->schoolYearModel->findById($yearId);

            if (!$year) {
                header('Location: ?action=academic&error=year_not_found');
                exit;
            }

            // Verificar institución
            if ($year['institution_id'] != $_SESSION['institution_id']) {
                die('Acceso denegado');
            }

            // No permitir eliminar año activo
            if ($year['is_active'] == 1) {
                header('Location: ?action=academic&error=cannot_delete_active');
                exit;
            }

            if ($this->schoolYearModel->delete($yearId)) {
                header('Location: ?action=academic&sy_deleted=1');
                exit;
            } else {
                header('Location: ?action=academic&error=has_courses');
                exit;
            }
        }
    }

    public function activateSchoolYear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $yearId = (int)$_POST['year_id'];
            $year = $this->schoolYearModel->findById($yearId);

            if (!$year) {
                header('Location: ?action=academic&error=year_not_found');
                exit;
            }

            // Verificar institución
            if ($year['institution_id'] != $_SESSION['institution_id']) {
                die('Acceso denegado');
            }

            if ($this->schoolYearModel->activate($yearId)) {
                header('Location: ?action=academic&sy_activated=1');
                exit;
            } else {
                header('Location: ?action=academic&error=activate_failed');
                exit;
            }
        }
    }

    public function deactivateSchoolYear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $yearId = (int)$_POST['year_id'];
            $year = $this->schoolYearModel->findById($yearId);

            if (!$year) {
                header('Location: ?action=academic&error=year_not_found');
                exit;
            }

            // Verificar institución
            if ($year['institution_id'] != $_SESSION['institution_id']) {
                die('Acceso denegado');
            }

            if ($this->schoolYearModel->deactivate($yearId)) {
                header('Location: ?action=academic&sy_deactivated=1');
                exit;
            } else {
                header('Location: ?action=academic&error=deactivate_failed');
                exit;
            }
        }
    }

    public function setSubjectHours() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?action=academic'); exit;
        }
        $courseId  = (int)($_GET['course_id'] ?? $_POST['course_id'] ?? 0);
        $subjectId = (int)$_POST['subject_id'];
        $hours     = max(1, min(20, (int)$_POST['hours_per_week']));

        $pdo = $this->db->connect();
        $pdo->prepare("UPDATE course_subjects SET hours_per_week = :h WHERE course_id = :cid AND subject_id = :sid")
            ->execute([':h' => $hours, ':cid' => $courseId, ':sid' => $subjectId]);

        header('Location: ?action=course_subjects&course_id=' . $courseId . '&updated=1');
        exit;
    }
}