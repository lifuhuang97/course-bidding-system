<?php
require_once '../include/common.php';
require_once '../include/user-dump.php';
require_once '../include/protect.php';

if (isset($_REQUEST['r'])){
    // json request
    $request=json_decode($_REQUEST['r'], JSON_PRETTY_PRINT);
    $errors=[];
    //check userid
    if (!isset($request['userid'])){
        $errors[]="missing userid";
    }elseif(strlen(trim($request['userid']))==0){
        $errors[]="blank userid";
    }else{
        $userid=$request['userid'];
    }
    //check for token error
    if (isset($tokenError)){
        $errors=array_merge ($tokenError,$errors);
    }
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
    //perform user dump if there is no error
    $result=doUserDump($userid);
}
header('Content-Type: application/json');
$json=json_encode($result, JSON_PRETTY_PRINT);
if ($result['status']=="success"){
    if (strpos($result['edollar'],'.')!== FALSE){
        // display float value
        if (substr($result['edollar'],-1)=='0'){
            $json=str_replace('"edollar": "'.$result['edollar'].'"','"edollar": '.number_format($result['edollar'],1).'',$json);
        }else{
            $json=str_replace('"edollar": "'.$result['edollar'].'"','"edollar": '.number_format($result['edollar'],2).'',$json);
        }
    }else{
        // display int as float value
        $json=str_replace('"edollar": "'.$result['edollar'].'"','"edollar": '.number_format($result['edollar'],1).'',$json);
    }
}
echo $json;
?>