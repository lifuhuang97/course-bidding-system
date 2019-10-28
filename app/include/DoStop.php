<?php
require_once 'common.php';
require_once 'function.php';
function doStop() {
    $adminRoundDAO = new adminRoundDAO();
    $roundDetail = $adminRoundDAO->retrieveRoundDetail();
    $roundID=$roundDetail->getRoundID();
    $roundStatus=$roundDetail->getRoundStatus();
    $errors=[];
    if ($roundStatus=="Started"){
        $adminRoundDAO->clearRound();
        $bidprocessorDAO = new BidProcessorDAO();
        $StudentSectionDAO = new StudentSectionDAO();
        $sectDAO = new SectionDAO();
        $bidDAO= new BidDAO();
        $sections = $sectDAO->getAllSections();

        foreach($sections as $section){
            $courseID = $section[0];
            $sectionID = $section[1];
            $bids = $bidDAO->getAllBids($section);

            $sectMinBid = CheckMinBid($courseID, $sectionID,FALSE);

            foreach($bids as $bid){
                $bidID = $bid->getUserid();
                $bidAmount = $bid->getAmount();
                $bidAmt = $bidAmount;
                $bidCourse = $bid->getCode();
                $bidSection = $bid->getSection();
                if ($bidAmt>=$sectMinBid){
                    $status="Success";
                    //add student to student section
                    if ($roundID==2){
                        $StudentSectionDAO->addBidResults($bidID,$bidAmt,$bidCourse,$bidSection,$status,$roundID);
                    }
                }else{
                    $status="Fail";
                }
                //add to bidprocessor table
                $bidprocessorDAO->addBidResults($bidID,$bidAmt,$bidCourse,$bidSection,$status,$roundID);
            }
        }
        //clear bid
        if ($roundID==2){
            // $bidDAO->removeAll();
            pass;
        }
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