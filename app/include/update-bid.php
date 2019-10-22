<?php
require_once 'common.php';
require_once 'function.php';

function doUpdateBid($userid,$amount,$course,$section) {
    $errors=array();
    $course=strtoupper($course);
    $section=strtoupper($section);
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
        //round 
        $adminRoundDAO=new adminRoundDAO();
        $roundDetail=$adminRoundDAO->RetrieveRoundDetail();
        $roundID=$roundDetail->getRoundID();
        $roundStatus=$roundDetail->getRoundStatus();
        if ($roundID==2 && CheckMinBid($course,$section)>$amount){
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
    return $result;
}
?>