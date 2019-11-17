<?php
     require_once 'include/common.php';
     require_once 'include/function.php';
     require_once 'include/protect.php';
     
     if (!isset($_SESSION['success'])){
         header('Location: login.php');
         exit; 
     }else{
        $student=$_SESSION['student']; 
        $userid = $student->getUserid(); #get userid
        $password = $student->getPassword(); #get password
        $name = $student->getName(); #get name
        $school = $student->getSchool(); #get school
        $edollar = $student->getEdollar(); #get edollar

        //getting the round ID and roundstat
        $adminround = new adminRoundDAO();
        $roundDetail = $adminround->retrieveRoundDetail();
        $roundID = $roundDetail->getRoundID();
        $roundstat = $roundDetail->getRoundStatus();


        //preparing for removing modules that user alr completed
        $courseDAO= new CourseDAO();
        $courses=$courseDAO->retrieveAllCourseDetail('', '', $school);

        //retrieve all courses *with different school*
        $allCourses = $courseDAO->retrieveAllCourseDetail('', '', '');
        
        $completedcourseDAO= new CourseCompletedDAO();
        $completed_courses=$completedcourseDAO->getAllCourseComplete($userid);

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
    <title>BIOS Bidding</title>
    <script src="https://kit.fontawesome.com/129e7cf8b7.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/mainpageUI.css">
    <style>
        th, td,tr {
        text-align: center;
        }
    </style> 
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
            <?php
                // check if user already added any bid. if added, then the form will be populated with the course code and sectionID selected.
                if(!isset($_GET['code'])){
                    $_GET['code'] = '';
                    $_GET['sectionID'] = '';
                }
            ?>
            <div class="form-container">
                <div class="form-header">
                    <p>Add Bid</p>
                </div>
                <form id="formMakeBid" action="processBid.php?token=<?php echo $_GET['token']?>" method="POST">
                    <input type='hidden' name='eCredit' value="<?=$edollar?>">
                    <div class="form-group">
                        <label for="code">Course Code: </label><br>
                        <input required class='form-control' type='text' name='code' value=<?=$_GET['code']?>>
                    </div>
                    <div class="form-group">
                       <label for="sectionID">Section ID: </label><br>
                        <input required class="form-control" type='text' name='sectionID' value=<?=$_GET['sectionID']?>>
                    </div>
                    <div class="form-group">
                        <label for="bidAmt">Bid Amount: </label><br>
                        <input class="form-control" type="text" name="bidAmt" required>
                    </div>
                    <input class="submit-btn" type='submit' name="submit">
                </form>
            </div>

            <?php
                //  display errors if there is error
                if (isset($_SESSION['errors1'])) {
                    foreach ($_SESSION['errors1'] as $errors){
                        echo "<p style='color: red; margin: 0'>".$errors."</p>";
                        print "<br>";
                    }
                    unset ($_SESSION['errors1']);
                }
            ?>

            <?php
if ($roundID==1 && $roundstat=='Started'){
    echo "<h3 style='text-align:left; font-weight:bold; padding-left:20px;'>Available Courses to Bid for Round 1</h3>";
    if (count($courses)==0){
        echo "No available course";
    }else {
        echo"<table border='1px'>
        <tr>
            <th>Course ID</th>
            <th style='text-align:center'>Title</th>
            <th>Section ID</th>
            <th style='text-align:center'>Day</th>
            <th>Lesson Start Time</th>
            <th>Lesson End Time</th>
            <th style='text-align:center'>Instructor</th>
            <th>Size</th> 
            <th>Exam Date</th>
            <th>Exam Start Time</th>
            <th>Exam End Time</th>
            <th>Add</th>
            <th>More Details</th>
        </tr>";

        $currentavailable = [];
        foreach ($courses as $course){
            // need remove modules that user alr completed and remove modules that the user already bidded and taking out those courses that require PREREQUISITES (but the user haven't take)
            if ( !(in_array ($course->getCourseid(), $realarray)) and !(in_array($course->getCourseid(),$biddedmodsarray)) and CheckForCompletedPrerequisites($userid,$course->getCourseid()) ){
                //print out every mods that the user haven't take and those modules that the user haven't bidded for and (those courses that require PREREQUISTIES, provided that the users had already complete the PREREQUISTIES mods)
                $weekday = [1=>'MON',2=>'TUE',3=>'WED',4=>'THU',5=>'FRI',6=>'SAT',7=>'SUN'];
                $eStartTime = $course->getExamStart();
                $eStartTime = substr($eStartTime,0,5);
                $eEndTime = $course->getExamEnd();
                $eEndTime = substr($eEndTime,0,5);
                $lStartTime = $course->getStart();
                $lStartTime = substr($lStartTime,0,5);
                $lEndTime = $course->getEnd();
                $lEndTime = substr($lEndTime,0,5);
                $_SESSION['currentcourse'] = $course->getCourseid();
                $_SESSION['currentsection'] = $course->getSectionid();
                echo"<tr>
                <td>{$course->getCourseid()}</td>
                <td>{$course->getTitle()}</td>
                <td>{$course->getSectionid()}</td>
                <td>{$weekday[$course->getDay()]}</td>
                <td>$lStartTime</td>
                <td>$lEndTime</td>
                <td>{$course->getInstructor()}</td>
                <td>{$course->getSize()}</td>
                <td>{$course->getExamDate()}</td>
                <td>$eStartTime</td>
                <td>$eEndTime</td>
                <td><a href = 'makebid.php?token={$_GET['token']}&code={$course->getCourseid()}&sectionID={$course->getSectionid()}'>Add</td>
                <td><a target='_blank' href = 'moredetails.php?token={$_GET['token']}&code={$course->getCourseid()}&sectionID={$course->getSectionid()}'>More Details</td>
                </tr>";
                if (isset($_GET['button'])){
                    $nnowcourse = $nowcourse;
                    $nnowsection = $nowsection;
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
    if (count($allCourses)==0){
        echo "No available course";
    }else {
        echo"<table border='1px'>
        <tr>
            <th>Course ID</th>
            <th style='text-align:center'>Title</th>
            <th>Section ID</th>
            <th style='text-align:center'>Day</th>
            <th>Lesson Start Time</th>
            <th>Lesson End Time</th>
            <th style='text-align:center'>Instructor</th>
            <th>Size</th> 
            <th>Vacancy Left</th>
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
            //Showing all modules and sections in round 2 
            if (True){
                //print out every mods(ALL) 
                $weekday = [1=>'MON',2=>'TUE',3=>'WED',4=>'THU',5=>'FRI',6=>'SAT',7=>'SUN'];
                $eStartTime = $course->getExamStart();
                $eStartTime = substr($eStartTime,0,5);
                $eEndTime = $course->getExamEnd();
                $eEndTime = substr($eEndTime,0,5);
                $lStartTime = $course->getStart();
                $lStartTime = substr($lStartTime,0,5);
                $lEndTime = $course->getEnd();
                $lEndTime = substr($lEndTime,0,5);
                $sectionDAO= new SectionDAO();
                $minbid = $sectionDAO->viewMinBid($course->getCourseid(),$course->getSectionid());
                $nowcourse = $course->getCourseid();
                $nowsection = $course->getSectionid();
                if ($minbid==''){
                    $minbid='-';
                }
                $vacancy = CheckVacancy($course->getCourseid(),$course->getSectionid(),$retrieveVale =True);
                if ($vacancy==False){
                    $vacancy=0;
                }
                echo"<tr>
                <td>{$course->getCourseid()}</td>
                <td>{$course->getTitle()}</td>
                <td>{$course->getSectionid()}</td>
                <td>{$weekday[$course->getDay()]}</td>
                <td>$lStartTime</td>
                <td>$lEndTime</td>
                <td>{$course->getInstructor()}</td>
                <td>{$course->getSize()}</td>
                <td>$vacancy</td>
                <td>{$course->getExamDate()}</td>
                <td>{$eStartTime}</td>
                <td>{$eEndTime}</td>
                <td><a href = 'makebid.php?token={$_GET['token']}&code=$nowcourse&sectionID=$nowsection'>Add</td>
                <td>$minbid</td>     
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
    //css + printing words/sentence in RED
    echo "<h2 style='color: red; text-transform: uppercase; padding-left: 1em;'>Rounds have not started!</h2>";
}
?>
        </div> 

</body>
</html>
