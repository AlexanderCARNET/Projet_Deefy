<?php
    session_start();
    if(isset($_SESSION['sess_count'])){
        //$_SESSION['sess_count']+=1;
        //echo "compteur : ".$_SESSION['sess_count']."<br>";
    }
    else if (isset($_GET['val'])){
        $_SESSION['sess_count'] = $_GET['val'];
        echo 'initialisation compteur a '.$_GET['val']."<br>";
    }
    else{
        $_SESSION['sess_count'] = 1;
        echo 'initialisation compteur <br>';
    }
?>