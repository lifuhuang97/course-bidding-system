<?php
require_once '../include/common.php';
require_once '../include/update-bid.php';
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
    if (!isset($request['amount'])){
        $errors[]="missing amount";
    }elseif(strlen(trim($request['amount']))==0){
        $errors[]="blank amount";
    }else{
        $amount=$request['amount'];
    }
    if (!isset($request['course'])){
        $errors[]="missing course";
    }elseif(strlen(trim($request['course']))==0){
        $errors[]="blank course";
    }else{
        $course=$request['course'];
    }
    if (!isset($request['section'])){
        $errors[]="missing section";
    }elseif(strlen(trim($request['section']))==0){
        $errors[]="blank section";
    }else{
        $section=$request['section'];
    }
    if (isset($tokenError)){
        $errors=array_merge ($tokenError,$errors);
    }
}else{
    $errors = array_merge ($tokenError,[
            isMissingOrEmpty ('userid'), 
            isMissingOrEmpty ('amount'),
            isMissingOrEmpty ('course'),
            isMissingOrEmpty ('section') ]);
    $errors = array_filter($errors);
    if (isEmpty($errors)) {
        $userid = $_REQUEST['userid'];
        $amount = $_REQUEST['amount'];
        $course = $_REQUEST['course'];
        $section = $_REQUEST['section'];
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
    $result=doUpdateBid($userid,$amount,$course,$section);
}
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
?>

