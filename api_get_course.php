<?php
ini_set( 'display_errors', 0 );
require_once("/home/hachiojisakura/www/sakura/schedule/const/const.inc");
require_once("/home/hachiojisakura/www/sakura/schedule/func.inc");

define(API_TOKEN, '7511a32c7b6fd3d085f7c6cbe66049e7');

$http_header = getallheaders();
$token = "";
if(isset($http_header["Api-Token"])){
		$token = $http_header["Api-Token"];
}
if ($token != API_TOKEN) {
	http_response_code(403);
	exit;
}

$course_id = $_GET[id];
if ($course_id) {
	$course_id   = str_replace('"',"",str_replace("'","",$course_id));
}

try {

	$sql = "SELECT ".
		"course_id,".
		"course_name ".
		"FROM tbl_course";
	if ($course_id) {
		$sql .= " WHERE course_id='$course_id'";
	}
	$stmt = $db->query($sql);
	$rslt = $stmt->fetchAll(PDO::FETCH_ASSOC);

	if ($course_id) {
		$str1 = "id=$course_id Found";
		$str2 = "id=$course_id Not Found";
	} else {
		$str1 = "All data";
		$str2 = "No data";
	}

	if ($rslt) {
		$res = array(
			'status'=>'success',
			'message'=>'',
			'description'=>$str1,
			'data'=>$rslt
			);
	} else {
		$res = array(
			'status'=>'error',
			'message'=>'データが存在しません',
			'description'=>$str2,
			'data'=>$rslt
			);
	}

} catch (Exception $e) {
	
	$res = array(
		'status'=>'error',
		'message'=>'exception',
		'description'=>$e->getMessage(),
		'data'=>''
		);

}

header("Content-Type: application/json; charset=utf-8");

//var_dump($http_header);
//var_dump($token);

echo json_encode($res);

?>