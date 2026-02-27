📋 RESUMEN EJECUTIVO — ECUASIST 2026
Chat 04 — Correcciones y Mejoras
Fecha: 27 de Febrero de 2026
Versión: v1.5.2
Estado: En progreso


12 cambios aplicados · 9 archivos modificados · 2 bugs pendientes de verificar


✅ CAMBIOS IMPLEMENTADOS
1. 📧 Email opcional al crear usuario (Admin)

Email NO obligatorio cuando autoridad crea usuario
Aplica en create() y createFromModal()
Registro propio sigue siendo obligatorio
Archivos: controllers/UserController.php, views/users/create.php

2. 🔑 Password no se borra al mostrar errores

Campos password usan data-val + JS DOMContentLoaded
Botón 👁 para mostrar/ocultar en ambos campos
Archivo: views/users/create.php

3. 📝 Permisos Justificaciones — Inspector y Docente

pending() y review() aceptan ['autoridad','inspector','docente']
Navbar muestra menú Justificaciones para docente
Ruta tutor_pending_justifications agregada
Link "🎓 Justificaciones de mi Curso" solo si docente es tutor
Archivos: controllers/JustificationController.php, views/partials/navbar.php, public/index.php

4. 🔔 Session is_tutor al hacer login

Se setea $_SESSION['is_tutor'] al autenticar
Evita error fatal $attModel null en navbar
Archivo: controllers/AuthController.php

5. 👁 Modal "Revisar Justificación" — botón funcionando

Datos pasados por data-* con htmlspecialchars(ENT_QUOTES)
JS usa addEventListener en lugar de onclick inline
Archivo: views/justifications/pending.php

6. 📎 Documento adjunto en modal (sin salir de página)

Modal independiente con z-index:9999
PDFs en <iframe>, imágenes en <img>
img.php extendido para servir PDFs
Archivos: views/justifications/pending.php, public/img.php

7. 🔔 Notificación al Docente/Tutor al justificar

Tutor del curso siempre recibe notificación
Sin auto-notificación si el tutor envía la justificación
Archivo: controllers/JustificationController.php

8. 🔐 Recuperación de contraseña

CSRF en forgotPassword() y resetPassword()
Validación formato email y longitud mínima contraseña
Token inválido/expirado muestra error antes del formulario
Vistas rediseñadas consistentes con el sistema
Email HTML con botón y texto plano como fallback
Archivos: controllers/AuthController.php, views/auth/forgot.php, views/auth/reset.php

9. 📬 Mailer — compatibilidad hosting sin fsockopen/mail()

Diagnóstico del hosting ecuasys.com:

mail() → ❌ bloqueada
fsockopen() → ❌ bloqueada
curl_exec() → ❌ bloqueada
curl_init() → ✅ disponible
socket_create() → ✅ disponible
proc_open + sendmail → ✅ disponible


Solución: proc_open('/usr/sbin/sendmail -t -i') directo
Sin dependencia de PHPMailer para el envío
Email multipart (HTML + texto plano)
Archivo: helpers/Mailer.php

10. 🛡 Validación de asistencia por horario

Docente solo puede registrar en clases que le pertenecen
Query agrega AND cs.teacher_id = :teacher_id
Autoridad queda exenta — puede registrar en cualquier horario
Si schedule_id no pertenece al docente → redirige con error
Mensaje: "⛔ No tienes permiso para registrar asistencia en esa clase"
Archivos: controllers/AttendanceController.php, views/attendance/register.php


🔴 PENDIENTE — CONTINUAR EN NUEVO CHAT
Bug: Docente ve solo 1 clase cuando debería ver 2 (viernes)
Contexto:

Curso: Inicial 1 (0-3 años) "A" - Matutina
Docente: Rengel Victor (user_id = 52)
Horario viernes: Hora 2 → Expresión y Comunicación, Hora 3 → Desarrollo Personal y Social
Hoy es viernes 27/02/2026 y solo aparece 1 clase

Hipótesis:

El fix de validación teacher_id está bloqueando una de las clases
En class_schedule solo hay 1 registro para el viernes con teacher_id=52

Query de diagnóstico pendiente ejecutar:
sqlSELECT cs.id, cs.day_of_week, cs.period_number,
       s.name as asignatura, cs.teacher_id
FROM class_schedule cs
INNER JOIN subjects s ON cs.subject_id = s.id
WHERE cs.teacher_id = 52
AND cs.day_of_week = 'viernes'
ORDER BY cs.period_number;
Si devuelve 2 filas → el fix de validación está filtrando mal → revisar AttendanceController.php
Si devuelve 1 fila → el horario en BD está incompleto → agregar la clase faltante en el horario

📁 ARCHIVOS MODIFICADOS EN ESTE CHAT
ArchivoCambiocontrollers/UserController.phpEmail opcional en create y createFromModalcontrollers/AuthController.phpis_tutor session + recuperación contraseñacontrollers/JustificationController.phpPermisos + notificación tutorcontrollers/AttendanceController.phpValidación teacher_id en registroviews/users/create.phpPassword restore + toggle + email opcionalviews/auth/forgot.phpRediseño + validacionesviews/auth/reset.phpRediseño + toggle + validación tiempo realviews/justifications/pending.phpModal revisar + modal documentoviews/attendance/register.phpMensaje error unauthorizedviews/partials/navbar.phpJustificaciones para docente + is_tutorpublic/index.phpRuta tutor_pending_justificationspublic/img.phpSoporte PDFhelpers/Mailer.phpproc_open + sendmail (sin fsockopen/mail)

⚙️ NOTAS TÉCNICAS CLAVE
Mailer — Hosting ecuasys.com
php// Usar proc_open con sendmail — única opción disponible
// SMTP_FROM debe ser correo del dominio ecuasys.com
// Configurar SPF/DKIM en cPanel para evitar spam
Validación horario docente
php// En AttendanceController::register()
// La query usa AND cs.teacher_id = :teacher_id
// Autoridad exenta: Security::hasRole('autoridad') omite el filtro
Session is_tutor
php// Seteado en AuthController::login()
// Usuarios con sesión activa deben re-loguearse para aplicar

🔗 ARCHIVOS CLAVE PARA INICIAR NUEVO CHAT
/controllers/AttendanceController.php  — Bug pendiente: clases viernes
/models/ClassSchedule.php              — getTeacherScheduleToday()
/controllers/AuthController.php        — Login + recuperación contraseña
/helpers/Mailer.php                    — proc_open sendmail
/views/justifications/pending.php      — Modal revisar + documento
/public/index.php                      — Todas las rutas

📋 RESUMEN EJECUTIVO — ECUASIST 2026
Sesión Chat 03 — Correcciones y Mejoras
Fecha: 27 de Febrero de 2026
Versión: v1.5.1
Estado: Estable — listo para continuar

🎯 OBJETIVO DE LA SESIÓN
Corrección de bugs funcionales y mejoras de UX/permisos detectados durante pruebas del sistema en producción local.

✅ CAMBIOS IMPLEMENTADOS
1. 📧 Correo opcional al crear usuario (Admin)
Problema: El administrador no podía crear usuarios sin ingresar un correo electrónico.
Archivos modificados:

controllers/UserController.php
views/users/create.php

Cambios:

Email ya no es obligatorio cuando la autoridad crea un usuario desde el panel
Si se ingresa email, sigue validando formato y unicidad
El campo en el formulario muestra etiqueta (opcional)
Aplica tanto al formulario /views/users/create.php (create_user) como al modal de users/index.php (create_user_modal)
El registro propio del usuario (AuthController) no fue modificado — sigue siendo obligatorio


2. 🔑 Contraseña no se borra al mostrar errores
Problema: Al haber un error de validación en el formulario de creación de usuario, los campos de contraseña se vaciaban obligando a ingresarla nuevamente.
Archivo modificado:

views/users/create.php

Cambios:

Los campos password y confirm_password ahora usan atributo data-val con el valor del POST
Un JS con DOMContentLoaded restaura el valor al cargar la página tras error
Se agregó botón 👁 para mostrar/ocultar contraseña en ambos campos
Solución compatible con todos los navegadores (evita bloqueo de value en type="password")


3. 📝 Permisos de Justificaciones — Inspector y Docente
Problema:

El inspector no podía ver ni acceder a "Revisar Justificaciones"
El docente/tutor no podía acceder a las justificaciones pendientes de su curso desde la notificación

Archivos modificados:

controllers/JustificationController.php
views/partials/navbar.php
public/index.php

Cambios:

pending() y review() ahora aceptan roles ['autoridad', 'inspector', 'docente']
El navbar muestra el menú Justificaciones también para rol docente
Docentes e inspectores ven el link "✅ Revisar Justificaciones"
Se agregó ruta tutor_pending_justifications en index.php que llama a pendingForTutor()
El navbar muestra "🎓 Justificaciones de mi Curso" solo si el docente es tutor (usando $_SESSION['is_tutor'])


4. 🔔 Session is_tutor al hacer login
Problema: El navbar llamaba $attModel->getTutorCourseId() directamente causando error fatal porque $attModel no existe en el contexto del navbar.
Archivo modificado:

controllers/AuthController.php

Cambios:

Al hacer login, se verifica si el docente es tutor de algún curso activo
Se setea $_SESSION['is_tutor'] = true/false
El navbar usa esta variable de sesión en lugar de llamar directamente al modelo
Nota: Usuarios con sesión activa deben cerrar sesión y volver a entrar para que se aplique


5. 👁 Modal "Revisar Justificación" — Botón no funcionaba
Problema: El botón "👁 Revisar" no abría el modal de revisión.
Archivo modificado:

views/justifications/pending.php

Causa: El onclick inline con parámetros PHP se rompía cuando el motivo contenía comillas, tildes o saltos de línea — el JS fallaba silenciosamente.
Solución:

Los datos se pasan por atributos data-* con htmlspecialchars(ENT_QUOTES)
El JS usa dataset para leer los valores — robusto ante cualquier texto
Se reemplazó el onclick inline por addEventListener en DOMContentLoaded


6. 📎 Ver documento adjunto — URL incorrecta
Problema: Al hacer clic en "Ver documento" redirigía a http://localhost/dashboard/ en lugar de abrir el archivo.
Archivos modificados:

views/justifications/pending.php
public/img.php

Cambios:

img.php ahora acepta archivos PDF además de imágenes (jpg, png, gif, webp)
Los PDFs se sirven con Content-Type: application/pdf e inline
La URL del documento ahora apunta a img.php?f=justifications/archivo.ext correctamente


7. 🖼 Documento adjunto en Modal (sin salir de la página)
Problema: El documento se abría en otra página, perdiendo el contexto del modal de revisión.
Archivo modificado:

views/justifications/pending.php

Cambios:

Se agregó un segundo modal independiente para previsualizar el documento
PDFs → se muestran en <iframe> dentro del modal
Imágenes → se muestran con <img> dentro del modal
Botón "⬇ Abrir en nueva pestaña" disponible como alternativa
El modal de documento usa z-index:9999 y position:fixed propio (sin depender de clase CSS modal-overlay) para garantizar que siempre quede encima del modal de revisión


8. 🔔 Notificación al Docente/Tutor cuando estudiante justifica
Problema: Cuando un estudiante enviaba una justificación, el docente tutor del curso no recibía notificación en la campana.
Archivo modificado:

controllers/JustificationController.php

Causa: La lógica original era excluyente — notificaba o al tutor o a autoridad/inspector según can_approve, nunca a ambos.
Solución:

El tutor del curso siempre recibe notificación independientemente de los días de falta
Si can_approve = inspector/autoridad, además se notifica también a esos roles
Si el que envía es el propio tutor, no se auto-notifica ($tutorId != $_SESSION['user_id'])
La query del tutor filtra por año lectivo activo


📁 ARCHIVOS MODIFICADOS EN ESTA SESIÓN
ArchivoCambiocontrollers/UserController.phpEmail opcional en create() y createFromModal()views/users/create.phpLabel opcional, restaurar password, toggle 👁controllers/JustificationController.phpPermisos docente/inspector + notificación tutorcontrollers/AuthController.phpSetear $_SESSION['is_tutor'] al loginviews/partials/navbar.phpMenú justificaciones para docente + link tutorpublic/index.phpRuta tutor_pending_justificationsviews/justifications/pending.phpModal revisar, modal documento, URLs correctaspublic/img.phpSoporte para PDF

🐛 BUGS CORREGIDOS
#BugEstado1Email obligatorio al crear usuario desde admin✅ Corregido2Password se borra al mostrar errores de validación✅ Corregido3Inspector no veía menú Justificaciones✅ Corregido4Docente tutor no podía acceder a justificaciones✅ Corregido5Error fatal $attModel en navbar✅ Corregido6Botón "Revisar" no abría modal✅ Corregido7"Ver documento" redirigía a dashboard✅ Corregido8PDF no se visualizaba✅ Corregido9Modal documento quedaba detrás del modal revisar✅ Corregido10Docente no recibía campana de notificación✅ Corregido

⚙️ NOTAS TÉCNICAS IMPORTANTES
Sesión is_tutor
php// Se setea en AuthController::login() tras validar credenciales
$_SESSION['is_tutor'] = (bool)$attModel->getTutorCourseId($user['id']);
Si un docente es asignado como tutor después de hacer login, verá el link en el navbar solo tras cerrar sesión y volver a entrar.
Restaurar password en formulario
javascript// data-val guarda el valor PHP; JS lo asigna al campo type="password"
document.querySelectorAll('input[data-val]').forEach(function(el) {
    if (el.getAttribute('data-val') !== '') el.value = el.getAttribute('data-val');
});
Servir documentos
URL: BASE_URL/img.php?f=justifications/archivo.pdf
img.php acepta: jpg, jpeg, png, gif, webp, pdf

🔄 PRÓXIMOS PASOS SUGERIDOS

Breadcrumbs en todas las vistas
Notificaciones toast en lugar de divs de alerta
Gráficos en reportes y estadísticas
Búsqueda global en navbar
Vista calendario para horarios (drag & drop)
Paginación en listados grandes (usuarios, asistencias)


🔗 ARCHIVOS CLAVE PARA REVISAR EN NUEVA SESIÓN
/controllers/UserController.php       — Gestión usuarios (email opcional)
/controllers/JustificationController.php — Permisos + notificaciones
/controllers/AuthController.php       — Login + session is_tutor
/views/partials/navbar.php            — Menú con roles corregidos
/views/justifications/pending.php     — Modal revisar + modal documento
/public/img.php                       — Servidor de archivos (PDF+imágenes)
/public/index.php                     — Rutas actualizadas

# 📋 Resumen Ejecutivo — EcuAsistencia 2026
**Mejoras y Cambios del Sistema** · v2.0 · Febrero 2026

---

> **17 cambios aplicados · 8 módulos afectados · 5 bugs críticos resueltos**

---

## 1. 🔔 Sistema de Notificaciones

### Panel emergente (campana)
- Al hacer clic en 🔔 se despliega un panel propio del sistema — ya no redirige a la página completa.
- Muestra título, mensaje con nombre del estudiante, tiempo relativo ("hace 5min") y punto azul para no leídas.
- Clic en una notificación: se marca como leída, el contador baja **inmediatamente** y navega al detalle.
- El badge se actualiza de forma optimista sin esperar respuesta del servidor.
- Polling cada **10 segundos** — si llegan notificaciones nuevas la campana anima con efecto de timbrazo.

### Mensajes con contexto
- Antes: `"Justificación de 2 día(s) requiere revisión"`
- Ahora: `"Rengel Romina justificó 2 día(s) (12/02/2026 al 13/02/2026)"`

### Eliminación cruzada
- Cuando tutor o inspector aprueba/rechaza una justificación, las notificaciones del otro revisor se eliminan automáticamente.
- Evita notificaciones huérfanas en la campana.

### Inspector recibe notificaciones
- Antes: el inspector solo recibía notificaciones para justificaciones de más de 3 días.
- Ahora: el inspector **siempre** recibe notificación; el tutor la recibe adicionalmente si aplica.

---

## 2. 👨‍👩‍👧‍👦 Módulo de Representantes

### Registro de representantes
- Formulario propio para el rol Representante accesible sin estar logueado.
- Orden de campos corregido: **Nombres → Apellidos**.
- DNI vacío se guarda como `NULL` para evitar conflictos con `UNIQUE KEY`.
- Rol `representante` asignado automáticamente al crear la cuenta.

### Solicitudes de vinculación
- Búsqueda de estudiantes por nombre o cédula en modal flotante con resultados en tiempo real.
- El representante selecciona parentesco y puede agregar un mensaje opcional.
- Al enviar solicitud, **autoridad e inspector reciben notificación automática** con nombre del representante y del estudiante.
- Panel de gestión para autoridad con pestañas: **Pendientes / Aprobadas / Rechazadas**.
- Botones Aprobar y Rechazar usan el modal del sistema (no el `confirm()` nativo).
- Al aprobar/rechazar, las notificaciones de vinculación se eliminan del contador de los demás revisores.

### Retirar representado
- Botón **🔗 Retirar** en cada tarjeta de la vista "Mis Representados".
- Solicita confirmación con el modal del sistema antes de desvincular.
- Elimina el registro de la tabla `representatives`.

### Menú de navegación
- Enlace **"🔗 Solicitudes de Vinculación"** agregado dentro del menú Administración.
- Disponible en desktop (dropdown) y mobile (acordeón), debajo de Representantes.

---

## 3. ⚠️ Modal de Confirmación del Sistema

Reemplaza **todos** los `confirm()` nativos del navegador por un modal con diseño propio:

- Configurable: ícono, título, mensaje, texto del botón OK y función callback al confirmar.
- Función global `ecConfirm()` disponible en todas las páginas a través del navbar.
- Se cierra con botón Cancelar o tecla **Escape**.

Aplicado en:
- Eliminar notificación individual
- Limpiar notificaciones leídas
- Aprobar solicitud de vinculación
- Rechazar solicitud de vinculación
- Retirar representado

---

## 4. 🔐 Correcciones de Seguridad y Sesión

### Redirección al login
- **Problema:** `requireLogin()` construía `BASE_URL + '/public/index.php?action=login'` pero `BASE_URL` ya incluye `/public`, resultando en `/public/public/index.php` — URL inválida que enviaba al usuario a `/dashboard/` sin sesión.
- **Corrección:** Cambiado a `BASE_URL . '/?action=login'`, funciona correctamente en local y producción.

### Timeout de sesión
- **Antes:** 30 minutos de inactividad cerraban la sesión.
- **Ahora:** 8 horas — evita cierres inesperados al dejar el sistema abierto.
- Cookie de sesión mantiene 24 horas de vida.

---

## 5. 🐛 Bugs Críticos Resueltos

| Archivo | Bug | Corrección |
|---|---|---|
| `RepresentativeController.php` | PDO no permite reusar `:q` tres veces en la misma query — `SQLSTATE[HY093]` | Renombrados a `:q1`, `:q2`, `:q3` |
| `RepresentativeController.php` | `:rel` y `:msg` duplicados en `ON DUPLICATE KEY UPDATE` | Renombrados a `:rel2`, `:msg2` |
| `ScheduleController.php` | Arrow function `fn() =>` incompatible con PHP < 7.4 — Error 500 en producción | Reemplazado por `function($c) use ($courseId) {}` |
| `ClassSchedule.php` | Campo `start_time NOT NULL` sin default — Error 500 al crear horario en producción | INSERT calcula `start_time`/`end_time` por número de periodo y detecta columnas automáticamente |
| `my_children.php` | `fetch()` con URL relativa fallaba según el contexto de la página actual | Corregido a URL absoluta usando `BASE_URL` de PHP |

---

## 6. 📁 Archivos Modificados

| Archivo | Cambios principales |
|---|---|
| `views/partials/navbar.php` | Panel popup notificaciones, animación campana, polling 10s, modal `ecConfirm`, menú Solicitudes de Vinculación |
| `controllers/JustificationController.php` | Inspector recibe notificaciones, mensajes con nombre/fecha, eliminación cruzada |
| `controllers/RepresentativeController.php` | Búsqueda estudiantes, solicitud vinculación con notificación, retirar representado, bugs PDO |
| `controllers/AuthController.php` | DNI null handling, asignación correcta de rol representante |
| `controllers/ScheduleController.php` | Arrow function → closure, validación año escolar activo |
| `models/ClassSchedule.php` | INSERT con `start_time`/`end_time`, detección automática de columnas |
| `models/Notification.php` | Método `deleteByLinkExcept()` |
| `helpers/Security.php` | URL de redirección al login corregida |
| `config/config.php` | Timeout sesión 30min → 8 horas |
| `views/representatives/my_children.php` | Búsqueda funcional, botón Retirar, mensajes de estado |
| `views/representatives/link_requests.php` | Toast flotante, modal `ecConfirm` en Aprobar/Rechazar |
| `views/auth/register.php` | Orden de campos: Nombres antes que Apellidos |
| `public/index.php` | Rutas nuevas: `unlink_student`, `search_students_json`, `link_requests` |

---

## 📌 Nota de Compatibilidad

Todos los cambios son retrocompatibles con **PHP 5.6+** y **MySQL 5.7+**. Los bugs de producción fueron causados por diferencias de versión PHP (arrow functions de 7.4) y esquema de base de datos (columnas `NOT NULL` sin default presentes en producción pero no en local). El sistema funciona en XAMPP y en producción (cPanel / ecuasys.com) sin configuración adicional.

---

*EcuAsistencia 2026 — Documento confidencial*