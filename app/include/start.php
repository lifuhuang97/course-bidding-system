<?php
require_once 'common.php';
require_once 'include/function.php';
function doStart() {
    $adminRoundDAO = new adminRoundDAO();
    $roundDetail = $adminRoundDAO->RetrieveRoundDetail();
    $roundID=$roundDetail->getRoundID();
    $roundStatus=$roundDetail->getRoundStatus();
    $bidprocessorDAO = new BidProcessorDAO();
    $errors=[];
    if ($roundStatus=="Not Started"){
        $adminRoundDAO->startRound();
        if ($roundID==1){
            $bidprocessorDAO->removeAll();
        }else{
            //round 2
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
                        $StudentSectionDAO->addBidResults($bidID,$bidAmt,$bidCourse,$bidSection,$status,$roundID);
                    }
                }
            }
            //add minbid to table
            foreach($sections as $section){
                $course = $section[0];
                $section = $section[1];
                $vacancy=CheckVacancy($course,$section,TRUE);
                if ($vacancy!=0){
                    //update section table
                    $sectDAO->updateSectionMinBid('10',$course,$section);
                }
            }
            //clear bid
            $bidDAO->removeAll();
        }
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