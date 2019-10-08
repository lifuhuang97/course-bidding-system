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

var_dump($roundProcessing);

var_dump($_SESSION['roundaction']);

var_dump($roundProcessing->getR1Start());


if ($_SESSION['roundaction'] == "Start Round"){

    // //Draw link to bootstrap
    // if($roundProcessing->getR1Start() == null){
    //     header('Location: bootstrap.php');
    // }

    $adminRoundDAO->startRound();


    header('Location: adminMainpage.php');
}

if ($_SESSION['roundaction'] == "Clear Round"){
    $adminRoundDAO->clearRound();
    header('Location: adminMainpage.php');
}

if ($_SESSION['roundaction'] == "Reset Round"){
    $adminRoundDAO->resetRound();

    //Draw link to bootstrap
    header('Location: adminMainpage.php');
}







?>