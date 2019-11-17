<?php
require_once '../include/protect.php';
require_once '../include/DoStop.php';

if (!isEmpty($tokenError)){
    //check for token error
    $result = [
        "status"=>"error",
        "message"=>$tokenError
    ];
}else{
    //perform stop if there is no error
    $result=doStop(); 
}
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
?>