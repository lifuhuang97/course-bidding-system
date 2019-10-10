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

$adminRoundDAO = new adminRoundDAO();

$roundProcessing = $adminRoundDAO->RetrieveRoundDetail();

if ($_SESSION['roundaction'] == "Start Round"){

    $adminRoundDAO->startRound();

    $roundNo = $roundProcessing->getRoundID();
    $roundStatus = $roundProcessing->getRoundStatus();

    if($roundNo == 1 && $roundStatus == "Not Started"){
        
        header('Location: bootstrap.php');
    }else{

    header('Location: adminMainpage.php');
    }
}

if ($_SESSION['roundaction'] == "Clear Round"){
    $adminRoundDAO->clearRound();
    header('Location: processRounds.php');
}

if ($_SESSION['roundaction'] == "Reset Round"){
    $adminRoundDAO->resetRound();

    header('Location: adminMainpage.php');
}







?>