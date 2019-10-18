<?php
require_once '../include/protect.php';
require_once '../include/dump.php';

// if (!isEmpty($tokenError)){
//     $result = [
//         "status"=>"error",
//         "message"=>$tokenError
//     ];
// }else{
    $result=doDump(); 
// }
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
?>