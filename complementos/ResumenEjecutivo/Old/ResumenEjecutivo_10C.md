📋 RESUMEN EJECUTIVO CONSOLIDADO — ECUASIST 2026

Fecha: 23 Feb 2026
Estado: Sistema funcional – Producción estable
Enfoque: Estabilidad, mejora UX y corrección de errores críticos

🏗️ Arquitectura del Proyecto
Patrón de Base de Datos
// Instanciación estándar en el proyecto
$db = new Database();
$this->model = new Model($db);

// Para queries directas en controllers:
$db = (new Database())->getConnection(); // retorna PDO
Sistema de Rutas

Rutas activas para representantes en public/index.php:

manage_representatives          → manageRepresentatives()
remove_representative           → removeRelation()
toggle_primary_representative   → togglePrimary()
edit_representative             → editRelation()

Sistema de detección automática Local / Producción.
Ya no es necesario modificar rutas al desplegar.

🎯 MEJORAS FUNCIONALES IMPLEMENTADAS
👨‍👩‍👧 Gestión de Representantes
🔒 Validación de Parentesco Exclusivo

Un estudiante no puede tener dos representantes con el mismo parentesco (Padre o Madre).

Validación en doble capa:

Modelo PHP

Triggers en base de datos

Corrección del conflicto con ON DUPLICATE KEY UPDATE.

🔄 Toggle Principal / Secundario

Cambio dinámico desde botón por fila.

Al marcar uno como Principal, los demás pasan automáticamente a Secundario.

El botón muestra la acción inversa al estado actual.

✏️ Edición Directa de Relación

Botón de edición (ícono lápiz).

Modal precargado con parentesco y tipo.

Permite corregir sin eliminar y volver a registrar.

🔍 Filtros y Búsqueda Mejorada

Búsqueda sin tildes mediante función norm().

Filtro de Curso convertido a <select> dinámico.

Conservación de nombres originales en atributos data-*.

📋 Optimización del Flujo

Orden lógico del formulario:

Representante

Parentesco

Estudiante

📅 Gestión de Horarios
🧩 Rediseño Completo

Panel horizontal superior de materias.

Columna “Hora” fija en móvil (position: sticky).

Asignación por Drag & Drop + clic.

Contador visual: horas asignadas vs configuradas.

🐞 Correcciones Críticas

Corrección en orden de creación del chip.

confirmAsgn() guardaba antes de cerrar modal.

Inclusión de hours_per_week en ScheduleController.

start_time y end_time ahora permiten NULL.

👤 Gestión de Usuarios
➕ Crear Usuario

Header visual azul.

Campo Pasaporte visible al marcar “Extranjero”.

Persistencia de datos tras error de validación.

🏫 Configuración Institucional
🛠 Correcciones Técnicas

Eliminado PHP embebido dentro de style="" (pantalla blanca).

Variables calculadas antes del foreach.

Corrección de referencia a clase InstitutionShift.

🔔 MEJORAS UX GLOBALES
✅ Toast Notifications

Reemplazo de alert() por notificaciones flotantes.

Desaparecen automáticamente (4 segundos).

Limpieza automática de URL tras mostrarse.

✅ Persistencia de Formularios

Conservación de datos cuando hay errores de validación.

🧹 DEPURACIÓN Y LIMPIEZA

Eliminado módulo Docente–Materia del menú.

Vistas pendientes de eliminación manual:

views/assignments/index.php
views/assignments/view_course.php
🗄️ CAMBIOS EN BASE DE DATOS
ALTER TABLE class_schedule 
MODIFY start_time TIME NULL DEFAULT NULL;

ALTER TABLE class_schedule 
MODIFY end_time TIME NULL DEFAULT NULL;

ALTER TABLE institutions 
ADD COLUMN working_days_list VARCHAR(100)
DEFAULT '["lunes","martes","miercoles","jueves","viernes"]';

ALTER TABLE course_subjects 
ADD COLUMN hours_per_week 
TINYINT UNSIGNED NOT NULL DEFAULT 1;
📁 ARCHIVOS MODIFICADOS
public/index.php
config/env.php

controllers/RepresentativeController.php
controllers/AcademicController.php
controllers/ScheduleController.php

models/InstitutionShift.php

views/representatives/manage.php
views/schedule/manage.php
views/users/create.php
views/academic/course_students.php
views/academic/index.php
views/institution/index.php
🚀 PRÓXIMAS MEJORAS RECOMENDADAS

Dashboard con métricas y gráficos (Chart.js)

Breadcrumbs globales

Sistema de notificaciones usando tabla notifications

Búsqueda global en navbar

Validación completa del flujo:

Horarios → Asistencia → Justificaciones → Reportes