<?php
header("Access-Control-Allow-Origin: *"); // Allow all domains (or specify your domain)
header("Access-Control-Allow-Methods: GET, POST");
header("Content-Type: text/plain");
require dirname(dirname(__FILE__)) . '/include/helper.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';
require_once dirname(dirname(__FILE__)) . '/user_api/error_handler.php';

header('Content-Type: application/json');
try {
    // Get parameters from request
    $text = isset($_REQUEST['text']) ? urlencode($_REQUEST['text']) : '';
    $sourceLang = isset($_REQUEST['sl']) ? $_REQUEST['sl'] : 'ar';
    $targetLang = isset($_REQUEST['dl']) ? $_REQUEST['dl'] : 'en';

    // Validate input
    if (empty($text)) {
        $response = generateResponse(
            'false',
            "Missing text parameter",
            400,

        );
    } else {

        // Build API URL
        $apiUrl = "https://ftapi.pythonanywhere.com/translate?sl=$sourceLang&dl=$targetLang&text=$text";

        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 10-second timeout

        // Execute request
        $_data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);


        curl_close($ch);
        $decoded_data = json_decode($_data, true);
        $response = generateResponse(
            'true',
            "Translated Successfully",
            200,
            array(
                "destination-text" => $decoded_data["destination-text"],
                "possible-translations" => $decoded_data["translations"]['possible-translations']
            )
        );
    }
    echo $response;

} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}
