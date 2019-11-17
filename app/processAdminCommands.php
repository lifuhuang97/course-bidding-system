<?php
require_once 'include/common.php';
require_once 'include/DoStart.php';
require_once 'include/DoStop.php';
require_once 'include/DoRestart.php';
require_once 'include/protect.php';

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

// check for round status. if "Bootstrap & Start Round" (round 1 not started) => go bootstrap , if "Start Round" (round 2 not started) => go to admin page
if($_SESSION['roundaction'] == "Start Round") {
    doStart();
    header("Location: adminMainPage.php?token={$_GET['token']}");
}
elseif($_SESSION['roundaction'] == "Bootstrap & Start Round") {
    doStart();
    header("Location: bootstrap.php?token={$_GET['token']}");
}

// Update round status & go to admin page
if ($_SESSION['roundaction'] == "Clear Round"){
    doStop();
    header("Location: adminMainPage.php?token={$_GET['token']}");
}

// Update round status, Reset rounds to clean slate (require bootstrap again)
if ($_SESSION['roundaction'] == "Reset Round"){
    doRestart();
    header("Location: adminMainPage.php?token={$_GET['token']}");
}

?>