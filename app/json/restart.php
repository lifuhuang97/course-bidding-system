<?php
require_once '../include/protect.php';
require_once '../include/DoRestart.php';

if (!isEmpty($tokenError)){
    $result = [
        "status"=>"error",
        "message"=>$tokenError
    ];
}else{
    $result=doRestart(); 
}
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
?>