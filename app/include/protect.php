<?php
require_once 'token.php';
require_once 'common.php';

$errors=[];
$token = '';
if  (isset($_REQUEST['token'])) {
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


?>