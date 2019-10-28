<?php

require_once 'include/common.php';
require_once 'include/start.php';
require_once 'include/stop.php';
require_once 'include/restart.php';

if (isset($_POST['submit'])) {
     $_SESSION['roundaction'] = $_POST['submit'];
}

/** three actions
 * Start Round
 * Clear Round
 * Reset Round
 */

 // get DAOs
$adminRoundDAO = new adminRoundDAO();

$round = $adminRoundDAO->retrieveRoundDetail();
$roundNo = $round->getRoundID();
$roundStatus = $round->getRoundStatus();

// Update round status. If it's call to start round in round 1, go to bootstrap
if ($_SESSION['roundaction'] == "Start Round"){
    doStart();
    if($roundNo == 1 && $roundStatus == "Not Started"){
        header('Location: bootstrap.php');
    }else{
        header('Location: adminMainPage.php');
    }
}

// Update round status & go to admin page
if ($_SESSION['roundaction'] == "Clear Round"){
    doStop();
    header('Location: adminMainPage.php');
}

// Update round status, Reset rounds to clean slate (require bootstrap again)
if ($_SESSION['roundaction'] == "Reset Round"){
    doRestart();
    header('Location: adminMainPage.php');
}

?>