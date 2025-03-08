<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';

header('Content-type: text/json');
try{
$lang_code = isset($_GET['lang']) ? $rstate->real_escape_string($_GET['lang']) : 'en';

$pol = array();
$c = array();
$sel = $rstate->query("select JSON_UNQUOTE(JSON_EXTRACT(name, '$.$lang_code')) as name ,id from tbl_government
          WHERE status=1
");
while ($row = $sel->fetch_assoc()) {

	$pol['id'] = $row['id'];
	$pol['name'] = $row['name'];




	$c[] = $pol;
}
if (empty($c)) {


	$returnArr    = generateResponse('true', "Government List Not Founded!", 200, array(
		"government_list" => $c
		,"length" => count($c),
	));
} else {

	$returnArr    = generateResponse('true', "Government List Founded!", 200, array(
		"government_list" => $c
		,"length" => count($c),
	));
}

echo $returnArr;
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ));
    echo $returnArr;
}
?>