<?php

function getFawryPaymentStatus($merchantCode, $merchantRefNumber, $signature) {
    $baseUrl = 'https://atfawry.fawrystaging.com/ECommerceWeb/Fawry/payments/status/v2';
    $url = $baseUrl . '?merchantCode=' . urlencode($merchantCode) . 
           '&merchantRefNumber=' . urlencode($merchantRefNumber) . 
           '&signature=' . urlencode($signature);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return ['status' => false];
    }

    if ($httpCode != 200) {
        return ['status' => false];
    }

    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['status' => false];
    }

    // Extract payment status and item code
    $orderStatus = $data['orderStatus'] ?? null;
    $orderAmount = $data['orderAmount'] ?? null;
    $itemCode = $data['orderItems'][0]['itemCode'] ?? null; // Assuming first item

    return [
        'status' => true,
        'orderStatus' => $orderStatus,
        'orderAmount' => $orderAmount,
        'itemCode' => $itemCode
    ];
}


?>