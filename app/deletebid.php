<?php

//to be included files

require_once 'include/common.php';
require_once 'include/function.php';
require_once 'include/protect.php';

//retrieve student information
$student=$_SESSION['student'];
$userid = $student->getUserid(); #get userid
$password = $student->getPassword(); #get password
$name = $student->getName(); #get name
$school = $student->getSchool(); #get school
$edollar = $student->getEdollar(); #get edollar

//retrieve student bidded mods
$bidDAO = New BidDAO();
$biddedModule = $bidDAO->getBidInfo($userid); 

//getting the round ID and round status
$adminround = new adminRoundDAO();
$roundDetail = $adminround->retrieveRoundDetail();
$roundID = $roundDetail->getRoundID();
$roundstat = $roundDetail->getRoundStatus();

?>
<style>
th, td,tr {
  text-align: center;
}
</style>
<!DOCTYPE html>
<html>
<head>
<title>Drop Bid</title>
<link rel="stylesheet" type="text/css" href="css/deletebid.css">
<link rel="stylesheet" type="text/css" href="css/mainpageUI.css">
<script src="https://kit.fontawesome.com/129e7cf8b7.js" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>

<body>
    <div class="container">
    <!-- Nav Bar -->
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
                        <p>Credit Balance: <?=$edollar?></p>
                    </div>
                </div>
            </div>
            <a href="mainpage.php?token=<?php echo $_GET['token']?>" style="color: white; text-decoration: none;"><div class="navbar-left__completed">HOME <i class="fas fa-home"></i></div></a>
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
        <!-- For user to drop bid -->
            <div class="form-container">
                <div class="form-header">
                    <p>Drop Existing Bid</p>
                </div>
                <form action="deletebidprocess.php" method="GET">
                    <input type='hidden' name='token' value="<?php echo $_GET['token'];?>">
                    <div class="form-group">
                        <label for="code">Course Code: </label><br>
                        <input class="form-control" type='text' name='code' required>
                    </div>
                    <div class="form-group">
                        <label for="section">Section ID: </label><br>
                        <input class="form-control" type='text' name='section' required>
                    </div>
                    <input class="submit-btn" name="submit" type='submit'>
                </form>
            </div>
            <div class="content-container">
                <?php
                //  display errors if there is error
                if (isset($_SESSION['errors1'])) {
                    foreach ($_SESSION['errors1'] as $errors){
                        echo "<p style='color: red'>".$errors."</p>";
                        print "<br>";
                    }
                    unset ($_SESSION['errors1']);
                }
                ?>
                <?php
                if (count($biddedModule)==0){
                    // if there is no bidded module
                    echo "<h3 style='text-align:left; font-weight:bold; '>No available course</h3>";
                }else {
                    echo"<table class='content-container__table' border='1px'>
                    <tr>
                    <th>Course ID</th>
                    <th <th style='text-align:center'>Title</th>
                    <th>Section ID</th>
                    <th <th style='text-align:center'>Day</th>
                    <th>Lesson Start Time</th>
                    <th>Lesson End Time</th>
                    <th <th style='text-align:center'>Instructor</th>
                    <th>Amount</th> 
                    <th>Delete</th>
                    </tr>";
                    foreach ($biddedModule as $module){
                        $i = 1;

                        echo "<tr><td>";
                        $code = $module->getCode();
                        echo "$code</td>";
                        echo "<td>";

                        $course = $module->getCourseDetailsByCourseSection();
                        $weekday = [1=>'MON',2=>'TUE',3=>'WED',4=>'THU',5=>'FRI',6=>'SAT',7=>'SUN'];
                        //format datetime to time only
                        $lStartTime = $course->getStart();
                        $lStartTime = substr($lStartTime,0,5);
                        $lEndTime = $course->getEnd();
                        $lEndTime = substr($lEndTime,0,5);

                        echo "{$course->getTitle()}</td>
                        <td>{$module->getSection()}</td>
                        <td>{$weekday[$course->getDay()]}</td>
                        <td>$lStartTime</td>
                        <td>$lEndTime</td>
                        <td>{$course->getInstructor()}</td>
                        <td>{$module->getAmount()}</td>";
                        ?>

                        <!-- notification for student confirm the selection -->
                        <td>
                            <button id="<?= $code?>" class="trigger">Drop</button>
                            <div class="modal" id="modal<?= $code?>">
                                <div class="modal-content">
                                    <h3>Are you sure you want to drop bid?
                                    </h3>
                                    <div class="btn-wrapper">
                                        <span class="close-button">Close</span>
                                        <a class="drop-button" href="deletebidprocess.php?token=<?=$_GET['token']?>&code=<?=$code?>&section=<?=$module->getSection()?>">Confirm</a>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <?php
                        echo "</tr>";

                        $i +=1;
                    }
                }

                ?>
            </div>
  
        </div>
    </div> 

</body>
<script>

$(document).ready(function(){
$(".trigger").click(function(e){
    var id = e.target.id
    $('#modal' + id).addClass('show-modal');
});

$('.close-button').click(function(){
    $(this).parent().parent().parent().removeClass('show-modal');
});

});




</script>
</html>