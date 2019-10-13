<?php
require_once '../include/common.php';
$adminRoundDAO = new adminRoundDAO();
$roundDetail = $adminRoundDAO->RetrieveRoundDetail();
$roundID=$roundDetail->getRoundID();
$roundStatus=$roundDetail->getRoundStatus();
$errors=[];
if ($roundStatus=="Not Started"){
    $adminRoundDAO->startRound();
}elseif($roundID==2 && $roundStatus=="Finished"){
    $errors=["round 2 ended"];
}else{
    $errors=["round already started"];
}
if (!isEmpty($errors)){
    $result = [
        "status" => "error",
        "message" => array_values($errors)
        ];
}else{
    $result = [
        "status" => "success"
        ];
}
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
?>