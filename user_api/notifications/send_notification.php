<?php

require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/user_api/estate.php';
require dirname(dirname(__FILE__), 2) . '/vendor/autoload.php';

use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;


function sendFirebaseNotification(
    string $title,
    string $body,
    string $uid,
    ?string $imageUrl = null,
    ?string $linkUrl = null
): string {
    // Initialize credentials
    $credential = new ServiceAccountCredentials(
        "https://www.googleapis.com/auth/firebase.messaging",
        json_decode(file_get_contents(dirname(dirname(__FILE__), 2) . '/keys/pvKey.json'), true)
    );

    // Fetch auth token
    $token = $credential->fetchAuthToken(HttpHandlerFactory::build());
    $deviceToken = $GLOBALS['rstate']->query("select registration_token from tbl_user where id = $uid ")->fetch_assoc()["registration_token"];
    // Prepare the message payload

    $message = [
        'message' => [
            'token' => $deviceToken,
            'notification' => [
                'title' => $title,
                'body' => $body
            ]
        ]
    ];

    // Add image if provided
   // if ($imageUrl !== null) {
     //   $message['message']['notification']['image'] = $imageUrl;
  //  }

    // Add webpush link if provided
    if ($linkUrl !== null) {
        $message['message']['webpush'] = [
            'fcm_options' => [
                'link' => $linkUrl
            ]
        ];
    }

    $payload = json_encode($message);

    // Initialize cURL
    $ch = curl_init("https://fcm.googleapis.com/v1/projects/trent-a4caf/messages:send");

    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token['access_token']
        ],
        CURLOPT_POSTFIELDS =>  $payload,
        CURLOPT_RETURNTRANSFER => true

    ]);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "post");


    try {
        

        // Only proceed with database operations if the message was sent successfully

        // Check if request was successful first
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode < 200 || $httpCode >= 300) {
            return false;
        }
    
        $GLOBALS['rstate']->begin_transaction();

        $h = new Estate();
        $table = "tbl_notification_head";
        $field_values = ["uid", "created_at", "is_seen", "title" , "body" , "img"];
        $created_at = date('Y-m-d H:i:s');
        $data_values = [$uid, $created_at ,0 , $title , $body , $imageUrl  ];

        $_id = $h->restateinsertdata_Api($field_values, $data_values, $table);

        if (!$_id) {
            return false;
        }

        $GLOBALS['rstate']->commit();
        

        return true;
    } catch (Exception $e) {
        return false;
    } finally {
        // Close cURL resource
        curl_close($ch);
    }
}
