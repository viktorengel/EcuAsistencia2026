📋 RESUMEN EJECUTIVO CONSOLIDADO — ECUASIST 2026
Sistema de Gestión Académica

Versión: v1.7
Estado: Funcional – Producción estable
Enfoque: Estabilidad + Mejora UX + Corrección de bugs críticos

🎯 MEJORAS FUNCIONALES IMPLEMENTADAS
👨‍👩‍👧 Gestión de Representantes
🔒 Validación de parentesco exclusivo

Un estudiante no puede tener dos representantes con el mismo parentesco (Padre o Madre).

Validación en doble capa:

Modelo PHP

Triggers en base de datos

Corrección de conflicto con ON DUPLICATE KEY UPDATE.

🔄 Toggle Principal / Secundario

Cambio dinámico desde botón en cada fila.

Al marcar uno como Principal, los demás pasan automáticamente a Secundario.

El botón muestra la acción inversa al estado actual.

✏️ Edición directa de relación

Nuevo botón de edición (ícono lápiz).

Modal precargado con parentesco y tipo.

Permite corregir sin eliminar y volver a registrar.

📋 Optimización del flujo del formulario

Orden lógico actualizado:

Representante

Parentesco

Estudiante

🔍 Filtros y búsqueda mejorada

Búsqueda sin tildes mediante función norm().

Filtro Curso convertido a <select> dinámico.

Conservación de texto original en atributos data-repname y data-student.

📅 Gestión de Horarios
🧩 Rediseño completo de la vista

Panel de materias horizontal superior.

Columna “Hora” fija en móvil (position: sticky).

Drag & Drop + clic para asignación.

Contador visual: horas asignadas vs configuradas.

🐞 Correcciones críticas

Orden de creación del chip corregido.

confirmAsgn() guardaba asgnTarget antes de closeAsgn().

Inclusión de hours_per_week en query del ScheduleController.

👤 Gestión de Usuarios
➕ Crear Usuario

Header visual azul agregado.

Campo Pasaporte visible al marcar “Extranjero”.

Restauración de estado tras error de validación.

🏫 Configuración Institucional
🛠 Correcciones técnicas

Eliminado PHP embebido dentro de style="" (causaba pantalla blanca).

Variables calculadas antes del foreach.

🔔 MEJORAS UX GLOBALES
Toast Notifications

Reemplazo de alert() por notificaciones flotantes.

Desaparecen automáticamente a los 4 segundos.

Limpieza automática de URL tras mostrarse.

Persistencia de Formularios

Conservación de datos ante errores de validación.

🧹 DEPURACIÓN Y LIMPIEZA

Eliminado módulo de asignaciones Docente–Materia del menú.

Vistas a eliminar manualmente:

views/assignments/index.php

views/assignments/view_course.php

⚙️ INFRAESTRUCTURA Y PRODUCCIÓN
🔁 Sistema de rutas inteligente

Detección automática local / producción.

Eliminada necesidad de modificar rutas al desplegar.

Ajustes en:

env.php

index.php

🐞 Bugs críticos resueltos en producción

Clase InstitutionShift mal referenciada.

start_time y end_time no permitían NULL.

Falta de require_once AcademicController en set_subject_hours.

🗄️ CAMBIOS EN BASE DE DATOS
ALTER TABLE class_schedule MODIFY start_time TIME NULL DEFAULT NULL;
ALTER TABLE class_schedule MODIFY end_time TIME NULL DEFAULT NULL;

ALTER TABLE institutions 
ADD COLUMN IF NOT EXISTS working_days_list VARCHAR(100) 
DEFAULT '["lunes","martes","miercoles","jueves","viernes"]';

ALTER TABLE course_subjects 
ADD COLUMN IF NOT EXISTS hours_per_week 
TINYINT UNSIGNED NOT NULL DEFAULT 1;
📁 ARCHIVOS MODIFICADOS
public/index.php
views/schedule/manage.php
views/representatives/manage.php
views/users/create.php
views/academic/course_students.php
views/academic/index.php
views/institution/index.php
controllers/AcademicController.php
controllers/ScheduleController.php
models/InstitutionShift.php
config/env.php
🚀 PRÓXIMAS MEJORAS RECOMENDADAS

Breadcrumbs globales

Toast centralizado para todo el sistema

Dashboard con métricas y gráficos

Búsqueda global en navbar