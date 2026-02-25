Tengo un problema, hay 2 tipos de faltas, una que el estudiante falta por horas de clase y otra que haya faltado todo el dia, como podemos definir ese aspecto.

Falta Ver logs de actividad del sistema

Quiero póder importar desde excel o csv los datos de los estudiantes, docentes, asignaturas, etc

Al registar un docente se deberia tomar en cuenta su titulo y experienca.

Generación de Reportes y Calendario de Asistencia en el menú seleccinar curso aparece 1.° BT &quote;Aquote; Informática (Soporte Técnico) - Matutina - Matutina

Popup de Notificaciones al dar clic en limpiar quiero el que estamos trabajando en los otros modulos Eliminar todas las notificaciones Leídas?

en Configiración academica en la sección Cursos registrados mostrar el filtro que hay en 📚
Asignaciones Docente — Materia y eliminar este modulo por que está de mas

ALTER TABLE course_subjects
ADD COLUMN hours_per_week INT NOT NULL DEFAULT 1;
ALTER TABLE institutions 
ADD COLUMN working_days_list VARCHAR(100) DEFAULT '["lunes","martes","miercoles","jueves","viernes"]';
ALTER TABLE `class_schedule` 
MODIFY COLUMN `teacher_id` INT NULL;
-- Actualizar todos los horarios con el docente actual de teacher_assignments
UPDATE class_schedule cs
INNER JOIN teacher_assignments ta 
    ON cs.course_id = ta.course_id 
    AND cs.subject_id = ta.subject_id 
    AND cs.school_year_id = ta.school_year_id
    AND ta.is_tutor = 0
SET cs.teacher_id = ta.teacher_id
WHERE cs.teacher_id != ta.teacher_id 
   OR cs.teacher_id IS NULL;

Total horas asignadas: 60 de 35 disponibles (7 horas/día × 5 días)

No es obligatorio el correo

Corregir mensaje y fecha actualizar asistencia: 
⚠ Importante:
Puede registrar asistencia de hoy o corregir registros anteriores hasta el
27/02/2026

Inspector no puede revisar Justificaciones pero si el docente y deberian poder justicar los 2
Como tutor no puedo justificar las faltas, me aparece la notificación pero no puedo accerder a justificar

Como representante no hay el menú para justificar pero si puede ver la asistencia

Como inspector no tiene el menú para acceder a reportes y estadisticas y tambien mis justificaciones

En el calendario deberia de lunes a viernes no sabado y domingo