-- ============================================
-- EcuAsist 2026 - Respaldo de Base de Datos
-- Base de datos: ecuasistencia2026_db
-- Fecha: 2026-03-05 18:33:05
-- ============================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- --------------------------------------------
-- Tabla: `activity_logs`
-- --------------------------------------------
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_date` (`created_at`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------
-- Tabla: `attendances`
-- --------------------------------------------
DROP TABLE IF EXISTS `attendances`;
CREATE TABLE `attendances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `hour_period` varchar(20) DEFAULT NULL,
  `status` enum('presente','ausente','tardanza','justificado') NOT NULL,
  `observation` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`),
  KEY `subject_id` (`subject_id`),
  KEY `teacher_id` (`teacher_id`),
  KEY `school_year_id` (`school_year_id`),
  KEY `shift_id` (`shift_id`),
  KEY `idx_date` (`date`),
  KEY `idx_student` (`student_id`),
  CONSTRAINT `attendances_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`),
  CONSTRAINT `attendances_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  CONSTRAINT `attendances_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  CONSTRAINT `attendances_ibfk_4` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`),
  CONSTRAINT `attendances_ibfk_5` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`id`),
  CONSTRAINT `attendances_ibfk_6` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------
-- Tabla: `class_schedule`
-- --------------------------------------------
DROP TABLE IF EXISTS `class_schedule`;
CREATE TABLE `class_schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `school_year_id` int(11) NOT NULL,
  `day_of_week` enum('lunes','martes','miercoles','jueves','viernes') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `period_number` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`),
  KEY `subject_id` (`subject_id`),
  KEY `teacher_id` (`teacher_id`),
  KEY `school_year_id` (`school_year_id`),
  CONSTRAINT `class_schedule_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  CONSTRAINT `class_schedule_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  CONSTRAINT `class_schedule_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`),
  CONSTRAINT `class_schedule_ibfk_4` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `class_schedule` (`id`, `course_id`, `subject_id`, `teacher_id`, `school_year_id`, `day_of_week`, `start_time`, `end_time`, `period_number`, `created_at`) VALUES
  ('22', '17', '1', '52', '1', 'jueves', '07:00:00', '07:45:00', '1', '2026-03-05 12:42:14'),
  ('23', '17', '2', '52', '1', 'jueves', '07:45:00', '08:30:00', '2', '2026-03-05 12:42:16'),
  ('24', '17', '3', '52', '1', 'jueves', '08:30:00', '09:15:00', '3', '2026-03-05 12:42:22');

-- --------------------------------------------
-- Tabla: `course_students`
-- --------------------------------------------
DROP TABLE IF EXISTS `course_students`;
CREATE TABLE `course_students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `enrollment_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_enrollment` (`student_id`,`course_id`,`school_year_id`),
  KEY `course_id` (`course_id`),
  KEY `school_year_id` (`school_year_id`),
  CONSTRAINT `course_students_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`),
  CONSTRAINT `course_students_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  CONSTRAINT `course_students_ibfk_3` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------
-- Tabla: `course_subjects`
-- --------------------------------------------
DROP TABLE IF EXISTS `course_subjects`;
CREATE TABLE `course_subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `hours_per_week` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_course_subject` (`course_id`,`subject_id`),
  KEY `cs_ibfk_2` (`subject_id`),
  CONSTRAINT `cs_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cs_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `course_subjects` (`id`, `course_id`, `subject_id`, `created_at`, `hours_per_week`) VALUES
  ('36', '17', '1', '2026-03-05 12:41:16', '1'),
  ('37', '17', '2', '2026-03-05 12:41:16', '1'),
  ('38', '17', '3', '2026-03-05 12:41:16', '1');

-- --------------------------------------------
-- Tabla: `courses`
-- --------------------------------------------
DROP TABLE IF EXISTS `courses`;
CREATE TABLE `courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institution_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `grade_level` varchar(50) DEFAULT NULL,
  `parallel` varchar(10) DEFAULT NULL,
  `shift_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `institution_id` (`institution_id`),
  KEY `school_year_id` (`school_year_id`),
  KEY `shift_id` (`shift_id`),
  CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`),
  CONSTRAINT `courses_ibfk_2` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`id`),
  CONSTRAINT `courses_ibfk_3` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `courses` (`id`, `institution_id`, `school_year_id`, `name`, `grade_level`, `parallel`, `shift_id`, `created_at`, `updated_at`) VALUES
  ('17', '1', '1', 'Inicial 1 (0-3 años) \"A\" - Matutina', 'Inicial 1 (0-3 años)', 'A', '1', '2026-03-05 12:41:16', '2026-03-05 12:41:16');

-- --------------------------------------------
-- Tabla: `institution_shifts`
-- --------------------------------------------
DROP TABLE IF EXISTS `institution_shifts`;
CREATE TABLE `institution_shifts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institution_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_institution_shift` (`institution_id`,`shift_id`),
  KEY `shift_id` (`shift_id`),
  CONSTRAINT `institution_shifts_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `institution_shifts_ibfk_2` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `institution_shifts` (`id`, `institution_id`, `shift_id`, `created_at`) VALUES
  ('1', '1', '1', '2026-02-15 06:41:49'),
  ('2', '1', '2', '2026-02-15 06:41:49');

-- --------------------------------------------
-- Tabla: `institutions`
-- --------------------------------------------
DROP TABLE IF EXISTS `institutions`;
CREATE TABLE `institutions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) NOT NULL,
  `address` text DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `director_name` varchar(200) DEFAULT NULL,
  `amie_code` varchar(20) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `working_days_list` varchar(100) DEFAULT '["lunes","martes","miercoles","jueves","viernes"]',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `institutions` (`id`, `name`, `code`, `address`, `province`, `city`, `phone`, `email`, `director_name`, `amie_code`, `website`, `logo_path`, `is_active`, `created_at`, `updated_at`, `working_days_list`) VALUES
  ('1', 'Unidad Educativa Pomasqui', 'UEP001', 'Av. Manuel Cordova Galarza 123, Quito, Ecuador', 'Pichincha', 'Quito', '02-2351072', 'contacto@uep.edu.ec', 'MSc. Nombre Apellido', '17h01988', 'https://www.uep.edu.ec', 'uploads/institution/logo_1_1771852565.jpg', '1', '2026-02-11 18:56:53', '2026-02-23 08:16:05', '[\"lunes\",\"martes\",\"miercoles\",\"jueves\",\"viernes\"]');

-- --------------------------------------------
-- Tabla: `justifications`
-- --------------------------------------------
DROP TABLE IF EXISTS `justifications`;
CREATE TABLE `justifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attendance_id` int(11) DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `working_days` int(11) DEFAULT NULL,
  `can_approve` enum('tutor','inspector','autoridad') NOT NULL DEFAULT 'inspector',
  `student_id` int(11) NOT NULL,
  `submitted_by` int(11) NOT NULL,
  `reason` text NOT NULL,
  `reason_type` varchar(100) DEFAULT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `status` enum('pendiente','aprobado','rechazado') DEFAULT 'pendiente',
  `reviewed_by` int(11) DEFAULT NULL,
  `review_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `attendance_id` (`attendance_id`),
  KEY `submitted_by` (`submitted_by`),
  KEY `reviewed_by` (`reviewed_by`),
  KEY `idx_status` (`status`),
  KEY `idx_student` (`student_id`),
  CONSTRAINT `justifications_ibfk_1` FOREIGN KEY (`attendance_id`) REFERENCES `attendances` (`id`) ON DELETE CASCADE,
  CONSTRAINT `justifications_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`),
  CONSTRAINT `justifications_ibfk_3` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`),
  CONSTRAINT `justifications_ibfk_4` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------
-- Tabla: `link_requests`
-- --------------------------------------------
DROP TABLE IF EXISTS `link_requests`;
CREATE TABLE `link_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `representative_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `relationship` varchar(50) NOT NULL,
  `message` text DEFAULT NULL,
  `status` enum('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'pendiente',
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `review_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_request` (`representative_id`,`student_id`),
  KEY `lr_stu_fk` (`student_id`),
  KEY `lr_rev_fk` (`reviewed_by`),
  CONSTRAINT `lr_rep_fk` FOREIGN KEY (`representative_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lr_rev_fk` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lr_stu_fk` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------
-- Tabla: `notifications`
-- --------------------------------------------
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','warning','success','danger') DEFAULT 'info',
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_read` (`user_id`,`is_read`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `link`, `is_read`, `created_at`) VALUES
  ('1', '54', '📅 Ausencia registrada', 'Se registró una ausencia el 24/02/2026 en Expresión y Comunicación. Puedes justificarla.', '', '?action=my_attendance', '0', '2026-02-25 14:49:53'),
  ('2', '55', '📅 Ausencia registrada', 'Se registró una ausencia el 24/02/2026 en Expresión y Comunicación. Puedes justificarla.', '', '?action=my_attendance', '0', '2026-02-25 14:49:53'),
  ('3', '54', '📅 Ausencia registrada', 'Se registró una ausencia el 23/02/2026 en Expresión y Comunicación. Puedes justificarla.', '', '?action=my_attendance', '0', '2026-02-25 14:50:10'),
  ('4', '54', '📅 Ausencia registrada', 'Se registró una ausencia el 23/02/2026 en Expresión y Comunicación. Puedes justificarla.', '', '?action=my_attendance', '0', '2026-02-25 14:50:35'),
  ('5', '55', '📅 Ausencia registrada', 'Se registró una ausencia el 24/02/2026 en Expresión y Comunicación. Puedes justificarla.', '', '?action=my_attendance', '0', '2026-02-25 14:52:15'),
  ('6', '52', '📝 Justificación pendiente (tutor)', 'Un estudiante de tu curso necesita justificación por 1 día(s).', 'info', '?action=tutor_pending_justifications', '1', '2026-02-25 14:58:17'),
  ('7', '54', '📅 Ausencia registrada', 'Se registró una ausencia el 26/02/2026 en Expresión y Comunicación. Puedes justificarla.', '', '?action=my_attendance', '0', '2026-02-26 09:47:13'),
  ('8', '52', '📝 Justificación pendiente (tutor)', 'Un estudiante de tu curso necesita justificación por 1 día(s).', 'info', '?action=tutor_pending_justifications', '1', '2026-02-26 09:47:51'),
  ('9', '55', '✅ Justificación aprobada', 'Tu justificación fue aprobada. Nota: Ok', 'success', '?action=my_justifications', '0', '2026-02-26 10:06:21'),
  ('10', '54', '✅ Justificación aprobada', 'Tu justificación fue aprobada. Nota: ok', 'success', '?action=my_justifications', '0', '2026-02-26 10:06:42'),
  ('11', '54', '📅 Ausencia registrada', 'Se registró una ausencia el 26/02/2026 en Desarrollo Personal y Social. Puedes justificarla.', '', '?action=my_attendance', '0', '2026-02-26 10:13:20'),
  ('12', '55', '📅 Ausencia registrada', 'Se registró una ausencia el 26/02/2026 en Desarrollo Personal y Social. Puedes justificarla.', '', '?action=my_attendance', '0', '2026-02-26 10:13:20'),
  ('13', '52', '📝 Justificación pendiente (tutor)', 'Un estudiante de tu curso necesita justificación por 1 día(s).', 'info', '?action=tutor_pending_justifications', '0', '2026-02-26 10:14:23'),
  ('14', '52', '📝 Justificación pendiente (tutor)', 'Un estudiante de tu curso necesita justificación por 1 día(s).', 'info', '?action=tutor_pending_justifications', '0', '2026-02-26 10:15:05'),
  ('15', '52', '📝 Justificación pendiente (tutor)', 'Un estudiante de tu curso necesita justificación por 1 día(s).', 'info', '?action=tutor_pending_justifications', '0', '2026-02-26 10:15:24'),
  ('16', '55', '✅ Justificación aprobada', 'Tu justificación fue aprobada.', 'success', '?action=my_justifications', '0', '2026-02-26 10:21:18'),
  ('17', '54', '✅ Justificación aprobada', 'Tu justificación fue aprobada.', 'success', '?action=my_justifications', '0', '2026-02-26 10:21:25'),
  ('18', '54', '✅ Justificación aprobada', 'Tu justificación fue aprobada.', 'success', '?action=my_justifications', '0', '2026-02-26 10:21:31'),
  ('19', '54', '📅 Ausencia registrada', 'Se registró una ausencia el 26/02/2026 en Desarrollo Personal y Social. Puedes justificarla.', '', '?action=my_attendance', '0', '2026-02-26 10:22:31'),
  ('20', '55', '📅 Ausencia registrada', 'Se registró una ausencia el 26/02/2026 en Desarrollo Personal y Social. Puedes justificarla.', '', '?action=my_attendance', '0', '2026-02-26 10:22:31'),
  ('21', '54', '📅 Ausencia registrada', 'Se registró una ausencia el 26/02/2026 en Relación con el Entorno Natural y Cultural. Puedes justificarla.', '', '?action=my_attendance', '0', '2026-02-26 10:24:51'),
  ('22', '55', '📅 Ausencia registrada', 'Se registró una ausencia el 26/02/2026 en Relación con el Entorno Natural y Cultural. Puedes justificarla.', '', '?action=my_attendance', '0', '2026-02-26 10:24:51'),
  ('23', '52', '📝 Justificación pendiente (tutor)', 'Un estudiante de tu curso necesita justificación por 1 día(s).', 'info', '?action=tutor_pending_justifications', '0', '2026-02-26 10:25:21'),
  ('24', '52', '📝 Justificación pendiente (tutor)', 'Un estudiante de tu curso necesita justificación por 1 día(s).', 'info', '?action=tutor_pending_justifications', '0', '2026-02-26 10:27:41');

-- --------------------------------------------
-- Tabla: `permissions`
-- --------------------------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `permission_type` varchar(50) NOT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------
-- Tabla: `representatives`
-- --------------------------------------------
DROP TABLE IF EXISTS `representatives`;
CREATE TABLE `representatives` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `representative_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `relationship` varchar(50) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_rep_student` (`representative_id`,`student_id`),
  KEY `student_id` (`student_id`),
  CONSTRAINT `representatives_ibfk_1` FOREIGN KEY (`representative_id`) REFERENCES `users` (`id`),
  CONSTRAINT `representatives_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `representatives` (`id`, `representative_id`, `student_id`, `relationship`, `is_primary`, `created_at`) VALUES
  ('6', '57', '54', 'Madre', '1', '2026-02-25 14:46:01'),
  ('7', '58', '55', 'Madre', '1', '2026-02-25 14:46:10');

-- --------------------------------------------
-- Tabla: `roles`
-- --------------------------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
  ('1', 'docente', 'Profesor de la institución', '2026-02-11 18:56:54', '2026-02-11 18:56:54'),
  ('2', 'estudiante', 'Estudiante activo', '2026-02-11 18:56:54', '2026-02-11 18:56:54'),
  ('3', 'inspector', 'Inspector de disciplina', '2026-02-11 18:56:54', '2026-02-11 18:56:54'),
  ('4', 'autoridad', 'Autoridad institucional', '2026-02-11 18:56:54', '2026-02-11 18:56:54'),
  ('5', 'representante', 'Representante legal de estudiante', '2026-02-11 18:56:54', '2026-02-11 18:56:54');

-- --------------------------------------------
-- Tabla: `school_years`
-- --------------------------------------------
DROP TABLE IF EXISTS `school_years`;
CREATE TABLE `school_years` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institution_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `institution_id` (`institution_id`),
  CONSTRAINT `school_years_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `school_years` (`id`, `institution_id`, `name`, `start_date`, `end_date`, `is_active`, `created_at`, `updated_at`) VALUES
  ('1', '1', '2025-2026', '2025-09-01', '2026-07-30', '1', '2026-02-20 08:42:21', '2026-02-23 11:49:15');

-- --------------------------------------------
-- Tabla: `shifts`
-- --------------------------------------------
DROP TABLE IF EXISTS `shifts`;
CREATE TABLE `shifts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `shifts` (`id`, `name`, `created_at`) VALUES
  ('1', 'matutina', '2026-02-11 18:56:54'),
  ('2', 'vespertina', '2026-02-11 18:56:54'),
  ('3', 'nocturna', '2026-02-11 18:56:54');

-- --------------------------------------------
-- Tabla: `subjects`
-- --------------------------------------------
DROP TABLE IF EXISTS `subjects`;
CREATE TABLE `subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institution_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `institution_id` (`institution_id`),
  CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `subjects` (`id`, `institution_id`, `name`, `code`, `created_at`, `updated_at`) VALUES
  ('1', '1', 'Desarrollo Personal y Social', '', '2026-02-20 09:42:59', '2026-02-20 09:42:59'),
  ('2', '1', 'Expresión y Comunicación', '', '2026-02-20 09:42:59', '2026-02-20 09:42:59'),
  ('3', '1', 'Relación con el Entorno Natural y Cultural', '', '2026-02-20 09:42:59', '2026-02-20 09:42:59'),
  ('4', '1', 'mate', 'MAT', '2026-02-23 12:17:02', '2026-02-23 12:17:02'),
  ('5', '1', 'Lengua', 'LEN', '2026-02-23 12:20:18', '2026-02-23 12:20:18');

-- --------------------------------------------
-- Tabla: `teacher_assignments`
-- --------------------------------------------
DROP TABLE IF EXISTS `teacher_assignments`;
CREATE TABLE `teacher_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `teacher_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `is_tutor` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `teacher_id` (`teacher_id`),
  KEY `course_id` (`course_id`),
  KEY `subject_id` (`subject_id`),
  KEY `school_year_id` (`school_year_id`),
  CONSTRAINT `teacher_assignments_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`),
  CONSTRAINT `teacher_assignments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  CONSTRAINT `teacher_assignments_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  CONSTRAINT `teacher_assignments_ibfk_4` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `teacher_assignments` (`id`, `teacher_id`, `course_id`, `subject_id`, `school_year_id`, `is_tutor`, `created_at`) VALUES
  ('15', '52', '17', '1', '1', '0', '2026-03-05 12:41:26'),
  ('16', '52', '17', '2', '1', '0', '2026-03-05 12:41:32'),
  ('17', '52', '17', '3', '1', '0', '2026-03-05 12:41:37');

-- --------------------------------------------
-- Tabla: `user_roles`
-- --------------------------------------------
DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_role` (`user_id`,`role_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `assigned_at`) VALUES
  ('1', '1', '4', '2026-02-19 12:49:20'),
  ('2', '1', '1', '2026-02-19 12:49:20'),
  ('3', '1', '2', '2026-02-19 12:49:20'),
  ('4', '1', '3', '2026-02-19 12:49:20'),
  ('5', '1', '5', '2026-02-19 12:49:20'),
  ('28', '52', '1', '2026-02-25 14:39:35'),
  ('29', '53', '1', '2026-02-25 14:40:17'),
  ('30', '54', '2', '2026-02-25 14:41:07'),
  ('31', '55', '2', '2026-02-25 14:42:11'),
  ('32', '56', '3', '2026-02-25 14:43:38'),
  ('33', '57', '5', '2026-02-25 14:44:30'),
  ('34', '58', '5', '2026-02-25 14:45:26'),
  ('35', '59', '1', '2026-02-25 14:47:15'),
  ('36', '60', '1', '2026-02-26 09:24:23'),
  ('37', '61', '1', '2026-02-26 09:42:50');

-- --------------------------------------------
-- Tabla: `users`
-- --------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institution_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(191) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `dni` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `is_superadmin` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `dni` (`dni`),
  KEY `institution_id` (`institution_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `institution_id`, `username`, `email`, `password`, `first_name`, `last_name`, `dni`, `phone`, `reset_token`, `reset_expires`, `is_active`, `is_superadmin`, `created_at`, `updated_at`) VALUES
  ('1', '1', 'admin', 'admin@uep.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'Sistema', '1700000000', '0999999999', NULL, NULL, '1', '1', '2026-02-11 18:56:54', '2026-02-19 12:49:20'),
  ('52', '1', 'victor', 'victor@uep.com', '$2y$10$wmQH8.2yEIw8arrl18raXOPb2bp0DzQeyVnvIRKf1F.PI5blsBJCS', 'Victor', 'Rengel', '1709613788', '0998368685', NULL, NULL, '1', '0', '2026-02-25 14:39:35', '2026-02-25 14:39:35'),
  ('53', '1', 'pilar', 'pilar@uep.com', '$2y$10$v3bWTXupUebcriFh1ZylRu8Bdoh4.Y4q7OwHNzxTTqycWhaon0F6S', 'Pilar', 'Serrano', '1234567890', '0998368685', NULL, NULL, '1', '0', '2026-02-25 14:40:11', '2026-02-25 14:40:11'),
  ('54', '1', 'Romina', 'romina@uep.com', '$2y$10$CpcfGgXYSFJk0aVbht9LpOlOW9vHVb9C.vPwq9jf7hDDIN9MmU5Pm', 'Romina', 'Racines', '1234567892', NULL, NULL, NULL, '1', '0', '2026-02-25 14:41:07', '2026-02-25 14:42:29'),
  ('55', '1', 'emilia', 'emilia@uep.com', '$2y$10$WIFBPEsB7DPoH05574LkZupqo6pMr9q9sqMOaKotFE2RTWGYrpY3e', 'Emilia', 'Rengel', '1234567891', '0981145721', NULL, NULL, '1', '0', '2026-02-25 14:42:11', '2026-02-25 14:42:11'),
  ('56', '1', 'marco', 'marco@uep.com', '$2y$10$XKRwq/5xOUIFthorbBspPeGpvBlvlpiYE3VAL0PYBoUr9M2v9Mqbq', 'Marco', 'Loachamin', '1234567893', '0981145722', NULL, NULL, '1', '0', '2026-02-25 14:43:38', '2026-02-25 14:43:38'),
  ('57', '1', 'rocio', 'rocio@uep.com', '$2y$10$e0660tk.HSD75YwkI8eB0.RzN3YRRcF2nq0yVu.8wCiGDsdiEx/6O', 'Rocio', 'Rengel', '1234567894', NULL, NULL, NULL, '1', '0', '2026-02-25 14:44:24', '2026-02-25 14:44:24'),
  ('58', '1', 'grace', 'grace@abc.com', '$2y$10$QvLnNRjINAZIryJtl46BmuVBRi7Exj4R0KEjCSKkZWbXoFMKfKD72', 'Grace', 'Tito', NULL, NULL, NULL, NULL, '1', '0', '2026-02-25 14:45:26', '2026-02-25 14:45:26'),
  ('59', '1', 'mario', 'mario@abc.com', '$2y$10$hyBbahMmiaofhP/qaTy9hePWBDHZoUEOurlsKDoLDOqSdjXpI6v9O', 'Mario', 'Muñoz', NULL, NULL, NULL, NULL, '1', '0', '2026-02-25 14:47:15', '2026-02-25 14:47:15'),
  ('60', '1', 'pamela', NULL, '$2y$10$AzE9jXPL19qck.axHZbCfuNrbiPUV6dN8WZVMgTJp5Fqvy.Sa7ne6', 'Pamela', 'Cortez', NULL, NULL, NULL, NULL, '1', '0', '2026-02-26 09:24:23', '2026-02-26 09:24:23'),
  ('61', '1', 'sandra', NULL, '$2y$10$KvxxLmMYRAbsPWEbogxicu.QqEh/gWVl2cPfi3IAIp9Oxptc4wG3K', 'Sandra', 'Flores', NULL, NULL, NULL, NULL, '1', '0', '2026-02-26 09:42:50', '2026-02-26 09:42:50');

SET FOREIGN_KEY_CHECKS=1;
-- Fin del respaldo
