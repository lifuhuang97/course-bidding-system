<?php
require_once 'common.php';
require_once 'function.php';
function doDropSection($userid,$course,$section) {
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
    $adminRoundDAO=new adminRoundDAO();
    $roundDetail=$adminRoundDAO->RetrieveRoundDetail();
    $roundID=$roundDetail->getRoundID();
    $roundStatus=$roundDetail->getRoundStatus();
    if ($roundStatus=="Not Started"){
        $errors[]="round not active";
    }
    //no such bid
    if (isEmpty($errors)){
        $status=CheckCourseEnrolled($userid,$course);
        if ($status===FALSE){
            $errors[]="no such enrollment record";
        }else{
            $status=DropSectionUpdateEdollar($userid,$course,$status);
            if ($roundID==2){
                $vacancy=CheckVacancy($course,$section,TRUE);
                if ($vacancy==1){
                    $sectionDAO=new SectionDAO();
                    $sectionDAO->updateSectionMinBid('10',$course,$section);
                }
            }
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