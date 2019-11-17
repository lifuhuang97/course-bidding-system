<?php
require_once '../include/protect.php';
require_once '../include/DoStart.php';

if (!isEmpty($tokenError)){
    //check for token error
    $result = [
        "status"=>"error",
        "message"=>$tokenError
    ];
}else{
    //perform start if there is no error
    $result=doStart(); 
}
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
?>