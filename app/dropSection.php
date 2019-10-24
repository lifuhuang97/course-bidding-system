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
</head>

<body>
    <h1>
        Drop Section
    </h1>
    Welcome, <?=$name?><br>
    School: <?=$school?><br>
    Credit Left: <?=$edollar?>
    <hr>

    Please enter the course that you wish to drop:
    <form action="dropSectionProcess.php" method="GET">
        <input type='hidden' name='token' value="<?php echo $_GET['token'];?>">
        <table>
            <tr>
                <th>Course Code:</th>
                <td><input type='text' name='code'></td>    
            </tr>
            <tr>
                <th>Section ID     : </th>
                <td><input type='text' name='section'></td>
            </tr>
        </table>
        <input type='submit' value='Drop Section'>
        <a href="mainpage.php?token=<?php echo $_GET['token']?>">Back</a>
        <br>
        <br>
    <?php
        if (isset($_SESSION['errors1'])) {
            foreach ($_SESSION['errors1'] as $errors){
                print $errors;
                print "<br>";
            }
            unset ($_SESSION['errors1']);
        }
    ?>
    </form>
    <hr>
    
    All Section



<?php
    if (count($sections)==0){
        echo "<br><h3>No available course</h3>";
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
            $course=$courseDAO->RetrieveAllCourseDetail($code,$section)[0];
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
</body>
</html>


