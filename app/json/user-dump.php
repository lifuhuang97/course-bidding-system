<?php
require_once '../include/common.php';
require_once '../include/user-dump.php';
require_once '../include/protect.php';

if (isset($_REQUEST['r'])){
    $request=json_decode($_REQUEST['r'], JSON_PRETTY_PRINT);
    $errors=[];
    if (!isset($request['userid'])){
        $errors[]="missing userid";
    }elseif(strlen(trim($request['userid']))==0){
        $errors[]="blank userid";
    }else{
        $userid=$request['userid'];
    }
    // if (isset($tokenError)){
    //     $errors=array_merge ($tokenError,$errors);
    // }
}else{
    $errors = array_merge ($tokenError,[isMissingOrEmpty ('userid')]);
    $errors = array_filter($errors);
    if (isEmpty($errors)) {
        $userid = $_REQUEST['userid'];
    }
}
if (!isEmpty($errors)) {
    $sortclass = new Sort();
    $errors = $sortclass->sort_it($errors,"field");
    $result = [
        "status" => "error",
        "message" => array_values($errors)
        ];
}
else{
    $result=doUserDump($userid);
}
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
?>