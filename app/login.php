<?php
    require_once 'include/common.php';
?>

<html>
    <head>
        <title>Login</title>
    </head>
    <body>
        <form action="processLogin.php" method="POST" style="text-align:center; padding:200px;">
            <h1>Login</h1>
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
                    <th>UserID</th>
                    <td><input name='username' type='text'></td>
                </tr>
                <tr>
                    <th>Password</th>
                    <td><input name='password' type='password'></td>
                </tr>
            </table>
            <br>
            <input type='submit' value='Sign In'>
        </form>
    </body>
</html>
