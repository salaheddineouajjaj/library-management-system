<?php
/**
 * AJAX - AI Chat Handler
 * 
 * Process chatbot messages from the floating chat UI.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/ai_client.php';

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Authentication required']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$message = trim($input['message'] ?? '');

if (empty($message)) {
    echo json_encode(['success' => false, 'error' => 'Message is empty']);
    exit;
}

// Context/System prompt for the chatbot
$systemPrompt = "You are the Library AI Assistant. Your goal is to help users find books, navigate the library, and answer general literary questions. Be helpful, concise, and professional. Mention that you can suggest books in the 'AI Suggestions' page or help items in the 'Shop'.";

$result = callOpenAI($message, $systemPrompt);

echo json_encode($result);
