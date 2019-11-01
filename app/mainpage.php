<?php
    require_once 'include/common.php';
    require_once 'include/protect.php';

    if (!isset($_SESSION['success'])){
        header('Location:login.php');
        exit;
    }
    else{
        //Retrieve student information
        if ($_SESSION['success']!='admin'){
            $studentDAO = New StudentDAO();
            $student = $studentDAO->retrieveStudent($_SESSION['success']);
            $loginID = $student->getUserid();
            $_SESSION['student'] = $student;
            $name = $student->getName();
            $school = $student->getSchool();
            $eCredit = $student->getEdollar();
            $bidDAO = New BidDAO();
            $biddedModule = $bidDAO->getBidInfo($_SESSION['success']);
            $bidresultsDAO = New StudentSectionDAO();
            $successModules=$bidresultsDAO->retrieveAllByUser($loginID);
        }else{
            $name = $_SESSION['success'];
            $school = '-';
            $eCredit = '-';
        }
    } 
?>
<style>
th, td,tr {
  text-align: center;
}
</style>
<html>
    <head>
        <title>BIOS Bidding</title>
        <link rel="stylesheet" type="text/css" href="css/mainpageUI.css">
        <script src="https://kit.fontawesome.com/129e7cf8b7.js" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="container">
            <div class="navbar-left">
                <div class="navbar-left__profile">
                    <div class="navbar-left__profile__container">
                        <div class="profile-picture">
                                <img class="profpic" src="css/profpic1.png">
                        </div>
                        <div class="profile-details">
                            <p>Welcome, <?=$name?></p>
                            <p><?=$school?></p>
                            <p>Credit Balance: <?=$eCredit?></p>
                        </div>
                    </div>
                </div>
                <a href="completed.php?token=<?php echo $_GET['token']?>" style="color: white; text-decoration: none;"><div class="navbar-left__completed">COMPLETED <i class="far fa-window-restore"></i></div></a>
                <a href="search.php?token=<?php echo $_GET['token']?>" style="color: white; text-decoration: none;"><div class="navbar-left__search">SEARCH <i class="fas fa-search"></i></div></a>
                <a href='makebid.php?token=<?php echo $_GET['token']?>' style="color: white; text-decoration: none;"><div class="navbar-left__addCourse">ADD BID <i class="far fa-calendar-plus"></i></div></a>
                <a href='editBid.php?token=<?php echo $_GET['token']?>' style="color: white; text-decoration: none;"><div class="navbar-left__editBid">EDIT BID <i class="fas fa-pen-square"></i></div></a>
                <a href='deletebid.php?token=<?php echo $_GET['token']?>' style="color: white; text-decoration: none;"><div class="navbar-left__dropCourse">DROP BID <i class="far fa-calendar-times"></i></div></a>
                <a href='dropSection.php?token=<?php echo $_GET['token']?>' style="color: white; text-decoration: none;"><div class="navbar-left__dropSection">DROP SECTION <i class="fas fa-minus-square"></i></div></a>
                <a href="logout.php" style="color: white; text-decoration: none;"><div class="navbar-left__logout">LOGOUT <i class="fas fa-sign-out-alt"></i></div></a>
                <div class="navbar-left__smuLogo">
                    <img src="css/smulogo.png">
                </div>
            </div>
            <div class="display-right">
                <?php //Current Time Table<br> ?>
                <div class="display-right-container">
                    <div class="display-right__table-dates">
                        <table class="display-right__table-dates__table">
                            <tr>
                                <th class="table-title" colspan="3">Boss Date</th>
                            </tr>
                            <tr>
                                <th style='text-align:center'>Event</th>
                                <th style='text-align:center'>Start Time</th>
                                <th style='text-align:center'>End Time</th>
                            </tr>
                            <tr>
<?php 

    $RoundStatusDAO = new adminRoundDAO();
    $roundstatus = $RoundStatusDAO->retrieveRoundDetail();

    $roundID = $roundstatus->getRoundID();
    $roundStatus = $roundstatus->getRoundStatus();
    $round1Start = $roundstatus->getR1Start();
    $round1End = $roundstatus->getR1End();
    $round2Start = $roundstatus->getR2Start();
    $round2End = $roundstatus->getR2End();


    $roundStartEndTimes = [$round1Start,$round1End,$round2Start,$round2End];

    if($round1Start != null && $round1End == null){
        $roundStartEndTimes[1] = "Ongoing";
    }
    if($round2Start != null && $round2End == null){
        $roundStartEndTimes[3] = "Ongoing";
    }
    for($i = 0; $i < 4; $i++){
        if($roundStartEndTimes[$i] == null){
            $roundStartEndTimes[$i] = "Not Started";
        }
    }


    
    $months = [1=>'JAN',2=>'FEB',3=>'MAR',4=>'APR',5=>'MAY',6=>'JUNE',7=>'JULY',8=>'AUG',9=>'SEPT',10=>'OCT',11=>'NOV',12=>'DEC'];
    if ($roundStartEndTimes[0]!='Not Started'){
        $starttime1 = $roundStartEndTimes[0];
        $starttime1 = explode(" ",$starttime1);
        $str1 ='';
        $str1 .= ($starttime1[0][8].$starttime1[0][9]." ".$months[$starttime1[0][5].$starttime1[0][6]] 
        ." ". substr($starttime1[1],0,5));
        $roundStartEndTimes[0] = $str1;
    }
    if ($roundStartEndTimes[1]=='Not Started' || $roundStartEndTimes[1]=='Ongoing'){
        $roundStartEndTimes[1] = $roundStartEndTimes[1];
    }else{
        $starttime2 = $roundStartEndTimes[1];
        $starttime2 = explode(" ",$starttime2);
        $str2 ='';
        $str2 .= ($starttime2[0][8].$starttime2[0][9]." ".$months[$starttime2[0][5].$starttime2[0][6]] 
        ." ". substr($starttime2[1],0,5));
        $roundStartEndTimes[1] = $str2;
    }
    if ($roundStartEndTimes[2]!='Not Started'){
        $starttime3 = $roundStartEndTimes[2];
        $starttime3 = explode(" ",$starttime3);
        $str3 ='';
        $str3 .= ($starttime3[0][8].$starttime3[0][9]." ".$months[$starttime3[0][5].$starttime3[0][6]] 
        ." ". substr($starttime3[1],0,5));
        $roundStartEndTimes[2] = $str3;
    }
    if ($roundStartEndTimes[3]=='Not Started' || $roundStartEndTimes[3]=='Ongoing'){
        $roundStartEndTimes[3] = $roundStartEndTimes[3];
    }else{
        $starttime4 = $roundStartEndTimes[3];
        $starttime4 = explode(" ",$starttime4);
        $str4 ='';
        $str4 .= ($starttime4[0][8].$starttime4[0][9]." ".$months[$starttime4[0][5].$starttime4[0][6]] 
        ." ". substr($starttime4[1],0,5));
        $roundStartEndTimes[3] = $str4;
    }

    echo"
                                <th>Round 1</th>
                                <td><b>$roundStartEndTimes[0]</b></td>
                                <td><b>$roundStartEndTimes[1]</b></td>
                                
                            </tr>
                            <tr>
                                <th>Round 2</th>
                                <td><b>$roundStartEndTimes[2]</b></td>
                                <td><b>$roundStartEndTimes[3]</b></td>
                            </tr>
                            </tr>";

?>
                        </table> 

                        </div>
                        
                        <?php
if($roundID == 2 && $roundStatus != "Started"){

?>

                    <div class="display-right__table-cart">
                        <table class="display-right__table-cart__table">
                            <tr>
                                <th class="table-title" colspan="9">Round <?php $roundID?> Bidding Results</th>
                            </tr>
                            <tr>
                                <th colspan="8">
                                <?php 

                                $bidDatabase = new BidProcessorDAO();
                                $bidsByUser = $bidDatabase->getBidsByID($loginID);
                                echo "<tr align='center'>
                                <th>Code</th>
                                <th style='text-align:center'>Title</th>
                                <th>Section</th>
                                <th style='text-align:center'>Day</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th style='text-align:center'>Instructor</th>
                                <th>Amount</th>
                                <th>Bid Result</th>";
                                if ($roundStatus == "Finished"){
                                    //should the round be started then they start to show the min bid?
                                    // echo "<th>Min Bid</th>";
                                };
                                echo "</tr>";

                            foreach ($bidsByUser as $bid){
                                
                                echo "<tr><td>";
                                    $code = $bid[2];
                                    $bidAmt = $bid[1];
                                    $bidSection = $bid[3];
                                    if($roundID == 2 && $roundStatus == "Not Started"){
                                        $biddedRound = 1;
                                    }elseif($roundID==2){
                                        $biddedRound = 2;
                                    }
                                    
                                    $bidFromWhichRound = $bid[5];
                                    if($biddedRound == $bidFromWhichRound){
                                    echo "$code</td>";
                                    echo "<td>";
                                    $coursesDAO = new CourseDAO();
                                    $course = $coursesDAO->retrieveAllCourseDetail($code,$bidSection);

                                    $weekday = [1=>'MON',2=>'TUE',3=>'WED',4=>'THU',5=>'FRI',6=>'SAT',7=>'SUN'];
                                    $lStartTime = $course[0]->getStart();
                                    $lStartTime = substr($lStartTime,0,5);
                                    $lEndTime = $course[0]->getEnd();
                                    $lEndTime = substr($lEndTime,0,5);

                                    $bidresult = $bid[4];

                                    
                                echo "{$course[0]->getTitle()}</td>
                                    <td>{$bidSection}</td>
                                    <td>{$weekday[$course[0]->getDay()]}</td>
                                    <td>$lStartTime</td>
                                    <td>$lEndTime</td>
                                    <td>{$course[0]->getInstructor()}</td>
                                    <td>{$bidAmt}</td>
                                    <td>{$bidresult}</td>";
                                    if ($roundStatus== "Finished"){
                                        //should the round be started then they start to show the min bid?
                                        
                                        // $minbid = CheckMinBid($course->getCourseid(),$course->getSectionid());
                                        // echo "<td>$minbid</td>";
                                    }
                                }
                            }
                        }
                       
                                ?>
                                </th></tr>
                            <tr></tr>
                        </table>
                    </div>


                    <div class="display-right__table-cart">
                        <table class="display-right__table-cart__table">
                            <tr>
                                <th class="table-title" colspan="9">Bidding Cart</th>
                            </tr>
                            <tr>
                                <th colspan="9">
                                <?php echo "
                                    Round {$roundID} is {$roundStatus}" ?>
                                </th></tr>

                            <tr></tr>
                                    <?php
                                        //getting the round ID and roundstat
                                        //print ($roundID);
                                        if (isset($biddedModule)){
                                            if (count($biddedModule)==0){
                                                echo "<tr>
                                                        <td>No Existing Bid</td>
                                                    </tr>";
                                            }
                                            else{
                                                echo "<tr>
                                                    <th>Code</th>
                                                    <th style='text-align:center'>Title</th>
                                                    <th>Section</th>
                                                    <th style='text-align:center'>Day</th>
                                                    <th>Start Time</th>
                                                    <th>End Time</th>
                                                    <th style='text-align:center'>Instructor</th>
                                                    <th>Amount</th>
                                                    <th>Bid Result</th>";
                                                    if ($roundID == 2 && $roundStatus != "Not Started"){
                                                        //should the round be started then they start to show the min bid?
                                                        echo "<th>Min Bid Required</th>";
                                                    };
                                                    echo "</tr>";

                                                foreach ($biddedModule as $module){
                                                    
                                                    echo "<tr><td>";
                                                    $code = $module->getCode();
                                                    $bidAmt = $module->getAmount();
                                                    $bidSection = $module->getSection();
                                                    echo "$code</td>";
                                                    echo "<td>";
                                                    $course = $module->getCourseDetailsByCourseSection();

                                                    $weekday = [1=>'MON',2=>'TUE',3=>'WED',4=>'THU',5=>'FRI',6=>'SAT',7=>'SUN'];
                                                    $lStartTime = $course->getStart();
                                                    $lStartTime = substr($lStartTime,0,5);
                                                    $lEndTime = $course->getEnd();
                                                    $lEndTime = substr($lEndTime,0,5);

                                                    $bidresult = $bidresultsDAO->getBidStatus($loginID,$bidAmt,$code,$bidSection);
                                                   
                                                    //retrieve minimum bid                  
                                                    $minbid = CheckMinBid($course->getCourseid(),$course->getSectionid(),FALSE);
                                                    $SectionDAO = new SectionDAO();
                                                    $currentMinBid = $SectionDAO->viewMinBid($course->getCourseid(),$course->getSectionid());

                                                    echo "{$course->getTitle()}</td>
                                                        <td>{$module->getSection()}</td>
                                                        <td>{$weekday[$course->getDay()]}</td>
                                                        <td>$lStartTime</td>
                                                        <td>$lEndTime</td>
                                                        <td>{$course->getInstructor()}</td>
                                                        <td>{$module->getAmount()}</td>";
                                                        if($roundID == 2){
                                                            if ($roundStatus != "Finished"){
                                                                if($module->getAmount() >= $minbid){
                                                                    echo "<td>Successful</td>";
                                                                }else{
                                                                    echo "<td>Unsuccessful. Bid too low.</td>";
                                                                }
                                                            }else{
                                                                if (!CheckCourseEnrolled($loginID,$course->getCourseid())){
                                                                    echo "<td>Unsuccessful. Bid too low.</td>";
                                                                }else{
                                                                    echo "<td>Successful</td>";
                                                                }
                                                            }
                                                            
                                                        }else{
                                                            echo "<td>Pending</td>";
                                                        }
                                                        if ($roundID==2 && $roundStatus != "Not Started"){
                                                            
                                                            echo "<td>$currentMinBid</td>";
                                                        }; 
                                                        
                                                    echo "</tr>";
                                                }
                                            }
                                        }
                                        else{
                                            echo "<tr>
                                                        <td>No Existing Bid</td>
                                                    </tr>";
                                        }
                                    ?>
                            </table>
                    </div>
                    <div class="display-right__table-enrolment">
                        <table class="display-right__table-enrolment__table">
                            <tr>
                                <th class="table-title">Enrolment</th>
                            </tr>
                            <tr>
                                <td>
                                <?php
                                    if (count($successModules)>0){
                                        echo"<table border='1'>
                                        <tr>
                                            <th>Code</th>
                                            <th style='text-align:center'>Title</th>
                                            <th>Section</th>
                                            <th style='text-align:center'>Day</th>
                                            <th>Lesson Start Time</th>
                                            <th>Lesson End Time</th>
                                            <th style='text-align:center'>Instructor</th>
                                            <th>Amount</th>
                                        </tr>";
                                        foreach ($successModules as $module){
                                            $courseDAO= new CourseDAO();
                                            $course=$courseDAO->retrieveAllCourseDetail($module[1],$module[2])[0];
                                            $weekday = [1=>'MON',2=>'TUE',3=>'WED',4=>'THU',5=>'FRI',6=>'SAT',7=>'SUN'];
                                            $lStartTime = $course->getStart();
                                            $lStartTime = substr($lStartTime,0,5);
                                            $lEndTime = $course->getEnd();
                                            $lEndTime = substr($lEndTime,0,5);
                                            echo "<tr>
                                            <td>{$module[1]}</td>
                                            <td>{$course->getTitle()}</td>
                                            <td>{$module[2]}</td>
                                            <td>{$weekday[$course->getDay()]}</td>
                                            <td>$lStartTime</td>
                                            <td>$lEndTime</td>
                                            <td>{$course->getInstructor()}</td>
                                            <td>{$module[0]}</td>
                                        </tr>";
                                        }
                                        echo"</table>";
                                    }
                                    else{
                                        echo "No Enrolled Course";
                                    }
                                ?>
                                </td>
                            </tr>
                            <tr>
<?php

// $EnrolledCourses = $bidresultsDAO->getSuccessfulBidsByID($loginID);
// foreach($EnrolledCourses as $course){
//     var_dump($course);
// }

?>
                            </tr>
                        </table>
                        
                        </table>
                        
                        <table class="display-right__table-timetable__table" border='1px black'>
                            <tr>
                                <th colspan='4' class="table-title">Timetable</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th>08:30 AM - 11:45 AM</th>
                                <th>12:00 PM - 3:15 PM</th>
                                <th>3:30 PM - 6:45PM</th>
                            </tr>

                            <?php
                                $days = [1, 2, 3, 4, 5];
                                $times = ['08:30', '12:00',  '03:00'];
                                    if (isset($biddedModule)){
                                        $timetable=[1=>['','',''],2=>['','',''],3=>['','',''],4=>['','',''],5=>['','','']];
                                        $status = [];
                                        if (count($biddedModule)!=0){
                                            foreach ($biddedModule as $module){
                                                $course=$module->getCourseDetailsByCourseSection();
                                                $day_no = $course->getDay();
                                                $startTime = $course->getStart();
                                                //retrieve minimum bid                  
                                                $minbid = CheckMinBid($course->getCourseid(),$course->getSectionid(),FALSE);
                                                $SectionDAO = new SectionDAO();
                                                $currentMinBid = $SectionDAO->viewMinBid($course->getCourseid(),$course->getSectionid());
                                                if ($startTime=="08:30:00"){
                                                    $timetable[$day_no][0]=$course->getTitle();
                                                }
                                                if($startTime=='12:00:00') {
                                                    $timetable[$day_no][1] = $course->getTitle();
                                                }
                                                if($startTime=='15:30:00') {
                                                    $timetable[$day_no][2] = $course->getTitle();
                                                }
                                                if($roundID == 2){
                                                    if ($roundStatus != "Finished"){
                                                        if($module->getAmount() >= $minbid){
                                                            // echo "<td>Successful</td>";
                                                            $status[$course->getTitle()] = 'successful';
                                                        }
        
                                                    }else{
                                                        if (CheckCourseEnrolled($loginID,$course->getCourseid())){
                                                            // echo "<td>Unsuccessful. Bid too low.</td>";
                                                            $status[$course->getTitle()] = 'successful';
                                                        }
                                                    }
                                                    
                                                }else{
                                                    // echo "<td>Pending</td>";
                                                    $status[$course->getTitle()] = 'pending';
                                                }
        
                                        }
                                    }
                                    // var_dump($status);
                                }
                                if (count($successModules)>0){
                                    foreach ($successModules as $module){
                                        $courseDAO= new CourseDAO();
                                        $course=$courseDAO->retrieveAllCourseDetail($module[1],$module[2])[0];
                                        $title = $course->getTitle();
                                        $day_no = $course->getDay();
                                        $startTime = $course->getStart();
                                        if ($startTime=="08:30:00"){
                                            $timetable[$day_no][0]=$course->getTitle();
                                        }
                                        if($startTime=='12:00:00') {
                                            $timetable[$day_no][1] = $course->getTitle();
                                        }
                                        if($startTime=='15:30:00') {
                                            $timetable[$day_no][2] = $course->getTitle();
                                        }
                                        $status[$title] = 'successful';
                                    }
                                }
                                // var_dump($timetable);
                            
                                foreach($days as $day) {
                                    if($day == 1) {
                                        echo "<tr><td><b>MONDAY</b></td>";
                                    }
                                    elseif($day==2) {
                                        echo "<tr><td><b>TUESDAY</b></td>";
                                    }
                                    elseif($day==3) {
                                        echo "<tr><td><b>WEDNESDAY</b></td>";
                                    }
                                    elseif($day==4) {
                                        echo "<tr><td><b>THURSDAY</b></td>";
                                    }
                                    elseif($day==5) {
                                        echo "<tr><td><b>FRIDAY</b></td>";
                                    }
                                    for($i=0; $i<=2;$i++){
                                        $title = $timetable[$day][$i];
                                        if(isset($status[$title]) && $status[$title] == 'successful') {
                                            echo "<td bgcolor='#6BFF32'>". $timetable[$day][$i] . "</td>";
                                        }
                                        elseif(isset($status[$title]) && $status[$title] == 'pending') { 
                                        echo "<td bgcolor='#FFF233'>". $timetable[$day][$i] . "</td>";
                                        }
                                        else {
                                            echo "<td></td>";
                                        }
                                    }
                                    echo "</tr>";
                                }
                                
                                echo "<tr><td colspan='4' style='padding: 20px'>Legend: <i class='fas fa-circle' style='color: #6BFF32'></i> Successful <i class='fas fa-circle' style='color: #FFF233'></i> Pending</td></tr>";
                    
                                // var_dump($timetable);
                                
                            ?>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
