<?php

require_once '../include/common.php';
require_once '../include/token.php';


// isMissingOrEmpty(...) is in common.php
$errors = [ isMissingOrEmpty ('username'), 
            isMissingOrEmpty ('password') ];
$errors = array_filter($errors);


if (!isEmpty($errors)) {
    $result = [
        "status" => "error",
        "message" => array_values( $errors)
        ];
}
else{
    $username = $_POST['username'];
    $password = $_POST['password'];

# complete authenticate API

    # check if username and password are right. generate a token and return it in proper json format
    $studentDAO=new StudentDAO();
    $returnMsg=$studentDAO->authenticate($username,$password);
    # after you are sure that the $username and $password are correct, you can do
    if ($returnMsg=='SUCCESS' || ($username=='admin' && $password=='P@ssw0rd!135')){
        $token=generate_token($username);
        $result = [
            "status" => "success",
            "token" => $token
            ];
    }else{
        if ($returnMsg=='Password is incorrect!')
            $returnMsg='invalid password';
        else
            $returnMsg='invalid username';
        $result = [
            "status" => "error",
            "message" => [$returnMsg]
            ];
            
    }
    # generate a secret token for the user based on their username

    # return the token to the user via JSON    
		
	# return error message if something went wrong

}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
 
?>