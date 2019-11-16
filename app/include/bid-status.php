<?php
require_once 'common.php';
require_once 'function.php';
function doBidStatus($course,$section) {
    $errors=array();
    $course=strtoupper($course);
    $section=strtoupper($section);
    $courseValid=TRUE;
    if(!CheckCourseExist($course)){
        // check if code exist in course table
        $errors[]="invalid course";
        $courseValid=FALSE;
    }
    if($courseValid && !CheckSectionExist($course,$section)){
        // check if section exist in section table
        $errors[]="invalid section";
    }
    if (isEmpty($errors)){
        $adminRoundDAO=new adminRoundDAO();
        $roundDetail=$adminRoundDAO->retrieveRoundDetail();
        $roundID=$roundDetail->getRoundID();
        $roundStatus=$roundDetail->getRoundStatus();
        $vacancy=CheckVacancy($course,$section,TRUE);
        
        if ($roundID==1){
            //round 1 started
            $checkminbid=CheckMinBid($course,$section,FALSE);
            $minbid=$checkminbid;
        }elseif ($roundID==2 && $roundStatus=='Not Started'){
            //round 1 ended
            $minbid=CheckMinBidFromBiddingResult($course,$section,1);
            $checkminbid=$minbid;
            if ($minbid==''){
                $minbid=10;
            }
        }elseif($roundID==2 && $roundStatus=='Finished'){
            //round 2 ended
            $minbid=CheckMinBidFromBiddingResult($course,$section,2);
            $checkminbid=$minbid;
        }else{
            // round 2 started
            $sectionDAO= new SectionDAO();
            $minbid = $sectionDAO->viewMinBid($course,$section);
            $checkminbid=CheckMinBid($course,$section,FALSE);
        }
        if ($minbid==''){
            $minbid='-';
        }
        
        if ($roundID==2 && $roundStatus=='Finished'){
            $StudentSectionDAO = new StudentSectionDAO();
            $allBid=$StudentSectionDAO->retrieveAllStudentByCourseSection($course,$section);
            $students=[];
            $studentDAO=new StudentDAO();
            foreach ($allBid as $oneBid){
                $userid=$oneBid->getUserid();
                $amount=$oneBid->getAmount();
                $student=$studentDAO->retrieveStudent($userid);
                $balance=$student->getEdollar();
                $students[]=["userid"=> $userid,
                    "amount"=> $amount,
                    "balance"=>  $balance, 
                    "status"=> 'success'];
            }
            $studentID=array_column($students,"userid");
            $studentAmount=array_column($students,"amount");
            array_multisort($studentAmount,SORT_DESC,$studentID,SORT_ASC,$students);
        }else{
            $bidDAO= new BidDAO();
            $allBid=$bidDAO->getAllBids([$course,$section]);
            $students=[];
            $studentDAO=new StudentDAO();
            foreach ($allBid as $oneBid){
                $userid=$oneBid->getUserid();
                $amount=$oneBid->getAmount();
                $student=$studentDAO->retrieveStudent($userid);
                $balance=$student->getEdollar();
                if ($roundID==1){
                    $status='pending';
                }elseif($amount>=$checkminbid){
                    $status='success';
                }else{
                    $status='fail';
                }
                $students[]=["userid"=> $userid,
                            "amount"=> $amount,
                            "balance"=>  $balance, 
                            "status"=> $status];
            }
        }
        
        $result = [
            "status" => "success",
            "vacancy"=> $vacancy,
            "min-bid-amount"=> (string)$minbid,
            "students"=>$students
            ];
    }else{
        $result = [
            "status" => "error",
            "message" => array_values($errors)
            ];
    }
   
    return $result;
}
?>