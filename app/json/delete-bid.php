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
            isMissingOrEmpty ('course'),
            isMissingOrEmpty ('section') ];
    $errors = array_filter($errors);
    if (isEmpty($errors)) {
        $userid = $_REQUEST['userid'];
        $course = $_REQUEST['course'];
        $section = $_REQUEST['section'];
    }
}
if (!isEmpty($errors)) {
    $result = [
        "status" => "error",
        "message" => array_values($errors)
        ];
}
else{
    //validate
    $courseValid=TRUE;
    if(!CheckCourseExist($course)){
        // check if code exist in course table
        $errors[]="invalid course";
        $courseValid=FALSE;
    }
    if(!CheckStudentExist($userid)){
        // check if userid exist in student table
        $errors[]="invalid userid";
    }
    if($courseValid && !CheckSectionExist($course,$section)){
        // check if section exist in section table
        $errors[]="invalid section";
    }
    //round ended
    $adminRoundDAO=new adminRoundDAO();
    $roundDetail=$adminRoundDAO->RetrieveRoundDetail();
    $roundID=$roundDetail->getRoundID();
    $roundStatus=$roundDetail->getRoundStatus();
    if ($roundStatus!="Started"){
        $errors[]="round ended";
    }
    //no such bid
    if (isEmpty($errors)){
        $status=CheckBidExist($userid,$course);
        if ($status===FALSE){
            $errors[]="no such bid";
        }else{
            $status=ChangeBidUpdateEdollar(new Bid($userid,$status,$course,$section),'Drop');
        }
    }
    if (!isEmpty($errors)){
        $result = [
            "status" => "error",
            "message" => array_values($errors)
            ];
    }else{
        $result = [
            "status" => "success"
            ];
    }
}
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
?>