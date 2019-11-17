<?php
    require_once 'include/common.php';
    require_once 'include/function.php';
    require_once 'include/protect.php';

    if (!isset($_SESSION['success'])){
        header('Location: login.php');
        exit; 
    }else{
        $courseDAO= new CourseDAO();
        $courses=$courseDAO->retrieveAllCourseDetail('', '', '');
        $student=$_SESSION['student'];
        $userid = $student->getUserid(); #get userid
        $password = $student->getPassword(); #get password
        $name = $student->getName(); #get name
        $school = $student->getSchool(); #get school
        $edollar = $student->getEdollar(); #get edollar
    }
    $currentavailablecourses = $_SESSION['availablecourses'];

    $currentcourses = $_GET['code'];
    $currentsections = $_GET['sectionID'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Course Details</title>
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
                        <p>Credit Balance: <?=$edollar?></p>
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
        <!-- display details of course -->
        <div class="display-right">
            <div class="content-container">
                <?php
                foreach ($courses as $course){
                        if ($course->getCourseid()==$currentcourses && $course->getSectionid()==$currentsections){
                            echo"<table border='1'>
                                <tr>
                                <td>Course ID:</td>
                                <td>{$course->getCourseid()}</td>
                                </tr>
                                <tr>
                                <td>Section ID:</td>
                                <td>{$course->getsectionID()}</td>
                                </tr>
                                <tr>
                                <td>Day:</td>
                                <td>{$course->getDay()}</td>
                                </tr>
                                <tr>
                                <td>Lesson start time:</td>
                                <td>{$course->getStart()}</td>
                                </tr>
                                <tr>
                                <td>Lesson end time:</td>
                                <td>{$course->getEnd()}</td>
                                </tr>
                                <tr>
                                <td>Instructor Name:</td>
                                <td>{$course->getInstructor()}</td>
                                </tr>
                                <tr>
                                <td>Venue:</td>
                                <td>{$course->getVenue()}</td>
                                </tr>
                                <tr>
                                <td>Class Size:</td>
                                <td>{$course->getSize()}</td>
                                </tr>
                                <tr>
                                <td>School:</td>
                                <td>{$course->getSchool()}</td>
                                </tr>
                                <tr>
                                <td>Title:</td>
                                <td>{$course->getTitle()}</td>
                                </tr>
                                <tr>
                                <td>Course Description:</td>
                                <td>{$course->getDescription()}</td>
                                </tr>
                                <tr>
                                <td>Exam Date:</td>
                                <td>{$course->getExamDate()}</td>
                                </tr>
                                <tr>
                                <td>Exam start time:</td>
                                <td>{$course->getExamStart()}</td>
                                </tr>
                                <tr>
                                <td>Exam end time:</td>
                                <td>{$course->getExamEnd()}</td>
                                </tr>";
                        }
                    }
            ?>
            </div>
            
        </div>
    </div>
</body>
</html>
