<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require dirname(dirname(__FILE__)) . '/user_api/estate.php';
require dirname(dirname(__FILE__)) . '/include/constants.php';

header('Content-Type: application/json');
try {

    $full_name = isset($_POST['full_name']) ? $_POST['full_name'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : null;
    $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
    $uid = isset($_POST['uid']) ? $_POST['uid'] : '';
    

    $field = [
        "name" => $full_name,
        "gender" => $gender,
    ];

    if (  $uid == '') {

        $returnArr    = generateResponse('false', "You must enter User Id !", 400);
    } else if (!validateName($full_name , 'Full Name' , 50)['status']) {
        $returnArr    = generateResponse('false', validateName($full_name , 'Full Name' , 50 )['response'], 400);
    } else if (!in_array($gender, ['f', 'm'])) {
        $returnArr    = generateResponse('false', "Gender Id not valid!", 400);
    } else if ($email !== null && $email !== '' && (!validateEmail($email)['status'])) {

        $returnArr    = generateResponse('false', validateEmail($email)['response'], 400);
    } else {

        $check_owner = $rstate->query("select * from tbl_user where  id=" . $uid . "")->num_rows;
        if ($check_owner != 0) {
            $field["email"] = $email; 
            

            // Allowed file types for images
            $allowedImageTypes = ['image/jpeg', 'image/png', 'image/jpg'];


            // Directories for storing images
            $uploadDirImages = dirname(dirname(__FILE__)) . "/images/profile/";

            $imageUrl = '';

            // Handle image upload
            if (isset($_FILES['pro_img'])) {

                // Check for errors during upload
                if ($_FILES['pro_img']['error'] === UPLOAD_ERR_OK) {

                    $maxFileSize = 2 * 1024 * 1024; // 2 MB

                    $imageName = time() . '_' . basename($_FILES['pro_img']['name']);
                    $destination = $uploadDirImages . $imageName;

                    // Validate image type
                    if (!in_array($_FILES['pro_img']['type'], $allowedImageTypes)) {
                        $returnArr = generateResponse("false", "Invalid image type. Allowed: JPEG, PNG, JPG", 400);
                    }
                    // Validate image size (max 2 MB)
                    elseif ($_FILES['pro_img']['size'] > $maxFileSize) {
                        $returnArr = generateResponse("false", "Image size exceeds 2 MB.", 400);
                    }
                    // Move uploaded file if valid
                    elseif (move_uploaded_file($_FILES['pro_img']['tmp_name'], $destination)) {
                        $imageUrl = "images/profile/" . $imageName;
                        $field["pro_pic"] = $imageUrl;

                    } else {
                        $returnArr = generateResponse("false", "Failed to upload image.", 500);
                    }
                } else {
                    $returnArr = generateResponse("false", "Error during image upload.", 400);
                }
            }




            if (!isset($returnArr)) {

                $table = "tbl_user";
                $where = "where id=" . '?' . "";
                $h = new Estate();
                $where_conditions = [$uid];
                $check = $h->restateupdateData_Api($field, $table, $where, $where_conditions);
            }
        } else {
            $returnArr    = generateResponse('false', "This Profile Not Exists!", 401);
        }
    }

    if (isset($returnArr)) {
        echo $returnArr;
    } else {
        if ($check) {
            $returnArr    = generateResponse('true', "Profile Updated Successfully", 200, array("full_name" => $full_name));
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
