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
        $bidprocessorDAO->removeAll();
        header('Location: bootstrap.php');
    }else{
    // clear existing bids when moving into round 2
    header('Location: adminMainPage.php');
    }
}

// Update round status & go to admin page
if ($_SESSION['roundaction'] == "Clear Round"){

$adminRoundDAO = new adminRoundDAO();
$round = $adminRoundDAO->RetrieveRoundDetail();
$roundNo = $round->getRoundID();
$roundStatus = $round->getRoundStatus();

$successBidDAO = new StudentSectionDAO();
$allSuccessfulBids = $successBidDAO->getAllSuccessfulBids();

$currentBidsDAO = new BidDAO();

$bidsRecordsDAO = new BidProcessorDAO();
$sectDAO = new SectionDAO();
$sections = $sectDAO->getAllSections();

    foreach($sections as $section){
        $courseID = $section[0];
        $sectionID = $section[1];
        $bids = $currentBidsDAO->getAllBids($section);

        $sectMinBid = (float)CheckMinBidFromBiddingResult($courseID, $sectionID);
        $sectionMinBid = (float)$sectMinBid;

        foreach($bids as $bid){
            $bidID = $bid->getUserid();
            $bidAmount = $bid->getAmount();
            $bidAmt = (float)$bidAmount;
            $bidCourse = $bid->getCode();
            $bidSection = $bid->getSection();

            $bidsRecordsDAO->addBidResults($bidID,$bidAmt,$bidCourse,$bidSection,"Pending",$roundNo);

            if($bidAmt <= $sectionMinBid){
                $bidsRecordsDAO->updateBidStatus($bidID,$bidAmt,$bidCourse,$bidSection, false);
            }elseif($bidAmt > $sectionMinBid){
                $bidsRecordsDAO->updateBidStatus($bidID,$bidAmt,$bidCourse,$bidSection,true);
                $successBidDAO->addBidResults($bidID,$bidAmt,$bidCourse,$bidSection,"Success",$roundNo);  
            }
        }
        
    }

    //clear bid inventory after round is processed
    $currentBidsDAO->removeAll();
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