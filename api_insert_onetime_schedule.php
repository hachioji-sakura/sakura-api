<?php
ini_set( 'display_errors', 0 );
require_once("../sakura/schedule/const/const.inc");
require_once("../sakura/schedule/func.inc");
require_once("./const.inc");
require_once("./func.inc");

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

$request_repetition_id = $_POST['repetition_id'];
$request_repetition_id = str_replace("'","",$request_repetition_id);
$request_repetition_id = str_replace('"',"",$request_repetition_id);

$request_teacher_id = $_POST['teacher_id'];
$request_teacher_id = str_replace("'","",$request_teacher_id);
$request_teacher_id = str_replace('"',"",$request_teacher_id);

$request_student_no = $_POST['student_no'];
$request_student_no = str_replace("'","",$request_student_no);
$request_student_no = str_replace('"',"",$request_student_no);

$request_ymd = $_POST['ymd'];
$request_ymd = str_replace("'","",$request_ymd);
$request_ymd = str_replace('"',"",$request_ymd);

sscanf($request_ymd,'%d-%d-%d',$year,$month,$day);

if (!checkdate($month, $day, $year)){
// illeagal date format.
$res = array(
'status'=>'2',
'request_ymd'=>$POST['ymd']
);
goto exit_label;
} 

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

if ($request_user_id < 100000 && $request_lecture_id == "" ){
	// lecture_id is not specified for the student.
	$res = array(
	'status'=>'5',
	'data'=>$rslt
	);
goto exit_label;
}
$request_group_lesson_id = $_POST['group_lesson_id'];
$request_group_lesson_id = str_replace("'","",$request_group_lesson_id);
$request_group_lesson_id = str_replace('"',"",$request_group_lesson_id);

$request_subject_expr = $_POST['subject_expr'];
$request_subject_expr = str_replace("'","",$request_subject_expr);
$request_subject_expr = str_replace('"',"",$request_subject_expr);

$request_work_id = $_POST['work_id'];
$request_work_id = str_replace("'","",$request_work_id);
$request_work_id = str_replace('"',"",$request_work_id);

$request_free = $_POST['free'];
$request_free = str_replace("'","",$request_free);
$request_free = str_replace('"',"",$request_free);

$request_cancel = $_POST['cancel'];
$request_cancel = str_replace("'","",$request_cancel);
$request_cancel = str_replace('"',"",$request_cancel);

$request_cancel_reason = $_POST['cancel_reason'];
$request_cancel_reason = str_replace("'","",$request_cancel_reason);
$request_cancel_reason = str_replace('"',"",$request_cancel_reason);

$request_alternate = $_POST['alternate'];
$request_alternate = str_replace("'","",$request_alternate);
$request_alternate = str_replace('"',"",$request_alternate);

$request_altsched_id = $_POST['altsched_id'];
$request_altsched_id = str_replace("'","",$request_altsched_id);
$request_altsched_id = str_replace('"',"",$request_altsched_id);

if ($_POST['altlimitdate']){
				// altlimitdate is specified.
	$request_altlimitdate = $_POST['altlimitdate'];
	$request_altlimitdate = str_replace("'","",$request_altlimitdate);
	$request_altlimitdate = str_replace('"',"",$request_altlimitdate);
} else {
				// altlimitdate is not specified.
	$request_altlimitdate = NULL;
}

$request_trial_id = $_POST['trial_id'];
$request_trial_id = str_replace("'","",$request_trial_id);
$request_trial_id = str_replace('"',"",$request_trial_id);

$request_repeattimes = $_POST['repeattimes'];
$request_repeattimes = str_replace("'","",$request_repeattimes);
$request_repeattimes = str_replace('"',"",$request_repeattimes);

$request_place_id = $_POST['place_id'];
$request_place_id = str_replace("'","",$request_place_id);
$request_place_id = str_replace('"',"",$request_place_id);

$request_temporary = $_POST['temporary'];
$request_temporary = str_replace("'","",$request_temporary);
$request_temporary = str_replace('"',"",$request_temporary);

if (!$request_temporary && $request_trial_id) { $request_temporary = 1; }

$request_recess = $_POST['recess'];
$request_recess = str_replace("'","",$request_recess);
$request_recess = str_replace('"',"",$request_recess);

$request_confirm = $_POST['confirm'];
$request_confirm = str_replace("'","",$request_confirm);
$request_confirm = str_replace('"',"",$request_confirm);

$request_additional = $_POST['additional'];
$request_additional = str_replace("'","",$request_additional);
$request_additional = str_replace('"',"",$request_additional);

$request_updateuser = $_POST['updateuser'];
$request_updateuser = str_replace("'","",$request_updateuser);
$request_updateuser = str_replace('"',"",$request_updateuser);

$request_comment = $_POST['comment'];
$request_comment = str_replace("'","",$request_comment);
$request_comment = str_replace('"',"",$request_comment);

$request_lmsnotify = $_POST['lmsnotify'];
$request_lmsnotify = str_replace("'","",$request_lmsnotify);
$request_lmsnotify = str_replace('"',"",$request_lmsnotify);

$now = date('Y-m-d H:i:s');

try {

	$sql = "INSERT INTO tbl_schedule_onetime (".
        	"repetition_id,".
        	"user_id,".
        	"teacher_id,".
        	"student_no,".
        	"ymd,". 
        	"starttime,".
        	"endtime,".
        	"lecture_id,".
        	"group_lesson_id,".
        	"subject_expr,".
        	"work_id,".
        	"free,".
        	"cancel,".
        	"cancel_reason,".
        	"alternate,".
        	"altsched_id,".
        	"altlimitdate,".
        	"trial_id,".
        	"repeattimes,".
        	"place_id,".
        	"temporary,".
        	"recess,".
        	"confirm,".
        	"additional,".
        	"entrytime,".
        	"updateuser,".
        	"comment) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"; 

		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(1,$request_repetition_id, PDO::PARAM_INT);
		$stmt->bindValue(2,$request_user_id, PDO::PARAM_INT);
		$stmt->bindValue(3,$request_teacher_id, PDO::PARAM_INT);
		$stmt->bindValue(4,$request_student_no, PDO::PARAM_INT);
		$stmt->bindValue(5,$request_ymd, PDO::PARAM_STR);
		$stmt->bindValue(6,$request_starttime, PDO::PARAM_STR);
		$stmt->bindValue(7,$request_endtime, PDO::PARAM_STR);
		$stmt->bindValue(8,$request_lecture_id, PDO::PARAM_INT);
		$stmt->bindValue(9,$request_group_lesson_id, PDO::PARAM_INT);
		$stmt->bindValue(10,$request_subject_expr, PDO::PARAM_INT);
		$stmt->bindValue(11,$request_work_id, PDO::PARAM_INT);
		$stmt->bindValue(12,$request_free, PDO::PARAM_STR);
		$stmt->bindValue(13,$request_cancel, PDO::PARAM_STR);
		$stmt->bindValue(14,$request_cancel_reason, PDO::PARAM_STR);
		$stmt->bindValue(15,$request_alternate, PDO::PARAM_STR);
		$stmt->bindValue(16,$request_altsched_id, PDO::PARAM_INT);
		$stmt->bindValue(17,$request_altlimitdate, PDO::PARAM_STR);
		$stmt->bindValue(18,$request_trial_id, PDO::PARAM_STR);
		$stmt->bindValue(19,$request_repeattimes, PDO::PARAM_INT);
		$stmt->bindValue(20,$request_place_id, PDO::PARAM_INT);
		$stmt->bindValue(21,$request_temporary, PDO::PARAM_INT);
		$stmt->bindValue(22,$request_recess, PDO::PARAM_STR);
		$stmt->bindValue(23,$request_confirm, PDO::PARAM_STR);
		$stmt->bindValue(24,$request_additional, PDO::PARAM_STR);
		$stmt->bindValue(25,$now, PDO::PARAM_STR);
		$stmt->bindValue(26,$request_updateuser, PDO::PARAM_INT);
		$stmt->bindValue(27,$request_comment, PDO::PARAM_STR);

		$stmt->execute();


	$sql = "SELECT MAX(id) AS maxid FROM tbl_schedule_onetime";
	$stmt = $dbh->query($sql);
	$rslt = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach ($rslt as $row) {
		$maxid = $row['maxid'];
	}
	$id = (int) $maxid;
	$sql = "INSERT INTO tbl_schedule_onetime_history (".
        	"id,".
        	"repetition_id,".
        	"user_id,".
        	"teacher_id,".
        	"student_no,".
        	"ymd,". 
        	"starttime,".
        	"endtime,".
        	"lecture_id,".
        	"group_lesson_id,".
        	"subject_expr,".
        	"work_id,".
        	"free,".
        	"cancel,".
        	"cancel_reason,".
        	"alternate,".
        	"altsched_id,".
        	"altlimitdate,".
        	"trial_id,".
        	"repeattimes,".
        	"place_id,".
        	"temporary,".
        	"recess,".
        	"confirm,".
        	"additional,".
        	"entrytime,".
        	"updateuser,".
        	"comment) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,? )"; 

		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(1,$id, PDO::PARAM_INT);
		$stmt->bindValue(2,$request_repetition_id, PDO::PARAM_INT);
		$stmt->bindValue(3,$request_user_id, PDO::PARAM_INT);
		$stmt->bindValue(4,$request_teacher_id, PDO::PARAM_INT);
		$stmt->bindValue(5,$request_student_no, PDO::PARAM_INT);
		$stmt->bindValue(6,$request_ymd, PDO::PARAM_STR);
		$stmt->bindValue(7,$request_starttime, PDO::PARAM_STR);
		$stmt->bindValue(8,$request_endtime, PDO::PARAM_STR);
		$stmt->bindValue(9,$request_lecture_id, PDO::PARAM_INT);
		$stmt->bindValue(10,$request_group_lesson_id, PDO::PARAM_INT);
		$stmt->bindValue(11,$request_subject_expr, PDO::PARAM_INT);
		$stmt->bindValue(12,$request_work_id, PDO::PARAM_INT);
		$stmt->bindValue(13,$request_free, PDO::PARAM_STR);
		$stmt->bindValue(14,$request_cancel, PDO::PARAM_STR);
		$stmt->bindValue(15,$request_cancel_reason, PDO::PARAM_STR);
		$stmt->bindValue(16,$request_alternate, PDO::PARAM_STR);
		$stmt->bindValue(17,$request_altsched_id, PDO::PARAM_INT);
		$stmt->bindValue(18,$request_altlimitdate, PDO::PARAM_INT);
		$stmt->bindValue(19,$request_trial_id, PDO::PARAM_STR);
		$stmt->bindValue(20,$request_repeattimes, PDO::PARAM_INT);
		$stmt->bindValue(21,$request_place_id, PDO::PARAM_INT);
		$stmt->bindValue(22,$request_temporary, PDO::PARAM_INT);
		$stmt->bindValue(23,$request_recess, PDO::PARAM_STR);
		$stmt->bindValue(24,$request_confirm, PDO::PARAM_STR);
		$stmt->bindValue(25,$request_additional, PDO::PARAM_STR);
		$stmt->bindValue(26,$now, PDO::PARAM_STR);
		$stmt->bindValue(27,$request_updateuser, PDO::PARAM_INT);
		$stmt->bindValue(28,$request_comment, PDO::PARAM_STR);

		$stmt->execute();
		
		if ($request_lmsnotify){
						// function call.
			$result = set_lmsnotify($maxid);
			if ($result === FALSE){
				$res = array(
				'status'=>'lmsimport failed',
				'id'=>$maxid
				);
			} else { 
				$res = array(
				'status'=>'0',
				'id'=>$maxid,
				'importstatus'=>$result
				);
			} 
		} else {
			$res = array(
			'status'=>'0',
			'id'=>$maxid
			);
		}
exit_label:
		// exit the program.


} catch (Exception $e) {
	
	$res = array(
		'status'=>'error',
		);

}

function set_lmsnotify($maxid){
		// this function notify update of the schedule to lms.
	$result = NULL;		// initialization.
	$senddata = array(
		'id' => $maxid
	);
	$query = http_build_query($senddata);
	$platform = PLATFORM;
        if ($platform === 'staging' ){
        	$result = file_get_contents('https://staging.sakuraone.jp/import/schedules?'.$query);
        } else if ($platform === 'production' ){
        	$result = file_get_contents('https://sakuraone.jp/import/schedules?'.$query);
	} 
	if ($result === FALSE ){
		return($result);
	}

				// http-post:
        if ($platform === 'staging' ){
        	$url = 'https://staging.sakuraone.jp/import/schedules?'.$query;
        } else if ($platform === 'production' ){
        	$url = 'https://sakuraone.jp/import/schedules?'.$query;
	} 
	$header = array(
		'Content-Type: application/x-www-form-urlencoded',
		'Content-Length: '.strlen($url),
		'Api-Token: 7511a32c7b6fd3d085f7c6cbe66049e7'  
	);
	$options = array('http' => array(
		'method' => 'POST',
		'header' => implode("\r\n",$header)
		)
	);
	$ctx = stream_context_create($options);
       	$result = file_get_contents($url,false,$ctx);
	if(!empty($result)) $result = json_decode($result);
	return($result);
}

header("Content-Type: application/json; charset=utf-8");

//var_dump($http_header);
//var_dump($token);

echo json_encode($res);

?>
