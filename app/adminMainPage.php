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
$adminRoundDAO = new adminRoundDAO();
$round = $adminRoundDAO->RetrieveRoundDetail();

$roundNumber = $round->getRoundID();
$roundStatus = $round->getRoundStatus();

?>

<form action="processAdmin.php" method="post">

<?php
echo "<table>
    <tr>
        <th>Round:{$roundNumber}</th>
        <th>Status:{$roundStatus}</th>
    </tr>"


?>

<?php
$disableButton = "disabled value='true'";
if ($roundStatus == "Started"){
    $startStatus = $disableButton;
    $clearStatus = '';
}else{
    $startStatus = '';
    $clearStatus = $disableButton;
}

echo "<tr>
    <td><input type='submit' name='submit' value='Start Round' $startStatus></td>
    <td><input type='submit' name='submit' value='Clear Round' $clearStatus></td>
</tr>"

?>

<tr>
    <td colspan='2'><input type="submit" name="submit" value="Reset Round"></td>
</tr>

</table>

</body>

</form>

</html>