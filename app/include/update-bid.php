<?php
require_once 'common.php';
require_once 'function.php';

function doUpdateBid($userid,$amount,$course,$section) {
    $errors=array();
    $course=strtoupper($course);
    $section=strtoupper($section);
    $adminRoundDAO=new adminRoundDAO();
    $roundDetail=$adminRoundDAO->RetrieveRoundDetail();
    $roundID=$roundDetail->getRoundID();
    $roundStatus=$roundDetail->getRoundStatus();
    if ($roundStatus!="Started"){
        $errors[]="round ended";
    }else{
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
    }
    if (isEmpty($errors)){
        $sectionDAO=new SectionDAO();
        
        if ($roundID==2){
            $currentMinBid=$sectionDAO->viewMinBid($course,$section);
            if ($currentMinBid>$amount){
                $errors[]="bid too low";
            }
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
        //update sectionTable
        $minbid = CheckMinBid($course,$section);
        if ($roundID==2 && $minbid>$currentMinBid){
            $SectionDAO=new SectionDAO();
            $SectionDAO->updateSectionMinBid($minbid,$course,$section);
        }
        if ($status){
            $result = [
                "status" => "success"
                ];
        }    
    }
    return $result;
}
?>