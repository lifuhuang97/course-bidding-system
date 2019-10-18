<?php
require_once '../include/bootstrap.php';
require_once '../include/protect.php';

if (!isEmpty($tokenError)){
    $result = [
        "status"=>"error",
        "message"=>$tokenError
    ];
}else{
    $result=doBootstrap();
}
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
?>
