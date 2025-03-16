<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require dirname(dirname(__FILE__)) . '/user_api/estate.php';
require dirname(dirname(__FILE__)) . '/include/constants.php';

header('Content-type: text/json');
try {

    $sender_id = isset($_POST['sender_id']) ? $_POST['sender_id'] : '';
    $receiver_id = isset($_POST['receiver_id']) ? $_POST['receiver_id'] : '';
    $message = isset($_POST['message']) ? $_POST['message'] : '';


    if ($sender_id == '' ||  $receiver_id == '') {

        $returnArr    = generateResponse('false', "You must enter Sender and Receiver Id  !", 400);
    } else if (!isset($_FILES['img']) && $message == '') {
        $returnArr    = generateResponse('false', "You must enter chat content!", 400);
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

        $created_at = date('Y-m-d H:i:s'); 



        if (!isset($returnArr)) {

            $field_values = ["sender_id", "receiver_id" , "message" , "img" , "created_at"] ; 
            $data_values = [ $sender_id , $receiver_id  , $message , $imageUrl , $created_at] ; 

            $table = "tbl_chat";
            $h = new Estate();
			$check = $h->restateinsertdata_Api($field_values, $data_values, $table);
        }
    }


    if (isset($returnArr)) {
        echo $returnArr;
    } else {
        if ($check) {
            $returnArr    = generateResponse('true', "Chat Added Successfully", 200, array("id" => $check));
        } else {
            $returnArr    = generateResponse('false', "Database error", 500);
        }
        echo $returnArr;
    }
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ));
    echo $returnArr;
}
