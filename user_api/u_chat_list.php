<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/constants.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';
require_once dirname(dirname(__FILE__)) . '/user_api/error_handler.php';

header('Content-Type: application/json');
try {

    $uid = isset($_GET['uid']) ? $rstate->real_escape_string($_GET['uid']) : null;

    // Get pagination parameters
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Current page
    $itemsPerPage = isset($_GET['items_per_page']) ? intval($_GET['items_per_page']) : 10; // Items per page
    $prop_id = isset($_GET['prop_id']) ? intval($_GET['prop_id']) : null; // Items per page

    // Calculate offset
    $offset = ($page - 1) * $itemsPerPage;

    if ($uid == null) {
        $returnArr = generateResponse('false', "You must enter User id.", 400);
    } else if (validateIdAndDatabaseExistance($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', "You must enter valid User id.", 400);
    } else if (checkTableStatus($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', "The account associated with this user ID has been deleted.", 400);
    } else {
        $data = array();
        $chat_list = array();
        $query = "
        SELECT c.* , tcp.prop_id
        FROM tbl_messages c
        INNER JOIN (
            SELECT 
                LEAST(sender_id, receiver_id) AS user1, 
                GREATEST(sender_id, receiver_id) AS user2, 
                MAX(id) AS last_msg_id
            FROM tbl_messages
            WHERE 
                (receiver_id = $uid AND is_approved = 1)  
                OR sender_id = $uid   
            GROUP BY user1, user2 , chat_id
            order by last_msg_id desc 
        ) last_msgs 
        ON 
            (LEAST(c.sender_id, c.receiver_id) = last_msgs.user1 AND 
             GREATEST(c.sender_id, c.receiver_id) = last_msgs.user2 AND 
             c.id = last_msgs.last_msg_id)
          INNER JOIN tbl_chat_property tcp 
            ON c.chat_id = tcp.id 
    ";
        if ($prop_id !== null) {
            $query .= "WHERE tcp.prop_id = $prop_id ";
        }
        $query .=   " ORDER BY last_msgs.last_msg_id DESC";


        $sel_length  = $rstate->query($query)->num_rows;
        $query .= " LIMIT " . $itemsPerPage . " OFFSET " . $offset;
        $chat_data = $rstate->query($query);
        while ($row = $chat_data->fetch_assoc()) {
            $receiver_id = $uid;
            if ($uid == $row['sender_id']) {
                $receiver_id = $row['receiver_id'];
            } else {
                $receiver_id = $row['sender_id'];
            }
            $user_data = $rstate->query("select * from tbl_user where id=" . $receiver_id . "")->fetch_assoc();

            $data['receiver_name'] = $user_data['name'];
            $data['receiver_id'] = $receiver_id;
            $data['receiver_image'] = $user_data['pro_pic'];
            $messageArray = json_decode($row["message"], true);
            $data['message'] = isset($messageArray['message']) ? $messageArray['message'] : $messageArray;
            $data['chat_id'] = (int)$row["chat_id"];
            $pro_id =(int)$row["prop_id"];
            $sel = $rstate->query("select title from tbl_property where  id=" . $pro_id .  "")->fetch_assoc();

            $data['prop_id'] = (int)$row["prop_id"];
            $data['prop_title'] = json_decode($sel['title']??'', true)['en'] ?? '' ;

            $chat_list[]  = $data;
        }
        $returnArr = generateResponse(
            'true',
            "Chat List Founded!",
            200,
            array(
                "chat_list" => $chat_list,
                "length" =>  $sel_length

            )
        );
    }
    echo $returnArr;
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}
