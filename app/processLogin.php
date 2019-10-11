<?php
    require_once 'include/common.php';
    require_once 'include/token.php';

    $dao = new StudentDAO();
    $_SESSION['errors'] = [];

    if (isset($_POST['username']) && isset($_POST['password'])) {
        if (strlen(trim($_POST['username'])) == 0) {
            array_push($_SESSION['errors'], 'Please enter your username');
        }
        if (strlen(trim($_POST['password'])) == 0) {
            array_push($_SESSION['errors'], 'Please enter your password');
        }

        // Authenticate then go to mainpage
        if ($_POST['username'] == 'admin') {
            if ($_POST['password'] == 'P@ssw0rd!547') {
                $_SESSION['success'] = $_POST['username'];
                $token=generate_token($userid);
                header('Location: bootstrap.php?token='.$token);
                exit;
            } else {
                array_push($_SESSION['errors'], 'Password is incorrect!');
            }
            
        } elseif (count($_SESSION['errors']) == 0){
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