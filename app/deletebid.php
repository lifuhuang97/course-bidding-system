<?php
     require_once 'include/common.php';
     require_once 'include/function.php';

    $student=$_SESSION['student'];
    $userid = $student->getUserid(); #get userid
    $password = $student->getPassword(); #get password
    $name = $student->getName(); #get name
    $school = $student->getSchool(); #get school
    $edollar = $student->getEdollar(); #get edollar

    $bidDAO = New BidDAO();
    $biddedModule = $bidDAO->getBidInfo($userid);

    //getting the round ID and roundstat
    $adminround = new adminRoundDAO();
    $roundDetail = $adminround->RetrieveRoundDetail();
    $roundID = $roundDetail->getRoundID();
    $roundstat = $roundDetail->getRoundStatus();
?>

<!DOCTYPE html>
<html>
<head>
<title>Drop Exisiting Bid</title>
<link rel="stylesheet" type="text/css" href="css/deletebid.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>

<body>
    <h1>
        Drop Existing Bid
    </h1>
    Welcome, <?=$name?><br>
    School: <?=$school?><br>
    Credit Left: <?=$edollar?>
    <hr>

    Please enter the course that you wish to drop:
    <form action="deletebidprocess.php" method="GET">
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
        <input type='submit'>
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
    
    All bidded courses



<?php
    if (count($biddedModule)==0){
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
            <th>Amount</th> 
            <th>Delete</th>
        </tr>";
        foreach ($biddedModule as $module){
            $i = 1;

            echo "<tr><td>";
            $code = $module->getCode();
            echo "$code</td>";
            echo "<td>";
            $course = $module->getCourseDetailsByCourseSection();
            echo "{$course->getTitle()}</td>
                <td>{$module->getSection()}</td>
                <td>{$course->getDay()}</td>
                <td>{$course->getStart()}</td>
                <td>{$course->getEnd()}</td>
                <td>{$course->getInstructor()}</td>
                <td>{$module->getAmount()}</td>";
                ?>
            
            <td>
            <button id="<?= $code?>" class="trigger">Drop</button>
            <div class="modal" id="modal<?= $code?>">
                <div class="modal-content">
                    <h3>Are you sure you want to drop bid?
                    </h3>
                    <span class="close-button">Close</span>
                    <a class="drop-button" href="deletebidprocess.php?token=<?=$_GET['token']?>&code=<?=$code?>&section=<?=$module->getSection()?>">Confirm</a>
                    
                </div>
            </div>
            </td>




            <?php
            echo "</tr>";

            $i +=1;
        }
    }

?>
</body>
<script>

$(".trigger").click(function(e){
    var id = e.target.id
    $('#modal' + id).addClass('show-modal');
});

$('.close-button').click(function(){
    $(this).parent().parent().removeClass('show-modal');
});

</script>
</html>


