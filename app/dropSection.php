<?php
     require_once 'include/common.php';
     require_once 'include/function.php';
     

    $student=$_SESSION['student'];
    $userid = $student->getUserid(); #get userid
    $password = $student->getPassword(); #get password
    $name = $student->getName(); #get name
    $school = $student->getSchool(); #get school
    $edollar = $student->getEdollar(); #get edollar

    $studentSectionDAO = New StudentSectionDAO();
    $sections = $studentSectionDAO->getSuccessfulBidsByID($userid);

    //getting the round ID and roundstat
    $adminround = new adminRoundDAO();
    $roundDetail = $adminround->RetrieveRoundDetail();
    $roundID = $roundDetail->getRoundID();
    $roundstat = $roundDetail->getRoundStatus();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Drop Section</title>
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
                    <p>Drop A Section</p>
                </div>
                <form action="dropSectionProcess.php" method="GET">
                    <input type='hidden' name='token' value="<?php echo $_GET['token'];?>">
                    <div class="form-group">
                        <label for="code">Course Code: </label><br>
                        <input type="text" name="code" required>
                    </div>
                    <div class="form-group">
                        <label for="section">Section ID: </label><br>
                        <input type="text" name="section" required>
                    </div>
                    <input class="submit-btn" type='submit' name="Submit">
                    <a href="mainpage.php?token=<?php echo $_GET['token']?>">Back</a>
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
                if (count($sections)==0){
                    echo "<h3>No available course</h3>";
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
                        <th>Amount</th> 
                        <th>Delete</th>
                    </tr>";
                    foreach ($sections as $module){
                        echo "<tr><td>";
                        $code = $module[2];
                        echo "$code</td>";
                        $section=$module[3];
                        $courseDAO= new CourseDAO();
                        $course=$courseDAO->retrieveAllCourseDetail($code,$section)[0];
                        echo "<td>";
                        echo "{$course->getTitle()}</td>
                            <td>{$section}</td>
                            <td>{$course->getDay()}</td>
                            <td>{$course->getStart()}</td>
                            <td>{$course->getEnd()}</td>
                            <td>{$course->getInstructor()}</td>
                            <td>{$module[1]}</td>";
                            ?>
                        
                        <td>
                            <a href="dropSectionProcess.php?token=<?=$_GET['token']?>&code=<?=$code?>&section=<?=$section?>">Drop</a>
                        </td>

                        <?php
                        echo "</tr>";
                    }
                    echo "</table>";
                }

            ?>
        </div>
    </div> 
</body>
</html>