<?php
    require_once 'include/common.php';
    require_once 'include/drop-section.php';

    $student=$_SESSION['student'];
    $userid = $student->getUserid(); #get userid
    $password = $student->getPassword(); #get password
    $name = $student->getName(); #get name
    $school = $student->getSchool(); #get school
    $edollar = $student->getEdollar(); #get edollar
    $studentSectionDAO = New StudentSectionDAO();
    $sections = $studentSectionDAO->getSuccessfulBidsByID($userid);

    //getting the round ID and roundstat
    $adminround = new adminRoundDAO();
    $roundDetail = $adminround->RetrieveRoundDetail();
    $roundID = $roundDetail->getRoundID();
    $roundstat = $roundDetail->getRoundStatus();

    $_SESSION['errors1']=[];

    if (!isset($_GET['code']) || strlen(trim($_GET['code'])) == 0){
        array_push($_SESSION['errors1'], 'Please enter a Course ID');
    }else{
        $deletemod = $_GET['code'];
    }
    if (!isset($_GET['section']) || strlen(trim($_GET['section'])) == 0) {
        array_push($_SESSION['errors1'], 'Please enter a Course ID');
    }else{
        $deletesection = $_GET['section'];
    }
    if (count($_SESSION['errors1']) > 0) {
        header("Location: dropSection.php?token={$_GET['token']}");
        exit;
    }

    $deletemod = strtoupper($deletemod);
    $deletesection = strtoupper($deletesection);
    $result=doDropSection($userid,$deletemod,$deletesection);
    if ($result['status']=="error"){
        $_SESSION['errors1']=array_merge ($_SESSION['errors1'], $result['message']);
        header("Location: dropSection.php?token={$_GET['token']}");
        exit;
    }else{
        header("Location: mainpage.php?token={$_GET['token']}");
        exit;
    }

?>