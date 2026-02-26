<?php

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/models/Institution.php';
require_once BASE_PATH . '/models/InstitutionShift.php';
require_once BASE_PATH . '/models/Shift.php';

class InstitutionController {
    private $institutionModel;
    private $institutionShiftModel;
    private $shiftModel;

    public function __construct() {
        Security::requireLogin();
        if (!Security::hasRole('autoridad')) {
            die('Acceso denegado');
        }

        $db = new Database();
        $this->institutionModel      = new Institution($db);
        $this->institutionShiftModel = new InstitutionShift($db);
        $this->shiftModel            = new Shift($db);
    }

    public function index() {
        $institution      = $this->institutionModel->getById(1);
        $allShifts        = $this->shiftModel->getAll();
        $assignedShiftIds = $this->institutionShiftModel->getInstitutionShiftIds(1);
        
        include BASE_PATH . '/views/institution/index.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $current  = $this->institutionModel->getById(1);
        // Mantener logo actual — triple respaldo: BD → hidden → null
        $logoPath = !empty($current['logo_path'])
                    ? $current['logo_path']
                    : (!empty($_POST['current_logo_path']) ? $_POST['current_logo_path'] : null);

        // Solo procesar si realmente viene un archivo
        $hasFile = isset($_FILES['logo'])
                   && is_array($_FILES['logo'])
                   && $_FILES['logo']['error'] === UPLOAD_ERR_OK
                   && $_FILES['logo']['size'] > 0;

        if ($hasFile) {
            $uploadDir = BASE_PATH . '/uploads/institution/';

            // Crear carpeta si no existe (permisos 0775 para hosting compartido)
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            $ext     = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $maxSize = 2 * 1024 * 1024;

            if (in_array($ext, $allowed) && $_FILES['logo']['size'] <= $maxSize) {
                $filename   = 'logo_' . $_SESSION['institution_id'] . '_' . time() . '.' . $ext;
                $uploadPath = $uploadDir . $filename;

                if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadPath)) {
                    // Eliminar logo anterior solo si existe físicamente
                    if (!empty($current['logo_path'])) {
                        $oldFile = BASE_PATH . '/' . ltrim($current['logo_path'], '/');
                        if (file_exists($oldFile)) {
                            @unlink($oldFile);
                        }
                    }
                    $logoPath = 'uploads/institution/' . $filename;
                }
                // Si move_uploaded_file falla: $logoPath mantiene el valor anterior
            }
        }

        $data = [
            'name'         => Security::sanitize($_POST['name']),
            'address'      => Security::sanitize($_POST['address']),
            'province'     => Security::sanitize($_POST['province']),
            'city'         => Security::sanitize($_POST['city']),
            'phone'        => Security::sanitize($_POST['phone']),
            'email'        => Security::sanitize($_POST['email']),
            'director_name'=> Security::sanitize($_POST['director_name']),
            'amie_code'    => Security::sanitize($_POST['amie_code']),
            'website'      => Security::sanitize($_POST['website']),
            'logo_path'         => $logoPath,
            'working_days_list' => json_encode(array_values($_POST['working_days'] ?? ['lunes','martes','miercoles','jueves','viernes'])),
        ];

        $this->institutionModel->update(1, $data);
        header('Location: ?action=institution&success=1');
        exit;
    }

    // Toggle: si está asignada la quita, si no la asigna — responde JSON para AJAX
    public function toggleShift() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Método no permitido']); exit;
        }

        $shiftId          = (int)$_POST['shift_id'];
        $assignedShiftIds = $this->institutionShiftModel->getInstitutionShiftIds(1);

        if (in_array($shiftId, $assignedShiftIds)) {
            $this->institutionShiftModel->remove(1, $shiftId);
            echo json_encode(['action' => 'removed', 'shift_id' => $shiftId]);
        } else {
            $this->institutionShiftModel->assign([
                ':institution_id' => 1,
                ':shift_id'       => $shiftId,
            ]);
            echo json_encode(['action' => 'assigned', 'shift_id' => $shiftId]);
        }
        exit;
    }

    // Mantener compatibilidad con rutas antiguas
    public function assignShift() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->institutionShiftModel->assign([
                ':institution_id' => 1,
                ':shift_id'       => (int)$_POST['shift_id'],
            ]);
            header('Location: ?action=institution&success=1');
            exit;
        }
    }

    public function removeShift() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->institutionShiftModel->remove(1, (int)$_POST['shift_id']);
            header('Location: ?action=institution&success=1');
            exit;
        }
    }
}