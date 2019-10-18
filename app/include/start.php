<?php
require_once 'common.php';
function doStart() {
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
    return $result;
}
?>