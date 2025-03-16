<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/constants.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';

header('Content-type: text/json');
try {

    $uid = isset($_GET['uid']) ? $rstate->real_escape_string($_GET['uid']) : null;

    if ($uid == null) {
        $returnArr = generateResponse('false', "You must enter User id!", 400);
    } else if (validateIdAndDatabaseExistance($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', "You must enter valid User id!", 400);
    } else if (checkTableStatus($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', "The account associated with this user ID has been deleted.", 400);
    } else {
        $data = array();
        $chat_list = array();
        $query = "
        SELECT c.*
        FROM tbl_chat c
        INNER JOIN (
            SELECT 
                LEAST(sender_id, receiver_id) AS user1, 
                GREATEST(sender_id, receiver_id) AS user2, 
                MAX(id) AS last_msg_id
            FROM tbl_chat
            WHERE 
                (receiver_id = $uid AND is_approved = 1)  
                OR sender_id = $uid   
            GROUP BY user1, user2
        ) last_msgs 
        ON 
            (LEAST(c.sender_id, c.receiver_id) = last_msgs.user1 AND 
             GREATEST(c.sender_id, c.receiver_id) = last_msgs.user2 AND 
             c.id = last_msgs.last_msg_id);
    ";
        $chat_data = $rstate->query($query);
        while ($row = $chat_data->fetch_assoc()) { 
            $receiver_id = $uid;
            if($uid == $row['sender_id']){
              $receiver_id = $row['receiver_id'];
            }else{
                $receiver_id = $row['sender_id'];
            }
            $user_data = $rstate->query("select * from tbl_user where id=" . $receiver_id . "")->fetch_assoc();

            $data['receiver_name'] = $user_data['name'];
            $data['receiver_image'] = $user_data['pro_pic'];
            $data['message'] = $row["message"];
            $data['id'] = $row["id"];
    
            $chat_list[]  = $data;
    
        }
        $returnArr = generateResponse(
            'true',
            "Chat List Founded!",
            200,
            array(
                "chat_list" => $chat_list
            )
        );
    }
    echo $returnArr;
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ));
    echo $returnArr;
}
