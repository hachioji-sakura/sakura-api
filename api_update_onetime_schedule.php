<?php
ini_set('mbstring.internal_encoding', 'UTF-8');
ini_set( 'display_errors', 0 );
require_once("../sakura/schedule/const/const.inc");
require_once("../sakura/schedule/func.inc");

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

define('CANCEL_REASON2','当日');
define('CANCEL_REASON3','規定回数以上');
define('CANCEL_REASON4','休講');
define('CANCEL_REASON5','振替事前連絡あり');

define('COURSE_GROUP',2);
define('COURSE_FAMILY',3);

define('PLACE_AROLE',6);

$request_id = $_POST['id'];
$request_id = str_replace("'","",$request_id);
$request_id = str_replace('"',"",$request_id);
if (!$request_id){
// id is a mandatory parameter.
$res = array(
'status'=>'1',
);
goto exit_label;
} 

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

$request_repetition_id = $_POST['repetition_id'];
$request_repetition_id = str_replace("'","",$request_repetition_id);
$request_repetition_id = str_replace('"',"",$request_repetition_id);

$request_user_id = $_POST['user_id'];
$request_user_id = str_replace("'","",$request_user_id);
$request_user_id = str_replace('"',"",$request_user_id);

$request_teacher_id = $_POST['teacher_id'];
$request_teacher_id = str_replace("'","",$request_teacher_id);
$request_teacher_id = str_replace('"',"",$request_teacher_id);


$request_student_no = $_POST['student_no'];
$request_student_no = str_replace("'","",$request_student_no);
$request_student_no = str_replace('"',"",$request_student_no);

$request_ymd = $_POST['ymd'];
$request_ymd = str_replace("'","",$request_ymd);
$request_ymd = str_replace('"',"",$request_ymd);

if ($request_ymd){
	sscanf($request_ymd,'%d-%d-%d',$year,$month,$day);

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
$request_starttime = str_replace('"',"",$request_starttime);

if ($request_starttime){
	sscanf($request_starttime,'%d:%d:%d',$hour,$minute,$second);
	if ($hour > 23 || $hour < 0 || $minute > 59 || $minute < 0 || $second > 59 || $second < 0 ){
		// illeagal time format.
		$res = array(
		'status'=>'3',
		'data'=>$rslt
		);
	goto exit_label;
	}
}
 
$request_endtime = $_POST['endtime'];
$request_endtime = str_replace("'","",$request_endtime);
$request_endtime = str_replace('"',"",$request_endtime);

if ($request_endtime){
	sscanf($request_endtime,'%d:%d:%d',$hour,$minute,$second);
	if ($hour > 23 || $hour < 0 || $minute > 59 || $minute < 0 || $second > 59 || $second < 0 ){
		// illeagal time format.
		$res = array(
		'status'=>'3',
		'data'=>$rslt
		);
	goto exit_label;
	}
}
 
$request_lecture_id = $_POST['lecture_id'];
$request_lecture_id = str_replace("'","",$request_lecture_id);
$request_lecture_id = str_replace('"',"",$request_lecture_id);

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

$request_recess = $_POST['recess'];
$request_recess = str_replace("'","",$request_recess);
$request_recess = str_replace('"',"",$request_recess);

$request_confirm = $_POST['confirm'];
$request_confirm = str_replace("'","",$request_confirm);
$request_confirm = str_replace('"',"",$request_confirm);

$request_additional = $_POST['additional'];
$request_additional = str_replace("'","",$request_additional);
$request_additional = str_replace('"',"",$request_additional);

$request_comment = $_POST['comment'];
$request_comment = str_replace("'","",$request_comment);
$request_comment = str_replace('"',"",$request_comment);

$request_type = $_POST['type'];
$request_type = str_replace("'","",$request_type);
$request_type = str_replace('"',"",$request_type);

$now = date('Y-m-d H:i:s');
$user="hachiojisakura";
$pass="20160401sakurasaku";


try {

$dbh=new PDO('mysql:host=mysql720.db.sakura.ne.jp;dbname=hachiojisakura_calendar;charset=utf8',$user,$pass);
$dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

				// getting necessary information of the target data.
	$sql ="SELECT user_id,teacher_id,student_no,ymd,lecture_id,place_id,altsched_id,trial_id,cancel,cancel_reason " ;
	$sql .=" FROM tbl_schedule_onetime WHERE id = ? " ;
	$stmt = $dbh->prepare($sql);
	$stmt->bindValue(1,$request_id, PDO::PARAM_INT);
	$stmt->execute();
	$rslt = $stmt->fetch(PDO::FETCH_ASSOC);
	if (!$rslt){
		$res = array(
			'status'=>'4'
			);
		goto exit_label;
	}
	$got_user_id = $rslt['user_id'];
	$got_teacher_id = $rslt['teacher_id'];
	$got_student_no = $rslt['student_no'];
	$got_ymd = $rslt['ymd'];   
	$got_ym = mb_substr($got_ymd,0,7);   // getting year month data like 2020-01 format.
	$got_lecture_id = (int)$rslt['lecture_id'];
	$got_place_id = (int)$rslt['place_id'];
	$got_trial_id = (int)$rslt['trial_id'];
	$got_altsched_id = (int)$rslt['altsched_id'];
	$got_cancel = $rslt['cancel'];
	$got_cancel_reason = $rslt['cancel_reason'];

	if ( $got_lecture_id) {
				// course id の取得
		$sql = "SELECT course_id from tbl_lecture WHERE lecture_id= ?";
		$stmt = $db->prepare($sql);
		$stmt->bindValue(1,$got_lecture_id, PDO::PARAM_INT);
		$stmt->execute();
		$rslt = $stmt->fetch(PDO::FETCH_ASSOC);
						// the data is determined uniquely.
		$got_course_id = $rslt['course_id'];
	}
	$sql = "UPDATE tbl_schedule_onetime SET ";
	if ($request_repetition_id){
		$sql .=" repetition_id = '$request_repetition_id', " ;
	}
	if ($request_user_id){
		$sql .=" user_id = '$request_user_id', " ;
	}
	if ($request_teacher_id){
		$sql .=" teacher_id = '$request_teacher_id', " ;
	}
	if ($request_student_no){
		$sql .=" student_no = '$request_student_no', " ;
	}
	if ($request_ymd){
		$sql .=" ymd = '$request_ymd', " ;
	}
	if ($request_starttime){
		$sql .=" starttime = '$request_starttime', " ;
	}
	if ($request_endtime){
		$sql .=" endtime = '$request_endtime', " ;
	}
	if ($request_lecture_id){
		$sql .=" lecture_id = '$request_lecture_id', " ;
	}
	if ($request_group_lesson_id){
		$sql .=" group_lesson_id = '$request_group_lesson_id', " ;
	}
	if ($request_subject_expr){
		$sql .=" subject_expr = '$request_subject_expr', " ;
	}
	if ($request_work_id){
		$sql .=" work_id = '$request_work_id', " ;
	}
	if ($request_free){
		$sql .=" free = '$request_free', " ;
	}
	if ($request_cancel){
		$sql .=" cancel = '$request_cancel', " ;
	}
	if ($request_alternate){
		$sql .=" alternate = '$request_alternate', " ;
	}
	if ($request_altsched_id){
		$sql .=" altsched_id = '$request_altsched_id', " ;
	}
	if ($request_trial_id){
		$sql .=" trial_id = '$request_trial_id', " ;
	}
	if ($request_repeattimes){
		$sql .=" repeattimes = '$request_repeattimes', " ;
	}
	if ($request_place_id){
		$sql .=" place_id = '$request_place_id', " ;
	}
	if ($request_temporary){
		$sql .=" temporary = '$request_temporary', " ;
	}
	if ($request_confirm){
		$sql .=" confirm = '$request_confirm', " ;
	}
	if ($request_recess){
		$sql .=" recess = '$request_recess', " ;
	}
	$sql .=" updatetime = ?, updateuser = ? WHERE id = ? " ;

	$stmt = $dbh->prepare($sql);
	$stmt->bindValue(1, $now, PDO::PARAM_STR);
	$stmt->bindValue(2, $request_updateuser, PDO::PARAM_INT);
	$stmt->bindValue(3, $request_id, PDO::PARAM_INT);
	$stmt->execute();

	$absent_id = null; 	// Initialization
	$alaternate = 'false'; 	// Initialization
				// if the request is for absent, judge what kind of absent it is.

	$absent_month_start = $got_ym.'-01';  			// beggining of the month.
	$dateObj = new DateTime($absent_month_start);   
	$absent_month_start_ts = $dateObj ->getTimestamp();  
	$absent_year = date('Y',$absent_month_start_ts);  
	$absent_month = date('m',$absent_month_start_ts);  
	$absent_month_end_ts = mktime(0,0,0,$absent_month+1,0,$absent_year);  // the end of the month 
	$month_end = getdate($absent_month_end_ts); 			
	$absent_month_end = $month_end['year'].'-'.$month_end['mon'].'-'.$month_end['mday'];  

	if ($request_type==='rest' ){
						// 休み連絡
		if ($got_user_id > 100000 ) { // teacher or staff. No need to check absent category.
			$absent_id = 'a';
			$sql = "UPDATE tbl_schedule_onetime SET cancel = ? WHERE id = ?";	
			$stmt = $dbh->prepare($sql);
			$stmt->bindValue(1,$absent_id, PDO::PARAM_STR);
			$stmt->bindValue(2,$request_id, PDO::PARAM_INT);
			$stmt->execute();
			goto normal_label;
		}

		$currentObj = new DateTime();
								// 21:00 + 3 hours makes 0:00 next day.
		$currentObj -> add(DateInterval::createFromDateString('3 hours'));
		$current_timestamp = $currentObj -> getTimestamp();

                $limitdateObj = new Datetime($got_ymd);
                $limitdate_timestamp = $limitdateObj -> getTimestamp();
										// check how many lessons in a week .
		$sql = "SELECT COUNT(*) AS COUNT FROM tbl_schedule_repeat WHERE delflag=0 AND user_id=? AND lecture_id=? AND (enddate IS NULL OR enddate > ?)";
		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(1,$got_user_id, PDO::PARAM_INT);
		$stmt->bindValue(2,$got_lecture_id, PDO::PARAM_INT);
		$stmt->bindValue(3,$got_ymd, PDO::PARAM_STR);
		$stmt->execute();
		$absent_threshold = (int)$stmt->fetchColumn();

		if ($got_course_id == COURSE_GROUP || $got_course_id == COURSE_FAMILY){
			$cancel_kind = 'a1';	// 休み１
		} else {
			$cancel_kind = 'a2';	// 休み２
		}
							// 対象月のcancel_reason なしの休みの数を調べる
		$sql = "SELECT COUNT(*) AS COUNT FROM tbl_schedule_onetime WHERE delflag = 0 AND ymd BETWEEN ? AND ? AND cancel = ?"  
			." AND user_id = ? AND lecture_id = ? AND cancel_reason = ' '" ; 
		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(1,$absent_month_start, PDO::PARAM_STR);
		$stmt->bindValue(2,$absent_month_end, PDO::PARAM_STR);
		$stmt->bindValue(3,$cancel_kind, PDO::PARAM_STR);
		$stmt->bindValue(4,$got_user_id, PDO::PARAM_INT);
		$stmt->bindValue(5,$got_lecture_id, PDO::PARAM_INT);
		$stmt->execute();
		$absent_cnt = (int)$stmt->fetchColumn();

		$request_cancel_reason = ' ' ; 		// Initialization.
		if ($got_trial_id !== 0) {
					// 体験の休み
			$absent_id = 'a2';
			if ( $limitdate_timestamp < $current_timestamp ) {
				$request_cancel_reason = CANCEL_REASON2 ;
			}
		} else if ($got_altsched_id !== 0 ) {
					// 振替の休み
			$absent_id = 'a1';
			if ( $limitdate_timestamp > $current_timestamp ) {
				$request_cancel_reason = CANCEL_REASON5 ;
			} else {
				$request_cancel_reason = null;
			}
		} else if ($absent_cnt < $absent_threshold && $limitdate_timestamp > $current_timestamp ) {
						// can take alternate lessons.
			if ($got_place_id == PLACE_AROLE ){
				$absent_id = 'a2';
			} else if ($got_course_id == COURSE_GROUP || $got_course_id == COURSE_FAMILY ){
				$absent_id = 'a1';
			} else {
				$absent_id = 'a2';
			}
			$request_cancel_reason = ' ' ;
		} else if ($absent_cnt >= $absent_threshold) {
				// more than threshold.
			$absent_id = 'a2';
			$request_cancel_reason = CANCEL_REASON3 ;
		} else {
				// over the time limit.
			$absent_id = 'a2';
			$request_cancel_reason = CANCEL_REASON2 ;
		}
		$sql = "UPDATE tbl_schedule_onetime SET cancel = ? ,cancel_reason = ? WHERE id = ?";
		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(1,$absent_id, PDO::PARAM_STR);
		$stmt->bindValue(2,$request_cancel_reason, PDO::PARAM_STR);
		$stmt->bindValue(3,$request_id, PDO::PARAM_INT);
		$stmt->execute();

	} else if ($request_type==='rest_cancel') {
					// request for cancelation of the rest.
		if (($got_cancel ==='a2' && $got_cancel_reason === ' ') ||
			( ($got_course_id == COURSE_GROUP || $got_course_id == COURSE_FAMILY) && $got_cancel==='a1' && $got_cancel_reason===' ')) {
				// 当該月に休み２規定回数以上が既に入力されていないかを調べる
			$cancel_kind = 'a2';
			$sql = "SELECT id FROM tbl_schedule_onetime WHERE delflag = 0 AND ymd BETWEEN ? AND ? AND cancel = ?"  
			." AND user_id = ? AND lecture_id = ? AND cancel_reason = ?" ; 
			$stmt = $dbh->prepare($sql);
			$stmt->bindValue(1,$absent_month_start, PDO::PARAM_STR);
			$stmt->bindValue(2,$absent_month_end, PDO::PARAM_STR);
			$stmt->bindValue(3,$cancel_kind, PDO::PARAM_STR);
			$stmt->bindValue(4,$got_user_id, PDO::PARAM_INT);
			$stmt->bindValue(5,$got_lecture_id, PDO::PARAM_INT);
			$target_cancel_reason = CANCEL_REASON3;
			$stmt->bindValue(6,$target_cancel_reason, PDO::PARAM_STR);
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$row_cnt = count($result);
			if ($row_cnt > 0) {
						//	最初の休み２規定回数以上のcancel_reasonの値を空白にする	
				foreach ($result as $row ) {
							// 最初の１行のcancel列を更新し、cancel_reasonを空白にする
					$sql = "UPDATE tbl_schedule_onetime SET cancel = ?,cancel_reason = ' ' WHERE id=?";
					$stmt = $dbh->prepare($sql);
					$stmt->bindValue(1,$got_cancel, PDO::PARAM_STR);
					$stmt->bindValue(2,$row['id'], PDO::PARAM_INT);
					$stmt->execute();
					break; // foreach
				}
			}
		}
		$request_cancel = ' '; 			// reset the field
		$request_cancel_reason = ' '; 		// reset the field
		$sql = "UPDATE tbl_schedule_onetime SET cancel = ? ,cancel_reason = ? WHERE id = ?";
		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(1,$request_cancel, PDO::PARAM_STR);
		$stmt->bindValue(2,$request_cancel_reason, PDO::PARAM_STR);
		$stmt->bindValue(3,$request_id, PDO::PARAM_INT);
		$stmt->execute();
		$absent_id = null;
			
	} else if ($request_type==='lecture_cancel') {
									// 休講
		$request_cancel = 'a1'; 			// 休み１
		$request_cancel_reason = CANCEL_REASON4; 		// 休講
		$sql = "UPDATE tbl_schedule_onetime SET cancel = ? ,cancel_reason = ? WHERE id = ?";
		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(1,$request_cancel, PDO::PARAM_STR);
		$stmt->bindValue(2,$request_cancel_reason, PDO::PARAM_STR);
		$stmt->bindValue(3,$request_id, PDO::PARAM_INT);
		$stmt->execute();
		$absent_id = 'a1';

	} else if ($request_type==='absence') {
			// 出欠確認での欠席
		$alternate = 'false' ;
			// 生徒か事務員かで'calcel'列および'cancel_reason'列、Confirm列の値が変わる
		if ($got_user_id > 100000 ) { // Staff or teacher.
			$request_cancel = 'a';
			$request_cancel_reason = ' ';
			$request_confirm = 'a';
			$absent_id = 'a';
		} else {
			$request_cancel = 'a2';
			$request_cancel_reason = CANCEL_REASON2;
			$request_confirm = 'a2';
			$absent_id = 'a2';
		}
		$sql = "UPDATE tbl_schedule_onetime SET cancel = ? ,cancel_reason = ?, confirm=?  WHERE id = ?";
		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(1,$request_cancel, PDO::PARAM_STR);
		$stmt->bindValue(2,$request_cancel_reason, PDO::PARAM_STR);
		$stmt->bindValue(3,$request_confirm, PDO::PARAM_STR);
		$stmt->bindValue(4,$request_id, PDO::PARAM_INT);
		$stmt->execute();
	} else if ($request_type==='presence') {
				// 出欠確認での出席
		$sql = "UPDATE tbl_schedule_onetime SET confirm = ? WHERE id = ?";
		$stmt = $dbh->prepare($sql);
		$request_confirm = 'f'; 
		$stmt->bindValue(1,$request_confirm, PDO::PARAM_STR);
		$stmt->bindValue(2,$request_id, PDO::PARAM_INT);
		$stmt->execute();
	} else if ($request_type==='cancel') {
				// 予定の取り消し
		$sql = "UPDATE tbl_schedule_onetime SET cancel = ? WHERE id = ?";
		$stmt = $dbh->prepare($sql);
		$request_cancel = 'c'; 
		$stmt->bindValue(1,$request_cancel, PDO::PARAM_STR);
		$stmt->bindValue(2,$request_id, PDO::PARAM_INT);
		$stmt->execute();
	} else if ($request_type==='confirm') {
			// 仮スケジュールの先生確認状態
		$sql = "UPDATE tbl_schedule_onetime SET temporary = ? WHERE id = ?";
		$stmt = $dbh->prepare($sql);
		$request_temporary = 10; 
		$stmt->bindValue(1,$request_temporary, PDO::PARAM_INT);
		$stmt->bindValue(2,$request_id, PDO::PARAM_INT);
		$stmt->execute();
	} else if ($request_type==='fix') {
			// 仮スケジュールの確定
		$sql = "UPDATE tbl_schedule_onetime SET temporary = ? WHERE id = ?";
		$stmt = $dbh->prepare($sql);
		$request_temporary = 110; 
		$stmt->bindValue(1,$request_temporary, PDO::PARAM_INT);
		$stmt->bindValue(2,$request_id, PDO::PARAM_INT);
		$stmt->execute();
 
	} 
	$sql ="SELECT ymd,cancel,cancel_reason " ;
	$sql .=" FROM tbl_schedule_onetime WHERE id = ? " ;
	$stmt = $dbh->prepare($sql);
	$stmt->bindValue(1,$request_id, PDO::PARAM_INT);
	$stmt->execute();
	$rslt = $stmt->fetch(PDO::FETCH_ASSOC);
					// the data is determined uniquely.
	$got_ymd = $rslt['ymd'];
	$got_cancel = $rslt['cancel'];
	$got_cancel_reason = $rslt['cancel_reason'];
	$limitdate = null;
	$nextmonth = '+1 month';
        $dateObj = new DateTime($got_ymd);
	$dateObj->add(DateInterval::createFromDateString($nextmonth));
        $alt_limit_ts = $dateObj -> getTimestamp();
        $alt_limit = getdate($alt_limit_ts);
        $alt_limitdate = $alt_limit['year'].'-'.$alt_limit['mon'].'-'.$alt_limit['mday'];

	$alternate = 'false' ; // Initialization.
	if ($got_cancel==='a2' && $got_cancel_reason != CANCEL_REASON2 && $got_cancel_reason != CANCEL_REASON3 ){
		$alternate = 'true';
                $limitdate = $alt_limitdate;
	}
	if ($got_cancel==='a1' && $got_cancel_reason === CANCEL_REASON5 ){
				// もう一回振替可能
		$alternate = 'true';
                $limitdate = $alt_limitdate;
	}
normal_label:		
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
       	"trial_id,".
       	"repeattimes,".
       	"place_id,".
       	"temporary,".
       	"recess,".
       	"confirm,".
       	"additional,".
       	"updatetime,".
       	"updateuser,".
       	"comment) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,? )"; 

	$stmt = $dbh->prepare($sql);
	$stmt->bindValue(1,$request_id, PDO::PARAM_INT);
	$stmt->bindValue(2,$request_repetition_id, PDO::PARAM_INT);
	$stmt->bindValue(3,$request_user_id, PDO::PARAM_INT);
	$stmt->bindValue(4,$request_teacher_id, PDO::PARAM_INT);
	$stmt->bindValue(5,$request_student_no, PDO::PARAM_INT);
	$stmt->bindValue(6,$request_ymd, PDO::PARAM_STR);
	$stmt->bindValue(7,$request_starttime, PDO::PARAM_STR);
	$stmt->bindValue(8,$request_endtime, PDO::PARAM_STR);
	$stmt->bindValue(9,$request_lecture_id, PDO::PARAM_STR);
	$stmt->bindValue(10,$request_group_lesson_id, PDO::PARAM_STR);
	$stmt->bindValue(11,$request_subject_expr, PDO::PARAM_STR);
	$stmt->bindValue(12,$request_work_id, PDO::PARAM_INT);
	$stmt->bindValue(13,$request_free, PDO::PARAM_STR);
	$stmt->bindValue(14,$request_cancel, PDO::PARAM_STR);
	$stmt->bindValue(15,$request_cancel_reason, PDO::PARAM_STR);
	$stmt->bindValue(16,$request_alternate, PDO::PARAM_STR);
	$stmt->bindValue(17,$request_altsched_id, PDO::PARAM_INT);
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

				// 更新イメージの取得

	$sql = "SELECT ".
               "id,".
               "repetition_id,".
               "user_id,".
               "teacher_id,".
               "student_no,".
               "ymd,".
               "starttime,".
               "endtime,".
               "lecture_id,".
               "subject_expr,".
               "work_id,".
               "cancel,".
               "cancel_reason,".
               "altsched_id,".
               "trial_id,".
               "place_id,".
               "temporary,".
               "confirm,".
               "entrytime,".
               "updatetime,".
               "updateuser,".
	       	"comment".
              " FROM tbl_schedule_onetime WHERE id='$request_id'";

	        $stmt = $dbh->query($sql);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$response = array_merge($result[0],array('alternate'=>$alternate,'altlimitdate'=>$limitdate));

		$res = array(
			'status'=>'0',
			'cancel'=>$absent_id,
			'data'=>$response
			);
exit_label:
		// exit the program.


} catch (Exception $e) {
	
	$res = array(
		'status'=>'error',
		'cancel'=>$absent_id, 
		);

}

header("Content-Type: application/json; charset=utf-8");

//var_dump($token);

echo json_encode($res);

?>
