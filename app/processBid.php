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
    $coursecounter = 0;
    $sectioncounter = 0;

    //-----
    //get all complete course 
    $completedDAO = New CourseCompletedDAO();
    $ccompleted = $completedDAO->getAllCourseComplete($userid);

    //preparing for removing modules that user alr completed
    $courseDAO= new CourseDAO();
    $courses=$courseDAO->retrieveAllCourseDetail('', '', $school);

    //retrieve all courses *with different school*
    $allCourses = $courseDAO->retrieveAllCourseDetail('', '', '');
    //----

    //getting the round ID and roundstat
    $adminround = new adminRoundDAO();
    $roundDetail = $adminround->retrieveRoundDetail();
    $roundID = $roundDetail->getRoundID();
    $roundstat = $roundDetail->getRoundStatus();

    $courseId = strtoupper($courseId);
    $sectionId = strtoupper($sectionId);
    //check for blanks Phase 1 
    if (isset($_POST['code']) && isset($_POST['sectionID']) && isset($_POST['bidAmt'])) {
        if (strlen(trim($_POST['code'])) == 0) {
            array_push($_SESSION['errors1'], 'Please enter a Course ID.');
        }
        if (strlen(trim($_POST['sectionID'])) == 0) {
            array_push($_SESSION['errors1'], 'Please enter a Section ID.');
        }
        if (strlen(trim($_POST['bidAmt'])) == 0) {
            array_push($_SESSION['errors1'], 'Please enter a bid amount.');
        }

    }
    //above is the front end portion ---------------------------------------------------------------
    $sectioncounter1=0;
    $coursecounter1=0;
    $x = 1;
    
    //checking if there's this courseid in the (whole list of the courses) ---

    foreach ($allCourses as $course){
        if ($course->getCourseid() == $courseId){
            $coursecounter1 += 1;
            if ($course->getSectionid() == $sectionId){
                $sectioncounter1 += 1;
            }
        }
    }
    //checking if there is a course in the courses page but if the coursecounter1 did not increase, it means that the courseId is 
    //invalid
    if ($coursecounter1 == 0 ){
        array_push($_SESSION['errors1'], 'Please enter a valid Course ID.'); 
    }
    //checking if there is a course in the courses page but if the sectioncounter1 did not increase, it means that the sectionid is 
    //invalid 
    if ($sectioncounter1 == 0){
        array_push($_SESSION['errors1'], 'Please enter a valid Section ID.'); 
    }
    //----
    //Phase 1.2, Checking of user input, must be equal or less than 2 decimal place.
    if (!is_numeric($bidAmt)){
        //check if the amount the user entered is numeric
        array_push($_SESSION['errors1'], 'Please enter a valid amount.');
    }else{
        if ($bidAmt<10){
            // if amount is less than 10
            array_push($_SESSION['errors1'], 'Please enter a value more than 9.99.');
        }
        
        if(strpos($bidAmt,'.')!=FALSE){
            $temp=explode('.',$bidAmt);
            if (strlen($temp[1])>2){
                array_push($_SESSION['errors1'], 'Please enter a value and round it up to 2 decimal place.');
            }
        }
    }
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

    //------------------------another checking**********
    if ($roundID == 1 && $roundstat == 'Started' || $roundID == 2 && $roundstat == 'Started'){
        //CHECKING OF PREREQ------------------------------------
        $checking = CheckForCompletedPrerequisites($userid,$courseId);
        if ($checking == True){
            #continue;            #array_push($_SESSION['errors1'], 'You has not completed the prerequisites for this course.');
        }
        elseif ($checking == False){
            array_push($_SESSION['errors1'], 'You has not completed the prerequisites for this course.');
        }

        //checking for if amount is more than that the user have 
        if ($bidAmt>$edollar){
            array_push($_SESSION['errors1'], 'Insufficient Edollar.');
            // if amount is more than that the user have             
        }

        //check if there is vacancy
        $seats = CheckVacancy($courseId,$sectionId);
        if($seats !== 'No record found.') {
            if (!$seats){
                array_push($_SESSION['errors1'], 'There is no vacancy left.');
            }
        }
        //checking at round 2 if the use did enter a value more than the minimum required bid
        if ($roundID==2){
            $SectionDAO = new SectionDAO();
            $currentMinBid = $SectionDAO->viewMinBid($courseId,$sectionId);
            if ($currentMinBid!='-' && $bidAmt < $currentMinBid){
                array_push($_SESSION['errors1'], 'Please enter a value higher than the Minimum Required Bid.');
            }
        }

        //checking of clash in timetable and exam timeable 
        $checkClassTT = CheckClassTimeTable($userid,$courseId,$sectionId);
        $checkExamTT = CheckExamTimeTable($userid,$courseId);
        if ($checkClassTT == False){
            array_push($_SESSION['errors1'], 'There is a clash in the Lesson date and time.');
        }
        if ($checkExamTT == False){
            array_push($_SESSION['errors1'], 'There is a clash in the Exam date and time.');
        }

        //A student can bid at most for 5 sections
        $checkforExceed = CheckForExceedOfBidSection($userid,$courseId);
        if (!$checkforExceed){
            array_push($_SESSION['errors1'], 'You currently have 5 bidded sections.');
        }

        //A student can only bid for one section per course. 
        $bidDAO = new BidDAO();
        $bidInfo = $bidDAO->getBidInfo($userid);

        foreach ($bidInfo as $bids) {
            if ($bids->getCode() == $courseId) {
                array_push($_SESSION['errors1'], 'You have already bidded for this module.');
            }
        }

        $ccounter = 0;
        //user had already complete this module
        foreach ($ccompleted as $completed){
            $nameofuser = $completed->getUserid();
            $coursecodecompleted = $completed->getCode();

            if ($nameofuser==$name && $coursecodecompleted==$courseId){
                $ccounter += 1;
            }
            
        }
        //if ccounter is more than 1, it means that the user had already completed that mod
        if ($ccounter >= 1 ){
            array_push($_SESSION['errors1'], 'You had already completed this course.'); 
        }       

        //user had already won a bid for a section in this course in a previous round.
        $successcounter = 0;
        if ($roundID==2){
            $StudentSecDAO = new StudentSectionDAO();
            $studentsuccessbid = $StudentSecDAO->getSuccessfulBidsByID($userid);

            foreach ($studentsuccessbid as $row){
                if ($row[2] == $courseId){
                    $successcounter += 1;
                }
            }
             
        }
        //if successcounter is more than 1, it means that the user had already enrolled into that mod
        if ($successcounter >= 1 ){
            array_push($_SESSION['errors1'], 'You had already enrolled in this course.'); 
        } 
        

        //not own school, but only for round 1
        $incourse = 0;
        if ($roundID==1){  
            foreach ($courses as $course){

                if ($course->getCourseid() == $courseId){
                    $incourse += 1;
                }
            }
            //if incourse is equal to 0, it means that the user had bidded a mod outside of his/her school
            //the list of school is filtered base of the school that the user is in
            if ($incourse == 0 ){
                array_push($_SESSION['errors1'], 'In round 1, You are not allowed to bid for course from other school.'); 
            } 
        }
        


        //------------------------------------------------

        //all the course if there is no error in error1, if it passes thought this, please that there's no error at all
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

    }else{
        //this is the error message area to tell use the round is not started
        array_push($_SESSION['errors1'], "There is no active round!");
        header("Location: makebid.php?token={$_GET['token']}");
        exit;
    }

?>