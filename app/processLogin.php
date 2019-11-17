<?php
    require_once 'include/common.php';
    require_once 'include/token.php';

    $dao = new StudentDAO();
    $_SESSION['errors'] = [];
    //Check if user submit a blank input, the user will be asked to enter their username/password
    if (isset($_POST['username']) && isset($_POST['password'])) {
        if (strlen(trim($_POST['username'])) == 0) {
            array_push($_SESSION['errors'], 'Please enter your username');
        }
        if (strlen(trim($_POST['password'])) == 0) {
            array_push($_SESSION['errors'], 'Please enter your password');
        }

        // Authenticate then go to mainpage
        // if user is admin, redirect to adminMainPage
        if (count($_SESSION['errors']) == 0 && $_POST['username'] == 'admin') {
            if ($_POST['password'] == 'P@ssw0rd!135') {
                $_SESSION['success'] = $_POST['username'];
                $token=generate_token($_POST['username']);
                header('Location: adminMainPage.php?token='.$token);
                exit;
            } else {    
                array_push($_SESSION['errors'], 'Your password is incorrect!');
            }
        
        } elseif (count($_SESSION['errors']) == 0){ // if user is student, redirect to mainpage
            $userid = $_POST['username'];
            $pass = $_POST['password'];

            $message = $dao->authenticate($userid,$pass);
            if ($message == 'SUCCESS') {
                $_SESSION['success'] = $userid;
                $token=generate_token($userid);
                header('Location: mainpage.php?token='.$token);
                exit;
            }
            else {
                array_push($_SESSION['errors'], $message);
            }
        }
        header("Location:login.php");
        exit;
    } else {
        header('Location: login.php');
        exit; 
    }
?>