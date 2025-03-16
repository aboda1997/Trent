<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/constants.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';

header('Content-type: text/json');
try {
	$chat_id = isset($_GET['chat_id']) ? $rstate->real_escape_string($_GET['chat_id']) : null;

	$uid = isset($_GET['uid']) ? $rstate->real_escape_string($_GET['uid']) : null;

	if ($uid == null) {
		$returnArr = generateResponse('false', "You must enter User id!", 400);
	} else if (validateIdAndDatabaseExistance($uid, 'tbl_user') === false) {
		$returnArr = generateResponse('false', "You must enter valid User id!", 400);
	} else if (checkTableStatus($uid, 'tbl_user') === false) {
		$returnArr = generateResponse('false', "The account associated with this user ID has been deleted.", 400);
	} else if (validateIdAndDatabaseExistance($chat_id, 'tbl_chat') === false) {
		$returnArr = generateResponse('false', "This chat Does not Exists", 400);
	} else if (validateIdAndDatabaseExistance($chat_id, 'tbl_chat', "sender_id = $uid or receiver_id = $uid") === false) {
		$returnArr = generateResponse('false', "This chat Does not Belongs to that user ", 400);
	} else {
		$data = array();
		$message = array();
		$chat_row  = $rstate->query("select * from tbl_chat where id=" . $chat_id . "")->fetch_assoc();
		$user1 = $uid; //main user data  
		$user2 = $uid;

		if ($chat_row["sender_id"] == $user1) {
			$user2 = $chat_row['receiver_id'];
		} else {
			$user2 = $chat_row['sender_id'];
		}

		// SQL Query
		$query = "
		SELECT *, 
			CASE WHEN sender_id = $user1 THEN 'true' ELSE 'false' END AS is_sender
		FROM tbl_chat 
		WHERE 
			(sender_id = $user1 AND receiver_id = $user2) 
			OR (sender_id = $user2 AND receiver_id = $user1 AND is_approved = 1)
		ORDER BY id ASC
	";
	$chat_messages = $rstate->query($query);
	while ($row = $chat_messages->fetch_assoc()) { 
		$message['message'] = $row['message'];
		$message['is_sender'] = $row['is_sender'];
		$message['img'] = $row['img'];
		$message['created_at'] = $row['created_at'];
	    $data[] = $message ;
	}
  
		$returnArr = generateResponse(
			'true',
			"Chat Messages Founded!",
			200,
			array(
				"chat_messages" => $data
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
