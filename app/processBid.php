<?php
	require_once 'include/common.php';
	require_once 'include/function.php';

	$courseId = $_POST['code'];
	$sectionId = $_POST['sectionID'];
	$bidAmt = $_POST['bidAmt'];
	$student=$_SESSION['student'];
	$school = $student->getSchool();
	$userid = $student->getUserid();
	$edollar = $student->getEdollar();
	$_SESSION['errors'] = [];

	//check for blanks
	if (strlen(trim($courseId)) == 0) {
        array_push($_SESSION['errors'], 'Please enter Course ID');
    }
    if (strlen(trim($sectionId)) == 0) {
        array_push($_SESSION['errors'], 'Please enter Section ID');
    }
    if (strlen(trim($bidAmt)) == 0) {
        array_push($_SESSION['errors'], 'Please enter bid amount');
    }

    //check for course ID
    //check for section ID
    if (count($_SESSION['errors']) == 0) {
    	$courseDAO = new CourseDAO();
        $course = $courseDAO->RetrieveAllCourseDetail('', '', $school);
        $realarray= [];
        foreach ($course as $value) {
            $a = ($value->getCourseid());
            $b = ($value->getSectionid());
            array_push($realarray, $a);
            array_push($realarray, $b);
        }
        if (!(in_array($courseId, $realarray))){
   			array_push($_SESSION['errors'], 'Invalid Course ID');
        }
        if (!(in_array($sectionId, $realarray))){
   				array_push($_SESSION['errors'], 'Invalid Section Course ID');
        }	
    }

    //check for Bid Amount
    $bidDAO = new BidDAO();
    $bidInfo = $bidDAO->getBidInfo($userid);
    if (sizeof($bidInfo) == 0) {
    	if ($bidAmt < 0 || $bidAmt > $edollar) {
    		array_push($_SESSION['errors'], 'Invalid Bid Amount');
    	}
    }

    // return to makebid.php if there is error
    if (sizeof($_SESSION['error']) > 0) {
    	header("makebid.php");
    }
?>