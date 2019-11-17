<?php
require_once '../include/common.php';
require_once '../include/bid-status.php';
require_once '../include/protect.php';

if (isset($_REQUEST['r'])){
    // json request
    $request=json_decode($_REQUEST['r'], JSON_PRETTY_PRINT);
    $errors=[];
    //check for course
    if (!isset($request['course'])){
        $errors[]="missing course";
    }elseif(strlen(trim($request['course']))==0){
        $errors[]="blank course";
    }else{
        $course=$request['course'];
    }
    //check for section
    if (!isset($request['section'])){
        $errors[]="missing section";
    }elseif(strlen(trim($request['section']))==0){
        $errors[]="blank section";
    }else{
        $section=$request['section'];
    }
    // check for token
    if (isset($tokenError)){
        $errors=array_merge ($tokenError,$errors);
    }
}else{
    $errors = array_merge ($tokenError,[ 
            isMissingOrEmpty ('course'),
            isMissingOrEmpty ('section')]);
    $errors = array_filter($errors);
    if (isEmpty($errors)) {
        $course = $_REQUEST['course'];
        $section = $_REQUEST['section'];
    }
}
if (!isEmpty($errors)) {
    $sortclass = new Sort();
    $errors = $sortclass->sort_it($errors,"field");
    $result = [
        "status" => "error",
        "message" => array_values($errors)
        ];
}
else{
    //perform bid status if there is no error
    $result=doBidStatus($course,$section);
    if ($result['status']=='success'){
        $result['vacancy']=(int)$result['vacancy'];
    }
}
header('Content-Type: application/json');
$json= json_encode($result, JSON_PRETTY_PRINT);
if ($result['status']=='success'){
    if ($result['min-bid-amount']!='-'){
        if (strpos($result['min-bid-amount'],'.')!== FALSE){
            // display float value
            if (substr($result['min-bid-amount'],-1)=='0'){
                $json=str_replace('"min-bid-amount": "'.$result['min-bid-amount'].'"','"min-bid-amount": '.number_format($result['min-bid-amount'],1).'',$json);
            }else{
                $json=str_replace('"min-bid-amount": "'.$result['min-bid-amount'].'"','"min-bid-amount": '.number_format($result['min-bid-amount'],2).'',$json);
            }
        }else{
            // display int as float value
            $json=str_replace('"min-bid-amount": "'.$result['min-bid-amount'].'"','"min-bid-amount": '.number_format($result['min-bid-amount'],1).'',$json);
        }
    }
    if (count($result['students'])>0){
        foreach ($result['students'] as $key=>$student){
            if (strpos($result['students'][$key]['amount'],'.')!== FALSE){
                // display float value
                if (substr($result['students'][$key]['amount'],-1)=='0'){
                    $json=str_replace('"amount": "'.$result['students'][$key]['amount'].'"','"amount": '.number_format($result['students'][$key]['amount'],1).'',$json);
                }else{
                    $json=str_replace('"amount": "'.$result['students'][$key]['amount'].'"','"amount": '.number_format($result['students'][$key]['amount'],2).'',$json);
                }
            }else{
                // display int as float value
                $json=str_replace('"amount": "'.$result['students'][$key]['amount'].'"','"amount": '.number_format($result['students'][$key]['amount'],1).'',$json);
            }
            if (strpos($result['students'][$key]['balance'],'.')!== FALSE){
                // display float value
                if (substr($result['students'][$key]['balance'],-1)=='0'){
                    $json=str_replace('"balance": "'.$result['students'][$key]['balance'].'"','"balance": '.number_format($result['students'][$key]['balance'],1).'',$json);
                }else{
                    $json=str_replace('"balance": "'.$result['students'][$key]['balance'].'"','"balance": '.number_format($result['students'][$key]['balance'],2).'',$json);
                }
            }else{
                // display int as float value
                $json=str_replace('"balance": "'.$result['students'][$key]['balance'].'"','"balance": '.number_format($result['students'][$key]['balance'],1).'',$json);
            }
                
        }
    }
}
echo $json;
?>