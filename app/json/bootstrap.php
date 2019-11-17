<?php
require_once '../include/bootstrap.php';
require_once '../include/protect.php';
if (!isEmpty($tokenError)){
    // check for token
    $result = [
        "status"=>"error",
        "message"=>$tokenError
    ];
}else{
    //perform bootstrap if there is no error
    $result=doBootstrap();
}
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
?>
