Quiero que el contenido de este archivo resumen Ejecutivo lo consolides con todos los avances y mejoras que se hayan realizado sin repetir, dame en formato markdown listo para guardar

Sistema de Gestión de Representantes — Resumen de Cambios

🔒 Validación de parentesco exclusivo
Un estudiante no puede tener dos representantes con el mismo parentesco Padre o Madre. La validación opera en dos capas: modelo PHP y triggers en base de datos. Se corrigió además el bug del ON DUPLICATE KEY UPDATE que saltaba la validación.

🔄 Toggle Principal / Secundario
Botón en cada fila para cambiar el tipo de representante. Al marcar uno como Principal, los demás del mismo estudiante pasan automáticamente a Secundario. El botón muestra la acción opuesta al estado actual.

✏️ Editar relación
Se agregó botón de lápiz junto a la X de eliminar. Abre un modal con parentesco y tipo precargados para corregir sin necesidad de eliminar y volver a crear.

📋 Orden del formulario
Se reordenaron los campos a: Representante → Parentesco → Estudiante, que es el flujo más natural al registrar.

🔔 Sistema de Toast
Se reemplazaron los mensajes alert estáticos por notificaciones flotantes en la esquina superior derecha que desaparecen solos a los 4 segundos. La URL se limpia después de mostrar el toast para evitar que se repita al recargar.

📝 Formulario conserva datos en error
Cuando hay un error de validación, el formulario mantiene los valores seleccionados para no tener que volver a llenarlos.

🗑️ Eliminación de Asignaciones Docente-Materia
Se removió el ítem del menú en desktop y móvil. Las vistas a eliminar manualmente son views/assignments/index.php y views/assignments/view_course.php.

🔧 Rutas y configuración
Se corrigió el sistema de rutas para local y producción usando detección automática en env.php y index.php, eliminando la necesidad de cambiar rutas al desplegar.

📋 RESUMEN EJECUTIVO — ECUASIST 2026 (Sesión Actual)
🎯 ESTADO: v1.7 — Sistema funcional con mejoras UX

🔧 CAMBIOS DE ESTA SESIÓN
1. Horario (views/schedule/manage.php)

Materias en panel superior horizontal (no lateral)
Columna "Hora" fija en móvil (position:sticky)
Drag & drop + click para asignar
Contador horas asignadas vs configuradas
Bug fix: orden de creación del chip corregido
Bug fix: confirmAsgn() guardaba asgnTarget antes de closeAsgn()
Bug fix: hours_per_week ahora incluido en query del ScheduleController

2. Representantes (views/representatives/manage.php)

Búsqueda sin tildes: función norm() con mapa explícito de caracteres
Filtro Curso cambiado de <input> a <select> poblado desde el DOM
data-repname y data-student guardan texto original (sin strtolower)

3. Crear Usuario (views/users/create.php)

Header azul con título agregado
Campo pasaporte visible cuando checkbox "Extranjero" está marcado
Estado restaurado al recargar con errores de validación

5. Configuración Institución (views/institution/index.php)

Bug fix: PHP dentro de style="" causaba pantalla blanca
Variables PHP calculadas antes del foreach

6. Bugs producción resueltos

InstitutionShift.php: clase llamada Institution en lugar de InstitutionShift
index.php: detección automática local/producción
class_schedule.start_time: ALTER TABLE ... MODIFY ... NULL DEFAULT NULL
set_subject_hours: faltaba require_once AcademicController


📁 ARCHIVOS MODIFICADOS EN ESTA SESIÓN
public/index.php                          ← detección local/producción, rutas
views/schedule/manage.php                 ← horario completo reescrito
views/representatives/manage.php          ← filtros con norm()
views/users/create.php                    ← header + pasaporte
views/academic/course_students.php        ← vista unificada con modales
views/academic/index.php                  ← sin botón Matricular
views/institution/index.php               ← bug fix PHP en style
controllers/AcademicController.php        ← viewCourseStudents ampliado
controllers/ScheduleController.php        ← hours_per_week en query
models/InstitutionShift.php               ← clase renombrada
config/env.php                            ← rutas local/producción

⚙️ CONFIGURACIÓN PRODUCCIÓN
BASE_PATH: /home/ecuasysc/ecuasistencia
BASE_URL:  https://www.ecuasys.com
index.php: /home/ecuasysc/public_html/index.php
🗄️ SQL EJECUTADO EN PRODUCCIÓN
sqlALTER TABLE class_schedule MODIFY start_time TIME NULL DEFAULT NULL;
ALTER TABLE class_schedule MODIFY end_time TIME NULL DEFAULT NULL;
ALTER TABLE institutions ADD COLUMN IF NOT EXISTS working_days_list VARCHAR(100) DEFAULT '["lunes","martes","miercoles","jueves","viernes"]';
ALTER TABLE course_subjects ADD COLUMN IF NOT EXISTS hours_per_week TINYINT UNSIGNED NOT NULL DEFAULT 1;
🚀 PRÓXIMOS PASOS SUGERIDOS

Breadcrumbs en todas las vistas
Notificaciones toast globales
Gráficos en dashboard y reportes
Búsqueda global en navbar