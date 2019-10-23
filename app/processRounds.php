<html>


<?php
require_once 'include/common.php';


$successBidDAO = new StudentSectionDAO();
$allSuccessfulBids = $successBidDAO->getAllSuccessfulBids();



$adminRoundDAO = new adminRoundDAO();
$round = $adminRoundDAO->RetrieveRoundDetail();
$roundNo = $round->getRoundID();
$roundStatus = $round->getRoundStatus();

$bidsRecordsDAO = new BidProcessorDAO();
$allBidsWithStatus = $bidsRecordsDAO->getAllBidsWithStatus($roundNo);

// Process bid results after "Clear round" is clicked
if ($round->getRoundID() != 1 && $roundStatus != "Started"){
    $bidResults = $adminRoundDAO->clearRoundBids();
} 
?>

<html>
<br>

<table>
<tr>
<th colspan = 5>
Bidding Results
</th>
</tr>
<tr>
<th>User ID</th><th>Amount</th><th>Course</th><th>Section</th><th>Result</th>
</tr>

<?php

// If round is cleared, print bid results to admin
if(isset($bidResults)){
    foreach($bidResults as $result){
        echo "$result";
    }
}elseif($roundStatus == "Started"){
    echo "<tr><td colspan = 5> CURRENT BIDS</td></tr>";
    foreach($allBidsWithStatus as $bid){
        $user = $bid->getUserid();
        $amount = $bid->getAmount();
        $course = $bid->getCourse();
        $section = $bid->getSection();
        $result = $bid->getBidStatus();

        echo "<tr><td>$user</td><td>$amount</td><td>$course</td><td>$section</td><td>$result</td></tr>";

    }
}


// preserve bid results to be visible before round 2 starts
if(!isempty($allBidsWithStatus) && $roundStatus != "Started"){
    echo "<tr><td colspan = 5>Previous Round Records</td></tr>";
    foreach($allBidsWithStatus as $bid){
        echo "<tr><td>{$bid->getUserid()}</td><td>{$bid->getAmount()}</td><td>{$bid->getCourse()}</td><td>{$bid->getSection()}</td><td>{$bid->getBidStatus()}</td><td>{$bid->getBidRound()}</td></tr>";
    }
}

?>

</table>

</html>

</html>