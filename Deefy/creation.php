<?php

session_start();

require_once 'vendor/autoload.php';

//use iutnc\deefy\audio\tracks\AlbumTrack as AlbumTrack;
//use iutnc\deefy\renderer\AlbumTrackRenderer as AlbumTrackRenderer;
use iutnc\deefy\audio\lists\AudioList as AudioList;
use iutnc\deefy\audio\tracks\AudioTrack as AudioTrack;
use iutnc\deefy\renderer\AudioListRenderer as AudioListRenderer;
use iutnc\deefy\audio\lists\Playlist as Playlist;
use \iutnc\deefy\audio\tracks\PodcastTrack as PodcastTrack;

//$piste = new AlbumTrack("boh", "./src/Classes/audio/02-I_Need_Your_Love-BB_King-Lucille.mp3", "boh", 1);

//$renderer = new AlbumTrackRenderer($piste);
//echo $renderer->render(1);
//echo $renderer->render(2);


$audiotrack = [new PodcastTrack("test","./src/Classes/audio/02-I_Need_Your_Love-BB_King-Lucille.mp3")];
//$renderer = new AudioListRenderer($audiolist);

//echo $renderer->render(0);

if(!isset($_SESSION['playlist'])){
    $playlist = new Playlist("test", $audiotrack);
    $_SESSION['playlist'] = $playlist;
    echo "created!!!";
}
else if(isset($_GET['titre']) && !isset($_SESSION['playlist'])){
    $playlist = new Playlist("test");
    $_SESSION['playlist'] = $playlist;
    echo "created!!!";
}
else{
    echo "Already created!!!";
}
?>
