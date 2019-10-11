<html>


<?php
require_once 'include/common.php';

$adminRoundDAO = new adminRoundDAO();
$round = $adminRoundDAO->RetrieveRoundDetail();

if ($round->getRoundID() != 1 && $round->getRoundStatus() != "Started"){
    $bidResults = $adminRoundDAO->clearRoundBids();
} 



?>

<html>
<table>
<tr>
<th colspan = 4>
Bidding Results
</th>
</tr>
<tr>
<th>User ID</th><th>Amount</th><th>Course</th><th>Section</th>
</tr>

<?php


if(isset($bidResults)){
    foreach($bidResults as $result){
        echo "$result";
    }
}else{
    echo "<tr><th colspan = 4>Round is currently ongoing / inactive. </th></tr>";
}

?>


</table>

</html>



<?php

// header('Location: adminMainpage.php');

?>




</html>