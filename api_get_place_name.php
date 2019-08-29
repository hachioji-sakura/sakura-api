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

$request_id = $_GET['id'];
$request_id = str_replace("'","",$request_id);
$request_id = str_replace('"',"",$request_id);

try {

	$sql = "SELECT ".
		"id,".
		"name".
		" FROM tbl_place ";

		if ($request_id) {
			$sql .= " WHERE id='$request_id'";
		}
		
		$stmt = $dbh->query($sql);
		$rslt = $stmt->fetchAll(PDO::FETCH_ASSOC);

	if ($rslt) {
		$res = array(
			'status'=>'0',
			'data'=>$rslt
			);
	} else {
		$res = array(
			// Not found.
			'status'=>'1',
			'data'=>$request_target
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
