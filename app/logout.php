<?php
    require_once 'include/common.php';
    session_unset();
    session_destroy();
    echo"<strong>You have successfully logged out of the system. Please close this browser window.</strong>";
    echo "<a href='login.php'>Click here to go back</a>";
?>