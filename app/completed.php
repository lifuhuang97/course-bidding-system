<?php

//Page shows students' completed courses

//to be included files
require_once 'include/common.php';
require_once 'include/protect.php';
require_once 'include/function.php';

//retrieve student details
$studentDAO = New StudentDAO();
$student = $studentDAO->retrieveStudent($_SESSION['success']);
$loginID = $student->getUserid();
$_SESSION['student'] = $student;
$name = $student->getName();
$school = $student->getSchool();
$eCredit = $student->getEdollar();

//retrieve student's completed course
$completedDAO = New CourseCompletedDAO();
$ccompleted = $completedDAO->getAllCourseComplete($loginID);

//for retrieval of course name
$courseDAO= New CourseDAO();
?>
<style>
th, td,tr {
  text-align: center;
}
</style>
<html>
<head>
    <title>Completed Courses</title>
    <link rel="stylesheet" type="text/css" href="css/mainpageUI.css">
    <script src="https://kit.fontawesome.com/129e7cf8b7.js" crossorigin="anonymous"></script>
</head>

<body>
<!-- Nav Bar -->
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
        <!-- Page content of completed courses -->
            <div class="display-right__container">
                <table>
                    <tr>
                        <th class="table-title" colspan="2">Completed</th>
                    </tr>
                    <tr>
                        <th>Course Code</th>
                        <th>Course Title</th>
                    </tr>
                <?php
                    echo '</tr>';
                    foreach ($ccompleted as $completed){
                        $coursecodecompleted = $completed->getCode();
                        
                        echo '<tr><td>';
                        echo $coursecodecompleted;
                        echo '</td><td>';
                        echo $courseDAO->RetrieveCourseName($coursecodecompleted);
                        echo '</td></tr>';
                    }
                ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>