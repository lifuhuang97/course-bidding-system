<?php
	require_once 'include/common.php';
    require_once 'include/function.php';
    require_once 'include/protect.php';

    if (!isset($_SESSION['success'])) {
    	header('Location: login.php');
    	exit;
    }
    else{
        $student = $_SESSION['student']; 
        $userid = $student->getUserid(); #get userid
        $password = $student->getPassword(); #get password
        $edollar = $student->getEdollar(); #get edollar

        $biddingDAO = new BidDAO();
        $biddedModule = $biddingDAO->getBidInfo($_SESSION['success']);

        $biddedmodsarray = [];
        foreach ($biddedModule as $mods) {
            $b = ($mods->getCode());
            array_push($biddedmodsarray, $b);
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
	<title>Edit Bid</title>
</head>
<body>
	<?php
		if (isset($_SESSION['errors1'])) {
			foreach ($_SESSION['errors1'] as $error) {
				print $error;
				print "<br>";
			}
			unset($_SESSION['errors1']);
		}
		if (isset($_SESSION['errors2'])) {
			foreach ($_SESSION['errors2'] as $error) {
				echo $error;
				echo "<br>";
			}
			unset($_SESSION['errors2']);
		}

	?>
	<form action="editBidProcess.php?token=<?php echo $_GET['token']?>" method="POST">
		<table>
			<tr>
				<th colspan="2">Edit Bid Amount</th>
			</tr>
			<tr>
				<th>Course Code:</th>
				<td><input type="text" name="code" required></td>
			</tr>
			<tr>
				<th>Section:</th>
				<td><input type="text" name="section" required></td>
			</tr>
			<tr>
				<th>New Bid Amount:</th>
				<td><input type="number" name="newBidAmt" required></td>
			</tr>
			<tr>
				<td><input type="submit" name="submitEdit"></td>
			</tr>
		</table>
	</form>

		<table>
			<?php
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
			?>
		</table>
</body>
</html>