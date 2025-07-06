<?php
// app/controller/Chat.php

class Chat {
    private ApiClient $apiClient;

    public function __construct(ApiClient $apiClient) {
        $this->apiClient = $apiClient;
    }

    /**
     * Verarbeitet die eingehenden Anfragen (POST für Prompts, GET für neuen Chat).
     * @return string|null Gibt eine Fehlermeldung zurück, falls eine auftritt.
     */
    public function handleRequest(): ?string {
        // "Neuer Chat" Anfrage
        if (isset($_GET['new_chat'])) {
            $this->startNewChat();
            return null; // Kein Fehler
        }
        
        // Formular-Anfrage
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handlePrompt();
        }

        return null; // Kein Fehler bei normalem Seitenaufruf
    }

    private function handlePrompt(): ?string {
        $prompt = trim($_POST['prompt'] ?? '');

        if (empty($prompt)) {
            return null; // Leere Eingabe ignorieren
        }
        
        if (!defined('GEMINI_API_KEY') || GEMINI_API_KEY === 'DEIN_API_KEY_HIER_EINFUEGEN') {
            global $L;
            return $L['error_no_key'];
        }

        // User-Nachricht zum Verlauf hinzufügen
        $_SESSION['chat_history'][] = ['role' => 'user', 'parts' => [['text' => $prompt]]];
        
        // Anfrage an die API senden
        $response = $this->apiClient->sendRequest($_SESSION['chat_history']);

        if ($response['success']) {
            // Erfolgreiche Antwort der API zum Verlauf hinzufügen
            $_SESSION['chat_history'][] = ['role' => 'model', 'parts' => [['text' => $response['data']]]];
        } else {
            // Fehler zurückgeben, um ihn auf der UI anzuzeigen
            return $response['data'];
        }

        // Redirect-after-Post, um doppeltes Senden zu verhindern
        header('Location: index.php');
        exit();
    }

    private function startNewChat(): void {
        unset($_SESSION['chat_history']);
        header('Location: index.php');
        exit();
    }
}
