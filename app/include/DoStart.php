<?php
require_once 'common.php';
require_once 'function.php';
function doStart() {
    $adminRoundDAO = new adminRoundDAO();
    $roundDetail = $adminRoundDAO->retrieveRoundDetail();
    $roundID=$roundDetail->getRoundID();
    $roundStatus=$roundDetail->getRoundStatus();
    $bidprocessorDAO = new BidProcessorDAO();
    $errors=[];
    if ($roundStatus=="Not Started"){
        $adminRoundDAO->startRound();
        if ($roundID==1){
            //remove all data from bid processor
            $bidprocessorDAO->removeAll();
        }else{
            //round 2
            $sectDAO = new SectionDAO();
            $bidDAO= new BidDAO();
            $sections = $sectDAO->getAllSections();

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
            "status" => "success",
            "round" => (int)$roundID
            ];
    }
    return $result;
}
?>