<?php
	require_once 'include/common.php';
    require_once 'include/function.php';
    require_once 'include/protect.php';

    //this variable holds the available courses excluding the bidded,courses that require prerequisties and course completed by user
    $currentavailablecourses = $_SESSION['availablecourses'];
    #var_dump($currentavailablecourses);
    $courseId = $_POST['code'];
	$sectionId = $_POST['sectionID'];
	$bidAmt = $_POST['bidAmt'];
    $student=$_SESSION['student'];
    $userid = $student->getUserid(); #get userid
    $password = $student->getPassword(); #get password
    $name = $student->getName(); #get name
    $school = $student->getSchool(); #get school
    $edollar = $student->getEdollar(); #get edollar
    $_SESSION['errors1'] = [];
    $_SESSION['errors2'] = [];
    $_SESSION['errors3'] = [];
    $coursecounter = 0;
    $sectioncounter = 0;

    //getting the round ID and roundstat
    $adminround = new adminRoundDAO();
    $roundDetail = $adminround->retrieveRoundDetail();
    $roundID = $roundDetail->getRoundID();
    $roundstat = $roundDetail->getRoundStatus();


    //check for blanks Phase 1 
    if (isset($_POST['code']) && isset($_POST['sectionID']) && isset($_POST['bidAmt'])) {
        if (strlen(trim($_POST['code'])) == 0) {
            array_push($_SESSION['errors1'], 'Please enter a Course ID');
        }
        if (strlen(trim($_POST['sectionID'])) == 0) {
            array_push($_SESSION['errors1'], 'Please enter a Section ID');
        }
        if (strlen(trim($_POST['bidAmt'])) == 0) {
            array_push($_SESSION['errors1'], 'Please enter a bid amount');
        }

    }
    #if there's a error, exit this page and go to makebid.php page and display the error message stored inside $_SESSION['errors1']
    if (count($_SESSION['errors1']) > 0) {
        header("Location: makebid.php?token={$_GET['token']}");
        exit;
    }
    //Phase 1.2, Checking of user input, must be equal or less than 2 decimal place.
    $valuetwodecimalplace = number_format((float)$bidAmt,2,'.','');
    if (($bidAmt - $valuetwodecimalplace) > 0){
        array_push($_SESSION['errors1'], 'Please enter a value and round it up to 2 decimal place');
    }
    #if there's a error, exit this page and go to makebid.php page and display the error message stored inside $_SESSION['errors1']
    if (count($_SESSION['errors1']) > 0) {
        header("Location: makebid.php?token={$_GET['token']}");
        exit;
    }
    //check phase 2
    //after making sure that the inputs by the users are not empty/blank 
    //we can do the following checks,
    //check for course ID
    //check for section ID
    //check for bidding amount
    //making sure all char is upper case
    $courseId = strtoupper($courseId);
    $sectionId = strtoupper($sectionId);

    //------------------------------------------------------------------------------------------------------------------------
    // checking is the round status is started.
    if ($roundID == 1 && $roundstat == 'Started' || $roundID == 2 && $roundstat == 'Started'){
        foreach ($currentavailablecourses as $items){
            if ($items[0] == $courseId){
                $coursecounter += 1;
                if ($items[2] == $sectionId){
                    $sectioncounter += 1;
                }
            }
        }
        //checking if there is a course in the 'filtered' courses page
        if ($coursecounter == 0 ){
            array_push($_SESSION['errors1'], 'Please enter a valid Course ID.'); 
        }
        //checking if there is a course in the 'filtered' courses page but the sectioncounter did not increase, it means that the sectionid is 
        //invalid 
        if ($sectioncounter == 0){
            array_push($_SESSION['errors1'], 'Please enter a valid Section ID'); 
        }
        //checking amount if is less than 10 and if user have enough money to bid
        if (floatval($bidAmt) < 10) {
            array_push($_SESSION['errors1'], 'Please Bid an Amount that is high than $9.99 edollar');
        }elseif ($edollar < $bidAmt) {
            array_push($_SESSION['errors1'], 'You do not have enough edollar');
        }

        //check if there is vacancy
        $seats = CheckVacancy($courseId,$sectionId);
        if($seats !== 'No record found.') {
            if (!$seats){
                array_push($_SESSION['errors1'], 'There is no vacancy left.');
            }
        }
        #if there's a error, exit this page and go to makebid.php page and display the error message stored inside $_SESSION['errors1']
        if (count($_SESSION['errors1']) > 0) {
            header("Location: makebid.php?token={$_GET['token']}");
            exit;
        }
        
        //check if use bid equal or more than the min required bid
        if ($roundID==2){
            $SectionDAO = new SectionDAO();
            $currentMinBid = $SectionDAO->viewMinBid($courseId,$sectionId);
            if ($currentMinBid!='-' && $bidAmt < $currentMinBid){
                array_push($_SESSION['errors1'], 'Please enter a value higher than the Minimum Required Bid.');
            }
        }
        #if there's a error, exit this page and go to makebid.php page and display the error message stored inside $_SESSION['errors1']
        if (count($_SESSION['errors1']) > 0) {
            header("Location: makebid.php?token={$_GET['token']}");
            exit;
        }

        //check phase 3
        //we can only do further checking only if the courseid, sectionid and the amount is correct
        
        //------------------------------------------------------------------------------------------------------
        //checking if there's a clash of timetable
        $checkClassTT = CheckClassTimeTable($userid,$courseId,$sectionId);
        $checkExamTT = CheckExamTimeTable($userid,$courseId);
        if ($checkClassTT == False){
            array_push($_SESSION['errors1'], 'There is a clash in the Lesson date and time');
        }
        if ($checkExamTT == False){
            array_push($_SESSION['errors1'], 'There is a clash in the Exam date and time');
        }
        //------------------------------------------------------------------------------------------------------


        //A student can bid at most for 5 sections
        $checkforExceed = CheckForExceedOfBidSection($userid,$courseId);
        #var_dump($checkforExceed);
        if (!$checkforExceed){
            array_push($_SESSION['errors1'], 'You currently have 5 bidded sections');
        }

        //A student can only bid for one section per course. 
        $bidDAO = new BidDAO();
        $bidInfo = $bidDAO->getBidInfo($userid);

        foreach ($bidInfo as $bids) {
            if ($bids->getCode() == $courseId) {
                array_push($_SESSION['errors1'], 'You have already bidded for this module');
            }
        }
        #if there's a error, exit this page and go to makebid.php page and display the error message stored inside $_SESSION['errors1']
        if (count($_SESSION['errors1']) > 0) {
            header("Location: makebid.php?token={$_GET['token']}");
            exit;
        }
        
        //add to the user bidding table
        //- the amount from the user edollar
        //create a html table to show the course that the user just bidded for 
        $status=$bidDAO->add(new Bid($userid, $bidAmt, $courseId, $sectionId));
        if ($status){
            //deduct from account
            $remainCredit=$edollar-$bidAmt;
            $studentDAO=new StudentDAO();
            $status=$studentDAO->updateDollar($userid,$remainCredit);
            //update sectionTable
            $minbid = CheckMinBid($courseId,$sectionId);
            if ($roundID==2 && $minbid>$currentMinBid){
                $SectionDAO->updateSectionMinBid($minbid,$courseId,$sectionId);
            }
            header("Location: mainpage.php?token={$_GET['token']}");
            exit;
        }
    } else {
        //this is the error message area to tell use the round is not started
        array_push($_SESSION['errors1'], "You can't add your bid when the round is not started!");
        header("Location: makebid.php?token={$_GET['token']}");
        exit;
    }   
?>