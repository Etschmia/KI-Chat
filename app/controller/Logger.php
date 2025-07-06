<?php
// app/controller/Logger.php

class Logger {
    private string $logDir;

    /**
     * @param string $logDir Der Pfad zum Verzeichnis, in dem die Logs gespeichert werden sollen.
     */
    public function __construct(string $logDir) {
        $this->logDir = $logDir;
        // Sicherstellen, dass das Log-Verzeichnis existiert und beschreibbar ist.
        if (!is_dir($this->logDir)) {
            // Versuche, das Verzeichnis zu erstellen.
            // @-Operator unterdrückt Fehler, falls das Verzeichnis bereits von einem anderen Prozess erstellt wurde.
            if (!@mkdir($this->logDir, 0775, true) && !is_dir($this->logDir)) {
                // In einer echten Anwendung könnte man hier eine robustere Fehlerbehandlung implementieren.
                error_log("Log-Verzeichnis konnte nicht erstellt werden: " . $this->logDir);
            }
        }
    }

    /**
     * Schreibt eine Anfrage und die dazugehörige Antwort in eine tagesaktuelle Log-Datei.
     *
     * @param string $prompt Die Frage des Benutzers.
     * @param string $response Die Antwort des Models.
     */
    public function log(string $prompt, string $response): void {
        $logFile = $this->logDir . '/' . date('d.m.Y') . '.log';
        $timestamp = date('H:i:s');
        $logEntry  = $timestamp . "\n" . "Frage: " . $prompt . "\n" . "Antwort: " . $response . "\n\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
}