<?php

    session_start();
    if (isset($_SESSION['fibonacci'])){
        $fib=$_SESSION['fibonacci'];
        $n=count($fib);
        echo "val 1 : ".$fib[$n-2]." val 2: ".$fib[$n-1]."<br>";
        $fib[$n]=$fib[$n-2]+$fib[$n-1];
        echo "valeur suivant: ".$fib[$n]."";
        $_SESSION['fibonacci']=$fib;
    }
    else{
        echo 'invalid session!!!!';
    }
?>