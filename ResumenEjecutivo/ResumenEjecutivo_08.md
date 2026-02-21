EcuAsistencia 2026
Sistema de Gestión de Asistencia Escolar
Resumen Ejecutivo de Cambios y Mejoras — v1.6 | Febrero 2026

v1.6  Actual	18/18 Modulos	22 Archivos	Prod. Ready

Este documento consolida todas las mejoras, correcciones y nuevas funcionalidades implementadas en EcuAsistencia 2026. Incluye archivos entregados, rutas de destino, bugs corregidos y estado actual de cada modulo.

1. Navbar — Rediseno Completo
Archivo: views/partials/navbar.php

• Nombre: "EcuAsist 2026" actualizado a EcuAsistencia con logo "EA"
• Una sola linea: todo el menu sin overflow
• Fondo oscuro profesional #0f172a (antes azul plano)
• Dropdowns con esquinas redondeadas y sombra suave
• Avatar con iniciales del usuario en lugar de icono generico
• Tooltip al hover: muestra "Apellido, Nombre"
• Boton Salir: icono SVG de logout + texto en rojo
• Indicador de pagina activa: linea azul debajo del item
• Fuente: Plus Jakarta Sans (Google Fonts)

2. Formulario Crear Curso — Rediseno y Persistencia
Archivos: views/academic/index.php + controllers/AcademicController.php

Diseno:
• Layout en 2 columnas: Nivel+Grado en fila 1, Paralelo+Jornada en fila 2
• Campos BT (Figura/Carrera) condicionales, ocultos por defecto
• Focus highlight azul oscuro, campo nombre con fondo azul claro

Persistencia de datos al error:
• Al error: campos guardados en $_SESSION[course_form]
• Al recargar: vista pre-rellena todos los selectores
• JS restaura: nivel -> grado -> figura -> carrera -> nombre generado
• Sesion se limpia al crear el curso exitosamente

3. Base de Datos — Tabla course_subjects
• Nueva tabla para gestionar asignaturas por curso
• PK: id | FK: course_id, subject_id (ambos con CASCADE)
• UNIQUE KEY (course_id, subject_id) para evitar duplicados
• Ejecutar: archivo crear_course_subjects.sql en phpMyAdmin

4. Asignacion Docente-Materia — Nuevo Flujo
Archivos: views/academic/course_subjects.php + controllers/AcademicController.php

Flujo anterior eliminado:
• Formulario global: Docente -> Curso -> Asignatura

Flujo nuevo:
• Configuracion Academica -> Curso -> Asignaturas -> boton Asignar por fila
• Modal con dropdown de docentes, pre-selecciona el actual si existe
• Boton Quitar para desasignar con confirmacion modal
• Vista global de asignaciones: solo consulta con filtros, sin formulario

5. Matriculacion de Estudiantes — Bug Fix
• Bug: form sin action -> navegador mostraba "seleccione un elemento de la lista"
• Fix: action="?action=enroll_students" agregado explicitamente
• Navbar estandar + breadcrumb + page-header verde agregados

6. Crear Usuario — Validaciones Ecuador
Archivos: views/users/create.php + controllers/UserController.php

Frontend (JavaScript, tiempo real):
• Email: validacion en blur con icono visual y check/error
• Cedula ecuatoriana: algoritmo Registro Civil (modulo 10, coef [2,1,2,1,2,1,2,1,2])
◦ 10 digitos, solo numeros, validacion de provincia 01-24
• Toggle extranjero: muestra campo Pasaporte, oculta Cedula
• Telefono Ecuador: Celular 09XXXXXXXX / Fijo 0[2-7]XXXXXXX
• Contrasenas: validacion cruzada en tiempo real

Backend (PHP, seguridad):
• UserController::validarCedulaEcuador() — mismo algoritmo
• Validacion telefono con regex en el controlador
• Pasaporte guardado en campo dni si es extranjero

7. Logo Institucional — Correccion de Rutas
Archivos: InstitutionController.php + views/institution/index.php + img.php + env.php

Problema:
• uploads/ fuera del document root -> no accesible por URL directa
• BASE_URL hardcodeado a localhost -> roto en produccion
• Logo se borraba al guardar si no se subia nuevo archivo

Solucion:
• img.php en RAIZ del proyecto (no dentro de public/)
◦ Sin dependencias externas, usa __DIR__ puro
◦ Bloqueo de path traversal con doble verificacion realpath()
◦ Sirve JPG, PNG, GIF, WebP con Content-Type correcto
• env.php: deteccion automatica localhost vs produccion
◦ LOCAL: BASE_PATH raiz XAMPP | BASE_URL localhost/...
◦ PROD: BASE_PATH /home/ecuasysc/ecuasistencia | BASE_URL ecuasys.com
• Triple respaldo logo_path: BD -> campo hidden -> null
• mkdir con permisos 0775 para hosting compartido

8. Correcciones UX/UI Globales
Page-header estandar agregado en:
• views/tutor/course_attendance.php — reemplazo de header propio
• views/tutor/no_tutor.php — rediseno con empty-state
• views/academic/enroll.php — header verde

Container unificado a 1200px (antes 1400px):
• views/representatives/manage.php
• views/stats/index.php
• views/dashboard/index.php

Breadcrumbs agregados en:
• views/academic/enroll.php
• views/users/create.php
• views/institution/index.php

9. Archivos Entregados — Tabla Completa

Archivo	Destino	Tipo
navbar.php	views/partials/navbar.php	Modificado
index_academic.php	views/academic/index.php	Modificado
course_subjects.php	views/academic/course_subjects.php	Modificado
enroll.php	views/academic/enroll.php	Modificado
create_user.php	views/users/create.php	Modificado
users_index.php	views/users/index.php	Modificado
assignments_index.php	views/assignments/index.php	Modificado
assignments_tutor.php	views/assignments/tutor.php	Modificado
institution_index.php	views/institution/index.php	Modificado
dashboard_index.php	views/dashboard/index.php	Modificado
representatives_manage.php	views/representatives/manage.php	Modificado
stats_index.php	views/stats/index.php	Modificado
tutor_course_attendance.php	views/tutor/course_attendance.php	Modificado
no_tutor.php	views/tutor/no_tutor.php	Modificado
course_students.php	views/academic/course_students.php	Modificado
AcademicController.php	controllers/AcademicController.php	Modificado
UserController.php	controllers/UserController.php	Modificado
InstitutionController.php	controllers/InstitutionController.php	Modificado
index_router.php	public/index.php	Modificado
config.php	config/config.php	Modificado
img.php	/ (raiz del proyecto)	NUEVO
crear_course_subjects.sql	Ejecutar en phpMyAdmin	SQL BD

10. Estado Actual y Proximos Pasos

✓ Completado	⏳ Pendiente
✓ Navbar rediseñado (EcuAsistencia, 1 línea)
✓ Formulario curso en 2 columnas
✓ Persistencia de datos al error (sesión)
✓ Tabla course_subjects en BD
✓ Asignación docente desde modal en curso
✓ Fix matrícula (form action explícito)
✓ Validación cédula ecuatoriana (algoritmo)
✓ Validación teléfono Ecuador (cel/fijo)
✓ Toggle extranjero / campo pasaporte
✓ Logo institucional con img.php
✓ env.php detección local/producción
✓ Triple respaldo para logo_path
✓ Page-header estándar en todas las vistas
✓ Container unificado 1200px
✓ Breadcrumbs en vistas principales
✓ Icono SVG en botón Cerrar Sesión
✓ Tooltip usuario con Apellido, Nombre	⏳ Notificaciones toast (reemplazar divs)
⏳ Gráficos interactivos con Chart.js
⏳ Breadcrumbs en vistas restantes
⏳ Búsqueda global en navbar
⏳ Tooltips en campos de formulario
⏳ Calendario drag & drop para horarios
⏳ Modo oscuro
⏳ API REST para app móvil
⏳ Reportes con filtros avanzados
⏳ Fotos de estudiantes
⏳ Mensajería docente-representante
⏳ Panel de analítica avanzada
⏳ Integración Google Calendar
⏳ Exportación masiva de nóminas

11. Configuracion para Produccion
Estructura de archivos clave en servidor:
• img.php -> RAIZ del proyecto (ecuasistencia/)
• uploads/institution/ con permisos 775
• config/env.php con rutas reales del servidor
• config/config.php incluye env.php al inicio

Comandos de permisos (Linux):
• chmod -R 755 . (todo el proyecto)
• chmod -R 775 uploads/ backups/

Checklist deploy:
• img.php en raiz (no en public/)
• env.php con BASE_PATH y BASE_URL correctos
• Tabla course_subjects creada en BD
• SMTP configurado en config.php
• Contrasena admin cambiada

EcuAsistencia 2026  —  PHP 7.4+ OOP | MySQL | Sin frameworks
Version v1.6  |  Febrero 2026  |  Listo para produccion

📋 RESUMEN EJECUTIVO — ECUASIST 2026

Versión: v2.0
Fecha: Febrero 2026
Estado: Sistema integral consolidado — Fase de optimización productiva

🎯 ESTADO GENERAL DEL PROYECTO

Sistema integral de asistencia escolar desarrollado en PHP OOP puro + MySQL, arquitectura MVC modular, sin frameworks externos.

✅ Módulos completados: 19/19

✅ Bugs críticos: 0

✅ Diseño visual unificado

✅ Seguridad reforzada

✅ Superusuario implementado

🔄 Fase actual: Optimización avanzada y validación productiva

El sistema ya no está en construcción base. Está en etapa de refinamiento y preparación final.

🏗️ ARQUITECTURA DEL SISTEMA
🧩 Backend

Arquitectura MVC modular

Router central: public/index.php

Models especializados por módulo

Controllers independientes

Helpers: Seguridad, Correo, Respaldo

BASE_PATH activo

Control de sesiones persistentes (24h)

Timeout automático (30 min)

🗄️ BASE DE DATOS
Principales

institutions

users

roles

permissions

Académico

school_years

courses

subjects

teacher_assignments

course_students

class_schedule

institution_shifts

Asistencia

attendances

justifications

Sistema

notifications

activity_logs

representatives

Base normalizada, validaciones activas y restricciones lógicas implementadas.

🎨 REDISEÑO VISUAL COMPLETO (v2.0)

Se eliminó completamente Bootstrap y CSS por vista.
Ahora existe un sistema de diseño propio unificado.

Archivo central:

public/global.css
Componentes creados

.page-header

.panel

Sistema de botones estandarizado

Formularios unificados

Alertas consistentes

Breadcrumbs globales

Empty states visuales

Tablas estilizadas con hover

Variables CSS del sistema

Resultado:
Interfaz limpia, consistente y sin conflictos CSS.

📱 NAVBAR Y RESPONSIVE TOTAL

Navbar sticky

Menú hamburguesa móvil

Dropdowns organizados por módulo

Contador de notificaciones en tiempo real

Acceso por rol dinámico

Resaltado de sección activa

Diseño adaptable:

3 columnas desktop

2 tablet

1 móvil

🔐 SUPERUSUARIO (ADMIN GLOBAL)

Implementación completa:

Nueva columna is_superadmin

Bypass total de roles

Acceso a todos los módulos

Sesión guarda flag $_SESSION['is_superadmin']

Script SQL de limpieza y reseteo

Admin (id=1) queda como superusuario absoluto.

🏫 CONFIGURACIÓN INSTITUCIONAL AVANZADA

Campos ampliados:

Provincia

Ciudad

Director

Código AMIE

Web

Logo institucional

Jornadas

Sistema toggle AJAX

Activación/desactivación sin recarga

Corrección de error en vista

Eliminación de caché del logo con ?v=time()

📅 AÑOS LECTIVOS

Corrección crítica aplicada:

lastInsertId() ahora usa la misma conexión

Funciones:

Activación inmediata

Modal personalizado (sin confirm() nativo)

Vistas creadas correctamente

Gestión estable sin errores fatales

👨‍🏫 SISTEMA DE HORARIOS

Tabla class_schedule

Validación anti-duplicados

Detección de conflictos en tiempo real

Auto-asignación docente

Gestión visual por curso

Flujo automatizado:

Crear curso

Asignar docente

Configurar horario

Asistencia lista automáticamente

📝 REGISTRO DE ASISTENCIA INTELIGENTE

Eliminada selección manual de curso

Detecta clases del día automáticamente

Validación 48h hábiles

Actualización automática si existe registro

Precarga de estados guardados

Sin duplicados.

📌 SISTEMA DE JUSTIFICACIONES AVANZADO
Nueva lógica estructural

Nuevas columnas:

date_from

date_to

working_days

reason_type

can_approve

attendance_id nullable

Flujo inteligente

≤ 3 días → Tutor

3 días → Inspector o Autoridad

Notificación automática

Formulario rediseñado

Solo muestra días con ausencias reales

Checkboxes por día

Contador dinámico

Aviso automático de aprobador

9 causas predefinidas

Botón bloqueado hasta validación completa

Sistema mucho más sólido y realista.

👨‍🏫 MÓDULO DOCENTE TUTOR
Dashboard Tutor

Métricas completas

Barra de efectividad

Estadísticas del día

Gráfico últimos 7 días

Top 10 ausencias

Lista total de estudiantes

Búsqueda avanzada

Por nombre, apellido o cédula

Porcentaje con colores:

Verde ≥ 90%

Amarillo ≥ 75%

Rojo < 75%

Módulo completamente funcional.

👥 GESTIÓN DE USUARIOS MEJORADA

Filtro por rol con botones píldora

Colores diferenciados

Mejor experiencia visual

Accesos directos en títulos de módulos

🗄️ MÓDULO DE RESPALDOS

Interfaz completa

Detección automática de mysqldump

Validación de archivos

Eliminación individual

Limpieza automática

Pendiente menor: ruta delete_backup.

📊 REPORTES

PDF con TCPDF

Excel con PhpSpreadsheet

Datos institucionales dinámicos

Corrección entidades HTML

Eliminación de duplicidades

Nombres sanitizados

🛡️ VALIDACIONES CRÍTICAS ACTIVAS

Tutor único por curso

Materia única por curso

Horarios sin conflictos

Asistencia sin duplicados

Estudiante único por año lectivo

Jornadas controladas

Eliminaciones protegidas

Roles asegurados

🐛 BUGS CRÍTICOS CORREGIDOS

lastInsertId incorrecto

Vista accediendo a modelo directamente

Logo no actualizaba

CSS conflictivos

Confirmaciones nativas inconsistentes

Rutas faltantes

Entidades HTML mal renderizadas

Backups vacíos

Stats ocultos

Justificaciones con fechas inválidas

Actualmente: 0 bugs críticos conocidos

⚙️ CONFIGURACIÓN GENERAL

Zona horaria Ecuador

Cookies seguras

Permisos correctos en:

/uploads

/uploads/institution

/backups

Credencial prueba:

prof.diaz / password

URL local:

http://localhost/EcuAsistencia2026/public/
📦 DEPENDENCIAS

phpmailer/phpmailer

phpoffice/phpspreadsheet

tecnickcom/tcpdf

📈 ESTADO GLOBAL
Área	Estado
Arquitectura	Estable
Módulos	Completos
Seguridad	Sólida
UX/UI	Unificada
Responsive	Implementado
Base de datos	Normalizada
Optimización	En progreso
🔜 PRÓXIMOS PASOS ESTRATÉGICOS
Alta prioridad

Validar flujo completo en entorno real

Probar todos los roles con datos reales

Validar rendimiento consultas estadísticas

Media

Implementar gráficos avanzados

Toast notifications

Breadcrumbs finales

Baja

Modo oscuro

Calendario académico visual

🎯 CONCLUSIÓN FINAL

EcuAsist 2026 v2.0 alcanzó madurez funcional completa:

Automatización académica sólida

Justificaciones inteligentes por días reales

Dashboard tutor avanzado

Seguridad reforzada con superusuario

Arquitectura modular escalable

Diseño visual profesional unificado

El sistema está listo para pruebas finales productivas y despliegue institucional controlado.

FIN DEL DOCUMENTO