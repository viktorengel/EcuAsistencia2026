# 🧩 Menú EcuAsistencia 2026 — v2.0

## 🏠 1. Dashboard
- Resumen institucional del día
- Indicadores rápidos (asistencia, alertas, justificativos pendientes)
- 🆕 Notificaciones visuales por rol (badge contador)
- 🆕 Accesos rápidos personalizados por rol

## 👥 2. Usuarios y Roles
- Gestión de usuarios (crear, editar, desactivar)
- Roles y permisos (Autoridad, Inspector, Docente, Estudiante, Representante)
- Cambio de contraseña / seguridad

## 🏫 3. Configuración Académica
- Institución (datos, logo, jornadas)
- Periodo lectivo / Año escolar
- Niveles, cursos y paralelos
- Materias / Asignaturas

## 👨‍🏫 4. Gestión Docente
- Asignación docente–materia–curso
- Tutorías (tutor por paralelo)

## 🎓 5. Gestión Estudiantil
- Registro de estudiantes
- Asignación a curso/paralelo
- Estado del estudiante

## 👪 6. Representantes
- Registro y vinculación con estudiantes
- 📲 Mis Representados (vista del representante)

## 📅 7. Horarios
- Configuración de bloques horarios
- Asignación de horario por curso
- Visualización de horarios

## ✅ 8. Asistencia
- Registrar asistencia (docente — por clase del día)
- Ver asistencias (por curso / por estudiante)
- Mi asistencia (vista estudiante)
- Calendario de asistencia

## 🚨 9. Alertas Tempranas  ← ❌ FALTA CONSTRUIR
- Configuración de reglas (X faltas consecutivas / acumuladas)
- Alertas generadas y seguimiento
- Notificación automática a representantes

## 📎 10. Justificaciones
- Solicitar justificación (subir documento)
- Revisar justificaciones (Inspector/Tutor)
- Justificaciones revisadas
- Mis justificaciones

## 📊 11. Reportes y Estadísticas
- Generar reportes (asistencia por curso/estudiante)
- Estadísticas visuales
- Exportación PDF / Excel

## 💬 12. Comunicación  ← ❌ FALTA CONSTRUIR
- Mensajería interna
- Avisos institucionales
- Notificación a representantes
- Historial de comunicaciones

## ⚙️ 13. Administración del Sistema
- Respaldos (backup / restaurar)
- 🆕 Logs y auditoría del sistema  ← ⚠️ FALTA VISTA
- 🆕 Configuración de correo SMTP  ← ⚠️ FALTA VISTA
- 🆕 Documentos institucionales    ← ⚠️ FALTA

❌ Lo que FALTA (brechas reales)
Del módulo 9 — Sistema de alerta temprana: No existe. No hay controlador, modelo, ni vista para reglas de alerta por faltas consecutivas/acumuladas ni notificación automática a representantes.
Del módulo 12 — Comunicación interna: No existe. No hay mensajería, avisos institucionales ni historial de comunicaciones. Solo existe el modelo Notification.php pero sin UI ni controlador activo.
Del módulo 13 — Incompleto:

Logs del sistema: Logger.php existe pero sin vista de auditoría
Configuración de correo: Mailer.php existe pero sin interfaz de configuración
Subida de documentos institucionales: no implementado

Del módulo 1 — Dashboard: Accesos rápidos y notificaciones visuales en tiempo real son básicos o inexistentes.