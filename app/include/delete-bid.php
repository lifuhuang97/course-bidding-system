<?php
require_once 'common.php';
require_once 'function.php';
function doDeleteBid($userid,$course,$section) {
    $errors=array();
    $course=strtoupper($course);
    $section=strtoupper($section);
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
    $roundDetail=$adminRoundDAO->retrieveRoundDetail();
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
    return $result;
}
?>