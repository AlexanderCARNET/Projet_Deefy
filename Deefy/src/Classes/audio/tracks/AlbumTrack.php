<?php

namespace iutnc\deefy\audio\tracks;

use Cassandra\Date;
use DateTime;
use iutnc\deefy\audio\tracks\AudioTrack as AudioTrack;
use iutnc\deefy\exception\InvalidPropertyNameException as InvalidName;


require_once "getID3/getID3/getid3.php";

class AlbumTrack extends AudioTrack {

    private string $artiste_album;
	private string $album;
	private int $annee;
	private int $nb_piste;

	public function __construct(string $titre, string $genre, int $duree, DateTime|string $date, $chemin_fichier, string $artiste, string $nom_album, int $annee, int $numero_piste){
        if(file_exists($chemin_fichier)){
            $getid3 = new \getID3();
            $fileInfo = $getid3->analyze($chemin_fichier);
            if(isset($fileInfo['tags']['id3v2'])){
                $tags = $fileInfo['tags']['id3v2'];
                parent::__construct($tags['title'][0], $tags['artist'][0], $tags['genre'][0], (int)$fileInfo['playtime_seconds'], $fileInfo['tags']['idv3v2']['recording_time'][0]??date('d/m/y'), $chemin_fichier);
            }
        }
	    else{
            parent::__construct($titre, $artiste, $genre, $duree, $date, $chemin_fichier);
        }
        $this->annee=$annee;
        $this->artiste_album = $artiste;
        $this->album = $nom_album;
        $this->nb_piste = $numero_piste;
    }

	public function __toString():string{
		$p=get_object_vars($this);
		$json=json_encode($p);
		return $json;
	}

    public function __get(string $at):mixed {
        if (property_exists ($this, $at))
            return $this->$at;
        throw new InvalidName ($at);
    }
}

//echo 'Title: ' . $tags['title'][0] . PHP_EOL;
//echo 'Artiste: ' . $tags['artist'][0] . PHP_EOL;
//echo 'Album: ' . $tags['album'][0] . PHP_EOL;
//echo 'Annee: ' . $tags['year'][0] . PHP_EOL;
//echo 'Genre: ' . $tags['genre'][0] . PHP_EOL;
//echo 'duree: ' . gmdate("is", (int)$fileInfo['playtime_seconds']) . PHP_EOL;