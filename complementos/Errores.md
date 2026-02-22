Tengo un problema, hay 2 tipos de faltas, una que el estudiante falta por horas de clase y otra que haya faltado todo el dia, como podemos definir ese aspecto.

👥 Asignar Estudiante a Representante me parece que esta mal, seleccionar representate, estudiante y parentezco por que si hay miles de estudiantes va ha ser tedioso, promero sera de selecionar un curso, luego un estudiantes y asignar quen va ha ser su representante o algo asi.

Falta Ver logs de actividad del sistema

Quiero póder importar desde excel o csv los datos de los estudiantes, docentes, asignaturas, etc

Al registar un docente se deberia tomar en cuenta su titulo y experienca.

Generación de Reportes y Calendario de Asistencia en el menú seleccinar curso aparece 1.° BT &quote;Aquote; Informática (Soporte Técnico) - Matutina - Matutina

Popup de Notificaciones al dar clic en limpiar quiero el que estamos trabajando en los otros modulos Eliminar todas las notificaciones Leídas?

en Configiración academica en la sección Cursos registrados mostrar el filtro que hay en 📚
Asignaciones Docente — Materia y eliminar este modulo por que está de mas

ALTER TABLE course_subjects
ADD COLUMN hours_per_week INT NOT NULL DEFAULT 1;

Estoy en gestion académica -> Cursos registrados -> Acciones estudiantes -> en Matricular Estudiantes Disponibles ya no deberia pedirme que seleccione el curso ya que debe venir de la tabla anterior

Ahora en las acciones de Cursos Registrados sale estudiantes y Matricular, me parece que esta de mas uno de los 2, por que primero deberia salir el listado de los estudiantes y un boton para matricular usando un modal , que opinas de mi idea?

quiero eliminar esta vista http://localhost/ecuasistencia2026/public/?action=enroll_students&course_id=6 y el boton Matricular de acciones en Cursos Registrados

En el public/index.php estoy trabajando con esto para identificar si estoy en local o producción

if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1') {
    require_once __DIR__ . '/../config/config.php';  // local
} else {
    require_once '/home/ecuasysc/ecuasistencia/config/config.php';  // producción
}

4. Matrícula (views/academic/course_students.php + AcademicController.php)

Vista unificada: lista matriculados + modal para matricular nuevos
Modal con buscador, "seleccionar todos", contador
Botón "✕ Retirar" por estudiante con modal de confirmación
Eliminada vista separada enroll_students
Eliminado botón "Matricular" de acciones de cursos