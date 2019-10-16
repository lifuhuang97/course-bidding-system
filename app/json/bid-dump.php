<?php
require_once '../include/common.php';
require_once '../include/function.php';
if (isset($_REQUEST['r'])){
    $request=json_decode($_REQUEST['r'], JSON_PRETTY_PRINT);
    $errors=[];
    if (!isset($request['course']) || strlen(trim($request['course']))==0){
        $errors[]="missing course";
    }else{
        $course=$request['course'];
    }
    if (!isset($request['section']) || strlen(trim($request['section']))==0){
        $errors[]="missing section";
    }else{
        $section=$request['section'];
    }
}else{
    $errors = [ isMissingOrEmpty ('course'),
            isMissingOrEmpty ('section') ];
    $errors = array_filter($errors);
    if (isEmpty($errors)) {
        $course = $_REQUEST['course'];
        $section = $_REQUEST['section'];
    }
}
if (!isEmpty($errors)) {
    $result = [
        "status" => "error",
        "message" => array_values($errors)
        ];
}
else{
    //validate
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
}
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
?>