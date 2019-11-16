<?php
require_once '../include/protect.php';
require_once '../include/dump.php';

if (!isEmpty($tokenError)){
    $result = [
        "status"=>"error",
        "message"=>$tokenError
    ];
}else{
    $result=doDump(); 
    if ($result['status']=="success"){
        if (count($result['course'])>0){
            foreach ($result['course'] as $key=>$course){
                $result['course'][$key]['exam date']=date("Ymd",strtotime($course['exam date']));
                $result['course'][$key]['exam start']=date("Gi",strtotime($course['exam start']));
                $result['course'][$key]['exam end']=date("Gi",strtotime($course['exam end']));
            }
        }
        if (count($result['section'])>0){
            foreach ($result['section'] as $key=>$section){
                $weekday=[1=>'Monday', 2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday',7=>'Sunday'];
                $result['section'][$key]['day']=$weekday[$section['day']];
                $result['section'][$key]['start']=date("Gi",strtotime($section['start']));
                $result['section'][$key]['end']=date("Gi",strtotime($section['end']));
                $result['section'][$key]['size']=(int)$section['size'];
            }
        }
    }
}
header('Content-Type: application/json');
$json= json_encode($result, JSON_PRETTY_PRINT);
if ($result['status']=="success"){
    if (count($result['student'])>0){
        foreach ($result['student'] as $key=>$student){
            if (strpos($result['student'][$key]['edollar'],'.')!== FALSE){
                $json=str_replace('"edollar": "'.$result['student'][$key]['edollar'].'"','"edollar": '.number_format($result['student'][$key]['edollar'],2).'',$json);
            }else{
                $json=str_replace('"edollar": "'.$result['student'][$key]['edollar'].'"','"edollar": '.number_format($result['student'][$key]['edollar'],1).'',$json);
            }
        }
    }
    if (count($result['bid'])>0){
        foreach ($result['bid'] as $key=>$student){
            if (strpos($result['bid'][$key]['amount'],'.')!== FALSE){
                $json=str_replace('"amount": "'.$result['bid'][$key]['amount'].'"','"amount": '.number_format($result['bid'][$key]['amount'],2).'',$json);
            }else{
                $json=str_replace('"amount": "'.$result['bid'][$key]['amount'].'"','"amount": '.number_format($result['bid'][$key]['amount'],1).'',$json);
            }
        }
    }
    if (count($result['section-student'])>0){
        foreach ($result['section-student'] as $key=>$student){
            if (strpos($result['section-student'][$key]['amount'],'.')!== FALSE){
                $json=str_replace('"amount": "'.$result['section-student'][$key]['amount'].'"','"amount": '.number_format($result['section-student'][$key]['amount'],2).'',$json); 
            }else{
                $json=str_replace('"amount": "'.$result['section-student'][$key]['amount'].'"','"amount": '.number_format($result['section-student'][$key]['amount'],1).'',$json); 
            }
        }
    }
}
echo $json;
?>