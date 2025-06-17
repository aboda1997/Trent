<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require dirname(dirname(__FILE__)) . '/user_api/estate.php';
require dirname(dirname(__FILE__)) . '/include/constants.php';
require_once dirname(dirname(__FILE__)) . '/user_api/error_handler.php';

header('Content-Type: application/json');
try {
    // Handle preflight request
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    $sender_id = isset($_POST['sender_id']) ? $_POST['sender_id'] : 0;
    $receiver_id = isset($_POST['receiver_id']) ? $_POST['receiver_id'] : 0;
    $prop_id = isset($_POST['prop_id']) ? $_POST['prop_id'] : '';
    $message = $rstate->real_escape_string(isset($_POST['message']) ? $_POST['message'] : '');

    $user1 = max($sender_id, $receiver_id);
    $user2 = min($sender_id, $receiver_id);

    // Check if a chat_id already exists for the given prop_id, user1, and user2
    $checkQuery = "SELECT id FROM tbl_chat_property WHERE prop_id = $prop_id AND user1 = $user1 AND user2 = $user2";
    $res = $rstate->query($checkQuery);
    if ($sender_id == 0 ||  $receiver_id == 0) {

        $returnArr    = generateResponse('false', "You Must Enter Sender and Receiver Id .", 400);
    } else if ($sender_id === $receiver_id) {

        $returnArr    = generateResponse('false', "You Must Enter Different Two Users.", 400);
    } else if (!isset($_FILES['img']) && $message == '') {
        $returnArr    = generateResponse('false', "You Must Enter Chat Content!", 400);
    } else if ($res->num_rows == 0 &&   validateIdAndDatabaseExistance($prop_id, 'tbl_property', "  add_user_id = " . $receiver_id . " and is_deleted = 0 ") === false) {
        $returnArr    = generateResponse('false', "This property id is not associated with the target user. Please verify the correct user.", 400);
    } else {
        // Allowed file types for images
        $allowedImageTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        // Directories for storing images
        $uploadDirImages = dirname(dirname(__FILE__)) . "/images/chat/";

        $imageUrl = '';

        // Handle image upload
        if (isset($_FILES['img'])) {

            // Check for errors during upload
            if ($_FILES['img']['error'] === UPLOAD_ERR_OK) {

                $maxFileSize = 2 * 1024 * 1024; // 2 MB

                $imageName = time() . '_' . basename($_FILES['img']['name']);
                $destination = $uploadDirImages . $imageName;

                // Validate image type
                if (!in_array($_FILES['img']['type'], $allowedImageTypes)) {
                    $returnArr = generateResponse("false", "Invalid image type. Allowed: JPEG, PNG, JPG", 400);
                }
                // Validate image size (max 2 MB)
                elseif ($_FILES['img']['size'] > $maxFileSize) {
                    $returnArr = generateResponse("false", "Image size exceeds 2 MB.", 400);
                }
                // Move uploaded file if valid
                elseif (move_uploaded_file($_FILES['img']['tmp_name'], $destination)) {
                    $imageUrl = "images/chat/" . $imageName;
                } else {
                    $returnArr = generateResponse("false", "Failed to upload image.", 500);
                }
            } else {
                $returnArr = generateResponse("false", "Error during image upload.", 400);
            }
        }


        $date = new DateTime('now', new DateTimeZone('Africa/Cairo'));
        $created_at = $date->format('Y-m-d H:i:s');

        if (!isset($returnArr)) {
            $GLOBALS['rstate']->begin_transaction();
            $h = new Estate();
            $table = "tbl_chat_property";

            $user1 = max($sender_id, $receiver_id);
            $user2 = min($sender_id, $receiver_id);

            // Check if a chat_id already exists for the given prop_id, user1, and user2
            $checkQuery = "SELECT id FROM tbl_chat_property WHERE prop_id = $prop_id AND user1 = $user1 AND user2 = $user2";
            $res = $rstate->query($checkQuery);
            if ($res->num_rows > 0) {
                $chat_id = (int)$res->fetch_assoc()['id'];
            } else {
                $chat_id = $h->restateinsertdata_Api(["prop_id", "user1", "user2"], [$prop_id, $user1, $user2], $table);
            }
            $encoded_message = json_encode([
                "message" => $message,

            ], JSON_UNESCAPED_UNICODE);
            $field_values = ["sender_id", "receiver_id","is_approved" ,  "message", "img", "created_at", "chat_id"];
            $data_values = [$sender_id, $receiver_id, 9,$encoded_message, $imageUrl, $created_at, $chat_id];
            $table = "tbl_messages";
            $message_id = $h->restateinsertdata_Api($field_values, $data_values, $table);
            $GLOBALS['rstate']->commit();
        }
    }


    if (isset($returnArr)) {
        echo $returnArr;
    } else {
        $returnArr    = generateResponse('true', "Chat Added Successfully", 201, array(
            "chat_id" => $chat_id,
            "message_id" => $message_id
        ));

        echo $returnArr;
    }
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}
