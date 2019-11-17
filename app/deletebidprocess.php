<?php

//to be included files

require_once 'include/common.php';
require_once 'include/function.php';
require_once 'include/protect.php';

//retrieve student information
$student=$_SESSION['student'];
$userid = $student->getUserid(); #get userid
$password = $student->getPassword(); #get password
$name = $student->getName(); #get name
$school = $student->getSchool(); #get school
$edollar = $student->getEdollar(); #get edollar

//retrieve student bidded mods
$bidDAO = New BidDAO();
$biddedModule = $bidDAO->getBidInfo($userid);
$_SESSION['errors1'] = [];
$deletemod = $_GET['code'];
$deletesection = $_GET['section'];
$modulecounter = 0;
$sectioncounter = 0;

//retrieve round ID and roundstatus
$adminround = new adminRoundDAO();
$roundDetail = $adminround->retrieveRoundDetail();
$roundID = $roundDetail->getRoundID();
$roundstat = $roundDetail->getRoundStatus();

//check for blanks
if (isset($_GET['code']) && isset($_GET['section'])) {
    if (strlen(trim($_GET['code'])) == 0) {
        array_push($_SESSION['errors1'], 'Please enter a Course ID.');
    }
    if (strlen(trim($_GET['section'])) == 0) {
        array_push($_SESSION['errors1'], 'Please enter a Section ID.');
    }
}
//if the count of the error1 is more than 0 , this if statement will be triggered
if (count($_SESSION['errors1']) > 0) {
    header("Location: deletebid.php?token={$_GET['token']}");
    exit;
}

//making sure all char is upper case
$deletemod = strtoupper($deletemod);
$deletesection = strtoupper($deletesection);

//allow user to delete bid only when round started
if ($roundstat == 'Started'){
    foreach ($biddedModule as $module){
        if ($module->getCode() == $deletemod){
            $modulecounter += 1;
            if ($module->getSection() == $deletesection){
                $sectioncounter += 1;
            }
        }
    }

    if ($modulecounter == 0){
        array_push($_SESSION['errors1'], 'Please enter a valid Course ID.');
    }

    if ($modulecounter == 1 and $sectioncounter == 0){
        array_push($_SESSION['errors1'], 'Please enter a valid Section ID.');
    }
    
    if (count($_SESSION['errors1']) > 0) {
        header("Location: deletebid.php?token={$_GET['token']}");
        exit;
    }

    //valid courseid and section id
    foreach ($biddedModule as $module){
        if ($module->getCode() == $deletemod){
            $result = ChangeBidUpdateEdollar($module, "Drop");
            if ($result){
                header("Location: mainpage.php?token={$_GET['token']}");
                exit;
            }
        }
    }
}else{
    //error message because the round is not started
    array_push($_SESSION['errors1'], "You can't drop your bid when the round is not active!");
    header("Location: deletebid.php?token={$_GET['token']}");
    exit;
}
?>