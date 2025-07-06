# KI Chat

Ein einfaches, PHP-basiertes Chat-Interface für die Google Gemini API.

## Konfiguration

1.  **API-Key eintragen:**
    Bevor du die Anwendung starten kannst, musst du deinen persönlichen API-Key von Google AI Studio eintragen.

    Öffne die Datei `app/private-env.php` und ersetze den Platzhalter durch deinen Key in der Konstante `GEMINI_API_KEY`.

    ```php
    // app/private-env.php
    define('GEMINI_API_KEY', 'DEIN_API_KEY_HIER_EINFUEGEN');
    ```

## Starten der Anwendung

Du kannst die Anwendung ganz einfach mit dem in PHP eingebauten Webserver starten.

1.  Wechsle in deinem Terminal in das `public`-Verzeichnis des Projekts.

2.  Führe den folgenden Befehl aus, um den Server zu starten:
    ```bash
    php -S localhost:8000 -d max_execution_time=100
    ```
    Die Option `-d max_execution_time=100` erhöht die maximale Ausführungszeit für Skripte. Dies ist hilfreich, um Timeouts zu vermeiden, falls die API für eine Antwort einmal länger benötigen sollte.

3.  Öffne deinen Webbrowser und gehe zur Adresse `http://localhost:8000`.

