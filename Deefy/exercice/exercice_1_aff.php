<?php
    session_start();

    if(isset($_SESSION['sess_count']) && isset($_GET['val'])){
        $_SESSION['sess_count']+= $_GET['val'];
        echo "compteur: ".$_SESSION['sess_count']."<br>";
    }
    else if(isset($_SESSION['sess_count'])){
        $_SESSION['sess_count']+=1;
        echo "compteur : ".$_SESSION['sess_count']."<br>";
    }else{
        echo "invalid session!!!!!!!!!!!";
    }
?>