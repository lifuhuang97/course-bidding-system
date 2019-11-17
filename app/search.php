<?php
    require_once 'include/common.php';
    require_once 'include/function.php';
    require_once 'include/protect.php';

    if (!isset($_SESSION['success'])){
        header('Location: login.php');
        exit; 
    } else{
        $student=$_SESSION['student']; 
        $userid = $student->getUserid(); #get userid
        $password = $student->getPassword(); #get password
        $name = $student->getName(); #get name
        $school = $student->getSchool(); #get school
        $edollar = $student->getEdollar(); #get edollar
    }
?>
<html>
    <head>
        <title>Search</title>
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
        <div class="display-right">
            <div class="form-container">
                <div class="form-header">
                    <p>Search Page</p>
                </div>
                <form action="search.php?token=<?php echo $_GET['token']?>" method="post">
                    <input class="form-btn" type='submit' name='navigation' value='Search by Course'>
                    <input class="form-btn" type='submit' name='navigation' value='Search by Faculty'>
                    <input class="form-btn" type='submit' name='navigation' value='Search by Course Title'>
                </form>
            </div>
            <div class="content-container">
                <?php 
    //Retrieve all courses 
    $courseDAO= new CourseDAO();
    $allCourses = $courseDAO->retrieveAllCourseDetail($courseid='',$sectionid='',$school='');
    $courses=$courseDAO->RetrieveAll(); 
    echo "<form action='search.php?token={$_GET['token']}' method='post'>";
    if (isset($_POST['navigation']) || isset($_POST['courseSelect']) || isset($_POST['selectfaculty']) || isset($_POST['coursetitle'])){
        if (isset($_POST['courseSelect']) || (isset($_POST['navigation']) && $_POST['navigation']=='Search by Course')){
            $sectionDAO= new SectionDAO();
            $section=$sectionDAO->RetrieveAll();
            
            echo "Select:";
            echo"<select name='course'>";
            $array= [];
            $allcourse1 = 'All course';
            foreach($section as $item){
                $cid=$item->getCourseid();
                $sid=$item->getSectionid();
                
                if (!in_array($cid,$array)){
                    $array[] = $cid;  
                    echo "<option value='$cid'";
                    if (isset($_POST['course']) && $_POST['course']=="$cid"){ // if option is selected, then it will display on the dropdown 
                         echo "selected";
                    }
                    echo">$cid</option>";
                }
                
                
            }
            echo"<option selected value='$allcourse1'>$allcourse1</option>";
            echo "</select>
            <input type='submit' name='courseSelect' value='Search'>";
            
            // print out details of course selected
            if (isset($_POST['courseSelect'])){
                $selcourse = $_POST['course'];
                $weekday = [1=>'MON',2=>'TUE',3=>'WED',4=>'THU',5=>'FRI',6=>'SAT',7=>'SUN'];  
                echo "<h1>Course Table</h1>";
                foreach($allCourses as $course){
                    $eStartTime = $course->getExamStart();
                    $eStartTime = substr($eStartTime,0,5);
                    $eEndTime = $course->getExamEnd();
                    $eEndTime = substr($eEndTime,0,5);
                    $lStartTime = $course->getStart();
                    $lStartTime = substr($lStartTime,0,5);
                    $lEndTime = $course->getEnd();
                    $lEndTime = substr($lEndTime,0,5);

                    if ($course->getCourseid() == $selcourse){
                        echo "<table border='1'>";
                        echo "<tr><td>Course Code:</td><td>{$course->getCourseid()}</td></tr>";
                        echo "<tr><td>School:</td><td>{$course->getSchool()}</td></tr>";
                        echo "<tr><td>Title:</td><td>{$course->getTitle()}</td></tr>";
                        echo "<tr><td>Description:</td><td>{$course->getDescription()}</td></tr>";
                        echo "<tr><td>Exam Date:</td><td>{$course->getExamDate()}</td></tr>";
                        echo "<tr><td>Exam Start:</td><td>$eStartTime</td></tr>";
                        echo "<tr><td>Exam End:</td><td>$eEndTime</td></tr>";
                        echo "<tr><td>Instructor Name:</td><td>{$course->getInstructor()}</td></tr>";
                        echo "<tr><td>Day:</td><td>{$weekday[$course->getDay()]}</td></tr>";
                        echo "<tr><td>Lesson Start Time:</td><td>$lStartTime</td></tr>";
                        echo "<tr><td>Lesson End Time:</td><td>$lEndTime</td></tr>";
                        echo "<tr><td>Venue:</td><td>{$course->getVenue()}</td></tr>";
                        echo "<tr><td>Class Size:</td><td>{$course->getSize()}</td></tr>";
                        echo "</table>";
                        echo "<br>";
                    }
                    if (('All course' == $selcourse)){
                        echo "<table border='1'>";
                        echo "<tr><td>Course Code:</td><td>{$course->getCourseid()}</td></tr>";
                        echo "<tr><td>School:</td><td>{$course->getSchool()}</td></tr>";
                        echo "<tr><td>Title:</td><td>{$course->getTitle()}</td></tr>";
                        echo "<tr><td>Description:</td><td>{$course->getDescription()}</td></tr>";
                        echo "<tr><td>Exam Date:</td><td>{$course->getExamDate()}</td></tr>";
                        echo "<tr><td>Exam Start:</td><td>$eStartTime</td></tr>";
                        echo "<tr><td>Exam End:</td><td>$eEndTime</td></tr>";
                        echo "<tr><td>Instructor Name:</td><td>{$course->getInstructor()}</td></tr>";
                        echo "<tr><td>Day:</td><td>{$weekday[$course->getDay()]}</td></tr>";
                        echo "<tr><td>Lesson Start Time:</td><td>$lStartTime</td></tr>";
                        echo "<tr><td>Lesson End Time:</td><td>$lEndTime</td></tr>";
                        echo "<tr><td>Venue:</td><td>{$course->getVenue()}</td></tr>";
                        echo "<tr><td>Class Size:</td><td>{$course->getSize()}</td></tr>";
                        echo "</table>";
                        echo "<br>";
                    }
                }   
                
            }
        }elseif (isset($_POST['selectfaculty']) || (isset($_POST['navigation']) && $_POST['navigation']=='Search by Faculty')){
            echo "<br>";
            echo "<br>";
            echo "Select:";
            echo"<select name='faculty'>";
            $array1=[];
            $allcourse2 = 'All Faculty';
            foreach ($courses as $course){
                $sch = $course->getSchool();
                if (!in_array($sch,$array1)){
                    $array1[]=$sch;
                    
                    echo "<option value='$sch' ";
                    if (isset($_POST['faculty']) && $_POST['faculty']=="$sch"){ // if option is selected, then it will display on the dropdown 
                        echo "selected";
                    }
                    echo">$sch</option>";
                }
                
            }echo"<option selected value='$allcourse2'>$allcourse2</option>";
            echo "</select>
            <input type='submit' name='selectfaculty' value='Search'>";
            echo '<br>';
            
            if (isset($_POST['selectfaculty'])){
                echo  '<h1>Courses with sessions</h1>';
                $school = $_POST['faculty'];
                if ($school=='All Faculty'){
                    $school='';
                }
                $coursebysch = $courseDAO->retrieveAllCourseDetail($courseid='',$sectionid='',$school);
                
                // print out details of faculty selected
                foreach ($coursebysch as $schmods){
                    $weekday = [1=>'MON',2=>'TUE',3=>'WED',4=>'THU',5=>'FRI',6=>'SAT',7=>'SUN'];
                    $eStartTime = $schmods->getExamStart();
                    $eStartTime = substr($eStartTime,0,5);
                    $eEndTime = $schmods->getExamEnd();
                    $eEndTime = substr($eEndTime,0,5);
                    $lStartTime = $schmods->getStart();
                    $lStartTime = substr($lStartTime,0,5);
                    $lEndTime = $schmods->getEnd();
                    $lEndTime = substr($lEndTime,0,5);
                    echo "<table border='1'>";
                    echo "<tr><td>Course Code:</td><td>{$schmods->getCourseid()}</td></tr>";
                    echo "<tr><td>School:</td><td>{$schmods->getSchool()}</td></tr>";
                    echo "<tr><td>Title:</td><td>{$schmods->getTitle()}</td></tr>";
                    echo "<tr><td>Description:</td><td>{$schmods->getDescription()}</td></tr>";
                    echo "<tr><td>Exam Date:</td><td>{$schmods->getExamDate()}</td></tr>";
                    echo "<tr><td>Exam Start:</td><td>$eStartTime</td></tr>";
                    echo "<tr><td>Exam End:</td><td>$eEndTime</td></tr>";
                    echo "<tr><td>Instructor Name:</td><td>{$schmods->getInstructor()}</td></tr>";
                    echo "<tr><td>Day:</td><td>{$weekday[$schmods->getDay()]}</td></tr>";
                    echo "<tr><td>Lesson Start Time:</td><td>$lStartTime</td></tr>";
                    echo "<tr><td>Lesson End Time:</td><td>$lEndTime</td></tr>";
                    echo "<tr><td>Venue:</td><td>{$schmods->getVenue()}</td></tr>";
                    echo "<tr><td>Class Size:</td><td>{$schmods->getSize()}</td></tr>";
                    echo "</table>";
                    echo "<br>";
                } 
                
            }
            
                      
            
        }elseif (isset($_POST['coursetitle']) || (isset($_POST['navigation']) && $_POST['navigation']=='Search by Course Title')){
            echo "<br>";
            echo "<br>";
            echo "Select:";
            echo"<select name='coursename'>";
            $array2=[];
            $allcourse3 = 'All Title';
            foreach ($courses as $course){
                $title = $course->getTitle();
                if (!in_array($title,$array2)){
                    $array1[]=$title;
                    
                    echo "<option value='$title' ";
                    if (isset($_POST['coursename']) && $_POST['coursename']=="$title"){ // if option is selected, then it will display on the dropdown 
                        echo "selected";
                    }
                    echo">$title</option>";
                }
                
            }echo"<option selected value='$allcourse3'>$allcourse3</option>";
            echo "</select>
            <input type='submit' name='coursetitle' value='Search'>";
            echo '<br>';

            if (isset($_POST['coursetitle'])){
                echo  '<h1>Courses that have the same Title are: </h1>';
                // print out details of selected course title
                foreach($allCourses as $course){
                    $str1 = $course->getTitle();
                    $str2 = $_POST['coursename'];
                    $weekday = [1=>'MON',2=>'TUE',3=>'WED',4=>'THU',5=>'FRI',6=>'SAT',7=>'SUN'];
                    $eStartTime = $course->getExamStart();
                    $eStartTime = substr($eStartTime,0,5);
                    $eEndTime = $course->getExamEnd();
                    $eEndTime = substr($eEndTime,0,5);
                    $lStartTime = $course->getStart();
                    $lStartTime = substr($lStartTime,0,5);
                    $lEndTime = $course->getEnd();
                    $lEndTime = substr($lEndTime,0,5);
                    if ($str1 == $str2){
                        echo "<table border='1'>";
                        echo "<tr><td>Course Code:</td><td>{$course->getCourseid()}</td></tr>";
                        echo "<tr><td>School:</td><td>{$course->getSchool()}</td></tr>";
                        echo "<tr><td>Title:</td><td>{$course->getTitle()}</td></tr>";
                        echo "<tr><td>Description:</td><td>{$course->getDescription()}</td></tr>";
                        echo "<tr><td>Exam Date:</td><td>{$course->getExamDate()}</td></tr>";
                        echo "<tr><td>Exam Start:</td><td>$eStartTime</td></tr>";
                        echo "<tr><td>Exam End:</td><td>$eEndTime</td></tr>";
                        echo "<tr><td>Instructor Name:</td><td>{$course->getInstructor()}</td></tr>";
                        echo "<tr><td>Day:</td><td>{$weekday[$course->getDay()]}</td></tr>";
                        echo "<tr><td>Lesson Start Time:</td><td>$lStartTime</td></tr>";
                        echo "<tr><td>Lesson End Time:</td><td>$lEndTime</td></tr>";
                        echo "<tr><td>Venue:</td><td>{$course->getVenue()}</td></tr>";
                        echo "<tr><td>Class Size:</td><td>{$course->getSize()}</td></tr>";
                        echo "</table>";
                        echo "<br>";
                    }elseif($str2 == 'All Title'){
                        echo "<table border='1'>";
                        echo "<tr><td>Course Code:</td><td>{$course->getCourseid()}</td></tr>";
                        echo "<tr><td>School:</td><td>{$course->getSchool()}</td></tr>";
                        echo "<tr><td>Title:</td><td>{$course->getTitle()}</td></tr>";
                        echo "<tr><td>Description:</td><td>{$course->getDescription()}</td></tr>";
                        echo "<tr><td>Exam Date:</td><td>{$course->getExamDate()}</td></tr>";
                        echo "<tr><td>Exam Start:</td><td>$eStartTime</td></tr>";
                        echo "<tr><td>Exam End:</td><td>$eEndTime</td></tr>";
                        echo "<tr><td>Instructor Name:</td><td>{$course->getInstructor()}</td></tr>";
                        echo "<tr><td>Day:</td><td>{$weekday[$course->getDay()]}</td></tr>";
                        echo "<tr><td>Lesson Start Time:</td><td>$lStartTime</td></tr>";
                        echo "<tr><td>Lesson End Time:</td><td>$lEndTime</td></tr>";
                        echo "<tr><td>Venue:</td><td>{$course->getVenue()}</td></tr>";
                        echo "<tr><td>Class Size:</td><td>{$course->getSize()}</td></tr>";
                        echo "</table>";
                        echo "<br>";
                    }

                }
            }
        }
        
    }
    echo '</form>'
?>
            </div>
        </div>
    </div>
</body>
