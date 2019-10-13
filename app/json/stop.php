<?php
require_once '../include/common.php';
$adminRoundDAO = new adminRoundDAO();
$roundDetail = $adminRoundDAO->RetrieveRoundDetail();
$roundID=$roundDetail->getRoundID();
$roundStatus=$roundDetail->getRoundStatus();
$errors=[];
if ($roundStatus=="Started"){
    $adminRoundDAO->clearRound();
    $adminRoundDAO->clearRoundBids();
    $bidDAO= new bidDAO();
    $bidDAO->removeAll();
}elseif($roundID==1 && $roundStatus=="Not Started"){
    $errors=["round not started"];
}else{
    $errors=["round already ended"];
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