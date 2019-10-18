<?php
require_once 'common.php';
function doStop() {
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
    return $result;
}
?>