-- =====================================================
-- ECUASIST 2026 - BASE DE DATOS COMPLETA
-- =====================================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS ecuasistencia2026_db 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE ecuasistencia2026_db;

-- =====================================================
-- ESTRUCTURA DE TABLAS
-- =====================================================

-- Instituciones
CREATE TABLE institutions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(100),
    logo VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Usuarios
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    institution_id INT NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    dni VARCHAR(20) UNIQUE,
    phone VARCHAR(20),
    reset_token VARCHAR(255),
    reset_expires DATETIME,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (institution_id) REFERENCES institutions(id)
) ENGINE=InnoDB;

-- Roles
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Usuarios-Roles
CREATE TABLE user_roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_role (user_id, role_id)
) ENGINE=InnoDB;

-- Permisos especiales
CREATE TABLE permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    permission_type VARCHAR(50) NOT NULL,
    entity_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Años lectivos
CREATE TABLE school_years (
    id INT PRIMARY KEY AUTO_INCREMENT,
    institution_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    is_active TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (institution_id) REFERENCES institutions(id)
) ENGINE=InnoDB;

-- Jornadas
CREATE TABLE shifts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Cursos
CREATE TABLE courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    institution_id INT NOT NULL,
    school_year_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    grade_level VARCHAR(50),
    parallel VARCHAR(10),
    shift_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (institution_id) REFERENCES institutions(id),
    FOREIGN KEY (school_year_id) REFERENCES school_years(id),
    FOREIGN KEY (shift_id) REFERENCES shifts(id)
) ENGINE=InnoDB;

-- Asignaturas
CREATE TABLE subjects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    institution_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (institution_id) REFERENCES institutions(id)
) ENGINE=InnoDB;

-- Asignaciones docente
CREATE TABLE teacher_assignments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    teacher_id INT NOT NULL,
    course_id INT NOT NULL,
    subject_id INT NOT NULL,
    school_year_id INT NOT NULL,
    is_tutor TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES users(id),
    FOREIGN KEY (course_id) REFERENCES courses(id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id),
    FOREIGN KEY (school_year_id) REFERENCES school_years(id)
) ENGINE=InnoDB;

-- Estudiantes en cursos
CREATE TABLE course_students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    school_year_id INT NOT NULL,
    enrollment_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id),
    FOREIGN KEY (course_id) REFERENCES courses(id),
    FOREIGN KEY (school_year_id) REFERENCES school_years(id),
    UNIQUE KEY unique_enrollment (student_id, course_id, school_year_id)
) ENGINE=InnoDB;

-- Representantes
CREATE TABLE representatives (
    id INT PRIMARY KEY AUTO_INCREMENT,
    representative_id INT NOT NULL,
    student_id INT NOT NULL,
    relationship VARCHAR(50),
    is_primary TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (representative_id) REFERENCES users(id),
    FOREIGN KEY (student_id) REFERENCES users(id),
    UNIQUE KEY unique_rep_student (representative_id, student_id)
) ENGINE=InnoDB;

-- Asistencias
CREATE TABLE attendances (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    subject_id INT NOT NULL,
    teacher_id INT NOT NULL,
    school_year_id INT NOT NULL,
    shift_id INT NOT NULL,
    date DATE NOT NULL,
    hour_period VARCHAR(20),
    status ENUM('presente', 'ausente', 'tardanza', 'justificado') NOT NULL,
    observation TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id),
    FOREIGN KEY (course_id) REFERENCES courses(id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id),
    FOREIGN KEY (teacher_id) REFERENCES users(id),
    FOREIGN KEY (school_year_id) REFERENCES school_years(id),
    FOREIGN KEY (shift_id) REFERENCES shifts(id),
    INDEX idx_date (date),
    INDEX idx_student (student_id)
) ENGINE=InnoDB;

-- Justificaciones
CREATE TABLE justifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    attendance_id INT NOT NULL,
    student_id INT NOT NULL,
    submitted_by INT NOT NULL,
    reason TEXT NOT NULL,
    document_path VARCHAR(255),
    status ENUM('pendiente', 'aprobado', 'rechazado') DEFAULT 'pendiente',
    reviewed_by INT,
    review_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (attendance_id) REFERENCES attendances(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id),
    FOREIGN KEY (submitted_by) REFERENCES users(id),
    FOREIGN KEY (reviewed_by) REFERENCES users(id),
    INDEX idx_status (status),
    INDEX idx_student (student_id)
) ENGINE=InnoDB;

-- Notificaciones
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'warning', 'success', 'danger') DEFAULT 'info',
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_read (user_id, is_read)
) ENGINE=InnoDB;

-- Logs de actividad
CREATE TABLE activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_user (user_id),
    INDEX idx_date (created_at)
) ENGINE=InnoDB;

-- =====================================================
-- DATOS INICIALES
-- =====================================================

-- Institución
INSERT INTO institutions (name, code, address, phone, email, is_active) VALUES
('Unidad Educativa Pomasqui', 'UEP001', 'Av. Manuel Cordova Galarza 123, Quito, Ecuador', '02-2351072', 'contacto@uep.edu.ec', 1);

-- Roles base
INSERT INTO roles (name, description) VALUES
('docente', 'Profesor de la institución'),
('estudiante', 'Estudiante activo'),
('inspector', 'Inspector de disciplina'),
('autoridad', 'Autoridad institucional'),
('representante', 'Representante legal de estudiante');

-- Jornadas
INSERT INTO shifts (name) VALUES 
('matutina'), 
('vespertina'), 
('nocturna');

-- Año lectivo activo
INSERT INTO school_years (institution_id, name, start_date, end_date, is_active) VALUES
(1, '2025-2026', '2025-09-01', '2026-07-30', 1);

-- =====================================================
-- USUARIOS DE PRUEBA
-- Contraseña para todos: "password"
-- =====================================================

-- Usuario Administrador
INSERT INTO users (institution_id, username, email, password, first_name, last_name, dni, phone, is_active) VALUES
(1, 'admin', 'admin@uep.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'Sistema', '1700000000', '0999999999', 1);

-- Docentes (6)
INSERT INTO users (institution_id, username, email, password, first_name, last_name, dni, phone, is_active) VALUES
(1, 'prof.garcia', 'garcia@uep.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Roberto', 'García Mendoza', '1751234567', '0991234571', 1),
(1, 'prof.martinez', 'martinez@uep.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ana', 'Martínez Silva', '1751234568', '0991234572', 1),
(1, 'prof.rodriguez', 'rodriguez@uep.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Luis', 'Rodríguez Cano', '1751234569', '0991234573', 1),
(1, 'prof.fernandez', 'fernandez@uep.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carmen', 'Fernández Ortiz', '1751234570', '0991234574', 1),
(1, 'prof.gomez', 'gomez@uep.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Miguel', 'Gómez Torres', '1751234571', '0991234575', 1),
(1, 'prof.diaz', 'diaz@uep.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Patricia', 'Díaz Ramírez', '1751234572', '0991234576', 1);

-- Estudiantes (15)
INSERT INTO users (institution_id, username, email, password, first_name, last_name, dni, phone, is_active) VALUES
(1, 'juan.perez', 'juan.perez@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Juan', 'Pérez García', '1750123456', '0991234567', 1),
(1, 'maria.lopez', 'maria.lopez@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'María', 'López Sánchez', '1750234567', '0991234568', 1),
(1, 'carlos.mora', 'carlos.mora@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carlos', 'Mora Ruiz', '1750345678', '0991234569', 1),
(1, 'ana.torres', 'ana.torres@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ana', 'Torres Vega', '1750456789', '0991234570', 1),
(1, 'sofia.castro', 'sofia.castro@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sofía', 'Castro Morales', '1750567890', '0991234577', 1),
(1, 'diego.herrera', 'diego.herrera@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Diego', 'Herrera Ríos', '1750567891', '0991234578', 1),
(1, 'valentina.lopez', 'valentina.lopez@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Valentina', 'López Suárez', '1750567892', '0991234579', 1),
(1, 'mateo.silva', 'mateo.silva@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mateo', 'Silva Navarro', '1750567893', '0991234580', 1),
(1, 'isabella.rojas', 'isabella.rojas@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Isabella', 'Rojas Pérez', '1750567894', '0991234581', 1),
(1, 'sebastian.cruz', 'sebastian.cruz@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sebastián', 'Cruz Méndez', '1750567895', '0991234582', 1),
(1, 'camila.ramos', 'camila.ramos@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Camila', 'Ramos Flores', '1750567896', '0991234583', 1),
(1, 'nicolas.vargas', 'nicolas.vargas@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nicolás', 'Vargas Castro', '1750567897', '0991234584', 1),
(1, 'martina.gil', 'martina.gil@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Martina', 'Gil Núñez', '1750567898', '0991234585', 1),
(1, 'lucas.medina', 'lucas.medina@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lucas', 'Medina Ortega', '1750567899', '0991234586', 1),
(1, 'emma.santos', 'emma.santos@estudiante.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Emma', 'Santos Molina', '1750567900', '0991234587', 1);

-- Representantes (5)
INSERT INTO users (institution_id, username, email, password, first_name, last_name, dni, phone, is_active) VALUES
(1, 'rep.castro', 'lcastro@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Laura', 'Castro Vega', '1752345678', '0991234588', 1),
(1, 'rep.herrera', 'jherrera@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jorge', 'Herrera Palacios', '1752345679', '0991234589', 1),
(1, 'rep.lopez', 'mlopez@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'María', 'López Benítez', '1752345680', '0991234590', 1),
(1, 'rep.silva', 'psilva@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pedro', 'Silva Moreno', '1752345681', '0991234591', 1),
(1, 'rep.rojas', 'crojas@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carolina', 'Rojas Delgado', '1752345682', '0991234592', 1);

-- Inspector
INSERT INTO users (institution_id, username, email, password, first_name, last_name, dni, phone, is_active) VALUES
(1, 'inspector', 'inspector@uep.edu.ec', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'José', 'Ramírez Campos', '1753456789', '0991234593', 1);

-- =====================================================
-- ASIGNAR ROLES
-- =====================================================

-- Admin (autoridad)
INSERT INTO user_roles (user_id, role_id) VALUES (1, 4);

-- Docentes
INSERT INTO user_roles (user_id, role_id) VALUES
(2, 1), (3, 1), (4, 1), (5, 1), (6, 1), (7, 1);

-- Estudiantes
INSERT INTO user_roles (user_id, role_id) VALUES
(8, 2), (9, 2), (10, 2), (11, 2), (12, 2), (13, 2), (14, 2), (15, 2),
(16, 2), (17, 2), (18, 2), (19, 2), (20, 2), (21, 2), (22, 2);

-- Representantes
INSERT INTO user_roles (user_id, role_id) VALUES
(23, 5), (24, 5), (25, 5), (26, 5), (27, 5);

-- Inspector
INSERT INTO user_roles (user_id, role_id) VALUES (28, 3);

-- =====================================================
-- ASIGNATURAS
-- =====================================================

INSERT INTO subjects (institution_id, name, code) VALUES
(1, 'Matemáticas', 'MAT'),
(1, 'Lengua y Literatura', 'LEN'),
(1, 'Ciencias Naturales', 'CCN'),
(1, 'Estudios Sociales', 'ESS'),
(1, 'Inglés', 'ING'),
(1, 'Educación Física', 'EDF'),
(1, 'Arte y Cultura', 'ART'),
(1, 'Informática', 'INF');

-- =====================================================
-- CURSOS
-- =====================================================

INSERT INTO courses (institution_id, school_year_id, name, grade_level, parallel, shift_id) VALUES
(1, 1, 'Octavo A', 'Octavo', 'A', 1),
(1, 1, 'Séptimo A', 'Séptimo', 'A', 1),
(1, 1, 'Octavo B', 'Octavo', 'B', 1),
(1, 1, 'Noveno A', 'Noveno', 'A', 2),
(1, 1, 'Décimo A', 'Décimo', 'A', 2);

-- =====================================================
-- MATRICULAR ESTUDIANTES
-- =====================================================

-- Octavo A (curso 1)
INSERT INTO course_students (student_id, course_id, school_year_id, enrollment_date) VALUES
(8, 1, 1, '2025-09-01'),
(9, 1, 1, '2025-09-01'),
(10, 1, 1, '2025-09-01'),
(11, 1, 1, '2025-09-01');

-- Séptimo A (curso 2)
INSERT INTO course_students (student_id, course_id, school_year_id, enrollment_date) VALUES
(12, 2, 1, '2025-09-01'),
(13, 2, 1, '2025-09-01'),
(14, 2, 1, '2025-09-01');

-- Octavo B (curso 3)
INSERT INTO course_students (student_id, course_id, school_year_id, enrollment_date) VALUES
(15, 3, 1, '2025-09-01'),
(16, 3, 1, '2025-09-01'),
(17, 3, 1, '2025-09-01');

-- Noveno A (curso 4)
INSERT INTO course_students (student_id, course_id, school_year_id, enrollment_date) VALUES
(18, 4, 1, '2025-09-01'),
(19, 4, 1, '2025-09-01'),
(20, 4, 1, '2025-09-01');

-- Décimo A (curso 5)
INSERT INTO course_students (student_id, course_id, school_year_id, enrollment_date) VALUES
(21, 5, 1, '2025-09-01'),
(22, 5, 1, '2025-09-01');

-- =====================================================
-- ASIGNACIONES DOCENTES
-- =====================================================

-- Profesor García (2) - Matemáticas, TUTOR Octavo A
INSERT INTO teacher_assignments (teacher_id, course_id, subject_id, school_year_id, is_tutor) VALUES
(2, 1, 1, 1, 1),
(2, 2, 1, 1, 0),
(2, 3, 1, 1, 0);

-- Profesora Martínez (3) - Lengua, TUTORA Séptimo A
INSERT INTO teacher_assignments (teacher_id, course_id, subject_id, school_year_id, is_tutor) VALUES
(3, 1, 2, 1, 0),
(3, 2, 2, 1, 1),
(3, 4, 2, 1, 0);

-- Profesor Rodríguez (4) - Ciencias, TUTOR Octavo B
INSERT INTO teacher_assignments (teacher_id, course_id, subject_id, school_year_id, is_tutor) VALUES
(4, 1, 3, 1, 0),
(4, 3, 3, 1, 1),
(4, 5, 3, 1, 0);

-- Profesora Fernández (5) - Estudios Sociales, TUTORA Noveno A
INSERT INTO teacher_assignments (teacher_id, course_id, subject_id, school_year_id, is_tutor) VALUES
(5, 2, 4, 1, 0),
(5, 3, 4, 1, 0),
(5, 4, 4, 1, 1);

-- Profesor Gómez (6) - Inglés, TUTOR Décimo A
INSERT INTO teacher_assignments (teacher_id, course_id, subject_id, school_year_id, is_tutor) VALUES
(6, 1, 5, 1, 0),
(6, 2, 5, 1, 0),
(6, 4, 5, 1, 0),
(6, 5, 5, 1, 1);

-- Profesora Díaz (7) - Educación Física
INSERT INTO teacher_assignments (teacher_id, course_id, subject_id, school_year_id, is_tutor) VALUES
(7, 1, 6, 1, 0),
(7, 3, 6, 1, 0),
(7, 5, 6, 1, 0);

-- =====================================================
-- REPRESENTANTES
-- =====================================================

INSERT INTO representatives (representative_id, student_id, relationship, is_primary) VALUES
(23, 12, 'Madre', 1),
(23, 13, 'Madre', 1),
(24, 14, 'Padre', 1),
(25, 15, 'Madre', 1),
(25, 16, 'Madre', 1),
(26, 17, 'Padre', 1),
(26, 18, 'Padre', 1),
(27, 19, 'Madre', 1),
(27, 20, 'Madre', 1),
(27, 21, 'Madre', 1);

-- =====================================================
-- ASISTENCIAS DE PRUEBA
-- =====================================================

-- Semana pasada - Octavo A - Matemáticas
INSERT INTO attendances (student_id, course_id, subject_id, teacher_id, school_year_id, shift_id, date, hour_period, status, observation) VALUES
-- Lunes
(8, 1, 1, 2, 1, 1, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '1ra hora', 'presente', ''),
(9, 1, 1, 2, 1, 1, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '1ra hora', 'presente', ''),
(10, 1, 1, 2, 1, 1, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '1ra hora', 'ausente', 'Sin justificación'),
(11, 1, 1, 2, 1, 1, DATE_SUB(CURDATE(), INTERVAL 4 DAY), '1ra hora', 'presente', ''),
-- Martes
(8, 1, 1, 2, 1, 1, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '1ra hora', 'tardanza', 'Llegó 10 min tarde'),
(9, 1, 1, 2, 1, 1, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '1ra hora', 'presente', ''),
(10, 1, 1, 2, 1, 1, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '1ra hora', 'justificado', 'Cita médica'),
(11, 1, 1, 2, 1, 1, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '1ra hora', 'presente', ''),
-- Miércoles
(8, 1, 1, 2, 1, 1, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '1ra hora', 'presente', ''),
(9, 1, 1, 2, 1, 1, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '1ra hora', 'presente', ''),
(10, 1, 1, 2, 1, 1, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '1ra hora', 'presente', ''),
(11, 1, 1, 2, 1, 1, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '1ra hora', 'ausente', 'Enfermo'),
-- Jueves
(8, 1, 1, 2, 1, 1, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '1ra hora', 'presente', ''),
(9, 1, 1, 2, 1, 1, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '1ra hora', 'presente', ''),
(10, 1, 1, 2, 1, 1, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '1ra hora', 'presente', ''),
(11, 1, 1, 2, 1, 1, DATE_SUB(CURDATE(), INTERVAL 1 DAY), '1ra hora', 'tardanza', 'Tráfico'),
-- Hoy
(8, 1, 1, 2, 1, 1, CURDATE(), '1ra hora', 'presente', ''),
(9, 1, 1, 2, 1, 1, CURDATE(), '1ra hora', 'presente', ''),
(10, 1, 1, 2, 1, 1, CURDATE(), '1ra hora', 'presente', ''),
(11, 1, 1, 2, 1, 1, CURDATE(), '1ra hora', 'presente', '');

-- Lengua y Literatura - Octavo A
INSERT INTO attendances (student_id, course_id, subject_id, teacher_id, school_year_id, shift_id, date, hour_period, status, observation) VALUES
(8, 1, 2, 3, 1, 1, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '2da hora', 'presente', ''),
(9, 1, 2, 3, 1, 1, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '2da hora', 'presente', 'Participación activa'),
(10, 1, 2, 3, 1, 1, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '2da hora', 'ausente', ''),
(11, 1, 2, 3, 1, 1, DATE_SUB(CURDATE(), INTERVAL 2 DAY), '2da hora', 'presente', '');

-- =====================================================
-- ESTADÍSTICAS FINALES
-- =====================================================

SELECT 'BASE DE DATOS CREADA EXITOSAMENTE' as '✅';
SELECT '' as '';
SELECT 'RESUMEN:' as '';
SELECT COUNT(*) as 'Total Usuarios' FROM users;
SELECT COUNT(*) as 'Docentes' FROM user_roles WHERE role_id = 1;
SELECT COUNT(*) as 'Estudiantes' FROM user_roles WHERE role_id = 2;
SELECT COUNT(*) as 'Inspectores' FROM user_roles WHERE role_id = 3;
SELECT COUNT(*) as 'Autoridades' FROM user_roles WHERE role_id = 4;
SELECT COUNT(*) as 'Representantes' FROM user_roles WHERE role_id = 5;
SELECT COUNT(*) as 'Cursos' FROM courses;
SELECT COUNT(*) as 'Asignaturas' FROM subjects;
SELECT COUNT(*) as 'Matrículas' FROM course_students;
SELECT COUNT(*) as 'Asignaciones Docentes' FROM teacher_assignments;
SELECT COUNT(*) as 'Relaciones Rep-Estudiante' FROM representatives;
SELECT COUNT(*) as 'Registros Asistencia' FROM attendances;

SELECT '' as '';
SELECT 'CREDENCIALES DE ACCESO:' as '';
SELECT 'Usuario: admin | Contraseña: password' as 'Administrador';
SELECT 'Usuario: prof.garcia | Contraseña: password' as 'Docente';
SELECT 'Usuario: juan.perez | Contraseña: password' as 'Estudiante';
SELECT 'Usuario: rep.castro | Contraseña: password' as 'Representante';
SELECT 'Usuario: inspector | Contraseña: password' as 'Inspector';