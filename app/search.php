<html>
<body>
<h1>Search Page</h1>
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
    $allCourses = $courseDAO->RetrieveAllCourseDetail($courseid='',$sectionid='',$school='');
    $courses=$courseDAO->RetrieveAll(); 

    if (isset($_POST['navigation']) || isset($_POST['courseSelect']) || isset($_POST['selectfaculty']) || isset($_POST['coursetitle'])){
        if (isset($_POST['courseSelect']) || (isset($_POST['navigation']) && $_POST['navigation']=='Search by Course')){
            $sectionDAO= new SectionDAO();
            $section=$sectionDAO->RetrieveAll();
            echo "<br>";
            echo "<br>";
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
?>

<br>
<a href="makebid.php?token=<?php echo $_GET['token']?>">Bid</a>
<br>
</body>
</html>
<a href="mainpage.php?token=<?php echo $_GET['token']?>">Back</a>
