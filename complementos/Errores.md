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