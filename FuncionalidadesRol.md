Quiero implementar estas funciones al sistema
Rol: AUTORIDAD
Sección: Gestión de Usuarios
Funcionalidad: Crear/editar/eliminar usuarios

# 📋 FUNCIONALIDADES POR ROL - ECUASIST 2026

## Versión: 1.5
**Fecha:** 16 de Febrero de 2026  
**Cobertura:** 97.4%

---

## 👑 AUTORIDAD

### Gestión de Usuarios
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 1 | Crear/editar/eliminar usuarios | ✅ Implementado | Ok
| 2 | Asignar/quitar roles a usuarios | ✅ Implementado | Ok

### Gestión Académica
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 3 | Crear/editar/eliminar años lectivos | ✅ Implementado |
| 4 | Activar/desactivar año lectivo | ✅ Implementado |
| 5 | Crear/editar/eliminar cursos | ✅ Implementado |
| 6 | Crear/editar/eliminar asignaturas | ✅ Implementado |

### Asignaciones Docentes
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 7 | Asignar docente-materia a curso | ✅ Implementado |
| 8 | Eliminar asignación docente-materia | ✅ Implementado |
| 9 | Asignar docente tutor a curso | ✅ Implementado |
| 10 | Quitar docente tutor de curso | ✅ Implementado |

### Matriculación
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 11 | Matricular estudiantes a cursos | ✅ Implementado |
| 12 | Retirar estudiante de curso | ✅ Implementado |

### Horarios
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 13 | Crear horarios de clases por curso | ✅ Implementado |
| 14 | Editar horarios de clases | ✅ Implementado |
| 15 | Eliminar clases del horario | ✅ Implementado |

### Configuración Institucional
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 16 | Configurar datos de institución | ✅ Implementado |
| 17 | Subir/cambiar logo institucional | ✅ Implementado |
| 18 | Asignar jornadas a institución | ✅ Implementado |
| 19 | Quitar jornadas de institución | ✅ Implementado |

### Asistencia
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 20 | Ver asistencias de todos los cursos | ✅ Implementado |
| 21 | Registrar asistencia (cualquier curso) | ✅ Implementado |
| 22 | Editar asistencia (dentro de 48h) | ✅ Implementado |
| 31 | Ver calendario de asistencias | ✅ Implementado |

### Justificaciones
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 23 | Ver todas las justificaciones | ✅ Implementado |
| 24 | Aprobar justificaciones | ✅ Implementado |
| 25 | Rechazar justificaciones | ✅ Implementado |

### Reportes y Estadísticas
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 26 | Generar reportes PDF de asistencia | ✅ Implementado |
| 27 | Generar reportes Excel de asistencia | ✅ Implementado |
| 28 | Ver estadísticas generales del sistema | ✅ Implementado |
| 29 | Ver estadísticas por curso | ✅ Implementado |
| 30 | Ver top estudiantes con más ausencias | ✅ Implementado |

### Respaldos y Logs
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 32 | Crear respaldos de base de datos | ✅ Implementado |
| 33 | Descargar respaldos | ✅ Implementado |
| 34 | Ver logs de actividad del sistema | ✅ Implementado |

### Representantes
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 35 | Vincular representante-estudiante | ✅ Implementado |
| 36 | Desvincular representante-estudiante | ✅ Implementado |
| 37 | Marcar representante como principal | ✅ Implementado |

### Sistema
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 38 | Buscar en el sistema (global) | ✅ Implementado |
| 39 | Editar su propio perfil | ✅ Implementado |
| 40 | Cambiar su propia contraseña | ✅ Implementado |

**Total Autoridad: 40/40 ✅**

---

## 🔍 INSPECTOR

### Visualización de Asistencia
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 1 | Ver asistencias de todos los cursos | ✅ Implementado |
| 2 | Ver asistencias por fecha | ✅ Implementado |
| 3 | Ver asistencias por curso | ✅ Implementado |
| 4 | Ver calendario de asistencias | ✅ Implementado |

### Justificaciones
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 5 | Ver todas las justificaciones pendientes | ✅ Implementado |
| 6 | Ver justificaciones aprobadas | ✅ Implementado |
| 7 | Ver justificaciones rechazadas | ✅ Implementado |
| 8 | Aprobar justificaciones | ✅ Implementado |
| 9 | Rechazar justificaciones | ✅ Implementado |
| 10 | Descargar documentos de justificación | ✅ Implementado |

### Estadísticas y Búsqueda
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 11 | Ver estadísticas de asistencia general | ✅ Implementado |
| 12 | Ver estadísticas por curso | ✅ Implementado |
| 13 | Ver top estudiantes con ausencias | ✅ Implementado |
| 14 | Buscar asistencias | ✅ Implementado |
| 15 | Buscar estudiantes | ✅ Implementado |
| 16 | Ver horarios de cursos | ✅ Implementado |

### Perfil
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 17 | Editar su propio perfil | ✅ Implementado |
| 18 | Cambiar su propia contraseña | ✅ Implementado |

### Casos Especiales
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 19 | Registrar asistencia (si dicta clase) | ⚠️ Depende (si tiene asignación docente) |

**Total Inspector: 18/19 ✅ (1 condicional)**

---

## 👨‍🏫 DOCENTE TUTOR
*Tiene rol docente + es tutor de un curso*

### Registro de Asistencia
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 1 | Ver sus clases programadas del día | ✅ Implementado |
| 2 | Registrar asistencia en sus clases | ✅ Implementado |
| 3 | Editar asistencia de sus clases (48h) | ✅ Implementado |
| 4 | Ver asistencias que registró | ✅ Implementado |

### Como Tutor del Curso
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 5 | Ver asistencias de su curso (como tutor) | ✅ Implementado |
| 6 | Ver calendario de su curso | ✅ Implementado |
| 7 | Ver horario de su curso | ✅ Implementado |
| 8 | Ver lista de estudiantes de su curso | ✅ Implementado |
| 9 | Ver estadísticas de su curso | ✅ Implementado |
| 13 | Ver representantes de sus estudiantes | ✅ Implementado |

### Justificaciones
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 10 | Ver justificaciones de su curso | ✅ Implementado |
| 11 | **Aprobar justificaciones de su curso** | ❌ **NO implementado** |
| 12 | **Rechazar justificaciones de su curso** | ❌ **NO implementado** |

### Sistema
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 14 | Ver dashboard con métricas de su curso | ✅ Implementado |
| 15 | Buscar estudiantes de su curso | ✅ Implementado |
| 16 | Editar su propio perfil | ✅ Implementado |
| 17 | Cambiar su propia contraseña | ✅ Implementado |

**Total Docente Tutor: 15/17 ✅ (2 pendientes)**

---

## 👨‍🏫 DOCENTE
*Sin ser tutor*

### Registro de Asistencia
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 1 | Ver sus clases programadas del día | ✅ Implementado |
| 2 | Registrar asistencia en sus clases | ✅ Implementado |
| 3 | Editar asistencia de sus clases (48h) | ✅ Implementado |
| 4 | Ver asistencias que registró | ✅ Implementado |

### Visualización
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 5 | Ver asistencias de cursos donde dicta | ✅ Implementado |
| 6 | Ver calendario de sus cursos | ✅ Implementado |
| 7 | Ver horario de sus clases | ✅ Implementado |
| 8 | Ver estudiantes de sus cursos | ✅ Implementado |

### Estadísticas y Sistema
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 9 | Ver estadísticas de sus cursos | ✅ Implementado |
| 10 | Ver dashboard con sus métricas | ✅ Implementado |
| 11 | Buscar estudiantes de sus cursos | ✅ Implementado |
| 12 | Editar su propio perfil | ✅ Implementado |
| 13 | Cambiar su propia contraseña | ✅ Implementado |

**Total Docente: 13/13 ✅**

---

## 🎓 ESTUDIANTE

### Visualización de Asistencia
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 1 | Ver su propia asistencia | ✅ Implementado |
| 2 | Ver calendario de su asistencia | ✅ Implementado |
| 3 | Ver estadísticas de su asistencia | ✅ Implementado |

### Información Académica
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 4 | Ver su curso y paralelo | ✅ Implementado |
| 5 | Ver horario de clases | ✅ Implementado |
| 6 | Ver sus docentes | ✅ Implementado |
| 7 | Ver quiénes son sus representantes | ✅ Implementado |

### Justificaciones
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 8 | **Justificar sus ausencias** | ✅ **Implementado** ⚠️ **REVISAR NECESIDAD** |
| 9 | **Subir documento de justificación** | ✅ **Implementado** ⚠️ **REVISAR NECESIDAD** |
| 10 | Ver estado de justificaciones enviadas | ✅ Implementado |
| 11 | Ver justificaciones aprobadas/rechazadas | ✅ Implementado |

### Sistema
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 12 | Ver dashboard con sus métricas | ✅ Implementado |
| 13 | Editar su propio perfil | ✅ Implementado |
| 14 | Cambiar su propia contraseña | ✅ Implementado |

**Total Estudiante: 14/14 ✅ (2 a revisar)**

---

## 👨‍👩‍👧 REPRESENTANTE

### Gestión de Representados
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 1 | Ver lista de sus representados | ✅ Implementado |
| 2 | Ver asistencia de cada representado | ✅ Implementado |
| 3 | Ver calendario de cada representado | ✅ Implementado |
| 4 | Ver estadísticas de cada representado | ✅ Implementado |

### Información Académica
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 5 | Ver curso de cada representado | ✅ Implementado |
| 6 | Ver horario de cada representado | ✅ Implementado |
| 7 | Ver docentes de cada representado | ✅ Implementado |

### Justificaciones
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 8 | Justificar ausencias de representados | ✅ Implementado |
| 9 | Subir documentos de justificación | ✅ Implementado |
| 10 | Ver justificaciones enviadas | ✅ Implementado |
| 11 | Ver estado de justificaciones | ✅ Implementado |

### Sistema
| # | Funcionalidad | Estado |
|---|---------------|--------|
| 12 | Ver dashboard con métricas de representados | ✅ Implementado |
| 13 | Editar su propio perfil | ✅ Implementado |
| 14 | Cambiar su propia contraseña | ✅ Implementado |

**Total Representante: 14/14 ✅**

---

## 📊 RESUMEN GENERAL

| Rol | Total Funcionalidades | ✅ Implementadas | ❌ Faltantes | ⚠️ A Revisar |
|-----|----------------------|------------------|--------------|--------------|
| **Autoridad** | 40 | 40 | 0 | 0 |
| **Inspector** | 19 | 18 | 0 | 1 |
| **Docente Tutor** | 17 | 15 | 2 | 0 |
| **Docente** | 13 | 13 | 0 | 0 |
| **Estudiante** | 14 | 14 | 0 | 2 |
| **Representante** | 14 | 14 | 0 | 0 |
| **TOTAL** | **117** | **114** | **2** | **3** |

### Cobertura: **97.4%**

---

## ⚠️ PUNTOS A REVISAR/IMPLEMENTAR

### ❌ FALTANTES (2)

#### 1. Docente Tutor - Aprobar justificaciones de su curso
- **Estado:** NO implementado
- **Pregunta:** ¿Debe implementarse?
- **Impacto:** Permitiría descentralizar aprobación de justificaciones
- **Acción sugerida:** 
  - Si SÍ → Crear lógica en `JustificationController`
  - Agregar permiso especial para tutores
  - Limitar solo a justificaciones de SU curso

#### 2. Docente Tutor - Rechazar justificaciones de su curso
- **Estado:** NO implementado
- **Pregunta:** ¿Debe implementarse?
- **Impacto:** Complementa la funcionalidad #1
- **Acción sugerida:** 
  - Si SÍ → Agregar al mismo controller
  - Mismo permiso que aprobación

---

### ⚠️ A REVISAR (3)

#### 1. Estudiante - Justificar sus propias ausencias
- **Estado:** ✅ Implementado actualmente
- **Problema detectado:** 
  - Podría prestarse para abusos
  - Estudiantes podrían justificar cualquier ausencia
  - Dificulta control institucional
- **Ventaja:**
  - Ayuda cuando padre no tiene acceso a computadora
  - Estudiantes responsables pueden gestionar
- **Opciones:**
  1. **Mantener** (con advertencia de uso responsable)
  2. **Deshabilitar** (solo representante justifica)
  3. **Requiere aprobación adicional** (estudiante envía → representante confirma → autoridad aprueba)
  4. **Limitar cantidad** (máximo X justificaciones por mes)

#### 2. Estudiante - Subir documentos de justificación
- **Estado:** ✅ Implementado actualmente
- **Problema:** Mismo que punto #1
- **Acción:** Depende de decisión del punto anterior

#### 3. Inspector - Registrar asistencia si dicta clase
- **Estado:** ⚠️ Depende
- **Situación actual:**
  - SI tiene asignación como docente → SÍ puede registrar
  - NO tiene asignación → NO puede registrar
- **Lógica:** Correcta según rol híbrido
- **Acción:** Mantener como está

---

## 🎯 RECOMENDACIONES

### Prioridad ALTA
1. **Decidir sobre justificaciones de estudiantes**
   - Analizar casos de uso reales
   - Consultar con autoridades educativas
   - Implementar solución definitiva

2. **Implementar aprobación de justificaciones por tutor**
   - Descentraliza carga de trabajo
   - Tutor conoce mejor a sus estudiantes
   - Acelera proceso de aprobación

### Prioridad MEDIA
3. **Agregar logs de justificaciones**
   - Quién justificó (estudiante/representante)
   - Quién aprobó (autoridad/inspector/tutor)
   - Auditoría completa

4. **Notificaciones automáticas**
   - Cuando se justifica → notificar tutor/autoridad
   - Cuando se aprueba → notificar estudiante/representante

### Prioridad BAJA
5. **Estadísticas de justificaciones**
   - Estudiantes con más justificaciones
   - Porcentaje de aprobación
   - Motivos más frecuentes

---

## 📝 NOTAS FINALES

- Sistema altamente funcional con **97.4% de cobertura**
- Faltantes son **decisiones de diseño**, no errores técnicos
- Arquitectura permite agregar funcionalidades fácilmente
- Código preparado para escalar

---

**Documento generado:** 16 de Febrero de 2026  
**Sistema:** EcuAsist 2026 v1.5  
**Autor:** Análisis de funcionalidades por rol