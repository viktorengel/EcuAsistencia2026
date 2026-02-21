Sistema de Gestión de Representantes — Resumen de Cambios

🔒 Validación de parentesco exclusivo
Un estudiante no puede tener dos representantes con el mismo parentesco Padre o Madre. La validación opera en dos capas: modelo PHP y triggers en base de datos. Se corrigió además el bug del ON DUPLICATE KEY UPDATE que saltaba la validación.

🔄 Toggle Principal / Secundario
Botón en cada fila para cambiar el tipo de representante. Al marcar uno como Principal, los demás del mismo estudiante pasan automáticamente a Secundario. El botón muestra la acción opuesta al estado actual.

✏️ Editar relación
Se agregó botón de lápiz junto a la X de eliminar. Abre un modal con parentesco y tipo precargados para corregir sin necesidad de eliminar y volver a crear.

📋 Orden del formulario
Se reordenaron los campos a: Representante → Parentesco → Estudiante, que es el flujo más natural al registrar.

🔔 Sistema de Toast
Se reemplazaron los mensajes alert estáticos por notificaciones flotantes en la esquina superior derecha que desaparecen solos a los 4 segundos. La URL se limpia después de mostrar el toast para evitar que se repita al recargar.

📝 Formulario conserva datos en error
Cuando hay un error de validación, el formulario mantiene los valores seleccionados para no tener que volver a llenarlos.

🗑️ Eliminación de Asignaciones Docente-Materia
Se removió el ítem del menú en desktop y móvil. Las vistas a eliminar manualmente son views/assignments/index.php y views/assignments/view_course.php.

🔧 Rutas y configuración
Se corrigió el sistema de rutas para local y producción usando detección automática en env.php y index.php, eliminando la necesidad de cambiar rutas al desplegar.