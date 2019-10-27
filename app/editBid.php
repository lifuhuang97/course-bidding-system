<?php
	require_once 'include/common.php';
    require_once 'include/function.php';
//    require_once 'include/protect.php';
    $student = $_SESSION['student']; 
	$userid = $student->getUserid(); #get userid
	$password = $student->getPassword(); #get password
	$name = $student->getName(); #get name
    $school = $student->getSchool();
	$edollar = $student->getEdollar(); #get edollar

    $adminRoundDAO = new adminRoundDAO();
    $adminRoundStatus = $adminRoundDAO->RetrieveRoundDetail();
    if ($adminRoundStatus->getRoundStatus() != "Started") {
    	echo "Edit Bid is not allowed at the moment. ";
    	echo "Go back to ";
    	echo "<a href='mainpage.php?token={$_GET['token']}'>mainpage</a>";
    } else {
    	if (!isset($_SESSION['success'])) {
    		header('Location: login.php');
    		exit;
    	}
	    else{
	        $student = $_SESSION['student']; 
	        $userid = $student->getUserid(); #get userid
	        $password = $student->getPassword(); #get password
	        $name = $student->getName(); #get name
        	$school = $student->getSchool();
	        $edollar = $student->getEdollar(); #get edollar

	        $biddingDAO = new BidDAO();
	        $biddedModule = $biddingDAO->getBidInfo($_SESSION['success']);

	        $biddedmodsarray = [];
	        foreach ($biddedModule as $mods) {
	            $b = ($mods->getCode());
	            array_push($biddedmodsarray, $b);
	        }
    	}
		if (isset($_SESSION['errors1'])) {
			foreach ($_SESSION['errors1'] as $error) {
				print $error;
				print "<br>";
			}
			unset($_SESSION['errors1']);
		}
	}
?>   
<!DOCTYPE html>
<html>
<head>
	<title>Edit Bid</title>
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
                    <p>Edit Bid Amount</p>
                </div>
                <form action="editBidProcess.php?token=<?php echo $_GET['token']?>" method="POST">
                    <input type='hidden' name='eCredit' value="<?=$edollar?>">
                    <div class="form-group">
                        <label for="code">Course Code: </label><br>
                        <input class="form-control" type="text" name="code" required>
                    </div>
                    <div class="form-group">
                       <label for="sectionID">Section ID: </label><br>
                        <input class="form-control" type="text" name="section" required>
                    </div>
                    <div class="form-group">
                        <label for="bidAmt">New Bid Amount: </label><br>
                        <input class="form-control" type="text" name="newBidAmt" required>
                    </div>
                    <input class="submit-btn" type='submit' name="submit">
                </form>
            </div>
            <div class="content-container">
            	<?php
            		echo "<table>";
					if (isset($biddedModule)) {
						if (count($biddedModule) == 0){
							echo "<tr>
							        <td>No Existing Bid</td>
							      </tr>";
						}
						else {
							echo "<tr>
							      	<th>Code</th>
							        <th>Title</th>
							        <th>Section</th>
							        <th>Day</th>
							        <th>Lesson Start Time</th>
							        <th>Lesson End Time</th>
							        <th>Instructor</th>
							        <th>Amount</th>
							      </tr>";
							foreach ($biddedModule as $module) {
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
								echo "</tr>";
							}
						}
					}
					else {
						echo "<tr>
							    <td>No Existing Bid</td>
							  </tr>";
					}
					echo "</table>";
            	?>
            </div>
        </div>
    </div>  
</body>
</html>