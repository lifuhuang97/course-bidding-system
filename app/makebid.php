<?php
    #$_SESSION['success'] = $userid;
     require_once 'include/common.php';
     require_once 'include/function.php';
     require_once 'include/protect.php';
     
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

        //preparing for removing modules that user alr completed
        $courseDAO= new CourseDAO();
        $courses=$courseDAO->RetrieveAllCourseDetail('', '', $school);

        $completedcourseDAO= new CourseCompletedDAO();
        $completed_courses=$completedcourseDAO->getallcoursecomplete($userid);

        $realarray= [];
        foreach ($completed_courses as $value) {
            $a = ($value->getCode());
            array_push($realarray, $a);
        }
        
        // need remove mod that user alr bidded

        $biddingDAO = new BidDAO();
        $modules = $biddingDAO->getBidInfo($userid);

        $biddedmodsarray = [];
        foreach ($modules as $mods) {
            $b = ($mods->getCode());
            array_push($biddedmodsarray, $b);
        }             
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
    <form action="processBid.php?token=<?php echo $_GET['token']?>" method="POST">
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
<?php
    if (isset($_SESSION['errors1'])) {
        foreach ($_SESSION['errors1'] as $errors){
            print $errors;
            print "<br>";
        }
        unset ($_SESSION['errors1']);
    }
    #if (isset($_SESSION['errors2'])) {
    #    foreach ($_SESSION['errors2'] as $errors){
    #        print $errors;
    #        print "<br>";
    #    }
    #    unset ($_SESSION['errors2']);
    #}
    #if (isset($_SESSION['errors3'])) {
    #    foreach ($_SESSION['errors3'] as $errors){
    #        print $errors;
    #        print "<br>";
    #    }
    #    unset ($_SESSION['errors3']);
    #}
?>
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
        <th>Exam Date</th>
        <th>Exam Start Time</th>
        <th>Exam End Time</th>
    </tr>";

    $currentavailable = [];
    foreach ($courses as $course){
        // need remove modules that user alr completed and remove modules that the use alr bidded and taking out those courses that require PREREQUISITES (but the user haven't take)
        if ( !(in_array ($course->getCourseid(), $realarray)) and !(in_array($course->getCourseid(),$biddedmodsarray)) and CheckForCompletedPrerequisites($userid,$course->getCourseid()) ){
            //print out every mods that the user haven't take and those modules that the user haven't bidded and those courses that require PREREQUISTIES that the user is available
            echo"<tr>
            <td>{$course->getCourseid()}</td>
            <td>{$course->getTitle()}</td>
            <td>{$course->getSectionid()}</td>
            <td>{$course->getDay()}</td>
            <td>{$course->getStart()}</td>
            <td>{$course->getEnd()}</td>
            <td>{$course->getInstructor()}</td>
            <td>{$course->getSize()}</td>
            <td>{$course->getExamDate()}</td>
            <td>{$course->getExamStart()}</td>
            <td>{$course->getExamEnd()}</td>
            </td>";
            //storing the available courses 
            array_push($currentavailable, [$course->getCourseid() , $course->getTitle() , $course->getSectionid() , $course->getDay(), 
            $course->getStart(), $course->getEnd() , $course->getInstructor() , $course->getSize() , $course->getExamDate() , 
            $course->getExamStart() , $course->getExamEnd()] );
        }
    }
    echo"</table>";
    $_SESSION['availablecourses'] = $currentavailable;
}
?>

</body>
</html>
<a href="mainpage.php?token=<?php echo $_GET['token']?>">Back</a>