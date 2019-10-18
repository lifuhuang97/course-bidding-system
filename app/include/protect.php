<?php
require_once 'token.php';
require_once 'common.php';

$token = '';
$tokenError='';
$pathSegments = explode('/',$_SERVER['PHP_SELF']); # Current url
$numSegment = count($pathSegments);
$currentFolder = $pathSegments[$numSegment - 2]; # Current folder
$page = $pathSegments[$numSegment -1]; # Current page

if ($currentFolder == "json")
{
    $tokenError = [ isMissingOrEmpty ('token')];
    $tokenError = array_filter($tokenError);
    if (isEmpty($tokenError)) {
        $token = $_REQUEST['token'];
        if (!verify_token($token)){
            $tokenError=["invalid token"];
        }
    }
}else{
    if (isset($_REQUEST['token'])) {
        $token = $_REQUEST['token'];
        $result=verify_token($token);
        if ($result=="Expired"){ //session expired
            $_SESSION['errors'] =  ["Session Expired. Please Login Again!"];
            header('Location:login.php');
        }elseif($result!=$_SESSION['success']){// wrong username that login
            $_SESSION['errors'] =  ["Token is invalid. Please Login!"];
            header('Location:login.php');
        }
    }else{
        $_SESSION['errors'] =  ["Missing token. Please Login!"];
        header('Location:login.php');
    }
}
?>