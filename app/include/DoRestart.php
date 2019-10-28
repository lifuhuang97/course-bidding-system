<?php
require_once 'common.php';
require_once 'function.php';
function doRestart() {
    $bidDAO = new BidDAO();
    $adminRoundDAO = new adminRoundDAO();
    $StudentSectionDAO = new StudentSectionDAO();
    $bidprocessorDAO = new BidProcessorDAO();
    $sectionDAO= new SectionDAO();
    $adminRoundDAO->resetRound();
    $StudentSectionDAO->removeAll();
    $bidprocessorDAO->removeAll();
    $bidDAO->removeAll();
    $sectionDAO->resetSectionMinBid();
    $result = [
        "status" => "success"
        ];
    return $result;
}
?>