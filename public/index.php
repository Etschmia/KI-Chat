<?php
// public/index.php

// 1. Initialisiere die Anwendung
// Wir gehen eine Ebene hoch (../), um in das app-Verzeichnis zu gelangen.
require_once __DIR__ . '/../app/bootstrap.php';

// 2. Erstelle die notwendigen Objekte (Dependency Injection)
$apiClient = new ApiClient(GEMINI_API_KEY, GEMINI_API_URL);
$chatController = new Chat($apiClient);

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
            <a class="navbar-brand" href="#"><i class="bi bi-robot"></i> <?php echo $L['navbar_title']; ?></a>
            <a href="?new_chat=true" class="btn btn-outline-light"><?php echo $L['new_chat_button']; ?></a>
        </div>
    </nav>

    <div class="container chat-container d-flex flex-column">
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (empty($chat_history)): ?>
            <div class="text-center text-muted mt-5">
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
            <form method="POST" action="index.php">
                <div class="input-group">
                    <textarea name="prompt" class="form-control" placeholder="<?php echo $L['textarea_placeholder']; ?>" rows="2" required autofocus></textarea>
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-send-fill"></i> <?php echo $L['send_button']; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        window.onload = function() {
            var chatContainer = document.querySelector('.chat-container');
            chatContainer.scrollTop = chatContainer.scrollHeight;
        };
    </script>

</body>
</html>
