-- =====================================================
-- DATOS DE PRUEBA COMPLETOS PARA ECUASIST 2026
-- =====================================================

-- 1. CREAR MÁS USUARIOS (docentes, estudiantes, representantes)
-- Contraseña para todos: "password"

-- DOCENTES (6 nuevos docentes)
INSERT INTO users (institution_id, username, email, password, first_name, last_name, dni, phone, is_active) VALUES
(1, 'prof.garcia', 'garcia@colegio.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Roberto', 'García Mendoza', '1751234567', '0991234571', 1),
(1, 'prof.martinez', 'martinez@colegio.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ana', 'Martínez Silva', '1751234568', '0991234572', 1),
(1, 'prof.rodriguez', 'rodriguez@colegio.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Luis', 'Rodríguez Cano', '1751234569', '0991234573', 1),
(1, 'prof.fernandez', 'fernandez@colegio.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carmen', 'Fernández Ortiz', '1751234570', '0991234574', 1),
(1, 'prof.gomez', 'gomez@colegio.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Miguel', 'Gómez Torres', '1751234571', '0991234575', 1),
(1, 'prof.diaz', 'diaz@colegio.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Patricia', 'Díaz Ramírez', '1751234572', '0991234576', 1);

-- ESTUDIANTES ADICIONALES (10 nuevos estudiantes)
INSERT INTO users (institution_id, username, email, password, first_name, last_name, dni, phone, is_active) VALUES
(1, 'sofia.castro', 'sofia.castro@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sofía', 'Castro Morales', '1750567890', '0991234577', 1),
(1, 'diego.herrera', 'diego.herrera@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Diego', 'Herrera Ríos', '1750567891', '0991234578', 1),
(1, 'valentina.lopez', 'valentina.lopez@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Valentina', 'López Suárez', '1750567892', '0991234579', 1),
(1, 'mateo.silva', 'mateo.silva@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mateo', 'Silva Navarro', '1750567893', '0991234580', 1),
(1, 'isabella.rojas', 'isabella.rojas@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Isabella', 'Rojas Pérez', '1750567894', '0991234581', 1),
(1, 'sebastian.cruz', 'sebastian.cruz@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sebastián', 'Cruz Méndez', '1750567895', '0991234582', 1),
(1, 'camila.ramos', 'camila.ramos@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Camila', 'Ramos Flores', '1750567896', '0991234583', 1),
(1, 'nicolas.vargas', 'nicolas.vargas@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nicolás', 'Vargas Castro', '1750567897', '0991234584', 1),
(1, 'martina.gil', 'martina.gil@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Martina', 'Gil Núñez', '1750567898', '0991234585', 1),
(1, 'lucas.medina', 'lucas.medina@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lucas', 'Medina Ortega', '1750567899', '0991234586', 1);

-- REPRESENTANTES (5 nuevos)
INSERT INTO users (institution_id, username, email, password, first_name, last_name, dni, phone, is_active) VALUES
(1, 'rep.castro', 'lcastro@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Laura', 'Castro Vega', '1752345678', '0991234587', 1),
(1, 'rep.herrera', 'jherrera@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jorge', 'Herrera Palacios', '1752345679', '0991234588', 1),
(1, 'rep.lopez', 'mlopez@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'María', 'López Benítez', '1752345680', '0991234589', 1),
(1, 'rep.silva', 'psilva@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pedro', 'Silva Moreno', '1752345681', '0991234590', 1),
(1, 'rep.rojas', 'crojas@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carolina', 'Rojas Delgado', '1752345682', '0991234591', 1);

-- 2. ASIGNAR ROLES
-- Asumiendo IDs: Usuarios anteriores 1-5, Docentes 6-11, Estudiantes 12-21, Representantes 22-26

-- Roles a docentes (rol_id = 1 para docente)
INSERT INTO user_roles (user_id, role_id) VALUES
(6, 1), (7, 1), (8, 1), (9, 1), (10, 1), (11, 1);

-- Roles a estudiantes (rol_id = 2 para estudiante)
INSERT INTO user_roles (user_id, role_id) VALUES
(12, 2), (13, 2), (14, 2), (15, 2), (16, 2),
(17, 2), (18, 2), (19, 2), (20, 2), (21, 2);

-- Roles a representantes (rol_id = 5 para representante)
INSERT INTO user_roles (user_id, role_id) VALUES
(22, 5), (23, 5), (24, 5), (25, 5), (26, 5);

-- 3. CREAR MÁS CURSOS
INSERT INTO courses (institution_id, school_year_id, name, grade_level, parallel, shift_id) VALUES
(1, 1, 'Séptimo A', 'Séptimo', 'A', 1),
(1, 1, 'Octavo B', 'Octavo', 'B', 1),
(1, 1, 'Noveno A', 'Noveno', 'A', 2),
(1, 1, 'Décimo A', 'Décimo', 'A', 2);

-- 4. CREAR MÁS ASIGNATURAS
INSERT INTO subjects (institution_id, name, code) VALUES
(1, 'Inglés', 'ING'),
(1, 'Educación Física', 'EDF'),
(1, 'Arte y Cultura', 'ART'),
(1, 'Informática', 'INF');

-- 5. MATRICULAR ESTUDIANTES EN CURSOS
-- Asumiendo: Curso 1 ya existe, Cursos nuevos 2-5

-- Octavo A (curso 1) - ya tiene estudiantes
-- Séptimo A (curso 2)
INSERT INTO course_students (student_id, course_id, school_year_id, enrollment_date) VALUES
(12, 2, 1, '2025-05-01'),
(13, 2, 1, '2025-05-01'),
(14, 2, 1, '2025-05-01');

-- Octavo B (curso 3)
INSERT INTO course_students (student_id, course_id, school_year_id, enrollment_date) VALUES
(15, 3, 1, '2025-05-01'),
(16, 3, 1, '2025-05-01'),
(17, 3, 1, '2025-05-01');

-- Noveno A (curso 4)
INSERT INTO course_students (student_id, course_id, school_year_id, enrollment_date) VALUES
(18, 4, 1, '2025-05-01'),
(19, 4, 1, '2025-05-01');

-- Décimo A (curso 5)
INSERT INTO course_students (student_id, course_id, school_year_id, enrollment_date) VALUES
(20, 5, 1, '2025-05-01'),
(21, 5, 1, '2025-05-01');

-- 6. ASIGNAR DOCENTES A MATERIAS Y CURSOS
-- Profesor García (6) - Matemáticas en varios cursos
INSERT INTO teacher_assignments (teacher_id, course_id, subject_id, school_year_id, is_tutor) VALUES
(6, 1, 1, 1, 1), -- Octavo A, Matemáticas, TUTOR
(6, 2, 1, 1, 0), -- Séptimo A, Matemáticas
(6, 3, 1, 1, 0); -- Octavo B, Matemáticas

-- Profesora Martínez (7) - Lengua y Literatura
INSERT INTO teacher_assignments (teacher_id, course_id, subject_id, school_year_id, is_tutor) VALUES
(7, 1, 2, 1, 0), -- Octavo A
(7, 2, 2, 1, 1), -- Séptimo A, TUTORA
(7, 4, 2, 1, 0); -- Noveno A

-- Profesor Rodríguez (8) - Ciencias Naturales
INSERT INTO teacher_assignments (teacher_id, course_id, subject_id, school_year_id, is_tutor) VALUES
(8, 1, 3, 1, 0), -- Octavo A
(8, 3, 3, 1, 1), -- Octavo B, TUTOR
(8, 5, 3, 1, 0); -- Décimo A

-- Profesora Fernández (9) - Estudios Sociales
INSERT INTO teacher_assignments (teacher_id, course_id, subject_id, school_year_id, is_tutor) VALUES
(9, 2, 4, 1, 0), -- Séptimo A
(9, 3, 4, 1, 0), -- Octavo B
(9, 4, 4, 1, 1); -- Noveno A, TUTORA

-- Profesor Gómez (10) - Inglés
INSERT INTO teacher_assignments (teacher_id, course_id, subject_id, school_year_id, is_tutor) VALUES
(10, 1, 5, 1, 0), -- Octavo A
(10, 2, 5, 1, 0), -- Séptimo A
(10, 4, 5, 1, 0), -- Noveno A
(10, 5, 5, 1, 1); -- Décimo A, TUTOR

-- Profesora Díaz (11) - Educación Física
INSERT INTO teacher_assignments (teacher_id, course_id, subject_id, school_year_id, is_tutor) VALUES
(11, 1, 6, 1, 0), -- Octavo A
(11, 3, 6, 1, 0), -- Octavo B
(11, 5, 6, 1, 0); -- Décimo A

-- 7. ASIGNAR REPRESENTANTES A ESTUDIANTES
-- Representante Castro (22) - Sofía Castro (12) y Diego Herrera (13) son hermanos
INSERT INTO representatives (representative_id, student_id, relationship, is_primary) VALUES
(22, 12, 'Madre', 1),
(22, 13, 'Tía', 0);

-- Representante Herrera (23) - Valentina López (14)
INSERT INTO representatives (representative_id, student_id, relationship, is_primary) VALUES
(23, 14, 'Padre', 1);

-- Representante López (24) - Mateo Silva (15) e Isabella Rojas (16) son hermanos
INSERT INTO representatives (representative_id, student_id, relationship, is_primary) VALUES
(24, 15, 'Madre', 1),
(24, 16, 'Madre', 1);

-- Representante Silva (25) - Sebastián Cruz (17) y Camila Ramos (18)
INSERT INTO representatives (representative_id, student_id, relationship, is_primary) VALUES
(25, 17, 'Padre', 1),
(25, 18, 'Tutor Legal', 0);

-- Representante Rojas (26) - Nicolás Vargas (19), Martina Gil (20), Lucas Medina (21)
INSERT INTO representatives (representative_id, student_id, relationship, is_primary) VALUES
(26, 19, 'Madre', 1),
(26, 20, 'Madre', 1),
(26, 21, 'Madre', 1);

-- 8. REGISTROS DE ASISTENCIA DE EJEMPLO
-- Asistencia Octavo A - Matemáticas - última semana

-- Lunes (hace 4 días)
INSERT INTO attendances (student_id, course_id, subject_id, teacher_id, school_year_id, shift_id, date, hour_period, status, observation) VALUES
(2, 1, 1, 6, 1, 1, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '1ra hora', 'presente', ''),
(3, 1, 1, 6, 1, 1, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '1ra hora', 'presente', ''),
(4, 1, 1, 6, 1, 1, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '1ra hora', 'ausente', 'Sin justificación'),
(5, 1, 1, 6, 1, 1, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '1ra hora', 'presente', '');

-- Martes (hace 3 días)
INSERT INTO attendances (student_id, course_id, subject_id, teacher_id, school_year_id, shift_id, date, hour_period, status, observation) VALUES
(2, 1, 1, 6, 1, 1, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '1ra hora', 'tardanza', 'Llegó 10 min tarde'),
(3, 1, 1, 6, 1, 1, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '1ra hora', 'presente', ''),
(4, 1, 1, 6, 1, 1, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '1ra hora', 'justificado', 'Cita médica'),
(5, 1, 1, 6, 1, 1, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '1ra hora', 'presente', '');

-- Miércoles (hace 2 días)
INSERT INTO attendances (student_id, course_id, subject_id, teacher_id, school_year_id, shift_id, date, hour_period, status, observation) VALUES
(2, 1, 1, 6, 1, 1, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '1ra hora', 'presente', ''),
(3, 1, 1, 6, 1, 1, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '1ra hora', 'presente', ''),
(4, 1, 1, 6, 1, 1, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '1ra hora', 'presente', ''),
(5, 1, 1, 6, 1, 1, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '1ra hora', 'ausente', '');

-- Jueves (ayer)
INSERT INTO attendances (student_id, course_id, subject_id, teacher_id, school_year_id, shift_id, date, hour_period, status, observation) VALUES
(2, 1, 1, 6, 1, 1, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '1ra hora', 'presente', ''),
(3, 1, 1, 6, 1, 1, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '1ra hora', 'presente', ''),
(4, 1, 1, 6, 1, 1, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '1ra hora', 'presente', ''),
(5, 1, 1, 6, 1, 1, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '1ra hora', 'tardanza', 'Tráfico');

-- Hoy
INSERT INTO attendances (student_id, course_id, subject_id, teacher_id, school_year_id, shift_id, date, hour_period, status, observation) VALUES
(2, 1, 1, 6, 1, 1, CURDATE(), '1ra hora', 'presente', ''),
(3, 1, 1, 6, 1, 1, CURDATE(), '1ra hora', 'presente', ''),
(4, 1, 1, 6, 1, 1, CURDATE(), '1ra hora', 'presente', ''),
(5, 1, 1, 6, 1, 1, CURDATE(), '1ra hora', 'presente', '');

-- 9. VERIFICAR DATOS
SELECT 'RESUMEN DE DATOS INSERTADOS' as '';

SELECT COUNT(*) as 'Total Usuarios' FROM users;
SELECT COUNT(*) as 'Total Docentes' FROM user_roles WHERE role_id = 1;
SELECT COUNT(*) as 'Total Estudiantes' FROM user_roles WHERE role_id = 2;
SELECT COUNT(*) as 'Total Representantes' FROM user_roles WHERE role_id = 5;
SELECT COUNT(*) as 'Total Cursos' FROM courses;
SELECT COUNT(*) as 'Total Asignaturas' FROM subjects;
SELECT COUNT(*) as 'Total Matrículas' FROM course_students;
SELECT COUNT(*) as 'Total Asignaciones Docentes' FROM teacher_assignments;
SELECT COUNT(*) as 'Total Relaciones Representante-Estudiante' FROM representatives;
SELECT COUNT(*) as 'Total Registros de Asistencia' FROM attendances;