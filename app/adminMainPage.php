<?php
    require_once 'include/dump.php';
    require_once 'include/user-dump.php';
    require_once 'include/bid-dump.php';
    require_once 'include/section-dump.php';
    require_once 'include/common.php';
    require_once 'include/function.php';
    require_once 'include/bid-status.php';
    require_once 'include/update-bid.php';
    require_once 'include/delete-bid.php';
    require_once 'include/drop-section.php';
    // require_once 'include/protect.php';
    // if (!isset($_SESSION['success'])){
    //     header('Location: login.php');
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
$roundNo = $round->getRoundID();
$roundStatus = $round->getRoundStatus();

?>

<form action="processAdminCommands.php" method="post">

<?php

//display current round & status
echo "<table>
<tr><th colspan='6'>Bid System Status</th></tr>
    <tr><th></th><th></th>
        <th>Round: {$roundNo}</th>
        <th>Status: {$roundStatus}</th>
        <th></th>
        <th></th>
    </tr>";

?>

<?php

// disables buttons according to round status
$disableButton = "disabled value='true'";

if ($roundStatus == "Started"){
    $startStatus = $disableButton;
    $clearStatus = '';
}else{
    if ($roundNo == 1){
        $startStatus = '';
        $clearStatus = $disableButton;
    }elseif($roundNo == 2 && $roundStatus == "Finished"){
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
</tr>
</form>";

?>
<!-- to reset database to base state >require new bootstrap< -->
<tr>
    <td colspan='6'><input type="submit" name="submit" value="Reset Round"></td>
</tr>

</table>

<?php

/** Display bid results after round ends */

// $successBidDAO = new StudentSectionDAO();
// $allSuccessfulBids = $successBidDAO->getAllSuccessfulBids();

if (!($roundNo==1 && $roundStatus=='Not Started') && $roundStatus!='Finished'){
    $currentBidsDAO = new BidDAO();
    $allBids = $currentBidsDAO->RetrieveAll();
    echo "<table>
    <tr>    
        <th colspan = 6>";
    if ($roundStatus=="Started"){
        echo"Current Bids";
    }else{
        echo"Bidding Results";
    }
    if (count($allBids)>0){
        echo"</tr>
        <tr>
            <th>User ID</th><th>Amount</th><th>Course</th><th>Section</th><th>Result</th>
        </tr>";
        foreach ($allBids as $bid){
            $bidID = $bid->getUserid();
            $bidAmt = $bid->getAmount();
            $bidCourse = $bid->getCode();
            $bidSect = $bid->getSection();
            if ($roundNo==1){
                $result="pending";
            }else{
                $minbid=CheckMinBid($bidCourse, $bidSect,FALSE);
                if ($bidAmt>=$minbid){
                    $result="success";
                }else{
                    $result="fail";
                }
            }
            echo "<tr>
                <td>$bidID</td>
                <td>$bidAmt</td>
                <td>$bidCourse</td>
                <td>$bidSect</td>
                <td>$result</td>
            </tr>";
        }
    }else{
        if ($roundStatus=="Started"){
            echo "<tr><td colspan = 6>No Existing Bids</td></tr>";
        }else{
            echo "<tr><td colspan = 6>No Bids Available</td></tr>";
        }
    }
    echo"</table>";
}
?>
<br>
<br>
<br>
<form action="adminMainPage.php" method="post">
<input type='submit' name='navigation' value='Show All Data'>
<input type='submit' name='navigation' value='Show Student'>
<input type='submit' name='navigation' value='Show Bid By Section'>
<input type='submit' name='navigation' value='Show All Student Section'>
<input type='submit' name='navigation' value='Show Bid Status'>
<input type='submit' name='navigation' value='Update bid'>
<input type='submit' name='navigation' value='Delete bid'>
<input type='submit' name='navigation' value='Drop Section'>

<?php
    if (isset($_POST['navigation']) || isset($_POST['studentSelect']) || isset($_POST['sectionSelect']) || isset($_POST['sectionsSelect']) || isset($_POST['bidStatusSelect']) || isset($_POST['updateBid']) || isset($_POST['deleteBid']) || isset($_POST['dropSection'])){
        if (isset($_POST['navigation']) && $_POST['navigation']=='Show All Data'){
            $result=doDump();
            echo "<br><table>
            <tr><th>Show All Data</th></tr>
            <tr><td><a href='adminMainPage.php#course'>Course Table</a></td></tr>
            <tr><td><a href='adminMainPage.php#section'>Section Table</a></td></tr>
            <tr><td><a href='adminMainPage.php#student'>Student Table</a></td></tr>
            <tr><td><a href='adminMainPage.php#prerequisite '>Prerequisite Table</a></td></tr>
            <tr><td><a href='adminMainPage.php#bid'>Bid Table</a></td></tr>
            <tr><td><a href='adminMainPage.php#completedCourse'>Completed Course Table</a></td></tr>
            <tr><td><a href='adminMainPage.php#sectionStudent'>Section Student Table</a></td></tr>
            </table>";
            //course
            echo"<section id='course'><h3>Course Table</h3></section>";
            echo"<table border='1'>
            <tr>
                <th>Row</th>
                <th>Course</th>
                <th>School</th>
                <th>Title</th>
                <th>Description</th>
                <th>Exam Date</th>
                <th>Exam Start</th>
                <th>Exam End</th>
            </tr>";
            $count=1;
            if (count($result['course'])>0){
                foreach($result['course'] as $row){
                    echo"<tr>
                        <td>$count</td>
                        <td>{$row['course']}</td>
                        <td>{$row['school']}</td>
                        <td>{$row['title']}</td>
                        <td>{$row['school']}</td>
                        <td>{$row['exam date']}</td>
                        <td>{$row['exam start']}</td>
                        <td>{$row['exam end']}</td>
                    </tr>";
                    $count++;
                }
            }else{
                echo"<tr>
                <td colspan='8'>no data</td>
                </tr>";
            }
            echo"</table>";
            //section
            echo"<section id='section'><h3>Section Table</h3></section>";
            echo"<table border='1'>
            <tr>
                <th>Row</th>
                <th>Course</th>
                <th>Section</th>
                <th>Day</th>
                <th>Start</th>
                <th>End</th>
                <th>Instructor</th>
                <th>Venue</th>
                <th>Size</th>
            </tr>";
            $count=1;
            if (count($result['section'])>0){
                foreach($result['section'] as $row){
                    $weekday=[1=>'MON',2=>'TUE',3=>'WED',4=>'THU',5=>'FRI',6=>'SAT',7=>'SUN'];
                    echo"<tr>
                        <td>$count</td>
                        <td>{$row['course']}</td>
                        <td>{$row['section']}</td>
                        <td> {$weekday[$row['day']]}</td>
                        <td>{$row['start']}</td>
                        <td>{$row['end']}</td>
                        <td>{$row['instructor']}</td>
                        <td>{$row['venue']}</td>
                        <td>{$row['size']}</td>
                    </tr>";
                    $count++;
                }
            }else{
                echo"<tr>
                <td colspan='9'>no data</td>
                </tr>";
            }
            echo"</table>";
            //student
            echo"<section id='student'><h3>Student Table</h3></section>";
            echo"<table border='1'>
            <tr>
                <th>Row</th>
                <th>Userid</th>
                <th>Password</th>
                <th>Name</th>
                <th>School</th>
                <th>Edollar</th>
            </tr>";
            $count=1;
            if (count($result['student'])>0){
                foreach($result['student'] as $row){
                    echo"<tr>
                        <td>$count</td>
                        <td>{$row['userid']}</td>
                        <td>{$row['password']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['school']}</td>
                        <td>{$row['edollar']}</td>
                    </tr>";
                    $count++;
                }
            }else{
                echo"<tr>
                <td colspan='6'>no data</td>
                </tr>";
            }
            echo"</table>";
            //prerequisite
            echo"<section id='prerequisite'><h3>Prerequisite Table</h3></section>";
            echo"<table border='1'>
            <tr>
                <th>Row</th>
                <th>Course</th>
                <th>Prerequisite</th>
            </tr>";
            $count=1;
            if (count($result['prerequisite'])>0){
                foreach($result['prerequisite'] as $row){
                    echo"<tr>
                        <td>$count</td>
                        <td>{$row['course']}</td>
                        <td>{$row['prerequisite']}</td>
                    </tr>";
                    $count++;
                }
            }else{
                echo"<tr>
                <td colspan='3'>no data</td>
                </tr>";
            }
            echo"</table>";
            //bid
            echo"<section id='bid'><h3>Bid Table</h3></section>";
            echo"<table border='1'>
            <tr>
                <th>Row</th>
                <th>Userid</th>
                <th>Amount</th>
                <th>Course</th>
                <th>Section</th>
            </tr>";
            $count=1;
            if (count($result['bid'])>0){
                foreach($result['bid'] as $row){
                    echo"<tr>
                        <td>$count</td>
                        <td>{$row['userid']}</td>
                        <td>{$row['amount']}</td>
                        <td>{$row['course']}</td>
                        <td>{$row['section']}</td>
                    </tr>";
                    $count++;
                }
            }else{
                echo"<tr>
                <td colspan='5'>no data</td>
                </tr>";
            }
            echo"</table>";
            //completed-course
            echo"<section id='completedCourse'><h3>Completed Course Table</h3></section>";
            echo"<table border='1'>
            <tr>
                <th>Row</th>
                <th>Userid</th>
                <th>Course</th>
            </tr>";
            $count=1;
            if (count($result['completed-course'])>0){
                foreach($result['completed-course'] as $row){
                    echo"<tr>
                        <td>$count</td>
                        <td>{$row['userid']}</td>
                        <td>{$row['course']}</td>
                    </tr>";
                    $count++;
                }
            }else{
                echo"<tr>
                <td colspan='3'>no data</td>
                </tr>";
            }
            echo"</table>";
            //section-student
            echo"<section id='sectionStudent'><h3>Section Student Table</h3></section>";
            echo"<table border='1'>
            <tr>
                <th>Row</th>
                <th>Userid</th>
                <th>Course</th>
                <th>Section</th>
                <th>Amount</th>
            </tr>";
            $count=1;
            if (count($result['section-student'])>0){
                foreach($result['section-student'] as $row){
                    echo"<tr>
                        <td>$count</td>
                        <td>{$row['userid']}</td>
                        <td>{$row['course']}</td>
                        <td>{$row['section']}</td>
                        <td>{$row['amount']}</td>
                    </tr>";
                    $count++;
                }
            }else{
                echo"<tr>
                <td colspan='5'>no data</td>
                </tr>";
            }
            echo"</table>";
        }elseif(isset($_POST['studentSelect']) || (isset($_POST['navigation']) && $_POST['navigation']=='Show Student')){
            echo "<h3>Show Student</h3>";
            $studentDAO= new StudentDAO();
            $students=$studentDAO->RetrieveAll();
            echo"<select name='student'>
            <option disabled selected value=''> -- select an option -- </option>";
            foreach($students as $student){
                $sid=$student->getUserid();
                echo "<option value='$sid'";
                if (isset($_POST['student']) && $_POST['student']==$sid){
                    echo "selected";
                }
                echo">$sid</option>";
            }
            echo "</select>
            <input type='submit' name='studentSelect' value='Search'>";
            if (isset($_POST['student'])){
                $result=doUserDump($_POST['student']);
                
                echo "<br><br><table border='1'>
                <tr><th>Userid</th><td>{$result['userid']}</td></tr>
                <tr><th>Password</th><td>{$result['password']}</td></tr>
                <tr><th>Name</th><td>{$result['name']}</td></tr>
                <tr><th>School</th><td>{$result['school']}</td></tr>
                <tr><th>Edollar</th><td>{$result['edollar']}</td></tr>
                </table>";
                
            }
            
        }elseif(isset($_POST['sectionSelect']) || (isset($_POST['navigation']) && $_POST['navigation']=='Show Bid By Section')){
            echo "<h3>Show Bid By Section</h3>";
            $sectionDAO= new SectionDAO();
            $section=$sectionDAO->RetrieveAll();
            echo"<select name='section'>
            <option disabled selected value=''> -- select an option -- </option>";
            foreach($section as $item){
                $cid=$item->getCourseid();
                $sid=$item->getSectionid();
                
                echo "<option value='$cid $sid'";
                if (isset($_POST['section']) && $_POST['section']=="$cid $sid"){
                    echo "selected";
                }
                echo">$cid Section: $sid</option>";
            }
            echo "</select>
            <input type='submit' name='sectionSelect' value='Search'>";
            if (isset($_POST['section'])){
                $info=explode(' ',$_POST['section']);
                $result=doBidDump($info[0],$info[1]);
                if(count($result['bids'])>0){
                    echo "<br><br><table border='1'><tr><th>Row</th><th>Userid</th><th>Amount</th><th>Result</th></tr>";
                    foreach($result['bids'] as $oneBid){
                        echo"<tr><td>{$oneBid['row']}</td><td>{$oneBid['userid']}</td><td>{$oneBid['amount']}</td><td>{$oneBid['result']}</td></tr>";
                    }
                    echo"</table>";
                }else{
                    echo"<br><p>No Student bid for this Section</p>";
                }
            }
            
        }elseif(isset($_POST['sectionsSelect']) || (isset($_POST['navigation']) && $_POST['navigation']=='Show All Student Section')){
            echo "<h3>Show All Student Section</h3>";
            $sectionDAO= new SectionDAO();
            $section=$sectionDAO->RetrieveAll();
            echo"<select name='sections'>
            <option disabled selected value=''> -- select an option -- </option>";
            foreach($section as $item){
                $cid=$item->getCourseid();
                $sid=$item->getSectionid();
                
                echo "<option value='$cid $sid'";
                if (isset($_POST['sections']) && $_POST['sections']=="$cid $sid"){
                    echo "selected";
                }
                echo">$cid Section: $sid</option>";
            }
            echo "</select>
            <input type='submit' name='sectionsSelect' value='Search'>";
            if (isset($_POST['sections'])){
                $info=explode(' ',$_POST['sections']);
                $result=doSectionDump($info[0],$info[1]);
                if(count($result['students'])>0){
                    $count=1;
                    echo "<br><br><table border='1'><tr><th>Row</th><th>Userid</th><th>Amount</th></tr>";
                    foreach($result['students'] as $oneStudent){
                        echo"<tr><td>$count</td><td>{$oneStudent['userid']}</td><td>{$oneStudent['amount']}</td></tr>";
                        $count++;
                    }
                    echo"</table>";
                }else{
                    echo"<br><p>No Student in this Section</p>";
                }
            } 
               
        }elseif(isset($_POST['bidStatusSelect']) || (isset($_POST['navigation']) && $_POST['navigation']=='Show Bid Status')){  
            echo "<h3>Show Bid Status</h3>";
            $sectionDAO= new SectionDAO();
            $section=$sectionDAO->RetrieveAll();
            echo"<select name='sectionb'>
            <option disabled selected value=''> -- select an option -- </option>";
            foreach($section as $item){
                $cid=$item->getCourseid();
                $sid=$item->getSectionid();
                
                echo "<option value='$cid $sid'";
                if (isset($_POST['sectionb']) && $_POST['sectionb']=="$cid $sid"){
                    echo "selected";
                }
                echo">$cid Section: $sid</option>";
            }
            echo "</select>
            <input type='submit' name='bidStatusSelect' value='Search'>";
            if (isset($_POST['sectionb'])){
                $info=explode(' ',$_POST['sectionb']);
                $result=doBidStatus($info[0],$info[1]);
                if ($result['status']=='success'){
                    echo"<table border='1'>
                    <tr><th>vacancy</th><td>{$result['vacancy']}</td></tr>
                    <tr><th>min-bid-amount</th><td>{$result['min-bid-amount']}</td></tr>
                    <tr><th colspan='2'>students</th></tr>";
                }
                if(count($result['students'])>0){
                    $count=1;
                    echo "<tr><td colspan='2'><table border='1'><tr><th>Row</th><th>Userid</th><th>Amount</th><th>Balance</th><th>Status</th></tr>";
                    foreach($result['students'] as $oneStudent){
                        echo"<tr><td>$count</td><td>{$oneStudent['userid']}</td><td>{$oneStudent['amount']}</td><td>{$oneStudent['balance']}</td><td>{$oneStudent['status']}</td></tr>";
                    }
                    echo"</table></td></tr>";
                }else{
                    echo"<tr><td colspan='2'>No Student bid for this Section</td></th>";
                }
                echo"</table>";
            }
            
        }elseif (isset($_POST['updateBid']) || (isset($_POST['navigation']) && $_POST['navigation']=='Update bid')){
            echo "<h3>Update Bid</h3>";
            echo "Userid: <input type='text' name='userid'><br>
            Course: <input type='text' name='course'><br>
            Section: <input type='text' name='section'><br>
            Amount: <input type='text' name='amount'><br>
            <input type='submit' name='updateBid' value='Update'><br>
            ";
            if(isset($_POST['updateBid'])){
                $errors=[];
                if(isset($_POST['userid']) && strlen(trim($_POST['userid'])) == 0){
                    $errors[]='Please enter a Userid';
                }
                if(isset($_POST['course']) && strlen(trim($_POST['course'])) == 0){
                    $errors[]='Please enter a Course';
                }
                if(isset($_POST['section']) && strlen(trim($_POST['section'])) == 0){
                    $errors[]='Please enter a Section';
                }
                if(isset($_POST['amount']) && strlen(trim($_POST['amount'])) == 0){
                    $errors[]='Please enter a Amount';
                }
                if (count($errors)==0){
                    $result=doUpdateBid($_POST['userid'],$_POST['amount'],$_POST['course'],$_POST['section']);
                    if($result['status']=="error"){
                        $errors=array_merge( $errors,$result['message']);
                    }else{
                        echo "Successfully Updated";
                    }
                }
                foreach ($errors as $error) {
                    echo $error . "<br>";
                }
            }
        }elseif (isset($_POST['deleteBid']) || (isset($_POST['navigation']) && $_POST['navigation']=='Delete bid')){
            echo "<h3>Delete Bid</h3>";
            echo "Userid: <input type='text' name='userid'><br>
            Course: <input type='text' name='course'><br>
            Section: <input type='text' name='section'><br>
            <input type='submit' name='deleteBid' value='Delete Bid'><br>
            ";
            if(isset($_POST['deleteBid'])){
                $errors=[];
                if(isset($_POST['userid']) && strlen(trim($_POST['userid'])) == 0){
                    $errors[]='Please enter a Userid';
                }
                if(isset($_POST['course']) && strlen(trim($_POST['course'])) == 0){
                    $errors[]='Please enter a Course';
                }
                if(isset($_POST['section']) && strlen(trim($_POST['section'])) == 0){
                    $errors[]='Please enter a Section';
                }
                if (count($errors)==0){
                    $result=doDeleteBid($_POST['userid'],$_POST['course'],$_POST['section']);
                    if($result['status']=="error"){
                        $errors=array_merge( $errors,$result['message']);
                    }else{
                        echo "Successfully Deleted";
                    }
                }
                foreach ($errors as $error) {
                    echo $error . "<br>";
                }
            }
        }elseif (isset($_POST['dropSection']) || (isset($_POST['navigation']) && $_POST['navigation']=='Drop Section')){
            echo "<h3>Drop Section</h3>";
            echo "Userid: <input type='text' name='userid'><br>
            Course: <input type='text' name='course'><br>
            Section: <input type='text' name='section'><br>
            <input type='submit' name='dropSection' value='Drop Section'><br>
            ";
            if(isset($_POST['dropSection'])){
                $errors=[];
                if(isset($_POST['userid']) && strlen(trim($_POST['userid'])) == 0){
                    $errors[]='Please enter a Userid';
                }
                if(isset($_POST['course']) && strlen(trim($_POST['course'])) == 0){
                    $errors[]='Please enter a Course';
                }
                if(isset($_POST['section']) && strlen(trim($_POST['section'])) == 0){
                    $errors[]='Please enter a Section';
                }
                if (count($errors)==0){
                    $result=doDropSection($_POST['userid'],$_POST['course'],$_POST['section']);
                    if($result['status']=="error"){
                        $errors=array_merge( $errors,$result['message']);
                    }else{
                        echo "Successfully Dropped Section";
                    }
                }
                foreach ($errors as $error) {
                    echo $error . "<br>";
                }
            }
        }
       
        
    }
?>
</form>

<br>
<a href="logout.php">Logout</a>

</body>

</form>

</html>