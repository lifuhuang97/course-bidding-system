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
            $_SESSION['student'] = $student;
            $name = $student->getName();
            $school = $student->getSchool();
            $eCredit = $student->getEdollar();
            $bidDAO = New BidDAO();
            $biddedModule = $bidDAO->getBidInfo($_SESSION['success']);
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
                <div class="navbar-left__completed">COMPLETED</div>
                <div class="navbar-left__addCourse">ADD COURSE</div>
                <div class="navbar-left__editBid">EDIT BIDS</div>
                <div class="navbar-left__dropCourse">DROP COURSE</div>
                <a href="logout.php" style="color: white; text-decoration: none;"><div class="navbar-left__logout">LOGOUT</div></a>
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
                                <th>Round 2A Window 1</th>
                                <td>26-Aug-19 17:00</td>
                                <td>28-Aug-19 10:00</td>
                            </tr>
                            <tr>
                                <th>Round 2A Window 2</th>
                                <td>28-Aug-19 17:00</td>
                                <td>30-Aug-19 10:00</td>
                            </tr>
                            <tr>
                                <th>Round 2A Window 3</th>
                                <td>30-Aug-19 17:00</td>
                                <td>02-Sep-19 10:00</td>
                                </tr>
                            </tr>
                        </table> 
                    </div>
                    <div class="display-right__table-cart">
                        <table>
                            <tr>
                                <th class="table-title" colspan="8">Bidding Cart</th>
                            </tr>
                            <tr>
                                <th colspan="8">
                                    Round 2A Window 1 is open from
                                    26-Aug-2019 17:00 to 28-Aug-2019 10:00 
                                </th>
                            </tr>
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
                                                    </tr>";
                                                foreach ($biddedModule as $module){
                                                    echo "<tr><td>";
                                                    $code = $module->getCode();
                                                    echo "$code</td>";
                                                    echo "<td>";
                                                    $course = $module->getCourseDetailsByCourseSection();
                                                    echo "{$course->getTitle()}</td>
                                                        <td>{$module->getSection()}</td>
                                                        <td>{$course->getDay()}</td>
                                                        <td>{$course->getStart()}</td>
                                                        <td>{$course->getEnd()}</td>
                                                        <td>{$course->getInstructor()}</td>
                                                        <td>{$module->getAmount()}</td>";
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
                                <td><a href='makebid.php?token=<?php echo $_GET['token']?>'>Make a Bid</a></td>
                                <td><a href='editBid.php?token=<?php echo $_GET['token']?>'>Edit a Bid</a></td>
                                <td><a href='deletebid.php'>Cancel/Drop Bid</a></td>          
                            </tr>
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