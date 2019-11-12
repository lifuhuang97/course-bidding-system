<?php
    require_once 'include/common.php';
    session_unset();
    session_destroy();
    session_start();
    $_SESSION['errors'] = ["You have successfully logged out"];
    header('Location: login.php');
?>