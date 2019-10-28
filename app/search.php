<?php
    require_once 'include/common.php';
    require_once 'include/function.php';
    require_once 'include/protect.php';
?>
<form action="search.php?token=<?php echo $_GET['token']?>" method="post">
<input type='submit' name='navigation' value='Search by Course'>
<input type='submit' name='navigation' value='Search by Faculty'>
<input type='submit' name='navigation' value='Search by Course Title'>


<?php 
    $courseDAO= new CourseDAO();
    $allCourses = $courseDAO->retrieveAllCourseDetail($courseid='',$sectionid='',$school='');
    $courses=$courseDAO->RetrieveAll(); 

    if (!isset($_SESSION['success'])){
        header('Location: login.php');
        exit; 
    } else{
        $student=$_SESSION['student']; 
        #var_dump($student);
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
                $courseDAO= new CourseDAO();
                $allCourses = $courseDAO->RetrieveAllCourseDetail($courseid='',$sectionid='',$school='');
                $courses=$courseDAO->RetrieveAll(); 
                echo "<form action='search.php?token={$_GET['token']}' method='post'>";
                if (isset($_POST['navigation']) || isset($_POST['courseSelect']) || isset($_POST['selectfaculty']) || isset($_POST['coursetitle'])){
                    if (isset($_POST['courseSelect']) || (isset($_POST['navigation']) && $_POST['navigation']=='Search by Course')){
                        $sectionDAO= new SectionDAO();
                        $section=$sectionDAO->RetrieveAll();
                        
                        echo "Select:";
                        echo"<select name='course'>";
                //<option disabled selected value=''> -- select an option -- </option>";
                        $array= [];
                        $allcourse1 = 'All course';
                        foreach($section as $item){
                            $cid=$item->getCourseid();
                            $sid=$item->getSectionid();
                            
                            if (!in_array($cid,$array)){
                                $array[] = $cid;  
                                echo "<option value='$cid'";
                                if (isset($_POST['course']) && $_POST['course']=="$cid"){
                                   echo "selected";
                               }
                               echo">$cid</option>";
                           }
                           
                           
                       }echo"<option selected value='$allcourse1'>$allcourse1</option>";
                       echo "</select>
                       <input type='submit' name='courseSelect' value='Search'>";
                       
                       if (isset($_POST['courseSelect'])){
                        $selcourse = $_POST['course'];  
                        echo "<h1>Course Table</h1>";
                        foreach($allCourses as $course){
                            
                            if ($course->getCourseid() == $selcourse){
                                echo "<table border='1'>";
                                echo "<tr><td>Course Code:</td><td>{$course->getCourseid()}</td></tr>";
                                echo "<tr><td>School:</td><td>{$course->getSchool()}</td></tr>";
                                echo "<tr><td>Title:</td><td>{$course->getTitle()}</td></tr>";
                                echo "<tr><td>Description:</td><td>{$course->getDescription()}</td></tr>";
                                echo "<tr><td>Exam Date:</td><td>{$course->getExamDate()}</td></tr>";
                                echo "<tr><td>Exam Start:</td><td>{$course->getExamStart()}</td></tr>";
                                echo "<tr><td>Exam End:</td><td>{$course->getExamEnd()}</td></tr>";
                                echo "<tr><td>Instructor Name:</td><td>{$course->getInstructor()}</td></tr>";
                                echo "<tr><td>Day:</td><td>{$course->getDay()}</td></tr>";
                                echo "<tr><td>Lesson Start Time:</td><td>{$course->getStart()}</td></tr>";
                                echo "<tr><td>Lesson End Time:</td><td>{$course->getEnd()}</td></tr>";
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
                                echo "<tr><td>Exam Start:</td><td>{$course->getExamStart()}</td></tr>";
                                echo "<tr><td>Exam End:</td><td>{$course->getExamEnd()}</td></tr>";
                                echo "<tr><td>Instructor Name:</td><td>{$course->getInstructor()}</td></tr>";
                                echo "<tr><td>Day:</td><td>{$course->getDay()}</td></tr>";
                                echo "<tr><td>Lesson Start Time:</td><td>{$course->getStart()}</td></tr>";
                                echo "<tr><td>Lesson End Time:</td><td>{$course->getEnd()}</td></tr>";
                                echo "<tr><td>Venue:</td><td>{$course->getVenue()}</td></tr>";
                                echo "<tr><td>Class Size:</td><td>{$course->getSize()}</td></tr>";
                                echo "</table>";
                                echo "<br>";
                            }
                        }   
                        
                    }
                }elseif (isset($_POST['selectfaculty']) || (isset($_POST['navigation']) && $_POST['navigation']=='Search by Faculty')){
                // $courseDAO= new CourseDAO();
                // $courses=$courseDAO->RetrieveAll();      
                //var_dump($courses);
                    echo "<br>";
                    echo "<br>";
                    echo "Select:";
                    echo"<select name='faculty'>";
                //<option disabled selected value=''> -- select an option -- </option>";
                    $array1=[];
                    $allcourse2 = 'All Faculty';
                    foreach ($courses as $course){
                        $sch = $course->getSchool();
                        if (!in_array($sch,$array1)){
                            $array1[]=$sch;
                            
                            echo "<option value='$sch' ";
                            if (isset($_POST['faculty']) && $_POST['faculty']=="$sch"){
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
                        $coursebysch = $courseDAO->RetrieveAllCourseDetail($courseid='',$sectionid='',$school);
                        foreach ($coursebysch as $schmods){
                            echo "<table border='1'>";
                            echo "<tr><td>Course Code:</td><td>{$schmods->getCourseid()}</td></tr>";
                            echo "<tr><td>School:</td><td>{$schmods->getSchool()}</td></tr>";
                            echo "<tr><td>Title:</td><td>{$schmods->getTitle()}</td></tr>";
                            echo "<tr><td>Description:</td><td>{$schmods->getDescription()}</td></tr>";
                            echo "<tr><td>Exam Date:</td><td>{$schmods->getExamDate()}</td></tr>";
                            echo "<tr><td>Exam Start:</td><td>{$schmods->getExamStart()}</td></tr>";
                            echo "<tr><td>Exam End:</td><td>{$schmods->getExamEnd()}</td></tr>";
                            echo "<tr><td>Instructor Name:</td><td>{$schmods->getInstructor()}</td></tr>";
                            echo "<tr><td>Day:</td><td>{$schmods->getDay()}</td></tr>";
                            echo "<tr><td>Lesson Start Time:</td><td>{$schmods->getStart()}</td></tr>";
                            echo "<tr><td>Lesson End Time:</td><td>{$schmods->getEnd()}</td></tr>";
                            echo "<tr><td>Venue:</td><td>{$schmods->getVenue()}</td></tr>";
                            echo "<tr><td>Class Size:</td><td>{$schmods->getSize()}</td></tr>";
                            echo "</table>";
                            echo "<br>";
                        } 
                        
                    }
                    
                    echo "<option value='$sch' ";
                    if (isset($_POST['faculty']) && $_POST['faculty']=="$sch"){
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
                foreach ($coursebysch as $schmods){
                    echo "<table border='1'>";
                    echo "<tr><td>Course Code:</td><td>{$schmods->getCourseid()}</td></tr>";
                    echo "<tr><td>School:</td><td>{$schmods->getSchool()}</td></tr>";
                    echo "<tr><td>Title:</td><td>{$schmods->getTitle()}</td></tr>";
                    echo "<tr><td>Description:</td><td>{$schmods->getDescription()}</td></tr>";
                    echo "<tr><td>Exam Date:</td><td>{$schmods->getExamDate()}</td></tr>";
                    echo "<tr><td>Exam Start:</td><td>{$schmods->getExamStart()}</td></tr>";
                    echo "<tr><td>Exam End:</td><td>{$schmods->getExamEnd()}</td></tr>";
                    echo "<tr><td>Instructor Name:</td><td>{$schmods->getInstructor()}</td></tr>";
                    echo "<tr><td>Day:</td><td>{$schmods->getDay()}</td></tr>";
                    echo "<tr><td>Lesson Start Time:</td><td>{$schmods->getStart()}</td></tr>";
                    echo "<tr><td>Lesson End Time:</td><td>{$schmods->getEnd()}</td></tr>";
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
            //<option disabled selected value=''> -- select an option -- </option>";
            $array2=[];
            $allcourse3 = 'All Title';
            foreach ($courses as $course){
                $title = $course->getTitle();
                if (!in_array($title,$array2)){
                    $array1[]=$title;
                    
                    
                }elseif (isset($_POST['coursetitle']) || (isset($_POST['navigation']) && $_POST['navigation']=='Search by Course Title')){
                    echo "<br>";
                    echo "<br>";
                    echo "Select:";
                    echo"<select name='coursename'>";
                //<option disabled selected value=''> -- select an option -- </option>";
                    $array2=[];
                    $allcourse3 = 'All Title';
                    foreach ($courses as $course){
                        $title = $course->getTitle();
                        if (!in_array($title,$array2)){
                            $array1[]=$title;
                            
                            echo "<option value='$title' ";
                            if (isset($_POST['coursename']) && $_POST['coursename']=="$title"){
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
                    //var_dump($allCourses);
                        foreach($allCourses as $course){
                            $str1 = $course->getTitle();
                            $str2 = $_POST['coursename'];
                            if ($str1 == $str2){
                                echo "<table border='1'>";
                                echo "<tr><td>Course Code:</td><td>{$course->getCourseid()}</td></tr>";
                                echo "<tr><td>School:</td><td>{$course->getSchool()}</td></tr>";
                                echo "<tr><td>Title:</td><td>{$course->getTitle()}</td></tr>";
                                echo "<tr><td>Description:</td><td>{$course->getDescription()}</td></tr>";
                                echo "<tr><td>Exam Date:</td><td>{$course->getExamDate()}</td></tr>";
                                echo "<tr><td>Exam Start:</td><td>{$course->getExamStart()}</td></tr>";
                                echo "<tr><td>Exam End:</td><td>{$course->getExamEnd()}</td></tr>";
                                echo "<tr><td>Instructor Name:</td><td>{$course->getInstructor()}</td></tr>";
                                echo "<tr><td>Day:</td><td>{$course->getDay()}</td></tr>";
                                echo "<tr><td>Lesson Start Time:</td><td>{$course->getStart()}</td></tr>";
                                echo "<tr><td>Lesson End Time:</td><td>{$course->getEnd()}</td></tr>";
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
                                echo "<tr><td>Exam Start:</td><td>{$course->getExamStart()}</td></tr>";
                                echo "<tr><td>Exam End:</td><td>{$course->getExamEnd()}</td></tr>";
                                echo "<tr><td>Instructor Name:</td><td>{$course->getInstructor()}</td></tr>";
                                echo "<tr><td>Day:</td><td>{$course->getDay()}</td></tr>";
                                echo "<tr><td>Lesson Start Time:</td><td>{$course->getStart()}</td></tr>";
                                echo "<tr><td>Lesson End Time:</td><td>{$course->getEnd()}</td></tr>";
                                echo "<tr><td>Venue:</td><td>{$course->getVenue()}</td></tr>";
                                echo "<tr><td>Class Size:</td><td>{$course->getSize()}</td></tr>";
                                echo "</table>";
                                echo "<br>";
                            }

                        }
                    }
            }//elseif (False){
            //   print 'next function';
            //}
                
            }
            echo '</form>'
            ?>
        </div>
        </div>
    </div>
</body>
