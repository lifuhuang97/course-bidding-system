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

        $biddingDAO = new BidDAO();
        $modules = $biddingDAO->getBidInfo($userid);
        $biddedModule = $biddingDAO->getBidInfo($_SESSION['success']);
    }

	if (isset($_POST['code']) && isset($_POST['newBidAmt']) && isset($_POST['section'])) {
		$code = $_POST['code'];
		$section = $_POST['section'];
		$newBidAmt = $_POST['newBidAmt'];
		$status = $biddingDAO->update($userid, $code, $section, $newBidAmt);
		if ($status) {
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
		elseif ($status == False) {
			echo "Update not successful. Try again.";
		}
	}
?>