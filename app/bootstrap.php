<?php
require_once 'include/common.php';
require_once 'include/bootstrap.php';
require_once 'include/protect.php';
?>

<form id='bootstrap-form' action="bootstrap.php?token=<?php echo $_GET['token']?>" method="post" enctype="multipart/form-data">
	Bootstrap file: 
	<input id='bootstrap-file' type="file" name="bootstrap-file"></br>
	<input type="submit" name="submitBootstrap" value="Import">
</form>
<!-- Back if want to start round but don't want to upload any bid data -->
<!-- temporary logout button for admin to logout -->
<a href="adminMainPage.php?token=<?php echo $_GET['token']?>">Back</a>
<a href="logout.php">Logout</a>
<?php
if (isset($_POST['submitBootstrap'])){
    $output=doBootstrap();
    echo "<hr>
    Bootstrap Result: {$output['status']}<br>";
    if (!isset($output['num-record-loaded'])){
        //no file found or missing file
        echo "Message: {$output['message'][0]}";
    }else{
        echo"<table border='1'>
        <tr>
            <th>File</th><th>Number of Records Loaded</th>
        </tr>";
        $total=0;
        foreach($output['num-record-loaded'] as $file => $count){
            echo"<tr>
                <td>$file</td><td>$count</td>
            </tr>";
            $total+=$count;
        }
        echo"<tr><th>Total Data added</th><th>$total</th></tr>
        </table>";
        if (isset($output['error'])){
            echo"<br>
            <table border='1'>
            <tr>
                <th>S/N</th><th>File</th><th>Line</th><th>Message</th>
            </tr>";
            $count=1;
            foreach($output['error'] as $error){
                echo"<tr><td>$count</td><td>{$error['file']}</td><td>{$error['line']}</td><td><ul>";
                foreach ($error['message'] as $oneError){
                    echo "<li>$oneError</li>";
                }
                echo"</ul></td></tr>";
                $count++;
            }
           echo "</table>"; 
        }
    }
    

}

?>