-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-02-2026 a las 11:48:27
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
(1, 3, 1, 3, 1, 1, 1, '2026-02-09', '1ra hora', 'presente', '', '2026-02-09 16:34:41', '2026-02-09 16:34:41'),
(2, 4, 1, 3, 1, 1, 1, '2026-02-09', '1ra hora', 'ausente', '', '2026-02-09 16:34:41', '2026-02-09 16:34:41'),
(3, 2, 1, 3, 1, 1, 1, '2026-02-09', '1ra hora', 'tardanza', '', '2026-02-09 16:34:41', '2026-02-09 16:34:41'),
(4, 5, 1, 3, 1, 1, 1, '2026-02-09', '1ra hora', 'presente', '', '2026-02-09 16:34:41', '2026-02-09 16:34:41'),
(5, 2, 1, 1, 6, 1, 1, '2026-02-07', '1ra hora', 'presente', '', '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(6, 3, 1, 1, 6, 1, 1, '2026-02-07', '1ra hora', 'presente', '', '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(7, 4, 1, 1, 6, 1, 1, '2026-02-07', '1ra hora', 'ausente', 'Sin justificación', '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(8, 5, 1, 1, 6, 1, 1, '2026-02-07', '1ra hora', 'presente', '', '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(9, 2, 1, 1, 6, 1, 1, '2026-02-08', '1ra hora', 'tardanza', 'Llegó 10 min tarde', '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(10, 3, 1, 1, 6, 1, 1, '2026-02-08', '1ra hora', 'presente', '', '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(11, 4, 1, 1, 6, 1, 1, '2026-02-08', '1ra hora', 'justificado', 'Cita médica', '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(12, 5, 1, 1, 6, 1, 1, '2026-02-08', '1ra hora', 'presente', '', '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(13, 2, 1, 1, 6, 1, 1, '2026-02-09', '1ra hora', 'presente', '', '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(14, 3, 1, 1, 6, 1, 1, '2026-02-09', '1ra hora', 'presente', '', '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(15, 4, 1, 1, 6, 1, 1, '2026-02-09', '1ra hora', 'presente', '', '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(16, 5, 1, 1, 6, 1, 1, '2026-02-09', '1ra hora', 'ausente', '', '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(17, 2, 1, 1, 6, 1, 1, '2026-02-10', '1ra hora', 'presente', '', '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(18, 3, 1, 1, 6, 1, 1, '2026-02-10', '1ra hora', 'presente', '', '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(19, 4, 1, 1, 6, 1, 1, '2026-02-10', '1ra hora', 'presente', '', '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(20, 5, 1, 1, 6, 1, 1, '2026-02-10', '1ra hora', 'tardanza', 'Tráfico', '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(21, 2, 1, 1, 6, 1, 1, '2026-02-11', '1ra hora', 'presente', '', '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(22, 3, 1, 1, 6, 1, 1, '2026-02-11', '1ra hora', 'presente', '', '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(23, 4, 1, 1, 6, 1, 1, '2026-02-11', '1ra hora', 'presente', '', '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(24, 5, 1, 1, 6, 1, 1, '2026-02-11', '1ra hora', 'presente', '', '2026-02-11 10:41:17', '2026-02-11 10:41:17');

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
(1, 1, 1, 'Octavo A', 'Octavo', 'A', 1, '2026-02-09 16:32:08', '2026-02-09 16:32:08'),
(2, 1, 1, 'Octavo B', 'Octavo', 'B', 1, '2026-02-09 16:40:28', '2026-02-09 16:40:28'),
(3, 1, 1, 'Séptimo A', 'Séptimo', 'A', 1, '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(4, 1, 1, 'Octavo B', 'Octavo', 'B', 1, '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(5, 1, 1, 'Noveno A', 'Noveno', 'A', 2, '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(6, 1, 1, 'Décimo A', 'Décimo', 'A', 2, '2026-02-11 10:41:17', '2026-02-11 10:41:17');

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
(1, 2, 1, 1, '2025-05-01', '2026-02-09 16:34:01'),
(2, 3, 1, 1, '2025-05-01', '2026-02-09 16:34:01'),
(3, 4, 1, 1, '2025-05-01', '2026-02-09 16:34:01'),
(4, 5, 1, 1, '2025-05-01', '2026-02-09 16:34:01'),
(5, 12, 2, 1, '2025-05-01', '2026-02-11 10:41:17'),
(6, 13, 2, 1, '2025-05-01', '2026-02-11 10:41:17'),
(7, 14, 2, 1, '2025-05-01', '2026-02-11 10:41:17'),
(8, 15, 3, 1, '2025-05-01', '2026-02-11 10:41:17'),
(9, 16, 3, 1, '2025-05-01', '2026-02-11 10:41:17'),
(10, 17, 3, 1, '2025-05-01', '2026-02-11 10:41:17'),
(11, 18, 4, 1, '2025-05-01', '2026-02-11 10:41:17'),
(12, 19, 4, 1, '2025-05-01', '2026-02-11 10:41:17'),
(13, 20, 5, 1, '2025-05-01', '2026-02-11 10:41:17'),
(14, 21, 5, 1, '2025-05-01', '2026-02-11 10:41:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `institutions`
--

CREATE TABLE `institutions` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) NOT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `institutions`
--

INSERT INTO `institutions` (`id`, `name`, `code`, `address`, `phone`, `email`, `logo`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Unidad Educativa Demo', 'UED001', 'Quito, Ecuador', NULL, NULL, NULL, 1, '2026-02-09 16:25:27', '2026-02-09 16:25:27');

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
(1, 22, 12, 'Madre', 1, '2026-02-11 10:41:17'),
(2, 22, 13, 'Tía', 0, '2026-02-11 10:41:17'),
(3, 23, 14, 'Padre', 1, '2026-02-11 10:41:17'),
(4, 24, 15, 'Madre', 1, '2026-02-11 10:41:17'),
(5, 24, 16, 'Madre', 1, '2026-02-11 10:41:17'),
(6, 25, 17, 'Padre', 1, '2026-02-11 10:41:17'),
(7, 25, 18, 'Tutor Legal', 0, '2026-02-11 10:41:17'),
(8, 26, 19, 'Madre', 1, '2026-02-11 10:41:17'),
(9, 26, 20, 'Madre', 1, '2026-02-11 10:41:17'),
(10, 26, 21, 'Madre', 1, '2026-02-11 10:41:17');

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
(1, 'docente', 'Profesor de la institución', '2026-02-09 16:25:27', '2026-02-09 16:25:27'),
(2, 'estudiante', 'Estudiante activo', '2026-02-09 16:25:27', '2026-02-09 16:25:27'),
(3, 'inspector', 'Inspector de disciplina', '2026-02-09 16:25:27', '2026-02-09 16:25:27'),
(4, 'autoridad', 'Autoridad institucional', '2026-02-09 16:25:27', '2026-02-09 16:25:27'),
(5, 'representante', 'Representante legal de estudiante', '2026-02-09 16:25:27', '2026-02-09 16:25:27');

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
(1, 1, '2025-2026', '2025-05-01', '2026-02-28', 1, '2026-02-09 16:25:28', '2026-02-09 16:25:28');

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
(1, 'mañana', '2026-02-09 16:25:28'),
(2, 'tarde', '2026-02-09 16:25:28'),
(3, 'noche', '2026-02-09 16:25:28');

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
(1, 1, 'Matemáticas', 'MAT', '2026-02-09 16:32:08', '2026-02-09 16:32:08'),
(2, 1, 'Lengua y Literatura', 'LEN', '2026-02-09 16:32:08', '2026-02-09 16:32:08'),
(3, 1, 'Ciencias Naturales', 'CCN', '2026-02-09 16:32:08', '2026-02-09 16:32:08'),
(4, 1, 'Estudios Sociales', 'ESS', '2026-02-09 16:32:08', '2026-02-09 16:32:08'),
(5, 1, 'Sistemas Operativos y Redes', 'SOR', '2026-02-09 16:40:49', '2026-02-09 16:40:49'),
(6, 1, 'Inglés', 'ING', '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(7, 1, 'Educación Física', 'EDF', '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(8, 1, 'Arte y Cultura', 'ART', '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(9, 1, 'Informática', 'INF', '2026-02-11 10:41:17', '2026-02-11 10:41:17');

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
(1, 1, 1, 5, 1, 1, '2026-02-11 10:40:06'),
(3, 6, 1, 1, 1, 1, '2026-02-11 10:41:17'),
(4, 6, 2, 1, 1, 0, '2026-02-11 10:41:17'),
(5, 6, 3, 1, 1, 0, '2026-02-11 10:41:17'),
(6, 7, 1, 2, 1, 0, '2026-02-11 10:41:17'),
(7, 7, 2, 2, 1, 1, '2026-02-11 10:41:17'),
(8, 7, 4, 2, 1, 0, '2026-02-11 10:41:17'),
(9, 8, 1, 3, 1, 0, '2026-02-11 10:41:17'),
(10, 8, 3, 3, 1, 1, '2026-02-11 10:41:17'),
(11, 8, 5, 3, 1, 0, '2026-02-11 10:41:17'),
(12, 9, 2, 4, 1, 0, '2026-02-11 10:41:17'),
(13, 9, 3, 4, 1, 0, '2026-02-11 10:41:17'),
(14, 9, 4, 4, 1, 1, '2026-02-11 10:41:17'),
(15, 10, 1, 5, 1, 0, '2026-02-11 10:41:17'),
(16, 10, 2, 5, 1, 0, '2026-02-11 10:41:17'),
(17, 10, 4, 5, 1, 0, '2026-02-11 10:41:17'),
(18, 10, 5, 5, 1, 1, '2026-02-11 10:41:17'),
(19, 11, 1, 6, 1, 0, '2026-02-11 10:41:17'),
(20, 11, 3, 6, 1, 0, '2026-02-11 10:41:17'),
(21, 11, 5, 6, 1, 0, '2026-02-11 10:41:17');

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
(1, 1, 'viktorengel', 'viktorengel@hotmail.com', '$2y$10$KFhz1gjqGsRNUNxlPyqv8O4rZEl5vKk72nEJPX5zbnS9t2rlROdVK', 'Victor', 'Rengel', '1709613788', '0998368685', NULL, NULL, 1, '2026-02-09 16:25:36', '2026-02-09 16:25:36'),
(2, 1, 'juan.perez', 'juan.perez@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Juan', 'Pérez García', '1750123456', '0991234567', NULL, NULL, 1, '2026-02-09 16:34:01', '2026-02-09 16:34:01'),
(3, 1, 'maria.lopez', 'maria.lopez@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'María', 'López Sánchez', '1750234567', '0991234568', NULL, NULL, 1, '2026-02-09 16:34:01', '2026-02-09 16:34:01'),
(4, 1, 'carlos.mora', 'carlos.mora@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carlos', 'Mora Ruiz', '1750345678', '0991234569', NULL, NULL, 1, '2026-02-09 16:34:01', '2026-02-09 16:34:01'),
(5, 1, 'ana.torres', 'ana.torres@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ana', 'Torres Vega', '1750456789', '0991234570', NULL, NULL, 1, '2026-02-09 16:34:01', '2026-02-09 16:34:01'),
(6, 1, 'prof.garcia', 'garcia@colegio.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Roberto', 'García Mendoza', '1751234567', '0991234571', NULL, NULL, 1, '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(7, 1, 'prof.martinez', 'martinez@colegio.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ana', 'Martínez Silva', '1751234568', '0991234572', NULL, NULL, 1, '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(8, 1, 'prof.rodriguez', 'rodriguez@colegio.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Luis', 'Rodríguez Cano', '1751234569', '0991234573', NULL, NULL, 1, '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(9, 1, 'prof.fernandez', 'fernandez@colegio.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carmen', 'Fernández Ortiz', '1751234570', '0991234574', NULL, NULL, 1, '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(10, 1, 'prof.gomez', 'gomez@colegio.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Miguel', 'Gómez Torres', '1751234571', '0991234575', NULL, NULL, 1, '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(11, 1, 'prof.diaz', 'diaz@colegio.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Patricia', 'Díaz Ramírez', '1751234572', '0991234576', NULL, NULL, 1, '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(12, 1, 'sofia.castro', 'sofia.castro@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sofía', 'Castro Morales', '1750567890', '0991234577', NULL, NULL, 1, '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(13, 1, 'diego.herrera', 'diego.herrera@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Diego', 'Herrera Ríos', '1750567891', '0991234578', NULL, NULL, 1, '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(14, 1, 'valentina.lopez', 'valentina.lopez@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Valentina', 'López Suárez', '1750567892', '0991234579', NULL, NULL, 1, '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(15, 1, 'mateo.silva', 'mateo.silva@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mateo', 'Silva Navarro', '1750567893', '0991234580', NULL, NULL, 1, '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(16, 1, 'isabella.rojas', 'isabella.rojas@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Isabella', 'Rojas Pérez', '1750567894', '0991234581', NULL, NULL, 1, '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(17, 1, 'sebastian.cruz', 'sebastian.cruz@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sebastián', 'Cruz Méndez', '1750567895', '0991234582', NULL, NULL, 1, '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(18, 1, 'camila.ramos', 'camila.ramos@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Camila', 'Ramos Flores', '1750567896', '0991234583', NULL, NULL, 1, '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(19, 1, 'nicolas.vargas', 'nicolas.vargas@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nicolás', 'Vargas Castro', '1750567897', '0991234584', NULL, NULL, 1, '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(20, 1, 'martina.gil', 'martina.gil@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Martina', 'Gil Núñez', '1750567898', '0991234585', NULL, NULL, 1, '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(21, 1, 'lucas.medina', 'lucas.medina@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lucas', 'Medina Ortega', '1750567899', '0991234586', NULL, NULL, 1, '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(22, 1, 'rep.castro', 'lcastro@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Laura', 'Castro Vega', '1752345678', '0991234587', NULL, NULL, 1, '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(23, 1, 'rep.herrera', 'jherrera@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jorge', 'Herrera Palacios', '1752345679', '0991234588', NULL, NULL, 1, '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(24, 1, 'rep.lopez', 'mlopez@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'María', 'López Benítez', '1752345680', '0991234589', NULL, NULL, 1, '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(25, 1, 'rep.silva', 'psilva@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pedro', 'Silva Moreno', '1752345681', '0991234590', NULL, NULL, 1, '2026-02-11 10:41:17', '2026-02-11 10:41:17'),
(26, 1, 'rep.rojas', 'crojas@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carolina', 'Rojas Delgado', '1752345682', '0991234591', NULL, NULL, 1, '2026-02-11 10:41:17', '2026-02-11 10:41:17');

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
(1, 1, 4, '2026-02-09 16:26:51'),
(2, 1, 1, '2026-02-09 16:27:27'),
(3, 2, 2, '2026-02-09 16:34:01'),
(4, 3, 2, '2026-02-09 16:34:01'),
(5, 4, 2, '2026-02-09 16:34:01'),
(6, 5, 2, '2026-02-09 16:34:01'),
(11, 6, 1, '2026-02-11 10:41:17'),
(12, 7, 1, '2026-02-11 10:41:17'),
(13, 8, 1, '2026-02-11 10:41:17'),
(14, 9, 1, '2026-02-11 10:41:17'),
(15, 10, 1, '2026-02-11 10:41:17'),
(16, 11, 1, '2026-02-11 10:41:17'),
(17, 12, 2, '2026-02-11 10:41:17'),
(18, 13, 2, '2026-02-11 10:41:17'),
(19, 14, 2, '2026-02-11 10:41:17'),
(20, 15, 2, '2026-02-11 10:41:17'),
(21, 16, 2, '2026-02-11 10:41:17'),
(22, 17, 2, '2026-02-11 10:41:17'),
(23, 18, 2, '2026-02-11 10:41:17'),
(24, 19, 2, '2026-02-11 10:41:17'),
(25, 20, 2, '2026-02-11 10:41:17'),
(26, 21, 2, '2026-02-11 10:41:17'),
(27, 22, 5, '2026-02-11 10:41:17'),
(28, 23, 5, '2026-02-11 10:41:17'),
(29, 24, 5, '2026-02-11 10:41:17'),
(30, 25, 5, '2026-02-11 10:41:17'),
(31, 26, 5, '2026-02-11 10:41:17');

--
-- Índices para tablas volcadas
--

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
-- AUTO_INCREMENT de la tabla `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `course_students`
--
ALTER TABLE `course_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `institutions`
--
ALTER TABLE `institutions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `teacher_assignments`
--
ALTER TABLE `teacher_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Restricciones para tablas volcadas
--

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
