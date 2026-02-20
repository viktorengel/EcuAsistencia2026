📘 FLUJO CORREGIDO PARA INICIAR SISTEMA DE CONTROL DE ASISTENCIA
🎯 Configuración Inicial desde Cero
1️⃣ Configurar la Institución

Ruta: Administración → Configuración General
Nombre de la institución
Código AMIE
Dirección (provincia, ciudad)
Teléfono, email, sitio web
Nombre del director
Logo (opcional)
Jornadas (matutina / vespertina / nocturna)
⚠ Recomendación: Las jornadas deben ser un catálogo independiente, no un simple campo dentro de institución.

2️⃣ Crear el Año Lectivo

Ruta: Administración → Configuración Académica → Años Lectivos
Nombre (Ej: 2025–2026)
Fecha inicio
Fecha fin
Estado (activo / inactivo)
⚠ Debe existir únicamente un año lectivo activo.

3️⃣ Crear Catálogos Base

Ruta: Administración → Configuración Académica
a) Niveles
EGB Básica
BGU
Otros

b) Asignaturas
Matemáticas
Lengua
Ciencias Naturales
Inglés
Etc.

⚠ El nivel debe existir antes de crear los cursos.

4️⃣ Crear Cursos

Ruta: Administración → Cursos
Cada curso debe contener:
Nivel (relación)
Paralelo
Jornada
Año lectivo
Ejemplo estructurado:
Nivel: 8vo EGB
Paralelo: A
Jornada: Matutina
Año lectivo: 2025–2026

⚠ No incluir el año en el nombre del curso. Debe relacionarse por clave foránea.

5️⃣ Crear Usuarios

Ruta: Administración → Gestión de Usuarios
Registrar todos los usuarios primero:
Docentes
Estudiantes
Representantes
Inspectores
Autoridades

6️⃣ Asignar Roles a Usuarios

Ruta: Administración → Roles
Un usuario puede tener uno o varios roles:
Docente
Estudiante
Representante
Inspector
Autoridad
Administrador

✔ Esto permite mayor flexibilidad futura.

7️⃣ Matricular Estudiantes

Ruta: Administración → Matriculación

Relación:
Estudiante
Curso
Año lectivo

Estado (activo / retirado / trasladado)

⚠ La matrícula debe estar en una tabla independiente.

8️⃣ Asignar Docente a Curso y Asignatura

Ruta: Administración → Carga Académica

Relación:
Docente
Curso
Asignatura
Año lectivo

✔ Aquí realmente se define la clase académica.

9️⃣ Crear Horarios

Ruta: Administración → Horarios

Debe basarse en:
Curso
Día
Hora inicio / fin
Relación de carga académica

⚠ El docente no se asigna directamente en horario.
Ya viene desde la carga académica.

🔟 Asignar Tutor

Ruta: Administración → Tutorías

Curso
Docente
Año lectivo
Permisos de aprobación

✔ La regla de aprobación de días debe ser configurable.

✅ Sistema Listo para Operar
Flujo diario del sistema:
Docente ingresa y visualiza su horario del día.
Registra asistencia por hora.
El sistema consolida faltas por día automáticamente.
Estudiante o representante envía justificación.
Tutor o inspector aprueba según reglas.
El sistema recalcula estado de asistencia.

🚀 Mejoras Recomendadas
✔ Parametrización de Reglas de Asistencia
Número máximo de faltas por materia
Porcentaje para pérdida de año
Días máximos de justificación por rol
✔ Consolidación Automática
Si un estudiante falta todas las horas del día, marcar falta diaria automáticamente.
✔ Bitácora de Cambios
Registrar:
Quién modificó asistencia
Quién aprobó justificación
Fecha y hora
Esto es clave para auditorías institucionales.

📌 Conclusión

El sistema debe construirse con:
Catálogos separados
Relaciones bien definidas
Reglas parametrizables
Control de integridad y trazabilidad
Con esta estructura el sistema ya puede escalar a modelo SaaS educativo sin problemas.