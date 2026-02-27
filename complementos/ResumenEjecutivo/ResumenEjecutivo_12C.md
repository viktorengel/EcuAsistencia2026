# 📋 RESUMEN EJECUTIVO — ECUASIST 2026
## Mejoras y Cambios del Sistema · v1.5 → v1.5.1 · Febrero 2026

---

> **27 cambios aplicados · 13 módulos afectados · 15 bugs resueltos**

---

## 🗂 ÍNDICE DE SESIONES

| Sesión | Enfoque principal | Cambios |
|---|---|---|
| Chat 02 | Notificaciones, Representantes, Seguridad | 17 |
| Chat 03 | Permisos, Justificaciones, UX formularios | 10 |

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

### Inspector y Docente/Tutor reciben notificaciones *(mejorado en Chat 03)*
- Antes: el inspector solo recibía notificaciones para justificaciones de más de 3 días. El docente tutor nunca recibía notificación cuando `can_approve` era `inspector`.
- Ahora: el inspector **siempre** recibe notificación. El tutor del curso **siempre** recibe notificación independientemente del número de días de falta.
- Si el que envía la justificación es el propio tutor, no se auto-notifica.

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

## 4. 👤 Gestión de Usuarios

### Email opcional al crear usuario *(Chat 03)*
- El administrador puede crear usuarios **sin correo electrónico**.
- Si se ingresa email, sigue validando formato y unicidad.
- El campo en el formulario muestra la etiqueta **(opcional)**.
- Aplica tanto al formulario de creación (`create_user`) como al modal (`create_user_modal`).
- El registro propio del usuario (`AuthController`) **no fue modificado** — sigue siendo obligatorio.

### Contraseña no se borra al mostrar errores *(Chat 03)*
- Los campos `password` y `confirm_password` ahora conservan su valor cuando hay errores de validación.
- Solución con atributo `data-val` + JS `DOMContentLoaded` — compatible con todos los navegadores.
- Se agregó botón 👁 para mostrar/ocultar contraseña en ambos campos.

---

## 5. 📝 Módulo de Justificaciones *(Chat 03)*

### Permisos corregidos
- `pending()` y `review()` ahora aceptan roles `['autoridad', 'inspector', 'docente']`.
- El navbar muestra el menú **Justificaciones** también para rol `docente`.
- Docentes e inspectores ven el link **"✅ Revisar Justificaciones"** e **"📋 Historial Revisadas"**.
- Se agregó ruta `tutor_pending_justifications` en `index.php` que llama a `pendingForTutor()`.
- El navbar muestra **"🎓 Justificaciones de mi Curso"** solo si el docente es tutor.

### Modal "Revisar" — botón no funcionaba
- **Causa:** El `onclick` inline se rompía cuando el motivo contenía comillas, tildes o saltos de línea.
- **Solución:** Datos pasados por `data-*` con `htmlspecialchars(ENT_QUOTES)` y JS con `addEventListener`.

### Ver documento adjunto en modal
- Antes: abrir documento redirigía a `http://localhost/dashboard/`.
- Ahora: se abre un **modal de previsualización** encima del modal de revisión.
- PDFs → renderizados en `<iframe>` dentro del modal.
- Imágenes → mostradas con `<img>` dentro del modal.
- Botón **"⬇ Abrir en nueva pestaña"** disponible como alternativa.
- Modal de documento usa `z-index:9999` con `position:fixed` independiente para garantizar posición encima de todo.
- `img.php` extendido para servir **PDFs** además de imágenes.

---

## 6. 🔐 Correcciones de Seguridad y Sesión

### Redirección al login
- **Problema:** `requireLogin()` construía `BASE_URL + '/public/index.php?action=login'` pero `BASE_URL` ya incluye `/public`, resultando en `/public/public/index.php`.
- **Corrección:** Cambiado a `BASE_URL . '/?action=login'`.

### Timeout de sesión
- **Antes:** 30 minutos de inactividad cerraban la sesión.
- **Ahora:** 8 horas — evita cierres inesperados al dejar el sistema abierto.
- Cookie de sesión mantiene 24 horas de vida.

### Session `is_tutor` al hacer login *(Chat 03)*
- **Problema:** El navbar llamaba `$attModel->getTutorCourseId()` directamente causando error fatal.
- **Solución:** Al hacer login se verifica si el docente es tutor y se setea `$_SESSION['is_tutor']`.
- El navbar usa esta variable de sesión sin acceder al modelo directamente.
- **Nota:** Usuarios con sesión activa deben cerrar sesión y volver a entrar para aplicar el cambio.

---

## 7. 🐛 Bugs Resueltos

| # | Archivo | Bug | Sesión |
|---|---|---|---|
| 1 | `RepresentativeController.php` | PDO no permite reusar `:q` tres veces — `SQLSTATE[HY093]` → renombrados `:q1`, `:q2`, `:q3` | Chat 02 |
| 2 | `RepresentativeController.php` | `:rel` y `:msg` duplicados en `ON DUPLICATE KEY UPDATE` → renombrados `:rel2`, `:msg2` | Chat 02 |
| 3 | `ScheduleController.php` | Arrow function `fn() =>` incompatible con PHP < 7.4 — Error 500 → reemplazado por closure | Chat 02 |
| 4 | `ClassSchedule.php` | Campo `start_time NOT NULL` sin default — Error 500 al crear horario → INSERT calcula automáticamente | Chat 02 |
| 5 | `my_children.php` | `fetch()` con URL relativa fallaba → corregido a URL absoluta con `BASE_URL` | Chat 02 |
| 6 | `UserController.php` | Email obligatorio al crear usuario desde admin → ahora opcional | Chat 03 |
| 7 | `views/users/create.php` | Campos password se vaciaban al mostrar errores → restaurados con `data-val` | Chat 03 |
| 8 | `JustificationController.php` | Inspector no podía ver justificaciones → permisos corregidos | Chat 03 |
| 9 | `JustificationController.php` | Docente tutor no recibía notificaciones → notificación siempre al tutor del curso | Chat 03 |
| 10 | `navbar.php` | Error fatal `$attModel` null en navbar → reemplazado por `$_SESSION['is_tutor']` | Chat 03 |
| 11 | `pending.php` | Botón "Revisar" no abría modal → `onclick` inline roto por caracteres especiales | Chat 03 |
| 12 | `pending.php` | "Ver documento" redirigía a `/dashboard/` → URL corregida con `img.php?f=` | Chat 03 |
| 13 | `img.php` | PDFs no se visualizaban → soporte PDF agregado | Chat 03 |
| 14 | `pending.php` | Modal documento quedaba detrás del modal revisar → `z-index:9999` independiente | Chat 03 |
| 15 | `AuthController.php` | `is_tutor` no se seteaba en sesión → verificación al hacer login | Chat 03 |

---

## 8. 📁 Archivos Modificados

| Archivo | Cambios principales | Sesión |
|---|---|---|
| `views/partials/navbar.php` | Panel popup notificaciones, animación campana, polling 10s, modal `ecConfirm`, menú Justificaciones para docente, link Solicitudes de Vinculación | 02 + 03 |
| `controllers/JustificationController.php` | Permisos docente/inspector, mensajes con nombre/fecha, eliminación cruzada, notificación siempre al tutor | 02 + 03 |
| `controllers/RepresentativeController.php` | Búsqueda estudiantes, solicitud vinculación con notificación, retirar representado, bugs PDO | 02 |
| `controllers/AuthController.php` | DNI null handling, rol representante, `$_SESSION['is_tutor']` al login | 02 + 03 |
| `controllers/UserController.php` | Email opcional en `create()` y `createFromModal()` | 03 |
| `controllers/ScheduleController.php` | Arrow function → closure, validación año escolar activo | 02 |
| `models/ClassSchedule.php` | INSERT con `start_time`/`end_time`, detección automática de columnas | 02 |
| `models/Notification.php` | Método `deleteByLinkExcept()` | 02 |
| `helpers/Security.php` | URL de redirección al login corregida | 02 |
| `config/config.php` | Timeout sesión 30min → 8 horas | 02 |
| `views/users/create.php` | Label email opcional, restaurar password, toggle 👁 | 03 |
| `views/justifications/pending.php` | Modal revisar con `data-*`, modal documento, URL correcta, `z-index` independiente | 03 |
| `views/representatives/my_children.php` | Búsqueda funcional, botón Retirar, mensajes de estado | 02 |
| `views/representatives/link_requests.php` | Toast flotante, modal `ecConfirm` en Aprobar/Rechazar | 02 |
| `views/auth/register.php` | Orden de campos: Nombres antes que Apellidos | 02 |
| `public/index.php` | Rutas: `unlink_student`, `search_students_json`, `link_requests`, `tutor_pending_justifications` | 02 + 03 |
| `public/img.php` | Soporte PDF agregado | 03 |

---

## 9. ⚙️ NOTAS TÉCNICAS IMPORTANTES

### Sesión `is_tutor`
```php
// Se setea en AuthController::login() tras validar credenciales
$_SESSION['is_tutor'] = (bool)$attModel->getTutorCourseId($user['id']);
// Si el docente es asignado como tutor después del login,
// debe cerrar sesión y volver a entrar para ver el menú actualizado.
```

### Restaurar password en formulario
```javascript
// data-val guarda el valor PHP; JS lo asigna al campo type="password"
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[data-val]').forEach(function(el) {
        if (el.getAttribute('data-val') !== '') el.value = el.getAttribute('data-val');
    });
});
```

### Servir documentos (PDF + imágenes)
```
URL: BASE_URL/img.php?f=justifications/archivo.pdf
Soporta: jpg, jpeg, png, gif, webp, pdf
```

### Compatibilidad PHP
- Todos los cambios son retrocompatibles con **PHP 7.4+** y **MySQL 5.7+**.
- Bugs de producción fueron causados por diferencias de versión PHP (arrow functions) y esquema BD (columnas `NOT NULL` sin default).

---

## 🔄 PRÓXIMOS PASOS SUGERIDOS

- Breadcrumbs en todas las vistas
- Notificaciones toast en lugar de divs de alerta
- Gráficos interactivos en reportes y estadísticas
- Búsqueda global en navbar
- Vista calendario para horarios (drag & drop)
- Paginación en listados grandes (usuarios, asistencias)
- Fotos de perfil de estudiantes

---

## 🔗 ARCHIVOS CLAVE PARA REVISAR EN NUEVA SESIÓN

```
/controllers/UserController.php            — Gestión usuarios (email opcional)
/controllers/JustificationController.php   — Permisos + notificaciones completas
/controllers/AuthController.php            — Login + session is_tutor
/controllers/RepresentativeController.php  — Vinculaciones + solicitudes
/views/partials/navbar.php                 — Menú con roles + panel notificaciones
/views/justifications/pending.php          — Modal revisar + modal documento
/public/img.php                            — Servidor de archivos (PDF + imágenes)
/public/index.php                          — Todas las rutas actualizadas
```

---

*EcuAsistencia 2026 — Documento confidencial*
*FIN DEL RESUMEN UNIFICADO ✅*