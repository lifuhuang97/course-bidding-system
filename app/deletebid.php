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
<title>Drop Bid</title>
<link rel="stylesheet" type="text/css" href="css/deletebid.css">
<link rel="stylesheet" type="text/css" href="css/mainpageUI.css">
<script src="https://kit.fontawesome.com/129e7cf8b7.js" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
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
                    <p>Drop Existing Bid</p>
                </div>
                <form action="deletebidprocess.php?token=<?php echo $_GET['token']?>" method="GET">
                    <input type='hidden' name='token' value="<?php echo $_GET['token'];?>">
                    <div class="form-group">
                        <label for="code">Course Code: </label><br>
                        <input class="form-control" type='text' name='code' required>
                    </div>
                    <div class="form-group">
                        <label for="section">Section ID: </label><br>
                        <input class="form-control" type='text' name='section' required>
                    </div>
                    <input class="submit-btn" name="submit" type='submit'>
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
        </div>
    </div> 

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


