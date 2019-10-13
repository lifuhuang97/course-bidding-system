<?php
    require_once 'include/common.php';
    require_once 'include/protect.php';

    if (!isset($_SESSION['success'])){
        header('Location.php');
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
                            <img class="profpic" src="css/profpic1.png">
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

    var_dump($roundStartEndTimes);

    echo"
                                <th>Round 1</th>
                                <td>$roundStartEndTimes[0]</td>
                                <td>$roundStartEndTimes[1]</td>
                            </tr>
                            <tr>
                                <th>Round 2A Window 2</th>
                                <td>$roundStartEndTimes[2]</td>
                                <td>$roundStartEndTimes[3]</td>
                            </tr>
                            </tr>";

?>
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
                                                    <th>Bid Status</th>
                                                    </tr>";
                                                foreach ($biddedModule as $module){
                                                    
                                                    echo "<tr><td>";
                                                    $code = $module->getCode();
                                                    $bidAmt = $module->getAmount();
                                                    $bidSection = $module->getSection();
                                                    echo "$code</td>";
                                                    echo "<td>";
                                                    $course = $module->getCourseDetailsByCourseSection();
                                                    $bidresult = $bidresultsDAO->getBidStatus($loginID,$bidAmt,$code,$bidSection);
                                                   
                                                    echo "{$course->getTitle()}</td>
                                                        <td>{$module->getSection()}</td>
                                                        <td>{$course->getDay()}</td>
                                                        <td>{$course->getStart()}</td>
                                                        <td>{$course->getEnd()}</td>
                                                        <td>{$course->getInstructor()}</td>
                                                        <td>{$module->getAmount()}</td>";
                                                        if(!isempty($bidresult)){echo "
                                                        <td>{$bidresult[0]}</td>";
                                                        }else{
                                                            echo "<td>Pending</td>";
                                                        }
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
                                <td colspan='8' align='center'><a href='pastResult.php'>View Past Bidding Result</a></td>
                            </tr>
                        </table>
                    </div>
                    <div class="display-right__table-enrolment">
                        <table>
                            <tr>
                                <th class="table-title">Enrolment</th>
                            </tr>
                            <tr>
                                <td>Enrolled TimeTable</td>
                            </tr>
                            <tr>
                                <td><a href='dropSection.php'>Drop a section</a></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
