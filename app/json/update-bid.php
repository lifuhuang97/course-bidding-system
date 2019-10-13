<?php
require_once '../include/common.php';
require_once '../include/function.php';
if (isset($_REQUEST['r'])){
    $request=json_decode($_REQUEST['r'], JSON_PRETTY_PRINT);
    $errors=[];
    if (!isset($request['userid']) || strlen(trim($request['userid']))==0){
        $errors[]="missing userid";
    }else{
        $userid=$request['userid'];
    }
    if (!isset($request['amount']) || strlen(trim($request['amount']))==0){
        $errors[]="missing amount";
    }else{
        $amount=$request['amount'];
    }
    if (!isset($request['course']) || strlen(trim($request['course']))==0){
        $errors[]="missing course";
    }else{
        $course=$request['course'];
    }
    if (!isset($request['section']) || strlen(trim($request['section']))==0){
        $errors[]="missing section";
    }else{
        $section=$request['section'];
    }
}else{
    $errors = [ isMissingOrEmpty ('userid'), 
            isMissingOrEmpty ('amount'),
            isMissingOrEmpty ('course'),
            isMissingOrEmpty ('section') ];
    $errors = array_filter($errors);
    if (isEmpty($errors)) {
        $userid = $_REQUEST['userid'];
        $amount = $_REQUEST['amount'];
        $course = $_REQUEST['course'];
        $section = $_REQUEST['section'];
    }
}
// var_dump($errors);
if (!isEmpty($errors)) {
    $result = [
        "status" => "error",
        "message" => array_values($errors)
        ];
}
else{
    //validate
    if(!is_numeric($amount) || ($amount<10) || $amount!=number_format($amount,2,'.','')){
        //check if is numeric value, value less than 10  and not more 2 decimal point
        $errors[]="invalid amount";
    }
    $courseValid=TRUE;
    if(!CheckCourseExist($course)){
         // check if code exist in course table
        $errors[]="invalid course";
        $courseValid=FALSE;
    }
    if($courseValid && (!CheckSectionExist($course,$section))){
        // check if section exist in section table
        $errors[]="invalid section";
    }
    if(!CheckStudentExist($userid)){
        // check if userid exist in student table
        $errors[]="invalid userid";
    }
    if (isEmpty($errors)){
        if (CheckMinBid($course,$section)[0]>$amount){
            $errors[]="bid too low";
        }
        if (!CheckForEdollar($userid,$amount,$course)){
            $errors[]="insufficient e$";
        }
        if (!CheckClassTimeTable($userid,$course,$section)){
            $errors[]="class timetable clash";
        }
        if (!CheckExamTimeTable($userid,$course)){
            $errors[]="exam timetable clash";
        }
        if (!CheckForCompletedPrerequisites($userid,$course)){
            $errors[]="incomplete prerequisites";
        }
        //round ended
        $adminRoundDAO=new adminRoundDAO();
        $roundDetail=$adminRoundDAO->RetrieveRoundDetail();
        $roundID=$roundDetail->getRoundID();
        $roundStatus=$roundDetail->getRoundStatus();
        if ($roundStatus!="Started"){
            $errors[]="round ended";
        }
        if (CheckForCompletedCourse($userid,$course)){
            $errors[]="course completed";
        }
        if (CheckCourseEnrolled($userid,$course)!==FALSE){
            $errors[]="course enrolled";
        }
        if (!CheckForExceedOfBidSection($userid,$course)){
            $errors[]="section limit reached";
        }
        if ($roundID==1 && !CheckForOwnSchool($userid,$course)){
            $errors[]="not own school course";
        }
        if (!CheckVacancy($course,$section)){
            $errors[]="no vacancy";
        }
    }
    if (!isEmpty($errors)){
        $result = [
            "status" => "error",
            "message" => array_values($errors)
            ];
    }else{
        $status=ChangeBidUpdateEdollar(new Bid($userid,$amount,$course,$section));
        if ($status){
            $result = [
                "status" => "success"
                ];
        }
        
    }
    
}
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);

?>

