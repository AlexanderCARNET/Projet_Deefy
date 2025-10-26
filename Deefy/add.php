<?php

require_once 'vendor/autoload.php';

session_start();

use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\audio\lists\Playlist;

$res = true;

if(isset($_SESSION['playlist'])){
    $playlist = $_SESSION['playlist'];
    if(isset($_GET['titre'], $_GET['artiste'], $_GET['genre'], $_GET['duree'], $_GET['nom_fichier'])){
        $audio_track = new AudioTrack($_GET['titre'], $_GET['artiste'], $_GET['genre'], $_GET['duree'], $_GET['nom_fichier']);
    }
    else if(isset($_GET['nom_fichier']) && !isset($_GET['titre'], $_GET['artiste'], $_GET['genre'], $_GET['duree'])){
        $audio_track = new AudioTrack("","","", 0, $_GET['nom_fichier']);
    }
    else{
        $res=false;
    }
    if($res){
        $playlist->addAudio($audio_track);
        $_SESSION['playlist'] = $playlist;
        echo 'track added to your playlist!!!';
    }
    else{
        echo 'something went wrong!!!';
    }
}
else{
    echo 'Any playlists exist!!!';
}

?>
