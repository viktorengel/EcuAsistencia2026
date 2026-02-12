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

        $command = sprintf(
            'mysqldump --host=%s --user=%s --password=%s %s > %s',
            escapeshellarg($this->dbHost),
            escapeshellarg($this->dbUser),
            escapeshellarg($this->dbPass),
            escapeshellarg($this->dbName),
            escapeshellarg($filepath)
        );

        exec($command, $output, $result);

        if ($result === 0 && file_exists($filepath)) {
            return $filename;
        }

        return false;
    }

    public function getBackups() {
        $files = glob($this->backupDir . 'backup_*.sql');
        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'name' => basename($file),
                'path' => $file,
                'size' => filesize($file),
                'date' => filemtime($file)
            ];
        }

        usort($backups, function($a, $b) {
            return $b['date'] - $a['date'];
        });

        return $backups;
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
}