# 📋 RESUMEN EJECUTIVO CONSOLIDADO — ECUASIST 2026

**Fecha:** 26 Feb 2026  
**Estado:** Sistema funcional – Producción estable  
**Enfoque:** Estabilidad, validaciones robustas, mejora UX y corrección de errores críticos  

---

# 🏗️ Arquitectura del Proyecto

## Patrón de Base de Datos

```php
// Instanciación estándar en el proyecto
$db = new Database();
$this->model = new Model($db);

// Para queries directas en controllers:
$db = (new Database())->getConnection(); // retorna PDO

Uso consistente de inyección de dependencia simple.

Separación clara Controller / Model.

PDO como capa única de conexión.

🔀 Sistema de Rutas
Rutas activas (Representantes)
manage_representatives          → manageRepresentatives()
remove_representative           → removeRelation()
toggle_primary_representative   → togglePrimary()
edit_representative             → editRelation()
assign_from_academic            → assignFromAcademic()
remove_from_academic            → removeFromAcademic()

Sistema de detección automática Local / Producción.

Ya no es necesario modificar rutas al desplegar.

🎯 MEJORAS FUNCIONALES IMPLEMENTADAS
👥 Gestión de Usuarios
✅ Validación Inteligente Cédula / Pasaporte
Auto-detección

Solo números → Cédula

Alfanumérico → Pasaporte

Cédula (Ecuador)

Validación completa con algoritmo Módulo 10:

Provincia válida (01–24)

Tercer dígito < 6

Dígito verificador correcto

Cédula inválida → se guarda con advertencia ⚠ (no bloquea)

Duplicados → bloqueados (error duro)

Pasaporte

Solo A-Z y 0-9

Entre 4 y 12 caracteres

Auto-uppercase

Badge 🌐 en tabla

Badges Visuales en Tabla

✓ Verde → válida

⚠ Amarillo → no verificada

🌐 Azul → pasaporte

Backend
User::findByDni($dni, $excludeId)

Valida unicidad incluso en edición.

🪟 Modales Crear / Editar Usuario

Reemplazo de páginas separadas por modales inline

Persistencia en $_SESSION tras error

Reapertura automática del modal si hay validación fallida

Validación teléfono:

Celular: 09XXXXXXXX

Fijo: 0[2-7]XXXXXXX

🔐 Protección del Administrador

No se puede eliminar usuario con rol administrador

Validación en controller (no solo en vista)

Toast:
✗ El usuario administrador no puede ser eliminado

🔎 Preservación Inteligente de Filtros

Crear → mantiene filtro si coincide con rol

Editar / Eliminar / Desactivar → conserva filtro activo

Redirecciones limpias

👨‍👩‍👧 Gestión de Representantes
🔒 Validación de Parentesco Exclusivo

Un estudiante no puede tener dos representantes con el mismo parentesco (Padre/Madre).

Validación en doble capa:

Modelo PHP

Trigger en base de datos

✔ Corrección del conflicto con ON DUPLICATE KEY UPDATE.

🔄 Toggle Principal / Secundario

Cambio dinámico por fila

Al marcar uno como Principal:

Los demás pasan automáticamente a Secundario

Botón muestra acción inversa

✏️ Edición Directa de Relación

Ícono lápiz

Modal precargado

Permite corregir sin eliminar

🔧 Fix Error 500

Métodos agregados:

assignFromAcademic()

removeFromAcademic()

Validaciones defensivas con ??

Verificación de campos obligatorios

⚙️ Configuración Académica
📊 Control de Horas Semanales por Curso
Badge Dinámico

ℹ 12/35 hrs → Disponible (amarillo)

✓ 35/35 hrs → Completo exacto (verde)

⚠ 60/35 hrs → Excedido (rojo)

Tooltip:

7 horas/día × 5 días = 35 horas máximas
Funcionalidad en Tiempo Real

Actualiza sin recargar página

Input en rojo si excede

Bloquea guardado si supera máximo

Toast descriptivo indicando horas restantes

➕ Modal Agregar Asignatura — Código Automático

Generación automática:

1 palabra → primeras 3 letras
Matemática → MAT

Varias palabras → iniciales significativas
Lengua y Literatura → LL

Palabras ignoradas:

y, e, o, a, de, del, la, las, el, los, en, con,
por, para, sin, al, un, una, que, se

Convierte a MAYÚSCULAS

Editable manualmente

🧹 Eliminación en Cascada Correcta

removeCourseSubject() ahora limpia en orden:

class_schedule

teacher_assignments

course_subjects

📅 Acceso Directo al Horario

Nuevo botón 📅 Horario por curso → manage_schedule

🗓️ Módulo Horario
🧩 Rediseño Completo

Panel horizontal superior de materias

Columna “Hora” fija en móvil (position: sticky)

Drag & Drop + clic

Contador visual: asignadas vs configuradas

🔄 Movimiento e Intercambio de Clases
Arrastrar a celda vacía

→ mueve clase
Endpoint: move_schedule_class

Arrastrar sobre celda ocupada

→ intercambia clases
Endpoint: swap_schedule_class

Toast de confirmación

Recarga automática

🐞 Correcciones Críticas

Corrección orden de creación del chip

confirmAsgn() guardaba antes de cerrar modal

Inclusión hours_per_week en ScheduleController

start_time y end_time ahora permiten NULL

Eliminado banner redundante de horas

🔔 Sistema Global de Notificaciones
✅ Toast Notifications

Reemplazo total de:

alert()

confirm() nativo

Características:

Esquina superior derecha

Verde (ok), Rojo (error), Azul (info)

Auto-desaparece (4s)

Limpieza de URL con history.replaceState

Cobertura total del sistema

🪟 Modal de Confirmación Propio

Reemplaza confirm() en:

Eliminar usuario

Quitar rol

Quitar docente

Eliminaciones académicas

🔗 Navbar

Eliminada línea blanca en dropdown

Corregido cierre por gap hover

Pseudo-elemento puente invisible

🏫 Configuración Institucional
Correcciones Técnicas

Eliminado PHP embebido dentro de style=""

Variables calculadas antes del foreach

Corrección referencia clase InstitutionShift

🔍 Búsquedas y Filtros

Búsqueda sin tildes con norm()

<select> dinámico para curso

Conservación de nombres en atributos data-*

Persistencia tras POST

🗄️ Cambios en Base de Datos
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
🧹 Depuración y Limpieza

Eliminado módulo Docente–Materia del menú

Vistas pendientes de eliminación manual:

views/assignments/index.php
views/assignments/view_course.php
📁 Archivos Modificados
Controllers

UserController.php

AcademicController.php

RepresentativeController.php

ScheduleController.php

public/index.php

Models

User.php

InstitutionShift.php

Views

users/index.php

users/create.php

representatives/manage.php

academic/index.php

academic/course_students.php

schedule/manage.php

institution/index.php

partials/navbar.php

⚠️ Acciones Pendientes en Servidor

Pegar métodos nuevos dentro de ScheduleController.php

Verificar redirecciones correctas en AcademicController

Confirmar nombre exacto del rol 'administrador' en base de datos

🚀 Próximas Mejoras Recomendadas

Dashboard con métricas y gráficos (Chart.js)

Breadcrumbs globales

Tabla notifications

Búsqueda global en navbar

Validación completa del flujo:

Horarios → Asistencia → Justificaciones → Reportes
✅ Estado General del Sistema

✔ Producción estable
✔ Validaciones robustas en doble capa
✔ UX moderna con modales y toasts
✔ Protección de integridad académica
✔ Horario interactivo con drag & drop
✔ Control estricto de horas semanales

ECUASIST 2026 — Plataforma Académica en Producción


