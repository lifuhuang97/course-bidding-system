<?php
    require_once 'include/common.php';
    require_once 'include/function.php';
    require_once 'include/protect.php';

    if (!isset($_SESSION['success'])){
        header('Location: login.php');
        exit; 
    }else{
        $courseDAO= new CourseDAO();
        $courses=$courseDAO->RetrieveAllCourseDetail('', '', '');
    }
    $currentavailablecourses = $_SESSION['availablecourses'];

    $currentcourses = $_GET['code'];
    $currentsections = $_GET['sectionID'];
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
<html>
<body>
<a href="makebid.php?token=<?php echo $_GET['token']?>">Back</a>
</body>
</html>