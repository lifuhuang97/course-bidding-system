<?php

// DO NOT MODIFY THIS FILE

spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});

session_start();

function printErrors() {
    if(isset($_SESSION['errors'])){
        print "<ul style='color:red;text-align:center;list-style:inside'>";
        
        foreach ($_SESSION['errors'] as $value) {
            print "<li >" . $value . "</li>";
        }
        
        print "</ul>";   
        unset($_SESSION['errors']);
    }    
}

?>