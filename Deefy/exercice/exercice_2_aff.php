<?php

    session_start();
    if (isset($_SESSION['fibonacci'])){
        $fib=$_SESSION['fibonacci'];
        echo 'Fibonacci :';
        for( $i= 0; $i<count($fib); $i++ ){
            echo ' '.$fib[$i];
        }
    }
    else{
        echo 'invalid session!!!!';
    }
?>