 <?php
	require dirname(dirname(__FILE__)) . '/include/reconfig.php';
	require dirname(dirname(__FILE__)) . '/include/constants.php';
	require dirname(dirname(__FILE__)) . '/include/helper.php';
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Pragma: no-cache"); // For older HTTP/1.0 clients
	header('Content-Type: application/json');
	try{
		$returnArr = generateResponse(
			'true',
			"User Exist!",
			200,
			array(
				"user_data" => []
			)
		);
	echo $returnArr;

} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ));
    echo $returnArr;
	
}
?>