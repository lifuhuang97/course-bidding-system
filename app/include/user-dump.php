<?php
require_once 'common.php';
require_once 'function.php';

function doUserDump($userid) {
    $errors=array();
    if(!CheckStudentExist($userid)){
        // check if userid exist in student table
        $errors[]="invalid userid";
    }
    if (!isEmpty($errors)){
        $result = [
            "status" => "error",
            "message" => array_values($errors)
            ];
    }else{
        $studentDAO=new StudentDAO();
        $student=$studentDAO->retrieveStudent($userid);
        $result = [
            "status" => "success",
            "userid" => $student->getUserid(),
            "password" => $student->getPassword(),
            "name" => $student->getName(),
            "school" => $student->getSchool(),
            "edollar" => $student->getEdollar(),
            ];
    }
    return $result;
}
?>