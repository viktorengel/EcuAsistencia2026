-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-02-2026 a las 22:43:33
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ecuasistencia2026_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `attendances`
--

CREATE TABLE `attendances` (
  `id` int(11) NOT NULL,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `attendances`
--

INSERT INTO `attendances` (`id`, `student_id`, `course_id`, `subject_id`, `teacher_id`, `school_year_id`, `shift_id`, `date`, `hour_period`, `status`, `observation`, `created_at`, `updated_at`) VALUES
(1, 8, 1, 1, 2, 1, 1, '2026-02-07', '1ra hora', 'presente', '', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(2, 9, 1, 1, 2, 1, 1, '2026-02-07', '1ra hora', 'presente', '', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(3, 10, 1, 1, 2, 1, 1, '2026-02-07', '1ra hora', 'ausente', 'Sin justificación', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(4, 11, 1, 1, 2, 1, 1, '2026-02-07', '1ra hora', 'presente', '', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(5, 8, 1, 1, 2, 1, 1, '2026-02-08', '1ra hora', 'tardanza', 'Llegó 10 min tarde', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(6, 9, 1, 1, 2, 1, 1, '2026-02-08', '1ra hora', 'presente', '', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(7, 10, 1, 1, 2, 1, 1, '2026-02-08', '1ra hora', 'justificado', 'Cita médica', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(8, 11, 1, 1, 2, 1, 1, '2026-02-08', '1ra hora', 'presente', '', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(9, 8, 1, 1, 2, 1, 1, '2026-02-09', '1ra hora', 'presente', '', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(10, 9, 1, 1, 2, 1, 1, '2026-02-09', '1ra hora', 'presente', '', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(11, 10, 1, 1, 2, 1, 1, '2026-02-09', '1ra hora', 'presente', '', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(12, 11, 1, 1, 2, 1, 1, '2026-02-09', '1ra hora', 'ausente', 'Enfermo', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(13, 8, 1, 1, 2, 1, 1, '2026-02-10', '1ra hora', 'presente', '', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(14, 9, 1, 1, 2, 1, 1, '2026-02-10', '1ra hora', 'presente', '', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(15, 10, 1, 1, 2, 1, 1, '2026-02-10', '1ra hora', 'presente', '', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(16, 11, 1, 1, 2, 1, 1, '2026-02-10', '1ra hora', 'tardanza', 'Tráfico', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(17, 8, 1, 1, 2, 1, 1, '2026-02-11', '1ra hora', 'presente', '', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(18, 9, 1, 1, 2, 1, 1, '2026-02-11', '1ra hora', 'presente', '', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(19, 10, 1, 1, 2, 1, 1, '2026-02-11', '1ra hora', 'presente', '', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(20, 11, 1, 1, 2, 1, 1, '2026-02-11', '1ra hora', 'presente', '', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(21, 8, 1, 2, 3, 1, 1, '2026-02-09', '2da hora', 'presente', '', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(22, 9, 1, 2, 3, 1, 1, '2026-02-09', '2da hora', 'presente', 'Participación activa', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(23, 10, 1, 2, 3, 1, 1, '2026-02-09', '2da hora', 'ausente', '', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(24, 11, 1, 2, 3, 1, 1, '2026-02-09', '2da hora', 'presente', '', '2026-02-11 23:56:54', '2026-02-11 23:56:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `class_schedule`
--

CREATE TABLE `class_schedule` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `day_of_week` enum('lunes','martes','miercoles','jueves','viernes','sabado') NOT NULL,
  `period_number` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `institution_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `grade_level` varchar(50) DEFAULT NULL,
  `parallel` varchar(10) DEFAULT NULL,
  `shift_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `courses`
--

INSERT INTO `courses` (`id`, `institution_id`, `school_year_id`, `name`, `grade_level`, `parallel`, `shift_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Octavo A', 'Octavo', 'A', 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(2, 1, 1, 'Séptimo A', 'Séptimo', 'A', 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(3, 1, 1, 'Octavo B', 'Octavo', 'B', 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(4, 1, 1, 'Noveno A', 'Noveno', 'A', 2, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(5, 1, 1, 'Décimo A', 'Décimo', 'A', 2, '2026-02-11 23:56:54', '2026-02-11 23:56:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `course_students`
--

CREATE TABLE `course_students` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `enrollment_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `course_students`
--

INSERT INTO `course_students` (`id`, `student_id`, `course_id`, `school_year_id`, `enrollment_date`, `created_at`) VALUES
(1, 8, 1, 1, '2025-09-01', '2026-02-11 23:56:54'),
(2, 9, 1, 1, '2025-09-01', '2026-02-11 23:56:54'),
(3, 10, 1, 1, '2025-09-01', '2026-02-11 23:56:54'),
(4, 11, 1, 1, '2025-09-01', '2026-02-11 23:56:54'),
(5, 12, 2, 1, '2025-09-01', '2026-02-11 23:56:54'),
(6, 13, 2, 1, '2025-09-01', '2026-02-11 23:56:54'),
(7, 14, 2, 1, '2025-09-01', '2026-02-11 23:56:54'),
(8, 15, 3, 1, '2025-09-01', '2026-02-11 23:56:54'),
(9, 16, 3, 1, '2025-09-01', '2026-02-11 23:56:54'),
(10, 17, 3, 1, '2025-09-01', '2026-02-11 23:56:54'),
(11, 18, 4, 1, '2025-09-01', '2026-02-11 23:56:54'),
(12, 19, 4, 1, '2025-09-01', '2026-02-11 23:56:54'),
(13, 20, 4, 1, '2025-09-01', '2026-02-11 23:56:54'),
(14, 21, 5, 1, '2025-09-01', '2026-02-11 23:56:54'),
(15, 22, 5, 1, '2025-09-01', '2026-02-11 23:56:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `institutions`
--

CREATE TABLE `institutions` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) NOT NULL,
  `address` text DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `director_name` varchar(200) DEFAULT NULL,
  `amie_code` varchar(20) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `institutions`
--

INSERT INTO `institutions` (`id`, `name`, `code`, `address`, `province`, `city`, `director_name`, `amie_code`, `website`, `phone`, `email`, `logo_path`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Unidad Educativa Pomasqui', 'UEP001', 'Av. Manuel Cordova Galarza 123, Quito, Ecuador', 'Pichincha', 'Quito', 'MSc. Rector', '17h01988', 'https://www.iepomasqui.com', '02-2351072', 'contacto@uep.edu.ec', 'uploads/institution/logo_1771364489.jpg', 1, '2026-02-11 23:56:53', '2026-02-17 21:41:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `institution_shifts`
--

CREATE TABLE `institution_shifts` (
  `id` int(11) NOT NULL,
  `institution_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `institution_shifts`
--

INSERT INTO `institution_shifts` (`id`, `institution_id`, `shift_id`, `created_at`) VALUES
(1, 1, 1, '2026-02-17 21:41:40'),
(2, 1, 2, '2026-02-17 21:41:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `justifications`
--

CREATE TABLE `justifications` (
  `id` int(11) NOT NULL,
  `attendance_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `submitted_by` int(11) NOT NULL,
  `reason` text NOT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `status` enum('pendiente','aprobado','rechazado') DEFAULT 'pendiente',
  `reviewed_by` int(11) DEFAULT NULL,
  `review_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','warning','success','danger') DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `permission_type` varchar(50) NOT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `representatives`
--

CREATE TABLE `representatives` (
  `id` int(11) NOT NULL,
  `representative_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `relationship` varchar(50) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `representatives`
--

INSERT INTO `representatives` (`id`, `representative_id`, `student_id`, `relationship`, `is_primary`, `created_at`) VALUES
(1, 23, 12, 'Madre', 1, '2026-02-11 23:56:54'),
(2, 23, 13, 'Madre', 1, '2026-02-11 23:56:54'),
(3, 24, 14, 'Padre', 1, '2026-02-11 23:56:54'),
(4, 25, 15, 'Madre', 1, '2026-02-11 23:56:54'),
(5, 25, 16, 'Madre', 1, '2026-02-11 23:56:54'),
(6, 26, 17, 'Padre', 1, '2026-02-11 23:56:54'),
(7, 26, 18, 'Padre', 1, '2026-02-11 23:56:54'),
(8, 27, 19, 'Madre', 1, '2026-02-11 23:56:54'),
(9, 27, 20, 'Madre', 1, '2026-02-11 23:56:54'),
(10, 27, 21, 'Madre', 1, '2026-02-11 23:56:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'docente', 'Profesor de la institución', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(2, 'estudiante', 'Estudiante activo', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(3, 'inspector', 'Inspector de disciplina', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(4, 'autoridad', 'Autoridad institucional', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(5, 'representante', 'Representante legal de estudiante', '2026-02-11 23:56:54', '2026-02-11 23:56:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `school_years`
--

CREATE TABLE `school_years` (
  `id` int(11) NOT NULL,
  `institution_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `school_years`
--

INSERT INTO `school_years` (`id`, `institution_id`, `name`, `start_date`, `end_date`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-2026', '2025-09-01', '2026-07-30', 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `shifts`
--

CREATE TABLE `shifts` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `shifts`
--

INSERT INTO `shifts` (`id`, `name`, `created_at`) VALUES
(1, 'matutina', '2026-02-11 23:56:54'),
(2, 'vespertina', '2026-02-11 23:56:54'),
(3, 'nocturna', '2026-02-11 23:56:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `institution_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `subjects`
--

INSERT INTO `subjects` (`id`, `institution_id`, `name`, `code`, `created_at`, `updated_at`) VALUES
(1, 1, 'Matemáticas', 'MAT', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(2, 1, 'Lengua y Literatura', 'LEN', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(3, 1, 'Ciencias Naturales', 'CCN', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(4, 1, 'Estudios Sociales', 'ESS', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(5, 1, 'Inglés', 'ING', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(6, 1, 'Educación Física', 'EDF', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(7, 1, 'Arte y Cultura', 'ART', '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(8, 1, 'Informática', 'INF', '2026-02-11 23:56:54', '2026-02-11 23:56:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `teacher_assignments`
--

CREATE TABLE `teacher_assignments` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `is_tutor` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `teacher_assignments`
--

INSERT INTO `teacher_assignments` (`id`, `teacher_id`, `course_id`, `subject_id`, `school_year_id`, `is_tutor`, `created_at`) VALUES
(1, 2, 1, 1, 1, 1, '2026-02-11 23:56:54'),
(2, 2, 2, 1, 1, 0, '2026-02-11 23:56:54'),
(3, 2, 3, 1, 1, 0, '2026-02-11 23:56:54'),
(4, 3, 1, 2, 1, 0, '2026-02-11 23:56:54'),
(5, 3, 2, 2, 1, 1, '2026-02-11 23:56:54'),
(6, 3, 4, 2, 1, 0, '2026-02-11 23:56:54'),
(7, 4, 1, 3, 1, 0, '2026-02-11 23:56:54'),
(8, 4, 3, 3, 1, 1, '2026-02-11 23:56:54'),
(9, 4, 5, 3, 1, 0, '2026-02-11 23:56:54'),
(10, 5, 2, 4, 1, 0, '2026-02-11 23:56:54'),
(11, 5, 3, 4, 1, 0, '2026-02-11 23:56:54'),
(12, 5, 4, 4, 1, 1, '2026-02-11 23:56:54'),
(13, 6, 1, 5, 1, 0, '2026-02-11 23:56:54'),
(14, 6, 2, 5, 1, 0, '2026-02-11 23:56:54'),
(15, 6, 4, 5, 1, 0, '2026-02-11 23:56:54'),
(16, 6, 5, 5, 1, 1, '2026-02-11 23:56:54'),
(17, 7, 1, 6, 1, 0, '2026-02-11 23:56:54'),
(18, 7, 3, 6, 1, 0, '2026-02-11 23:56:54'),
(19, 7, 5, 6, 1, 0, '2026-02-11 23:56:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `institution_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `dni` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `institution_id`, `username`, `email`, `password`, `first_name`, `last_name`, `dni`, `phone`, `reset_token`, `reset_expires`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'admin', 'admin@uep.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'Sistema', '1700000000', '0999999999', NULL, NULL, 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(2, 1, 'prof.garcia', 'garcia@uep.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Roberto', 'García Mendoza', '1751234567', '0991234571', NULL, NULL, 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(3, 1, 'prof.martinez', 'martinez@uep.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ana', 'Martínez Silva', '1751234568', '0991234572', NULL, NULL, 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(4, 1, 'prof.rodriguez', 'rodriguez@uep.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Luis', 'Rodríguez Cano', '1751234569', '0991234573', NULL, NULL, 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(5, 1, 'prof.fernandez', 'fernandez@uep.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carmen', 'Fernández Ortiz', '1751234570', '0991234574', NULL, NULL, 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(6, 1, 'prof.gomez', 'gomez@uep.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Miguel', 'Gómez Torres', '1751234571', '0991234575', NULL, NULL, 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(7, 1, 'prof.diaz', 'diaz@uep.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Patricia', 'Díaz Ramírez', '1751234572', '0991234576', NULL, NULL, 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(8, 1, 'juan.perez', 'juan.perez@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Juan', 'Pérez García', '1750123456', '0991234567', NULL, NULL, 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(9, 1, 'maria.lopez', 'maria.lopez@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'María', 'López Sánchez', '1750234567', '0991234568', NULL, NULL, 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(10, 1, 'carlos.mora', 'carlos.mora@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carlos', 'Mora Ruiz', '1750345678', '0991234569', NULL, NULL, 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(11, 1, 'ana.torres', 'ana.torres@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ana', 'Torres Vega', '1750456789', '0991234570', NULL, NULL, 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(12, 1, 'sofia.castro', 'sofia.castro@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sofía', 'Castro Morales', '1750567890', '0991234577', NULL, NULL, 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(13, 1, 'diego.herrera', 'diego.herrera@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Diego', 'Herrera Ríos', '1750567891', '0991234578', NULL, NULL, 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(14, 1, 'valentina.lopez', 'valentina.lopez@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Valentina', 'López Suárez', '1750567892', '0991234579', NULL, NULL, 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(15, 1, 'mateo.silva', 'mateo.silva@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mateo', 'Silva Navarro', '1750567893', '0991234580', NULL, NULL, 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(16, 1, 'isabella.rojas', 'isabella.rojas@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Isabella', 'Rojas Pérez', '1750567894', '0991234581', NULL, NULL, 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(17, 1, 'sebastian.cruz', 'sebastian.cruz@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sebastián', 'Cruz Méndez', '1750567895', '0991234582', NULL, NULL, 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(18, 1, 'camila.ramos', 'camila.ramos@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Camila', 'Ramos Flores', '1750567896', '0991234583', NULL, NULL, 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(19, 1, 'nicolas.vargas', 'nicolas.vargas@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nicolás', 'Vargas Castro', '1750567897', '0991234584', NULL, NULL, 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(20, 1, 'martina.gil', 'martina.gil@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Martina', 'Gil Núñez', '1750567898', '0991234585', NULL, NULL, 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(21, 1, 'lucas.medina', 'lucas.medina@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lucas', 'Medina Ortega', '1750567899', '0991234586', NULL, NULL, 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(22, 1, 'emma.santos', 'emma.santos@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Emma', 'Santos Molina', '1750567900', '0991234587', NULL, NULL, 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(23, 1, 'rep.castro', 'lcastro@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Laura', 'Castro Vega', '1752345678', '0991234588', NULL, NULL, 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(24, 1, 'rep.herrera', 'jherrera@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jorge', 'Herrera Palacios', '1752345679', '0991234589', NULL, NULL, 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(25, 1, 'rep.lopez', 'mlopez@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'María', 'López Benítez', '1752345680', '0991234590', NULL, NULL, 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(26, 1, 'rep.silva', 'psilva@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pedro', 'Silva Moreno', '1752345681', '0991234591', NULL, NULL, 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(27, 1, 'rep.rojas', 'crojas@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carolina', 'Rojas Delgado', '1752345682', '0991234592', NULL, NULL, 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54'),
(28, 1, 'inspector', 'inspector@uep.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'José', 'Ramírez Campos', '1753456789', '0991234593', NULL, NULL, 1, '2026-02-11 23:56:54', '2026-02-11 23:56:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_roles`
--

CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `assigned_at`) VALUES
(1, 1, 4, '2026-02-11 23:56:54'),
(2, 2, 1, '2026-02-11 23:56:54'),
(3, 3, 1, '2026-02-11 23:56:54'),
(4, 4, 1, '2026-02-11 23:56:54'),
(5, 5, 1, '2026-02-11 23:56:54'),
(6, 6, 1, '2026-02-11 23:56:54'),
(7, 7, 1, '2026-02-11 23:56:54'),
(8, 8, 2, '2026-02-11 23:56:54'),
(9, 9, 2, '2026-02-11 23:56:54'),
(10, 10, 2, '2026-02-11 23:56:54'),
(11, 11, 2, '2026-02-11 23:56:54'),
(12, 12, 2, '2026-02-11 23:56:54'),
(13, 13, 2, '2026-02-11 23:56:54'),
(14, 14, 2, '2026-02-11 23:56:54'),
(15, 15, 2, '2026-02-11 23:56:54'),
(16, 16, 2, '2026-02-11 23:56:54'),
(17, 17, 2, '2026-02-11 23:56:54'),
(18, 18, 2, '2026-02-11 23:56:54'),
(19, 19, 2, '2026-02-11 23:56:54'),
(20, 20, 2, '2026-02-11 23:56:54'),
(21, 21, 2, '2026-02-11 23:56:54'),
(22, 22, 2, '2026-02-11 23:56:54'),
(23, 23, 5, '2026-02-11 23:56:54'),
(24, 24, 5, '2026-02-11 23:56:54'),
(25, 25, 5, '2026-02-11 23:56:54'),
(26, 26, 5, '2026-02-11 23:56:54'),
(27, 27, 5, '2026-02-11 23:56:54'),
(28, 28, 3, '2026-02-11 23:56:54'),
(30, 1, 1, '2026-02-12 05:00:28');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_date` (`created_at`);

--
-- Indices de la tabla `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `school_year_id` (`school_year_id`),
  ADD KEY `shift_id` (`shift_id`),
  ADD KEY `idx_date` (`date`),
  ADD KEY `idx_student` (`student_id`);

--
-- Indices de la tabla `class_schedule`
--
ALTER TABLE `class_schedule`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_schedule` (`course_id`,`day_of_week`,`period_number`,`school_year_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `school_year_id` (`school_year_id`);

--
-- Indices de la tabla `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `institution_id` (`institution_id`),
  ADD KEY `school_year_id` (`school_year_id`),
  ADD KEY `shift_id` (`shift_id`);

--
-- Indices de la tabla `course_students`
--
ALTER TABLE `course_students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_enrollment` (`student_id`,`course_id`,`school_year_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `school_year_id` (`school_year_id`);

--
-- Indices de la tabla `institutions`
--
ALTER TABLE `institutions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indices de la tabla `institution_shifts`
--
ALTER TABLE `institution_shifts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_institution_shift` (`institution_id`,`shift_id`),
  ADD KEY `shift_id` (`shift_id`);

--
-- Indices de la tabla `justifications`
--
ALTER TABLE `justifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendance_id` (`attendance_id`),
  ADD KEY `submitted_by` (`submitted_by`),
  ADD KEY `reviewed_by` (`reviewed_by`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_student` (`student_id`);

--
-- Indices de la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_read` (`user_id`,`is_read`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `representatives`
--
ALTER TABLE `representatives`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_rep_student` (`representative_id`,`student_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indices de la tabla `school_years`
--
ALTER TABLE `school_years`
  ADD PRIMARY KEY (`id`),
  ADD KEY `institution_id` (`institution_id`);

--
-- Indices de la tabla `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `institution_id` (`institution_id`);

--
-- Indices de la tabla `teacher_assignments`
--
ALTER TABLE `teacher_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `school_year_id` (`school_year_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD KEY `institution_id` (`institution_id`);

--
-- Indices de la tabla `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_role` (`user_id`,`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `class_schedule`
--
ALTER TABLE `class_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `course_students`
--
ALTER TABLE `course_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `institutions`
--
ALTER TABLE `institutions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `institution_shifts`
--
ALTER TABLE `institution_shifts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `justifications`
--
ALTER TABLE `justifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `representatives`
--
ALTER TABLE `representatives`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `school_years`
--
ALTER TABLE `school_years`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `shifts`
--
ALTER TABLE `shifts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `teacher_assignments`
--
ALTER TABLE `teacher_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `attendances_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `attendances_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `attendances_ibfk_4` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `attendances_ibfk_5` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`id`),
  ADD CONSTRAINT `attendances_ibfk_6` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`);

--
-- Filtros para la tabla `class_schedule`
--
ALTER TABLE `class_schedule`
  ADD CONSTRAINT `class_schedule_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_schedule_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_schedule_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_schedule_ibfk_4` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`),
  ADD CONSTRAINT `courses_ibfk_2` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`id`),
  ADD CONSTRAINT `courses_ibfk_3` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`);

--
-- Filtros para la tabla `course_students`
--
ALTER TABLE `course_students`
  ADD CONSTRAINT `course_students_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `course_students_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `course_students_ibfk_3` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`id`);

--
-- Filtros para la tabla `institution_shifts`
--
ALTER TABLE `institution_shifts`
  ADD CONSTRAINT `institution_shifts_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `institution_shifts_ibfk_2` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `justifications`
--
ALTER TABLE `justifications`
  ADD CONSTRAINT `justifications_ibfk_1` FOREIGN KEY (`attendance_id`) REFERENCES `attendances` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `justifications_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `justifications_ibfk_3` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `justifications_ibfk_4` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `representatives`
--
ALTER TABLE `representatives`
  ADD CONSTRAINT `representatives_ibfk_1` FOREIGN KEY (`representative_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `representatives_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `school_years`
--
ALTER TABLE `school_years`
  ADD CONSTRAINT `school_years_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`);

--
-- Filtros para la tabla `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`);

--
-- Filtros para la tabla `teacher_assignments`
--
ALTER TABLE `teacher_assignments`
  ADD CONSTRAINT `teacher_assignments_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `teacher_assignments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `teacher_assignments_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `teacher_assignments_ibfk_4` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`id`);

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`);

--
-- Filtros para la tabla `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
