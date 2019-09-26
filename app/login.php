<?php
    require_once 'include/common.php';
?>

<html>
    <head>
        <title>Login</title>
        <link rel="stylesheet" type="text/css" href="login.css">
    </head>
    <body>
        <header>
            <div class="header-logo">
                <img src="smulogo-white.png">
            </div>
        </header>
        <div class="boss-title">
            <img src="bios.png" style="width: 6em">
            <img src="boss-full.png" class="boss-full">
        </div>
        <div class="signin-title">
            <img src="Headline.png" style="width: 7em">
            <!-- <img src="line1.png"> -->
        </div>
        <div class="login-form">
            <form action="processLogin.php" method="POST" style="text-align:center">
                <?php
                    // Display error messages
                    if (isset($_SESSION['errors'])){
                        $msg = $_SESSION['errors'];
                        printErrors();
                        unset ($_SESSION['errors']);
                    }
                        
                ?>
                <table align='center'>
                    <tr>
                        <td><input class="username" name='username' type='text' placeholder="UserID"></td>
                    </tr>
                    <tr></tr>
                    <tr>
                        <td><input class="password" name='password' type='password' placeholder="Password"></td>
                    </tr>
                </table>
                <br>
                <input class="submit" type='submit' value='Sign In'>
        </form>
        </div>
    </body>
</html>
