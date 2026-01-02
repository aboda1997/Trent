<?php

require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/vendor/autoload.php';

use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;


function sendFirebaseNotification(
    string $title,
    string $body,
    string $uid,
    $key, 
    $value,
    ?string $imageUrl = null,
    ?string $linkUrl = null, 
   
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
      
    
        $GLOBALS['rstate']->begin_transaction();

        $h = new Estate();
        $table1 = "tbl_notification_head";
        $table2 = "tbl_notification_body";
        $field_values = ["uid", "created_at", "is_seen", "title" , "body" , "img"];
        $field_values2 = ["head_id", "name" , 'value'];
        $date = new DateTime('now', new DateTimeZone('Africa/Cairo'));
        $created_at = $date->format('Y-m-d H:i:s');
        $data_values = [$uid, $created_at ,0 , $title , $body , $imageUrl  ];

        $_id = $h->restateinsertdata_Api($field_values, $data_values, $table1);
        $data_values2 = [$_id,$key , $value ];

        $_ned = $h->restateinsertdata_Api($field_values2, $data_values2, $table2);

        $GLOBALS['rstate']->commit();
        

        return true;
    } catch (Exception $e) {
        return false;
    } finally {
        // Close cURL resource
        curl_close($ch);
    }
}
