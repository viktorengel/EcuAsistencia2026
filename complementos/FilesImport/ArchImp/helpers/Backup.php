<?php
class Backup {
    private $dbHost = 'localhost';
    private $dbUser = 'root';
    private $dbPass = '';
    private $dbName = 'ecuasistencia2026_db';
    private $backupDir;

    public function __construct() {
        $this->backupDir = BASE_PATH . '/backups/';
        if (!file_exists($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }

    public function createBackup() {
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $filepath = $this->backupDir . $filename;

        // Verificar que mysqldump esté disponible
        $mysqldumpPath = 'C:\\xampp\\mysql\\bin\\mysqldump.exe'; // XAMPP Windows
        if (!file_exists($mysqldumpPath)) {
            $mysqldumpPath = 'mysqldump'; // Linux/Mac o PATH configurado
        }

        // Comando mejorado con opciones
        if (empty($this->dbPass)) {
            $command = sprintf(
                '"%s" --host=%s --user=%s --skip-password %s > "%s" 2>&1',
                $mysqldumpPath,
                $this->dbHost,
                $this->dbUser,
                $this->dbName,
                $filepath
            );
        } else {
            $command = sprintf(
                '"%s" --host=%s --user=%s --password=%s %s > "%s" 2>&1',
                $mysqldumpPath,
                $this->dbHost,
                $this->dbUser,
                $this->dbPass,
                $this->dbName,
                $filepath
            );
        }

        exec($command, $output, $result);

        // Verificar que el archivo existe y tiene contenido
        if (file_exists($filepath) && filesize($filepath) > 0) {
            return $filename;
        }

        // Si falló, intentar eliminar archivo vacío
        if (file_exists($filepath)) {
            unlink($filepath);
        }

        return false;
    }

    public function getBackups() {
        $files = glob($this->backupDir . 'backup_*.sql');
        $backups = [];

        foreach ($files as $file) {
            $size = filesize($file);
            $backups[] = [
                'filename' => basename($file),
                'path' => $file,
                'size' => $this->formatBytes($size),
                'date' => date('d/m/Y H:i:s', filemtime($file)),
                'timestamp' => filemtime($file)
            ];
        }

        usort($backups, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });

        return $backups;
    }

    private function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function deleteOldBackups($daysToKeep = 30) {
        $files = glob($this->backupDir . 'backup_*.sql');
        $deleted = 0;

        foreach ($files as $file) {
            if (time() - filemtime($file) > ($daysToKeep * 24 * 60 * 60)) {
                unlink($file);
                $deleted++;
            }
        }

        return $deleted;
    }

    public function downloadBackup($filename) {
        $filepath = $this->backupDir . $filename;

        if (!file_exists($filepath)) {
            return false;
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    }

    public function deleteBackup($filename) {
        // Validar que sea un archivo de backup válido
        if (!preg_match('/^backup_[\d\-_]+\.sql$/', $filename)) {
            return false;
        }

        $filepath = $this->backupDir . $filename;

        if (file_exists($filepath)) {
            return unlink($filepath);
        }

        return false;
    }
}