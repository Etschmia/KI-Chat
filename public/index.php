<?php
// public/index.php

// 1. Initialisiere die Anwendung
// Wir gehen eine Ebene hoch (../), um in das app-Verzeichnis zu gelangen.
require_once __DIR__ . '/../app/bootstrap.php';

// 2. Erstelle die notwendigen Objekte (Dependency Injection)
$logDir = __DIR__ . '/../log';
$logger = new Logger($logDir);
$apiClient = new ApiClient(GEMINI_API_KEY, GEMINI_API_URL);
$chatController = new Chat($apiClient, $logger);

// 3. Verarbeite die aktuelle Anfrage
$error_message = $chatController->handleRequest();

// 4. Lade Daten für die Ansicht
$chat_history = $_SESSION['chat_history'] ?? [];

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $L['page_title']; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Unsere eigenen Styles -->
    <link rel="stylesheet" href="css/custom.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#"><img src="images/simpsons_web_optimized.png" alt="Logo" style="height: 24px; margin-right: 8px;"> <?php echo $L['navbar_title']; ?></a>
            <a href="?new_chat=true" class="btn btn-outline-light"><?php echo $L['new_chat_button']; ?></a>
        </div>
    </nav>

    <div id="chat-container" class="container chat-container d-flex flex-column">
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (empty($chat_history)): ?>
            <div id="welcome-message" class="text-center text-muted mt-5">
                <h2><?php echo $L['welcome_headline']; ?></h2>
                <p><?php echo $L['welcome_text']; ?></p>
            </div>
        <?php endif; ?>

        <?php foreach ($chat_history as $message): ?>
            <div class="chat-message <?php echo ($message['role'] === 'user') ? 'user-message' : 'model-message'; ?>">
                <?php
                    $text = $message['parts'][0]['text'];
                    echo ($message['role'] === 'model') ? $parsedown->text($text) : htmlspecialchars($text);
                ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="input-form-container">
        <div class="container">
            <form id="chat-form" method="POST" action="index.php">
                <div class="input-group">
                    <textarea id="prompt-textarea" name="prompt" class="form-control" placeholder="<?php echo $L['textarea_placeholder']; ?>" rows="2" required autofocus></textarea>
                    <button id="submit-button" class="btn btn-primary" type="submit">
                        <i class="bi bi-send-fill"></i> <?php echo $L['send_button']; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const chatContainer = document.getElementById('chat-container');
        const chatForm = document.getElementById('chat-form');
        const promptTextarea = document.getElementById('prompt-textarea');
        const submitButton = document.getElementById('submit-button');

        // Scroll to bottom on initial page load
        window.addEventListener('load', () => {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        });

        // Formular bei "Enter" senden, "Shift+Enter" für Zeilenumbruch erlauben
        promptTextarea.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault(); // Verhindert das Einfügen einer neuen Zeile
                chatForm.requestSubmit(); // Löst das 'submit'-Event des Formulars aus
            }
        });

        // Handle form submission for instant feedback
        chatForm.addEventListener('submit', (e) => {
            const promptText = promptTextarea.value.trim();
            if (promptText === '') {
                return; // Let browser handle 'required' validation
            }

            // Hide welcome message if it exists
            const welcomeMessage = document.getElementById('welcome-message');
            if (welcomeMessage) {
                welcomeMessage.style.display = 'none';
            }

            // Disable form to prevent multiple submissions
            promptTextarea.readOnly = true;
            submitButton.disabled = true;
            // Change button to show loading state
            submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <?php echo $L['loading_text']; ?>`;

            // Create and append a temporary user message
            const userMessageDiv = document.createElement('div');
            userMessageDiv.className = 'chat-message user-message';
            userMessageDiv.textContent = promptText;
            chatContainer.appendChild(userMessageDiv);

            // Create and append a spinner message
            const spinnerMessageDiv = document.createElement('div');
            spinnerMessageDiv.className = 'chat-message model-message d-flex justify-content-center align-items-center';
            spinnerMessageDiv.innerHTML = `<div class="spinner-border text-primary" role="status"><span class="visually-hidden"><?php echo $L['loading_text']; ?></span></div>`;
            chatContainer.appendChild(spinnerMessageDiv);

            // Scroll to the new messages
            chatContainer.scrollTop = chatContainer.scrollHeight;
        });
    </script>

</body>
</html>
