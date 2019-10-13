<?php
require_once 'include/common.php';
#require_once 'include/protect.php';

?>

<form id='bootstrap-form' action="bootstrap-process.php" method="post" enctype="multipart/form-data">
	Bootstrap file: 
	<input id='bootstrap-file' type="file" name="bootstrap-file"></br>
	<input type="submit" name="submit" value="Import">
</form>
<!-- Back if want to start round but don't want to upload any bid data -->
<!-- temporary logout button for admin to logout -->
<a href="adminMainPage.php">Back</a>
<a href="logout.php">Logout</a>
