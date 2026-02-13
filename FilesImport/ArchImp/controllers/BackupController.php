<?php
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/helpers/Backup.php';

class BackupController {
    private $backup;

    public function __construct() {
        Security::requireLogin();
        if (!Security::hasRole('autoridad')) {
            die('Acceso denegado');
        }

        $this->backup = new Backup();
    }

    public function index() {
        $backups = $this->backup->getBackups();
        include BASE_PATH . '/views/backup/index.php';
    }

    public function create() {
        $filename = $this->backup->createBackup();
        
        if ($filename) {
            header('Location: ?action=backups&success=1&file=' . $filename);
        } else {
            header('Location: ?action=backups&error=1');
        }
        exit;
    }

    public function download() {
        $filename = $_GET['file'] ?? '';
        $this->backup->downloadBackup($filename);
    }

    public function cleanup() {
        $deleted = $this->backup->deleteOldBackups(30);
        header('Location: ?action=backups&cleanup=' . $deleted);
        exit;
    }
}