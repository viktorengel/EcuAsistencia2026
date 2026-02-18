📋 RESUMEN EJECUTIVO — ECUASIST 2026 (Sesión Actual)
Versión: v1.6 | Fecha: 17 Feb 2026

🔧 CORRECCIONES APLICADAS EN ESTA SESIÓN
Tablas creadas en BD:
sql-- Ejecutadas en phpMyAdmin
CREATE TABLE institution_shifts (...)
CREATE TABLE class_schedule (...)
ALTER TABLE institutions ADD COLUMN province, city, director_name, amie_code, website, logo_path
Bugs corregidos:

&quot; en nombres de cursos → html_entity_decode() en createCourse() y editCourse() del AcademicController
&quot; en BD existente → UPDATE courses SET name = REPLACE(name, '&quot;', '"')
Jornada duplicada en Asignar Docente → eliminado - <?= $course['shift_name'] ?> en assignments/index.php
onclick roto por comillas → uso de json_encode con comillas simples en atributo HTML
reviewed_justifications sin require_once en index.php


📁 ARCHIVOS MODIFICADOS EN ESTA SESIÓN
ArchivoCambiocontrollers/AcademicController.phphtml_entity_decode en create y edit cursocontrollers/DashboardController.phpgetTutorCourse() agregadocontrollers/JustificationController.phpMétodo reviewed() agregadocontrollers/AttendanceController.phpgetExistingAttendance() agregadomodels/Justification.phpgetReviewed() agregadomodels/ClassSchedule.phpZona horaria Ecuador en getCurrentDayName()views/academic/index.phpNiveles Ecuador completos + nocturna condicionalviews/academic/course_edit.phpReescrito completo con nuevos nivelesviews/justifications/reviewed.phpVista nueva creadaviews/dashboard/index.phpCard tutor docenteviews/attendance/register.phpFix onclick + precargar estados existentespublic/index.phpRutas: reviewed_justifications, get_existing_attendance

✅ FUNCIONALIDADES IMPLEMENTADAS

Inspector ve justificaciones aprobadas/rechazadas con filtros
Dashboard docente muestra curso del que es tutor
Crear curso con estructura educativa ecuatoriana completa (Inicial, EGB, BGU, BT)
Bachillerato Técnico con figura profesional y especialidad
Jornada nocturna solo para 8º-10º EGB y bachillerato
Registro de asistencia precarga estados ya guardados
Editar curso precarga figura profesional y carrera


🔄 PENDIENTE / PRÓXIMOS PASOS

Verificar que getExistingAttendance() funcione correctamente
Revertir código de diagnóstico en register.php (el catch temporal)
Notificaciones toast en lugar de alert()
Gráficos en estadísticas
Breadcrumbs


⚙️ CONFIGURACIÓN IMPORTANTE
php// /config/config.php o /public/index.php
date_default_timezone_set('America/Guayaquil');
Credenciales prueba: prof.diaz / password
URL: http://localhost/EcuAsistencia2026/public/