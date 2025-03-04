<?php 
require dirname( dirname(__FILE__) ).'/include/reconfig.php';

header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);
$lang_code = 'en';
 
 if($_GET['lang']){
$lang_code = $_GET['lang'];
 }
$pol = array();
$c = array();
$cc = array();
$ccc = array();
$period = array();

$sel = $rstate->query("SELECT MIN(price) AS min_price, MAX(price) AS max_price FROM tbl_property WHERE status=1")->fetch_assoc();
$c['min_price']  = $sel['min_price'];
$c['max_price']  = $sel['max_price'];
$loop = 1 ; 
$periods = [
   [ "name" => ["ar" => "يومي", "en" => "daily"] , "id"=> 'd'],
    [ "name" => ["ar" => "شهري", "en" => "monthly"] ,  "id"=> 'm']
];
while($loop >=0){
    $period['id'] = $periods[$loop]['id'] ; 
    $period['name'] = $periods[$loop]['name'][$lang_code];
    $loop-=1;
    $ccc[] = $period;

 }
$sel = $rstate->query("select JSON_UNQUOTE(JSON_EXTRACT(name, '$.$lang_code')) as name ,id from tbl_compound ");
while($row = $sel->fetch_assoc())
{
   
		$pol['id'] = $row['id'];
		$pol['name'] = $row['name'];
		
		$cc[] = $pol;
	
	
}
if($sel->num_rows > 0)
{
    $sel = $sel->fetch_assoc();
    $returnArr = array(   
    
    "price_range" => $c
    ,
    "compound_names" => $cc,
    "period" => $ccc,
    

    "ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Api Filiters Founded!");

}

echo json_encode($returnArr);
?>