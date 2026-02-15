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
        $this->institutionModel = new Institution($db);
        $this->institutionShiftModel = new InstitutionShift($db);
        $this->shiftModel = new Shift($db);
    }

    public function index() {
        $institution = $this->institutionModel->getById(1); // Por ahora solo una institución
        $allShifts = $this->shiftModel->getAll();
        $assignedShiftIds = $this->institutionShiftModel->getInstitutionShiftIds(1);

        include BASE_PATH . '/views/institution/index.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $logoPath = null;
            
            // Subir logo si existe
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
                $uploadDir = BASE_PATH . '/uploads/institution/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                $filename = 'logo_' . time() . '.' . $extension;
                $uploadPath = $uploadDir . $filename;

                if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadPath)) {
                    $logoPath = 'uploads/institution/' . $filename;
                    
                    // Eliminar logo anterior si existe
                    $current = $this->institutionModel->getById(1);
                    if ($current['logo_path'] && file_exists(BASE_PATH . '/' . $current['logo_path'])) {
                        unlink(BASE_PATH . '/' . $current['logo_path']);
                    }
                }
            }

            $data = [
                'name' => Security::sanitize($_POST['name']),
                'address' => Security::sanitize($_POST['address']),
                'province' => Security::sanitize($_POST['province']),
                'city' => Security::sanitize($_POST['city']),
                'phone' => Security::sanitize($_POST['phone']),
                'email' => Security::sanitize($_POST['email']),
                'director_name' => Security::sanitize($_POST['director_name']),
                'amie_code' => Security::sanitize($_POST['amie_code']),
                'website' => Security::sanitize($_POST['website']),
                'logo_path' => $logoPath
            ];

            $this->institutionModel->update(1, $data);
            header('Location: ?action=institution&success=1');
            exit;
        }
    }

    public function assignShift() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                ':institution_id' => 1,
                ':shift_id' => (int)$_POST['shift_id']
            ];

            $this->institutionShiftModel->assign($data);
            header('Location: ?action=institution&shift_assigned=1');
            exit;
        }
    }

    public function removeShift() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $shiftId = (int)$_POST['shift_id'];
            $this->institutionShiftModel->remove(1, $shiftId);
            header('Location: ?action=institution&shift_removed=1');
            exit;
        }
    }
}