<?php
require_once 'common.php';
require_once 'function.php';
function doSectionDump($course,$section) {
    $errors=array();
    $course=strtoupper($course);
    $section=strtoupper($section);
    $courseValid=TRUE;
    if(!CheckCourseExist($course)){
        // check if code exist in course table
        $errors[]="invalid course";
        $courseValid=FALSE;
    }
    if($courseValid && !CheckSectionExist($course,$section)){
        // check if section exist in section table
        $errors[]="invalid section";
    }
    if (!isEmpty($errors)){
        $result = [
            "status" => "error",
            "message" => array_values($errors)
            ];
    }else{
        $StudentSectionDAO=new StudentSectionDAO();
        $students=$StudentSectionDAO->retrieveAllStudentByCourseSection($course,$section);
        $StudentList=[];
        foreach ($students as $student){
            $StudentList[]=["userid"=>$student->getUserid(),
                        "amount"=>$student->getAmount()];
        }
        $result = [
            "status" => "success",
            "students" => $StudentList
            ];

    }
    return $result;
}
?>