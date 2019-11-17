<?php

//to be included files

require_once 'include/common.php';
require_once 'include/function.php';
require_once 'include/protect.php';
require_once 'include/update-bid.php';


$student = $_SESSION['student']; 
$userid = $student->getUserid(); #get userid
$password = $student->getPassword(); #get password
$name = $student->getName(); #get name
$school = $student->getSchool(); #get school
$edollar = $student->getEdollar(); #get edollar
$_SESSION['errors1'] = [];

$biddingDAO = New BidDAO();
$modules = $biddingDAO->getBidInfo($_SESSION['success']);
$biddedCourse = [];
foreach ($modules as $mods) {
    if (!array_key_exists($mods->getCode(),$biddedCourse)){
        $biddedCourse[$mods->getCode()]=[$mods->getSection()];
    }else{
        $biddedCourse[$mods->getCode()][]=$mods->getSection();
    }
}


//do validation
//check if user did enter anything to the text field
//if user never add anything return error message and go back to editbid.php
if (!isset($_POST['code']) || strlen(trim($_POST['code'])) == 0) {
    array_push($_SESSION['errors1'], 'Please enter a Course ID.');
}
if (!isset($_POST['section']) || strlen(trim($_POST['section'])) == 0) {
    array_push($_SESSION['errors1'], 'Please enter a Section ID.');
}
if (!isset($_POST['newBidAmt']) || strlen(trim($_POST['newBidAmt'])) == 0) {
    array_push($_SESSION['errors1'], 'Please enter a Bid Amount.');
}
#if there's a error, exit this page and go to makebid.php page and display the error message stored inside $_SESSION['errors1']
if (count($_SESSION['errors1']) > 0) {
    header("Location: editBid.php?token={$_GET['token']}");
    exit;
}else{
    $code = $_POST['code'];
    $section = $_POST['section'];
    $newBidAmt = $_POST['newBidAmt'];

    //making sure all char is upper case
    $code = strtoupper($code);
    $section = strtoupper($section);

    //Checking if the course ID & section ID is valid
    if (!array_key_exists($code, $biddedCourse)) {
        array_push($_SESSION['errors1'], 'Please enter a valid Course ID.');
    }elseif (!in_array($section, $biddedCourse[$code])) {
        array_push($_SESSION['errors1'], 'Please enter a valid Section ID.');
    }
    if (count($_SESSION['errors1']) > 0) {
        header("Location: editBid.php?token={$_GET['token']}");
        exit;
    }
    if(!is_numeric($newBidAmt)){
        //check if the amount the user entered is numeric
        array_push($_SESSION['errors1'], 'Please enter a valid amount.');
    }elseif ($newBidAmt<10){
        // if amount is less than 10
        array_push($_SESSION['errors1'], 'Please enter a value more than 9.99.');
    }else{
        if(strpos($newBidAmt,'.')!=FALSE){
            $temp=explode('.',$newBidAmt);
            if (strlen($temp[1])>2){
                array_push($_SESSION['errors1'], 'Please enter a value and round it up to 2 decimal place.');
            }
        }
        if (count($_SESSION['errors1'])==0){
            if ($newBidAmt>$edollar){
                // if amount is more than that the user have 
                array_push($_SESSION['errors1'], 'Insufficient Edollar.');
            }
        }
    }
    if (count($_SESSION['errors1']) > 0) {
        header("Location: editBid.php?token={$_GET['token']}");
        exit;
    }
    //update bid
    $result=doUpdateBid($userid,$newBidAmt,$code,$section);
    if ($result['status']=='error'){
        $_SESSION['errors1']=array_merge($_SESSION['errors1'],$result['message']);
        header("Location: editBid.php?token={$_GET['token']}");
        exit;
    }else{
        header("Location: mainpage.php?token={$_GET['token']}");
        exit;
    }
}
?>