<?php 
function load_language() {
    if (isset($_GET['lang']) && in_array($_GET['lang'], ['ar', 'en'])) {
        $_SESSION['lang'] = $_GET['lang'];
    }
     
    if(!isset($_GET['lang'])){
        $_SESSION['lang'] = "en";
    }
    $language_code = $_SESSION['lang'] ?? 'en'; 

    $filePath = dirname( dirname(__FILE__) ). "/languages/{$language_code}.php";
    return file_exists($filePath) ? include $filePath : include dirname( dirname(__FILE__) ) . '/languages/en.php';
}

function load_specific_langauage($language_code){
    $filePath = dirname( dirname(__FILE__) ) . "/languages/{$language_code}.php";

    return file_exists($filePath) ? include $filePath : include dirname( dirname(__FILE__) ) . '/languages/en.php';

}

function load_language_code(){
    if (isset($_GET['lang']) && in_array($_GET['lang'], ['ar', 'en'])) {
        $_SESSION['lang'] = $_GET['lang'];
    }
     
    if(!isset($_GET['lang'])){
        $_SESSION['lang'] = "en";
    }
    $language_code = $_SESSION['lang'] ?? 'en';
    $dir = 'ltr'; 
    if ($language_code == 'ar'){
        $dir = "rtl";
    }
    return ['language_code' => $language_code, 'dir' => $dir];

}
?>