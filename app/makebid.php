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
    <title>Add Bid</title>
    <link rel="stylesheet" type="text/css" href="css/mainpageUI.css">
    <script src="https://kit.fontawesome.com/129e7cf8b7.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <div class="navbar-left">
            <div class="navbar-left__profile">
                <div class="navbar-left__profile__container">
                    <div class="profile-picture">
                        <img class="profpic" src="css/profpic1.png">
                    </div>
                    <div class="profile-details">
                        <p>Welcome, <?=$name?></p>
                        <p><?=$school?></p>
                        <p>Credit Balance: <?=$edollar?></p>
                    </div>
                </div>
            </div>
            <div class="navbar-left__completed">COMPLETED <i class="far fa-window-restore"></i></div>
            <a href='makebid.php?token=<?php echo $_GET['token']?>' style="color: white; text-decoration: none;"><div class="navbar-left__addCourse">ADD BID <i class="far fa-calendar-plus"></i></div></a>
            <a href='editBid.php?token=<?php echo $_GET['token']?>' style="color: white; text-decoration: none;"><div class="navbar-left__editBid">EDIT BID <i class="fas fa-pen-square"></i></div></a>
            <a href='deletebid.php?token=<?php echo $_GET['token']?>' style="color: white; text-decoration: none;"><div class="navbar-left__dropCourse">DROP BID <i class="far fa-calendar-times"></i></div></a>
            <a href="logout.php" style="color: white; text-decoration: none;"><div class="navbar-left__logout">LOGOUT <i class="fas fa-sign-out-alt"></i></div></a>
            <div class="navbar-left__smuLogo">
                    <img src="css/smulogo.png">
            </div>
        </div>
        <div class="display-right">
            <div class="form-container">
                <div class="form-header">
                    <p>Make a bid</p>
                </div>
                <form action="processBid.php?token=<?php echo $_GET['token']?>" method="POST">
                    <input type='hidden' name='eCredit' value="<?=$edollar?>">
                    <div class="form-group">
                        <label for="code">Course Code: </label><br>
                        <input class="form-control" type="text" name="code" required>
                    </div>
                    <div class="form-group">
                       <label for="sectionID">Section ID: </label><br>
                        <input class="form-control" type="text" name="sectionID" required>
                    </div>
                    <div class="form-group">
                        <label for="bidAmt">Bid Amount: </label><br>
                        <input class="form-control" type="number" name="bidAmt" required>
                    </div>
                    <input class="submit-btn" type='submit' name="submit">
                </form>
            </div>
            <?php
                if (isset($_SESSION['errors1'])) {
                    foreach ($_SESSION['errors1'] as $errors){
                        print $errors;
                        print "<br>";
                    }
                    unset ($_SESSION['errors1']);
                }
            ?>
            <?php
            if ($roundID==1) {
                if (count($courses)==0) {
                    echo "No available course";
                } else {
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
                    foreach ($courses as $course) {
                        // need remove modules that user alr completed and remove modules that the use alr bidded and taking out those courses that require PREREQUISITES (but the user haven't take)
                        if ( !(in_array ($course->getCourseid(), $realarray)) and !(in_array($course->getCourseid(),$biddedmodsarray)) and CheckForCompletedPrerequisites($userid,$course->getCourseid()) ) {
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
            } elseif ($roundID==2){
                print('Incomplete, should show all courses that is available for the user,should include the validation stuffs as well ');
                //should show all courses that is available for the user 
                //should include the validation stuffs as well
            }
            ?>
        </div>
    </div>    
</body>
</html>