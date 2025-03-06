<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';

header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);
$lang_code = 'en';

if ($_GET['lang']) {
	$lang_code = $_GET['lang'];
}
$pol = array();
$c = array();
$sel = $rstate->query("select JSON_UNQUOTE(JSON_EXTRACT(name, '$.$lang_code')) as name ,id from tbl_government ");
while ($row = $sel->fetch_assoc()) {

	$pol['id'] = $row['id'];
	$pol['name'] = $row['name'];




	$c[] = $pol;
}
if (empty($c)) {


	$returnArr    = generateResponse('false', "Government List Not Founded!", 404, array(
		"government_list" => $c
	));
} else {

	$returnArr    = generateResponse('true', "Government List Founded!", 200, array(
		"government_list" => $c
	));
}

echo $returnArr;
?>