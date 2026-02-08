<?php
/**
 * AI Book Suggestions
 * 
 * Allows users to get AI-powered book recommendations.
 * Uses OpenAI API to generate suggestions based on user preferences.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/ai_client.php';

$pageTitle = "AI Book Suggestions";
$currentPage = "ai";

$userPrompt = '';
$aiResponse = null;
$matchingBooks = [];
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userPrompt = trim($_POST['prompt'] ?? '');
    
    if (empty($userPrompt)) {
        $error = 'Please describe what kind of books you\'re looking for.';
    } elseif (strlen($userPrompt) < 10) {
        $error = 'Please provide more details about your preferences (at least 10 characters).';
    } elseif (strlen($userPrompt) > 1000) {
        $error = 'Your message is too long. Please keep it under 1000 characters.';
    } else {
        // Call the OpenAI API
        $result = callOpenAI($userPrompt);
        
        if ($result['success']) {
            $aiResponse = $result['message'];
            
            // Try to find matching books in our library
            $categories = extractCategories($aiResponse);
            if (!empty($categories)) {
                $matchingBooks = findMatchingBooks($categories, 4);
            }
        } else {
            // AI Failed (e.g. 429 Rate Limit) - FALLBACK
            $error = "AI service is busy (" . $result['error'] . "). Switching to direct search.";
            // We will still try to show OpenLibrary results below using the user prompt
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav_user.php';
?>

<div class="page-header">
    <h1>🤖 AI Book Suggestions</h1>
</div>

<div class="card ai-card">
    <h3>Ask Our AI for Book Recommendations</h3>
    <p class="text-muted">
        Describe your reading preferences, favorite genres, or the type of story you're in the mood for.
        Our AI will suggest books you might enjoy!
    </p>
    
    <form method="POST" action="">
        <div class="form-group">
            <label for="prompt">What are you looking for?</label>
            <textarea id="prompt" name="prompt" 
                      placeholder="Example: I love mystery novels with detective stories and unexpected plot twists. I also enjoy books set in Victorian England..."
                      rows="4"><?= sanitize($userPrompt) ?></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">
            ✨ Get AI Suggestions
        </button>
    </form>
</div>

<?php if ($error): ?>
    <div class="alert alert-warning">
        <?= sanitize($error) ?>
    </div>
<?php endif; ?>

<?php if ($aiResponse): ?>
    <!-- AI Response -->
    <div class="card">
        <div class="card-header">
            <h3>🤖 AI Recommendations</h3>
        </div>
        <div class="ai-response">
            <?= nl2br(sanitize($aiResponse)) ?>
        </div>
    </div>
<?php endif; ?>

<!-- Results Section (Local or OpenLibrary) -->
<?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($matchingBooks) || !empty($userPrompt))): ?>
    
    <!-- Matching Books from Local Library -->
    <?php if (!empty($matchingBooks)): ?>
        <div class="card">
            <div class="card-header">
                <h3>📚 Available in Our Library</h3>
            </div>
            <p class="text-muted">
                Based on the AI suggestions, here are some books we have available:
            </p>
            
            <div class="book-grid">
                <?php foreach ($matchingBooks as $book): ?>
                    <div class="book-card">
                        <h3><?= sanitize($book['title']) ?></h3>
                        <p class="book-author">by <?= sanitize($book['author']) ?></p>
                        <span class="book-category"><?= sanitize($book['category']) ?></span>
                        
                        <?php if (!empty($book['description'])): ?>
                            <p class="book-description">
                                <?= substr(sanitize($book['description']), 0, 100) ?>...
                            </p>
                        <?php endif; ?>
                        
                        <div class="book-availability">
                            <span class="copies available">
                                <?= $book['available_copies'] ?> available
                            </span>
                            <form method="POST" action="<?= url('/user/borrow_book.php') ?>">
                                <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-primary">Borrow</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- OpenLibrary Fallback (Show if no local matches OR if AI failed) -->
    <?php if (empty($matchingBooks)): ?>
        <?php
            // Extract keywords from Prompt if AI response failed or didn't give categories
            $searchSource = $aiResponse ?? $userPrompt;
            // Clean up prompt to get decent keywords (simple approach)
            $searchTerms = implode('+', array_slice(explode(' ', strip_tags($searchSource)), 0, 6)); // First 6 words
            
            // If AI failed completely, explicitly use user prompt
            if (empty($aiResponse)) {
                $searchTerms = implode('+', array_slice(explode(' ', $userPrompt), 0, 6));
            }

            $olUrl = "https://openlibrary.org/search.json?q=" . urlencode($searchTerms) . "&limit=4";
            $context = stream_context_create(['http' => ['ignore_errors' => true, 'timeout' => 5]]);
            $olResponse = @file_get_contents($olUrl, false, $context);
            $olBooks = $olResponse ? (json_decode($olResponse, true)['docs'] ?? []) : [];
        ?>
        
        <?php if (!empty($olBooks)): ?>
            <div class="card">
                <div class="card-header">
                    <h3>🌐 <?= empty($aiResponse) ? 'Direct Search Results' : 'Suggested from OpenLibrary' ?></h3>
                </div>
                <p class="text-muted">
                    <?= empty($aiResponse) 
                        ? "Since our AI assistant is napping, here are some books matching your request:" 
                        : "We didn't find exact local matches, but you can borrow these instantly:" 
                    ?>
                </p>
                <div class="book-grid">
                    <?php foreach ($olBooks as $book): 
                        $title = $book['title'] ?? 'Unknown';
                        $authors = isset($book['author_name']) ? implode(', ', array_slice($book['author_name'], 0, 2)) : 'Unknown';
                        $coverId = $book['cover_i'] ?? null;
                        $thumbnail = $coverId ? "https://covers.openlibrary.org/b/id/$coverId-M.jpg" : 'https://via.placeholder.com/128x196?text=No+Cover';
                    ?>
                        <div class="book-card">
                            <div class="book-cover" style="height: 150px;">
                                <img src="<?= $thumbnail ?>" alt="<?= sanitize($title) ?>"
                                     onerror="this.onerror=null;this.src='https://via.placeholder.com/128x196?text=No+Cover';">
                            </div>
                            <div class="book-details">
                                <h3><?= sanitize($title) ?></h3>
                                <p class="book-author"><?= sanitize($authors) ?></p>
                                <div class="book-actions">
                                     <form method="POST" action="<?= url('/user/borrow_book.php') ?>" style="display: inline-block;">
                                        <input type="hidden" name="api_source" value="openlibrary">
                                        <input type="hidden" name="title" value="<?= sanitize($title) ?>">
                                         <input type="hidden" name="author" value="<?= sanitize($authors) ?>">
                                         <input type="hidden" name="category" value="AI Suggestion">
                                         <input type="hidden" name="cover_url" value="<?= $thumbnail ?>">
                                        <button type="submit" class="btn btn-sm btn-primary">Borrow Now</button>
                                    </form>
                                    
                                    <form method="POST" action="<?= url('/user/add_to_shelf.php') ?>" style="display: inline-block;">
                                        <input type="hidden" name="api_source" value="openlibrary">
                                        <input type="hidden" name="title" value="<?= sanitize($title) ?>">
                                        <input type="hidden" name="author" value="<?= sanitize($authors) ?>">
                                        <input type="hidden" name="category" value="AI Suggestion">
                                        <input type="hidden" name="cover_url" value="<?= $thumbnail ?>">
                                        <input type="hidden" name="shelf" value="want_to_read">
                                        <input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI'] ?>">
                                        
                                        <button type="submit" class="btn btn-sm btn-secondary">+ Library</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php elseif (!empty($error)): ?>
             <div class="card empty-state">
                <p>We couldn't find any books matching your descriptions automatically.</p>
             </div>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>

<!-- Example Prompts -->
<div class="card">
    <h3>💡 Example Prompts</h3>
    <p class="text-muted">Not sure what to ask? Try one of these:</p>
    <ul style="color: var(--color-text-muted); margin-left: 1.5rem;">
        <li>I want something that will make me think, like 1984 or Brave New World</li>
        <li>I'm looking for a light, fun romance novel for a beach vacation</li>
        <li>Suggest me some classic literature that's easy to read</li>
        <li>I love Harry Potter, what similar fantasy books would you recommend?</li>
        <li>I want a gripping thriller that I can't put down</li>
    </ul>
</div>

<?php require_once __DIR__ . '/../includes/footer_user.php'; ?>

