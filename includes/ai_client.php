<?php
/**
 * AI Client - OpenAI API Integration
 * 
 * Helper functions to call OpenAI's Chat Completions API using cURL.
 * No external libraries required.
 */

require_once __DIR__ . '/../config/ai_config.php';

/**
 * Send a request to OpenAI's Chat Completions API
 * 
 * @param string $userPrompt The user's message/question
 * @param string $systemPrompt Optional system prompt to set AI behavior
 * @return array ['success' => bool, 'message' => string, 'error' => string|null]
 */
function callOpenAI($userPrompt, $systemPrompt = null) {
    // Check if API key is configured
    if (OPENAI_API_KEY === 'YOUR_API_KEY_HERE' || empty(OPENAI_API_KEY)) {
        return [
            'success' => false,
            'message' => '',
            'error' => 'API key not configured. Please add your API key in config/ai_config.php'
        ];
    }
    
    // Google Gemini API Logic
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . OPENAI_API_KEY;
    
    // Default system prompt
    if ($systemPrompt === null) {
        $systemPrompt = "You are a helpful library assistant. Recommend specific books based on the user's request. Include title and author.";
    }
    
    // Build Gemini Payload
    $data = [
        'contents' => [
            [
                'role' => 'user',
                'parts' => [
                    ['text' => $systemPrompt . "\n\nUser Request: " . $userPrompt]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.7,
            'maxOutputTokens' => 800
        ]
    ];
    
    // Initialize cURL
    $ch = curl_init($url);
    
    // Set cURL options
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json'
        ],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false // Some XAMPP setups have SSL issues
    ]);
    
    // Execute the request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    
    curl_close($ch);
    
    // Handle cURL errors
    if ($curlError) {
        return [
            'success' => false,
            'message' => '',
            'error' => 'Connection error: ' . $curlError
        ];
    }
    
    // Parse the response
    $result = json_decode($response, true);
    
    // Handle API errors
    if ($httpCode !== 200) {
        if ($httpCode === 429) {
            $errorMessage = "Quota exceeded or API Rate Limit. Please check your Gemini API key status.";
        } else {
            $errorMessage = $result['error']['message'] ?? 'Unknown API error';
        }
        return [
            'success' => false,
            'message' => '',
            'error' => "API Error ($httpCode): $errorMessage"
        ];
    }
    
    // Extract the AI's response (Gemini specific path)
    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        return [
            'success' => true,
            'message' => trim($result['candidates'][0]['content']['parts'][0]['text']),
            'error' => null
        ];
    }
    
    return [
        'success' => false,
        'message' => '',
        'error' => 'Unexpected API response format'
    ];
}

/**
 * Extract book-related keywords/categories from AI response
 * Useful for matching against database categories
 * 
 * @param string $aiResponse The AI's text response
 * @return array List of potential category keywords
 */
function extractCategories($aiResponse) {
    // Common book categories to look for
    $categories = [
        'Fiction', 'Non-Fiction', 'Mystery', 'Thriller', 'Romance',
        'Science Fiction', 'Fantasy', 'Horror', 'Biography', 'History',
        'Self-Help', 'Business', 'Science', 'Philosophy', 'Poetry',
        'Drama', 'Comedy', 'Adventure', 'Children', 'Young Adult'
    ];
    
    $found = [];
    $lowerResponse = strtolower($aiResponse);
    
    foreach ($categories as $category) {
        if (stripos($lowerResponse, strtolower($category)) !== false) {
            $found[] = $category;
        }
    }
    
    return array_unique($found);
}

/**
 * Search library books that match AI-suggested categories
 * 
 * @param array $categories Categories to search for
 * @param int $limit Maximum books to return
 * @return array Books from database matching categories
 */
function findMatchingBooks($categories, $limit = 6) {
    if (empty($categories)) {
        return [];
    }
    
    try {
        $db = getDB();
        
        $placeholders = implode(',', array_fill(0, count($categories), '?'));
        $sql = "
            SELECT * FROM books 
            WHERE category IN ($placeholders) 
              AND available_copies > 0
            ORDER BY RAND()
            LIMIT ?
        ";
        
        $params = array_merge($categories, [$limit]);
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        return [];
    }
}

