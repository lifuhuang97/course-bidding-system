<?php
require_once '../include/protect.php';
require_once '../include/DoRestart.php';

if (!isEmpty($tokenError)){
    //check for token error
    $result = [
        "status"=>"error",
        "message"=>$tokenError
    ];
}else{
    //perform restart if there is no error
    $result=doRestart(); 
}
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
?>