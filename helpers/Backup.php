<?php
class Backup {
    private $pdo;
    private $backupDir;

    public function __construct() {
        $this->backupDir = BASE_PATH . '/backups/';
        if (!file_exists($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
        // Reutilizar conexión PDO del sistema (usa las credenciales de database.php)
        $db        = new Database();
        $this->pdo = $db->connect();
    }

    public function createBackup() {
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $filepath = $this->backupDir . $filename;

        try {
            $pdo = $this->pdo;

            // Obtener nombre real de la BD
            $dbName = $pdo->query("SELECT DATABASE()")->fetchColumn();

            $sql  = "-- ============================================\n";
            $sql .= "-- EcuAsist 2026 - Respaldo de Base de Datos\n";
            $sql .= "-- Base de datos: {$dbName}\n";
            $sql .= "-- Fecha: " . date('Y-m-d H:i:s') . "\n";
            $sql .= "-- ============================================\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
            $sql .= "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n";
            $sql .= "SET NAMES utf8mb4;\n";
            $sql .= "SET CHARACTER SET utf8mb4;\n\n";

            // Obtener todas las tablas
            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

            foreach ($tables as $table) {
                $sql .= "-- --------------------------------------------\n";
                $sql .= "-- Tabla: `{$table}`\n";
                $sql .= "-- --------------------------------------------\n";

                // Estructura
                $createRow = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(PDO::FETCH_NUM);
                $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $sql .= $createRow[1] . ";\n\n";

                // Datos
                $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($rows)) {
                    // Insertar en bloques de 100 filas para evitar queries enormes
                    $chunks = array_chunk($rows, 100);
                    $cols   = '`' . implode('`, `', array_keys($rows[0])) . '`';

                    foreach ($chunks as $chunk) {
                        $sql .= "INSERT INTO `{$table}` ({$cols}) VALUES\n";
                        $lines = [];
                        foreach ($chunk as $row) {
                            $vals = array_map(function($v) use ($pdo) {
                                if ($v === null) return 'NULL';
                                return $pdo->quote($v);
                            }, array_values($row));
                            $lines[] = '  (' . implode(', ', $vals) . ')';
                        }
                        $sql .= implode(",\n", $lines) . ";\n";
                    }
                    $sql .= "\n";
                }
            }

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
            $sql .= "-- Fin del respaldo\n";

            // Escribir archivo
            $written = file_put_contents($filepath, $sql);

            if ($written !== false && filesize($filepath) > 100) {
                return $filename;
            }

            if (file_exists($filepath)) unlink($filepath);
            return false;

        } catch (Exception $e) {
            if (file_exists($filepath)) unlink($filepath);
            return false;
        }
    }

    public function getBackups() {
        $files   = glob($this->backupDir . 'backup_*.sql');
        $backups = [];

        if (!$files) return $backups;

        foreach ($files as $file) {
            $backups[] = [
                'filename'  => basename($file),
                'path'      => $file,
                'size'      => $this->formatBytes(filesize($file)),
                'date'      => date('d/m/Y H:i:s', filemtime($file)),
                'timestamp' => filemtime($file)
            ];
        }

        usort($backups, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });

        return $backups;
    }

    public function downloadBackup($filename) {
        // Validar nombre seguro
        if (!preg_match('/^backup_[\d\-_]+\.sql$/', $filename)) return false;

        $filepath = $this->backupDir . $filename;
        if (!file_exists($filepath)) return false;

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        header('Pragma: no-cache');
        readfile($filepath);
        exit;
    }

    public function deleteBackup($filename) {
        if (!preg_match('/^backup_[\d\-_]+\.sql$/', $filename)) return false;

        $filepath = $this->backupDir . $filename;
        if (file_exists($filepath)) return unlink($filepath);

        return false;
    }

    public function deleteOldBackups($daysToKeep = 30) {
        $files   = glob($this->backupDir . 'backup_*.sql');
        $deleted = 0;

        if (!$files) return $deleted;

        foreach ($files as $file) {
            if (time() - filemtime($file) > ($daysToKeep * 24 * 60 * 60)) {
                unlink($file);
                $deleted++;
            }
        }

        return $deleted;
    }

    private function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow   = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}