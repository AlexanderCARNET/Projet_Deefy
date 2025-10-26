<?php

require_once 'vendor/autoload.php';

session_start();

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\renderer\AudioListRenderer;

if(isset($_SESSION['playlist'])){
    $playlist = $_SESSION['playlist'];
    $renderer = new AudioListRenderer($playlist);
    echo $renderer->render(0);
}
else{
    echo 'Any playlists exist!!!';
}
