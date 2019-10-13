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
}else{
    $errors = [ isMissingOrEmpty ('userid') ];
    $errors = array_filter($errors);
    if (isEmpty($errors)) {
        $userid = $_REQUEST['userid'];
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
}
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
?>