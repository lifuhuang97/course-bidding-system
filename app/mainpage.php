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
<html>
    <head>
        <title>BOSS Bidding</title>
        <link rel="stylesheet" type="text/css" href="css/mainpageUI.css">
        <script src="https://kit.fontawesome.com/129e7cf8b7.js" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="container">
            <div class="navbar-left">
                <div class="navbar-left__profile">
                    <div class="navbar-left__profile__container">
                        <div class="profile-picture">
                        <a href="mainpage.php?token=<?php echo $_GET['token']?>">
                        <img class="profpic" src="css/profpic1.png">
                        </a>
                            
                        </div>
                        <div class="profile-details">
                            <p>Welcome, <?=$name?></p>
                            <p><?=$school?></p>
                            <p>Credit Balance: <?=$eCredit?></p>
                        </div>
                    </div>
                </div>
                <div class="navbar-left__completed">COMPLETED <i class="far fa-window-restore"></i></div>
                <a href='makebid.php?token=<?php echo $_GET['token']?>' style="color: white; text-decoration: none;"><div class="navbar-left__addCourse">ADD BID <i class="far fa-calendar-plus"></i></div></a>
                <a href='editBid.php?token=<?php echo $_GET['token']?>' style="color: white; text-decoration: none;"><div class="navbar-left__editBid">EDIT BID <i class="fas fa-pen-square"></i></div></a>
                <a href='deletebid.php?token=<?php echo $_GET['token']?>' style="color: white; text-decoration: none;"><div class="navbar-left__dropCourse">DROP BID <i class="far fa-calendar-times"></i></div></a>
                <a href="logout.php" style="color: white; text-decoration: none;"><div class="navbar-left__logout">LOGOUT <i class="fas fa-sign-out-alt"></i></div></a>
                <div class="navbar-left__smuLogo">
                    <img src="css/smulogo.png">
                </div>
            </div>
            <div class="display-right">
                <?php //Current Time Table<br> ?>
                <div class="display-right-container">
                    <div class="display-right__table-dates">
                        <table>
                            <tr>
                                <th class="table-title" colspan="3">Boss Date</th>
                            </tr>
                            <tr>
                                <th>Event</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                            </tr>
                            <tr>
<?php 

    $RoundStatusDAO = new adminRoundDAO();
    $roundstatus = $RoundStatusDAO->RetrieveRoundDetail();

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


    echo"
                                <th>Round 1</th>
                                <td>$roundStartEndTimes[0]</td>
                                <td>$roundStartEndTimes[1]</td>
                            </tr>
                            <tr>
                                <th>Round 2</th>
                                <td>$roundStartEndTimes[2]</td>
                                <td>$roundStartEndTimes[3]</td>
                            </tr>
                            </tr>";

?>
                        </table> 

                        </div>
                        
                        <?php
if($roundID == 2 && $roundStatus != "Started"){

?>

                    <div class="display-right__table-cart">
                        <table>
                            <tr>
                                <th class="table-title" colspan="8">Round <?php $roundID?> Bidding Results</th>
                            </tr>
                            <tr>
                                <th colspan="8">
                                <?php 

                                $bidDatabase = new BidProcessorDAO();
                                $bidsByUser = $bidDatabase->getBidsByID($loginID);
                                echo "<tr align='center'>
                                <th>Code</th>
                                <th>Title</th>
                                <th>Section</th>
                                <th>Day</th>
                                <th>Lesson Start Time</th>
                                <th>Lesson End Time</th>
                                <th>Instructor</th>
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
                                    $course = $coursesDAO->RetrieveAllCourseDetail($code,$bidSection);
                                    $bidresult = $bid[4];

                                    
                                echo "{$course[0]->getTitle()}</td>
                                    <td>{$bidSection}</td>
                                    <td>{$course[0]->getDay()}</td>
                                    <td>{$course[0]->getStart()}</td>
                                    <td>{$course[0]->getEnd()}</td>
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
                        <table>
                            <tr>
                                <th class="table-title" colspan="8">Bidding Cart</th>
                            </tr>
                            <tr>
                                <th colspan="8">
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
                                                    <th>Title</th>
                                                    <th>Section</th>
                                                    <th>Day</th>
                                                    <th>Lesson Start Time</th>
                                                    <th>Lesson End Time</th>
                                                    <th>Instructor</th>
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
                                                    $bidresult = $bidresultsDAO->getBidStatus($loginID,$bidAmt,$code,$bidSection);
                                                   
                                                    //retrieve minimum bid                  
                                                    $minbid = CheckMinBid($course->getCourseid(),$course->getSectionid(),FALSE);
                                                    $SectionDAO = new SectionDAO();
                                                    $currentMinBid = $SectionDAO->viewMinBid($course->getCourseid(),$course->getSectionid());

                                                    echo "{$course->getTitle()}</td>
                                                        <td>{$module->getSection()}</td>
                                                        <td>{$course->getDay()}</td>
                                                        <td>{$course->getStart()}</td>
                                                        <td>{$course->getEnd()}</td>
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
                            <tr>
                                <td colspan='8' align='center'><a href='search.php?token=<?php echo $_GET['token']?>'>Search</a></td>
                                <!-- <td colspan='8' align='center'><a href='pastResult.php'>View Past Bidding Result</a></td> -->
                            </tr>
                        </table>
                    </div>
                    <div class="display-right__table-enrolment">
                        <table>
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
                                            <th>Title</th>
                                            <th>Section</th>
                                            <th>Day</th>
                                            <th>Lesson Start Time</th>
                                            <th>Lesson End Time</th>
                                            <th>Instructor</th>
                                            <th>Amount</th>
                                        </tr>";
                                        foreach ($successModules as $module){
                                            $courseDAO= new CourseDAO();
                                            $course=$courseDAO->RetrieveAllCourseDetail($module[1],$module[2])[0];
                                            echo "<tr>
                                            <td>{$module[1]}</td>
                                            <td>{$course->getTitle()}</td>
                                            <td>{$module[2]}</td>
                                            <td>{$course->getDay()}</td>
                                            <td>{$course->getStart()}</td>
                                            <td>{$course->getEnd()}</td>
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
                                <td><a href='dropSection.php?token=<?php echo $_GET['token']?>'>Drop a section</a></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
