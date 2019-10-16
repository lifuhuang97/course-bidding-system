<?php
# edit the file included below. the bootstrap logic is there
require_once 'include/bootstrap.php';

$output=doBootstrap();
// var_dump($output);
if (isset($output)){
    echo $output['status'];
    var_dump ($output['num-record-loaded']);
    if (isset($output['error'])){
        foreach($output['error'] as $error){
            var_dump($error);
        }
    }
}
exit;
header('Location: adminMainPage.php');
