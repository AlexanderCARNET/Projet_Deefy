<?php

namespace iutnc\deefy\audio\tracks;

use DateTime;
use iutnc\deefy\exception\InvalidPropertyValueException as InvalidValue;
use iutnc\deefy\exception\InvalidPropertyNameException as InvalidName;

require_once "getID3/getid3/getid3.php";


abstract class AudioTrack{
	protected string $titre='';
	protected string $artiste='';
	protected DateTime $date;
	protected string $genre='';
	protected ?int $duree = 0;
	protected string $nom_fichier_audio='';

    /**
     * @throws \Exception
     */
    public function __construct(string $titre, string $artiste, string $genre, int $duree, DateTime|string $date, string $nom_fichier){
        $getid3 = new \getID3();
        $info = $getid3->analyze($nom_fichier);
        if($titre == "" && $artiste == "" && $genre == "" && $duree == 0 && $nom_fichier != ""){
            if(isset($info['tags']['id3v2'])){
                $tags = $info['tags']['id3v2'];
                $this->titre=$tags['title'][0]??'';
                $this->artiste=$tags['artist'][0]??'';
                $this->genre=$tags['genre'][0]??'';
                $this->duree = ((int)$info['playtime_seconds']) ?? 0;
            }
        }
        else{
            $this->titre = $titre;
            $this->artiste = $artiste;
            $this->genre = $genre;
            $this->duree = $duree ?? 0;
        }
        if (!empty($info['tags']['id3v2']['recording_time'][0])) {
            $this->date = new DateTime($info['tags']['id3v2']['recording_time'][0]);
        } else if($date!=null){
            if($date instanceof DateTime)
                $this->date = $date;
            else
                $this->date = new DateTime($date);
        }
        else if($date==date('d/m/Y', strtotime('01/01/1970'))){
            $this->date = new DateTime('01/01/1970');
        }
        $this->nom_fichier_audio= $nom_fichier;
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

    public function __set(string $at, mixed $obj){
        if (property_exists ($this, $at))
            $this->$at=$obj;
        throw new InvalidName ($at);
    }
}