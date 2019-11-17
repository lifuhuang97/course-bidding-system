<?php

//to be included files

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
require_once 'include/protect.php';
?>


<html>
<!-- HTML Stuff for admin main page-->
<head>
    <title>BOSS Bidding System</title>
    <link rel="stylesheet" type="text/css" href="css/adminUI.css">
    <script src="https://kit.fontawesome.com/129e7cf8b7.js" crossorigin="anonymous"></script>
    <style> 
        table, th, td {
            text-align:center; 
        } 
    </style> 
</head>

<body>
    <!-- UI -->
    <div class="container">
        <div class="navbar-left">
            <div class="navbar-left_profile">
                <div class="navbar-left__profile__container">
                    <div class="profile-picture">
                        <a href="adminMainPage.php?token=<?php echo $_GET['token']?>">
                            <img class="profpic" src="css/profpic1.png">
                        </a>
                    </div>
                    <div class="profile-details">
                        <p>Welcome, Admin!</p>
                    </div>
                    <a href="bootstrap.php?token=<?php echo $_GET['token']?>" style="color: white; text-decoration: none;"><div class="navbar-left__logout">BOOTSTRAP <i class="fas fa-sign-out-alt"></i></div></a>
                    <a href="logout.php" style="color: white; text-decoration: none;"><div class="navbar-left__logout">LOGOUT <i class="fas fa-sign-out-alt"></i></div></a>
                    <div class="navbar-left__smuLogo">
                    <img src="css/smulogo.png">
                </div>
                </div>
            </div>
        </div>
        
        <div class="display-right">
            <div class="display-right-bidSystem">
                
                <?php
                // Get admin round information
                $adminRoundDAO = new adminRoundDAO();
                $round = $adminRoundDAO->RetrieveRoundDetail();
                $roundNo = $round->getRoundID();
                $roundStatus = $round->getRoundStatus();
                ?>

                <div class="form-container">
                    <div class="form-header">
                        <p>Bid System Status</p>
                    </div>
                    <form action="processAdminCommands.php?token=<?php echo "{$_GET['token']};"?>" method="post">

                        <?php
                        //display current round & status
                        echo "<table>
                        <tr>
                        <th colspan='2'>Round: {$roundNo}</th>
                        <th colspan='2'>Status: {$roundStatus}</th>
                        </tr>";
                        ?>

                        <?php
                        // disables buttons according to round status
                        $disableButton = "disabled value='true'";
                        $value = 'Start Round';
                        if ($roundStatus == "Started"){
                            $startStatus = $disableButton;
                            $clearStatus = '';
                        }else{
                            if ($roundNo == 1){
                                $value = 'Bootstrap & Start Round';
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
                        <td colspan='2'><input class='form-btn' type='submit' name='submit' value='$value' $startStatus></td>
                        <td colspan='2'><input class='form-btn' type='submit' name='submit' value='Clear Round' $clearStatus></td>
                        </tr>
                        </form>";
                        ?>
<tr>
<td colspan='4'><input class='form-btn' type='submit' name='submit' value='Reset Round'></td>
</tr>
</table>
</div>
</div>

<div class="display-right-bidInfo">
    <?php
    /** Display bid results after round ends */
    if (!($roundNo==1 && $roundStatus=='Not Started') && $roundStatus!='Finished'){
        $currentBidsDAO = new BidDAO();
        $allBids = $currentBidsDAO->RetrieveAll();
        echo "<table><tr><th colspan = 5 class='table-title'>";
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
                    if ($roundStatus!='Started'){
                        if(CheckCourseEnrolled($bidID,$bidCourse)){
                            $result="success";
                        }else{
                            $result="fail";
                        }
                    }else{
                        $minbid=CheckMinBid($bidCourse, $bidSect,FALSE);
                        if ($bidAmt>=$minbid){
                            $result="success";
                        }else{
                            $result="fail";
                        }
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


</div>
<br>
<br>
<br>
<div class="display-right-allInfo">
<!-- Additional functionality: display additional information to admin & allow admin to update/delete bid or drop selection for a user-->
    <form action="adminMainPage.php?token=<?php echo "{$_GET['token']};"?>" method="post">
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
                <tr><td><a href='adminMainPage.php?token={$_GET['token']}#course'>Course Table</a></td></tr>
                <tr><td><a href='adminMainPage.php?token={$_GET['token']}#section'>Section Table</a></td></tr>
                <tr><td><a href='adminMainPage.php?token={$_GET['token']}#student'>Student Table</a></td></tr>
                <tr><td><a href='adminMainPage.php?token={$_GET['token']}#prerequisite '>Prerequisite Table</a></td></tr>
                <tr><td><a href='adminMainPage.php?token={$_GET['token']}#bid'>Bid Table</a></td></tr>
                <tr><td><a href='adminMainPage.php?token={$_GET['token']}#completedCourse'>Completed Course Table</a></td></tr>
                <tr><td><a href='adminMainPage.php?token={$_GET['token']}#sectionStudent'>Section Student Table</a></td></tr>
                </table>";
            //course details
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
            //section information
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
            //students information
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
            //prerequisites information
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
            //bid information
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
            //completed-courses information
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
            //Enrolled students information
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
                // All students' information
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
                // All bids by section information
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
                // All enrolled students information
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
                // Bid status information
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
                            $count++;
                        }
                        echo"</table></td></tr>";
                    }else{
                        echo"<tr><td colspan='2'>No Student bid for this Section</td></th>";
                    }
                    echo"</table>";
                }

            }elseif (isset($_POST['updateBid']) || (isset($_POST['navigation']) && $_POST['navigation']=='Update bid')){    
                // Update a student's bid
                echo "<h3>Update Bid</h3>
                <table border='0' class='bid-table'>
                <tr><td>Userid:</td><td><input type='text' name='userid'></td></tr>
                <tr><td>Course:</td><td><input type='text' name='course'></td></tr>
                <tr><td>Section:</td><td><input type='text' name='section'></td></tr>
                <tr><td>Amount:</td><td><input type='text' name='amount'></td></tr>
                <tr></tr>
                <tr><td></td><td align='right'><input type='submit' name='updateBid' value='Update' class='table-button'></td></tr>
                </table>
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
                    echo "<br>";
                    foreach ($errors as $error) {
                        echo "<h4 style='color: red; margin:5px'>". $error . "</h4>";
                    }
                }
            }elseif (isset($_POST['deleteBid']) || (isset($_POST['navigation']) && $_POST['navigation']=='Delete bid')){
                // Delete a student's bid
                echo "<h3>Delete Bid</h3>
                <table border='0' class='bid-table'>
                <tr><td>Userid:</td><td><input type='text' name='userid'></td></tr>
                <tr><td>Course:</td><td><input type='text' name='course'></td></tr>
                <tr><td>Section:</td><td><input type='text' name='section'></td></tr>
                <tr></tr>
                <tr><td></td><td align='right'>
                <input type='submit' name='deleteBid' value='Delete Bid' class='table-button'></td></tr>
                </table>
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
                    echo "<br>";
                    foreach ($errors as $error) {
                        echo "<h4 style='color: red; margin:5px'>". $error . "</h4>";
                    }
                }
            }elseif (isset($_POST['dropSection']) || (isset($_POST['navigation']) && $_POST['navigation']=='Drop Section')){
                // Drop a student's enrolled section
                echo "<h3>Drop Section</h3>
                <table border='0' class='bid-table'>
                <tr><td>Userid:</td><td><input type='text' name='userid'></td></tr>
                <tr><td>Course:</td><td><input type='text' name='course'></td></tr>
                <tr><td>Section:</td><td><input type='text' name='section'></td></tr>
                <tr></tr>
                <tr><td></td><td align='right'>
                <input type='submit' name='dropSection' value='Drop Section' class='table-button'></td></tr>
                </table>
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
                    echo "<br>";
                    foreach ($errors as $error) {
                        echo "<h4 style='color: red; margin:5px'>". $error . "</h4>";
                    }
                }
            }


        }
        ?>
    </form>
</div>
</div>
</div>
<br><br><br>
</body>
</form>
</html>