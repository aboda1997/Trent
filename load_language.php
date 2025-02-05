<?php 
function loadLanguage() {
    if (isset($_POST['language'])) {
        $_SESSION['language'] = $_POST['language'];
    }

    $language_code = $_SESSION['language'] ?? 'en'; // Default to 'en'

    $filePath = __DIR__ . "/languages/{$language_code}.php";
    return file_exists($filePath) ? include $filePath : include __DIR__ . '/languages/en.php';
}
?>