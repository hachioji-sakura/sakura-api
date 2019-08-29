<?php
ini_set( 'display_errors', 0 );
require_once("../sakura/schedule/const/const.inc");
require_once("../sakura/schedule/func.inc");

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

$request_id = $_GET[id];
$request_id   = str_replace('"',"",str_replace("'","",$request_id));

try {

	$sql = "SELECT ".
		"tbl_text.text_id as id,".
		"tbl_text.name,".
		"tbl_text.publisher_id,".
		"tbl_text_publisher_name.name as publisher_name,".
		"tbl_text.teika_price,".
		"tbl_text.tewatashi_price1,".
		"tbl_text.tewatashi_price2,".
		"tbl_text.tewatashi_price3,".
		"tbl_text.publisher_price,".
		"tbl_text.level,".
		"tbl_text.explain,".
		"tbl_text_subject_name.name as subject,".
		"tbl_grade_name.name as grade,".
		"tbl_text_supplier.supplier_id, ".
		"tbl_text_supplier_name.name as supplier_name ".
		"FROM tbl_text ".
		"LEFT JOIN tbl_text_subject ON  tbl_text_subject.text_id = tbl_text.text_id ".
		"LEFT JOIN tbl_text_subject_name ON tbl_text_subject_name.subject_id=tbl_text_subject.subject_id ".
		"LEFT JOIN tbl_text_grade ON tbl_text_grade.text_id=tbl_text.text_id ".
		"LEFT JOIN tbl_grade_name ON tbl_grade_name.grade_id=tbl_text_grade.grade ".
		"LEFT JOIN tbl_text_publisher_name ON tbl_text_publisher_name.publisher_id=tbl_text.publisher_id ".
		"LEFT JOIN tbl_text_supplier ON tbl_text_supplier.text_id=tbl_text.text_id ".
		"LEFT JOIN tbl_text_supplier_name ON tbl_text_supplier_name.supplier_id=tbl_text_supplier.supplier_id";
	if ($request_id) {
		$sql .= " WHERE tbl_text.text_id='$request_id'";
	}
	$stmt = $db->query($sql);
	$rslt = $stmt->fetchAll(PDO::FETCH_ASSOC);

	if ($request_id) {
		$str1 = "id=$request_id Found";
		$str2 = "id=$request_id Not Found";
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

echo json_encode($res);

?>
