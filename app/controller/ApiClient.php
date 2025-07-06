<?php
// app/controller/ApiClient.php

class ApiClient {
    private string $apiKey;
    private string $apiUrl;

    public function __construct(string $apiKey, string $apiUrl) {
        $this->apiKey = $apiKey;
        $this->apiUrl = $apiUrl;
    }

    /**
     * Sendet den Chatverlauf an die Gemini API.
     * @param array $chatHistory Der gesamte Chatverlauf.
     * @return array ['success' => bool, 'data' => string|array]
     */
    public function sendRequest(array $chatHistory): array {
        $data = ['contents' => $chatHistory];
        
        $headers = [
            'Content-Type: application/json',
            'x-goog-api-key: ' . $this->apiKey
        ];

        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['success' => false, 'data' => "cURL Fehler: " . $error];
        }

        $result = json_decode($response, true);

        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            return ['success' => true, 'data' => $result['candidates'][0]['content']['parts'][0]['text']];
        }

        // Detaillierter Fehler von der API
        $errorMessage = 'Fehler von der API erhalten. Antwort: <pre>' . htmlspecialchars($response) . '</pre>';
        return ['success' => false, 'data' => $errorMessage];
    }
}
