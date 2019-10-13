<?php
	require_once 'include/common.php';
    require_once 'include/function.php';
    require_once 'include/protect.php';

    if (!isset($_SESSION['success'])) {
    	header('Location: login.php');
    	exit;
    }
    else{
        $student = $_SESSION['student']; 
        $userid = $student->getUserid(); #get userid
        $password = $student->getPassword(); #get password
        $name = $student->getName(); #get name
        $school = $student->getSchool(); #get school
        $edollar = $student->getEdollar(); #get edollar
		$_SESSION['errors1'] = [];
		$_SESSION['errors2'] = [];

        $biddingDAO = New BidDAO();
        $modules = $biddingDAO->getBidInfo($_SESSION['success']);
        $biddedCourse = [];
        $biddedSection = [];
        foreach ($modules as $mods) {
        	array_push($biddedCourse, $mods->getCode());
        	array_push($biddedSection, $mods->getSection());
        }
    }

	$code = $_POST['code'];
	$section = $_POST['section'];
	$newBidAmt = $_POST['newBidAmt'];

	//do validation
	//check if user did enter anything to the text field
	//if user never add anything return error message and go back to editbid.php
	if (isset($_POST['code']) && isset($_POST['newBidAmt']) && isset($_POST['section'])) {
		
		// in case user enters all spaces
		if (strlen(trim($_POST['code'])) == 0) {
            array_push($_SESSION['errors1'], 'Please enter a Course ID');
        }
        if (strlen(trim($_POST['newBidAmt'])) == 0) {
            array_push($_SESSION['errors1'], 'Please enter a Section ID');
		}
		if (strlen(trim($_POST['section'])) == 0) {
            array_push($_SESSION['errors1'], 'Please enter a Section ID');
		}
		if (count($_SESSION['errors1']) > 0) {
	        header("Location: editBid.php?token={$_GET['token']}");
	        exit;
		} else {
			//making sure all char is upper case
			$code = strtoupper($code);
			$section = strtoupper($section);

			if (!in_array($code, $biddedCourse)) {
				array_push($_SESSION['errors2'], 'Please enter a valid Course ID');
			} elseif (!in_array($section, $biddedSection)) {
				array_push($_SESSION['errors2'], 'Please enter a valid Section ID');
			}

			foreach ($modules as $mods) {
				$checkMod = $mods->getCode();
				$checkSec = $mods->getSection();
				$checkAmt = $mods->getAmount();
				if ($code == $checkMod && $section == $checkSec && $newBidAmt != $checkAmt) {
					if ($newBidAmt < 10 || $newBidAmt > $edollar) {
						array_push($_SESSION['errors2'], 'Please enter a valid bid amount');
					} elseif ($newBidAmt >= 10 && $newBidAmt < $edollar) {
						$status = $biddingDAO->update($userid, $code, $section, $newBidAmt);
					}
				}
			}

			if (count($_SESSION['errors2']) > 0) {
				header("Location: editBid.php?token={$_GET['token']}");
	        	exit;
			} else {
				$modules = $biddingDAO->getBidInfo($_SESSION['success']);
				$biddedAmt = 0;
				foreach ($modules as $mods) {
					$c = $mods->getAmount();
					$biddedAmt += $c;
				}
				$remainCredit = 200 - $biddedAmt;
				$_SESSION['remain'] = $biddedAmt;
				$studentDAO = new StudentDAO();
			    $status = $studentDAO->updateDollar($userid, $remainCredit);
			    header("Location: mainpage.php?token={$_GET['token']}");
			    exit;
			}
		}
	}
?>