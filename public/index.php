<?php

if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1') {
    require_once __DIR__ . '/../config/config.php';  // local
} else {
    require_once '/home/ecuasysc/ecuasistencia/config/config.php';  // producción
}

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
    
    case 'edit_attendance':
        require_once BASE_PATH . '/controllers/AttendanceController.php';
        $attCtrl = new AttendanceController();
        $attCtrl->editAttendance();
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
    
    case 'set_subject_hours':
        require_once BASE_PATH . '/controllers/AcademicController.php';
        $controller = new AcademicController();
        $controller->setSubjectHours();
        break;

    case 'course_subjects':
        require_once BASE_PATH . '/controllers/AcademicController.php';
        $acadCtrl = new AcademicController();
        $acadCtrl->courseSubjects();
        break;

    case 'view_course_students':
        require_once BASE_PATH . '/controllers/AcademicController.php';
        $acadCtrl = new AcademicController();
        $acadCtrl->viewCourseStudents();
        break;

    case 'edit_course_subject':
        require_once BASE_PATH . '/controllers/AcademicController.php';
        $acadCtrl = new AcademicController();
        $acadCtrl->editCourseSubject();
        break;

    case 'remove_course_subject':
        require_once BASE_PATH . '/controllers/AcademicController.php';
        $acadCtrl = new AcademicController();
        $acadCtrl->removeCourseSubject();
        break;

    case 'assign_subject_teacher':
        require_once BASE_PATH . '/controllers/AcademicController.php';
        $acadCtrl = new AcademicController();
        $acadCtrl->assignSubjectTeacher();
        break;

    case 'unassign_subject_teacher':
        require_once BASE_PATH . '/controllers/AcademicController.php';
        $acadCtrl = new AcademicController();
        $acadCtrl->unassignSubjectTeacher();
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

    case 'remove_tutor':
        require_once BASE_PATH . '/controllers/AssignmentController.php';
        $controller = new AssignmentController();
        $controller->removeTutor();
        break;

    case 'get_course_teachers':
        require_once BASE_PATH . '/controllers/AssignmentController.php';
        $controller = new AssignmentController();
        $controller->getCourseTeachers();
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
    
    case 'stats':
        require_once BASE_PATH . '/controllers/StatsController.php';
        $statsCtrl = new StatsController();
        $statsCtrl->index();
        break;

    case 'attendance_calendar':
        require_once BASE_PATH . '/controllers/AttendanceController.php';
        $attCtrl = new AttendanceController();
        $attCtrl->calendar();
        break;
    
    case 'submit_justification':
        require_once BASE_PATH . '/controllers/JustificationController.php';
        $justCtrl = new JustificationController();
        $justCtrl->submit();
        break;

    case 'my_justifications':
        require_once BASE_PATH . '/controllers/JustificationController.php';
        $justCtrl = new JustificationController();
        $justCtrl->myJustifications();
        break;

    case 'pending_justifications':
        require_once BASE_PATH . '/controllers/JustificationController.php';
        $justCtrl = new JustificationController();
        $justCtrl->pending();
        break;

    case 'reviewed_justifications':
        require_once BASE_PATH . '/controllers/JustificationController.php';
        $controller = new JustificationController();
        $controller->reviewed();
        break;

    case 'tutor_pending_justifications':
        require_once BASE_PATH . '/controllers/JustificationController.php';
        $justCtrl = new JustificationController();
        $justCtrl->pendingForTutor();
        break;

    case 'review_justification':
        require_once BASE_PATH . '/controllers/JustificationController.php';
        $justCtrl = new JustificationController();
        $justCtrl->review();
        break;

    case 'backups':
        require_once BASE_PATH . '/controllers/BackupController.php';
        $backupCtrl = new BackupController();
        $backupCtrl->index();
        break;

    case 'create_backup':
        require_once BASE_PATH . '/controllers/BackupController.php';
        $backupCtrl = new BackupController();
        $backupCtrl->create();
        break;

    case 'download_backup':
        require_once BASE_PATH . '/controllers/BackupController.php';
        $backupCtrl = new BackupController();
        $backupCtrl->download();
        break;

    case 'cleanup_backups':
        require_once BASE_PATH . '/controllers/BackupController.php';
        $backupCtrl = new BackupController();
        $backupCtrl->cleanup();
        break;

    case 'remove_role':
        require_once BASE_PATH . '/controllers/UserController.php';
        $userCtrl = new UserController();
        $userCtrl->removeRole();
        break;

    case 'get_course_subjects':
        require_once BASE_PATH . '/controllers/AttendanceController.php';
        $controller = new AttendanceController();
        $controller->getCourseSubjects();
        break;

    case 'get_teacher_course_subjects':
            require_once BASE_PATH . '/controllers/AttendanceController.php';
            $controller = new AttendanceController();
            $controller->getTeacherCourseSubjects();
            break;

    case 'schedules':
            require_once BASE_PATH . '/controllers/ScheduleController.php';
            $controller = new ScheduleController();
            $controller->index();
            break;
            
    case 'manage_schedule':
        require_once BASE_PATH . '/controllers/ScheduleController.php';
        $controller = new ScheduleController();
        $controller->manageCourse();
        break;
        
    case 'delete_schedule_class':
        require_once BASE_PATH . '/controllers/ScheduleController.php';
        $controller = new ScheduleController();
        $controller->deleteClass();
        break;

    case 'swap_schedule_class':
        require_once BASE_PATH . '/controllers/ScheduleController.php';
        $controller = new ScheduleController();
        $controller->swapClass();
        break;

    case 'move_schedule_class':
        require_once BASE_PATH . '/controllers/ScheduleController.php';
        $controller = new ScheduleController();
        $controller->moveClass();
        break;
        
    case 'get_schedule_info':
        require_once BASE_PATH . '/controllers/AttendanceController.php';
        $controller = new AttendanceController();
        $controller->getScheduleInfo();
        break;

    case 'get_existing_attendance':
        require_once BASE_PATH . '/controllers/AttendanceController.php';
        $controller = new AttendanceController();
        $controller->getExistingAttendance();
        break;

    case 'tutor_management':
        require_once BASE_PATH . '/controllers/AssignmentController.php';
        $controller = new AssignmentController();
        $controller->tutorManagement();
        break;

    case 'check_course_tutor':
        require_once BASE_PATH . '/controllers/AssignmentController.php';
        $controller = new AssignmentController();
        $controller->checkCourseTutor();
        break;

    case 'get_course_subjects_schedule':
        require_once BASE_PATH . '/controllers/ScheduleController.php';
        $controller = new ScheduleController();
        $controller->getCourseSubjectsSchedule();
        break;

    case 'check_schedule_conflict':
        require_once BASE_PATH . '/controllers/ScheduleController.php';
        $controller = new ScheduleController();
        $controller->checkScheduleConflict();
        break;

    case 'institution':
        require_once BASE_PATH . '/controllers/InstitutionController.php';
        $controller = new InstitutionController();
        $controller->index();
        break;
        
    case 'update_institution':
        require_once BASE_PATH . '/controllers/InstitutionController.php';
        $controller = new InstitutionController();
        $controller->update();
        break;
        
    case 'assign_institution_shift':
        require_once BASE_PATH . '/controllers/InstitutionController.php';
        $controller = new InstitutionController();
        $controller->assignShift();
        break;
        
    case 'remove_institution_shift':
        require_once BASE_PATH . '/controllers/InstitutionController.php';
        $controller = new InstitutionController();
        $controller->removeShift();
        break;

    case 'toggle_institution_shift':
        require_once BASE_PATH . '/controllers/InstitutionController.php';
        (new InstitutionController())->toggleShift();
        break;
    
    case 'create_user':
        require_once BASE_PATH . '/controllers/UserController.php';
        $controller = new UserController();
        $controller->create();
        break;

    case 'create_user_modal':
        require_once BASE_PATH . '/controllers/UserController.php';
        $controller = new UserController();
        $controller->createFromModal();
        break;

    case 'edit_user_modal':
        require_once BASE_PATH . '/controllers/UserController.php';
        $controller = new UserController();
        $controller->editFromModal();
        break;

    case 'edit_user':
        require_once BASE_PATH . '/controllers/UserController.php';
        $controller = new UserController();
        $controller->edit();
        break;

    case 'delete_user':
        require_once BASE_PATH . '/controllers/UserController.php';
        $controller = new UserController();
        $controller->delete();
        break;

    case 'create_school_year':
        require_once BASE_PATH . '/controllers/AcademicController.php';
        $controller = new AcademicController();
        $controller->createSchoolYear();
        break;

    case 'edit_school_year':
        require_once BASE_PATH . '/controllers/AcademicController.php';
        $controller = new AcademicController();
        $controller->editSchoolYear();
        break;

    case 'delete_school_year':
        require_once BASE_PATH . '/controllers/AcademicController.php';
        $controller = new AcademicController();
        $controller->deleteSchoolYear();
        break;

    case 'activate_school_year':
        require_once BASE_PATH . '/controllers/AcademicController.php';
        $controller = new AcademicController();
        $controller->activateSchoolYear();
        break;

    case 'deactivate_school_year':
        require_once BASE_PATH . '/controllers/AcademicController.php';
        $controller = new AcademicController();
        $controller->deactivateSchoolYear();
        break;

    case 'edit_course':
        require_once BASE_PATH . '/controllers/AcademicController.php';
        $controller = new AcademicController();
        $controller->editCourse();
        break;

    case 'delete_course':
        require_once BASE_PATH . '/controllers/AcademicController.php';
        $controller = new AcademicController();
        $controller->deleteCourse();
        break;

    case 'edit_subject':
        require_once BASE_PATH . '/controllers/AcademicController.php';
        $controller = new AcademicController();
        $controller->editSubject();
        break;

    case 'delete_subject':
        require_once BASE_PATH . '/controllers/AcademicController.php';
        $controller = new AcademicController();
        $controller->deleteSubject();
        break;

    case 'enroll_students':
        require_once BASE_PATH . '/controllers/AcademicController.php';
        $controller = new AcademicController();
        $controller->enrollStudents();
        break;

    case 'unenroll_student':
        require_once BASE_PATH . '/controllers/AcademicController.php';
        $controller = new AcademicController();
        $controller->unenrollStudent();
        break;

    case 'delete_backup':
        require_once BASE_PATH . '/controllers/BackupController.php';
        $controller = new BackupController();
        $controller->delete();
        break;

    case 'remove_representative':
        require_once BASE_PATH . '/controllers/RepresentativeController.php';
        $controller = new RepresentativeController();
        $controller->removeRelation();
        break;

    case 'assign_rep_from_academic':
        require_once BASE_PATH . '/controllers/RepresentativeController.php';
        $controller = new RepresentativeController();
        $controller->assignFromAcademic();
        break;

    case 'remove_rep_from_academic':
        require_once BASE_PATH . '/controllers/RepresentativeController.php';
        $controller = new RepresentativeController();
        $controller->removeFromAcademic();
        break;

    case 'toggle_primary_representative':
        require_once BASE_PATH . '/controllers/RepresentativeController.php';
        $controller = new RepresentativeController();
        $controller->togglePrimary();
        break;

    case 'edit_representative':
        require_once BASE_PATH . '/controllers/RepresentativeController.php';
        $controller = new RepresentativeController();
        $controller->editRelation();
        break;

    case 'notifications':
        require_once BASE_PATH . '/controllers/NotificationController.php';
        (new NotificationController())->index();
        break;

    case 'notifications_mark_read':
        require_once BASE_PATH . '/controllers/NotificationController.php';
        (new NotificationController())->markRead();
        break;

    case 'notifications_mark_all':
        require_once BASE_PATH . '/controllers/NotificationController.php';
        (new NotificationController())->markAllRead();
        break;

    case 'notifications_delete':
        require_once BASE_PATH . '/controllers/NotificationController.php';
        (new NotificationController())->delete();
        break;

    case 'notifications_delete_read':
        require_once BASE_PATH . '/controllers/NotificationController.php';
        (new NotificationController())->deleteRead();
        break;

    case 'notifications_unread_json':
        require_once BASE_PATH . '/controllers/NotificationController.php';
        (new NotificationController())->getUnread();
        break;

    case 'tutor_course_attendance':
        require_once BASE_PATH . '/controllers/TutorController.php';
        (new TutorController())->courseAttendance();
        break;

    case 'tutor_course_attendance_ajax':
        require_once BASE_PATH . '/controllers/TutorController.php';
        (new TutorController())->ajax();
        break;


    case 'unlink_student':
        require_once BASE_PATH . '/controllers/RepresentativeController.php';
        (new RepresentativeController())->unlinkStudent();
        break;

    case 'search_students_json':
        require_once BASE_PATH . '/controllers/RepresentativeController.php';
        $repCtrl = new RepresentativeController();
        $repCtrl->searchStudentsJson();
        break;

    case 'request_link':
        require_once BASE_PATH . '/controllers/RepresentativeController.php';
        $repCtrl = new RepresentativeController();
        $repCtrl->requestLink();
        break;

    case 'link_requests':
        require_once BASE_PATH . '/controllers/RepresentativeController.php';
        $repCtrl = new RepresentativeController();
        $repCtrl->linkRequests();
        break;

    case 'review_link_request':
        require_once BASE_PATH . '/controllers/RepresentativeController.php';
        $repCtrl = new RepresentativeController();
        $repCtrl->reviewLinkRequest();
        break;

    default:
        header('Location: ?action=login');
}