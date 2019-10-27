<html>
<body>
<h1> Completed Courses </h1>

<?php
    require_once 'include/common.php';
    require_once 'include/protect.php';
    require_once 'include/function.php';
    

    $studentDAO = New StudentDAO();
    $student = $studentDAO->retrieveStudent($_SESSION['success']);
    $loginID = $student->getUserid();
    $_SESSION['student'] = $student;
    $name = $student->getName();
    $school = $student->getSchool();

    $completedDAO = New CourseCompletedDAO();
    $ccompleted = $completedDAO->getallcoursecomplete($loginID);
    echo '<table border="1" color="#00002">';
    echo '<tr><td>';
    echo 'Student ID</td><td>Course Code</td>';
    echo '</tr>';
    foreach ($ccompleted as $completed){
        $nameofuser = $completed->getUserid();
        $coursecodecompleted = $completed->getCode();
        
        echo '<tr><td>';
        echo $nameofuser;
        echo '</td><td>';
        echo $coursecodecompleted;
        echo '</td></tr>';  
    }
    
    
?>
<a href='mainpage.php?token=<?php echo $_GET['token']?>'>Back</a>
<br>
<a href='makebid.php?token=<?php echo $_GET['token']?>'>Bid for a course</a>


</body>
</html>