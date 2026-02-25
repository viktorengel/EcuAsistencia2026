📋 Resumen Ejecutivo — Sesión Gestión de Usuarios & Correcciones
Módulo 👥 Gestión de Usuarios — Reescritura completa
Archivos modificados

views/users/index.php ← principal, reescrito
controllers/UserController.php ← métodos nuevos
models/User.php ← método nuevo
public/index.php ← rutas nuevas

Funcionalidades implementadas
Buscador

Búsqueda en tiempo real por nombre, email y cédula
Normalización de tildes (busca "Ramirez" y encuentra "Ramírez")
Botón limpiar búsqueda

Modal Crear Usuario (reemplaza página separada)

Ruta nueva: ?action=create_user_modal
Todos los campos: usuario, email, contraseña, nombres, apellidos, cédula/pasaporte, teléfono, roles
Si hay error de validación → datos se conservan en $_SESSION y el modal se reabre automáticamente
Al crear: preserva el filtro de rol activo o cambia al rol del nuevo usuario

Modal Editar Usuario (reemplaza página separada)

Ruta nueva: ?action=edit_user_modal
Datos precargados desde JSON embebido (sin fetch)
Misma lógica de conservación en error

Campo Cédula / Pasaporte inteligente

Auto-detección: solo dígitos → trata como cédula; letras+números → trata como pasaporte
Cédula: validación algoritmo Módulo 10 oficial Ecuador con 3 capas:

Provincia (01-24)
Tercer dígito < 6
Dígito verificador


Pasaporte: automáticamente en MAYÚSCULAS, solo alfanumérico, 4-12 caracteres, sin espacios
Estrategia de error: cédula inválida se GUARDA (advertencia), no bloquea; pasaporte con formato incorrecto sí bloquea; duplicado siempre bloquea
Badge en tabla: ✓ verde si válida, ⚠ amarillo si no verificada, 🌐 para pasaporte
models/User.php: nuevo método findByDni($dni, $excludeId) para unicidad

Validación teléfono en tiempo real

Celular: 09XXXXXXXX (10 dígitos) → ✅ Celular válido
Fijo: 0[2-7]XXXXXXX (9 dígitos) → ✅ Teléfono fijo válido
Feedback progresivo mientras escribe

Modal de confirmación propio

Reemplaza confirm() del browser en: eliminar usuario, quitar rol
Mismo estilo que modales del sistema

Sistema de Toast (reemplaza divs estáticos)

Igual al módulo Horarios: flotante esquina superior derecha
ok verde, err rojo, inf azul
URL limpiada con history.replaceState tras mostrar

Preservación de filtros

Al crear usuario: si el filtro actual coincide con rol asignado → mantiene; si no → cambia al primer rol asignado
Al editar usuario: mantiene filtro activo
Al eliminar usuario: mantiene filtro activo
En errores de validación: mantiene filtro activo


Módulo ⚙️ Configuración Académica
Archivos modificados

views/academic/index.php
controllers/AcademicController.php

Correcciones y mejoras
Bug crítico: eliminar asignatura no limpiaba el horario

removeCourseSubject() ahora hace DELETE en cascada:

class_schedule (horas en horario)
teacher_assignments (docente asignado)
course_subjects (la asignatura del curso)


Ya no bloquea si hay docente — lo elimina todo en orden

Modal confirmación para quitar docente

Botón ✖ de quitar docente usaba confirm() del browser → migrado a showConfirmModal

Botón acceso directo al Horario

Nueva acción en la columna de acciones de cada curso: 📅 Horario
Enlaza directo a ?action=manage_schedule&course_id=X

Sistema de Toast (reemplaza divs estáticos)

Todos los mensajes migrados: curso/asignatura/año creado, actualizado, eliminado, horas actualizadas, representante asignado/quitado, todos los errores


Módulo 🗓️ Navbar
Archivo modificado

views/partials/navbar.php

Correcciones

Línea blanca en menú activo: eliminado ::after con línea de 2px, reemplazado por background: rgba(255,255,255,0.15) sin artefactos
Dropdown se cerraba al bajar el mouse: top: calc(100%+2px) → top: 100% (sin gap), pseudo-elemento ::before invisible como puente de hover


Bug pendiente confirmado
RepresentativeController.php — Error 500 en producción

Causa: el servidor tiene el archivo original sin los métodos assignFromAcademic() y removeFromAcademic()
Solución: subir controllers/RepresentativeController.php del output
Mejora aplicada: validación defensiva con ?? en todos los $_POST para evitar futuros 500


Archivos para subir al servidor (outputs de esta sesión)
ArchivoEstadoviews/users/index.php✅ Listocontrollers/UserController.php✅ Listomodels/User.php✅ Listopublic/index.php✅ Listoviews/academic/index.php✅ Listocontrollers/AcademicController.php✅ Listocontrollers/RepresentativeController.php✅ Listo — URGENTE subirviews/partials/navbar.php✅ Listo

Pendientes / Sugerencias para próxima sesión

Verificar que el navbar funcione correctamente tras los cambios de hover/active
Revisar si hay otros confirm() del browser en otros módulos (Horarios, Representantes, Reportes)
Considerar migrar mensajes estáticos en otros módulos al sistema de toast
Probar flujo completo de crear usuario con cada combinación de roles y filtros