<?php

// DO NOT MODIFY THIS FILE

spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});

session_start();

function printErrors() {
    if(isset($_SESSION['errors'])){
        // print "<ul style='color:red;text-align:center;list-style:inside'>";
        print "<p style='color:red;text-align:center'>";
        foreach ($_SESSION['errors'] as $value) {
            echo $value;
            echo "<br>";
        }
        
        print "</p>";   
        unset($_SESSION['errors']);
    }    
}

function isMissingOrEmpty($name) {
    if (!isset($_REQUEST[$name])) {
        return "missing $name";
    }

    // client did send the value over
    $value = $_REQUEST[$name];
    if (empty($value)) {
        return "blank $name";
    }
}

# this is better than empty when use with array, empty($var) returns FALSE even when
# $var has only empty cells
function isEmpty($var) {
    if (isset($var) && is_array($var))
        foreach ($var as $key => $value) {
            if (empty($value)) {
               unset($var[$key]);
            }
        }

    if (empty($var))
        return TRUE;
}

?>