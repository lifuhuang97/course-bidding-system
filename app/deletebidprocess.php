<?php
    require_once 'include/common.php';
    require_once 'include/function.php';

    $student=$_SESSION['student'];
    $userid = $student->getUserid(); #get userid
    $password = $student->getPassword(); #get password
    $name = $student->getName(); #get name
    $school = $student->getSchool(); #get school
    $edollar = $student->getEdollar(); #get edollar
    $bidDAO = New BidDAO();
    $biddedModule = $bidDAO->getBidInfo($userid);
    $_SESSION['errors1'] = [];
    $deletemod = $_GET['code'];
    $deletesection = $_GET['section'];
    $modulecounter = 0;
    $sectioncounter = 0;

    //Phase1 getting the round ID and roundstat
    $adminround = new adminRoundDAO();
    $roundDetail = $adminround->retrieveRoundDetail();
    $roundID = $roundDetail->getRoundID();
    $roundstat = $roundDetail->getRoundStatus();

    if (isset($_GET['code']) && isset($_GET['section'])) {
        if (strlen(trim($_GET['code'])) == 0) {
            array_push($_SESSION['errors1'], 'Please enter a Course ID');
        }
        if (strlen(trim($_GET['section'])) == 0) {
            array_push($_SESSION['errors1'], 'Please enter a Section ID');
        }
    }
    if (count($_SESSION['errors1']) > 0) {
        header("Location: deletebid.php?token={$_GET['token']}");
        exit;
    }

    //Phase 1.2, Checking of user input, must be equal or less than 2 decimal place.
    $valuetwodecimalplace = number_format((float)$bidAmt,2,'.','');
    if (($bidAmt - $valuetwodecimalplace) > 0){
        array_push($_SESSION['errors1'], 'Please enter a value and round up to 2 decimal place');
    }
    if (count($_SESSION['errors1']) > 0) {
        header("Location: makebid.php?token={$_GET['token']}");
        exit;
    }
    //making sure all char is upper case
    $deletemod = strtoupper($deletemod);
    $deletesection = strtoupper($deletesection);

    //------------------------------------------------------------------------------------------------------------------------
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
            array_push($_SESSION['errors1'], 'Please enter a valid Course ID');
        }

        if ($modulecounter == 1 and $sectioncounter == 0){
            array_push($_SESSION['errors1'], 'Please enter a valid Section ID');
        }
        
        if (count($_SESSION['errors1']) > 0) {
            header("Location: deletebid.php?token={$_GET['token']}");
            exit;
        }

        //until this stage, the user had already entered a valid Course ID
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
        array_push($_SESSION['errors1'], "You can't drop your bid when the round is not started!");
        header("Location: deletebid.php?token={$_GET['token']}");
        exit;
        
    }
?>