<?php
// app/bootstrap.php

// Session starten
session_start();

// Pfad-Konstante definieren, um das Einbinden zu erleichtern
define('APP_PATH', __DIR__);

// Konfiguration und Literale laden
require_once APP_PATH . '/private-env.php';
require_once APP_PATH . '/config.php';
require_once APP_PATH . '/literals.php';

// Bibliotheken laden
require_once APP_PATH . '/lib/Parsedown.php';

// Controller-Klassen laden
require_once APP_PATH . '/controller/ApiClient.php';
require_once APP_PATH . '/controller/Chat.php';
require_once APP_PATH . '/controller/Logger.php';

// Globale Instanz des Markdown-Parsers erstellen
$parsedown = new Parsedown();
$parsedown->setBreaksEnabled(true);
