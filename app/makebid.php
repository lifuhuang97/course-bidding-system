<?php
    #$_SESSION['success'] = $userid;
     require_once 'include/common.php';
     if (!isset($_SESSION['success'])){
         header('Location: login.php');
         exit; 
     }else{
        $student=$_SESSION['student']; 
        #var_dump($student);
        $userid = $student->getUserid(); #get userid
        $password = $student->getPassword(); #get password
        $name = $student->getName(); #get name
        $school = $student->getSchool(); #get school
        $edollar = $student->getEdollar(); #get edollar

        $courseDAO= new CourseDAO();
        $courses=$courseDAO->RetrieveAllCourseDetail('', '', $school);

        $completedcourseDAO= new CourseCompletedDAO();
        $completed_courses=$completedcourseDAO->getallcoursecomplete($userid);

        $realarray= [];
        foreach ($completed_courses as $value) {
            $a = ($value->getCode());
            array_push($realarray, $a);

        }
        
        
        #var_dump($completed_courses);
        // need remove mod that user cannot take due to prerequitsite
        // need remove mod that user alr bidded        
        
     }
?>
<html>
<head>
</head>
<body>
    Welcome, <?=$name?><br>
    School: <?=$school?><br>
     Credit Left: <?=$edollar?>
     <hr>
     Please place your bid here:
    <form action="processBid.php" method="POST">
        <input type='hidden' name='eCredit' value="<?=$edollar?>">
        <table>
            <tr>
                <th>Course Code:</th>
                <td><input type='text' name='code'></td>
            </tr>
            <tr>
                <th>Section ID:</th>
                <td><input type='text' name='sectionID'></td>
            </tr>
            <tr>
                <th>Bid Amount:</th>
                <td><input type='text' name='bidAmt'></td>
            </tr>
        </table>
        <input type='submit'>
    </form>

<hr>
Available Course to Bid
<?php
if (count($courses)==0){
    echo "No available course";
}else {
    echo"<table border='1px'>
    <tr>
        <th>Course ID</th>
        <th>Title</th>
        <th>Section ID</th>
        <th>Day</th>
        <th>Lesson Start Time</th>
        <th>Lesson End Time</th>
        <th>Instructor</th>
        <th>Size</th> 
    </tr>";
    foreach ($courses as $course){
        // need remove modules that user alr completed
        if (!(in_array($course->getCourseid(), $realarray))){
            //print out every mods that the user haven't take
            echo"<tr>
            <td>{$course->getCourseid()}</td>
            <td>{$course->getTitle()}</td>
            <td>{$course->getSectionid()}</td>
            <td>{$course->getDay()}</td>
            <td>{$course->getStart()}</td>
            <td>{$course->getEnd()}</td>
            <td>{$course->getInstructor()}</td>
            <td>{$course->getSize()}</td>
            </td>";
        }
    }
    echo"</table>";
}
?>

</body>
</html>
