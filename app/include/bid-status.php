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
        $roundDetail=$adminRoundDAO->RetrieveRoundDetail();
        $roundID=$roundDetail->getRoundID();
        $roundStatus=$roundDetail->getRoundStatus();
        $vacancy=CheckVacancy($course,$section,TRUE);
        $minbid=CheckMinBid($course,$section);
        $checkminbid=CheckMinBid($course,$section,FALSE);
        if ($roundID==2 && $roundStatus=='Finished'){
            $minbid=$checkminbid;
            $StudentSectionDAO = new StudentSectionDAO();
            $allBid=$StudentSectionDAO->RetrieveAllStudentByCourseSection($course,$section);
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
                if ($roundID==1 && ($roundStatus=="Started" || $roundStatus=="Not Started")){
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
            "min-bid-amount"=> $minbid,
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