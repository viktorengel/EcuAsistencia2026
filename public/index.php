<?php
require_once '../config/config.php';

$action = $_GET['action'] ?? 'login';

switch ($action) {
    case 'register':
        require_once BASE_PATH . '/controllers/AuthController.php';
        $auth = new AuthController();
        $auth->register();
        break;
    
    case 'login':
        require_once BASE_PATH . '/controllers/AuthController.php';
        $auth = new AuthController();
        $auth->login();
        break;
    
    case 'logout':
        require_once BASE_PATH . '/controllers/AuthController.php';
        $auth = new AuthController();
        $auth->logout();
        break;
    
    case 'forgot':
        require_once BASE_PATH . '/controllers/AuthController.php';
        $auth = new AuthController();
        $auth->forgotPassword();
        break;
    
    case 'reset':
        require_once BASE_PATH . '/controllers/AuthController.php';
        $auth = new AuthController();
        $auth->resetPassword();
        break;
    
    case 'dashboard':
        require_once BASE_PATH . '/controllers/DashboardController.php';
        $dash = new DashboardController();
        $dash->index();
        break;
    
    case 'users':
        require_once BASE_PATH . '/controllers/UserController.php';
        $userCtrl = new UserController();
        $userCtrl->index();
        break;
    
    case 'assign_role':
        require_once BASE_PATH . '/controllers/UserController.php';
        $userCtrl = new UserController();
        $userCtrl->assignRole();
        break;
    
    case 'attendance_register':
        require_once BASE_PATH . '/controllers/AttendanceController.php';
        $attCtrl = new AttendanceController();
        $attCtrl->register();
        break;
    
    case 'get_students':
        require_once BASE_PATH . '/controllers/AttendanceController.php';
        $attCtrl = new AttendanceController();
        $attCtrl->getStudents();
        break;
    
    case 'attendance_view':
        require_once BASE_PATH . '/controllers/AttendanceController.php';
        $attCtrl = new AttendanceController();
        $attCtrl->view();
        break;
    
    case 'my_attendance':
        require_once BASE_PATH . '/controllers/AttendanceController.php';
        $attCtrl = new AttendanceController();
        $attCtrl->myAttendance();
        break;
    
    case 'academic':
        require_once BASE_PATH . '/controllers/AcademicController.php';
        $acadCtrl = new AcademicController();
        $acadCtrl->index();
        break;
    
    case 'create_course':
        require_once BASE_PATH . '/controllers/AcademicController.php';
        $acadCtrl = new AcademicController();
        $acadCtrl->createCourse();
        break;
    
    case 'create_subject':
        require_once BASE_PATH . '/controllers/AcademicController.php';
        $acadCtrl = new AcademicController();
        $acadCtrl->createSubject();
        break;
    
    case 'enroll_students':
        require_once BASE_PATH . '/controllers/AcademicController.php';
        $acadCtrl = new AcademicController();
        $acadCtrl->enrollStudents();
        break;
    
    case 'view_course_students':
        require_once BASE_PATH . '/controllers/AcademicController.php';
        $acadCtrl = new AcademicController();
        $acadCtrl->viewCourseStudents();
        break;
    
    case 'reports':
        require_once BASE_PATH . '/controllers/ReportController.php';
        $reportCtrl = new ReportController();
        $reportCtrl->index();
        break;
    
    case 'generate_pdf':
        require_once BASE_PATH . '/controllers/ReportController.php';
        $reportCtrl = new ReportController();
        $reportCtrl->generatePDF();
        break;
    
    case 'generate_excel':
        require_once BASE_PATH . '/controllers/ReportController.php';
        $reportCtrl = new ReportController();
        $reportCtrl->generateExcel();
        break;
    
    case 'manage_representatives':
        require_once BASE_PATH . '/controllers/RepresentativeController.php';
        $repCtrl = new RepresentativeController();
        $repCtrl->manageRepresentatives();
        break;
    
    case 'my_children':
        require_once BASE_PATH . '/controllers/RepresentativeController.php';
        $repCtrl = new RepresentativeController();
        $repCtrl->myChildren();
        break;
    
    case 'child_attendance':
        require_once BASE_PATH . '/controllers/RepresentativeController.php';
        $repCtrl = new RepresentativeController();
        $repCtrl->childAttendance();
        break;
    
    case 'assignments':
        require_once BASE_PATH . '/controllers/AssignmentController.php';
        $assignCtrl = new AssignmentController();
        $assignCtrl->index();
        break;
    
    case 'create_assignment':
        require_once BASE_PATH . '/controllers/AssignmentController.php';
        $assignCtrl = new AssignmentController();
        $assignCtrl->assign();
        break;
    
    case 'set_tutor':
        require_once BASE_PATH . '/controllers/AssignmentController.php';
        $assignCtrl = new AssignmentController();
        $assignCtrl->setTutor();
        break;
    
    case 'remove_assignment':
        require_once BASE_PATH . '/controllers/AssignmentController.php';
        $assignCtrl = new AssignmentController();
        $assignCtrl->remove();
        break;
    
    case 'view_course_assignments':
        require_once BASE_PATH . '/controllers/AssignmentController.php';
        $assignCtrl = new AssignmentController();
        $assignCtrl->viewByCourse();
        break;

    case 'profile':
        require_once BASE_PATH . '/controllers/ProfileController.php';
        $profileCtrl = new ProfileController();
        $profileCtrl->view();
        break;

    case 'edit_profile':
        require_once BASE_PATH . '/controllers/ProfileController.php';
        $profileCtrl = new ProfileController();
        $profileCtrl->edit();
        break;

    case 'change_password':
        require_once BASE_PATH . '/controllers/ProfileController.php';
        $profileCtrl = new ProfileController();
        $profileCtrl->changePassword();
        break;

    case 'search_students':
        require_once BASE_PATH . '/controllers/SearchController.php';
        $searchCtrl = new SearchController();
        $searchCtrl->searchStudents();
        break;

    case 'export_student_list':
        require_once BASE_PATH . '/controllers/ExportController.php';
        $exportCtrl = new ExportController();
        $exportCtrl->exportStudentList();
        break;
    
    default:
        header('Location: ?action=login');
}