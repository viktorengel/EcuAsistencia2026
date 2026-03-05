<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Asistencias - EcuAsist</title>
</head>
<body>

<?php include BASE_PATH . '/views/partials/navbar.php'; ?>

<div class="breadcrumb">
    <a href="?action=dashboard">🏠 Inicio</a> &rsaquo;
    Asistencia &rsaquo;
    Ver Asistencias
</div>

<div class="container">

    <!-- Header -->
    <div class="page-header" style="background:linear-gradient(135deg,#1565c0,#1976d2);">
        <div class="ph-icon">📋</div>
        <div>
            <h1>Ver Asistencias</h1>
            <p>Consulta el registro de asistencia por curso y fecha</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="panel" style="margin-bottom:20px;">
        <h3 style="margin-bottom:16px;font-size:.95rem;color:#555;">🔍 Filtros de Búsqueda</h3>
        <form method="POST" id="filterForm">
            <div class="form-row" style="align-items:flex-end;">
                <div class="form-group" style="flex:2;">
                    <label>Curso</label>
                    <select name="course_id" class="form-control" required>
                        <option value="">Seleccionar curso...</option>
                        <?php foreach($courses as $course): ?>
                            <option value="<?= $course['id'] ?>"
                                <?= (isset($_POST['course_id']) && $_POST['course_id'] == $course['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($course['name']) ?>
                                — <?= ucfirst($course['shift_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="flex:1;">
                    <label>Fecha</label>
                    <input type="date" name="date" class="form-control"
                           value="<?= isset($_POST['date']) ? htmlspecialchars($_POST['date']) : date('Y-m-d') ?>"
                           max="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="form-group" style="flex:0 0 auto;">
                    <button type="submit" class="btn btn-primary">🔍 Buscar</button>
                </div>
            </div>
        </form>
    </div>

    <?php if(isset($_GET['edited'])): ?>
        <div class="alert alert-success" style="margin-bottom:16px;">✓ Asistencia actualizada correctamente</div>
    <?php endif; ?>

    <?php if(isset($_GET['edited'])): ?>
        <div class="alert alert-success" style="margin-bottom:16px;">✓ Asistencia actualizada correctamente</div>
    <?php endif; ?>
    <?php if($_SERVER['REQUEST_METHOD'] === 'POST'): ?>

        <?php if(empty($attendances)): ?>
            <!-- Sin resultados -->
            <div class="empty-state">
                <div class="icon">📭</div>
                <p>No se encontraron registros de asistencia para los filtros seleccionados.</p>
                <small style="color:#aaa;">Verifica que el docente haya registrado asistencia para este curso y fecha.</small>
            </div>

        <?php else:
            /* ---- Calcular estadísticas ---- */
            $stats = ['presente'=>0,'ausente'=>0,'tardanza'=>0,'justificado'=>0];
            $students = [];
            foreach($attendances as $att) {
                $s = $att['status'];
                if(isset($stats[$s])) $stats[$s]++;
                $students[$att['last_name'].' '.$att['first_name']] = true;
            }
            $total      = count($attendances);
            $totalStu   = count($students);
            $pctPresente = $total > 0 ? round(($stats['presente'] + $stats['justificado']) / $total * 100) : 0;

            $selectedCourse = '';
            foreach($courses as $c) {
                if($c['id'] == $_POST['course_id']) {
                    $selectedCourse = $c['name'] . ' — ' . ucfirst($c['shift_name']);
                    break;
                }
            }
        ?>

        <!-- Stat cards -->
        <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:16px;margin-bottom:20px;">
            <div class="stat-card">
                <div class="stat-icon" style="background:#e3f2fd;">📊</div>
                <div class="stat-value"><?= $total ?></div>
                <div class="stat-label">Total registros</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#e8f5e9;">✅</div>
                <div class="stat-value" style="color:#2e7d32;"><?= $stats['presente'] ?></div>
                <div class="stat-label">Presentes</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#ffebee;">❌</div>
                <div class="stat-value" style="color:#c62828;"><?= $stats['ausente'] ?></div>
                <div class="stat-label">Ausentes</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#fffde7;">⏰</div>
                <div class="stat-value" style="color:#f57f17;"><?= $stats['tardanza'] ?></div>
                <div class="stat-label">Tardanzas</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#e0f7fa;">📝</div>
                <div class="stat-value" style="color:#00838f;"><?= $stats['justificado'] ?></div>
                <div class="stat-label">Justificados</div>
            </div>
        </div>

        <!-- Barra de asistencia global -->
        <div class="panel" style="margin-bottom:20px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                <span style="font-size:.9rem;color:#555;">
                    📚 <strong><?= htmlspecialchars($selectedCourse) ?></strong>
                    &nbsp;|&nbsp; 📅 <?= date('d/m/Y', strtotime($_POST['date'])) ?>
                    &nbsp;|&nbsp; 👥 <?= $totalStu ?> estudiante<?= $totalStu!=1?'s':'' ?>
                </span>
                <span style="font-weight:700;font-size:1.1rem;color:<?= $pctPresente>=90?'#2e7d32':($pctPresente>=75?'#f57f17':'#c62828') ?>">
                    <?= $pctPresente ?>% asistencia efectiva
                </span>
            </div>
            <div style="background:#f0f0f0;border-radius:6px;height:10px;overflow:hidden;">
                <div style="height:100%;border-radius:6px;width:<?= $pctPresente ?>%;
                    background:<?= $pctPresente>=90?'#4caf50':($pctPresente>=75?'#ff9800':'#f44336') ?>;
                    transition:width .4s;">
                </div>
            </div>
        </div>

        <!-- Tabla de resultados -->
        <div class="table-wrap">
            <div class="table-info">
                <span>📋 <strong>Registros de Asistencia</strong> — <?= $total ?></span>
                <div style="display:flex;gap:8px;align-items:center;">
                    <input type="text" id="searchTable" class="form-control"
                           placeholder="🔎 Buscar estudiante..."
                           style="width:200px;font-size:13px;"
                           oninput="filterTable(this.value)">
                </div>
            </div>
            <table id="attendanceTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Estudiante</th>
                        <th>Asignatura</th>
                        <th>Docente</th>
                        <th style="text-align:center;">Hora</th>
                        <th style="text-align:center;">Estado</th>
                        <th>Observación</th>
                        <th style="text-align:center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    foreach($attendances as $att):
                        $s = $att['status'];
                        $badgeMap = [
                            'presente'    => 'badge-green',
                            'ausente'     => 'badge-red',
                            'tardanza'    => 'badge-yellow',
                            'justificado' => 'badge-teal',
                        ];
                        $labelMap = [
                            'presente'    => '✓ Presente',
                            'ausente'     => '✗ Ausente',
                            'tardanza'    => '⏰ Tardanza',
                            'justificado' => '📝 Justificado',
                        ];
                        $badge = $badgeMap[$s] ?? 'badge-gray';
                        $label = $labelMap[$s] ?? ucfirst($s);
                    ?>
                    <tr>
                        <td style="color:#999;font-size:12px;"><?= $i++ ?></td>
                        <td>
                            <strong><?= htmlspecialchars($att['last_name'] . ' ' . $att['first_name']) ?></strong>
                        </td>
                        <td style="color:#555;"><?= htmlspecialchars($att['subject_name']) ?></td>
                        <td style="font-size:13px;color:#666;"><?= htmlspecialchars($att['teacher_name']) ?></td>
                        <td style="text-align:center;">
                            <span style="font-size:13px;background:#f0f0f0;padding:3px 8px;border-radius:4px;">
                                <?= htmlspecialchars($att['hour_period']) ?>
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <span class="badge <?= $badge ?>"><?= $label ?></span>
                        </td>
                        <td style="font-size:13px;color:#777;">
                            <?= $att['observation'] ? htmlspecialchars($att['observation']) : '<span style="color:#ccc;">—</span>' ?>
                        </td>
                        <td style="text-align:center;">
                            <?php
                            $createdAt  = strtotime($att['created_at']);
                            $hoursLimit = defined('EDIT_ATTENDANCE_HOURS') ? EDIT_ATTENDANCE_HOURS : 48;
                            $canEdit    = (time() - $createdAt) <= ($hoursLimit * 3600);
                            $isOwner    = ($att['teacher_id'] == $_SESSION['user_id']) || Security::hasRole('autoridad');
                            ?>
                            <?php if ($canEdit && $isOwner): ?>
                                <button type="button"
                                    class="btn-edit-att"
                                    data-id="<?= $att['id'] ?>"
                                    data-name="<?= htmlspecialchars($att['last_name'].' '.$att['first_name'], ENT_QUOTES) ?>"
                                    data-status="<?= $att['status'] ?>"
                                    data-obs="<?= htmlspecialchars($att['observation'] ?? '', ENT_QUOTES) ?>"
                                    title="Editar registro">
                                    ✏️ Editar
                                </button>
                            <?php elseif (!$canEdit): ?>
                                <span style="font-size:11px;color:#bbb;" title="Superó el límite de <?= $hoursLimit ?>h para editar">🔒 Cerrado</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Leyenda -->
        <div style="margin-top:12px;display:flex;gap:12px;flex-wrap:wrap;">
            <span class="badge badge-green">✓ Presente</span>
            <span class="badge badge-red">✗ Ausente</span>
            <span class="badge badge-yellow">⏰ Tardanza</span>
            <span class="badge badge-teal">📝 Justificado</span>
            <span style="font-size:12px;color:#999;margin-left:8px;">✏️ Editable hasta <?= defined('EDIT_ATTENDANCE_HOURS') ? EDIT_ATTENDANCE_HOURS : 48 ?>h después del registro</span>
        </div>

        <?php endif; ?>
    <?php endif; ?>

</div>

<!-- ── Modal Editar Asistencia ─────────────────────────────── -->
<div id="modalEditAtt" style="display:none;position:fixed;inset:0;z-index:3000;background:rgba(0,0,0,0.6);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:12px;width:90%;max-width:440px;overflow:hidden;box-shadow:0 10px 40px rgba(0,0,0,0.3);">
        <!-- Header -->
        <div style="background:linear-gradient(135deg,#1565c0,#1976d2);color:#fff;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;">
            <span style="font-weight:700;font-size:15px;">✏️ Editar Asistencia</span>
            <button onclick="closeEditModal()" style="background:none;border:none;color:#fff;font-size:20px;cursor:pointer;line-height:1;">×</button>
        </div>
        <!-- Body -->
        <form method="POST" action="?action=edit_attendance" id="formEditAtt">
            <div style="padding:20px;">
                <input type="hidden" name="csrf_token" value="<?= Security::generateToken() ?>">
                <input type="hidden" name="attendance_id" id="edit_att_id">
                <!-- Preservar filtros actuales para redirigir al mismo resultado -->
                <input type="hidden" name="back_course" value="<?= htmlspecialchars($_POST['course_id'] ?? '') ?>">
                <input type="hidden" name="back_date"   value="<?= htmlspecialchars($_POST['date'] ?? '') ?>">

                <p style="font-size:13px;color:#555;margin-bottom:16px;">
                    Estudiante: <strong id="edit_att_name"></strong>
                </p>

                <div style="margin-bottom:14px;">
                    <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px;">Estado</label>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                        <?php foreach([
                            'presente'    => ['✓ Presente',    '#e8f5e9','#2e7d32'],
                            'ausente'     => ['✗ Ausente',     '#ffebee','#c62828'],
                            'tardanza'    => ['⏰ Tardanza',    '#fffde7','#f57f17'],
                            'justificado' => ['📝 Justificado', '#e0f7fa','#00838f'],
                        ] as $val => [$lbl, $bg, $color]): ?>
                        <label style="cursor:pointer;">
                            <input type="radio" name="status" value="<?= $val ?>" id="st_<?= $val ?>" style="display:none;">
                            <div class="status-opt" data-val="<?= $val ?>"
                                 style="border:2px solid #e0e0e0;border-radius:8px;padding:10px;text-align:center;font-size:13px;font-weight:600;background:#fff;color:#555;transition:all .15s;">
                                <?= $lbl ?>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div style="margin-bottom:16px;">
                    <label style="font-size:13px;font-weight:600;display:block;margin-bottom:6px;">Observación <span style="font-weight:400;color:#999;">(opcional)</span></label>
                    <textarea name="observation" id="edit_att_obs" rows="2"
                              style="width:100%;border:1px solid #ddd;border-radius:6px;padding:8px;font-size:13px;resize:vertical;"
                              placeholder="Ej: Llegó 10 minutos tarde..."></textarea>
                </div>
            </div>
            <!-- Footer -->
            <div style="padding:12px 20px;background:#f8f9fa;border-top:1px solid #eee;display:flex;justify-content:flex-end;gap:8px;">
                <button type="button" onclick="closeEditModal()" class="btn btn-outline btn-sm">Cancelar</button>
                <button type="submit" class="btn btn-primary btn-sm">💾 Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

<style>
.btn-edit-att {
    background: #e3f2fd; color: #1565c0; border: 1px solid #90caf9;
    border-radius: 6px; padding: 4px 10px; font-size: 12px;
    cursor: pointer; font-weight: 600;
}
.btn-edit-att:hover { background: #1565c0; color: #fff; }
.status-opt.selected { border-color: var(--sc) !important; background: var(--sb) !important; color: var(--sc) !important; }
</style>

<script>
function filterTable(q) {
    q = q.toLowerCase();
    const rows = document.querySelectorAll('#attendanceTable tbody tr');
    rows.forEach(r => {
        const name = r.cells[1] ? r.cells[1].textContent.toLowerCase() : '';
        r.style.display = name.includes(q) ? '' : 'none';
    });
}

var STATUS_COLORS = {
    'presente':    {bg:'#e8f5e9', color:'#2e7d32'},
    'ausente':     {bg:'#ffebee', color:'#c62828'},
    'tardanza':    {bg:'#fffde7', color:'#f57f17'},
    'justificado': {bg:'#e0f7fa', color:'#00838f'},
};

// Delegación de eventos — evita onclick inline con datos especiales
document.addEventListener('click', function(e) {
    var btn = e.target.closest('.btn-edit-att');
    if (btn) {
        openEditModal(
            btn.dataset.id,
            btn.dataset.name,
            btn.dataset.status,
            btn.dataset.obs
        );
    }
});

function openEditModal(id, name, status, obs) {
    document.getElementById('edit_att_id').value       = id;
    document.getElementById('edit_att_name').textContent = name;
    document.getElementById('edit_att_obs').value      = obs;

    // Seleccionar estado actual
    document.querySelectorAll('.status-opt').forEach(function(el) {
        var val = el.dataset.val;
        el.classList.remove('selected');
        el.style.removeProperty('--sc');
        el.style.removeProperty('--sb');
        el.style.borderColor = '#e0e0e0';
        el.style.background  = '#fff';
        el.style.color       = '#555';
        if (val === status) {
            var c = STATUS_COLORS[val];
            el.style.borderColor = c.color;
            el.style.background  = c.bg;
            el.style.color       = c.color;
            document.getElementById('st_' + val).checked = true;
        }
    });

    // Click en opciones
    document.querySelectorAll('.status-opt').forEach(function(el) {
        el.onclick = function() {
            document.querySelectorAll('.status-opt').forEach(function(x) {
                x.style.borderColor = '#e0e0e0';
                x.style.background  = '#fff';
                x.style.color       = '#555';
            });
            var c = STATUS_COLORS[el.dataset.val];
            el.style.borderColor = c.color;
            el.style.background  = c.bg;
            el.style.color       = c.color;
            document.getElementById('st_' + el.dataset.val).checked = true;
        };
    });

    var m = document.getElementById('modalEditAtt');
    m.style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('modalEditAtt').style.display = 'none';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeEditModal();
});
</script>

</body>
</html>