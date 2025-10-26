<?php

    session_start();
    if (!isset($_SESSION['fibonacci'])){
        if(isset($_GET['val1']) && isset($_GET['val2'])){
            $fib=array($_GET['val1'], $_GET['val2']);
            $_SESSION['fibonacci']=$fib;
        }
        else{
            $fib=array(0, 1);
            $_SESSION['fibonacci']=$fib;
        }
        echo 'create a new session!!!!!';
    }
?>