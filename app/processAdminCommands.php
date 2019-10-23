<?php

require_once 'include/common.php';

if (isset($_POST['submit'])) {
     $_SESSION['roundaction'] = $_POST['submit'];
}

/** three actions
 * Start Round
 * Clear Round
 * Reset Round
 */

 // get DAOs
$bidDAO = new BidDAO();
$adminRoundDAO = new adminRoundDAO();
$StudentSectionDAO = new StudentSectionDAO();
$bidprocessorDAO = new BidProcessorDAO();

$roundProcessing = $adminRoundDAO->RetrieveRoundDetail();

// Update round status. If it's call to start round in round 1, go to bootstrap
if ($_SESSION['roundaction'] == "Start Round"){
    $adminRoundDAO->startRound();
    $roundNo = $roundProcessing->getRoundID();
    $roundStatus = $roundProcessing->getRoundStatus();

    if($roundNo == 1 && $roundStatus == "Not Started"){
        header('Location: bootstrap.php');
    }else{
    // clear existing bids when moving into round 2
    $bidDAO->removeAll();
    header('Location: adminMainPage.php');
    }
}

// Update round status & go to admin page
if ($_SESSION['roundaction'] == "Clear Round"){
    $adminRoundDAO->clearRound();
    header('Location: adminMainPage.php');
}

// Update round status, Reset rounds to clean slate (require bootstrap again)
if ($_SESSION['roundaction'] == "Reset Round"){
    $adminRoundDAO->resetRound();
    $StudentSectionDAO->removeAll();
    $bidprocessorDAO->removeAll();
    $bidDAO->removeAll();
    header('Location: adminMainPage.php');
}







?>