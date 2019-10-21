<?php
    require_once 'include/dump.php';
    require_once 'include/user-dump.php';
    require_once 'include/bid-dump.php';
    require_once 'include/section-dump.php';
    require_once 'include/common.php';
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
    }elseif($roundNumber == 2 && $roundStatus == "Finished"){
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

include 'processRounds.php';
 
?>
<br>
<form action="adminMainPage.php" method="post">
<input type='submit' name='navigation' value='Show All Data'>
<input type='submit' name='navigation' value='Show Student'>
<input type='submit' name='navigation' value='Show Bid By Section'>
<input type='submit' name='navigation' value='Show All Student Section'>
<?php
    if (isset($_POST['navigation']) || isset($_POST['studentSelect']) || isset($_POST['sectionSelect']) || isset($_POST['sectionsSelect'])){
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
            foreach($result['prerequisite'] as $row){
                echo"<tr>
                    <td>$count</td>
                    <td>{$row['course']}</td>
                    <td>{$row['prerequisite']}</td>
                </tr>";
                $count++;
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
            foreach($result['completed-course'] as $row){
                echo"<tr>
                    <td>$count</td>
                    <td>{$row['userid']}</td>
                    <td>{$row['course']}</td>
                </tr>";
                $count++;
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
                // var_dump($result);
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
            
        }
       
        
    }
?>
</form>

<br>
<a href="logout.php">Logout</a>

</body>

</form>

</html>