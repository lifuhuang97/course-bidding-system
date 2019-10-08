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

    //check for blanks Phase 1 
    if (isset($_POST['code']) && isset($_POST['sectionID']) && isset($_POST['bidAmt'])) {
        if (strlen(trim($_POST['code'])) == 0) {
            array_push($_SESSION['errors1'], 'Please enter Course ID');
        }
        if (strlen(trim($_POST['sectionID'])) == 0) {
            array_push($_SESSION['errors1'], 'Please enter Section ID');
        }
        if (strlen(trim($_POST['bidAmt'])) == 0) {
            array_push($_SESSION['errors1'], 'Please enter bid amount');
        }

    }
    #print $_SESSION['errors1'];
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
        array_push($_SESSION['errors1'], 'Invalid Course ID'); 
    }
    //checking if there is a course in the 'filtered' courses page but the sectioncounter did not increase, it means that the sectionid is 
    //invalid 
    if ($sectioncounter == 0){
        array_push($_SESSION['errors1'], 'Invalid Section ID'); 
    }
    //checking amount if is less than 10 and if user have enough money to bid
    if (floatval($bidAmt) < 10) {
        array_push($_SESSION['errors1'], 'Bid Amount is less than $10 edollar');
    }elseif ($edollar < $bidAmt) {
        array_push($_SESSION['errors1'], 'Insufficient Balance');
    }

    if (count($_SESSION['errors1']) > 0) {
        header("Location: makebid.php?token={$_GET['token']}");
        exit;
    }

    //check phase 3
    //we can only do further checking only if the courseid, sectionid and the amount is correct
    
    //------------------------------------------------------------------------------------------------------
    //checking if there's a clash of timetable
    // this does not account for the date FOR NOW 8/10/2019 checkclasstimetable
    $checkClassTT = CheckClassTimeTable($userid,$courseId,$sectionId);
    $checkExamTT = CheckExamTimeTable($userid,$courseId);
    if ($checkClassTT == False){
        array_push($_SESSION['errors1'], 'ClassTimeTable Clashes');
    }
    if ($checkExamTT == False){
        array_push($_SESSION['errors1'], 'ExamTimeTable Clashes');
    }
    #print ($common==True);
    #$common1 = CheckExamTimeTable($userid,$courseId);
    #print ($common1);
    #if ((CheckClassTimeTable($userid,$courseId,$sectionId)) == False){
    #    print('YOOOOO');
    #    array_push($_SESSION['errors3'], 'ClassTimeTable Clashes');
    #}

    #if ((CheckExamTimeTable($userid,$courseId)) == False){
    #    print('WOOOOO');
    #    array_push($_SESSION['errors3'], 'ExamTimeTable Clashes');
    #}

    //------------------------------------------------------------------------------------------------------


    //A student can bid at most for 5 sections
    $checkforExceed = CheckForExceedOfBidSection($userid,$courseId);
    #var_dump($checkforExceed);
    if (!$checkforExceed){
        array_push($_SESSION['errors1'], 'You currently have 5 bidded sections');
    }
    #if (!(CheckForExceedOfBidSection($userid,$courseId))) {
    #    array_push($_SESSION['errors3'], 'You currently have 5 bidded sections');
    #}

    //A student can only bid for one section per course. 
    $bidDAO = new BidDAO();
    $bidInfo = $bidDAO->getBidInfo($userid);

    foreach ($bidInfo as $bids) {
        if ($bids->getCode() == $courseId) {
            array_push($_SESSION['errors1'], 'You currently have bidded a have current bid for this module');
        }
    }

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
        //i believe this need some token to access 
        header("Location: mainpage.php?token={$_GET['token']}");
        exit;
    }   
?>