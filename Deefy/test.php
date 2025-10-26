<?php

use iutnc\deefy\audio\lists\Playlist;

require_once './vendor/autoload.php';

\iutnc\deefy\repository\DeefyRepository::setConfig('./config/deefy.db.ini');

$repo = \iutnc\deefy\repository\DeefyRepository::getInstance();

$playlists = $repo->findAllPlaylists();
foreach ($playlists as $pl) {
    print "playlist  : " . $pl->nom . ":". $pl->id . "\n";
}

echo "<br>";


$pl = new PlayList('test11', []);
$pl = $repo->saveEmptyPlaylist($pl);
print "playlist  : " . $pl->nom . ":". $pl->id . "\n";

$track = new \iutnc\deefy\audio\tracks\PodcastTrack('test', 'auteur', 'genre', 10, '2021-01-01', 'test.mp3', 'boh', '2021-01-01');
$track = $repo->savePodcastTrack($track);
print "track 2 : " . $track->titre . ":". get_class($track). "\n";
$repo->addTrackToPlaylist($pl->id, $track->id);
