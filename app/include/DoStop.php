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
        $studentDAO=new StudentDAO();
        $sections = $sectDAO->getAllSections();

        foreach($sections as $section){
            $courseID = $section[0];
            $sectionID = $section[1];
            $bids = $bidDAO->getAllBids($section);

            $sectMinBid = CheckMinBid($courseID, $sectionID,FALSE);
            #round 1 clearing
            $NoSameBid=1;
            if ($roundID==1){
                #number of same price
                $NoSameBid=noOfSameMinBid($courseID,$sectionID,$sectMinBid);
                $vacancy=CheckVacancy($courseID,$sectionID,TRUE);
                if ($vacancy-count($bids)>0){
                    $NoSameBid=1;
                }
            }
            

            foreach($bids as $bid){
                $bidID = $bid->getUserid();
                $bidAmount = $bid->getAmount();
                $bidAmt = $bidAmount;
                $bidCourse = $bid->getCode();
                $bidSection = $bid->getSection();
                if ($bidAmt>$sectMinBid || ($bidAmt==$sectMinBid && $NoSameBid==1)){
                    $status="Success";
                    //add student to student section
                    $StudentSectionDAO->addBidResults($bidID,$bidAmt,$bidCourse,$bidSection,$status,$roundID);
                }else{
                    $status="Fail";
                    //refund edollar
                    $student=$studentDAO->retrieveStudent($bidID);
                    $eDollar=$student->getEdollar();
                    $TotalAmt=$eDollar+$bidAmt;
                    $studentDAO->updateDollar($bidID,$TotalAmt);
                }
                //add to bidprocessor table
                $bidprocessorDAO->addBidResults($bidID,$bidAmt,$bidCourse,$bidSection,$status,$roundID);
            }
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