<?php Security::requireLogin(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Representantes - EcuAsist</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .navbar { background: #007bff; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { font-size: 24px; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 1000px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { padding: 12px 30px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #218838; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .badge { padding: 3px 8px; border-radius: 3px; font-size: 11px; background: #007bff; color: white; }
        .badge-primary { background: #ffc107; color: #333; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Gestión de Representantes</h1>
        <div>
            <a href="?action=dashboard">← Dashboard</a>
            <a href="?action=logout">Cerrar sesión</a>
        </div>
    </div>

    <div class="container">
        <?php if(isset($_GET['success'])): ?>
            <div class="success">✓ Relación asignada correctamente</div>
        <?php endif; ?>

        <div class="card">
            <h2>Asignar Estudiante a Representante</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Representante</label>
                    <select name="representative_id" required>
                        <option value="">Seleccionar...</option>
                        <?php foreach($representatives as $rep): ?>
                            <option value="<?= $rep['id'] ?>">
                                <?= $rep['last_name'] . ' ' . $rep['first_name'] ?> (<?= $rep['dni'] ?? 'Sin cédula' ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Estudiante</label>
                    <select name="student_id" required>
                        <option value="">Seleccionar...</option>
                        <?php foreach($students as $student): ?>
                            <option value="<?= $student['id'] ?>">
                                <?= $student['last_name'] . ' ' . $student['first_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Parentesco</label>
                    <select name="relationship" required>
                        <option value="">Seleccionar...</option>
                        <option value="Padre">Padre</option>
                        <option value="Madre">Madre</option>
                        <option value="Tutor Legal">Tutor Legal</option>
                        <option value="Abuelo/a">Abuelo/a</option>
                        <option value="Tío/a">Tío/a</option>
                        <option value="Hermano/a">Hermano/a</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_primary" value="1" style="width: auto; margin-right: 5px;">
                        Representante Principal
                    </label>
                </div>

                <button type="submit">Asignar</button>
            </form>
        </div>

        <div class="card">
            <h2>Representantes y sus Estudiantes</h2>
            <?php foreach($representatives as $rep): 
                $children = $this->representativeModel->getStudentsByRepresentative($rep['id']);
                if(count($children) > 0):
            ?>
            <div style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 4px;">
                <h3 style="margin-bottom: 10px;">
                    <?= $rep['last_name'] . ' ' . $rep['first_name'] ?>
                    <small style="color: #666;">(<?= $rep['email'] ?>)</small>
                </h3>
                <table>
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Parentesco</th>
                            <th>Curso</th>
                            <th>Principal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($children as $child): ?>
                        <tr>
                            <td><?= $child['last_name'] . ' ' . $child['first_name'] ?></td>
                            <td><?= $child['relationship'] ?></td>
                            <td><?= $child['course_name'] ?? 'Sin curso' ?></td>
                            <td>
                                <?php if($child['is_primary']): ?>
                                    <span class="badge badge-primary">Principal</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; endforeach; ?>
        </div>
    </div>
</body>
</html>