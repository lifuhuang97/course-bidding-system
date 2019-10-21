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

        //getting the round ID and roundstat
        $adminround = new adminRoundDAO();
        $roundDetail = $adminround->RetrieveRoundDetail();
        $roundID = $roundDetail->getRoundID();
        $roundstat = $roundDetail->getRoundStatus();


        //preparing for removing modules that user alr completed
        $courseDAO= new CourseDAO();
        $courses=$courseDAO->RetrieveAllCourseDetail('', '', $school);
        //retrieve all courses *with different school*
        $allCourses = $courseDAO->RetrieveAllCourseDetail('', '', '');
        //var_dump($allCourses);
        

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
        
        $nnowcourse = '';
        $nnowsection = '';
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
    <?php
        if(!isset($_GET['code'])){
            $_GET['code'] = '';
            $_GET['sectionID'] = '';
        }
        
    ?>
    <form action="processBid.php?token=<?php echo $_GET['token']?>" method="POST">
        <input type='hidden' name='eCredit' value="<?=$edollar?>">
        <table>
            <tr>
                <th>Course Code:</th>
                <td><input type='text' name='code' value=<?=$_GET['code']?>></td>
            </tr>
            <tr>
                <th>Section ID:</th>
                <td><input type='text' name='sectionID' value=<?=$_GET['sectionID']?>></td>
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

<?php
if ($roundID==1 && $roundstat=='Started'){
    echo "Available Courses to Bid for Round 1";
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
            <th>Add</th>
            <th>More Details</th>
        </tr>";

        $currentavailable = [];
        foreach ($courses as $course){
            // need remove modules that user alr completed and remove modules that the use alr bidded and taking out those courses that require PREREQUISITES (but the user haven't take)
            if ( !(in_array ($course->getCourseid(), $realarray)) and !(in_array($course->getCourseid(),$biddedmodsarray)) and CheckForCompletedPrerequisites($userid,$course->getCourseid()) ){
                //print out every mods that the user haven't take and those modules that the user haven't bidded and those courses that require PREREQUISTIES that the user is available
                //$nowcourse = $course->getCourseid();
                //$nowsection = $course->getSectionid();
                $_SESSION['currentcourse'] = $course->getCourseid();
                $_SESSION['currentsection'] = $course->getSectionid();
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
                <td><a href = 'makebid.php?token={$_GET['token']}&code={$course->getCourseid()}&sectionID={$course->getSectionid()}'>Add</td>
                <td><a href = 'moredetails.php?token={$_GET['token']}&code={$course->getCourseid()}&sectionID={$course->getSectionid()}'>More Details</td>
                </tr>";
                if (isset($_GET['button'])){
                    $nnowcourse = $nowcourse;
                    //print $nnowcourse;
                    $nnowsection = $nowsection;
                    //print $nnowsection;
                }

                //storing the available courses 
                array_push($currentavailable, [$course->getCourseid() , $course->getTitle() , $course->getSectionid() , $course->getDay(), 
                $course->getStart(), $course->getEnd() , $course->getInstructor() , $course->getSize() , $course->getExamDate() , 
                $course->getExamStart() , $course->getExamEnd()] );
            }
        }
        echo"</table>";
        $_SESSION['availablecourses'] = $currentavailable;
    }
}elseif ($roundID==2 && $roundstat=='Started'){
    echo "Available Courses to Bid for Round 2";
    //should show all courses that is available for the user 
    //should include the validation stuffs as well
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
            <th>Add</th>
            <th>Min Bid Required</th>
            <th>More Details</th>
        </tr>";

        $currentavailable = [];
        //getting round 2 maximum and minimum bid



        foreach ($allCourses as $course){
            // need remove modules that user alr completed and remove modules that the use alr bidded and taking out those courses that require PREREQUISITES (but the user haven't take)
            if ( !(in_array ($course->getCourseid(), $realarray)) and !(in_array($course->getCourseid(),$biddedmodsarray)) and CheckForCompletedPrerequisites($userid,$course->getCourseid()) ){
                //print out every mods that the user haven't take and those modules that the user haven't bidded and those courses that require PREREQUISTIES that the user is available
                $minbid = CheckMinBid1($course->getCourseid(),$course->getSectionid());
                $nowcourse = $course->getCourseid();
                $nowsection = $course->getSectionid();
                
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
                <td><a href = 'makebid.php?token={$_GET['token']}&code=$nowcourse&sectionID=$nowsection'>Add</td>
                <td>$minbid[0]</td>     
                <td><a href = 'moredetails.php?token={$_GET['token']}&code={$course->getCourseid()}&sectionID={$course->getSectionid()}'>More Details</td>
                </tr>";
                //storing the available courses 
                array_push($currentavailable, [$course->getCourseid() , $course->getTitle() , $course->getSectionid() , $course->getDay(), 
                $course->getStart(), $course->getEnd() , $course->getInstructor() , $course->getSize() , $course->getExamDate() , 
                $course->getExamStart() , $course->getExamEnd()] );
            }
        }
        echo"</table>";
        $_SESSION['availablecourses'] = $currentavailable;
    }
}else{
    echo "Rounds have not started!";
}
?>

</body>
</html>
<a href="mainpage.php?token=<?php echo $_GET['token']?>">Back</a>