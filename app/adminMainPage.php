<?php
    require_once 'include/common.php';
    // require_once 'include/protect.php';
    // if (!isset($_SESSION['success'])){
    //     header('Location.php');
    //     exit;
    // }

?>


<html>
<head>
    <title>BOSS Bidding System</title>
    <style> 
        table, th, td {
            text-align:center; 
        } 
  
    </style> 
</head>

<body>
    <p>Welcome, Admin!</p>
    <br>
    <br>

<?php

// Get up-to-date round details
$adminRoundDAO = new adminRoundDAO();
$round = $adminRoundDAO->RetrieveRoundDetail();
$roundNumber = $round->getRoundID();
$roundStatus = $round->getRoundStatus();

?>

<form action="processAdminCommands.php" method="post">

<?php

//display current round & status
echo "<table>
<tr><th colspan='6'>Bid System Status</th></tr>
    <tr><th></th><th></th>
        <th>Round: {$roundNumber}</th>
        <th>Status: {$roundStatus}</th>
        <th></th>
        <th></th>
    </tr>"


?>

<?php

// disables buttons according to round status
$disableButton = "disabled value='true'";

if ($roundStatus == "Started"){
    $startStatus = $disableButton;
    $clearStatus = '';
}else{
    if ($roundNumber == 1){
        $startStatus = '';
        $clearStatus = $disableButton;
    }else if($roundNumber == 2 && $roundStatus == "Finished"){
        $startStatus = $disableButton;
        $clearStatus = $disableButton; 
    }else{
        $startStatus = '';
        $clearStatus = $disableButton;
    }
}

echo "<tr>
<td></td><td></td>
    <td><input type='submit' name='submit' value='Start Round' $startStatus></td>
    <td><input type='submit' name='submit' value='Clear Round' $clearStatus></td>
    <td></td><td></td>
</tr>"

?>
<!-- to reset database to base state >require new bootstrap< -->
<tr>
    <td colspan='6'><input type="submit" name="submit" value="Reset Round"></td>
</tr>

</table>

<?php

/** Display bid results after round ends */

include 'processRounds.php';
 
?>
<br>

<a href="logout.php">Logout</a>

</body>

</form>

</html>