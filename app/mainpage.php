<?php
    require_once 'include/common.php';

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
    <body>
        Welcome, <?=$name?><br>
        School: <?=$school?><br>
        Your Credit Balance: <?=$eCredit?><br>
        <?php //Current Time Table<br> ?>
        <table border = 1px>
            <tr>
                <th>Boss Date</th>
            </tr>
            <tr>
                <td>
                    <table border = 1px>
                        <tr>
                            <th>Event</th>
                            <th>Start Time</th>
                            <th>End TIme</th>
                        </tr>
                        <tr>
                            <th>Round 2A Window 1</th>
                            <th>26-Aug-19 17:00</th>
                            <th>28-Aug-19 10:00</th>
                        </tr>
                        <tr>
                            <th>Round 2A Window 2</th>
                            <th>28-Aug-19 17:00</th>
                            <th>30-Aug-19 10:00</th>
                        </tr>
                        <tr>
                            <th>Round 2A Window 3</th>
                            <th>30-Aug-19 17:00</th>
                            <th>02-Sep-19 10:00</th>
                        </tr>
                    </table>
                </td>
            </tr>
        </table> 
<br><hr><br>
        <table border = 1px>
        <tr>
            <th colspan = '3'>Bidding Cart<br>
            Round 2A Window 1 is open from
            26-Aug-2019 17:00 to 28-Aug-2019 10:00
            </th>
        </tr>
        <tr>
            <td colspan = '3'>
                <?php
                    if (isset($biddedModule)){
                        if (count($biddedModule)==0){
                            echo "No Existing Bid";
                        }
                        else{
                            echo "<table border = 1px>
                            <tr>
                                <th>Code</th>
                                <th>Title</th>
                                <th>Section</th>
                                <th>Day</th>
                                <th>Lesson Start Time</th>
                                <th>Lesson End Time</th>
                                <th>Instructor</th>
                                <th>Amount</th>";
                            echo "</tr>";
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
                            echo "</table>";
                        }
                    }
                    else{
                        echo "No Existing Bid";
                    }
                ?>
            </td>
        </tr>
        <tr>
            <td align='center'><a href='makebid.php'>Make a Bid</a></td>
            <td align='center'><a href='editbid.php'>Edit a Bid</a></td>
            <td align='center'><a href='deletebid.php'>Cancel/Drop Bid</a></td>          
        </tr>
        <tr>
            <td colspan='3' align='center'><a href='pastResult.php'>View Past Bidding Result</a></td>
        </tr>
        </table>
<br><hr><br>
        <table border=1px>
            <tr>
                <th>Enrolment</th>
            </tr>
            <tr>
                <td>Enrolled TimeTable</td>
            </tr>
            <tr>
                <td><a href='dropSection.php'>Drop a section</a></td>
            </tr>
        </table>
<br><hr><br>
        <a href="logout.php">Logout</a>
    </body>
</html>