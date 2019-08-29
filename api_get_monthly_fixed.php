<?php
ini_set( 'display_errors', 0 );
require_once("../sakura/schedule/const/const.inc");
require_once("../sakura/schedule/func.inc");
require_once("./const.inc");
require_once("./func.inc");

define(API_TOKEN, '7511a32c7b6fd3d085f7c6cbe66049e7');

$http_header = getallheaders();
$token = "";
if(isset($http_header["Api-Token"])){
		$token = $http_header["Api-Token"];
}
// Comment out for temporary.
if ($token != API_TOKEN) {
	http_response_code(403);
	exit;
}

$request_year = $_GET['year'];
$request_year = str_replace("'","",$request_year);
$request_year = str_replace('"',"",$request_year);
$request_year = (int)$request_year;

$request_month = $_GET['month'];
$request_month = str_replace("'","",$request_month);
$request_month = str_replace('"',"",$request_month);
$request_month = (int)$request_month;

try {

	$sql = "SELECT ".
		"insert_timestamp".
		" FROM tbl_fixed WHERE year=? AND month=?";

//		$stmt = $db->prepare($sql);
		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(1,$request_year, PDO::PARAM_INT);
		$stmt->bindValue(2,$request_month, PDO::PARAM_INT);
		$stmt->execute();
		$rslt = $stmt->fetchAll(PDO::FETCH_ASSOC);

	if ($rslt) {
		$res = array(
			'status'=>'1',
			'data'=>$rslt
			);
	} else {
		$res = array(
			// Not found.
			'status'=>'0',
			);
	}
exit_label:
		// exit the program.


} catch (Exception $e) {
	
	$res = array(
		'status'=>'error',
		'data'=>''
		);

}

header("Content-Type: application/json; charset=utf-8");

//var_dump($http_header);
//var_dump($token);

echo json_encode($res);

?>
