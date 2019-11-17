<?php
require_once '../include/common.php';
require_once '../include/update-bid.php';
require_once '../include/protect.php';

if (isset($_REQUEST['r'])){
    // json request
    $request=json_decode($_REQUEST['r'], JSON_PRETTY_PRINT);
    $errors=[];
    // check userid
    if (!isset($request['userid'])){
        $errors[]="missing userid";
    }elseif(strlen(trim($request['userid']))==0){
        $errors[]="blank userid";
    }else{
        $userid=$request['userid'];
    }
    // check amount
    if (!isset($request['amount'])){
        $errors[]="missing amount";
    }elseif(strlen(trim($request['amount']))==0){
        $errors[]="blank amount";
    }else{
        $amount=$request['amount'];
    }
    // check course
    if (!isset($request['course'])){
        $errors[]="missing course";
    }elseif(strlen(trim($request['course']))==0){
        $errors[]="blank course";
    }else{
        $course=$request['course'];
    }
    // check section
    if (!isset($request['section'])){
        $errors[]="missing section";
    }elseif(strlen(trim($request['section']))==0){
        $errors[]="blank section";
    }else{
        $section=$request['section'];
    }
    //check for token error
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
    //perform update bid if there is no error
    $result=doUpdateBid($userid,$amount,$course,$section);
}
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
?>

