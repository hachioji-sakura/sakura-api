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

$request_user_id = $_POST['user_id'];
$request_user_id = str_replace("'","",$request_user_id);
$request_user_id = str_replace('"',"",$request_user_id);

if (!$request_user_id){
// user_id is a mandatory parameter.
$res = array(
'status'=>'1',
);
goto exit_label;
}

$request_teacher_id = $_POST['teacher_id'];
$request_teacher_id = str_replace("'","",$request_teacher_id);
$request_teacher_id = str_replace('"',"",$request_teacher_id);

$request_student_no = $_POST['student_no'];
$request_student_no = str_replace("'","",$request_student_no);
$request_student_no = str_replace('"',"",$request_student_no);

$request_group_lesson_id = $_POST['group_lesson_id'];
$request_group_lesson_id = str_replace("'","",$request_group_lesson_id);
$request_group_lesson_id = str_replace('"',"",$request_group_lesson_id);

$request_subject_expr = $_POST['subject_expr'];
$request_subject_expr = str_replace("'","",$request_subject_expr);
$request_subject_expr = str_replace('"',"",$request_subject_expr);

$request_kind = $_POST['kind'];
$request_kind = str_replace("'","",$request_kind);
$request_kind = str_replace('"',"",$request_kind);

$request_dayofweek = $_POST['dayofweek'];
$request_dayofweek = str_replace("'","",$request_dayofweek);
$request_dayofweek = str_replace('"',"",$request_dayofweek);

$request_dayofmonth = $_POST['dayofmonth'];
$request_dayofmonth = str_replace("'","",$request_dayofmonth);
$request_dayofmonth = str_replace('"',"",$request_dayofmonth);

$request_startdate = $_POST['startdate'];
$request_startdate = str_replace("'","",$request_startdate);
$request_startdate = str_replace('"',"",$request_startdate);

if ($request_startdate) {
	sscanf($request_startdate,'%d-%d-%d',$year,$month,$day);

	if (!checkdate($month, $day, $year)){
	// illeagal date format.
	$res = array(
	'status'=>'2',
	);
	goto exit_label;
	} 
} 

$request_enddate = $_POST['enddate'];
$request_enddate = str_replace("'","",$request_enddate);
$request_enddate = str_replace('"',"",$request_enddate);

if ($request_enddate) {
	sscanf($request_enddate,'%d-%d-%d',$year,$month,$day);

	if (!checkdate($month, $day, $year)){
	// illeagal date format.
	$res = array(
	'status'=>'2',
	);
	goto exit_label;
	} 
} 

$request_starttime = $_POST['starttime'];
$request_starttime = str_replace("'","",$request_starttime);
$request_starttime = $_POST['starttime'];
$request_starttime = str_replace("'","",$request_starttime);
$request_starttime = str_replace('"',"",$request_starttime);

sscanf($request_starttime,'%d:%d:%d',$hour,$minute,$second);
if ($hour > 23 || $hour < 0 || $minute > 59 || $minute < 0 || $second > 59 || $second < 0 ){
	// illeagal time format.
	$res = array(
	'status'=>'3',
	'data'=>$rslt
	);
goto exit_label;
}
 
$request_endtime = $_POST['endtime'];
$request_endtime = str_replace("'","",$request_endtime);
$request_endtime = str_replace('"',"",$request_endtime);

sscanf($request_endtime,'%d:%d:%d',$hour,$minute,$second);
if ($hour > 23 || $hour < 0 || $minute > 59 || $minute < 0 || $second > 59 || $second < 0 ){
	// illeagal time format.
	$res = array(
	'status'=>'3',
	'data'=>$rslt
	);
goto exit_label;
}
 
$request_lecture_id = $_POST['lecture_id'];
$request_lecture_id = str_replace("'","",$request_lecture_id);
$request_lecture_id = str_replace('"',"",$request_lecture_id);


$request_work_id = $_POST['work_id'];
$request_work_id = str_replace("'","",$request_work_id);
$request_work_id = str_replace('"',"",$request_work_id);

$request_free = $_POST['free'];
$request_free = str_replace("'","",$request_free);
$request_free = str_replace('"',"",$request_free);

$request_place_id = $_POST['place_id'];
$request_place_id = str_replace("'","",$request_place_id);
$request_place_id = str_replace('"',"",$request_place_id);

$request_recess = $_POST['recess'];
$request_recess = str_replace("'","",$request_recess);
$request_recess = str_replace('"',"",$request_recess);

$request_updateuser = $_POST['updateuser'];
$request_updateuser = str_replace("'","",$request_updateuser);
$request_updateuser = str_replace('"',"",$request_updateuser);

if (!$request_updateuser){
// update user is a mandatory parameter.
$res = array(
'status'=>'1',
);
goto exit_label;
}


$request_comment = $_POST['comment'];
$request_comment = str_replace("'","",$request_comment);
$request_comment = str_replace('"',"",$request_comment);

$now = date('Y-m-d H:i:s');

try {

	$sql = "SELECT COUNT(*) AS COUNT FROM tbl_schedule_repeat WHERE delflag=0 AND user_id=? AND dayofweek=? AND starttime=? AND endtime=?";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1,$request_user_id, PDO::PARAM_STR);
        $stmt->bindValue(2,$request_dayofweek, PDO::PARAM_STR);
        $stmt->bindValue(3,$request_starttime, PDO::PARAM_STR);
        $stmt->bindValue(4,$request_endtime, PDO::PARAM_STR);
        $stmt->execute();
        $already_exist = (int)$stmt->fetchColumn();
        if ($already_exist > 0 ) {
                        // duplicate.
                $res = array(
                'status'=>'4',
                'data'=>$rslt
                );
                goto exit_label;
        }



	$sql = "INSERT INTO tbl_schedule_repeat (".
        	"user_id,".
        	"teacher_id,".
        	"student_no,".
        	"group_lesson_id,".
        	"subject_expr,".
        	"kind,". 
        	"dayofweek,". 
        	"dayofmonth,". 
        	"startdate,".
        	"enddate,".
        	"starttime,".
        	"endtime,".
        	"lecture_id,".
        	"work_id,".
        	"free,".
        	"place_id,".
        	"recess,".
        	"entrytime,".
        	"updateuser,".
        	"comment) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,? )"; 

		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(1,$request_user_id, PDO::PARAM_INT);
		$stmt->bindValue(2,$request_teacher_id, PDO::PARAM_INT);
		$stmt->bindValue(3,$request_student_no, PDO::PARAM_INT);
		$stmt->bindValue(4,$request_group_lesson_id, PDO::PARAM_INT);
		$stmt->bindValue(5,$request_subject_expr, PDO::PARAM_STR);
		$stmt->bindValue(6,$request_kind, PDO::PARAM_STR);
		$stmt->bindValue(7,$request_dayofweek, PDO::PARAM_STR);
		$stmt->bindValue(8,$request_dayofmonth, PDO::PARAM_STR);
		$stmt->bindValue(9,$request_startdate, PDO::PARAM_STR);
		$stmt->bindValue(10,$request_enddate, PDO::PARAM_STR);
		$stmt->bindValue(11,$request_starttime, PDO::PARAM_STR);
		$stmt->bindValue(12,$request_endtime, PDO::PARAM_STR);
		$stmt->bindValue(13,$request_lecture_id, PDO::PARAM_STR);
		$stmt->bindValue(14,$request_work_id, PDO::PARAM_STR);
		$stmt->bindValue(15,$request_free, PDO::PARAM_STR);
		$stmt->bindValue(16,$request_place_id, PDO::PARAM_STR);
		$stmt->bindValue(17,$request_recess, PDO::PARAM_STR);
		$stmt->bindValue(18,$now, PDO::PARAM_STR);
		$stmt->bindValue(19,$request_updateuser, PDO::PARAM_INT);
		$stmt->bindValue(20,$request_comment, PDO::PARAM_STR);

		$stmt->execute();


	$sql = "SELECT MAX(id) AS maxid FROM tbl_schedule_repeat";
	$stmt = $dbh->query($sql);
	$rslt = $stmt->fetch(PDO::FETCH_ASSOC);
	$maxid = $rslt['maxid'];
	$id = (int) $maxid;
	$sql = "INSERT INTO tbl_schedule_repeat_history (".
        	"id,".
        	"user_id,".
        	"teacher_id,".
        	"student_no,".
        	"group_lesson_id,".
        	"subject_expr,".
        	"kind,". 
        	"dayofweek,". 
        	"dayofmonth,". 
        	"startdate,".
        	"enddate,".
        	"starttime,".
        	"endtime,".
        	"lecture_id,".
        	"work_id,".
        	"free,".
        	"place_id,".
        	"recess,".
        	"entrytime,".
        	"updateuser,".
        	"comment) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,? )"; 

		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(1,$id, PDO::PARAM_INT);
		$stmt->bindValue(2,$request_user_id, PDO::PARAM_INT);
		$stmt->bindValue(3,$request_teacher_id, PDO::PARAM_INT);
		$stmt->bindValue(4,$request_student_no, PDO::PARAM_INT);
		$stmt->bindValue(5,$request_group_lesson_id, PDO::PARAM_INT);
		$stmt->bindValue(6,$request_subject_expr, PDO::PARAM_INT);
		$stmt->bindValue(7,$request_kind, PDO::PARAM_STR);
		$stmt->bindValue(8,$request_dayofweek, PDO::PARAM_STR);
		$stmt->bindValue(9,$request_dayofmonth, PDO::PARAM_STR);
		$stmt->bindValue(10,$request_startdate, PDO::PARAM_STR);
		$stmt->bindValue(11,$request_enddate, PDO::PARAM_STR);
		$stmt->bindValue(12,$request_starttime, PDO::PARAM_STR);
		$stmt->bindValue(13,$request_endtime, PDO::PARAM_STR);
		$stmt->bindValue(14,$request_lecture_id, PDO::PARAM_STR);
		$stmt->bindValue(15,$request_work_id, PDO::PARAM_STR);
		$stmt->bindValue(16,$request_free, PDO::PARAM_STR);
		$stmt->bindValue(17,$request_place_id, PDO::PARAM_STR);
		$stmt->bindValue(18,$request_recess, PDO::PARAM_STR);
		$stmt->bindValue(19,$now, PDO::PARAM_STR);
		$stmt->bindValue(20,$request_updateuser, PDO::PARAM_INT);
		$stmt->bindValue(21,$request_comment, PDO::PARAM_STR);

		$stmt->execute();
		
		$res = array(
			'status'=>'0',
			'id'=>$maxid
			);
exit_label:
		// exit the program.


} catch (Exception $e) {
	
	$res = array(
		'status'=>'error',
		);

}

header("Content-Type: application/json; charset=utf-8");

//var_dump($http_header);
//var_dump($token);

echo json_encode($res);

?>
