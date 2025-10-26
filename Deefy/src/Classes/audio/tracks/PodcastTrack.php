<?php

namespace iutnc\deefy\audio\tracks;

use DateTime;
use iutnc\deefy\exception\InvalidPropertyNameException as InvalidName;

class PodcastTrack extends AudioTrack
{
    private string $auteur_podcast;
    private DateTime $date_podcast;

    public function __construct(string $titre, string $artiste, string $genre, int $duree, DateTime|string $date, string $nom_fichier_audio, string $auteur_podcast, DateTime|string $date_podcast){
        if(file_exists($nom_fichier_audio)) {
            parent::__construct("", "", "", 0, new DateTime('01/01/1970'), $nom_fichier_audio);
        }
        else{
            parent::__construct($titre, $artiste, $genre, $duree, $date, $nom_fichier_audio);
        }
        $this->auteur_podcast = $auteur_podcast;
        if($date_podcast!=null) {
            if ($date_podcast instanceof DateTime)
                $this->date_podcast = $date_podcast;
            else
                $this->date_podcast = new DateTime($date_podcast);
        }
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