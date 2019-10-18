<?php
require_once 'common.php';
require_once 'function.php';
function doBidDump($course,$section) {
    $errors=array();
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
    if (!isEmpty($errors)){
        $result = [
            "status" => "error",
            "message" => array_values($errors)
            ];
    }else{
        $bidDAO=new BidDAO();
        $AllBids=$bidDAO->getAllBids([$course,$section]);
        $minBid=CheckMinBid($course,$section)[0];
        $bidList=[];
        $index=1;
        foreach ($AllBids as $onebid){
            if ($onebid->getAmount()>=$minBid){
                $result='in';
            }else{
                $result="out";
            }
            $bidList[]=["row"=>$index,
                        "userid"=>$onebid->getUserid(),
                        "amount"=>$onebid->getAmount(),
                        "result"=>$result];
            $index++;
        }
        $result = [
            "status" => "success",
            "bids" => $bidList
            ];
    }
    return $result;
}
?>