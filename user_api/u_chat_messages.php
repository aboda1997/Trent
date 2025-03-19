<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/constants.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';
require_once dirname(dirname(__FILE__)) . '/user_api/error_handler.php';

header('Content-type: text/json');
try {
	$chat_id = isset($_GET['chat_id']) ? $rstate->real_escape_string($_GET['chat_id']) : null;

	$uid = isset($_GET['uid']) ? $rstate->real_escape_string($_GET['uid']) : null;

	// Get pagination parameters
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Current page
	$itemsPerPage = isset($_GET['items_per_page']) ? intval($_GET['items_per_page']) : 10; // Items per page

	// Calculate offset
	$offset = ($page - 1) * $itemsPerPage;
	if ($uid == null) {
		$returnArr = generateResponse('false', "You must enter User id!", 400);
	} else if (validateIdAndDatabaseExistance($uid, 'tbl_user') === false) {
		$returnArr = generateResponse('false', "You must enter valid User id!", 400);
	} else if (checkTableStatus($uid, 'tbl_user') === false) {
		$returnArr = generateResponse('false', "The account associated with this user ID has been deleted.", 400);
	} else if (validateIdAndDatabaseExistance($chat_id, 'tbl_chat_property') === false) {
		$returnArr = generateResponse('false', "This chat Does not Exists", 400);
	} else if (validateIdAndDatabaseExistance($chat_id, 'tbl_chat_property', "user1 = $uid or user2 = $uid") === false) {
		$returnArr = generateResponse('false', "This chat Does not Belongs to that user ", 400);
	} else {
		$data = array();
		$message = array();
		$chat_row  = $rstate->query("select * from tbl_chat_property where id=" . $chat_id . "")->fetch_assoc();
		$user1 = $chat_row["user1"]; 
		$user2 = $chat_row["user2"];

		if ( $user1 != $uid) {
			$user1 = $chat_row["user2"]; 
			$user2 = $chat_row["user1"];
		} 

		// SQL Query
		$query = "
		SELECT *, 
			CASE WHEN sender_id = $user1 THEN 'true' ELSE 'false' END AS is_sender
		FROM tbl_messages 
		WHERE 
			chat_id = $chat_id
            and 
			(sender_id = $user1 AND receiver_id = $user2) 
			OR (sender_id = $user2 AND receiver_id = $user1 AND is_approved = 1)
		ORDER BY id DESC 
	";
	
	$sel_length  = $rstate->query($query)->num_rows;
	$query .= " LIMIT " . $itemsPerPage . " OFFSET " . $offset;
	$chat_messages = $rstate->query($query);
	while ($row = $chat_messages->fetch_assoc()) { 
		$messageArray = json_decode($row["message"], true);
		$message['message'] = isset($messageArray['message']) ? $messageArray['message'] : $messageArray;
		$message['id'] = $row['id'];
		$message['is_sender'] = $row['is_sender'];
		$message['img'] = $row['img'];
		$message['created_at'] = $row['created_at'];
		$message['sender_id'] = $row['sender_id'];
		$message['receiver_id'] = $row['receiver_id'];
	    $data[] = $message ;
	}
  
		$returnArr = generateResponse(
			'true',
			"Chat Messages Founded!",
			200,
			array(
				"chat_messages" => $data,
				"prop_id" =>  (int)$chat_row["prop_id"], 
				"length" =>  $sel_length
			)
		);
	}
	echo $returnArr;
}  catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile() ,  $e->getLine());
    echo $returnArr;
}
