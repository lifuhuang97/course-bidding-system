<?php
require_once '../include/common.php';
require_once '../include/section-dump.php';
require_once '../include/protect.php';

if (isset($_REQUEST['r'])){
    $request=json_decode($_REQUEST['r'], JSON_PRETTY_PRINT);
    $errors=[];
    if (!isset($request['course'])){
        $errors[]="missing course";
    }elseif(strlen(trim($request['course']))==0){
        $errors[]="blank course";
    }else{
        $course=$request['course'];
    }
    if (!isset($request['section'])){
        $errors[]="missing section";
    }elseif(strlen(trim($request['section']))==0){
        $errors[]="blank section";
    }else{
        $section=$request['section'];
    }
    if (isset($tokenError)){
        $errors=array_merge ($tokenError,$errors);
    }
}else{
    $errors = array_merge ($tokenError,[isMissingOrEmpty ('course'),
                                        isMissingOrEmpty ('section') ]);
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
    $result=doSectionDump($course,$section);
}
header('Content-Type: application/json');
$json=json_encode($result, JSON_PRETTY_PRINT);

if ($result['status']=='success' && count($result['students'])>0){
    foreach ($result['students'] as $key=>$student){
        if (strpos($result['students'][$key]['amount'],'.')!== FALSE){
            $json=str_replace('"amount": "'.$result['students'][$key]['amount'].'"','"amount": '.number_format($result['students'][$key]['amount'],2).'',$json);
        }else{
            $json=str_replace('"amount": "'.$result['students'][$key]['amount'].'"','"amount": '.number_format($result['students'][$key]['amount'],1).'',$json);
        }
    }
}
echo $json;
?>