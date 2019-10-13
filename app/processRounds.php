<html>


<?php
require_once 'include/common.php';


$successBidDAO = new StudentSectionDAO();
$allSuccessfulBids = $successBidDAO->getAllSuccessfulBids();
$allBidsWithStatus = $successBidDAO->getAllBidsWithStatus();

$adminRoundDAO = new adminRoundDAO();
$round = $adminRoundDAO->RetrieveRoundDetail();
$roundStatus = $round->getRoundStatus();


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
}elseif((isempty($allBidsWithStatus)) || ($round->getRoundID() == 2 && !isset($bidResults))){
    
    echo "<tr><th colspan = 5>Round is currently ongoing / inactive. </th></tr>";
}


// preserve bid results to be visible before round 2 starts
if(!isempty($allBidsWithStatus) && $roundStatus != "Started"){
    foreach($allBidsWithStatus as $bid){
        echo "<tr><td>$bid[0]</td><td>$bid[1]</td><td>$bid[2]</td><td>$bid[3]</td><td>$bid[4]</td></tr>";
    }
}

?>

</table>

</html>

</html>