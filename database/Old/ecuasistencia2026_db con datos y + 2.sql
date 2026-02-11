-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-02-2026 a las 17:36:45
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
-- Base de datos: `ecuasist`
--
CREATE DATABASE IF NOT EXISTS `ecuasist` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ecuasist`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignaciones_docente`
--

CREATE TABLE `asignaciones_docente` (
  `id` int(11) NOT NULL,
  `docente_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `asignatura_id` int(11) NOT NULL,
  `periodo_lectivo_id` int(11) NOT NULL,
  `jornada` enum('matutina','vespertina','nocturna') DEFAULT 'matutina',
  `es_tutor` tinyint(1) DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignaturas`
--

CREATE TABLE `asignaturas` (
  `id` int(11) NOT NULL,
  `institucion_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `codigo` varchar(20) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencias`
--

CREATE TABLE `asistencias` (
  `id` int(11) NOT NULL,
  `institucion_id` int(11) NOT NULL,
  `matricula_id` int(11) NOT NULL,
  `asignatura_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora` tinyint(1) NOT NULL,
  `estado` enum('presente','atraso','justificado','falta') DEFAULT 'presente',
  `observacion` text DEFAULT NULL,
  `registrado_por` int(11) NOT NULL,
  `anulado` tinyint(1) DEFAULT 0,
  `motivo_anulacion` text DEFAULT NULL,
  `anulado_por` int(11) DEFAULT NULL,
  `anulado_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

CREATE TABLE `auditoria` (
  `id` int(11) NOT NULL,
  `entidad` varchar(50) NOT NULL,
  `accion` varchar(50) NOT NULL,
  `entidad_id` int(11) NOT NULL,
  `datos_anteriores` text DEFAULT NULL,
  `datos_nuevos` text DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bloqueos_asistencia`
--

CREATE TABLE `bloqueos_asistencia` (
  `id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora` tinyint(1) NOT NULL,
  `motivo` text DEFAULT NULL,
  `bloqueado_por` int(11) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `desbloqueado_por` int(11) DEFAULT NULL,
  `motivo_desbloqueo` text DEFAULT NULL,
  `desbloqueado_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calculos_asistencia`
--

CREATE TABLE `calculos_asistencia` (
  `id` int(11) NOT NULL,
  `matricula_id` int(11) NOT NULL,
  `periodo_mes` varchar(7) NOT NULL COMMENT 'YYYY-MM',
  `total_clases` int(11) DEFAULT 0,
  `presentes` int(11) DEFAULT 0,
  `atrasos` int(11) DEFAULT 0,
  `justificados` int(11) DEFAULT 0,
  `faltas` int(11) DEFAULT 0,
  `porcentaje_asistencia` decimal(5,2) DEFAULT 0.00,
  `requiere_calculo` tinyint(1) DEFAULT 1,
  `calculado_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL,
  `institucion_id` int(11) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `tipo` enum('string','number','boolean','json') DEFAULT 'string',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE `cursos` (
  `id` int(11) NOT NULL,
  `institucion_id` int(11) NOT NULL,
  `nivel_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `paralelo` varchar(10) NOT NULL,
  `jornada` enum('matutina','vespertina','nocturna') DEFAULT 'matutina',
  `capacidad` int(11) DEFAULT 40,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios`
--

CREATE TABLE `horarios` (
  `id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `asignatura_id` int(11) NOT NULL,
  `dia_semana` tinyint(1) NOT NULL COMMENT '1=Lunes, 5=Viernes',
  `hora` tinyint(1) NOT NULL COMMENT '1-8',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instituciones`
--

CREATE TABLE `instituciones` (
  `id` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `codigo` varchar(50) DEFAULT NULL,
  `tipo` enum('fiscal','fiscomisional','particular','municipal') DEFAULT 'fiscal',
  `nivel` enum('inicial','basica','bachillerato','completo') DEFAULT 'completo',
  `direccion` text DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `rector` varchar(100) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `instituciones`
--

INSERT INTO `instituciones` (`id`, `nombre`, `codigo`, `tipo`, `nivel`, `direccion`, `telefono`, `email`, `rector`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Institución Educativa Demo', 'DEMO-001', 'fiscal', 'completo', 'Quito, Ecuador', '022222222', 'demo@ecuasist.com', 'Director Demo', 1, '2026-02-05 12:31:52', '2026-02-05 12:31:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `justificaciones`
--

CREATE TABLE `justificaciones` (
  `id` int(11) NOT NULL,
  `matricula_id` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `motivo` text NOT NULL,
  `archivo_adjunto` varchar(255) DEFAULT NULL,
  `estado` enum('pendiente','aprobado','rechazado') DEFAULT 'pendiente',
  `revisado_por` int(11) DEFAULT NULL,
  `observacion_revision` text DEFAULT NULL,
  `revisado_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matriculas`
--

CREATE TABLE `matriculas` (
  `id` int(11) NOT NULL,
  `institucion_id` int(11) NOT NULL,
  `estudiante_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `periodo_lectivo_id` int(11) NOT NULL,
  `estado` enum('activo','inactivo','retirado','graduado') DEFAULT 'activo',
  `fecha_matricula` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `niveles`
--

CREATE TABLE `niveles` (
  `id` int(11) NOT NULL,
  `institucion_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `orden` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `periodos_lectivos`
--

CREATE TABLE `periodos_lectivos` (
  `id` int(11) NOT NULL,
  `institucion_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `activo` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `representantes_estudiantes`
--

CREATE TABLE `representantes_estudiantes` (
  `id` int(11) NOT NULL,
  `representante_id` int(11) NOT NULL,
  `estudiante_id` int(11) NOT NULL,
  `parentesco` varchar(50) DEFAULT NULL,
  `principal` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sincronizacion_offline`
--

CREATE TABLE `sincronizacion_offline` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo_operacion` enum('asistencia','justificacion') NOT NULL,
  `datos` text NOT NULL,
  `sincronizado` tinyint(1) DEFAULT 0,
  `error` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sincronizado_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `institucion_id` int(11) NOT NULL,
  `identificacion` varchar(20) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('administrador','docente','inspector','docente_tutor','estudiante','representante') NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `ultimo_acceso` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `institucion_id`, `identificacion`, `nombres`, `apellidos`, `email`, `password`, `rol`, `activo`, `ultimo_acceso`, `created_at`, `updated_at`) VALUES
(1, 1, '9999999999', 'Administrador', 'Sistema', 'admin@ecuasist.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador', 1, NULL, '2026-02-05 12:31:52', '2026-02-05 12:31:52');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asignaciones_docente`
--
ALTER TABLE `asignaciones_docente`
  ADD PRIMARY KEY (`id`),
  ADD KEY `docente_id` (`docente_id`),
  ADD KEY `curso_id` (`curso_id`),
  ADD KEY `asignatura_id` (`asignatura_id`),
  ADD KEY `periodo_lectivo_id` (`periodo_lectivo_id`);

--
-- Indices de la tabla `asignaturas`
--
ALTER TABLE `asignaturas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `institucion_id` (`institucion_id`);

--
-- Indices de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `institucion_id` (`institucion_id`),
  ADD KEY `matricula_id` (`matricula_id`),
  ADD KEY `asignatura_id` (`asignatura_id`),
  ADD KEY `registrado_por` (`registrado_por`),
  ADD KEY `fecha` (`fecha`),
  ADD KEY `estado` (`estado`);

--
-- Indices de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `entidad` (`entidad`),
  ADD KEY `accion` (`accion`);

--
-- Indices de la tabla `bloqueos_asistencia`
--
ALTER TABLE `bloqueos_asistencia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `curso_id` (`curso_id`),
  ADD KEY `bloqueado_por` (`bloqueado_por`);

--
-- Indices de la tabla `calculos_asistencia`
--
ALTER TABLE `calculos_asistencia`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `matricula_periodo` (`matricula_id`,`periodo_mes`),
  ADD KEY `matricula_id` (`matricula_id`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `institucion_clave` (`institucion_id`,`clave`),
  ADD KEY `institucion_id` (`institucion_id`);

--
-- Indices de la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `institucion_id` (`institucion_id`),
  ADD KEY `nivel_id` (`nivel_id`);

--
-- Indices de la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `curso_id` (`curso_id`),
  ADD KEY `asignatura_id` (`asignatura_id`);

--
-- Indices de la tabla `instituciones`
--
ALTER TABLE `instituciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `justificaciones`
--
ALTER TABLE `justificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `matricula_id` (`matricula_id`),
  ADD KEY `revisado_por` (`revisado_por`);

--
-- Indices de la tabla `matriculas`
--
ALTER TABLE `matriculas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `institucion_id` (`institucion_id`),
  ADD KEY `estudiante_id` (`estudiante_id`),
  ADD KEY `curso_id` (`curso_id`),
  ADD KEY `periodo_lectivo_id` (`periodo_lectivo_id`);

--
-- Indices de la tabla `niveles`
--
ALTER TABLE `niveles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `institucion_id` (`institucion_id`);

--
-- Indices de la tabla `periodos_lectivos`
--
ALTER TABLE `periodos_lectivos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `institucion_id` (`institucion_id`);

--
-- Indices de la tabla `representantes_estudiantes`
--
ALTER TABLE `representantes_estudiantes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `representante_id` (`representante_id`),
  ADD KEY `estudiante_id` (`estudiante_id`);

--
-- Indices de la tabla `sincronizacion_offline`
--
ALTER TABLE `sincronizacion_offline`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `identificacion` (`identificacion`),
  ADD KEY `institucion_id` (`institucion_id`),
  ADD KEY `rol` (`rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asignaciones_docente`
--
ALTER TABLE `asignaciones_docente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `asignaturas`
--
ALTER TABLE `asignaturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `auditoria`
--
ALTER TABLE `auditoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `bloqueos_asistencia`
--
ALTER TABLE `bloqueos_asistencia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `calculos_asistencia`
--
ALTER TABLE `calculos_asistencia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `horarios`
--
ALTER TABLE `horarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `instituciones`
--
ALTER TABLE `instituciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `justificaciones`
--
ALTER TABLE `justificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `matriculas`
--
ALTER TABLE `matriculas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `niveles`
--
ALTER TABLE `niveles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `periodos_lectivos`
--
ALTER TABLE `periodos_lectivos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `representantes_estudiantes`
--
ALTER TABLE `representantes_estudiantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sincronizacion_offline`
--
ALTER TABLE `sincronizacion_offline`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asignaciones_docente`
--
ALTER TABLE `asignaciones_docente`
  ADD CONSTRAINT `asignaciones_docente_ibfk_1` FOREIGN KEY (`docente_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `asignaciones_docente_ibfk_2` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`),
  ADD CONSTRAINT `asignaciones_docente_ibfk_3` FOREIGN KEY (`asignatura_id`) REFERENCES `asignaturas` (`id`),
  ADD CONSTRAINT `asignaciones_docente_ibfk_4` FOREIGN KEY (`periodo_lectivo_id`) REFERENCES `periodos_lectivos` (`id`);

--
-- Filtros para la tabla `asignaturas`
--
ALTER TABLE `asignaturas`
  ADD CONSTRAINT `asignaturas_ibfk_1` FOREIGN KEY (`institucion_id`) REFERENCES `instituciones` (`id`);

--
-- Filtros para la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD CONSTRAINT `asistencias_ibfk_1` FOREIGN KEY (`institucion_id`) REFERENCES `instituciones` (`id`),
  ADD CONSTRAINT `asistencias_ibfk_2` FOREIGN KEY (`matricula_id`) REFERENCES `matriculas` (`id`),
  ADD CONSTRAINT `asistencias_ibfk_3` FOREIGN KEY (`asignatura_id`) REFERENCES `asignaturas` (`id`),
  ADD CONSTRAINT `asistencias_ibfk_4` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD CONSTRAINT `auditoria_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `bloqueos_asistencia`
--
ALTER TABLE `bloqueos_asistencia`
  ADD CONSTRAINT `bloqueos_asistencia_ibfk_1` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`),
  ADD CONSTRAINT `bloqueos_asistencia_ibfk_2` FOREIGN KEY (`bloqueado_por`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `calculos_asistencia`
--
ALTER TABLE `calculos_asistencia`
  ADD CONSTRAINT `calculos_asistencia_ibfk_1` FOREIGN KEY (`matricula_id`) REFERENCES `matriculas` (`id`);

--
-- Filtros para la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD CONSTRAINT `configuracion_ibfk_1` FOREIGN KEY (`institucion_id`) REFERENCES `instituciones` (`id`);

--
-- Filtros para la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD CONSTRAINT `cursos_ibfk_1` FOREIGN KEY (`institucion_id`) REFERENCES `instituciones` (`id`),
  ADD CONSTRAINT `cursos_ibfk_2` FOREIGN KEY (`nivel_id`) REFERENCES `niveles` (`id`);

--
-- Filtros para la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`),
  ADD CONSTRAINT `horarios_ibfk_2` FOREIGN KEY (`asignatura_id`) REFERENCES `asignaturas` (`id`);

--
-- Filtros para la tabla `justificaciones`
--
ALTER TABLE `justificaciones`
  ADD CONSTRAINT `justificaciones_ibfk_1` FOREIGN KEY (`matricula_id`) REFERENCES `matriculas` (`id`),
  ADD CONSTRAINT `justificaciones_ibfk_2` FOREIGN KEY (`revisado_por`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `matriculas`
--
ALTER TABLE `matriculas`
  ADD CONSTRAINT `matriculas_ibfk_1` FOREIGN KEY (`institucion_id`) REFERENCES `instituciones` (`id`),
  ADD CONSTRAINT `matriculas_ibfk_2` FOREIGN KEY (`estudiante_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `matriculas_ibfk_3` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`),
  ADD CONSTRAINT `matriculas_ibfk_4` FOREIGN KEY (`periodo_lectivo_id`) REFERENCES `periodos_lectivos` (`id`);

--
-- Filtros para la tabla `niveles`
--
ALTER TABLE `niveles`
  ADD CONSTRAINT `niveles_ibfk_1` FOREIGN KEY (`institucion_id`) REFERENCES `instituciones` (`id`);

--
-- Filtros para la tabla `periodos_lectivos`
--
ALTER TABLE `periodos_lectivos`
  ADD CONSTRAINT `periodos_lectivos_ibfk_1` FOREIGN KEY (`institucion_id`) REFERENCES `instituciones` (`id`);

--
-- Filtros para la tabla `representantes_estudiantes`
--
ALTER TABLE `representantes_estudiantes`
  ADD CONSTRAINT `representantes_estudiantes_ibfk_1` FOREIGN KEY (`representante_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `representantes_estudiantes_ibfk_2` FOREIGN KEY (`estudiante_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `sincronizacion_offline`
--
ALTER TABLE `sincronizacion_offline`
  ADD CONSTRAINT `sincronizacion_offline_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`institucion_id`) REFERENCES `instituciones` (`id`);
--
-- Base de datos: `ecuasistencia2026_db`
--
CREATE DATABASE IF NOT EXISTS `ecuasistencia2026_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ecuasistencia2026_db`;

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
(31, 26, 5, '2026-02-11 10:41:17'),
(32, 1, 3, '2026-02-11 16:34:48');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

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
--
-- Base de datos: `ecuasist_db`
--
CREATE DATABASE IF NOT EXISTS `ecuasist_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `ecuasist_db`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `institutions`
--

INSERT INTO `institutions` (`id`, `name`, `code`, `address`, `phone`, `email`, `logo`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Unidad Educativa Demo', 'UED001', 'Quito, Ecuador', NULL, NULL, NULL, 1, '2026-02-09 16:13:48', '2026-02-09 16:13:48');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'docente', 'Profesor de la institución', '2026-02-09 16:13:48', '2026-02-09 16:13:48'),
(2, 'estudiante', 'Estudiante activo', '2026-02-09 16:13:48', '2026-02-09 16:13:48'),
(3, 'inspector', 'Inspector de disciplina', '2026-02-09 16:13:48', '2026-02-09 16:13:48'),
(4, 'autoridad', 'Autoridad institucional', '2026-02-09 16:13:48', '2026-02-09 16:13:48'),
(5, 'representante', 'Representante legal de estudiante', '2026-02-09 16:13:48', '2026-02-09 16:13:48');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `school_years`
--

INSERT INTO `school_years` (`id`, `institution_id`, `name`, `start_date`, `end_date`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-2026', '2025-05-01', '2026-02-28', 1, '2026-02-09 16:13:48', '2026-02-09 16:13:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `shifts`
--

CREATE TABLE `shifts` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `shifts`
--

INSERT INTO `shifts` (`id`, `name`, `created_at`) VALUES
(1, 'mañana', '2026-02-09 16:13:48'),
(2, 'tarde', '2026-02-09 16:13:48'),
(3, 'noche', '2026-02-09 16:13:48');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_roles`
--

CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `course_students`
--
ALTER TABLE `course_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `teacher_assignments`
--
ALTER TABLE `teacher_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
--
-- Base de datos: `ecuasys`
--
CREATE DATABASE IF NOT EXISTS `ecuasys` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ecuasys`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_general`
--

CREATE TABLE `configuracion_general` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre_institucion` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_07_170410_create_personal_access_tokens_table', 1),
(5, '2026_01_07_170729_add_role_to_users_table', 1),
(6, '2026_01_07_170730_create_configuracion_general_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('TpNx0pT5ywXK2QFhsHLvNc1iRfCrKHtsD8VBGxqK', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUXB1b0pqTXduc0pHTXlUOWd4V0VBdTlzUXJVTFpUaU1ScnhoV2Z4QSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1767806362);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','docente','estudiante') NOT NULL DEFAULT 'estudiante',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `activo`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', 'admin@ecuasys.com', NULL, '$2y$12$dONlyscDCRQVTfk9v1kc6uy1GMSNurHVbBEKlVQOiH63eoZUsI1JC', 'admin', 1, NULL, '2026-01-07 22:10:16', '2026-01-07 22:10:16');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `configuracion_general`
--
ALTER TABLE `configuracion_general`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `configuracion_general`
--
ALTER TABLE `configuracion_general`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- Base de datos: `ecuasys-2026`
--
CREATE DATABASE IF NOT EXISTS `ecuasys-2026` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ecuasys-2026`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `academic_periods`
--

CREATE TABLE `academic_periods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `institution_id` bigint(20) UNSIGNED NOT NULL,
  `school_year_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('Trimestre','Quimestre') NOT NULL COMMENT 'Tipo de periodo',
  `number` int(11) NOT NULL COMMENT 'Número del periodo (1, 2, 3)',
  `start_date` date NOT NULL COMMENT 'Fecha de inicio',
  `end_date` date NOT NULL COMMENT 'Fecha de finalización',
  `weight` decimal(6,4) NOT NULL DEFAULT 33.3333 COMMENT 'Peso porcentual para el promedio final',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_current` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Periodo actual en curso',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `academic_periods`
--

INSERT INTO `academic_periods` (`id`, `institution_id`, `school_year_id`, `name`, `type`, `number`, `start_date`, `end_date`, `weight`, `is_active`, `is_current`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'Primer Trimestre', 'Trimestre', 1, '2025-09-01', '2025-12-20', 33.3333, 1, 1, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(2, 1, 1, 'Segundo Trimestre', 'Trimestre', 2, '2025-12-21', '2026-04-10', 33.3333, 1, 0, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(3, 1, 1, 'Tercer Trimestre', 'Trimestre', 3, '2026-04-11', '2026-07-31', 33.3333, 1, 0, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `agenda_confirmations`
--

CREATE TABLE `agenda_confirmations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `agenda_entry_id` bigint(20) UNSIGNED NOT NULL,
  `representative_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `confirmed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Fecha y hora de confirmación',
  `notes` text DEFAULT NULL COMMENT 'Notas del representante',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `agenda_entries`
--

CREATE TABLE `agenda_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `student_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` date NOT NULL COMMENT 'Fecha de la entrada',
  `type` enum('Tarea','Observación','Comunicado','Evento') NOT NULL DEFAULT 'Comunicado' COMMENT 'Tipo de entrada',
  `title` varchar(255) NOT NULL COMMENT 'Título de la entrada',
  `description` text NOT NULL COMMENT 'Descripción detallada',
  `delivery_date` date DEFAULT NULL COMMENT 'Fecha de entrega (solo para tareas)',
  `is_general` tinyint(1) NOT NULL DEFAULT 1 COMMENT '¿Es para todo el curso?',
  `priority` enum('Baja','Media','Alta') NOT NULL DEFAULT 'Media' COMMENT 'Prioridad de la entrada',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `agenda_entries`
--

INSERT INTO `agenda_entries` (`id`, `course_id`, `teacher_id`, `subject_id`, `student_id`, `date`, `type`, `title`, `description`, `delivery_date`, `is_general`, `priority`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 11, NULL, '2026-01-21', 'Observación', 'gdsfgsfdg', 'gsdfgdfg', NULL, 1, 'Media', '2026-01-21 16:25:04', '2026-01-21 16:25:04', NULL),
(2, 2, 1, 5, 2, '2026-01-21', 'Observación', 'Prueba', 'Prueba de Ingles', NULL, 0, 'Media', '2026-01-21 16:49:58', '2026-01-21 16:49:58', NULL),
(3, 1, 1, 5, 1, '2026-01-21', 'Comunicado', 'Prueba', 'Prueba de Ingles', NULL, 0, 'Media', '2026-01-21 16:50:51', '2026-01-21 16:59:29', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `courses`
--

CREATE TABLE `courses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `institution_id` bigint(20) UNSIGNED NOT NULL,
  `school_year_id` bigint(20) UNSIGNED NOT NULL,
  `educational_level_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `grade_number` int(11) NOT NULL,
  `parallel` varchar(10) NOT NULL,
  `max_students` int(11) NOT NULL DEFAULT 35,
  `current_students` int(11) NOT NULL DEFAULT 0,
  `shift` enum('Matutina','Vespertina','Nocturna') NOT NULL DEFAULT 'Matutina',
  `classroom` varchar(255) DEFAULT NULL COMMENT 'Aula asignada',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `courses`
--

INSERT INTO `courses` (`id`, `institution_id`, `school_year_id`, `educational_level_id`, `teacher_id`, `name`, `grade_number`, `parallel`, `max_students`, `current_students`, `shift`, `classroom`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 2, NULL, '8º Básica A', 8, 'A', 35, 1, 'Matutina', 'Aula 208A', 1, '2026-01-21 13:10:19', '2026-01-21 13:10:25', NULL),
(2, 1, 1, 2, NULL, '8º Básica B', 8, 'B', 35, 1, 'Matutina', 'Aula 208B', 1, '2026-01-21 13:10:19', '2026-01-21 13:10:25', NULL),
(3, 1, 1, 2, NULL, '9º Básica A', 9, 'A', 35, 1, 'Matutina', 'Aula 209A', 1, '2026-01-21 13:10:19', '2026-01-21 13:10:25', NULL),
(4, 1, 1, 2, NULL, '9º Básica B', 9, 'B', 35, 1, 'Matutina', 'Aula 209B', 1, '2026-01-21 13:10:19', '2026-01-21 13:10:25', NULL),
(5, 1, 1, 2, NULL, '10º Básica A', 10, 'A', 35, 1, 'Matutina', 'Aula 2010A', 1, '2026-01-21 13:10:19', '2026-01-21 13:10:25', NULL),
(6, 1, 1, 2, NULL, '10º Básica B', 10, 'B', 35, 1, 'Matutina', 'Aula 2010B', 1, '2026-01-21 13:10:19', '2026-01-21 13:10:25', NULL),
(7, 1, 1, 3, NULL, '1º Bachillerato A', 1, 'A', 40, 1, 'Matutina', 'Aula 41A', 1, '2026-01-21 13:10:19', '2026-01-21 13:10:25', NULL),
(8, 1, 1, 3, NULL, '1º Bachillerato B', 1, 'B', 40, 1, 'Matutina', 'Aula 41B', 1, '2026-01-21 13:10:19', '2026-01-21 13:10:26', NULL),
(9, 1, 1, 3, NULL, '2º Bachillerato A', 2, 'A', 40, 0, 'Matutina', 'Aula 42A', 1, '2026-01-21 13:10:19', '2026-01-21 13:10:19', NULL),
(10, 1, 1, 3, NULL, '2º Bachillerato B', 2, 'B', 40, 0, 'Matutina', 'Aula 42B', 1, '2026-01-21 13:10:19', '2026-01-21 13:10:19', NULL),
(11, 1, 1, 3, NULL, '3º Bachillerato A', 3, 'A', 40, 0, 'Matutina', 'Aula 43A', 1, '2026-01-21 13:10:19', '2026-01-21 13:10:19', NULL),
(12, 1, 1, 3, NULL, '3º Bachillerato B', 3, 'B', 40, 0, 'Matutina', 'Aula 43B', 1, '2026-01-21 13:10:19', '2026-01-21 13:10:19', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `educational_levels`
--

CREATE TABLE `educational_levels` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0 COMMENT 'Orden de visualización',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `educational_levels`
--

INSERT INTO `educational_levels` (`id`, `name`, `code`, `description`, `order`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Inicial', 'INI', 'Educación Inicial (3 a 5 años)', 1, 1, '2026-01-21 13:10:19', '2026-01-21 13:10:19', NULL),
(2, 'Básica', 'BAS', 'Educación General Básica (1ro a 10mo)', 2, 1, '2026-01-21 13:10:19', '2026-01-21 13:10:19', NULL),
(3, 'Bachillerato', 'BACH', 'Bachillerato General Unificado (1ro a 3ro)', 3, 1, '2026-01-21 13:10:19', '2026-01-21 13:10:19', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `enrollments`
--

CREATE TABLE `enrollments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `school_year_id` bigint(20) UNSIGNED NOT NULL,
  `enrollment_date` date NOT NULL COMMENT 'Fecha de matrícula',
  `withdrawal_date` date DEFAULT NULL COMMENT 'Fecha de retiro si aplica',
  `status` enum('Activa','Retirada','Finalizada','Suspendida') NOT NULL DEFAULT 'Activa' COMMENT 'Estado actual de la matrícula',
  `status_reason` text DEFAULT NULL COMMENT 'Motivo de retiro o suspensión',
  `notes` text DEFAULT NULL COMMENT 'Observaciones generales',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `enrollments`
--

INSERT INTO `enrollments` (`id`, `student_id`, `course_id`, `school_year_id`, `enrollment_date`, `withdrawal_date`, `status`, `status_reason`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 1, '2026-01-13', NULL, 'Activa', NULL, NULL, '2026-01-21 13:10:25', '2026-01-21 13:10:25', NULL),
(2, 2, 2, 1, '2026-01-11', NULL, 'Activa', NULL, NULL, '2026-01-21 13:10:25', '2026-01-21 13:10:25', NULL),
(3, 3, 3, 1, '2025-12-24', NULL, 'Activa', NULL, NULL, '2026-01-21 13:10:25', '2026-01-21 13:10:25', NULL),
(4, 4, 4, 1, '2025-12-23', NULL, 'Activa', NULL, NULL, '2026-01-21 13:10:25', '2026-01-21 13:10:25', NULL),
(5, 5, 5, 1, '2025-12-29', NULL, 'Activa', NULL, NULL, '2026-01-21 13:10:25', '2026-01-21 13:10:25', NULL),
(6, 6, 6, 1, '2026-01-05', NULL, 'Activa', NULL, NULL, '2026-01-21 13:10:25', '2026-01-21 13:10:25', NULL),
(7, 7, 7, 1, '2026-01-20', NULL, 'Activa', NULL, NULL, '2026-01-21 13:10:25', '2026-01-21 13:10:25', NULL),
(8, 8, 8, 1, '2026-01-14', NULL, 'Activa', NULL, NULL, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(9, 6, 1, 1, '2025-11-22', '2026-01-11', 'Retirada', 'Cambio de ciudad por motivos familiares', 'Estudiante se retiró voluntariamente', '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grades`
--

CREATE TABLE `grades` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `academic_period_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `grade` decimal(5,2) NOT NULL COMMENT 'Calificación numérica (0.00 - 10.00)',
  `observations` text DEFAULT NULL COMMENT 'Observaciones del docente',
  `graded_at` date NOT NULL COMMENT 'Fecha de registro de la calificación',
  `last_modified_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `grades`
--

INSERT INTO `grades` (`id`, `student_id`, `subject_id`, `academic_period_id`, `course_id`, `teacher_id`, `grade`, `observations`, `graded_at`, `last_modified_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 2, 1, 1, 2, 8.76, 'Buen rendimiento académico. Cumple con las tareas asignadas.', '2025-12-25', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(2, 1, 3, 1, 1, 2, 8.43, NULL, '2026-01-01', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(3, 1, 4, 1, 1, 2, 9.97, 'Excelente desempeño en clase. Participación activa y comprensión profunda de los temas.', '2025-12-26', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(4, 1, 6, 1, 1, 2, 7.15, 'Cumple con los objetivos mínimos. Se recomienda mayor dedicación.', '2025-12-31', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(5, 1, 7, 1, 1, 2, 2.99, 'Rendimiento insuficiente. Requiere atención inmediata y refuerzo.', '2026-01-04', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(6, 2, 1, 1, 2, 2, 8.80, 'Demuestra comprensión de los temas. Puede mejorar en participación.', '2026-01-19', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(7, 2, 2, 1, 2, 2, 7.47, 'Rendimiento aceptable. Necesita reforzar algunos conceptos.', '2026-01-13', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(8, 2, 3, 1, 2, 2, 8.19, 'Buen rendimiento académico. Cumple con las tareas asignadas.', '2025-12-27', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(9, 2, 6, 1, 2, 2, 7.67, 'Desempeño satisfactorio. Estudiante responsable.', '2026-01-01', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(10, 2, 7, 1, 2, 2, 8.26, 'Desempeño satisfactorio. Estudiante responsable.', '2025-12-28', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(11, 3, 2, 1, 3, 2, 8.10, 'Demuestra comprensión de los temas. Puede mejorar en participación.', '2026-01-06', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(12, 3, 3, 1, 3, 2, 7.68, 'Demuestra comprensión de los temas. Puede mejorar en participación.', '2026-01-04', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(13, 3, 4, 1, 3, 2, 5.25, 'Necesita recuperar contenidos. Se ha contactado al representante.', '2026-01-03', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(14, 3, 5, 1, 3, 2, 7.86, NULL, '2026-01-11', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(15, 3, 7, 1, 3, 2, 8.95, NULL, '2026-01-05', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(16, 4, 2, 1, 4, 2, 8.07, 'Demuestra comprensión de los temas. Puede mejorar en participación.', '2025-12-26', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(17, 4, 4, 1, 4, 2, 8.76, 'Desempeño satisfactorio. Estudiante responsable.', '2026-01-08', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(18, 4, 5, 1, 4, 2, 8.18, 'Desempeño satisfactorio. Estudiante responsable.', '2026-01-09', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(19, 4, 6, 1, 4, 2, 9.70, 'Demuestra dominio excepcional de la materia. Sobresale en evaluaciones.', '2025-12-22', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(20, 4, 7, 1, 4, 2, 8.66, 'Demuestra comprensión de los temas. Puede mejorar en participación.', '2025-12-28', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(21, 5, 1, 1, 5, 2, 9.99, NULL, '2026-01-15', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(22, 5, 3, 1, 5, 2, 7.71, 'Desempeño satisfactorio. Estudiante responsable.', '2026-01-09', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(23, 5, 4, 1, 5, 2, 7.65, 'Desempeño satisfactorio. Estudiante responsable.', '2026-01-14', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(24, 5, 5, 1, 5, 2, 8.99, 'Buen rendimiento académico. Cumple con las tareas asignadas.', '2025-12-22', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(25, 5, 7, 1, 5, 2, 4.42, 'Presenta dificultades en algunos temas. Requiere apoyo adicional.', '2025-12-22', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(26, 6, 1, 1, 6, 2, 7.78, 'Desempeño satisfactorio. Estudiante responsable.', '2025-12-29', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(27, 6, 2, 1, 6, 2, 9.75, 'Excelente desempeño en clase. Participación activa y comprensión profunda de los temas.', '2025-12-22', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(28, 6, 4, 1, 6, 2, 2.56, 'Situación académica crítica. Contactar urgentemente al representante.', '2026-01-17', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(29, 6, 5, 1, 6, 2, 8.13, 'Buen rendimiento académico. Cumple con las tareas asignadas.', '2026-01-07', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(30, 6, 6, 1, 6, 2, 9.41, 'Demuestra dominio excepcional de la materia. Sobresale en evaluaciones.', '2025-12-29', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(31, 7, 9, 1, 7, 2, 8.11, 'Buen rendimiento académico. Cumple con las tareas asignadas.', '2026-01-17', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(32, 7, 10, 1, 7, 2, 5.66, 'Se recomienda refuerzo académico. Debe mejorar hábitos de estudio.', '2026-01-08', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(33, 7, 11, 1, 7, 2, 9.41, 'Estudiante destacado. Muestra gran interés y dedicación.', '2026-01-03', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(34, 7, 12, 1, 7, 2, 8.13, NULL, '2026-01-13', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(35, 7, 14, 1, 7, 2, 9.16, 'Demuestra dominio excepcional de la materia. Sobresale en evaluaciones.', '2025-12-22', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(36, 8, 8, 1, 8, 2, 4.94, 'Se recomienda refuerzo académico. Debe mejorar hábitos de estudio.', '2026-01-06', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(37, 8, 9, 1, 8, 2, 8.45, NULL, '2026-01-07', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(38, 8, 10, 1, 8, 2, 7.92, 'Demuestra comprensión de los temas. Puede mejorar en participación.', '2025-12-25', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(39, 8, 13, 1, 8, 2, 9.21, 'Demuestra dominio excepcional de la materia. Sobresale en evaluaciones.', '2025-12-24', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(40, 8, 14, 1, 8, 2, 8.95, 'Buen rendimiento académico. Cumple con las tareas asignadas.', '2026-01-11', 2, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `incidents`
--

CREATE TABLE `incidents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `incident_date` date NOT NULL COMMENT 'Fecha de la incidencia',
  `incident_time` time DEFAULT NULL COMMENT 'Hora de la incidencia',
  `type` enum('Positiva','Negativa') NOT NULL COMMENT 'Tipo de incidencia',
  `category` enum('Disciplinaria','Académica','Asistencia','Convivencia','Otra') NOT NULL COMMENT 'Categoría de la incidencia',
  `severity` enum('Leve','Moderada','Grave','Muy Grave') NOT NULL DEFAULT 'Leve' COMMENT 'Nivel de gravedad',
  `title` varchar(255) NOT NULL COMMENT 'Título breve de la incidencia',
  `description` text NOT NULL COMMENT 'Descripción detallada',
  `measures_taken` text DEFAULT NULL COMMENT 'Medidas tomadas o acciones realizadas',
  `notified_representative` tinyint(1) NOT NULL DEFAULT 0 COMMENT '¿Se notificó al representante?',
  `notification_date` date DEFAULT NULL COMMENT 'Fecha de notificación al representante',
  `representative_response` text DEFAULT NULL COMMENT 'Respuesta del representante',
  `resolved` tinyint(1) NOT NULL DEFAULT 0 COMMENT '¿Incidencia resuelta?',
  `resolution_date` date DEFAULT NULL COMMENT 'Fecha de resolución',
  `resolution_notes` text DEFAULT NULL COMMENT 'Notas de resolución',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `institutions`
--

CREATE TABLE `institutions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL COMMENT 'Código único de institución',
  `type` enum('Fiscal','Particular','Fiscomisional') NOT NULL DEFAULT 'Fiscal',
  `levels` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`levels`)),
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL COMMENT 'Ruta del logo institucional',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `institutions`
--

INSERT INTO `institutions` (`id`, `name`, `code`, `type`, `levels`, `email`, `phone`, `address`, `city`, `logo`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Unidad Educativa Pomasqui', '17H01988', 'Fiscal', '\"[\\\"Inicial\\\",\\\"B\\u00e1sica\\\",\\\"Bachillerato\\\"]\"', '17h01988', '022351072', 'Av. Manuel Córdova Galarza N1-189 y Manuela Sáenz', 'Quito', NULL, 1, '2026-01-21 13:10:19', '2026-01-21 13:10:19', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(16, '2026_01_14_084838_create_representatives_table', 1),
(43, '0001_01_01_000000_create_users_table', 2),
(44, '0001_01_01_000001_create_cache_table', 2),
(45, '0001_01_01_000002_create_jobs_table', 2),
(46, '2026_01_08_230919_create_permission_tables', 2),
(47, '2026_01_08_231024_add_description_to_roles_table', 2),
(48, '2026_01_08_232224_create_institutions_table', 2),
(49, '2026_01_09_120752_modify_institutions_level_to_json', 2),
(50, '2026_01_09_200602_create_school_years_table', 2),
(51, '2026_01_10_105013_create_educational_levels_table', 2),
(52, '2026_01_10_212931_create_courses_table', 2),
(53, '2026_01_11_022333_create_teacher_profiles_table', 2),
(54, '2026_01_12_212028_create_student_profiles_table', 2),
(55, '2026_01_12_213841_create_enrollments_table', 2),
(56, '2026_01_14_065828_create_representative_profiles_table', 2),
(57, '2026_01_14_065855_create_representative_student_table', 2),
(58, '2026_01_14_180907_create_subjects_table', 2),
(59, '2026_01_15_150153_create_academic_periods_table', 2),
(60, '2026_01_16_110151_create_grades_table', 2),
(61, '2026_01_18_204640_create_agenda_entries_table', 2),
(62, '2026_01_18_204641_create_agenda_confirmations_table', 2),
(63, '2026_01_19_182825_create_incidents_table', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(3, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 3),
(3, 'App\\Models\\User', 4),
(3, 'App\\Models\\User', 5),
(3, 'App\\Models\\User', 6),
(3, 'App\\Models\\User', 7),
(3, 'App\\Models\\User', 8),
(3, 'App\\Models\\User', 9),
(5, 'App\\Models\\User', 10),
(5, 'App\\Models\\User', 11),
(5, 'App\\Models\\User', 12),
(5, 'App\\Models\\User', 13),
(5, 'App\\Models\\User', 14),
(5, 'App\\Models\\User', 15),
(5, 'App\\Models\\User', 16),
(5, 'App\\Models\\User', 17),
(6, 'App\\Models\\User', 18),
(6, 'App\\Models\\User', 19),
(6, 'App\\Models\\User', 20),
(6, 'App\\Models\\User', 21),
(6, 'App\\Models\\User', 22);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `representatives`
--

CREATE TABLE `representatives` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `identification_number` varchar(255) NOT NULL COMMENT 'Cédula/DNI',
  `gender` enum('Masculino','Femenino','Otro') DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `secondary_phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL COMMENT 'Profesión u ocupación',
  `workplace` varchar(255) DEFAULT NULL COMMENT 'Lugar de trabajo',
  `work_phone` varchar(20) DEFAULT NULL,
  `civil_status` enum('Soltero/a','Casado/a','Divorciado/a','Viudo/a','Unión libre') DEFAULT NULL,
  `status` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `representatives`
--

INSERT INTO `representatives` (`id`, `first_name`, `last_name`, `identification_number`, `gender`, `birth_date`, `email`, `phone`, `secondary_phone`, `address`, `city`, `occupation`, `workplace`, `work_phone`, `civil_status`, `status`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Roberto', 'Pérez González', '0901020304', 'Masculino', '1980-05-15', 'roberto.perez@example.com', '0991234567', '022345678', 'Av. Principal 123', 'Quito', 'Ingeniero Civil', 'Constructora ABC', '023456789', 'Casado/a', 'Activo', NULL, '2026-01-14 14:08:36', '2026-01-14 14:08:36', NULL),
(2, 'María', 'López Torres', '0901020305', 'Femenino', '1982-08-22', 'maria.lopez@example.com', '0991234568', NULL, 'Calle Secundaria 456', 'Quito', 'Docente', 'Colegio Nacional', NULL, 'Casado/a', 'Activo', NULL, '2026-01-14 14:08:36', '2026-01-14 14:08:36', NULL),
(3, 'Carlos', 'Ramírez Castro', '0901020306', 'Masculino', '1978-12-10', 'carlos.ramirez@example.com', '0991234569', NULL, 'Barrio El Centro', 'Quito', 'Comerciante', NULL, NULL, 'Divorciado/a', 'Activo', NULL, '2026-01-14 14:08:36', '2026-01-14 14:08:36', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `representative_profiles`
--

CREATE TABLE `representative_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `institution_id` bigint(20) UNSIGNED DEFAULT NULL,
  `identification_number` varchar(255) NOT NULL COMMENT 'Cédula/DNI',
  `birth_date` date DEFAULT NULL,
  `gender` enum('Masculino','Femenino','Otro') DEFAULT NULL,
  `personal_phone` varchar(20) DEFAULT NULL,
  `work_phone` varchar(20) DEFAULT NULL,
  `emergency_phone` varchar(20) DEFAULT NULL,
  `email_secondary` varchar(255) DEFAULT NULL COMMENT 'Email alternativo',
  `address` text DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL COMMENT 'Profesión u ocupación',
  `workplace` varchar(255) DEFAULT NULL COMMENT 'Lugar de trabajo',
  `workplace_address` text DEFAULT NULL,
  `civil_status` enum('Soltero/a','Casado/a','Divorciado/a','Viudo/a','Unión libre') DEFAULT NULL,
  `status` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `representative_profiles`
--

INSERT INTO `representative_profiles` (`id`, `user_id`, `institution_id`, `identification_number`, `birth_date`, `gender`, `personal_phone`, `work_phone`, `emergency_phone`, `email_secondary`, `address`, `occupation`, `workplace`, `workplace_address`, `civil_status`, `status`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 18, 1, '0101010101', '1980-05-15', 'Masculino', '0987654321', '072345678', NULL, NULL, 'Cuenca, Ecuador', 'Ingeniero Civil', 'Constructora Mendoza S.A.', NULL, 'Casado/a', 'Activo', NULL, '2026-01-21 13:10:24', '2026-01-21 13:10:24', NULL),
(2, 19, 1, '0101010102', '1982-08-22', 'Femenino', '0987654322', '072345679', NULL, NULL, 'Cuenca, Ecuador', 'Doctora', 'Hospital Regional', NULL, 'Casado/a', 'Activo', NULL, '2026-01-21 13:10:24', '2026-01-21 13:10:24', NULL),
(3, 20, 1, '0101010103', '1978-03-10', 'Masculino', '0987654323', NULL, NULL, NULL, 'Cuenca, Ecuador', 'Comerciante', 'Tienda Torres', NULL, 'Casado/a', 'Activo', NULL, '2026-01-21 13:10:25', '2026-01-21 13:10:25', NULL),
(4, 21, 1, '0101010104', '1985-11-30', 'Femenino', '0987654324', NULL, '0987654399', NULL, 'Cuenca, Ecuador', 'Contadora', 'Estudio Contable Rivas', NULL, 'Casado/a', 'Activo', NULL, '2026-01-21 13:10:25', '2026-01-21 13:10:25', NULL),
(5, 22, 1, '0101010105', '1983-07-18', 'Masculino', '0987654325', NULL, NULL, NULL, 'Cuenca, Ecuador', 'Profesor Universitario', 'Universidad de Cuenca', NULL, 'Casado/a', 'Activo', NULL, '2026-01-21 13:10:25', '2026-01-21 13:10:25', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `representative_student`
--

CREATE TABLE `representative_student` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `representative_profile_id` bigint(20) UNSIGNED NOT NULL,
  `student_profile_id` bigint(20) UNSIGNED NOT NULL,
  `relationship_type` enum('Padre','Madre','Tutor Legal','Abuelo/a','Tío/a','Hermano/a','Otro') NOT NULL DEFAULT 'Padre',
  `is_primary` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Representante principal',
  `is_authorized_pickup` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Autorizado para retirar estudiante',
  `lives_with_student` tinyint(1) NOT NULL DEFAULT 0 COMMENT '¿Vive con el estudiante?',
  `priority_contact` int(11) NOT NULL DEFAULT 1 COMMENT 'Orden de contacto (1=primero)',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `representative_student`
--

INSERT INTO `representative_student` (`id`, `representative_profile_id`, `student_profile_id`, `relationship_type`, `is_primary`, `is_authorized_pickup`, `lives_with_student`, `priority_contact`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'Padre', 1, 1, 1, 1, NULL, '2026-01-21 13:10:24', '2026-01-21 13:10:24'),
(2, 1, 6, 'Madre', 0, 1, 1, 2, NULL, '2026-01-21 13:10:24', '2026-01-21 13:10:24'),
(3, 2, 5, 'Padre', 1, 1, 1, 1, NULL, '2026-01-21 13:10:24', '2026-01-21 13:10:24'),
(4, 2, 8, 'Madre', 0, 1, 1, 2, NULL, '2026-01-21 13:10:24', '2026-01-21 13:10:24'),
(5, 3, 1, 'Padre', 1, 1, 1, 1, NULL, '2026-01-21 13:10:25', '2026-01-21 13:10:25'),
(6, 3, 8, 'Madre', 0, 1, 1, 2, NULL, '2026-01-21 13:10:25', '2026-01-21 13:10:25'),
(7, 4, 1, 'Padre', 1, 1, 1, 1, NULL, '2026-01-21 13:10:25', '2026-01-21 13:10:25'),
(8, 4, 5, 'Madre', 0, 1, 1, 2, NULL, '2026-01-21 13:10:25', '2026-01-21 13:10:25'),
(9, 5, 3, 'Padre', 1, 1, 1, 1, NULL, '2026-01-21 13:10:25', '2026-01-21 13:10:25'),
(10, 5, 8, 'Madre', 0, 1, 1, 2, NULL, '2026-01-21 13:10:25', '2026-01-21 13:10:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', 'web', 'Control total del sistema', '2026-01-21 13:10:17', '2026-01-21 13:10:17'),
(2, 'Autoridad', 'web', 'Director, Rector, Coordinador académico', '2026-01-21 13:10:18', '2026-01-21 13:10:18'),
(3, 'Docente', 'web', 'Profesor de asignaturas', '2026-01-21 13:10:18', '2026-01-21 13:10:18'),
(4, 'Docente Tutor', 'web', 'Tutor responsable de un curso', '2026-01-21 13:10:18', '2026-01-21 13:10:18'),
(5, 'Estudiante', 'web', 'Alumno del sistema', '2026-01-21 13:10:18', '2026-01-21 13:10:18'),
(6, 'Representante', 'web', 'Padre, madre o tutor legal', '2026-01-21 13:10:18', '2026-01-21 13:10:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `school_years`
--

CREATE TABLE `school_years` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `institution_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('Activo','Inactivo','Finalizado') NOT NULL DEFAULT 'Inactivo',
  `is_current` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Indica el año lectivo actual de la institución',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Control lógico de visibilidad/estado',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `school_years`
--

INSERT INTO `school_years` (`id`, `institution_id`, `name`, `start_date`, `end_date`, `status`, `is_current`, `is_active`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, '2025-2026', '2025-09-01', '2026-07-31', 'Activo', 1, 1, 'Año lectivo actual en curso', '2026-01-21 13:10:19', '2026-01-21 13:10:19', NULL),
(2, 1, '2026-2027', '2026-09-01', '2027-07-31', 'Inactivo', 0, 1, 'Año lectivo planificado para el próximo periodo', '2026-01-21 13:10:19', '2026-01-21 13:10:19', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('bYDyQR2hxGvm9mZbvr06gMmGIqT3MXcauL7WdnS6', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiRE90cHFjamJ6MjlRQ1Zwd2M2YUtPdTY2cG5EM0hpbzZ3Wkx6alc3cSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9yZXByZXNlbnRhdGl2ZXMiO3M6NToicm91dGUiO3M6MjE6InJlcHJlc2VudGF0aXZlcy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjA6e31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1769178283),
('h9PfwxFMOkFDHZ5bdif62Rk0oFMA95GvvClXZpXt', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNjh3bTFCMzcyUGFJQ050dFhDcTZXMHk1cVZDZ0hGQXFBZ3dGT1FpWSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2Rhc2hib2FyZCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvZGFzaGJvYXJkIjtzOjU6InJvdXRlIjtzOjk6ImRhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1769178164),
('tM38uPXcE3BgGxUk6XLVl5kmdnKJJvVIgoN7FSiB', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVFg5Mk1Sd2hZM1VSZ3ZYM1RPUENDYXdxV2FBTUd6em1KZDl1MU0yQSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1769178164);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `student_profiles`
--

CREATE TABLE `student_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `institution_id` bigint(20) UNSIGNED DEFAULT NULL,
  `identification_number` varchar(255) NOT NULL COMMENT 'Cédula/DNI del estudiante',
  `birth_date` date DEFAULT NULL,
  `gender` enum('Masculino','Femenino','Otro') DEFAULT NULL,
  `birth_place` varchar(255) DEFAULT NULL COMMENT 'Lugar de nacimiento',
  `nationality` varchar(255) NOT NULL DEFAULT 'Ecuatoriana',
  `blood_type` varchar(10) DEFAULT NULL COMMENT 'Tipo de sangre',
  `personal_phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `medical_conditions` text DEFAULT NULL COMMENT 'Condiciones médicas, alergias',
  `special_needs` text DEFAULT NULL COMMENT 'Necesidades educativas especiales',
  `previous_school` varchar(255) DEFAULT NULL COMMENT 'Institución anterior',
  `enrollment_year` year(4) DEFAULT NULL COMMENT 'Año de ingreso al sistema',
  `status` enum('Activo','Inactivo','Retirado','Graduado') NOT NULL DEFAULT 'Activo',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `student_profiles`
--

INSERT INTO `student_profiles` (`id`, `user_id`, `institution_id`, `identification_number`, `birth_date`, `gender`, `birth_place`, `nationality`, `blood_type`, `personal_phone`, `address`, `medical_conditions`, `special_needs`, `previous_school`, `enrollment_year`, `status`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 10, 1, '0150607080', '2010-03-15', 'Masculino', 'Cuenca, Ecuador', 'Ecuatoriana', 'O+', '0993636661', 'Cuenca, Ecuador', NULL, NULL, NULL, '2024', 'Activo', NULL, '2026-01-21 13:10:21', '2026-01-21 13:10:21', NULL),
(2, 11, 1, '0150607081', '2010-07-22', 'Femenino', 'Cuenca, Ecuador', 'Ecuatoriana', 'A+', '0997934562', 'Cuenca, Ecuador', NULL, NULL, NULL, '2024', 'Activo', NULL, '2026-01-21 13:10:21', '2026-01-21 13:10:21', NULL),
(3, 12, 1, '0150607082', '2011-11-08', 'Masculino', 'Azogues, Ecuador', 'Ecuatoriana', 'B+', '0994946155', 'Cuenca, Ecuador', NULL, NULL, NULL, '2024', 'Activo', NULL, '2026-01-21 13:10:22', '2026-01-21 13:10:22', NULL),
(4, 13, 1, '0150607083', '2011-05-30', 'Femenino', 'Cuenca, Ecuador', 'Ecuatoriana', 'AB+', '0999013421', 'Cuenca, Ecuador', NULL, NULL, NULL, '2024', 'Activo', NULL, '2026-01-21 13:10:22', '2026-01-21 13:10:22', NULL),
(5, 14, 1, '0150607084', '2010-12-18', 'Masculino', 'Cuenca, Ecuador', 'Ecuatoriana', 'O-', '0995686972', 'Cuenca, Ecuador', NULL, NULL, NULL, '2024', 'Activo', NULL, '2026-01-21 13:10:22', '2026-01-21 13:10:22', NULL),
(6, 15, 1, '0150607085', '2012-02-14', 'Femenino', 'Cuenca, Ecuador', 'Ecuatoriana', 'A-', '0993973687', 'Cuenca, Ecuador', NULL, NULL, NULL, '2024', 'Activo', NULL, '2026-01-21 13:10:23', '2026-01-21 13:10:23', NULL),
(7, 16, 1, '0150607086', '2012-09-20', 'Masculino', 'Cuenca, Ecuador', 'Ecuatoriana', 'B-', '0999269202', 'Cuenca, Ecuador', NULL, NULL, NULL, '2024', 'Activo', NULL, '2026-01-21 13:10:23', '2026-01-21 13:10:23', NULL),
(8, 17, 1, '0150607087', '2013-04-10', 'Femenino', 'Cuenca, Ecuador', 'Ecuatoriana', 'O+', '0998971356', 'Cuenca, Ecuador', NULL, NULL, NULL, '2024', 'Activo', NULL, '2026-01-21 13:10:23', '2026-01-21 13:10:23', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `subjects`
--

CREATE TABLE `subjects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `institution_id` bigint(20) UNSIGNED NOT NULL,
  `educational_level_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(255) NOT NULL COMMENT 'Código único de la asignatura',
  `name` varchar(255) NOT NULL COMMENT 'Nombre de la asignatura',
  `description` text DEFAULT NULL,
  `type` enum('Obligatoria','Optativa') NOT NULL DEFAULT 'Obligatoria',
  `weekly_hours` int(11) NOT NULL DEFAULT 1 COMMENT 'Horas semanales',
  `order` int(11) NOT NULL DEFAULT 0 COMMENT 'Orden de visualización',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `subjects`
--

INSERT INTO `subjects` (`id`, `institution_id`, `educational_level_id`, `teacher_id`, `code`, `name`, `description`, `type`, `weekly_hours`, `order`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 2, 2, 'MAT-BAS', 'Matemáticas', 'Desarrollo del pensamiento lógico y resolución de problemas', 'Obligatoria', 5, 1, 1, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(2, 1, 2, 3, 'LEN-BAS', 'Lengua y Literatura', 'Comprensión lectora y expresión escrita', 'Obligatoria', 5, 2, 1, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(3, 1, 2, 4, 'CCNN-BAS', 'Ciencias Naturales', 'Estudio del mundo natural y científico', 'Obligatoria', 4, 3, 1, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(4, 1, 2, 5, 'CCSS-BAS', 'Estudios Sociales', 'Historia, geografía y cívica', 'Obligatoria', 4, 4, 1, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(5, 1, 2, 6, 'ING-BAS', 'Inglés', 'Lengua extranjera', 'Obligatoria', 3, 5, 1, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(6, 1, 2, 2, 'EDF-BAS', 'Educación Física', 'Desarrollo físico y deportivo', 'Obligatoria', 2, 6, 1, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(7, 1, 2, 3, 'ECA-BAS', 'Educación Cultural y Artística', 'Expresión artística y cultural', 'Obligatoria', 2, 7, 1, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(8, 1, 3, 2, 'MAT-BACH', 'Matemáticas', 'Álgebra, geometría y cálculo', 'Obligatoria', 5, 1, 1, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(9, 1, 3, 3, 'FIS-BACH', 'Física', 'Estudio de las leyes de la naturaleza', 'Obligatoria', 4, 2, 1, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(10, 1, 3, 4, 'QUIM-BACH', 'Química', 'Estudio de la materia y sus transformaciones', 'Obligatoria', 3, 3, 1, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(11, 1, 3, 5, 'BIO-BACH', 'Biología', 'Estudio de los seres vivos', 'Obligatoria', 3, 4, 1, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(12, 1, 3, 6, 'HIST-BACH', 'Historia', 'Historia universal y del Ecuador', 'Obligatoria', 3, 5, 1, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(13, 1, 3, 2, 'FIL-BACH', 'Filosofía', 'Pensamiento crítico y filosófico', 'Obligatoria', 2, 6, 1, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL),
(14, 1, 3, 3, 'EMC-BACH', 'Emprendimiento y Gestión', 'Desarrollo de proyectos emprendedores', 'Optativa', 2, 7, 1, '2026-01-21 13:10:26', '2026-01-21 13:10:26', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `teacher_profiles`
--

CREATE TABLE `teacher_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `institution_id` bigint(20) UNSIGNED DEFAULT NULL,
  `identification_number` varchar(255) NOT NULL COMMENT 'Cédula/DNI',
  `birth_date` date DEFAULT NULL,
  `gender` enum('Masculino','Femenino','Otro') DEFAULT NULL,
  `personal_phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `civil_status` enum('Soltero/a','Casado/a','Divorciado/a','Viudo/a','Unión libre') DEFAULT NULL,
  `professional_title` varchar(255) DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `hire_date` date DEFAULT NULL COMMENT 'Fecha de ingreso',
  `contract_type` enum('Nombramiento','Contrato','Honorarios') NOT NULL DEFAULT 'Contrato',
  `subjects_can_teach` text DEFAULT NULL COMMENT 'Asignaturas que puede dictar',
  `status` enum('Activo','Inactivo','Licencia') NOT NULL DEFAULT 'Activo',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `teacher_profiles`
--

INSERT INTO `teacher_profiles` (`id`, `user_id`, `institution_id`, `identification_number`, `birth_date`, `gender`, `personal_phone`, `address`, `civil_status`, `professional_title`, `specialization`, `hire_date`, `contract_type`, `subjects_can_teach`, `status`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 5, 1, '0102030405', '1985-03-15', 'Femenino', '076237577', 'Cuenca, Ecuador', 'Soltero/a', 'Licenciada en Educación Básica', 'Matemáticas', '2015-09-01', 'Nombramiento', 'Matemáticas', 'Activo', NULL, '2026-01-21 13:10:19', '2026-01-21 13:10:19', NULL),
(2, 6, 1, '0102030406', '1982-07-22', 'Masculino', '076457641', 'Cuenca, Ecuador', 'Soltero/a', 'Licenciado en Ciencias de la Educación', 'Lengua y Literatura', '2012-05-15', 'Nombramiento', 'Lengua y Literatura', 'Activo', NULL, '2026-01-21 13:10:20', '2026-01-21 13:10:20', NULL),
(3, 7, 1, '0102030407', '1990-11-08', 'Femenino', '077127136', 'Cuenca, Ecuador', 'Soltero/a', 'Licenciada en Educación Inicial', 'Educación Inicial', '2018-02-01', 'Contrato', 'Educación Inicial', 'Activo', NULL, '2026-01-21 13:10:20', '2026-01-21 13:10:20', NULL),
(4, 8, 1, '0102030408', '1988-05-30', 'Masculino', '079515306', 'Cuenca, Ecuador', 'Soltero/a', 'Licenciado en Ciencias Exactas', 'Física y Química', '2016-09-01', 'Nombramiento', 'Física y Química', 'Activo', NULL, '2026-01-21 13:10:20', '2026-01-21 13:10:20', NULL),
(5, 9, 1, '0102030409', '1992-12-18', 'Femenino', '075267483', 'Cuenca, Ecuador', 'Soltero/a', 'Licenciada en Ciencias Sociales', 'Historia y Geografía', '2019-09-02', 'Contrato', 'Historia y Geografía', 'Activo', NULL, '2026-01-21 13:10:21', '2026-01-21 13:10:21', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Administrador Sistema', 'admin@ecuasys.test', NULL, '$2y$12$RUGsfv7aOY47r6MeSaJN9udHGCQeUnDXmjoEmpBqnhgAISN2DV6xi', NULL, '2026-01-21 13:10:18', '2026-01-21 13:10:18'),
(2, 'Juan Pérez', 'juan.perez@ecuasys.test', NULL, '$2y$12$lFl6LL9j8KZjEXTSaxzhyOYe9jzZSgwL1BlXdbCYN5JHNFUXJd7ay', NULL, '2026-01-21 13:10:18', '2026-01-21 13:10:18'),
(3, 'María López', 'maria.lopez@ecuasys.test', NULL, '$2y$12$kNjyyemhBbQhCaIdebT45eUFMsHScR5edhZFC9FixxqDy2gqs70lq', NULL, '2026-01-21 13:10:19', '2026-01-21 13:10:19'),
(4, 'Carlos Ruiz', 'carlos.ruiz@ecuasys.test', NULL, '$2y$12$fYqbfXZDswL26hR.CAOgf.C/m89NNtKerQBzv/aeG9g16rP.l3hYO', NULL, '2026-01-21 13:10:19', '2026-01-21 13:10:19'),
(5, 'María Fernanda González', 'maria.gonzalez@ecuasys.test', NULL, '$2y$12$RgtSLoFDUynaYSG6bOogoOuQoafoHaS7Ybp5GhxOysTHpSTnXfkI6', NULL, '2026-01-21 13:10:19', '2026-01-21 13:10:19'),
(6, 'Carlos Alberto Ramírez', 'carlos.ramirez@ecuasys.test', NULL, '$2y$12$tIh.yuAPaHb6soAFiSmTdOA.04chI5A832hudZebKZRwSppPBA1wa', NULL, '2026-01-21 13:10:20', '2026-01-21 13:10:20'),
(7, 'Ana Patricia Torres', 'ana.torres@ecuasys.test', NULL, '$2y$12$ruXSUPy3xwIIJS7VL4wotOMrE9WZjoDJ7bSdIqPAl9j86ER2DxQ/q', NULL, '2026-01-21 13:10:20', '2026-01-21 13:10:20'),
(8, 'Jorge Luis Mendoza', 'jorge.mendoza@ecuasys.test', NULL, '$2y$12$hsQiutF1dxwRCUIBdadfJuaoVvlBPFdcpnEK4heDk.PoI38zg2dVS', NULL, '2026-01-21 13:10:20', '2026-01-21 13:10:20'),
(9, 'Sofía Valentina Castro', 'sofia.castro@ecuasys.test', NULL, '$2y$12$2x2NnokNadXSFwz.ew4StO6yq24J8sAGVoa.IyuXM1h9hhqb07.aq', NULL, '2026-01-21 13:10:21', '2026-01-21 13:10:21'),
(10, 'Juan Carlos Pérez López', 'juan.perez@estudiante.test', NULL, '$2y$12$exCaN1OAGIJVrUIUolKa1./O5dgm1FAx/c0KEMj.XTgwMnBq/fxRi', NULL, '2026-01-21 13:10:21', '2026-01-21 13:10:21'),
(11, 'María José González Torres', 'maria.gonzalez@estudiante.test', NULL, '$2y$12$zTVYLkHSR44VhwlkkIEoKOh9pZutUDOwNAK122rq.l6mhhT1GDnN2', NULL, '2026-01-21 13:10:21', '2026-01-21 13:10:21'),
(12, 'Carlos Andrés Ramírez Castro', 'carlos.ramirez@estudiante.test', NULL, '$2y$12$ADUxMcpDoxPDn/Q/bO9u3.dgqtk5kpnfR4WTx5BjKvNB1Rnieuxii', NULL, '2026-01-21 13:10:22', '2026-01-21 13:10:22'),
(13, 'Ana Sofía Mendoza Vera', 'ana.mendoza@estudiante.test', NULL, '$2y$12$l.jiYrJmkc4PmvA3sjJuNuVsUrgL1Zt7OGgqAc1ksePIRQxp0zeBi', NULL, '2026-01-21 13:10:22', '2026-01-21 13:10:22'),
(14, 'Diego Alejandro Torres Sánchez', 'diego.torres@estudiante.test', NULL, '$2y$12$YFbPDgQvmvQRIYikSjOWvO6LP5S8Wx/shgrYHqWLXXUCNDwG/dO02', NULL, '2026-01-21 13:10:22', '2026-01-21 13:10:22'),
(15, 'Valentina Isabel Cruz Morales', 'valentina.cruz@estudiante.test', NULL, '$2y$12$P0gbn1LQ1TFkx5oksOxfzeEy4FzRtMKicyhU3UbK5e6sJRB4UMOZu', NULL, '2026-01-21 13:10:23', '2026-01-21 13:10:23'),
(16, 'Mateo Sebastián Rojas Fernández', 'mateo.rojas@estudiante.test', NULL, '$2y$12$KhzWreoIGffXf85Zlh5sn.e6ptgwacVwHjdGqRZr/vIig6gkq0SNW', NULL, '2026-01-21 13:10:23', '2026-01-21 13:10:23'),
(17, 'Isabella Camila Vega Ortiz', 'isabella.vega@estudiante.test', NULL, '$2y$12$tbCk8kyrmHR/CRkvTv5y2uFikXyAR0HY1MchDlVd9Lyge64BU.1SW', NULL, '2026-01-21 13:10:23', '2026-01-21 13:10:23'),
(18, 'Roberto Carlos Mendoza', 'roberto.mendoza@representante.test', NULL, '$2y$12$4gNJtWLlYsBru.T4TFHOxeqnzZ3nPqNO0OzVbk6lNn6npKC2GWzPm', NULL, '2026-01-21 13:10:24', '2026-01-21 13:10:24'),
(19, 'María Fernanda Álvarez', 'maria.alvarez@representante.test', NULL, '$2y$12$b6YfUI0CW42W3JFFt9Acb.noGdjqt6f1ZUQajQdFUCmx9jVDZkspi', NULL, '2026-01-21 13:10:24', '2026-01-21 13:10:24'),
(20, 'Juan Pablo Torres', 'juan.torres@representante.test', NULL, '$2y$12$F4kx/S.g12/JN20lmGTYOec/J.uDF1P8ADSio/CdMj/bYnPlv/x5a', NULL, '2026-01-21 13:10:25', '2026-01-21 13:10:25'),
(21, 'Carmen Lucía Rivas', 'carmen.rivas@representante.test', NULL, '$2y$12$El8CEtv.Wap6tjZ4gc9X6eroZKbONwaT.OMkcJlMvOJQiAERh/jJO', NULL, '2026-01-21 13:10:25', '2026-01-21 13:10:25'),
(22, 'Diego Alejandro Castro', 'diego.castro@representante.test', NULL, '$2y$12$ey4UTqQrlFx4sXZ4gjHyDepq0Acy.kg3fLwaRaquuo1nW3HGK3.qa', NULL, '2026-01-21 13:10:25', '2026-01-21 13:10:25');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `academic_periods`
--
ALTER TABLE `academic_periods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_period_per_year` (`school_year_id`,`type`,`number`),
  ADD KEY `academic_periods_institution_id_index` (`institution_id`),
  ADD KEY `academic_periods_school_year_id_index` (`school_year_id`),
  ADD KEY `academic_periods_is_active_index` (`is_active`),
  ADD KEY `academic_periods_is_current_index` (`is_current`),
  ADD KEY `academic_periods_type_number_index` (`type`,`number`);

--
-- Indices de la tabla `agenda_confirmations`
--
ALTER TABLE `agenda_confirmations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_confirmation_per_entry_rep_student` (`agenda_entry_id`,`representative_id`,`student_id`),
  ADD KEY `agenda_confirmations_agenda_entry_id_index` (`agenda_entry_id`),
  ADD KEY `agenda_confirmations_representative_id_index` (`representative_id`),
  ADD KEY `agenda_confirmations_student_id_index` (`student_id`);

--
-- Indices de la tabla `agenda_entries`
--
ALTER TABLE `agenda_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `agenda_entries_subject_id_foreign` (`subject_id`),
  ADD KEY `agenda_entries_student_id_foreign` (`student_id`),
  ADD KEY `agenda_entries_course_id_index` (`course_id`),
  ADD KEY `agenda_entries_teacher_id_index` (`teacher_id`),
  ADD KEY `agenda_entries_date_index` (`date`),
  ADD KEY `agenda_entries_type_index` (`type`),
  ADD KEY `agenda_entries_is_general_index` (`is_general`),
  ADD KEY `agenda_entries_course_id_date_index` (`course_id`,`date`);

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_course_context` (`institution_id`,`school_year_id`,`educational_level_id`,`grade_number`,`parallel`,`shift`),
  ADD KEY `courses_teacher_id_foreign` (`teacher_id`),
  ADD KEY `courses_institution_id_index` (`institution_id`),
  ADD KEY `courses_school_year_id_index` (`school_year_id`),
  ADD KEY `courses_educational_level_id_index` (`educational_level_id`),
  ADD KEY `courses_is_active_index` (`is_active`);

--
-- Indices de la tabla `educational_levels`
--
ALTER TABLE `educational_levels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `educational_levels_name_unique` (`name`),
  ADD UNIQUE KEY `educational_levels_code_unique` (`code`),
  ADD KEY `educational_levels_is_active_index` (`is_active`),
  ADD KEY `educational_levels_order_index` (`order`);

--
-- Indices de la tabla `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_enrollment_per_student_course_year` (`student_id`,`course_id`,`school_year_id`),
  ADD KEY `enrollments_student_id_index` (`student_id`),
  ADD KEY `enrollments_course_id_index` (`course_id`),
  ADD KEY `enrollments_school_year_id_index` (`school_year_id`),
  ADD KEY `enrollments_status_index` (`status`),
  ADD KEY `enrollments_enrollment_date_index` (`enrollment_date`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_grade_per_student_subject_period` (`student_id`,`subject_id`,`academic_period_id`),
  ADD KEY `grades_last_modified_by_foreign` (`last_modified_by`),
  ADD KEY `grades_student_id_index` (`student_id`),
  ADD KEY `grades_subject_id_index` (`subject_id`),
  ADD KEY `grades_academic_period_id_index` (`academic_period_id`),
  ADD KEY `grades_course_id_index` (`course_id`),
  ADD KEY `grades_teacher_id_index` (`teacher_id`),
  ADD KEY `grades_graded_at_index` (`graded_at`);

--
-- Indices de la tabla `incidents`
--
ALTER TABLE `incidents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `incidents_subject_id_foreign` (`subject_id`),
  ADD KEY `incidents_student_id_index` (`student_id`),
  ADD KEY `incidents_course_id_index` (`course_id`),
  ADD KEY `incidents_teacher_id_index` (`teacher_id`),
  ADD KEY `incidents_incident_date_index` (`incident_date`),
  ADD KEY `incidents_type_index` (`type`),
  ADD KEY `incidents_category_index` (`category`),
  ADD KEY `incidents_severity_index` (`severity`),
  ADD KEY `incidents_resolved_index` (`resolved`),
  ADD KEY `incidents_student_id_incident_date_index` (`student_id`,`incident_date`),
  ADD KEY `incidents_course_id_type_index` (`course_id`,`type`);

--
-- Indices de la tabla `institutions`
--
ALTER TABLE `institutions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `institutions_code_unique` (`code`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indices de la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indices de la tabla `representatives`
--
ALTER TABLE `representatives`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `representatives_identification_number_unique` (`identification_number`),
  ADD UNIQUE KEY `representatives_email_unique` (`email`),
  ADD KEY `representatives_identification_number_index` (`identification_number`),
  ADD KEY `representatives_email_index` (`email`),
  ADD KEY `representatives_status_index` (`status`);

--
-- Indices de la tabla `representative_profiles`
--
ALTER TABLE `representative_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `representative_profiles_user_id_unique` (`user_id`),
  ADD UNIQUE KEY `representative_profiles_identification_number_unique` (`identification_number`),
  ADD KEY `representative_profiles_user_id_index` (`user_id`),
  ADD KEY `representative_profiles_institution_id_index` (`institution_id`),
  ADD KEY `representative_profiles_status_index` (`status`),
  ADD KEY `representative_profiles_identification_number_index` (`identification_number`);

--
-- Indices de la tabla `representative_student`
--
ALTER TABLE `representative_student`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_representative_student_relationship` (`representative_profile_id`,`student_profile_id`,`relationship_type`),
  ADD KEY `representative_student_representative_profile_id_index` (`representative_profile_id`),
  ADD KEY `representative_student_student_profile_id_index` (`student_profile_id`),
  ADD KEY `representative_student_is_primary_index` (`is_primary`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indices de la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indices de la tabla `school_years`
--
ALTER TABLE `school_years`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_school_year_per_institution` (`institution_id`,`name`),
  ADD KEY `school_years_institution_id_index` (`institution_id`),
  ADD KEY `school_years_is_current_index` (`is_current`),
  ADD KEY `school_years_is_active_index` (`is_active`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_profiles_user_id_unique` (`user_id`),
  ADD UNIQUE KEY `student_profiles_identification_number_unique` (`identification_number`),
  ADD KEY `student_profiles_user_id_index` (`user_id`),
  ADD KEY `student_profiles_institution_id_index` (`institution_id`),
  ADD KEY `student_profiles_status_index` (`status`),
  ADD KEY `student_profiles_identification_number_index` (`identification_number`);

--
-- Indices de la tabla `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_subject_code_per_institution` (`institution_id`,`code`),
  ADD KEY `subjects_institution_id_index` (`institution_id`),
  ADD KEY `subjects_educational_level_id_index` (`educational_level_id`),
  ADD KEY `subjects_teacher_id_index` (`teacher_id`),
  ADD KEY `subjects_is_active_index` (`is_active`),
  ADD KEY `subjects_order_index` (`order`);

--
-- Indices de la tabla `teacher_profiles`
--
ALTER TABLE `teacher_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teacher_profiles_user_id_unique` (`user_id`),
  ADD UNIQUE KEY `teacher_profiles_identification_number_unique` (`identification_number`),
  ADD KEY `teacher_profiles_user_id_index` (`user_id`),
  ADD KEY `teacher_profiles_institution_id_index` (`institution_id`),
  ADD KEY `teacher_profiles_status_index` (`status`),
  ADD KEY `teacher_profiles_identification_number_index` (`identification_number`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `academic_periods`
--
ALTER TABLE `academic_periods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `agenda_confirmations`
--
ALTER TABLE `agenda_confirmations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `agenda_entries`
--
ALTER TABLE `agenda_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `courses`
--
ALTER TABLE `courses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `educational_levels`
--
ALTER TABLE `educational_levels`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `grades`
--
ALTER TABLE `grades`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `incidents`
--
ALTER TABLE `incidents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `institutions`
--
ALTER TABLE `institutions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `representatives`
--
ALTER TABLE `representatives`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `representative_profiles`
--
ALTER TABLE `representative_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `representative_student`
--
ALTER TABLE `representative_student`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `school_years`
--
ALTER TABLE `school_years`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `student_profiles`
--
ALTER TABLE `student_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `teacher_profiles`
--
ALTER TABLE `teacher_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `academic_periods`
--
ALTER TABLE `academic_periods`
  ADD CONSTRAINT `academic_periods_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `academic_periods_school_year_id_foreign` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `agenda_confirmations`
--
ALTER TABLE `agenda_confirmations`
  ADD CONSTRAINT `agenda_confirmations_agenda_entry_id_foreign` FOREIGN KEY (`agenda_entry_id`) REFERENCES `agenda_entries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `agenda_confirmations_representative_id_foreign` FOREIGN KEY (`representative_id`) REFERENCES `representative_profiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `agenda_confirmations_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `student_profiles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `agenda_entries`
--
ALTER TABLE `agenda_entries`
  ADD CONSTRAINT `agenda_entries_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `agenda_entries_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `student_profiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `agenda_entries_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `agenda_entries_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_educational_level_id_foreign` FOREIGN KEY (`educational_level_id`) REFERENCES `educational_levels` (`id`),
  ADD CONSTRAINT `courses_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `courses_school_year_id_foreign` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `courses_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `enrollments_school_year_id_foreign` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`id`),
  ADD CONSTRAINT `enrollments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `student_profiles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_academic_period_id_foreign` FOREIGN KEY (`academic_period_id`) REFERENCES `academic_periods` (`id`),
  ADD CONSTRAINT `grades_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `grades_last_modified_by_foreign` FOREIGN KEY (`last_modified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `grades_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `student_profiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grades_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `grades_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `incidents`
--
ALTER TABLE `incidents`
  ADD CONSTRAINT `incidents_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `incidents_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `student_profiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `incidents_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `incidents_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `representative_profiles`
--
ALTER TABLE `representative_profiles`
  ADD CONSTRAINT `representative_profiles_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `representative_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `representative_student`
--
ALTER TABLE `representative_student`
  ADD CONSTRAINT `representative_student_representative_profile_id_foreign` FOREIGN KEY (`representative_profile_id`) REFERENCES `representative_profiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `representative_student_student_profile_id_foreign` FOREIGN KEY (`student_profile_id`) REFERENCES `student_profiles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `school_years`
--
ALTER TABLE `school_years`
  ADD CONSTRAINT `school_years_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD CONSTRAINT `student_profiles_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `student_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_educational_level_id_foreign` FOREIGN KEY (`educational_level_id`) REFERENCES `educational_levels` (`id`),
  ADD CONSTRAINT `subjects_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subjects_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `teacher_profiles`
--
ALTER TABLE `teacher_profiles`
  ADD CONSTRAINT `teacher_profiles_institution_id_foreign` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `teacher_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
--
-- Base de datos: `ecuasys2026`
--
CREATE DATABASE IF NOT EXISTS `ecuasys2026` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ecuasys2026`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_08_150635_create_permission_tables', 2),
(5, '2026_01_08_151130_add_description_to_roles_table', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', 'web', 'Control total del sistema', '2026-01-08 20:17:45', '2026-01-08 20:17:45'),
(2, 'Autoridad', 'web', 'Director, Rector, Coordinador académico', '2026-01-08 20:17:45', '2026-01-08 20:17:45'),
(3, 'Docente', 'web', 'Profesor de asignaturas', '2026-01-08 20:17:45', '2026-01-08 20:17:45'),
(4, 'Docente Tutor', 'web', 'Tutor responsable de un curso', '2026-01-08 20:17:45', '2026-01-08 20:17:45'),
(5, 'Estudiante', 'web', 'Alumno del sistema', '2026-01-08 20:17:45', '2026-01-08 20:17:45'),
(6, 'Representante', 'web', 'Padre, madre o tutor legal', '2026-01-08 20:17:45', '2026-01-08 20:17:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('ntEMcajZUQvHSFpPYb8aVdUNn2QbqKr5DVlRat1A', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:146.0) Gecko/20100101 Firefox/146.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibU1sT2JmZ3pCWnBvenY0UktrbDE3b0hlSVhuOHJ3WXVod1RjVmVpUyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1767902697);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin Prueba', 'admin@ecuasys.test', NULL, '$2y$12$tfyQAeolC8kaShVBQSiApO00oGa6zx3LLwHZzIUQTA45nElIJSQPe', NULL, '2026-01-08 19:58:29', '2026-01-08 19:58:29');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indices de la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indices de la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
--
-- Base de datos: `mysis`
--
CREATE DATABASE IF NOT EXISTS `mysis` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `mysis`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Base de datos: `phpmyadmin`
--
CREATE DATABASE IF NOT EXISTS `phpmyadmin` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `phpmyadmin`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__bookmark`
--

CREATE TABLE `pma__bookmark` (
  `id` int(10) UNSIGNED NOT NULL,
  `dbase` varchar(255) NOT NULL DEFAULT '',
  `user` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `query` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Bookmarks';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__central_columns`
--

CREATE TABLE `pma__central_columns` (
  `db_name` varchar(64) NOT NULL,
  `col_name` varchar(64) NOT NULL,
  `col_type` varchar(64) NOT NULL,
  `col_length` text DEFAULT NULL,
  `col_collation` varchar(64) NOT NULL,
  `col_isNull` tinyint(1) NOT NULL,
  `col_extra` varchar(255) DEFAULT '',
  `col_default` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Central list of columns';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__column_info`
--

CREATE TABLE `pma__column_info` (
  `id` int(5) UNSIGNED NOT NULL,
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `column_name` varchar(64) NOT NULL DEFAULT '',
  `comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `mimetype` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `transformation` varchar(255) NOT NULL DEFAULT '',
  `transformation_options` varchar(255) NOT NULL DEFAULT '',
  `input_transformation` varchar(255) NOT NULL DEFAULT '',
  `input_transformation_options` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Column information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__designer_settings`
--

CREATE TABLE `pma__designer_settings` (
  `username` varchar(64) NOT NULL,
  `settings_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Settings related to Designer';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__export_templates`
--

CREATE TABLE `pma__export_templates` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `export_type` varchar(10) NOT NULL,
  `template_name` varchar(64) NOT NULL,
  `template_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved export templates';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__favorite`
--

CREATE TABLE `pma__favorite` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Favorite tables';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__history`
--

CREATE TABLE `pma__history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db` varchar(64) NOT NULL DEFAULT '',
  `table` varchar(64) NOT NULL DEFAULT '',
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp(),
  `sqlquery` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='SQL history for phpMyAdmin';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__navigationhiding`
--

CREATE TABLE `pma__navigationhiding` (
  `username` varchar(64) NOT NULL,
  `item_name` varchar(64) NOT NULL,
  `item_type` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Hidden items of navigation tree';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__pdf_pages`
--

CREATE TABLE `pma__pdf_pages` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `page_nr` int(10) UNSIGNED NOT NULL,
  `page_descr` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='PDF relation pages for phpMyAdmin';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__recent`
--

CREATE TABLE `pma__recent` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Recently accessed tables';

--
-- Volcado de datos para la tabla `pma__recent`
--

INSERT INTO `pma__recent` (`username`, `tables`) VALUES
('root', '[{\"db\":\"ecuasistencia2026_db\",\"table\":\"attendances\"},{\"db\":\"ecuasistencia2026_db\",\"table\":\"shifts\"},{\"db\":\"ecuasistencia2026_db\",\"table\":\"institutions\"},{\"db\":\"ecuasistencia2026_db\",\"table\":\"roles\"},{\"db\":\"asistencia_escolar\",\"table\":\"usuarios\"},{\"db\":\"ecuasys-2026\",\"table\":\"institutions\"},{\"db\":\"ecuasys-2026\",\"table\":\"users\"},{\"db\":\"ecuasys-2026\",\"table\":\"representative_student\"},{\"db\":\"ecuasys-2026\",\"table\":\"representative_profiles\"},{\"db\":\"ecuasys-2026\",\"table\":\"teacher_profiles\"}]');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__relation`
--

CREATE TABLE `pma__relation` (
  `master_db` varchar(64) NOT NULL DEFAULT '',
  `master_table` varchar(64) NOT NULL DEFAULT '',
  `master_field` varchar(64) NOT NULL DEFAULT '',
  `foreign_db` varchar(64) NOT NULL DEFAULT '',
  `foreign_table` varchar(64) NOT NULL DEFAULT '',
  `foreign_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Relation table';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__savedsearches`
--

CREATE TABLE `pma__savedsearches` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `search_name` varchar(64) NOT NULL DEFAULT '',
  `search_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved searches';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__table_coords`
--

CREATE TABLE `pma__table_coords` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `pdf_page_number` int(11) NOT NULL DEFAULT 0,
  `x` float UNSIGNED NOT NULL DEFAULT 0,
  `y` float UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table coordinates for phpMyAdmin PDF output';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__table_info`
--

CREATE TABLE `pma__table_info` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `display_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__table_uiprefs`
--

CREATE TABLE `pma__table_uiprefs` (
  `username` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `prefs` text NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tables'' UI preferences';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__tracking`
--

CREATE TABLE `pma__tracking` (
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `version` int(10) UNSIGNED NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `schema_snapshot` text NOT NULL,
  `schema_sql` text DEFAULT NULL,
  `data_sql` longtext DEFAULT NULL,
  `tracking` set('UPDATE','REPLACE','INSERT','DELETE','TRUNCATE','CREATE DATABASE','ALTER DATABASE','DROP DATABASE','CREATE TABLE','ALTER TABLE','RENAME TABLE','DROP TABLE','CREATE INDEX','DROP INDEX','CREATE VIEW','ALTER VIEW','DROP VIEW') DEFAULT NULL,
  `tracking_active` int(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Database changes tracking for phpMyAdmin';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__userconfig`
--

CREATE TABLE `pma__userconfig` (
  `username` varchar(64) NOT NULL,
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `config_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User preferences storage for phpMyAdmin';

--
-- Volcado de datos para la tabla `pma__userconfig`
--

INSERT INTO `pma__userconfig` (`username`, `timevalue`, `config_data`) VALUES
('root', '2026-02-11 11:56:28', '{\"Console\\/Mode\":\"collapse\",\"lang\":\"es\"}');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__usergroups`
--

CREATE TABLE `pma__usergroups` (
  `usergroup` varchar(64) NOT NULL,
  `tab` varchar(64) NOT NULL,
  `allowed` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User groups with configured menu items';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__users`
--

CREATE TABLE `pma__users` (
  `username` varchar(64) NOT NULL,
  `usergroup` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Users and their assignments to user groups';

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pma__central_columns`
--
ALTER TABLE `pma__central_columns`
  ADD PRIMARY KEY (`db_name`,`col_name`);

--
-- Indices de la tabla `pma__column_info`
--
ALTER TABLE `pma__column_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `db_name` (`db_name`,`table_name`,`column_name`);

--
-- Indices de la tabla `pma__designer_settings`
--
ALTER TABLE `pma__designer_settings`
  ADD PRIMARY KEY (`username`);

--
-- Indices de la tabla `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_user_type_template` (`username`,`export_type`,`template_name`);

--
-- Indices de la tabla `pma__favorite`
--
ALTER TABLE `pma__favorite`
  ADD PRIMARY KEY (`username`);

--
-- Indices de la tabla `pma__history`
--
ALTER TABLE `pma__history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`db`,`table`,`timevalue`);

--
-- Indices de la tabla `pma__navigationhiding`
--
ALTER TABLE `pma__navigationhiding`
  ADD PRIMARY KEY (`username`,`item_name`,`item_type`,`db_name`,`table_name`);

--
-- Indices de la tabla `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  ADD PRIMARY KEY (`page_nr`),
  ADD KEY `db_name` (`db_name`);

--
-- Indices de la tabla `pma__recent`
--
ALTER TABLE `pma__recent`
  ADD PRIMARY KEY (`username`);

--
-- Indices de la tabla `pma__relation`
--
ALTER TABLE `pma__relation`
  ADD PRIMARY KEY (`master_db`,`master_table`,`master_field`),
  ADD KEY `foreign_field` (`foreign_db`,`foreign_table`);

--
-- Indices de la tabla `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_savedsearches_username_dbname` (`username`,`db_name`,`search_name`);

--
-- Indices de la tabla `pma__table_coords`
--
ALTER TABLE `pma__table_coords`
  ADD PRIMARY KEY (`db_name`,`table_name`,`pdf_page_number`);

--
-- Indices de la tabla `pma__table_info`
--
ALTER TABLE `pma__table_info`
  ADD PRIMARY KEY (`db_name`,`table_name`);

--
-- Indices de la tabla `pma__table_uiprefs`
--
ALTER TABLE `pma__table_uiprefs`
  ADD PRIMARY KEY (`username`,`db_name`,`table_name`);

--
-- Indices de la tabla `pma__tracking`
--
ALTER TABLE `pma__tracking`
  ADD PRIMARY KEY (`db_name`,`table_name`,`version`);

--
-- Indices de la tabla `pma__userconfig`
--
ALTER TABLE `pma__userconfig`
  ADD PRIMARY KEY (`username`);

--
-- Indices de la tabla `pma__usergroups`
--
ALTER TABLE `pma__usergroups`
  ADD PRIMARY KEY (`usergroup`,`tab`,`allowed`);

--
-- Indices de la tabla `pma__users`
--
ALTER TABLE `pma__users`
  ADD PRIMARY KEY (`username`,`usergroup`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pma__column_info`
--
ALTER TABLE `pma__column_info`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pma__history`
--
ALTER TABLE `pma__history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  MODIFY `page_nr` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Base de datos: `test`
--
CREATE DATABASE IF NOT EXISTS `test` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `test`;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
